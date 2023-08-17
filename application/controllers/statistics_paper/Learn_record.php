<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Learn_record extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('management/leave_model');
        $this->load->model(['require_model', 'online_app_model']);
    }

    public function index()
    {
    	if($this->input->post()){
    		$markList = $this->input->post('mark');
    		$cancelList = $this->input->post('cancel');
    		
    		if(count($markList) > 0){
    			for($i=0;$i<count($markList);$i++){
    				$this->leave_model->updateMark($markList[$i],1);
    			}
    			
    		}

    		if(count($cancelList) > 0){
    			for($i=0;$i<count($cancelList);$i++){
    				$this->leave_model->updateMark($cancelList[$i],0);
    			}
    		}

    		
            $this->setAlert(1, '儲存成功');          
            redirect(base_url("statistics_paper/learn_record?{$_SERVER['QUERY_STRING']}"));
    	}

    	$thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;

        if($this->input->get()){
        	$this->data['list'] = $this->leave_model->getLearnRecord($this->input->get(),$this->flags->user['bureau_id']);
        }

        $this->data['sess_year'] = $year;
        $this->data['start_date'] = isset($_GET['start_date'])?$_GET['start_date']:'';
        $this->data['end_date'] = isset($_GET['end_date'])?$_GET['end_date']:'';
        $this->data['class_name'] = isset($_GET['class_name'])?$_GET['class_name']:'';
        $this->data['student_name'] = isset($_GET['student_name'])?$_GET['student_name']:'';
        $this->data['idno'] = isset($_GET['idno'])?$_GET['idno']:'';
        $this->data['link_save2'] = base_url("statistics_paper/learn_record?{$_SERVER['QUERY_STRING']}");
   
        $this->layout->view('statistics_paper/learn_record/list',$this->data);
    }

    public function record(){
    	$condition = $this->getFilterData(['year', 'class_no', 'term', 'vacation_date', 'id', 'no']);
        $this->data['class_info'] = $this->require_model->find($condition);
        $this->data['learns'] = $this->leave_model->getLearnList($condition);
    

        //將未報到的人的資料整理成一筆
        $index=[];//紀錄學員狀態是未報到的index
        for($i=0;$i<count($this->data['learns']);$i++){
            if($this->data['learns'][$i]->yn_sel=='5'){
                $index[$i]=$this->data['learns'][$i]->name;
            }
        }
        

        
            //var_dump(array_count_values($index));
            $index_name=array_count_values($index);//記錄這個人有幾筆重複
            //var_dump($index_name);
        //}

        //大於一筆才做unset重複的資料
        $index2=array_unique($index);
        for($j=0;$j<count($this->data['learns']);$j++){
            if(isset($index2[$j])&&$index_name[$index2[$j]]>=2){
                unset($this->data['learns'][$j]);
            }
        }

       
        $params = array();
        $params['year'] = $condition['year'];
        $params['class_id'] = $condition['class_no'];
        $params['term'] = $condition['term'];
        $room_uses = $this->leave_model->getRoomUse($params);

        $this->data['coursedate_list'] = '';
        if(!empty($room_uses)){
            foreach($room_uses as $room_use){
                $this->data['coursedate_list'] .= date('m/d',strtotime($room_use->use_date)).'、';
            }

            $this->data['coursedate_list'] = mb_substr($this->data['coursedate_list'],0,-1,'utf-8');
        }
           

        $this->data['learns']=array_values($this->data['learns']);

        $this->load->view('statistics_paper/learn_record/record', $this->data);
       
    }

}
