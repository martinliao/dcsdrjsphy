<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_learn_record extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('customer_service/student_learn_record_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '1';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['query_student_name'])) {
            $this->data['filter']['query_student_name'] = '';
        }
        if (!isset($this->data['filter']['start_date1'])) {
            $this->data['filter']['start_date1'] = '';
        }
        if (!isset($this->data['filter']['end_date1'])) {
            $this->data['filter']['end_date1'] = '';
        }
        if (!isset($this->data['filter']['query_bureau_name'])) {
            $this->data['filter']['query_bureau_name'] = '';
        }
        
        if (!isset($this->data['filter']['clear'])) {
            $this->data['filter']['clear'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $conditions = array();
       
        $this->data['filter']['clear']=$this->input->post('clear');

        $idno=$this->session->userdata('idno');

        //var_dump($idno);
        if($idno!=''){
            $conditions['online_app.id'] = $idno;
        }
        
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['online_app.year'] = $this->data['filter']['query_year'];
        }
        if ($this->data['filter']['start_date1'] !== '' ) {
            $conditions['start_date1 >='] = $this->data['filter']['start_date1'];
        }

        if ($this->data['filter']['end_date1'] !== '' ) {
            $conditions['start_date1 <='] = $this->data['filter']['end_date1'];
        }
        if ($this->data['filter']['clear'] == '0' ) {
            $this->session->unset_userdata('idno');
            //die();
            redirect(base_url('customer_service/student_learn_record'),'refresh');
        }

        $username=substr($this->flags->user['username'],0,4);
        $bureau_id=$this->flags->user['bureau_id'];

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        
        $attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['query_student_name'] != '') {
            $attrs['query_student_name'] = $this->data['filter']['query_student_name'];
            //die();
        }
        if ($this->data['filter']['query_bureau_name'] != '') {
            $attrs['query_bureau_name'] = $this->data['filter']['query_bureau_name'];
        }
        
        $this->data['filter']['total'] = $total = $this->student_learn_record_model->getListCount($attrs,$bureau_id,$username);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['query_student_name'] != '') {
            $attrs['query_student_name'] = $this->data['filter']['query_student_name'];
            //die();
        }
        if ($this->data['filter']['query_bureau_name'] != '') {
            $attrs['query_bureau_name'] = $this->data['filter']['query_bureau_name'];
        }

        //$this->data['list'] = $this->student_learn_record_model->getList($attrs,$bureau_id,$username);
        

        $this->load->library('pagination');
        $config['base_url'] = base_url("customer_service/student_learn_record?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("customer_service/student_learn_record/");
        $this->data['link_search'] = base_url("customer_service/student_learn_record/search");
        $this->data['link_export'] = base_url("customer_service/student_learn_record/export");
        $this->data['link_import'] = base_url("customer_service/student_learn_record/import");
        $this->layout->view('customer_service/student_learn_record/list',$this->data);
    }
    public function export($seq_no=null)
    {

        /*$this->data['filter']['query_year']=$this->input->post('query_year');
        $this->data['filter']['start_date1']=$this->input->post('start_date1');
        $this->data['filter']['end_date1']=$this->input->post('end_date1');
        $this->data['filter']['clear']=$this->input->post('clear');
        $this->data['filter']['query_class_name']=$this->input->post('query_class_name');
        $this->data['filter']['query_student_name']=$this->input->post('query_student_name');
        $this->data['filter']['query_bureau_name']=$this->input->post('query_bureau_name');*/
        
        $idno= $this->session->userdata('idno');

        //var_dump($idno);
        if($idno!=''){
            $conditions['online_app.id'] = $idno;
        }
        
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['online_app.year'] = $this->data['filter']['query_year'];
        }
        if ($this->data['filter']['start_date1'] !== '' ) {
            $conditions['start_date1 >='] = $this->data['filter']['start_date1'];
        }

        if ($this->data['filter']['end_date1'] !== '' ) {
            $conditions['end_date1 <='] = $this->data['filter']['end_date1'];
        }

        $username=substr($this->flags->user['username'],0,4);
        $bureau_id=$this->flags->user['bureau_id'];

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        
        $attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['query_student_name'] != '') {
            $attrs['query_student_name'] = $this->data['filter']['query_student_name'];
            //die();
        }
        if ($this->data['filter']['query_bureau_name'] != '') {
            $attrs['query_bureau_name'] = $this->data['filter']['query_bureau_name'];
        }
        
       

        $attrs = array(
            'conditions' => $conditions,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['query_student_name'] != '') {
            $attrs['query_student_name'] = $this->data['filter']['query_student_name'];
            //die();
        }
        if ($this->data['filter']['query_bureau_name'] != '') {
            $attrs['query_bureau_name'] = $this->data['filter']['query_bureau_name'];
        }

        $info = $this->student_learn_record_model->getList($attrs,$bureau_id,$username);

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=learn_record.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $filename = 'learn_record.csv';
        echo iconv("UTF-8", "BIG5", "機關參訓學員學習紀錄\r\n");
        echo iconv("UTF-8", "BIG5", "姓名,");
        echo iconv("UTF-8", "BIG5", "年度,");
        echo iconv("UTF-8", "BIG5", "機關,");
        echo iconv("UTF-8", "BIG5", "身分證字號,");
        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "期別,");
        echo iconv("UTF-8", "BIG5", "開課起迄日,");
        echo iconv("UTF-8", "BIG5", "研習時數,\r\n");
        
        for ($i=0;$i<count($info);$i++) {
            echo "\"".iconv("UTF-8", "BIG5//IGNORE", $info[$i]['name']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['year']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['company']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['id']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['class_name']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']."\",");
            echo "\"".iconv("UTF-8", "BIG5", substr($info[$i]['start_date1'],0,10).'~'.substr($info[$i]['end_date1'],0,10)."\",");
            echo "\"".iconv("UTF-8", "BIG5//IGNORE", $info[$i]['range']."\"\r\n");
        }

    }
    public function import()
    {
        if(isset($_FILES['myfile']['name'])){
			if(basename($_FILES['myfile']['name']) == 'student_record.csv'){
				$uploaddir = DIR_UPLOAD_FILES;
				$uploadfile = $uploaddir.basename($_FILES['myfile']['name']);
				$uploadfile = iconv("utf-8", "big5", $uploadfile);
				if (move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile)) {    
					$fp = fopen ($uploadfile,"r") or die("無法開啟");
					$data = array();
					$row = 0;
					while(!feof($fp)){
						$content = fgets($fp);
						// $content = mb_convert_encoding($content, 'UTF-8', 'BIG5');
						$fields = explode(",",$content);
                        if($row == '1' && count($fields) == 1 && !empty($fields[0])){
                                $data['id'] = trim($fields[0]);
                                if(!empty($data['id'])){
                                    $this->db->select('bureau_id,username');
                                    $this->db->where('idno',$data['id']);
                                    $query=$this->db->get('BS_user');
                                    $result=$query->result_array();
                                }
                                $conditions['online_app.id'] = $data['id'];
                                $username=substr($this->flags->user['username'],0,4);
                                $bureau_id=$this->flags->user['bureau_id'];
                                $page = $this->data['filter']['page'];
                                $rows = $this->data['filter']['rows'];

                                $attrs = array(
                                    'conditions' => $conditions,
                                );

                                $this->data['filter']['total'] = $total = $this->student_learn_record_model->getListCount($attrs,$bureau_id,$username);
                                $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
                                
                                $attrs = array(
                                    'conditions' => $conditions,
                                    'rows' => $rows,
                                    'offset' => $offset,
                                );
                                $arr=array('idno'=>$data['id']);
                                $this->data['list'] = $this->student_learn_record_model->getList($attrs,$bureau_id,$username);

                                $this->session->set_userdata($arr);
                                //$this->session->set_flashdata('key',$data['id']);
                                redirect(base_url("customer_service/student_learn_record/search"));
						}
						$row = 1;
					}
				}
			}
        }
        $this->data['link_import'] = base_url("customer_service/student_learn_record/import");
        $this->layout->view('customer_service/student_learn_record/import',$this->data);
    }
    public function search()
    {
        $this->data['page_name'] = 'list';

        $conditions = array();
        
        /*$this->data['filter']['query_year']=$this->input->post('query_year');
        $this->data['filter']['start_date1']=$this->input->post('start_date1');
        $this->data['filter']['end_date1']=$this->input->post('end_date1');
        $this->data['filter']['clear']=$this->input->post('clear');
        $this->data['filter']['query_class_name']=$this->input->post('query_class_name');
        $this->data['filter']['query_student_name']=$this->input->post('query_student_name');
        $this->data['filter']['query_bureau_name']=$this->input->post('query_bureau_name');*/
        //var_dump($_Get['query_year']);
        /*$this->data['filter']['query_year']=$_REQUSET['query_year'];
        $this->data['filter']['start_date1']=$this->input->post('start_date1');
        $this->data['filter']['end_date1']=$this->input->post('end_date1');
        $this->data['filter']['clear']=$this->input->post('clear');
        $this->data['filter']['query_class_name']=$this->input->post('query_class_name');
        $this->data['filter']['query_student_name']=$this->input->post('query_student_name');
        $this->data['filter']['query_bureau_name']=$this->input->post('query_bureau_name');
*/      
        //$test=$_get['query_year'];
        //var_dump($test);

        if ($this->data['filter']['query_year'] == null) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        
        $idno=$this->session->userdata('idno');
       
        if($idno!=''){
            $conditions['online_app.id'] = $idno;
        }
        
        /*if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['online_app.year'] = $this->data['filter']['query_year'];
        }
        if ($this->data['filter']['start_date1'] !== '' ) {
            $conditions['start_date1 >='] = $this->data['filter']['start_date1'];
        }*/
        if ($this->data['filter']['query_year']!=='') {
            $conditions['online_app.year'] = $this->data['filter']['query_year'];
        }

        if (!empty($this->data['filter']['start_date1'])) {
            $conditions['start_date1 >='] = $this->data['filter']['start_date1'];
        }

        if (!empty($this->data['filter']['end_date1'])) {
            $conditions['end_date1 <='] = $this->data['filter']['end_date1'];
        }
        //die();
        if ($this->data['filter']['clear']=='0') {
            $this->session->unset_userdata('idno');
            redirect(base_url('customer_service/student_learn_record'),'refresh');
        }
        
        $username=substr($this->flags->user['username'],0,4);
        $bureau_id=$this->flags->user['bureau_id'];

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        
        $attrs = array(
            'conditions' => $conditions,
        );

        /*if ($this->data['filter']['query_class_name'] !== '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['query_student_name'] !== '') {
            $attrs['query_student_name'] = $this->data['filter']['query_student_name'];
        }
        if ($this->data['filter']['query_bureau_name'] !== '') {
            $attrs['query_bureau_name'] = $this->data['filter']['query_bureau_name'];
        }*/
        if (!empty($this->data['filter']['query_class_name'])) {
            $attrs['query_class_name']= $this->data['filter']['query_class_name'];
        }
        if (!empty($this->data['filter']['query_student_name'])) {
            $attrs['query_student_name']= $this->data['filter']['query_student_name'];
        }
        if (!empty($this->data['filter']['query_bureau_name'])) {
            $attrs['query_bureau_name']= $this->data['filter']['query_bureau_name'];
        }
        
        $this->data['filter']['total'] = $total = $this->student_learn_record_model->getListCount($attrs,$bureau_id,$username);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

       /* if ($this->data['filter']['query_class_name'] !== '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['query_student_name'] !== '') {
            $attrs['query_student_name'] = $this->data['filter']['query_student_name'];
            //die();
        }
        if ($this->data['filter']['query_bureau_name'] !== '') {
            $attrs['query_bureau_name'] = $this->data['filter']['query_bureau_name'];
        }*/
        if (!empty($this->data['filter']['query_class_name'])) {
            $attrs['query_class_name']= $this->data['filter']['query_class_name'];
        }
        if (!empty($this->data['filter']['query_student_name'])) {
            $attrs['query_student_name']= $this->data['filter']['query_student_name'];
        }
        if (!empty($this->data['filter']['query_bureau_name'])) {
            $attrs['query_bureau_name']= $this->data['filter']['query_bureau_name'];
        }

        $this->data['list'] = $this->student_learn_record_model->getList($attrs,$bureau_id,$username);
        //var_dump($this->data['list']);
        
        // if(preg_match("/^61.216.24.9[5,6]$/", $_SERVER["REMOTE_ADDR"])) {
        //     echo '<pre>';
        //     print_r($this->data['list']);
        //     die();
        // }
        

        $this->load->library('pagination');
        //$config['base_url'] = base_url("customer_service/student_learn_record/search". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("customer_service/student_learn_record/");
        $this->data['link_search'] = base_url("customer_service/student_learn_record/search");
        $this->data['link_export'] = base_url("customer_service/student_learn_record/export");
        $this->data['link_import'] = base_url("customer_service/student_learn_record/import");
        $this->layout->view('customer_service/student_learn_record/list',$this->data);
    }

}
