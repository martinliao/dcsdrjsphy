<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting_class extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}

		$this->load->model('create_class/setting_class_model');
		$this->load->model('create_class/courseteacher_model');
		$this->load->model('venue_rental/room_use_model');
		if (empty($this->data['filter']['start_date'])) {
            $this->data['filter']['start_date'] = '';
        }

        if (empty($this->data['filter']['end_date'])) {
            $this->data['filter']['end_date'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'index';
        $conditions = array();
        $this->data['list'] = array();
        if ($this->data['filter']['start_date'] != '' && $this->data['filter']['end_date'] != '') {
            if(isDate($this->data['filter']['start_date']) && isDate($this->data['filter']['end_date'])){
            	$conditions['start_date'] = $this->data['filter']['start_date'];
            	$conditions['end_date'] = $this->data['filter']['end_date'];
            	if($conditions['start_date'] > $conditions['end_date']){
            		$this->setAlert(3, '開始日期不能大於結束日期');
                	redirect(base_url("create_class/setting_class"));
            	}
            	$this->data['list'] = $this->setting_class_model->getList($conditions);
            }else{
            	$this->setAlert(3, '日期錯誤');
                redirect(base_url("create_class/setting_class"));
            }
        }

        if($post = $this->input->post()){

        	if(!empty($post['chkISEVALUATE'])){
        		$this->_update_evaluate($post['chkISEVALUATE']);
        	}
        	if(!empty($post['chkIsOnline'])){
        		$this->_update_question($post['chkIsOnline']);
        	}
        	if(!empty($post['isEvaluated']) && empty($post['cancelEvaluated'])){
        		$this->_cancel_evaluate($post['isEvaluated']);
        	}
        	if(!empty($post['isOnline']) && empty($post['setOnline'])){
        		$this->_cancel_question($post['isOnline']);
        	}
        	if(!empty($post['isEvaluated']) && !empty($post['cancelEvaluated'])){
        		$cancelEvaluated = array_diff($post['isEvaluated'],$post['cancelEvaluated']);
        		if($cancelEvaluated){
        			$this->_cancel_evaluate($cancelEvaluated);
        		}
        		$this->_cancel_evaluate($cancelEvaluated);
        	}
        	if(!empty($post['isOnline']) && !empty($post['setOnline'])){
        		$setOnline = array();
        		foreach($post['isOnline'] as $key => $row){
        			if(!isset($post['setOnline'][$key])){
        				$setOnline[] = $row;
        			}
        		}
        		// $setOnline = array_diff($post['isOnline'],$post['setOnline']);
        		// jd($post['isOnline']);
        		// jd($post['setOnline']);
        		// jd($setOnline,1);
        		if($setOnline){
        			$this->_cancel_question($setOnline);
        		}
        	}
        	$this->setAlert(1, '設定完成');
        	redirect(base_url("create_class/setting_class/?".$_SERVER['QUERY_STRING']));
        }
        // jd($this->data['list']);

        $this->layout->view('create_class/setting_class/list', $this->data);
    }

    public function _update_evaluate($chkISEVALUATE=array())
    {
    	foreach($chkISEVALUATE as $row){
    		$data = $this->setting_class_model->get($row);
    		if($data){
    			$fields = array(
    				'isevaluate' => 'Y',
    			);
    			$this->setting_class_model->update($data['seq_no'], $fields);
    			$conditions = array(
    				'year' => $data['year'],
    				'class_no' => $data['class_no'],
    				'term' => $data['term'],
    			);
    			$this->courseteacher_model->delete($conditions);
    			$conditions = array(
    				'year' => $data['year'],
    				'class_no' => $data['class_no'],
    				'term' => $data['term'],
    				'isteacher' => 'Y',
    			);
    			$room_use_teacher = $this->room_use_model->get_room_teacher($conditions);
    			if($room_use_teacher){
    				foreach($room_use_teacher as $teacher_row){
                        // jd($teacher_row, 1);
                        $teacher_row['cre_dte'] = $teacher_row['cre_date'];
                        $fields = $teacher_row;
                        unset($fields['cre_date']);
	    				$this->courseteacher_model->insert($fields);
	    			}
    			}

    		}
    	}
    }

    public function _update_question($chkIsOnline=array())
    {
    	foreach($chkIsOnline as $row){
    		$data = $this->setting_class_model->get($row);
    		if($data){
    			$fields = array(
    				'question_addr' => 'http://172.16.10.29/q.php',
    			);
    			$this->setting_class_model->update($data['seq_no'], $fields);
    		}
    	}
    }

    public function _cancel_evaluate($isEvaluated=array())
    {
    	foreach($isEvaluated as $row){
    		$data = $this->setting_class_model->get($row);
    		if($data){
    			$fields = array(
    				'isevaluate' => 'N',
    			);
    			$this->setting_class_model->update($data['seq_no'], $fields);
    		}
    	}
    }

    public function _cancel_question($isOnline=array())
    {
    	foreach($isOnline as $row){
    		$data = $this->setting_class_model->get($row);
    		if($data){
    			$fields = array(
    				'question_addr' => NULL,
    			);
    			$this->setting_class_model->update($data['seq_no'], $fields);
    		}
    	}
    }

}
