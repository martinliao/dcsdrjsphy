<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Older_teacher_evaluate_score extends MY_Controller
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
        $this->data['link_refresh'] = base_url("old_report/older_teacher_evaluate_score/");
        // $this->data['link_new'] = base_url("old_report/teacher_evaluate_score/");
        $this->data['datas'] = array();
        if($this->input->get()){
            $list = $this->old_report_model->getOlderTeacherScoreList($teacher_name, $teacher_id, $course_name, $class_name, $start_date, $end_date);
            $this->data['filter']['total'] = $total = count($list);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $list = $this->old_report_model->getOlderTeacherScoreList($teacher_name, $teacher_id, $course_name, $class_name, $start_date, $end_date, $rows, $offset);
            
            $this->data['datas'] = $list;
        }

        
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->layout->view('old_report/older_teacher_evaluate_score/list',$this->data);
    }
    
    public function exportCsv()
    {
        $question_id=intval($_GET['question_id']);

        $data = $this->old_report_model->searchSpecialDetailGd($question_id);

        header("Content-type: application/csv");    
        header("Content-Disposition: attachment; filename=openDetail.csv;charset=UTF-8");
        header("Pragma: no-cache");
        header("Expires: 0");

        $title = "題目,";
        $title .= "答案,";
        $title .= "人次\r\n";
        echo mb_convert_encoding($title , 'Big5', 'UTF-8');
        
        $output = '';
        foreach($data as $item) {
            $item['ANSWER'] = unserialize($item['ANSWER']);
            // $csv .= '"' . $row['id'] . '",';
            $output .= '"'.$item['ITEM_TITLE'].'",';
            $output .= '"'.preg_replace('/\s+/', ' ',$item['ANSWER']).'",'; // remove white space
            $output .= '"'.$item['ANSWER_TIMES'].'"';
            $output .= PHP_EOL;
        }
        echo mb_convert_encoding($output , 'Big5', 'UTF-8');
        exit;
    }
}
