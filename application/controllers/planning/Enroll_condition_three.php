<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Enroll_condition_three extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
    
        $this->load->model('planning/enroll_condition_three_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $class_number = array();

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $course_name=$this->data['filter']['query_class_name'];
            $result=$this->getCourseNumber($course_name);
            if (!empty($result)) {
                $data = array();

                for($i=0;$i<count($result);$i++) {
                    $course_no = $result[$i]['class_no'];
                    $data[]=$this->compareCourse($course_no);
                }
                for($j=0;$j<count($data);$j++) {
                    for($k=0;$k<count($data[$j]);$k++)
                    {   
                        $class_number[]=$data[$j][$k]['class_no_2'];
                    }
                }
            }
            else{
                $conditions['class_no_2'] = FALSE;
                $class_number[]=null;
            }
        }

        $attrs = array(
            'conditions' => $conditions,
            'class_number' => $class_number,
        );
        $this->data['filter']['total'] = $total = $this->enroll_condition_three_model->getListCount($attrs);
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

        if ($this->data['filter']['query_class_name'] !== '') {
            $course_name=$this->data['filter']['query_class_name'];
            $result=$this->getCourseNumber($course_name);
            if (!empty($result)) {
                $data = array();
                for ($i=0;$i<count($result);$i++) {
                    $course_no = $result[$i]['class_no'];
                    $data[]=$this->compareCourse($course_no);
                }
                for ($j=0;$j<count($data);$j++) {
                    for ($k=0;$k<count($data[$j]);$k++) {
                        $attrs['class_no_2'] = TRUE;
                        $class_number[]=$data[$j][$k]['class_no_2'];
                    }
                }
            } else {
                $attrs['class_no_2'] = false;
                $class_number[]=null;
            }
        }

        $this->data['list'] = $this->enroll_condition_three_model->getList($attrs);
        
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("planning/enroll_condition_three/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
            $tmp_class = explode(',', $row['class_no_2']);
            $tmp_array = array();
            for($i=0;$i<count($tmp_class);$i++){
                $class_name = $this->enroll_condition_three_model->getDistinctClassName($tmp_class[$i]);
                for($j=0;$j<count($class_name);$j++){
                    array_push($tmp_array, $class_name[$j]);
                }
            }

            $row['class_list'] = $tmp_array;
        }
        
        $this->load->library('pagination');
        $config['base_url'] = base_url("planning/enroll_condition_three?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("planning/enroll_condition_three/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("planning/enroll_condition_three/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("planning/enroll_condition_three/");

        $this->layout->view('planning/enroll_condition_three/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['form'] = $this->enroll_condition_three_model->getFormDefault();
        $this->data['choices']['condition'] = array(''=>'請選擇','in'=>'必修','not in'=>'擋修');

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $post['compare_type'] = 0;
                $post['class_no_2'] = substr($post['class'], 0,-1);
                unset($post['class']);
                $post['create_time'] = date('Y-m-d H:i:s');
                $post['create_user'] = $this->flags->user['id'];
                $post['modify_time'] = date('Y-m-d H:i:s');
                $post['modify_user'] = $this->flags->user['id'];
                $saved_id = $this->enroll_condition_three_model->_insert($post);
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url('planning/enroll_condition_three'));
            }
        }

        $this->data['link_save'] = base_url("planning/enroll_condition_three/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/enroll_condition_three/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/enroll_condition_three/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        
        if ($post = $this->input->post()) {
            if ($this->_isVerify('edit') == TRUE) {
                $post['class'] = substr($post['class'],0,-1);
                $post['class'] = array_unique(explode(',',$post['class']));
                $post['class_no_2'] = implode(',',$post['class']);
                unset($post['class']);
                $post['modify_time'] = date('Y-m-d H:i:s');
                $post['modify_user'] = $this->flags->user['id'];
                $rs = $this->enroll_condition_three_model->_update($id, $post);
                if ($rs) {
                    $this->setAlert(2, '資料編輯成功');
                }
                redirect(base_url("planning/enroll_condition_three/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['choices']['condition'] = array(''=>'請選擇','in'=>'必修','not in'=>'擋修');
        $tmp_data = $this->enroll_condition_three_model->getDataById($id);
       
        foreach ($tmp_data as & $row) {
            $this->data['form']['limit_name'] = $row['limit_name'];
            $this->data['form']['condition'] = $row['condition'];

            $tmp_class = explode(',', $row['class_no_2']);
            $tmp_array = array();
            for($i=0;$i<count($tmp_class);$i++){
                $class_name = $this->enroll_condition_three_model->getDistinctClassName($tmp_class[$i]);
                for($j=0;$j<count($class_name);$j++){
                    array_push($tmp_array, $class_name[$j]);
                }
            }

            $this->data['form']['class_list'] = $tmp_array;
            $this->data['form']['class'] = $row['class_no_2'].',';
        }

        $this->data['link_save'] = base_url("planning/enroll_condition_three/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("planning/enroll_condition_three/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('planning/enroll_condition_three/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $id) {
                $rs = $this->enroll_condition_three_model->delete($id);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("planning/enroll_condition_three/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->enroll_condition_three_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
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
        $this->db->select('class_no_2');
        $this->db->like('class_no_2',$course_no);
        $query = $this->db->get('enroll_condition_3');
        $result = $query->result_array();
        return $result;
    }
}
