<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_data_search extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/regist_contractors_model');
        $this->load->model('management/beaurau_persons_model');
        $this->load->model('management/code_table_model');
        $this->load->model('management/online_app_model');
        $this->load->model('customer_service/BS_user_model');
        $this->data['choices']['year'] = $this->_get_year_list();
        $this->data['choices']['year']['109'] = 109;
        krsort($this->data['choices']['year']);

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['term'])) {
            $this->data['filter']['term'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
        if (!isset($this->data['filter']['allQueryChecked'])) {
             $this->data['filter']['allQueryChecked'] = '';
        }
        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }

    }

    public function index()
    {
        //$allQueryChecked = ($this->data['filter']['allQueryChecked'] != '')? $this->data['filter']['allQueryChecked']:0 ;
        if($this->data['filter']['allQueryChecked']!=''){
            $allQueryChecked=$this->data['filter']['allQueryChecked'];
        }else{
            $allQueryChecked=0;
        }
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $idno=$this->flags->user['idno'];

        if($allQueryChecked == 0){

            $conditions = array();
            $conditions['year'] = $this->data['filter']['year'];
            $conditions['worker']=$idno;

            $attrs = array(
                'conditions' => $conditions,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] !== '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }

            if ($this->data['filter']['term'] !== '' ) {
                $attrs['term'] = $this->data['filter']['term'];
            }else{
                $attrs['term'] = '';
            }

            $this->data['filter']['total'] = $total = $this->regist_contractors_model->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] != '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }
            if ($this->data['filter']['sort'] != '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }
            if ($this->data['filter']['term'] !== '' ) {
                $attrs['term'] = $this->data['filter']['term'];
            }else{
                $attrs['term'] = '';
            }
            $this->data['list'] = $this->regist_contractors_model->getList($attrs);
            foreach ($this->data['list'] as & $row) {
                $row['link_regist'] = base_url("management/student_data_search/show/{$row['seq_no']}");
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("management/student_data_search?". $this->getQueryString(array(), array('page'))); 
            //die();
            //$this->data['list'] = array();
            //$this->data['filter']['total'] = $total = 0;
            //$this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        }else{
            
            $conditions = array();
            $conditions['year'] = $this->data['filter']['year'];
            $attrs = array(
                'conditions' => $conditions,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] !== '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }
            if ($this->data['filter']['term'] !== '' ) {
                $attrs['term'] = $this->data['filter']['term'];
            }else{
                $attrs['term'] = '';
            }
            $this->data['filter']['total'] = $total = $this->regist_contractors_model->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );
            $attrs['class_status'] = array('2','3');
            if ($this->data['filter']['class_name'] != '' ) {
                $attrs['class_name'] = $this->data['filter']['class_name'];
            }
            if ($this->data['filter']['class_no'] != '' ) {
                $attrs['class_no'] = $this->data['filter']['class_no'];
            }
            if ($this->data['filter']['sort'] != '' ) {
                $attrs['sort'] = $this->data['filter']['sort'];
            }
            if ($this->data['filter']['term'] !== '' ) {
                $attrs['term'] = $this->data['filter']['term'];
            }else{
                $attrs['term'] = '';
            }
            $this->data['list'] = $this->regist_contractors_model->getList($attrs);
            // jd($this->data['list'],1);
            foreach ($this->data['list'] as & $row) {
                $row['link_regist'] = base_url("management/student_data_search/show/{$row['seq_no']}");
            }
            $this->load->library('pagination');
            $config['base_url'] = base_url("management/student_data_search?". $this->getQueryString(array(), array('page'))); 
        }    
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("management/student_data_search/");
        $this->layout->view('management/student_data_search/list',$this->data);
    }
    public function show($seq_no=NULL)
    {   
        $class = $this->data['class'] = $this->regist_contractors_model->get($seq_no);
         if(!isset($this->data['class'])){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/student_data_search/'));
        }
        $attrs = array();
        $attrs['year'] = $class['year'];
        $attrs['class_no'] = $class['class_no'];
        $attrs['term'] = $class['term'];
        $attrs['where_not_in'] = array('6','2','7') ;
        //$class_list = $this->online_app_model->getList($attrs);
        $this->db->select('*');
        $this->db->where('year',$class['year']);
        $this->db->where('class_no',$class['class_no']);
        $this->db->where('term',$class['term']);
        $yn_sel=[2,6,7];
        $this->db->where_not_in('yn_sel',$yn_sel);
        $query=$this->db->get('online_app');
        $class_list=$query->result_array();

        $this->db->select('group_no');
        $this->db->distinct('group_no');
        $this->db->where('year',$class['year']);
        $this->db->where('class_no',$class['class_no']);
        $this->db->where('term',$class['term']);
        $yn_sel=[2,6,7];
        $this->db->where_not_in('yn_sel',$yn_sel);
        $query=$this->db->get('online_app');
        $this->data['group_no']=$query->result_array();
        //var_dump($group_no);

        $idarray=array();
        $st_no = array();
        foreach ($class_list as $key => $value) {//get idno
            $idarray[$key] = $value['id'];
            $st_no[$value['id']]['st_no'] = $value['st_no'];
            $st_no[$value['id']]['group_no'] = $value['group_no'];
            $st_no[$value['id']]['stop_reason'] = $value['stop_reason'];
            $st_no[$value['id']]['yn_sel'] = $value['yn_sel'];
        }
        //var_dump($st_no);
        $select = 'BS_user.bureau_id,job_title,BS_user.name,BS_user.co_empdb_poftel,f.description as position,BS_user.office_email,birthday,idno,cellphone,office_tel,email,gender,job_distinguish,education,d.name as bureau_name';
        $memberData = $this->BS_user_model->getMemberInfo($idarray,$select);
        foreach ($memberData as $key => $value) {
            $stno = $st_no[$value['idno']]['st_no'];
            $member[$stno]['group_no']=$st_no[$value['idno']]['group_no'];
            $member[$stno]['name'] = $value['name'];
            //$member[$stno]['birthday'] = date("y-m-d", strtotime($value['birthday']."-1911 year"));
            if(strlen(substr($value['birthday'],0,4)-'1911')<3){
                $year=substr($value['birthday'],0,4)-'1911';
                $year='0'.$year;
            }else{
                $year=substr($value['birthday'],0,4)-'1911';
            }
            $member[$stno]['birthday'] =$year.'/'.substr($value['birthday'],5,2).'/'.substr($value['birthday'],8,2);
            $member[$stno]['idno'] = $value['idno'];
            $member[$stno]['phone'] = is_null($value['co_empdb_poftel'])? $value['office_tel']: $value['co_empdb_poftel'];
            $member[$stno]['email'] = $value['office_email'];
            $member[$stno]['gender'] = $value['gender']=='F'? '女': '男';
            $member[$stno]['bureau_name'] = is_null($value['bureau_name'])? $this->online_app_model->getBureau($value['bureau_id']): $value['bureau_name'] ; //單位
            $member[$stno]['job_title'] =  $this->code_table_model->getJobTitle($value['job_title']); //職稱
            $member[$stno]['job_distinguish'] = $this->code_table_model->getPosition($value['job_distinguish']); //區分
            // $member[$stno]['job_distinguish'] = $value['job_distinguish']; 
            $member[$stno]['education'] = $this->code_table_model->getEducation($value['education']);//學歷
            //$member[$stno]['stop_reason'] =  is_null($st_no[$value['idno']]['stop_reason'])? '':$st_no[$value['idno']]['stop_reason'];
            $member[$stno]['yn_sel'] = $st_no[$value['idno']]['yn_sel'];
        }
        
        if(isset($member)) {
            ksort($member);
            $this->data['list'] = $member;
        }
        $this->load->library('pagination');
        $this->data['filter']['total'] = count($idarray);
        // $config['total_rows'] = 200;
        // $config['per_page'] = 20;
        // $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("management/student_data_search/");
        $this->layout->view('management/student_data_search/show',$this->data);
    }

    public function _get_year_list()
    {
        $year_list = array();

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }

}
