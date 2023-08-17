<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Elearn_count_model extends MY_Model
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
        if (isset($attrs['q'])) {
            $params['q'] = $attrs['q'];
        }

        $data = $this->getList($params);
        return count($data);
    }
    public function getCourseOne($data)
    {
        $this->db->select('*');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);    
        $this->db->where('material',1);
        $query=$this->db->get('require_content');
        $query=$query->result_array();
        
        return $query;
    }
    public function getCourseTwo($data)
    {
        $this->db->select('*');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);    
        $this->db->where('material',2);
        $query=$this->db->get('require_content');
        $query=$query->result_array();
        
        return $query;
    }
    public function getCourseThree($data)
    {
        $this->db->select('*');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);    
        $this->db->where('material',3);
        $query=$this->db->get('require_content');
        $query=$query->result_array();
        
        return $query;
    }
    public function getCourseZero($data)
    {
        $this->db->select('*');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);    
        $this->db->where('material',0);
        $query=$this->db->get('require_content');
        $query=$query->result_array();
        
        return $query;
    }
    public function getRequireContentFlag($data)
    {
        $this->db->select('*');
        $this->db->where('year',$data['year']);
        $this->db->where('class_no',$data['class_no']);
        $this->db->where('term',$data['term']);
        $material=[0,1,2,3];
        $this->db->where_in('material',$material);
        

        $query=$this->db->get('require_content');
        $query=$query->result_array();
        //var_dump($query);
        if(!empty($query)){
            return true;
        }
        return false;
    }
   

    public function getList($attrs=array())
    {
        
        $params = array(
            'select' => '*'
                         ,
            'order_by' => 'class_name,term',
        );

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
            
        }
        
        $data = $this->getData($params);
        for($i=0;$i<count($data);$i++){
            $data[$i]['bname']=$this->getBureauName($data[$i]);
            $data[$i]['course_zero']=$this->getCourseZero($data[$i]);
            $data[$i]['course_one']=$this->getCourseOne($data[$i]);
            $data[$i]['course_two']=$this->getCourseTwo($data[$i]);
            $data[$i]['course_three']=$this->getCourseThree($data[$i]);
            $data[$i]['course_flag']=$this->getRequireContentFlag($data[$i]);
        }
       
        return $data;        
    }
    public function getBureauName($data=array())
    {
        $this->db->distinct();
        $this->db->select('bureau_name');
        $this->db->where('bureau_id',$data['req_beaurau']);
        $this->db->order_by('bureau_name desc');
        $result=$this->db->get('BS_user');
        $result=$result->result_array();
        if(!empty($result)){
            return $result[0]['bureau_name'];
        }
        return null;
    }

}
