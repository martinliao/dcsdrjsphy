<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_evaluate_score extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('old_report/old_report_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }
    public function index()
    {
        $start_date = isset($_GET['start_date'])?addslashes($_GET['start_date']):"";
        $end_date = isset($_GET['end_date'])?addslashes($_GET['end_date']):"";
        $course_name = isset($_GET['course_name'])?addslashes($_GET['course_name']):"";
        $class_name = isset($_GET['class_name'])?addslashes($_GET['class_name']):"";
        $teacher_name = isset($_GET['teacher_name'])?addslashes($_GET['teacher_name']):"";
        $teacher_id = isset($_GET['teacher_id'])?addslashes($_GET['teacher_id']):"";
        
        $this->load->library('pagination');
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $total = 0;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_course_name'] = $course_name;
        $this->data['sess_class_name'] = $class_name;
        $this->data['sess_teacher_name'] = $teacher_name;
        $this->data['sess_teacher_id'] = $teacher_id;
        $this->data['link_refresh'] = base_url("old_report/teacher_evaluate_score/");
        $this->data['link_old'] = base_url("old_report/old_teacher_evaluate_score/");
        $this->data['datas'] = array();
        if($this->input->get()){
            $list = $this->old_report_model->getTeacherScoreList($teacher_name, $teacher_id, $course_name, $class_name, $start_date, $end_date);

            $this->data['filter']['total'] = $total = count($list);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $list = $this->old_report_model->getTeacherScoreList($teacher_name, $teacher_id, $course_name, $class_name, $start_date, $end_date, $rows, $offset);

            for($i=0;$i<count($list);$i++){
                $cnt = $this->old_report_model->get_teacher_cnt_gender_score($list[$i]['class_id'],$list[$i]['year'],$list[$i]['term'],$list[$i]['course_code']);
                $list[$i]['score_list'] = $this->old_report_model->get_teacher_score($list[$i]['class_id'],$list[$i]['year'],$list[$i]['term'],$list[$i]['course_code'],$list[$i]['id']);
                
                $list[$i]['male'] = 0;
                if(intval($cnt['male'])!=0 && intval($cnt['m_cnt'])!=0){
                    $list[$i]['male'] = round(intval($cnt['male'])/intval($cnt['m_cnt']), 2);
                }

                $list[$i]['female'] = 0;
                if(intval($cnt['female'])!=0 && intval($cnt['f_cnt'])!=0){
                    $list[$i]['female'] = round(intval($cnt['female'])/intval($cnt['f_cnt']), 2);
                }
                
                $list[$i]["full_class_name"] = $list[$i]["year"].'年 '.$list[$i]["class_name"].' 第'.$list[$i]["term"].'期';
            }
            
            $this->data['datas'] = $list;
        }

        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->layout->view('old_report/teacher_evaluate_score/list',$this->data);
    }

}
