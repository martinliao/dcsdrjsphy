<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Course_public extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('search_work/Course_public_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {
        $query_class_name = isset($_GET['classname']) ? addslashes($_GET['classname']) : "";
        $query_type = isset($_GET['type']) ? addslashes($_GET['type']) : "";
        $use_s_date = isset($_GET['use_s_date']) ? addslashes($_GET['use_s_date']) : "";
        $use_e_date = isset($_GET['use_e_date']) ? addslashes($_GET['use_e_date']) : "";
        $t_name = isset($_GET['t_name']) ? addslashes($_GET['t_name']) : "";
        $t_source = isset($_GET['t_source']) ? addslashes($_GET['t_source']) : "";
        $cre_s_date = isset($_GET['cre_s_date']) ? addslashes($_GET['cre_s_date']) : "";
        $cre_e_date = isset($_GET['cre_e_date']) ? addslashes($_GET['cre_e_date']) : "";
        $edu = isset($_GET['edu']) ? addslashes($_GET['edu']) : "";
        $job = isset($_GET['job']) ? addslashes($_GET['job']) : "";
        $pagetype = isset($_GET['pagetype']) ? addslashes($_GET['pagetype']) : "";

        $this->data['sess_classname'] = $query_class_name;
        $this->data['sess_type'] = $query_type;
        $this->data['sess_use_s_date'] = $use_s_date;
        $this->data['sess_use_e_date'] = $use_e_date;
        $this->data['sess_t_name'] = $t_name;
        $this->data['sess_t_source'] = $t_source;
        $this->data['sess_cre_s_date'] = $cre_s_date;
        $this->data['sess_cre_e_date'] = $cre_e_date;
        $this->data['sess_edu'] = $edu;
        $this->data['sess_job'] = $job;

        $this->data['source'] = $this->Course_public_model->get_source_list();
        $this->data['types'] = $this->Course_public_model->get_Stype_list();
        $this->data['studentype'] = $this->Course_public_model->get_StudentData_list();

        $this->data['link_refresh'] = base_url("search_work/course_public/");

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if(($use_s_date != "" && $use_e_date != "") || ($cre_s_date != "" && $cre_e_date != "")){
            $this->data['datas'] = $this->Course_public_model->getCoursePublicData($query_class_name, $query_type, $use_s_date, $use_e_date, $t_name, $t_source, $cre_s_date, $cre_e_date, $edu, $job);
        }
        else {
            $this->data['datas'] = array();
        }
        
        $this->data['filter']['total'] = $total = count($this->data['datas']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        if($total > 0) {
            $this->data['datas'] = $this->Course_public_model->getCoursePublicData($query_class_name, $query_type, $use_s_date, $use_e_date, $t_name, $t_source, $cre_s_date, $cre_e_date, $edu, $job, $rows, $offset);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->load->library('pagination');
        $config['base_url'] = base_url("search_work/course_public?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        if($pagetype == "detail"){
            $YEAR = isset($_GET['year']) ? addslashes($_GET['year']) : "";
            $CLASS_NO = isset($_GET['class_no']) ? addslashes($_GET['class_no']) : "";
            $TERM = isset($_GET['term']) ? addslashes($_GET['term']) : "";
            $query_detail_string = sprintf("a.use_date is not null and a.year=%s AND a.class_id=trim(ltrim(%s)) and a.term=%s ", 
                $this->db->escape($YEAR),
                $this->db->escape($CLASS_NO),
                $this->db->escape($TERM)
            );
            
            $output = array();
            $output["data"] =  $this->Course_public_model->getDataFunction($YEAR,$CLASS_NO,$TERM);
            $output["list"] =  $this->Course_public_model->get_list($query_detail_string);
            // $output["list"] = array();
            $output["classRoomName"] =  $this->Course_public_model->get_Classroom_Name_List($query_detail_string);
            $output["mixlist"] =  $this->Course_public_model->get_mixlist($YEAR,$CLASS_NO,$TERM);
            $output["roomCount"] =  $this->Course_public_model->get_room_count($query_detail_string);
            echo json_encode($output);
        }
        else{
            if(($use_s_date != "" && $use_e_date != "") || ($cre_s_date != "" && $cre_e_date != "")) {
                $this->data['dayOfWeek'] = $this->Course_public_model->getDayOfWeek();
                if (isset($_GET['iscsv']) && $_GET['iscsv'] == 1) {
                    $this->Course_public_model->csvexport(date("Y-m-d"), "", "", $this->data['dayOfWeek'], $query_class_name, $query_type, $use_s_date, $use_e_date, $t_name, $t_source, $cre_s_date, $cre_e_date, $edu, $job);
                } else {
                    $this->layout->view('search_work/course_public/list', $this->data);
                }
            }
            else {
                $this->layout->view('search_work/course_public/list', $this->data);
            }
        }

    }

}
