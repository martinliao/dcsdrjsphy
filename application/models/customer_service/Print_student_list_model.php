<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_student_list_model extends MY_Model
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
            'select' => "*",
            'order_by' => 'year,class_no, term',
        );

        //var_dump($attrs);

       /*if(isset($attrs['class_name'])) {
            $like_name = array(
                array('field' => 'class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }*/
        
        if (isset($attrs['class_name'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'require.class_name', 'value'=>$attrs['class_name'], 'position'=>'both'),
                    ),
                );
            }
       
        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }


        $params['where_special'] = '(require.5a_is_cancel != "Y" or require.5a_is_cancel is null)';
       
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