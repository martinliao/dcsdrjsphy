<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Old_teacher_evaluate_score extends MY_Controller
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
        $this->data['link_refresh'] = base_url("old_report/old_teacher_evaluate_score/");
        $this->data['link_new'] = base_url("old_report/teacher_evaluate_score/"); 
        $this->data['datas'] = array();
        if($this->input->get()){
            $list = $this->old_report_model->getOldTeacherScoreList($teacher_name, $teacher_id, $course_name, $class_name, $start_date, $end_date);
            $this->data['filter']['total'] = $total = count($list);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $list = $this->old_report_model->getOldTeacherScoreList($teacher_name, $teacher_id, $course_name, $class_name, $start_date, $end_date, $rows, $offset);
            
            for($i=0;$i<count($list);$i++){
                $year = str_pad($list[$i]['year'],3,'0',STR_PAD_LEFT);
                $check = $this->old_report_model->getQuestionData($year, $list[$i]['class_no'], $list[$i]['term']);
                if($check){
                    $list[$i]['export_url_1'] = base_url("old_report/old_teacher_evaluate_score/q_qw_excel2?y={$list[$i]['year']}&c={$list[$i]['class_no']}&t={$list[$i]['term']}");
                    $list[$i]['export_url_2_1'] = base_url("old_report/old_teacher_evaluate_score/q_qw_excel5?y={$list[$i]['year']}&c={$list[$i]['class_no']}&t={$list[$i]['term']}&type=1");
                    $list[$i]['export_url_2_2'] = base_url("old_report/old_teacher_evaluate_score/q_qw_excel5?y={$list[$i]['year']}&c={$list[$i]['class_no']}&t={$list[$i]['term']}&type=2");
                    $list[$i]['export_url_3'] = base_url("old_report/old_teacher_evaluate_score/q_qw_download_other_main?y={$list[$i]['year']}&c={$list[$i]['class_no']}&t={$list[$i]['term']}");
                }
            }

            $this->data['datas'] = $list;
        }

        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->layout->view('old_report/old_teacher_evaluate_score/list',$this->data);
    }
    
    public function q_qw_excel2()
    {
        $year=trim($_GET['y']);
        $year = str_pad($year,3,'0',STR_PAD_LEFT);
        $term=intval(trim($_GET['t']));
        $class_no=addslashes(trim($_GET['c']));

        $list = $this->old_report_model->getQuestionData($year, $class_no, $term);

        if(!empty($list)){
            $list[0]['QUESTION'] = $this->old_report_model->getDefaultQuestionData($list[0]['QD_ID']);

            for($i=0;$i<count($list[0]['QUESTION']);$i++){
                $list[0]['QUESTION'][$i]['answer'] = $this->old_report_model->getOpenQuestion($year, $class_no, $term, $list[0]['QUESTION'][$i]['question']);
            }
        }

        header("Expires: ".gmdate("D, d M Y H:i:s",time()+(0*60))." GMT");
        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=Download.xls'); 

        $this->data['list'] = $list;
        $this->load->view('old_report/old_teacher_evaluate_score/q_qw_excel2',$this->data);
    }

    public function q_qw_excel5()
    {
        $year=trim($_GET['y']);
        $year = str_pad($year,3,'0',STR_PAD_LEFT);
        $term=intval(trim($_GET['t']));
        $class_no=addslashes(trim($_GET['c']));
        $type = trim($_GET['type']);
        
        $list = $this->old_report_model->getQuestionData($year, $class_no, $term);

        if(!empty($list)){
            $list[0]['online_count'] = $this->old_report_model->getOnlineAppCount($year, $class_no, $term);
            $list[0]['t_evaluate_count'] = $this->old_report_model->getTEvaluateCount($year, $class_no, $term);
            $list[0]['QUESTION'] = $this->old_report_model->getDefaultQuestionDataForRestaurant($list[0]['QD_ID']);

            for($i=0;$i<count($list[0]['QUESTION']);$i++){
                $all = $this->old_report_model->getAnswerData($list[0]['QD_ID'], $list[0]['QUESTION'][$i]['id'], 1);

                if($type == '1'){
                    $list[0]['type'] = '滿意'; 
                    $score = $this->old_report_model->getAnswerData($list[0]['QD_ID'], $list[0]['QUESTION'][$i]['id'], 2);
                } else {
                    $list[0]['type'] = '不滿意'; 
                    $score = $this->old_report_model->getAnswerData($list[0]['QD_ID'], $list[0]['QUESTION'][$i]['id'], 3);
                }
                
                $list[0]['QUESTION'][$i]['percent'] = round($score/$all * 100, 2);
                $list[0]['QUESTION'][$i]['answer'] = $this->old_report_model->getOpenQuestion($year, $class_no, $term, $list[0]['QUESTION'][$i]['question']);
            }

            if($list[0]['online_count'] > 0){
                if($list[0]['t_evaluate_count'] > $list[0]['online_count']){
                    $list[0]['receive_rate'] = round($list[0]['t_evaluate_count']/$list[0]['online_count']*50,2);
                } else {
                    $list[0]['receive_rate'] = round($list[0]['t_evaluate_count']/$list[0]['online_count']*100,2);
                }
            }
        }

        header("Expires: ".gmdate("D, d M Y H:i:s",time()+(0*60))." GMT");
        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=Download.xls'); 

        $this->data['list'] = $list;
        $this->load->view('old_report/old_teacher_evaluate_score/q_qw_excel5',$this->data);
    }

    public function q_qw_download_other_main()
    {
        $year=trim($_GET['y']);
        $year = str_pad($year,3,'0',STR_PAD_LEFT);
        $term=intval(trim($_GET['t']));
        $class_no=addslashes(trim($_GET['c']));
        
        $list = $this->old_report_model->getOpenQuestionDetail($year, $class_no, $term);

        header("Expires: ".gmdate("D, d M Y H:i:s",time()+(0*60))." GMT");
        header('Content-Type:   application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename=Download.xls'); 

        $this->data['list'] = $list;
        $this->load->view('old_report/old_teacher_evaluate_score/q_qw_download_other_main',$this->data);
    }
}
