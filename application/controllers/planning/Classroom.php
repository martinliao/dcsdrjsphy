<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Classroom extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('planning/booking_place_model');
        $this->load->model('data/place_category_model');
        $this->load->model('data/reservation_time_model');
        $this->load->model('planning/createclass_model');

        $this->data['choices']['room_type'] = $this->place_category_model->getChoices();
        $this->data['choices']['time_list'] = $this->reservation_time_model->getChoices();
        // jd($this->data['choices']['time_list'],1);
        if (empty($this->data['filter']['start_date'])) {
            $this->data['filter']['start_date'] = date('Y-m-d', time() - (86400 * 7));
        }

        if (empty($this->data['filter']['end_date'])) {
            $this->data['filter']['end_date'] = date('Y-m-d', time() );
        }
        if (empty($this->data['filter']['room_type'])) {
            $this->data['filter']['room_type'] = '';
        }
        if (empty($this->data['filter']['room'])) {
            $this->data['filter']['room'] = '';
        }

    }

    public function index()
    {
        $this->data['page_name'] = 'index';
        $conditions = array();
		if ($this->data['filter']['start_date'] != '') {
            $conditions['start_date'] = $this->data['filter']['start_date'];
        }
        if ($this->data['filter']['end_date'] != '') {
            $conditions['end_date'] = $this->data['filter']['end_date'];
        }
        if ($this->data['filter']['room_type'] != '') {
            $conditions['cat_id'] = $this->data['filter']['room_type'];
            $this->data['choices']['room'] = $this->booking_place_model->get_room($this->data['filter']['room_type'], TRUE);
        }else{
            $this->data['filter']['room'] = '';
            $this->data['choices']['room'] = array();
            if(isset($this->data['filter']['sort']) && $this->data['filter']['room_type']==''){
                $this->setAlert(3, '請選擇場地類別');
                redirect(base_url("planning/classroom"));
            }
        }
        if ($this->data['filter']['room'] != '') {
            $conditions['room_id'] = $this->data['filter']['room'];
        }

        // jd($this->data['filter']);
        if($this->data['filter']['room_type'] != ''){
            if(isDate($conditions['start_date']) && isDate($conditions['end_date'])){
                $days = ((strtotime($conditions['end_date'])-strtotime($conditions['start_date'])) / 86400) + 1;
                if($days>30){
                    $this->setAlert(3, '搜尋日期請勿超過30天');
                    redirect(base_url("planning/classroom"));
                }
                $this->data['list'] = $this->booking_place_model->select_booking($conditions);
                // jd($this->data['list']);
            }else{
                $this->setAlert(3, '日期錯誤');
                redirect(base_url("planning/classroom"));
            }
        }else{
            $this->data['list'] = array();
        }

        $this->data['link_add'] = base_url("planning/classroom/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_refresh'] = base_url("planning/classroom/");


        $this->layout->view('planning/classroom/list', $this->data);
    }

    public function add($id=NULL)
	{
		$this->data['page_name'] = 'add';
		$this->data['form'] = $this->booking_place_model->getFormDefault();
        if(!empty($id)){
            $conditions = array(
                'seq_no' => $id,
            );
            $old_data = $this->createclass_model->get($conditions);
            $this->data['form'] = array(
                'year' => $old_data['year'],
                'class_no' => $old_data['class_no'],
                'term' => $old_data['term'],
                'class_name' => $old_data['class_name'],
                'start_date' => $old_data['start_date1'],
                'end_date' => $old_data['end_date1'],
                'seq_no' => $old_data['seq_no'],
            );
            $this->data['booking'] = $data = $this->booking_place_model->getBooking($id);
        }

		if ($post = $this->input->post()) {
			if ($this->_isVerify('add') == TRUE) {

                $field = array();
				$field['cre_date'] = date('Y-m-d H:i:s');
				$field['cre_user'] = $this->flags->user['id'];
				$field['upd_date'] = date('Y-m-d H:i:s');
				$field['upd_user'] = $this->flags->user['id'];
                $field['year'] = $post['year'];
                $field['class_no'] = $post['class_no'];
                $field['term'] = $post['term'];
                $field['booking_period'] = $post['room_time'];
                $field['cat_id'] = $post['room_type'];
                $field['room_id'] = $post['addRoom'];
                $field['seq_no'] = $post['seq_no'];

                $conditions = array(
                    'room_id' => $field['room_id'],
                    'booking_period' => $field['booking_period'],
                    'booking_date >=' => $post['start_date'],
                    'booking_date <=' => $post['end_date'],
                );
                $used_room = $this->booking_place_model->get($conditions);

                if(strtotime($post['start_date'])>strtotime($post['end_date'])){
                    $this->setAlert(3,'起日不能大於迄日');
                    redirect(base_url("planning/classroom/add/{$post['seq_no']}?".$_SERVER['QUERY_STRING']));
                    exit;
                }
                //room_useu也檢查
                if($used_room){
                    $this->setAlert(3, '該時段中已有使用,請重新選取');
                    redirect(base_url("planning/classroom/add/{$post['seq_no']}?".$_SERVER['QUERY_STRING']));
                }

                $conditions = array(
                    'seq_no' => $post['seq_no'],
                );
                $get_class = $this->createclass_model->get($conditions);

                if($get_class){
                    $days = ((strtotime($post['end_date'])-strtotime($post['start_date'])) / 86400) + 1;

                    $limit_days = ceil((intval($get_class['range'])/6))+1;
                   
                    if(!in_array('9', $this->flags->user['group_id']) && $days > $limit_days){
                        $this->setAlert(2, '超過可預約天數<br>可預約教室天數＝研習時數÷6(無條件進位)＋1天');
                        redirect(base_url("planning/classroom/add/{$post['seq_no']}?".$_SERVER['QUERY_STRING']));
                        exit;
                    }

                    for($i=0; $i<$days; $i++){
                        $booking_date = date("Y-m-d",strtotime("+{$i} day",strtotime($post['start_date'])));
                        $field['booking_date'] = $booking_date;
                        //jd($field,1);
                        $this->booking_place_model->_insert($field);
                    }

                    $date_interval = $this->booking_place_model->get_date_interval($get_class['seq_no']);
                    // jd($date_interval,1);
                    if($date_interval){
                        $update_data['start_date1'] = $date_interval['start_date'];
                        $update_data['end_date1'] = $date_interval['end_date'];
                    }else{
                        $update_data['start_date1'] = $post['start_date'];
                        $update_data['end_date1'] = $post['end_date'];
                    }
                    $update_data['room_code'] = $field['room_id'];
                    $update_data['room_remark'] = null;
                    $update_data['reason'] = ceil(date('n', strtotime($post['start_date']))/3);
                    $this->createclass_model->update($get_class['seq_no'], $update_data);
                    $this->setAlert(1, '預約成功');
                    redirect(base_url("planning/classroom/add/{$post['seq_no']}?".$_SERVER['QUERY_STRING']));

                }

			}
		}

		$this->data['link_save'] = base_url("planning/classroom/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("planning/classroom/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('planning/classroom/add', $this->data);
	}

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->booking_place_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

	public function ajax($action)
    {
        //$action = $this->input->get('action');
        $post = $this->input->post();

        $result = array(
            'status' => FALSE,
            'data' => array(),
        );
        $rs = NULL;
        if ($action && $post) {
            $fields = array();
            switch ($action) {

                case 'del_booking':
                    $error = FALSE;
                    $fields = '';

                    if(empty($post['seq_no'])){
                        $error = TRUE;
                    }
                    if(empty($post['room_id'])){
                        $error = TRUE;
                    }
                    if(empty($post['cat_id'])){
                        $error = TRUE;
                    }
                    if(empty($post['booking_period'])){
                        $error = TRUE;
                    }
                    if(empty($post['start_date'])){
                        $error = TRUE;
                    }
                    if(empty($post['end_date'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){

                    }else{
                        $params['select'] = 'id, seq_no, room_id';
                        $params['conditions'] = array(
                            "seq_no" => addslashes($post['seq_no']),
                            "room_id" => addslashes($post['room_id']),
                            "cat_id" => addslashes($post['cat_id']),
                            "booking_period" => addslashes($post['booking_period']),
                            "booking_date >=" => addslashes($post['start_date']),
                            "booking_date <=" => addslashes($post['end_date']),
                        );
                        $old_data = $this->booking_place_model->getData($params);
                        if(!empty($old_data)){
                            foreach($old_data as $row){
                                $this->booking_place_model->delete($row['id']);
                            }
                        }
                        $result['status'] = TRUE;
                        $this->setAlert(3, '預約已刪除');
                    }

                    break;

                case 'del_list_booking':
                    $error = FALSE;
                    $fields = '';

                    if(empty($post['booking_id'])){
                        $error = TRUE;
                    }
                    if(empty($post['booking_date'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){

                    }else{
                        $conditions = array(
                            "id" => addslashes($post['booking_id']),
                            "booking_date" => addslashes($post['booking_date']),
                        );
                        $old_data = $this->booking_place_model->get($conditions);
                        if(!empty($old_data)){
                            $this->booking_place_model->delete($conditions['id']);
                        }
                        $result['status'] = TRUE;
                        $this->setAlert(3, '預約已刪除');
                    }

                    break;

                case 'get_booking':
                    $error = FALSE;
                    $fields = '';

                    if(empty($post['seq_no'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['data'] = '';
                    }else{
                        $seq_no = $post['seq_no'];
                        $data = $this->booking_place_model->getBooking($seq_no);
                        foreach($data as & $row){
                            $row['booking_period_name'] = $this->data['choices']['time_list'][$row['booking_period']];
                            $row['cat_name'] = $this->data['choices']['room_type'][$row['cat_id']];
                        }
                        $result['status'] = TRUE;
                        $result['data'] = $data;
                    }

                    break;

                case 'get_place':
                	$error = FALSE;
                	$fields = '';

                    if(empty($post['start_date'])){
                    	$error = TRUE;
                    }
                    if(empty($post['end_date'])){
                    	$error = TRUE;
                    }
                    if(empty($post['room_type'])){
                    	$error = TRUE;
                    }
                    if(empty($post['room_time'])){
                    	$error = TRUE;
                    }

                    if($error === TRUE){
                    	$result['data'] = '';
                    }else{
                        $conditions = array(
                            "room_time" => addslashes($post['room_time']),
                            "room_type" => addslashes($post['room_type']),
                            "start_date" => addslashes($post['start_date']),
                            "end_date" => addslashes($post['end_date']),
                        );
                        $data = $this->booking_place_model->getPlace($conditions);
                    	$result['status'] = TRUE;
                    	$result['data'] = $data;
                    }

                    break;

                case 'get_room':
                    $error = FALSE;
                    $fields = '';

                    if(empty($post['room_type'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['data'] = '';
                    }else{
                        $room_type = addslashes($post['room_type']);
                        $data = $this->booking_place_model->get_room($room_type);
                        $result['status'] = TRUE;
                        $result['data'] = $data;
                    }


                    break;



            }
        }

        echo json_encode($result);
    }

}
