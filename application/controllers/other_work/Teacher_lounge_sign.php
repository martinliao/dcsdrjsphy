<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_lounge_sign extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('other_work/Teacher_lounge_model');
    }

    public function index()
    {
    	if($this->input->post('mode') == 'reserve' && !empty($this->input->post('teacher_name')) && !empty($this->input->post('class_name')) && !empty($this->input->post('lounge'))){

    		$reserve_list = $this->input->post('lounge');
    		$teacher_name = $this->input->post('teacher_name');
    		$class_name = $this->input->post('class_name');
    		$message = '';
    		for($i=0;$i<count($reserve_list);$i++){
    			$info_array = explode('_', $reserve_list[$i]);
    			if(count($info_array) == 3){
    				$time_interval = $info_array[0];
    				$lounge = $info_array[1];
    				$reserve_date = $info_array[2];

    				$check_keep = $this->Teacher_lounge_model->checkKeepForReserve($reserve_date,$lounge);

    				if(!$check_keep){
    					$check_insert = $this->Teacher_lounge_model->reserve($reserve_date, $time_interval, $lounge, $teacher_name, $class_name, $this->flags->user['idno']);
    				} else {
    					$message .= $reserve_date.' '.$this->getTimeInterval($time_interval).' '.$lounge.'預約失敗：已被保留'.'<br>';
    					continue;
    				}
    				
    				if(!$check_insert){
    					$message .= $reserve_date.' '.$this->getTimeInterval($time_interval).' '.$lounge.'預約失敗'.'<br>';
    				}
    			}
    		}

    		if(!empty($message)){
    			$this->setAlert(2, $message);
    		} else {
    			$this->setAlert(1, '預約成功');
    		}

    		redirect("other_work/teacher_lounge_sign?{$_SERVER['QUERY_STRING']}");
    	}

    	$start_date = $this->input->get('start_date');
    	$end_date = $this->input->get('end_date');

		if(isset($start_date) && isset($end_date)){
			$this->data['sess_start_date'] = $start_date;
			$this->data['sess_end_date'] = $end_date;
			$date1 = date_create($this->data['sess_start_date']);
			$date2 = date_create($this->data['sess_end_date']);
			$diff = date_diff($date1,$date2);
			$this->data['days'] = $diff->days + 1;
		} else {
			$date = date('Y-m-d');
			$first = 1;

			$w = date('w',strtotime($date));  
			$week_start = date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days'));
			$week_end = date('Y-m-d',strtotime("$week_start +6 days"));
			$this->data['sess_start_date'] = $week_start;
			$this->data['sess_end_date'] = $week_end;
			$this->data['days'] = 7;
		}

		$data_list = $this->Teacher_lounge_model->getReserveList($this->data['sess_start_date'], $this->data['sess_end_date']);

		$new_data_list = array();
		for($i=0;$i<count($data_list);$i++){
			$data_list[$i]['description1'] = '<font style="margin-left:12px">'.$data_list[$i]['name'].'</font> '.'<font style="color:red">預約</font>'.'<br>';
            $data_list[$i]['description2'] = '班期：'.$data_list[$i]['class_name'].'<br>';
            $data_list[$i]['description3'] = '<font style="margin-left:12px">老師：</font>'.'<font style="color:blue">'.$data_list[$i]['teacher_name'].'</font><br>';

            $key = $data_list[$i]['time_interval'].'_'.$data_list[$i]['lounge'].'_'.$data_list[$i]['reserve_date'];
			$new_data_list[$key] = $data_list[$i];
		}

		$keep_list = array();
		for($i=0;$i<$this->data['days'];$i++){
			$key = '';
            $thisday = date('Y-m-d',strtotime($this->data['sess_start_date'] ."+ $i days"));
			$keep_info = $this->Teacher_lounge_model->getKeepListInfo($thisday);

			for($j=0;$j<count($keep_info);$j++){
				$key = $keep_info[$j]['lounge'].'_'.$thisday;
				$keep_list[$key] = '<font style="color:blue">('.$keep_info[$j]['name'].'已保留)</font>';
			}
		}

		$this->data['link_keep'] = base_url("other_work/teacher_lounge_sign/keep");
		$this->data['link_lounge_edit'] = base_url("other_work/teacher_lounge_sign/edit?{$_SERVER['QUERY_STRING']}");
		$this->data['data_list'] = $new_data_list;
		$this->data['keep_list'] = $keep_list;

		if(isset($_GET['iscsv']) && $_GET['iscsv'] == 1){
        	$filename=date("YmdHis").".xls";   
			header("Content-type:application/vnd.ms-excel"); 
			header("Content-Disposition:filename=$filename");  

           	$this->load->view('other_work/teacher_lounge_sign/exportView',$this->data);
        } else {
	        $this->layout->view('other_work/teacher_lounge_sign/list',$this->data);
        }
    }

    public function keep($reload = 0){
    	if($this->input->post('cancel') > 0){
    		$check_cancel = $this->Teacher_lounge_model->cancelKeep($this->input->post('cancel'));

    		if($check_cancel){
    			$this->setAlert(1, '取消成功');
    			redirect("other_work/teacher_lounge_sign/keep/1");
    		} else {
    			$this->setAlert(2, '取消失敗，請再試一次');
    			redirect("other_work/teacher_lounge_sign/keep/0");
    		}
    	}

    	if($this->input->post('mode') == 'save' && !empty($this->input->post('start_date')) && !empty($this->input->post('end_date')) && !empty($this->input->post('lounge'))){
    		$start_date = $this->input->post('start_date');
    		$end_date = $this->input->post('end_date');
    		$lounge = $this->input->post('lounge');

    		$check_reserve = $this->Teacher_lounge_model->checkReserve($start_date, $end_date, $lounge);
    		$check_keep = $this->Teacher_lounge_model->checkKeep($start_date, $end_date, $lounge);

    		if(!$check_reserve && !$check_keep){
    			$insert_status = $this->Teacher_lounge_model->insertKeep($start_date, $end_date, $lounge, $this->flags->user['idno']);
    		} else {
    			$this->setAlert(2, '此時間區間內有被預約或保留的紀錄，故無法保留');
    			redirect("other_work/teacher_lounge_sign/keep/0");
    		}
    		
    		if($insert_status){
    			$this->setAlert(1, '保留成功');
    			redirect("other_work/teacher_lounge_sign/keep/1");
    		} 
    	}

    	$this->data['reload'] = $reload;
    	$this->data['data_list'] = $this->Teacher_lounge_model->getKeepList($this->flags->user['idno']);

    	$this->layout->view('other_work/teacher_lounge_sign/keep', $this->data);
    }

    public function edit(){
    	if($this->input->post('mode') == 'edit' && !empty($this->input->post('teacher_name')) && !empty($this->input->post('class_name')) && !empty($this->input->post('lounge'))){

    		$reserve_list = $this->input->post('lounge');
    		$teacher_name = $this->input->post('teacher_name');
    		$class_name = $this->input->post('class_name');
    		$message = '';
    		for($i=0;$i<count($reserve_list);$i++){
				$check_insert = $this->Teacher_lounge_model->reserveById($reserve_list[$i], $teacher_name, $class_name);
				
				if(!$check_insert){
					$reserve_info = $this->Teacher_lounge_model->getReserveInfo($reserve_list[$i]);
					if(!empty($reserve_info)){
						$message .= $reserve_info[0]['reserve_date'].' '.$this->getTimeInterval($reserve_info[0]['time_interval']).' '.$reserve_info[0]['lounge'].'預約失敗'.'<br>';
					} else {
						$message .= '預約失敗:無此筆資料'.'<br>';
					}
				}	
    		}

    		if(!empty($message)){
    			$this->setAlert(2, $message);
    		} else {
    			$this->setAlert(1, '預約成功');
    		}

    		redirect("other_work/teacher_lounge_sign/edit?{$_SERVER['QUERY_STRING']}");
    	} else if($this->input->post('mode') == 'del' && !empty($this->input->post('lounge'))){
    		$reserve_list = $this->input->post('lounge');
    	
    		$message = '';
    		for($i=0;$i<count($reserve_list);$i++){
				$check_del = $this->Teacher_lounge_model->delReserve($reserve_list[$i]);
				
				if(!$check_del){
					$reserve_info = $this->Teacher_lounge_model->getReserveInfo($reserve_list[$i]);
					if(!empty($reserve_info)){
						$message .= $reserve_info[0]['reserve_date'].' '.$this->getTimeInterval($reserve_info[0]['time_interval']).' '.$reserve_info[0]['lounge'].'刪除失敗'.'<br>';
					} else {
						$message .= '刪除失敗:無此筆資料'.'<br>';
					}
				}	
    		}

    		if(!empty($message)){
    			$this->setAlert(2, $message);
    		} else {
    			$this->setAlert(1, '刪除成功');
    		}

    		redirect("other_work/teacher_lounge_sign/edit?{$_SERVER['QUERY_STRING']}");
    	}

    	$start_date = $this->input->get('start_date');
    	$end_date = $this->input->get('end_date');

		if(isset($start_date) && isset($end_date)){
			$this->data['sess_start_date'] = $start_date;
			$this->data['sess_end_date'] = $end_date;
			$date1 = date_create($this->data['sess_start_date']);
			$date2 = date_create($this->data['sess_end_date']);
			$diff = date_diff($date1,$date2);
			$this->data['days'] = $diff->days + 1;
		} else {
			$date = date('Y-m-d');
			$first = 1;

			$w = date('w',strtotime($date));  
			$week_start = date('Y-m-d',strtotime("$date -".($w ? $w - $first : 6).' days'));
			$week_end = date('Y-m-d',strtotime("$week_start +6 days"));
			$this->data['sess_start_date'] = $week_start;
			$this->data['sess_end_date'] = $week_end;
			$this->data['days'] = 7;
		}

		$data_list = $this->Teacher_lounge_model->getReserveList($this->data['sess_start_date'], $this->data['sess_end_date'], $this->flags->user['idno']);

		$new_data_list = array();
		for($i=0;$i<count($data_list);$i++){
			$data_list[$i]['description1'] = '<font style="margin-left:12px">'.$data_list[$i]['name'].'</font> '.'<font style="color:red">預約</font>'.'<br>';
            $data_list[$i]['description2'] = '班期：'.$data_list[$i]['class_name'].'<br>';
            $data_list[$i]['description3'] = '<font style="margin-left:12px">老師：</font>'.'<font style="color:blue">'.$data_list[$i]['teacher_name'].'</font><br>';

            $key = $data_list[$i]['time_interval'].'_'.$data_list[$i]['lounge'].'_'.$data_list[$i]['reserve_date'];
			$new_data_list[$key] = $data_list[$i];
		}

		$this->data['link_keep'] = base_url("other_work/teacher_lounge_sign/keep");
		$this->data['link_cancel_edit'] = base_url("other_work/teacher_lounge_sign?{$_SERVER['QUERY_STRING']}");
		$this->data['data_list'] = $new_data_list;

        $this->layout->view('other_work/teacher_lounge_sign/edit',$this->data);
    }

  	function getTimeInterval($type){
  		switch ($type) {
  			case 'A':
  				$time_interval = '08:00-12:00';
  				break;
  			case 'B':
  				$time_interval = '12:00-13:40';
  				break;
  			case 'C':
  				$time_interval = '13:40-17:30';
  				break;
  			case 'D':
  				$time_interval = '17:30-';
  				break;
  			default:
  				$time_interval = '無此時段';
  				break;
  		}

  		return $time_interval;
  	}

}
