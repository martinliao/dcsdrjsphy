<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Not_reported_model extends MY_Model
{
    public $table = 'online_app';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

    }
    public function getListInfo($class_info, $paginate = true, $yn_sel = false){

        $card_log = $this->db->select("gid, count(*) cnt")
                             ->from('card_log')
                             ->where('class_no', $class_info['class_no'])
                             ->where('year', $class_info['year'])
                             ->where('term', $class_info['term'])
                             ->group_by('gid')
                             ->get_compiled_select();

		$this->db->start_cache();		

    	$this->db->select("oa.yn_sel, oa.st_no, oa.id, user.name, oa.memo, ifnull(og.ou_gov, bc.name) be_name, jt.name job_title, 
            cl.cnt")
    			 ->from('online_app oa')
    			 ->join('BS_user user', 'user.idno = oa.id', 'left')
                 ->join('bureau bc', 'bc.bureau_id = user.bureau_id', 'left')
                 ->join('job_title jt', 'jt.item_id = user.job_title', 'left')
                 ->join('out_gov og', 'og.id = user.idno', 'left')
                 ->join("({$card_log}) cl", "cl.gid=user.idno", 'left')
    			 ->where('oa.class_no', $class_info['class_no'])
    			 ->where('oa.year', $class_info['year'])
    			 ->where('oa.term', $class_info['term']);

        if (!empty($yn_sel)){
            $this->db->where('oa.yn_sel', $yn_sel);
        }else{
            $this->db->where_in('oa.yn_sel', ['3', '8', '5']);
        }

    	$this->db->stop_cache();
    	//if ($paginate) $this->paginate();
		$this->db->order_by('oa.st_no');

		$query = $this->db->get();
		$this->db->flush_cache(); 
		return $query->result(); 
		
    }

    public function getRequires($condition, $paginate = true, $idno){
    	$this->db->start_cache();

    	$this->db->select("r.*")
    			 ->from("require r")
    			 // ->join('online_app oa', 'oa.class_no = r.class_no AND oa.year = r.year AND oa.term = r.term' ,'left') 不知道要幹麻先mark掉
    			 ->where("r.year", $condition['year'])
    			 ->where("r.is_cancel", 0)
                 ->where("(r.5a_is_cancel !='Y' or r.5a_is_cancel is null)");
        if ($idno&&$idno!=1){
            $this->db->where('r.worker', $idno);
        }


    	if (!empty($condition['class_no'])){
    		$condition['class_no'] = strtoupper($condition['class_no']);
    		$this->db->where("UPPER(r.class_no) LIKE", "%{$condition['class_no']}%" );
    	}

    	if (!empty($condition['class_name'])){
    		$condition['class_name'] = strtoupper($condition['class_name']);
    		$this->db->where("UPPER(r.class_name) LIKE", "%{$condition['class_name']}%");
    	}

    	$this->db->stop_cache();

    	//if ($paginate) $this->paginate();
    	$this->db->order_by('r.year, r.class_no, r.term');
    	$query = $this->db->get();

    	$this->db->flush_cache(); 
    	return $query->result();
    }

    public function getRoomUse($condition){
    	$this->db->select("use_date")
    			 ->from("room_use")
    			 ->where($condition);
    	$query = $this->db->get();
    	return $query->result();
    }

    public function getRequire($condition){
    	$this->db->select("*")
    			 ->from("require")
    			 ->where($condition)
    			 ->where("is_cancel", 0);  
    	$query = $this->db->get();
    	return $query->row();  	
    }
}