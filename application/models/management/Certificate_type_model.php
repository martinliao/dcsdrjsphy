<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_type_model extends MY_Model
{
    public $table = 'certificate_type';
    public $pk = 'id';

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
            'select' => "id, title, demo_text, bg_file_id, signature_file_id, seal_file_id, category",
            'order_by' => 'id desc',
        );

        $date_like = array();
        if (isset($attrs['type_title_name'])) {
            $like_idno = array(
                array('field' => 'title', 'value'=>$attrs['type_title_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
        }
        

        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
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
        // jd($this->db->last_query());

        return $data;
    }


    public function get_all_list()
    {
        $this->db->select("*");
        $this->db->from($this->table);

        $query = $this->db->get();
        $type_list = $query->result_array();
        return $type_list;
    }

    public function get_all_list_new($category)
    {
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where('category',intval($category));

        $query = $this->db->get();
        $type_list = $query->result_array();
        return $type_list;
    }

    public function del_certificate_image($id)
    {
        if($this->db->delete('certificate_image',array('id' => $id))){
			return true;
		}else{
			return false;
		}
    }

    public function del_certificate_type($id,$file_sl)
    {
        if($this->db->where($file_sl.'_file_id',$id)->update('certificate_type',array($file_sl.'_file_id' => 0))){
			return true;
		}else{
			return false;
		}

    }

    public function get_certificate_file_name($id)
    {
        $this->db->select('*');
        $this->db->from('certificate_image');
        $this->db->where('id', $id);
        $query = $this->db->get();
        $type_list = $query->row();
        
        return $type_list->file_name;
        

    }


}