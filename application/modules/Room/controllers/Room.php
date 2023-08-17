<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Room extends AdminController
{
	protected $choices = array();

	public function __construct()
	{
		parent::__construct();
		//$this->logged_in();
		//$this->smarty_acl->authorized('roles'); // Even do authorize check. 最新的檢查
		// 預約場地/教室
		$this->load->model('planning/booking_place_model');
		$this->load->model('planning/createclass_model');
		$this->load->model('data/reservation_time_model');

		$this->choices['time_list'] = $this->reservation_time_model->getChoices();
		$this->load->model('room_model', 'model');

		if (empty($this->data['filter']['start_date'])) {
			$this->data['filter']['start_date'] = date('Y-m-d', time() - (86400 * 7));
		}

		if (empty($this->data['filter']['end_date'])) {
			$this->data['filter']['end_date'] = date('Y-m-d', time());
		}
	}

	function getAvailableRoom()
	{
		$_post = $this->input->post();
		$startDate = $_post['start_date'];
		$endDate = $_post['end_date'];
		$conditions = array(
			"room_time" => addslashes($_post['room_time']),
			"room_type" => addslashes($_post['room_type']),
			"start_date" => addslashes($_post['start_date']),
			"end_date" => addslashes($_post['end_date']),
		);
		// 取得起迄日
		$begin = new DateTime($startDate);
		$end = new DateTime($endDate);
		$end = $end->modify('+1 day');
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);

		$availableRecords= $this->booking_place_model->getPlace($conditions);
		$i = 0;
		$data = array();
		$dateRange = 10;
		if (sizeof($availableRecords) > 0) {
			foreach ($availableRecords as $rec) {
				$d = (object)$rec;
				$i++;
				$_dataTag = $this->getDataTag([
					'data-room_id' => $d->room_id,
					'data-room_sname' => $d->room_sname,
					'data-room_cap' => $d->room_cap
				]);
				$btn_book = '<button type="button" class="btn btn-warning btn-xs edit" ' .  $_dataTag . '><i class="fas fa-fw fa-pen"></i> 訂!!</button>';
				//$data[] = array($i, $d->room_id, $d->room_name, $d->room_sname, $d->room_cap, $btn_book);
				$_dateChecks = [];
				foreach ($period as $dt) {
					$_date = $dt->format("Y-m-d");
					$_dateChecks[] = "<label><input type='checkbox' data-room_id='{$d->room_id}' data-bookingdate='{$_date}'></label>";
				}
				$data[] = array_merge(array($i, $d->room_id, $d->room_sname, $d->room_cap), $_dateChecks, array($btn_book));
			}
		}
		$output = array(
			"data" => $data,
		);
		echo json_encode($output);
	}

	/**
	 * 訂教室
	 * checked: true is booking; false is remove booking.
	 * Parameter: bookingdate, seq_no, room_id, room_time
	 * Return: json
	 */
	public function bookingRoom()
	{
		$result = null;
		$success = true;
		$message = '';
		$_post = $this->input->post();
		$doBooking = $_post['checked'];
		//$startDate = $_post['start_date'];
		//$endDate = $_post['end_date'];
		$startDate = $endDate = $_post['bookingdate'];
		$_seqNo = $_post['seq_no'];
		// 因為是checkbox, 所以不會有這個情況.
		// if(strtotime($startDate)>strtotime($endDate)){
		// 	echo json_encode(array('success' => false, 'message' => '起日不能大於迄日')); exit;
		// }
		$conditions = array(
			'room_id' => $_post['room_id'],
			'booking_period' => $_post['root_time'],
			'booking_date >=' => $startDate,
			'booking_date <=' => $endDate,
		);
		$used_room = $this->booking_place_model->get($conditions);
		//room_useu也檢查
		if($used_room){
			echo json_encode(array('success' => false, 'message' => '該時段中已有使用,請重新選取')); exit;
		}
		$_class = $this->createclass_model->get(array('seq_no' => $_seqNo));
		if($_class){
			$_days = ((strtotime($startDate)-strtotime($endDate)) / 86400) + 1;
			$_limit_days = ceil((intval($_class['range'])/6))+1;
			//if(!in_array('9', $this->flags->user['group_id']) && $_days > $_limit_days){
			if($_days > $_limit_days){
				echo json_encode(array('success' => false, 'message' => '超過可預約天數<br/>可預約教室天數＝研習時數÷6(無條件進位)＋1天')); exit;
			}
			$field = array();
			$field['cre_date'] = date('Y-m-d H:i:s');
			$field['cre_user'] = 'jack'; //$this->flags->user['id'];
			$field['upd_date'] = date('Y-m-d H:i:s');
			$field['upd_user'] = 'jack'; //$this->flags->user['id'];
			$field['year'] = $_class['year'];
			$field['class_no'] = $_class['class_no'];
			$field['term'] = $_class['term'];
			$field['booking_period'] = $_post['room_time'];
			$field['cat_id'] = '01'; //$_post['room_type']; 
			$field['room_id'] = $_post['room_id'];
			$field['seq_no'] = $_seqNo;
			$booking_date = date("Y-m-d", strtotime( "+0 day", strtotime($startDate)));
			$field['booking_date'] = $booking_date;
			$tmp = $this->booking_place_model->_insert($field);
			$message = "{$tmp}";

			/* // 更新 createclass_model
			$date_interval = $this->booking_place_model->get_date_interval($_seqNo);
			if($date_interval){
				$update_data['start_date1'] = $date_interval['start_date'];
				$update_data['end_date1'] = $date_interval['end_date'];
			}else{
				$update_data['start_date1'] = $startDate;
				$update_data['end_date1'] = $endDate;
			}
			$update_data['room_code'] = $field['room_id'];
			$update_data['room_remark'] = 'Change by ajax.';
			$update_data['reason'] = ceil(date('n', strtotime($startDate))/3);
			$this->createclass_model->update($_seqNo, $update_data);/** */
		}
		echo json_encode(array('success' => $success, 'message' => $message));
	}

}
