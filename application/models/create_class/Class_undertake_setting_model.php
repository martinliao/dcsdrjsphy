<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_undertake_setting_model extends MY_Model
{	
    public $table = 'require r';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();
		$this->tdb2 = $this->load->database('phy_require',TRUE);
    }	

	public function getList($condition, $page, $rows){
		//20210602 原sql碼
		/* $select = "
			CASE r.type
				WHEN 'A' THEN '行政'
				WHEN 'B' THEN '發展'
				ELSE ''
			END as style,
			CASE r.app_type
				WHEN '1' THEN '承辦單位統一報名'
				WHEN '2' THEN '限定單位人事報名'
				ELSE '各單位報名'
			END as app_type_text,
			CASE r.is_send
				WHEN 'Y' THEN 'disabled'
				ELSE ''
			END as edit_disable,
			substr(r.apply_s_date, 6, 5) as apply_s_date_format,
			substr(r.apply_e_date, 6, 5) as apply_e_date_format,
			substr(r.apply_s_date2, 6, 5) as apply_s_date2_format,
			substr(r.apply_e_date2, 6, 5) as apply_e_date2_format,
			substr(r.sel_s_date, 6, 5) as sel_s_date_format,
			substr(r.sel_e_date, 6, 5) as sel_e_date_format,
			r.*,
			c.name as description"; */

			//20210602 加入四個欄位，也在資料庫裡新增四個欄位
			$select = "
			CONCAT(r.year,'-',r.class_no,'-',r.term) AS id,
			CASE r.type
				WHEN 'A' THEN '行政'
				WHEN 'B' THEN '發展'
				ELSE ''
			END as style,
			CASE r.app_type
				WHEN '1' THEN '承辦單位統一報名'
				WHEN '2' THEN '限定單位人事報名'
				ELSE '各單位報名'
			END as app_type_text,
			CASE r.is_send
				WHEN 'Y' THEN 'disabled'
				ELSE ''
			END as edit_disable,
			substr(r.apply_s_date, 6, 5) as apply_s_date_format,
			substr(r.apply_e_date, 6, 5) as apply_e_date_format,
			substr(r.apply_s_date2, 6, 5) as apply_s_date2_format,
			substr(r.apply_e_date2, 6, 5) as apply_e_date2_format,
			substr(r.sel_s_date, 6, 5) as sel_s_date_format,
			substr(r.sel_e_date, 6, 5) as sel_e_date_format,
			substr(r.ref_s_date, 6, 5) as ref_s_date_format,
			substr(r.ref_e_date, 6, 5) as ref_e_date_format,
			substr(r.ext_s_date, 6, 5) as ext_s_date_format,
			substr(r.ext_e_date, 6, 5) as ext_e_date_format,
			r.*,
			c.name as description";
		$where = [];
		/*$year_start=substr($condition["start_date1"],0,3)+'1911';
		$date_start=substr($condition["start_date1"],3,6);
		$condition["start_date1"]=$year_start.$date_start;

		$year_end=substr($condition["end_date1"],0,3)+'1911';
		$date_end=substr($condition["end_date1"],3,6);
		$condition["end_date1"]=$year_end.$date_end;*/
		$where["(is_cancel = '0' or is_cancel is null)"] = null;
		$where["(5a_is_cancel != 'Y' or 5a_is_cancel is null)"] = null; 
		if (!empty($condition["start_date1"])) $where["start_date1 >="] = $condition["start_date1"]; 
		if (!empty($condition["end_date1"])) $where["end_date1 <="] = $condition["end_date1"]; 
		if (!empty($condition["class_no"])) $where["class_no LIKE"] = '%'.$condition["class_no"].'%'; 
		if (!empty($condition["class_name"])) $where["class_name LIKE"] = '%'.$condition["class_name"].'%'; 
		if (!empty($condition["reason"])) $where["reason"] = $condition["reason"]; 
		if (!empty($condition["worker"])) $where["worker"] = $condition["worker"]; 

		//var_dump($condition);
		$query = [
			"select" => $select,
			"order_by"=>'year desc,class_no,term asc',
			"join" => [
				[
					"table" => "bureau c",
					"condition" => "r.req_beaurau=c.bureau_id",
					"join_type" => "left"
				]
			],
			"conditions" => $where,
			"rows" => $rows,
			"offset" => $rows * ($page - 1),
		];

		if (!empty($condition["sort"])) $query["order_by"] = $condition["sort"];
		
		$return['data'] = $this->getData($query, 'object');
	
		$return['count'] = $this->getCount($query['conditions']);
		
		return $return;
	}

    public function getVerifyConfig()
    {
        $config = array(
            // 'new_class_name' => array(
            //     'field' => 'apply_type',
            //     'label' => '報名方式',
            //     'rules' => 'trim|required',
            // )
        );

        return $config;
    }

	public function getselect2($post)
	{
		//var_dump($post);die();
		$this->tdb2->select('YEAR');		
        $this->tdb2->where('YEAR',$post['phy_year']);
		$this->tdb2->where('CLASS_NO',$post['phy_class_no']);
		$this->tdb2->where('TERM',$post['phy_term']);
        $query = $this->tdb2->get('mdl_fet_phy_require');
    	$result = $query->result_array();
			return $result;

	}
	public function getselect3($phy_year, $phy_class_no, $phy_term)
	{
		//var_dump($phy_year, $phy_class_no, $phy_term);die();
		$this->tdb2->select('YEAR');		
        $this->tdb2->where('YEAR',$phy_year);
		$this->tdb2->where('CLASS_NO',$phy_class_no);
		$this->tdb2->where('TERM',$phy_term);
        $query3 = $this->tdb2->get('mdl_fet_phy_require');
    	$result3 = $query3->result_array();
			return $result3;

	}

	public function update_phy_require($post)
	{
		
		$this->tdb2->set('apply_s_date',$post['apply_s_date']);
		$this->tdb2->set('apply_e_date',$post['apply_e_date']);
		$this->tdb2->set('apply_s_date2',$post['apply_s_date2']);
		$this->tdb2->set('apply_e_date2',$post['apply_e_date2']);

		$this->tdb2->set('ref_s_date',$post['ref_s_date']);
		$this->tdb2->set('ref_e_date',$post['ref_e_date']);
		$this->tdb2->set('ext_s_date',$post['ext_s_date']);
		$this->tdb2->set('ext_e_date',$post['ext_e_date']);

		$this->tdb2->set('undertake_remark',$post['undertake_remark']);
    
    	$this->tdb2->where('year',$post['phy_year']);
		$this->tdb2->where('class_no',$post['phy_class_no']);
		$this->tdb2->where('term',$post['phy_term']);

    	if($this->tdb2->update('mdl_fet_phy_require')){
    		return true;
    	}	

		//}
		    	
	}

}