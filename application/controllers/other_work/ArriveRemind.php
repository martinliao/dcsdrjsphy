<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ArriveRemind extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
  //       ini_set("display_errors", "On"); 
		

        $this->load->model(['arrive_remind_model', 'bs_user_model', 'teacher_model']);
    }

    public function index()
    {
		$queryData = $this->getFilterData(['idno', 'member_name', 'member_type']);
		if (empty($queryData['member_type'])){
			$this->data['list'] = [];
		}else{
			$this->data['list'] = $this->arrive_remind_model->getForList($queryData);			
		}

    	$this->layout->view("other_work/arriveRemind/index", $this->data);
    }

    public function create()
    {
		$rules = array(
					array(
						'field'   => 'idno',
						'label'   => '蒞臨人員',
						'rules'   => 'required'
					),
					array(
						'field'   => 'member_type',
						'label'   => '蒞臨人員',
						'rules'   => 'required'
					),
					array(
						'field'   => 'remind_member_name',
						'label'   => '寄送對象(輸入姓名)',
						'rules'   => 'required'
					),   
					array(
						'field'   => 'email',
						'label'   => 'Email',
						'rules'   => 'required'
					),
					array(
						'field'   => 'remind_sdate',
						'label'   => '提醒期間(起)',
						'rules'   => 'required'
					),
   					array(
						'field'   => 'remind_edate',
						'label'   => '提醒期間(訖)',
						'rules'   => 'required'
					)   
        		);


		$this->form_validation->set_rules($rules);   

    	if ($this->form_validation->run()){
    		$this->store();
    	}	

    	$this->data['now'] = new DateTime();
		$this->data['link_refresh'] = base_url("other_work/arriveRemind/");
    	$this->layout->view("other_work/arriveRemind/form", $this->data);
    }

    private function store()
    {
    	$arriveRemind = $this->input->post(['idno', 'member_type', 'remind_member_name', 'email', 'remind_sdate', 'remind_edate']);
    	if ($this->arrive_remind_model->insert($arriveRemind)){
    		$this->setAlert(2, "新增成功");
    		redirect(base_url('other_work/ArriveRemind'));
    	}else{
    	    $this->setAlert(4, "新增失敗");
    		redirect(base_url('other_work/ArriveRemind/create'));	
    	}
    }

    public function delete()
    {
		$rules = array(
					array(
						'field'   => 'ids[]',
						'label'   => '流水號',
						'rules'   => 'required'
					)
        		);

		$this->form_validation->set_rules($rules);  
		
    	if ($this->form_validation->run()){
	    	if ($this->arrive_remind_model->deleteByIds($this->input->post('ids'))){
	    		$this->setAlert(2, "刪除成功");
	    		redirect(base_url('other_work/ArriveRemind'));
	    	}else{
	    	    $this->setAlert(4, "刪除失敗");
	    		redirect(base_url('other_work/ArriveRemind/create'));	
	    	}    		
    	}	

    }

    public function selectMember()
    {
    	$queryData = $this->getFilterData(['idno', 'member_name', 'queryType']);

    	if ($queryData['queryType'] == 'teacher'){
    		$this->data['members'] = $this->teacher_model->getForArriveSelect($queryData);
    	}elseif ($queryData['queryType'] == 'student'){
    		$this->data['members'] = $this->bs_user_model->getForArriveSelect($queryData);
    	}else{
    		$this->data['members'] = [];
    	}

    	$this->load->view("other_work/arriveRemind/selectMember", $this->data);
    }
}