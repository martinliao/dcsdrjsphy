<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Expense_detail extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('venue_rental/appinfo_model');
        $this->load->model('venue_rental/unit_management_model');
        $this->load->model('venue_rental/room_use_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['app_name'])) {
            $this->data['filter']['app_name'] = '';
        }
        if (!isset($this->data['filter']['appi_id'])) {
            $this->data['filter']['appi_id'] = '';
        }
        if (empty($this->data['filter']['start_cre_date'])) {
            // $this->data['filter']['start_cre_date'] = date('Y-m-d', time() - (86400 * 7));
            $this->data['filter']['start_cre_date'] = '';
        }

        if (empty($this->data['filter']['end_cre_date'])) {
            // $this->data['filter']['end_cre_date'] = date('Y-m-d', time() );
            $this->data['filter']['end_cre_date'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        if ($this->data['filter']['start_cre_date'] != '') {
            $conditions['cre_date >='] = $this->data['filter']['start_cre_date'];
        }
        if ($this->data['filter']['end_cre_date'] != '') {
            $conditions['cre_date <='] = $this->data['filter']['end_cre_date'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['app_name'] !== '' ) {
            $attrs['app_name'] = $this->data['filter']['app_name'];
        }
        if ($this->data['filter']['appi_id'] !== '' ) {
            $attrs['appi_id'] = $this->data['filter']['appi_id'];
        }

        $this->data['filter']['total'] = $total = $this->appinfo_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['app_name'] !== '' ) {
            $attrs['app_name'] = $this->data['filter']['app_name'];
        }
        if ($this->data['filter']['appi_id'] !== '' ) {
            $attrs['appi_id'] = $this->data['filter']['appi_id'];
        }

        $this->data['list'] = $this->appinfo_model->getList($attrs);
        // jd($this->data['list'],1);
        foreach ($this->data['list'] as & $row) {
            $row['print_accounting'] = base_url("venue_rental/expense_detail/print_accounting/{$row['appi_id']}");
            $row['print_application'] = base_url("venue_rental/expense_detail/print_application/{$row['appi_id']}");
            $row['print_premises'] = base_url("venue_rental/expense_detail/print_premises/{$row['appi_id']}");
            $row['mail_detail'] = base_url("venue_rental/expense_detail/mail_detail/{$row['appi_id']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("venue_rental/expense_detail?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        // $this->data['link_add'] = base_url("venue_rental/expense_detail/add/?{$_SERVER['QUERY_STRING']}");
        // $this->data['link_delete'] = base_url("venue_rental/expense_detail/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("venue_rental/expense_detail/");
        $this->data['print_room'] = base_url("venue_rental/expense_detail/print_room/");

        $this->layout->view('venue_rental/expense_detail/list', $this->data);
    }

    public function print_room()
    {
    	$conditions = array();
    	if (!empty($this->data['filter']['appi_id'])) {
            $conditions['appi_id'] = addslashes($this->data['filter']['appi_id']);
        }else{
        	$conditions['appi_id'] = '';
        }
        if (!empty($this->data['filter']['app_name'])) {
            $conditions['app_name'] = addslashes($this->data['filter']['app_name']);
        }else{
        	$conditions['app_name'] = '';
        }
        if (!empty($this->data['filter']['start_date'])) {
            $conditions['start_date'] = addslashes($this->data['filter']['start_date']);
        }else{
        	$conditions['start_date'] = '';

        }
        if (!empty($this->data['filter']['end_date'])) {
            $conditions['end_date'] = addslashes($this->data['filter']['end_date']);
        }else{
        	$conditions['end_date'] = '';
        }


    	$this->data['list'] = $this->appinfo_model->get_print_room($conditions);


        

    	$this->load->view('venue_rental/expense_detail/print_room', $this->data);
    }

    public function print_accounting($appi_id=NULL)
    {

		$conditions = array();
		$conditions['appi_id'] = addslashes($appi_id);
    	$this->data['applicant'] = $this->appinfo_model->get_print_accounting($conditions);

        

    	$this->data['room_list'] = $this->appinfo_model->get_room($conditions['appi_id']);


    	$this->load->view('venue_rental/expense_detail/print_accounting', $this->data);
    }

    public function print_application($appi_id=NULL)
    {
        $conditions = array();
        $conditions['appi_id'] = addslashes($appi_id);
    	$this->data['applicant'] = $this->appinfo_model->get_print_accounting($conditions);

        $this->data['room_list'] = $this->appinfo_model->get_room($conditions['appi_id']);
        foreach($this->data['room_list'] as & $row){
            $row['COUNTBY'] = $this->room_use_model->get_room_countby($row['room_id']);
        }

    	$this->load->view('venue_rental/expense_detail/print_application', $this->data);
    }

    public function print_premises($appi_id=NULL)
    {

    	$conditions = array();
        $conditions['appi_id'] = addslashes($appi_id);
        $this->data['applicant'] = $this->appinfo_model->get_print_accounting($conditions);

        $this->data['room_list'] = $this->appinfo_model->get_room($conditions['appi_id']);
        foreach($this->data['room_list'] as & $row){
            $row['COUNTBY'] = $this->room_use_model->get_room_countby($row['room_id']);
        }
       
        // jd($this->data['applicant']);
        // jd($this->data['room_list']);
    	$this->load->view('venue_rental/expense_detail/print_premises', $this->data);
    }

    public function mail_detail($appi_id=NULL)
    {

        $conditions = array();
        $conditions['appi_id'] = addslashes($appi_id);
        $this->data['applicant'] = $this->appinfo_model->get_print_accounting($conditions);

        $this->data['room_list'] = $this->appinfo_model->get_room($conditions['appi_id']);
        foreach($this->data['room_list'] as & $row){
            $row['COUNTBY'] = $this->room_use_model->get_room_countby($row['room_id']);
        }

        $mail_view = $this->load->view('venue_rental/expense_detail/mail_detail', $this->data, TRUE);
        $recipient = $this->data['applicant']['EMAIL'];
        // $recipient = 'peter19841115@hotmail.com';

        $result = $this->_sent_mail($recipient, '場地使用費用明細表', $mail_view);
        if(is_string($result)){
            '<script>
                alert("$result")
                window.close();
                </script>';
        }else{
            echo '<script>
                alert("發送完成")
                window.close();
                </script>';
        }
    }

    public function _sent_mail($recipient, $subject, $message)
    {
        $recipients = explode(',', $recipient);
        $this->email->from('pstc_apdd@mail.taipei.gov.tw', '臺北市政府公務人員訓練處');
        $this->email->to($recipients);
        $this->email->subject($subject);
        $this->email->message($message);
        $rs = $this->email->send();
        if ($rs) {
            return TRUE;
        }else{
            return 'Email 發送失敗 ('.$recipients.')<br />';
        }

    }

}
