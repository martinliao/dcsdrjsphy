<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leave_approval_model extends MY_Model
{
    public $table = 'leave_online';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

    }

    public function getList($condition,$default){
        $where = ' AND `require`.is_cancel = 0 ';
        if(!empty($default)){
            $where .=  sprintf(" AND room_use.use_date = '%s'",$default);
        }

        if(isset($condition['year']) && !empty($condition['year'])){
            $where .= sprintf(" AND `require`.`year` = '%s'",$condition['year']);
        }

        if(isset($condition['class_no']) && !empty($condition['class_no'])){
            $where .= sprintf(" AND `require`.class_no = '%s'",$condition['class_no']);
        }

        if(isset($condition['class_name']) && !empty($condition['class_name'])){
            $where .= sprintf(" AND `require`.class_name like '%%%s%%'",$condition['class_name']);
        }

        $sql = sprintf("SELECT
                            `require`.class_name,
                            `room_use`.room_id as room_code,
                            `require`.term,
                            `require`.`year`,
                            `require`.class_no 
                        FROM
                            `require`
                            JOIN room_use ON `require`.`year` = room_use.`year` 
                            AND `require`.class_no = room_use.class_id 
                            AND `require`.term = room_use.term
                            JOIN venue_information ON room_use.room_id = venue_information.room_id 
                        WHERE
                            /* venue_information.room_bel != '68001' 20210819教務處說要拿掉*/
                            1=1
                            %s
                        GROUP BY
                            `require`.`year`,
                            `require`.class_no,
                            `require`.term 
                        ORDER BY
                            `room_use`.room_id,`require`.term",$where);   
        
        $query = $this->db->query($sql);
        $data = $query->result_array();

        return $data;
    }

    public function getLeaveDetail($class_info, $paginate = true){
        $this->db->select('leave_online.id,online_app.st_no,BS_user.`name`,leave_online.vacation_date,leave_online.from_time,leave_online.to_time,leave_online.hours,leave_online.apply_date,leave_online.approval,leave_online.approver');
        $this->db->from('leave_online');
        $this->db->join('online_app','leave_online.`year` = online_app.`year`
                        AND leave_online.class_no = online_app.class_no 
                        AND leave_online.term = online_app.term 
                        AND leave_online.idno = online_app.id');
        $this->db->join('BS_user','leave_online.idno = BS_user.idno');

        $this->db->where('leave_online.year',$class_info['year']);
        $this->db->where('leave_online.class_no',$class_info['class_no']);
        $this->db->where('leave_online.term',$class_info['term']);
        $this->db->order_by('leave_online.vacation_date,online_app.st_no,leave_online.from_time');

        // if ($paginate) $this->paginate();
        $query = $this->db->get();
        $data = $query->result_array();

        return $data;

    }

    public function add_leave($data){
        $this->db->set('year',$data['year']);
        $this->db->set('class_no',$data['class_no']);
        $this->db->set('term',$data['term']);
        $this->db->set('idno',$data['id']);
        $this->db->set('vacation_date',$data['vacation_date']);
        $this->db->set('va_code',$data['va_code']);
        $this->db->set('from_time',$data['from_time']);
        $this->db->set('to_time',$data['to_time']);
        $this->db->set('hours',$data['hours']);
        $this->db->set('apply_date',date('Y-m-d H:i:s'));

        return $this->db->insert("leave_online");
    }

    public function approval_confirm($id,$name){
        $this->db->set('approval',1);
        $this->db->set('approver',$name);
        $this->db->where('id',$id);

        if($this->db->update('leave_online')){
            return true;
        }

        return false;
    }

    public function approval_delete($id){
        $this->db->where('id',$id);

        if($this->db->delete('leave_online')){
            return true;
        }

        return false;
    }

    public function getStudentLeaveInfo($id){
        $sql = sprintf("SELECT
                            BS_user.name,
                            leave_online.* 
                        FROM
                            leave_online
                            JOIN BS_user ON leave_online.idno = BS_user.idno 
                        WHERE
                            leave_online.id = %s",$this->db->escape(addslashes($id)));

        $query = $this->db->query($sql);
        $data = $query->result_array();

        return $data;
    }

    public function updateLeave($id,$from_time,$to_time,$vacation_date,$hours){
        $this->db->set('from_time',$from_time);
        $this->db->set('to_time',$to_time);
        $this->db->set('vacation_date',$vacation_date);
        $this->db->set('hours',$hours);
        $this->db->where('id',$id);

        if($this->db->update('leave_online')){
            return true;
        }

        return false;
    }
}