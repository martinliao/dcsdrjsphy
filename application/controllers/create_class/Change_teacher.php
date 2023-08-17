<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_teacher extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('create_class/change_teacher_model');
	
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] != '') {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->change_teacher_model->getListCount($attrs);
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

		$this->data['list'] = $this->change_teacher_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("create_class/change_teacher/edit/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("create_class/change_teacher?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("create_class/change_teacher/");

		$this->layout->view('create_class/change_teacher/list', $this->data);
	}

    public function edit($id=NULL)
    {
        if ($post = $this->input->post()) {
            $old_data = $this->change_teacher_model->get($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {
                $year = $post['year'];
                $class_no = $post['class_no'];
                $term = $post['term'];
                $new_teacher_id = $post['new_teacher_id'];
                $new_teacher_title = $post['new_teacher_title_name'];
                $old_data = explode('::', $post['teacher']);
                $old_teacher_id = $old_data[0];
                $old_isteacher = $old_data[1];
                $old_course_code = $old_data[2];

                $this->change_teacher_model->updateRooomUse($year,$class_no,$term,$old_teacher_id,$old_isteacher,$old_course_code,$new_teacher_id,$new_teacher_title);
                $this->change_teacher_model->updateCourseTeacher($year,$class_no,$term,$old_teacher_id,$old_course_code,$new_teacher_id);

                if($old_isteacher=='Y'){;
                    $teacher_type = '1';
                } else {
                    $teacher_type = '2';
                }

                $chkCanteach = $this->change_teacher_model->chkCanteach($new_teacher_id,$old_course_code,$teacher_type);

                if($chkCanteach == '0'){
                    $this->change_teacher_model->insertCanteach($new_teacher_id,$old_course_code,$teacher_type,$this->flags->user['username']);
                }

                $teacher_data = $this->change_teacher_model->getTeacherData($new_teacher_id,$teacher_type);
                $hour_traffic_data = $this->change_teacher_model->getHourTrafficTax($year,$class_no,$term,$old_data[0],$old_isteacher);

                if(!empty($hour_traffic_data)){
                    for($i=0;$i<count($hour_traffic_data);$i++){
                        $teacher_bank_type = $this->change_teacher_model->getBankType($teacher_data[0]['bank_code']);
                        $hire_type = $teacher_data[0]['hire_type'];

                        if($old_isteacher == 'Y'){
                            $t_source = $teacher_data[0]['hire_type'];
                        } else {
                            $t_source = $this->change_teacher_model->getAssistantSource($year,$class_no,$term,$hour_traffic_data[$i]['use_date']);
                        }

                        $this->change_teacher_model->updateHourTrafficTax($teacher_data[0]['idno'],$teacher_data[0]['name'],$teacher_bank_type,$teacher_data[0]['bank_code'],$teacher_data[0]['bank_account'],$teacher_data[0]['account_name'],$teacher_data[0]['address'],$t_source,$hire_type,$old_isteacher,$hour_traffic_data[$i]['seq']);
                    }
                    
                }
               
                $this->setAlert(1, '資料修改成功');
                
                redirect(base_url("create_class/change_teacher/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['page_name'] = 'edit';
        $this->data['form'] = $this->change_teacher_model->getFormDefault($this->change_teacher_model->get($id));

        $this->data['choices']['teacher'] = $this->change_teacher_model->getTeacher($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        // $this->data['choices']['teacher'][''] = '請選擇';

        $this->data['link_save2'] = base_url("create_class/change_teacher/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("create_class/change_teacher/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('create_class/change_teacher/edit', $this->data);
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->change_teacher_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

}