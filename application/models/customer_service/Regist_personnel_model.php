<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Regist_personnel_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getListCount($attrs=array())
    {

        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($attrs=array())
    {

        $params = array(
            'select' => "*, case when ((CURDATE() between APPLY_S_DATE and APPLY_E_DATE) OR (CURDATE() between APPLY_S_DATE2 and APPLY_E_DATE2)) then 'Y' end as canapp",
            'order_by' => 'year,class_no, term',
        );

        //var_dump($attrs);

        $date_like = array();
        if (isset($attrs['class_no'])) {
            $like_idno = array(
                array('field' => 'class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
        }
        if (isset($attrs['class_name'])) {
            $like_name = array(
                array('field' => 'class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($attrs['term'])) {
            $like_name = array(
                array('field' => 'term', 'value'=>$attrs['term'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }
        //用於where
        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }

        $this->db->select('name');
        $this->db->from('date_set');
        $this->db->where("item_id", '01');
        $query = $this->db->get();
        $sh_strat_time = $query->row_array();
        $sh_strat_time = $sh_strat_time['name'];
        if(empty($sh_strat_time)){
            $sh_strat_time = '0';
        }

        $this->db->select('name');
        $this->db->from('date_set');
        $this->db->where("item_id", '02');
        $query = $this->db->get();
        $sh_end_time = $query->row_array();
        $sh_end_time = $sh_end_time['name'];
        if(empty($sh_end_time)){
            $sh_end_time = '7';
        }

        $this->db->select('name');
        $this->db->from('date_set');
        $this->db->where("item_id", '05');
        $query = $this->db->get();
        $sh_strat_time_2 = $query->row_array();
        $sh_strat_time_2 = $sh_strat_time_2['name'];
        if(empty($sh_end_time)){
            $sh_strat_time_2 = '0';
        }

        $this->db->select('name');
        $this->db->from('date_set');
        $this->db->where("item_id", '06');
        $query = $this->db->get();
        $sh_end_time_2 = $query->row_array();
        $sh_end_time_2 = $sh_end_time_2['name'];
        if(empty($sh_end_time)){
            $sh_end_time_2 = '7';
        }

        $now = new DateTime();

        $params['where_special'] = "((apply_s_date <= '". $now->format('Y-m-d')."' AND apply_e_date >= '". $now->format('Y-m-d') ."') OR (apply_s_date2 <= '". $now->format('Y-m-d')."' AND apply_e_date2 >= '". $now->format('Y-m-d') ."'))";

        //增加篩選報名時間
        // if (isset($attrs['where_special'])) {
        //     $params['where_special'] = $attrs['where_special'];
        //     $params['where_special'] .= " and (CURDATE() between date_sub(apply_s_date, interval {$sh_strat_time} day) and date_add(apply_e_date, interval {$sh_end_time} day) or CURDATE() between date_sub(apply_s_date2, interval {$sh_strat_time_2} day) and date_add(apply_e_date2, interval {$sh_end_time_2} day) )";
        // }else{
        //     $params['where_special'] = "(CURDATE() between date_sub(apply_s_date, interval {$sh_strat_time} day) and date_add(apply_e_date, interval {$sh_end_time} day) or CURDATE() between date_sub(apply_s_date2, interval {$sh_strat_time_2} day) and date_add(apply_e_date2, interval {$sh_end_time_2} day) )";
        // }

        if (isset($attrs['class_status'])) {
            $params['where_in'] = array(
                'field' => 'class_status',
                'value' => $attrs['class_status'],
            );
        }
        if (isset($attrs['rows'])) {
            $params['rows'] = $attrs['rows'];
        }
        if (isset($attrs['offset'])) {
            $params['offset'] = $attrs['offset'];
        }
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }
        $data = $this->getData($params);

        foreach($data as & $row){

            $worker_data = array();
            if(!empty($row['worker'])){
                $worker_data = $this->user_model->get(array('idno'=>$row['worker']));
                $this->db->select('ext1');
                $this->db->from('agent_set');
                $this->db->where("item_id", $row['worker']);
                $query = $this->db->get();
                $agent_set = $query->row_array();
                $row['worker_name'] = $worker_data['name'];
                $row['ext'] = $agent_set['ext1'];
            }else{
                $row['worker_name'] = '';
                $row['ext'] = '';
            }

            $not_in = array('6');
            $this->db->from('online_app');
            $this->db->where(array('year'=>$row['year'], 'class_no'=>$row['class_no'] , 'term'=>$row['term']));
            $this->db->where_not_in('yn_sel', $not_in);
            $row['cnt'] = $this->db->count_all_results();

            $row['classNature'] = "無";
            if($row['is_assess']==1 && $row['is_mixed']==0) {
                $row['classNature'] = "考核";
            }
            elseif($row['is_assess']==1 && $row['is_mixed']==1) {
                $row['classNature'] = "考核+混成";
            }

        }

        return $data;
    }
    public function getConditions($seq_no)
    {
        $this->db->select('year,class_no,term,class_name');
        $this->db->from($this->table);
        $this->db->where("seq_no",$seq_no);
        $query = $this->db->get();
        $data = $query->result_array();
        return $data['0'];
    }
    public function getUserConditions($seq_no)
    {
        $this->db->select('year,class_no,term,class_name');
        $this->db->from($this->table);
        $this->db->where("seq_no",$seq_no);
        $query = $this->db->get();
        $data = $query->result_array();
        return $data['0'];
    }
    public function getClassData($attrs=array()){
        $this->db->select('seq_no,class_name');
        $this->db->from($this->table);
        $this->db->where("year",$attrs['year']);
        $this->db->where("class_no",$attrs['class_no']);
        $this->db->where("term",$attrs['term']);
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }
}