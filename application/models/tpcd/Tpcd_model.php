<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tpcd_model extends MY_Model
{
    public $table = 'tpcd_push_log';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();
        $this->init($this->table, $this->pk);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'id, type, notification_title, notification_context, message_content, pusher_name, push_time',
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
                    array('field' => 'pusher_name', 'value'=>$attrs['q'], 'position'=>'both'),
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

    public function getContent($id)
    {
        $this->db->select('message_content');
        $this->db->from('tpcd_push_log');
        $this->db->where('id', intval($id));

        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function getLocalUserList()
    {
        $this->db->select('idno');
        $this->db->from('is_local');
    
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function insertLog($data, $messageSchedule_id, $scheduleTime, $idno, $name)
    {
        $this->db->set('type', addslashes($data['type']));
        $this->db->set('send_list', addslashes($data['send_list']));
        $this->db->set('notification_title', addslashes($data['notification_title']));
        $this->db->set('notification_context', addslashes($data['notification_context']));
        $this->db->set('message_title', addslashes($data['message_title']));
        $this->db->set('message_content', htmlspecialchars(trim($data['message_content'])));
        $this->db->set('pusher', addslashes($idno));
        $this->db->set('pusher_name', addslashes($name));
        $this->db->set('push_time', addslashes($scheduleTime));
        $this->db->set('messageSchedule_id', intval($messageSchedule_id));

        if($this->db->insert('tpcd_push_log')){
            return true;
        }

        return false;
    }

    public function checkLimit($limit, $idno)
    {
        $first = date('Y-m-01', strtotime(date("Y-m-d")));
        $last = date('Y-m-d', strtotime("$first +1 month -1 day"));

        $this->db->select('count(1) cnt');
        $this->db->from('tpcd_push_log');
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

    public function getAllUser()
    {
        $this->db->select('idno');
        $this->db->from('BS_user');
    
        $query = $this->db->get();
        $result = $query->result_array();

        return $result;
    }

    public function insertUserLocal($idno)
    {
        $this->db->set('idno', addslashes($idno));

        if($this->db->insert('is_local')){
            return true;
        }

        return false;
    }

    public function clearIsLocal()
    {
        $sql = 'TRUNCATE table is_local';

        if($this->db->query($sql)){
            return true;
        }

        return false;
    }
}