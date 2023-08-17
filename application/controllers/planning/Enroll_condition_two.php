<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enroll_condition_two extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
    
        $this->load->model('planning/enroll_condition_two_model');
        $this->load->model('planning/set_startdate_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['group_name'])) {
            $this->data['filter']['group_name'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $this->data['choices']['group_name'] = $this->getGroup();
        $this->data['choices']['group_name'][''] = '請選擇群組';
       
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $class_number = array();
        if ($this->data['filter']['group_name'] !== '' ) {
            $conditions['group_name'] = $this->data['filter']['group_name'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $course_name=$this->data['filter']['query_class_name'];
            $result=$this->getCourseNumber($course_name);
            if (!empty($result)) {
                $data = array();
                for ($i=0;$i<count($result);$i++) {
                    $course_no = $result[$i]['class_no'];
                    $course_no=$this->compareCourse($course_no);
                    array_push($data, $course_no);
                }
                for ($j=0;$j<count($data);$j++) {
                    $course_number = $data[$j][0]['class_no'];
                    $class_number[]=$course_number;
                }
            }
            else{
                $conditions['class_no'] = false;
                $class_number[]=null;
            }
        }
      
        $attrs = array(
            'conditions' => $conditions,
            'class_number' => $class_number,
        );
     
        $this->data['filter']['total'] = $total = $this->enroll_condition_two_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
            'class_number' => $class_number,
        );

        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }
        if ($this->data['filter']['group_name'] !== '' ) {
            $attrs['group_name'] = $this->data['filter']['group_name'];
        }
        
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $course_name=$this->data['filter']['query_class_name'];
            $result=$this->getCourseNumber($course_name);
            if (!empty($result)) {
                $data = array();
                for ($i=0;$i<count($result);$i++) {
                    $course_no = $result[$i]['class_no'];
                    $course_no=$this->compareCourse($course_no);
                    array_push($data, $course_no);
                }
                for ($j=0;$j<count($data);$j++) {
                    $course_number = $data[$j][0]['class_no'];

                    $attrs['class_no'] = TRUE;
                    $class_number[]=$course_number;
                }
            }
            else{
                $attrs['class_no'] = false;
            }
        }
        
        $this->data['list'] = $this->enroll_condition_two_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("planning/enroll_condition_two/edit/{$row['group_id']}/?{$_SERVER['QUERY_STRING']}");
            $row['class_list'] = $this->enroll_condition_two_model->getClassList($row['group_id']);
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url("planning/enroll_condition_two?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("planning/enroll_condition_two/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("planning/enroll_condition_two/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("planning/enroll_condition_two/");
        
        $this->layout->view('planning/enroll_condition_two/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['form'] = $this->enroll_condition_two_model->getFormDefault();
        
        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $post['create_time'] = date('Y-m-d H:i:s');
                $post['create_user'] = $this->flags->user['id'];
                $post['modify_time'] = date('Y-m-d H:i:s');
                $post['modify_user'] = $this->flags->user['id'];
                $saved_id = $this->enroll_condition_two_model->insertData($post);
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url('planning/enroll_condition_two'));
            }
        }

        $this->data['link_save'] = base_url("planning/enroll_condition_two/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/enroll_condition_two/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/enroll_condition_two/add', $this->data);
    }

    public function edit($group_id=NULL)
    {
        $this->data['page_name'] = 'edit';
        
        if ($post = $this->input->post()) {
            if ($this->_isVerify('edit') == TRUE) {
                $post['create_time'] = date('Y-m-d H:i:s');
                $post['create_user'] = $this->flags->user['id'];
                $post['modify_time'] = date('Y-m-d H:i:s');
                $post['modify_user'] = $this->flags->user['id'];
                $rs = $this->enroll_condition_two_model->updateData($post);
                if ($rs) {
                    $this->setAlert(2, '資料編輯成功');
                }
                redirect(base_url("planning/enroll_condition_two/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $tmp_data = $this->enroll_condition_two_model->getDataByGroupId($group_id);
        foreach ($tmp_data as & $row) {
            $this->data['form']['group_id'] = $row['group_id'];
            $this->data['form']['group_name'] = $row['group_name'];
            $this->data['form']['limited'] = $row['limited'];
            $this->data['form']['class_list'] = $this->enroll_condition_two_model->getClassList($row['group_id']);
            $this->data['form']['class'] = '';
            for($i=0;$i<count($this->data['form']['class_list']);$i++){
                $this->data['form']['class'] .= $this->data['form']['class_list'][$i]['class_no'].',';
            }
        }

        $this->data['link_save'] = base_url("planning/enroll_condition_two/edit/{$group_id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/enroll_condition_two/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/enroll_condition_two/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $id) {
                $rs = $this->enroll_condition_two_model->deleteData($id);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("planning/enroll_condition_two/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->enroll_condition_two_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }
    public function getGroup()
    {
        $data = array();
        $this->db->select('group_name');
        $this->db->from('enroll_condition_2');
        $query = $this->db->get();
        $bureau = $query->result_array();

        foreach ($bureau as $key) {
            $data[$key['group_name']] = $key['group_name'];
        }

        return $data;
    }

    public function getCourseNumber($course_name)
    {   
        $this->db->select('class_no');
        $this->db->like('class_name',$course_name);
        $this->db->distinct();
        $query = $this->db->get('require');
        $result = $query->result_array();
        return $result;
    }

    public function compareCourse($course_no)
    {
        $this->db->select('class_no');
        $this->db->where('class_no',$course_no);
        $query = $this->db->get('enroll_condition_2');
        $result = $query->result_array();
        return $result;
    }
}
