<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Merge_model extends MY_Model
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
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        if (isset($attrs['query_class_name'])) {
            $params['query_class_name'] = $attrs['query_class_name'];
        }
        $data = $this->getList($params);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'require.seq_no, require.year, require.class_no, require.class_name, series_category.name as series_name, second_category.name as second_name, count(1) as total_terms',
            'order_by' => 'require.year,require.class_no',
        );

        $params['join'] = array(array('table' => 'series_category',
                            'condition' => 'series_category.item_id = require.type',
                            'join_type' => 'left'),
                            array('table' => 'second_category',
                            'condition' => 'second_category.item_id = require.beaurau_id',
                            'join_type' => 'left')
                    );

        if (isset($attrs['query_class_name'])) {
            $params['or_like'] = array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'require.class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                ),
            );
        }

        $params['group_by'] = 'require.year,require.class_no,require.class_name,series_category.name,series_category.item_id,second_category.name';

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

        $data = $this->getData($params);

        return $data;
    }

    public function getClassData($id){
        $this->db->select('year,class_no');
        $this->db->where('seq_no',$id);
        $query = $this->db->get('require');
        $result = $query->row_array();

        $this->db->select('seq_no,year,class_no,term,class_name,start_date1,end_date1,contactor');
        $this->db->where('year',$result['year']);
        $this->db->where('class_no',$result['class_no']);
        $this->db->order_by('term');
        $query = $this->db->get('require');
        $data = $query->result_array();

        return $data;
    }

    public function requireMerge($class_info, $merge_terms){

        if (empty($class_info['year'])) return false;
        if (empty($class_info['class_no'])) return false;
        if (count($merge_terms) < 2) return false;

        // 取得要合併的最小期別
        $min_term = min($merge_terms);
        
        for($i=0; $i<count($merge_terms); $i++){
            if($merge_terms[$i] !== $min_term){
                $class_info['term'] = $merge_terms[$i];
                // 合併報名資料至合併後的班期(勾選中的最小期)
                $this->db->where($class_info)
                         ->where("yn_sel in ('2', '3')")
                         ->update("online_app", ["term" => $min_term, "ori_term" => $merge_terms[$i]]);

                // 更新異動紀錄的期別
                $this->db->where($class_info)
                         ->update("stud_modifylog", ["term" => $min_term]);

                // 清除合併後剩餘無用的資料
                $clear_table = [
                    "require",
                    "stud_modify",
                    "online_app",
                    "online_app_score",
                    "course",
                    "courseteacher",
                    "booking_place",
                    "upload_file",
                    "s_vacation",
                    "require_grade",
                ];

                foreach($clear_table as $table){
                    $this->db->where($class_info)
                             ->delete($table);
                }               
                
                $class_info['class_id'] = $class_info['class_no'];
                unset($class_info['class_no']); 
                $this->db->where($class_info)
                         ->delete("room_use");                
            }
        }
        
        $class_info['class_no'] = $class_info['class_id'];
        unset($class_info['class_id']); 

        // 更新合併後其他班期的期別資訊
        unset($class_info["term"]);
        $update_table = [
            "require",
            "online_app",
            "online_app_score",
            "course",
            "courseteacher",
            "booking_place",
            "upload_file",
            "s_vacation",
            "require_grade",
            "mail_log",
            "hour_traffic_tax",
            "require_content",
            "question_management",
            "periodtime",
            "dining_teacher",
            "dining_student",
            "dining",
            "beaurau_persons",                                    
        ];

        foreach($update_table as $table){
            $this->db->where($class_info)
                     ->where("term >", $min_term)
                     ->set("term", "term-1", false)
                     ->update($table);
        }

        $class_info['class_id'] = $class_info['class_no'];
        unset($class_info['class_no']);

        $this->db->where($class_info)
                 ->where("term >", $min_term)
                 ->set("term", "term-1", false)
                 ->update("room_use");

        return true;
    }
}