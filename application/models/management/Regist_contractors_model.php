<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Regist_contractors_model extends MY_Model
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
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
            //var_dump($attrs['conditions']);
        }
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
        }
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
            $worker_data = $this->user_model->get(array('idno'=>$row['worker']));
            if(!empty($row['worker'])){
                $worker_data = $this->user_model->get(array('idno'=>$row['worker']));
                $row['worker_name'] = $worker_data['name'];
            }else{
                $row['worker_name'] = '';
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

}