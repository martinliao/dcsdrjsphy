<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Booking extends AdminController
{
	protected $choices = array();

	protected $userBureau;

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
		$this->load->model('booking_model', 'model');

		if (empty($this->data['filter']['start_date'])) {
			$this->data['filter']['start_date'] = date('Y-m-d', time() - (86400 * 7));
		}

		if (empty($this->data['filter']['end_date'])) {
			$this->data['filter']['end_date'] = date('Y-m-d', time());
		}
		//$this->userBureau = $this->flags->user['bureau_id'];
		// ToDo: Fix user bureau.
		$this->userBureau = '379680000A';
	}

	public function query($id = null)
	{
		$seqNo = empty($id) ? $this->input->post('seq_no', true) : $id;
		$data['csrf'] = array(
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		);
		$data['current_seq_no'] = $seqNo;
		$data['all_class'] = $this->_getAllClassArray($seqNo);
		$this->load->view('session', $data);
	}

	public function session($seqNo = null)
	{
		$seqNo = empty($seqNo) ? $this->input->post('seq_no', true) : $seqNo;
		$old_data = null;
		$bookRecords = [];
		$data = [
			'title' => 'Booking Room',
			'form' => $this->booking_place_model->getFormDefault(),
			'filter' => $this->data['filter']
		];
		if (!empty($seqNo)) {
			$conditions = array(
				'seq_no' => $seqNo,
			);
			$old_data = $this->createclass_model->get($conditions); // select * from require where seq_no = $seqNo
			$data['form'] = array(
				'year' => $old_data['year'],
				'class_no' => $old_data['class_no'],
				'term' => $old_data['term'],
				'class_name' => $old_data['class_name'],
				'start_date' => $old_data['start_date1'],
				'end_date' => $old_data['end_date1'],
				'seq_no' => $old_data['seq_no'],
				'range' => $old_data['range'],
				'no_persons' => $old_data['no_persons']
			);
			//$data['filter']['start_date'] = $old_data['start_date1'];
			//$data['filter']['end_date'] = $old_data['end_date1'];
		} else {
			$data['form']['no_persons'] = 0; // 本期人數
			$data['form']['range'] = 0; // 訓練期程(小時)
		}
		$data['choices'] = $this->choices;

		$data['seq_no'] = $seqNo;
		$data['csrf'] = array(
			'name' => $this->security->get_csrf_token_name(),
			'hash' => $this->security->get_csrf_hash()
		);
		$this->load->view('modal/_session_detail', $data);  // 主畫面: query_bookingroom
		$data['filter']['class_room_type_A'] = 'A';
		// Date
		$filterDate1 = new DateTime($old_data['start_date1']);
		$data['filter']['start_date'] = $filterDate1->format('Y-m-d');
		$filterDate2 = new DateTime($old_data['end_date1']);
		$data['filter']['end_date'] = $filterDate2->format('Y-m-d');
		$this->load->view('modal/available_room', $data);
	}

	public function availableTable($seqNo = null)
	{
		$seqNo = empty($seqNo) ? $this->input->post('seq_no', true) : $seqNo;
		$_post = $this->input->post();
		$startDate = $_post['start_date'];
		$endDate = $_post['end_date'];
		$_class = $this->createclass_model->get(array('seq_no' => $seqNo));
		$data['form'] = array(
			'year' => $_class['year'],
			'class_no' => $_class['class_no'],
			'term' => $_class['term'],
			'class_name' => $_class['class_name'],
			'start_date' => $_class['start_date1'],
			'end_date' => $_class['end_date1'],
			'seq_no' => $_class['seq_no'],
			'range' => $_class['range'],
			'no_persons' => $_class['no_persons']
		);
		/*$begin = new DateTime($_class['start_date1']);
		$end = new DateTime($_class['end_date1']);/** */
		// 取得起迄日
		$begin = new DateTime($startDate);
		$end = new DateTime($endDate);
		$end = $end->modify('+1 day');
		$interval = DateInterval::createFromDateString('1 day');
		$period = new DatePeriod($begin, $interval, $end);
		$data['period'] = array();
		foreach ($period as $dt) {
			$data['period'][] = $dt->format("m/d");
		}
		$this->load->view('modal/_available_table', $data); // Just table?
	}

	public function getLists($id = NULL)
	{
		$seqNo = empty($id) ? $this->input->post('seq_no', true) : $id;
		$data = array();
		$conditions = null;
		$this->data['form'] = $this->booking_place_model->getFormDefault();
		$old_data = null;
		$bookRecords = [];
		if (!empty($seqNo)) {
			$conditions = array(
				'seq_no' => $seqNo,
			);
		}
		$_seqNo = $this->input->post('seq_no', true);
		if (!empty($_seqNo)) {
			$conditions = array('seq_no' => $_seqNo);
		}
		if (!empty($conditions)) {
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
			$bookRecords = $this->booking_place_model->getBooking($seqNo);
		}
		$i = 0;
		$term = $old_data['term'];
		if (sizeof($bookRecords) > 0) {
			$i = $_POST['start'];
			foreach ($bookRecords as $rec) {
				$d = (object)$rec;
				$i++;
				$_dataTag = $this->getDataTag([
					'data-room_id' => $d->room_id,
					'data-booking_period' => $d->booking_period,
					'data-cat_id' => $d->cat_id,
					'data-class_no' => $d->class_no,
					'data-seq_no' => $d->seq_no,
					'data-start_date' => $d->start_date,
					'data-end_date' => $d->end_date,
				]);
				//$btn_edit = '<button type="button" class="btn btn-warning btn-xs edit" data-cat_id="' . $d->cat_id .'" data-class_no="' . $d->class_no . '" data-seq_no="' . $d->seq_no . '"><i class="fas fa-fw fa-pen"></i> 修改</button>';
				$btn_edit = '<button type="button" class="btn btn-warning btn-xs edit" ' . $_dataTag . '"><i class="fas fa-fw fa-pen"></i> 修改</button>';
				//$btn_hapus = '<button type="button" class="btn btn-danger btn-xs hapus"  data-cat_id="' . $d->cat_id . '"> 刪除</button>';
				$btn_hapus = '<button type="button" class="btn btn-danger btn-xs delete" ' . $_dataTag . '"> 刪除</button>';

				$_period = $this->choices['time_list'][$d->booking_period];
				$roomType = $this->choices['room_type'][$d->cat_id];

				$data[] = array($i, $term, $d->seq_no, $d->start_date, $d->end_date, $d->room_name, $_period, $btn_edit . ' ' . $btn_hapus);
			}
		}
		$output = array(
			//"draw" => $_POST['draw'],
			//"recordsTotal" => $this->model->countAll(),
			//"recordsFiltered" => $this->model->countFiltered($_POST),
			"data" => $data,
		);
		echo json_encode($output);
	}

	/**
	 * 從這個期, 找出所有班期
	 * 	ToDo: 要移到 models 內, 因為現在正在改版, 所以先在這裡, May2023.
	 */
	private function _getAllClasses($seqNo)
	{
		$classData = $this->createclass_model->get(array('seq_no' => $seqNo));
		$attrs = array(
			'conditions' => array(
				'year' => $classData['year'],
				'class_no' => $classData['class_no'],
			)
		);
		return $this->createclass_model->getList($attrs, $this->userBureau);
	}

	/**
	 * 取得所有期之後, 加工處理給前端用
	 * 	ToDo: 要移到 models 內, 因為現在正在改版, 所以先在這裡, May2023.
	 */
	private function _getAllClassArray($seqNo)
	{
		$outputArray = [];
		$allClassData = $this->_getAllClasses($seqNo);
		foreach ($allClassData as $c) {
			$outputArray[] = array(
				'seq_no' => $c['seq_no'],
				'year' => $c['year'],
				'term' => $c['term'],
				'class_status' => $c['class_status'],
				'range' => $c['range'],
				'is_cancel' => $c['is_cancel'],
			);
		}
		return $outputArray;
	}

	public function getAllClasses($seqNo)
	{
		$seqNo = empty($seqNo) ? $this->input->post('seq_no', true) : $seqNo;
		$allClassData = $this->_getAllClasses($seqNo);
		//$result = $this->createclass_model->getList($attrs,$this->data['user_bureau']);
		echo json_encode($allClassData);
	}

	public function getSessionData($seqNo)
	{
		$seqNo = empty($seqNo) ? $this->input->post('seq_no', true) : $seqNo;
		$result = $this->createclass_model->get(array('seq_no' => $seqNo)); // select * from require where seq_no = $id
		$data = array(
			'year' => $result['year'],
			'class_no' => $result['class_no'],
			'term' => $result['term'],
			'class_name' => $result['class_name'],
			'start_date' => $result['start_date1'],
			'end_date' => $result['end_date1'],
			'seq_no' => $result['seq_no'],
			'range' => $result['range'],
			'no_persons' => $result['no_persons']
		);
		/** */
		echo json_encode($data);
	}
}
