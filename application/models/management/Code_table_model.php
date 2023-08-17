<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Code_table_model extends MY_Model
{
    public $table = 'code_table';
    public $pk = 'item_id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }

    public function getJobTitle($job_title=NULL){   
        if(is_null($job_title)) return NULL;
        $this->db->select('description');
        $this->db->from($this->table);
        $this->db->where('item_id',$job_title);
        $this->db->where('type_id','02');
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['description'];
    }
    public function getPosition($co_position=NULL){   
        if(is_null($co_position)) return NULL;
        $this->db->select('description');
        $this->db->from($this->table);
        $this->db->where('item_id',$co_position);
        $this->db->where('type_id','03');
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['description'];
    }
    public function getEducation($co_education=NULL){   
        if(is_null($co_education)) return NULL;
        $this->db->select('description');
        $this->db->from($this->table);
        $this->db->where('item_id',$co_education);
        $this->db->where('type_id','04');
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['description'];
    }  
}
