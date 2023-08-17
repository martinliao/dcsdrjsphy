<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MixClassReport extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statistics_paper/Course_finish_count_model');
        $this->load->model('statistics_paper/MixClassReport_model');
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $season = isset($_GET['season'])?$_GET['season']:"";
        $type = isset($_GET['type'])?$_GET['type']:"";
        $startMonth = isset($_GET['startMonth'])?$_GET['startMonth']:"";
        $endMonth = isset($_GET['endMonth'])?$_GET['endMonth']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $searchTopic = "";
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 1 || $type == 2){
            $dateRange = $this->Course_finish_count_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->Course_finish_count_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1]; 
            } 
        }

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['ssearchTopic'] = $searchTopic;

        $this->data['link_refresh'] = base_url("statistics_paper/mixClassReport/");
        $this->data['datas'] = array();

        if(!empty($this->input->get())){
            $this->data['datas'] = $this->Course_finish_count_model->getCourseFinishCountData($year,$ssd,$sed,$searchTopic,'Y');


            $tmp_count = count($this->data['datas']);
            if($tmp_count > 0){
            	$final = $tmp_count-1;
            	$this->data['datas'][$final]['total_scount'] = 0;
            }
            
            for($i=0;$i<$tmp_count;$i++){
            	$this->data['datas'][$i]['scount'] = $this->MixClassReport_model->getSignStudentCount($this->data['datas'][$i]['year'],$this->data['datas'][$i]['class_no'],$this->data['datas'][$i]['term']);
            	$this->data['datas'][$final]['total_scount'] += $this->data['datas'][$i]['scount'];

            	$onlineCourseList = $this->MixClassReport_model->getOnlineCourse($this->data['datas'][$i]['year'],$this->data['datas'][$i]['class_no'],$this->data['datas'][$i]['term']);

            	$tmpOnlineCourseArray = array(); 
            	$tmpOnlineTeacherArray = array();
            	$tmpPhyCourseArray = array(); 
            	$tmpPhyTeacherArray = array();  
            	foreach ($onlineCourseList as $key => $value) {
            		if(!in_array($value['class_name'], $tmpOnlineCourseArray)){
            			$tmpOnlineCourseArray[] = $value['class_name'];
            		}

            		if(!in_array($value['teacher_name'], $tmpOnlineTeacherArray)){
            			$tmpOnlineTeacherArray[] = $value['teacher_name'];
            		}
            	}

            	if(count($tmpOnlineCourseArray) > 0){
            		for($j=0;$j<count($tmpOnlineCourseArray);$j++){
            			$tmpOnlineCourseArray[$j] = ($j+1).'、'.$tmpOnlineCourseArray[$j];
            		}
            		$this->data['datas'][$i]['onlineCourse'] = implode('<br>', $tmpOnlineCourseArray);
            	}

            	if(count($tmpOnlineTeacherArray) > 0){
            		for($j=0;$j<count($tmpOnlineTeacherArray);$j++){
            			if(!empty($tmpOnlineTeacherArray[$j])){
            				$tmpOnlineTeacherArray[$j] = ($j+1).'、'.$tmpOnlineTeacherArray[$j];
            			}
            		}
            		$this->data['datas'][$i]['onlineTeacher'] = implode('<br>', $tmpOnlineTeacherArray);
            	}

            	$phyCourseList = $this->MixClassReport_model->getphyCourse($this->data['datas'][$i]['year'],$this->data['datas'][$i]['class_no'],$this->data['datas'][$i]['term']);
                
            	foreach ($phyCourseList as $key => $value) {
            		if($value['isteacher'] == 'N'){
            			$phyCourseList[$key]['teacher_name'] = $phyCourseList[$key]['teacher_name'].'(助)';
            		}

            		if(!in_array($value['course_name'], $tmpPhyCourseArray)){
            			$tmpPhyCourseArray[] = $value['course_name'];
            		}

            		if(!in_array($phyCourseList[$key]['teacher_name'], $tmpPhyTeacherArray)){
            			$tmpPhyTeacherArray[] = $phyCourseList[$key]['teacher_name'];
            		}
            	}

            	if(count($tmpPhyCourseArray) > 0){
            		for($j=0;$j<count($tmpPhyCourseArray);$j++){
            			$tmpPhyCourseArray[$j] = ($j+1).'、'.$tmpPhyCourseArray[$j];
            		}
            		$this->data['datas'][$i]['phyCourse'] = implode('<br>', $tmpPhyCourseArray);
            	}

            	if(count($tmpPhyTeacherArray) > 0){
            		for($j=0;$j<count($tmpPhyTeacherArray);$j++){
            			$tmpPhyTeacherArray[$j] = ($j+1).'、'.$tmpPhyTeacherArray[$j];
            		}
            		$this->data['datas'][$i]['phyTeacher'] = implode('<br>', $tmpPhyTeacherArray);
            	}
            }

            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1){
            	$filename=date("YmdHis").".xls";   
				header("Content-type:application/vnd.ms-excel"); 
				header("Content-Disposition:filename=$filename");  

               	$this->load->view('statistics_paper/mixClassReport/exportView',$this->data);
            } else {
            	$this->layout->view('statistics_paper/mixClassReport/list',$this->data);
            }
        } else {
        	$this->layout->view('statistics_paper/mixClassReport/list',$this->data);
        }
    }
}
