<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_room extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('create_class/change_room_model');
	    $this->load->model('create_class/set_course_model');
        $this->load->model('create_class/volunteer_model');

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

        $this->data['filter']['total'] = $total = $this->change_room_model->getListCount($attrs);
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

		$this->data['list'] = $this->change_room_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("create_class/change_room/edit/{$row['seq_no']}/?{$_SERVER['QUERY_STRING']}");
        }
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("create_class/change_room?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		$this->data['link_refresh'] = base_url("create_class/change_room/");

		$this->layout->view('create_class/change_room/list', $this->data);
	}

    public function edit($id=NULL)
    {
        if ($post = $this->input->post()) {
            $old_data = $this->change_room_model->get($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {
                $chk_exist = $this->change_room_model->checkExist($post,$this->flags->user['idno']);

                if(!empty($chk_exist)){
                    $msg = '';
                    for($i=0;$i<count($chk_exist);$i++){
                        if($msg == ''){
                            $msg = $chk_exist[$i]['year'].'年度 '.$chk_exist[$i]['class_name'].' 第'.$chk_exist[$i]['term'].'期 '.$chk_exist[$i]['use_date'];
                        } else {
                            $msg .= '<br>'.$chk_exist[$i]['year'].'年度 '.$chk_exist[$i]['class_name'].' 第'.$chk_exist[$i]['term'].'期 '.$chk_exist[$i]['use_date'];
                        }
                    }

                    $this->setAlert(1, $msg.'<br>衝堂');
                } else {
                    $rs = $this->change_room_model->changeRoom($post);
                    if ($rs) {
                        $post_year = intval($post['year']);
                        $post_class_no = addslashes($post['class_no']);
                        $post_term = intval($post['term']);
                        $post_old_roomid = addslashes($post['room_id']);
                        $post_new_roomid = addslashes($post['new_room_id']);
                        $post_course_date = addslashes($post['use_date']);
                        $volunteer_class_id = $this->volunteer_model->getVolunteerClassId($post_year,$post_class_no,$post_term);

                        if($volunteer_class_id > 0){
                            if($post_old_roomid != $post_new_roomid){
                                $classroom_id = $this->volunteer_model->getClassRoomId($post_new_roomid);
                                if($classroom_id < 0){
                                    $room_info = $this->set_course_model->getRoomInfo($post_new_roomid);
                                    $vcid = $this->volunteer_model->insertClassRoom($post_new_roomid,$room_info);
                                } else {
                                    $vcid = $this->volunteer_model->getVcid($post_new_roomid);
                                }
                                
                                $old_vcid = $this->volunteer_model->getVcid($post_old_roomid);

                                if($vcid > 0 && $old_vcid > 0){
                                    $chk_update_volunteer_calendar = $this->volunteer_model->updateVolunteerCalendarRoom($volunteer_class_id,$vcid,$old_vcid,$post_course_date);
                                    if(!$chk_update_volunteer_calendar){
                                        $this->setAlert(2, '同步志工資料失敗');
                                    }
                                } else {
                                    $this->setAlert(2, '同步志工資料失敗');
                                }
                            }
                        }

                        $this->set_course_model->updateRequireRoom($post['year'],$post['class_no'],$post['term']);
                        $this->setAlert(1, '資料修改成功');
                    }
                }

                redirect(base_url("create_class/change_room/edit/{$id}/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['page_name'] = 'edit';
        $this->data['form'] = $this->change_room_model->getFormDefault($this->change_room_model->get($id));

        $this->data['choices']['room_id'] = $this->change_room_model->getRoom($this->data['form']['year'],$this->data['form']['class_no'],$this->data['form']['term']);
        $this->data['choices']['room_id'][''] = '請選擇';

        $this->data['link_get_room_date'] = base_url("create_class/change_room/get_room_date");
        $this->data['link_save'] = base_url("create_class/change_room/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("create_class/change_room/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('create_class/change_room/edit', $this->data);
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->change_room_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

    public function get_room_date(){
        $year = $this->input->post('year');
        $class_no = $this->input->post('class_no');
        $term = $this->input->post('term');
        $room_id = $this->input->post('room_id');
        $data = $this->change_room_model->getRoomUseDate($year,$class_no,$term,$room_id);

        print_r(json_encode($data));
    }

}