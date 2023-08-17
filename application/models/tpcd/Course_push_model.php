<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_push_model extends MY_Model
{
    public $table = 'tpcd_course_push_log';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->init($this->table, $this->pk);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'year, term, class_name, pusher_name, push_time',
            'order_by' => 'push_time desc',
        );
        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
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
        if (isset($attrs['q'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'class_name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'class_no', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'year', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'pusher_name', 'value'=>$attrs['q'], 'position'=>'both'),
                    array('field' => 'term', 'value'=>$attrs['q'], 'position'=>'both'),
                ),
            );
        }

        $data = $this->getData($params);

        return $data;
    }

    public function getListCount($attrs=array())
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['q'])) {
            $params['q'] = $attrs['q'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getSetupData()
    {
        $this->db->select('*');
        $this->db->from('course_push_setup');

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }   

    public function setupData($data, $idno)
    {
        $this->db->set('before', intval($data['before']));
        $this->db->set('limit', intval($data['limit']));
        $this->db->set('notification_title', trim($data['notification_title']));
        $this->db->set('notification_context', trim($data['notification_context']));
        $this->db->set('message_title', trim($data['message_title']));
        $this->db->set('message_content', htmlspecialchars(trim($data['message_content'])));
        $this->db->set('modify_user', addslashes($idno));
        $this->db->set('modify_time', date('Y-m-d H:i:s'));

        if($this->db->update('course_push_setup')){
            return true;
        }

        return false;
    }

    public function getClassList($get_date)
    {
        // $this->db->select('require.seq_no, require.year, require.class_no, require.term, require.class_name, require.worker, BS_user.name');
        // $this->db->from('require');
        // $this->db->join('BS_user','require.worker = BS_user.idno');
        // $this->db->where('start_date1', $get_date);

        $sql = sprintf("SELECT
                            a.year,
                            a.class_no,
                            a.term,
                            `require`.class_name,
                            `require`.worker,
                            `require`.seq_no,
                            BS_user.`name` 
                        FROM
                            ( SELECT year, class_no, term, min( course_date ) min_course_date FROM periodtime GROUP BY year, class_no, term ) a
                            JOIN `require` ON a.YEAR = `require`.`year` 
                            AND a.class_no = `require`.class_no 
                            AND a.term = `require`.term
                            JOIN BS_user ON `require`.worker = BS_user.idno 
                        WHERE
                            a.min_course_date = '%s'", addslashes($get_date));


        $query = $this->db->query($sql);
        $result = $query->result_array();
        
        return $result;
    }

    public function checkMail($data = array())
    {   
        $this->db->select('count(1) cnt');
        $this->db->from('mail_log');
        $this->db->where('year', intval($data['year']));
        $this->db->where('class_no', addslashes($data['class_no']));
        $this->db->where('term', intval($data['term']));
        $this->db->where('mail_type', 3);

        $query = $this->db->get();
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function checkLimit($limit, $idno)
    {
        $first = date('Y-m-01', strtotime(date("Y-m-d")));
        $last = date('Y-m-d', strtotime("$first +1 month -1 day"));

        $this->db->select('count(1) cnt');
        $this->db->from('tpcd_course_push_log');
        $this->db->where('pusher', addslashes($idno));
        $this->db->where('push_time >=', $first);
        $this->db->where('push_time <=', $last);

        $query = $this->db->get();
        $result = $query->result_array();

        if($result[0]['cnt'] < $limit){
            return true;
        }

        return false;
    }

    public function getSendList($data = array())
    {
        $this->db->select('id');
        $this->db->from('online_app');
        $this->db->where('year', intval($data['year']));
        $this->db->where('class_no', addslashes($data['class_no']));
        $this->db->where('term', intval($data['term']));
        $this->db->where_in('yn_sel', ['1','3','4','5','8']);

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function insertLog($data, $info, $messageSchedule_id, $scheduleTime, $idno, $name)
    {
        if(count($info['send_list']) > 0){ 
            $send_list_tmp = array();
            for($i=0;$i<count($info['send_list']);$i++){
                $send_list_tmp[] = addslashes($info['send_list'][$i]['id']); 
            }

            $send_list = implode(',', $send_list_tmp);
        } else {
            $send_list = '';
        }

        $this->db->set('year', intval($info['year']));
        $this->db->set('class_no', addslashes($info['class_no']));
        $this->db->set('term', intval($info['term']));
        $this->db->set('class_name', addslashes($info['class_name']));
        $this->db->set('send_list', addslashes($send_list));
        $this->db->set('notification_title', addslashes($data[0]['notification_title']));
        $this->db->set('notification_context', addslashes($data[0]['notification_context']));
        $this->db->set('message_title', addslashes($data[0]['message_title']));
        $this->db->set('message_content', addslashes(trim($data[0]['message_content'])));
        $this->db->set('pusher', addslashes($idno));
        $this->db->set('pusher_name', addslashes($name));
        $this->db->set('push_time', addslashes($scheduleTime));
        $this->db->set('messageSchedule_id', intval($messageSchedule_id));

        if($this->db->insert('tpcd_course_push_log')){
            return true;
        }

        return false;
    }
}
?>