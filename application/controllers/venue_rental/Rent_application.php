<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rent_application extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('venue_rental/unit_management_model');
        $this->load->model('venue_rental/appinfo_model');
        $this->load->model('venue_rental/tv_wall_model');
        $this->load->model('venue_rental/room_use_model');
        $this->load->model('data/place_category_model');
        $this->load->model('data/reservation_time_model');
        $this->load->model('data/venue_time_model');
        $this->load->model('planning/booking_place_model');

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

        if (empty($this->data['filter']['start_date'])) {
            // $this->data['filter']['start_date'] = date('Y-m-d', time() - (86400 * 7));
            $this->data['filter']['start_date'] = '';
        }

        if (empty($this->data['filter']['end_date'])) {
            // $this->data['filter']['end_date'] = date('Y-m-d', time() );
            $this->data['filter']['end_date'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions["IFNULL(del_flag,'') !="] = 'Y';

        if ($this->data['filter']['start_cre_date'] != '') {
            $conditions['cre_date >='] = $this->data['filter']['start_cre_date'];
        }
        if ($this->data['filter']['end_cre_date'] != '') {
            $conditions['cre_date <='] = $this->data['filter']['end_cre_date'];
        }

        if ($this->data['filter']['start_date'] != '') {
            $conditions['start_date >='] = $this->data['filter']['start_date'];
        }
        if ($this->data['filter']['end_date'] != '') {
            $conditions['end_date <='] = $this->data['filter']['end_date'];
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
            $room_name = $this->room_use_model->get_room_name($row['appi_id']);
            $row['room_name'] = $room_name;
            $row['link_edit'] = base_url("venue_rental/rent_application/edit/{$row['appi_id']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("venue_rental/rent_application?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("venue_rental/rent_application/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_delete'] = base_url("venue_rental/rent_application/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("venue_rental/rent_application/");

        $this->layout->view('venue_rental/rent_application/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['form'] = $this->appinfo_model->getFormDefault();

        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $post['cre_date'] = date('Y-m-d H:i:s');
                $post['cre_user'] = $this->flags->user['username'];
                $post['upd_date'] = date('Y-m-d H:i:s');
                $post['upd_user'] = $this->flags->user['username'];
                $fields = array(
                    'cre_date' => $post['cre_date'],
                    'cre_user' => $post['cre_user'],
                    'upd_date' => $post['upd_date'],
                    'upd_user' => $post['upd_user'],
                    'app_id' => $post['app_id'],
                    'app_reason' => $post['app_reason'],
                    'memo' => $post['memo'],
                    'other_expense' => $post['other_expense'],
                    'total_expense' => $post['total_expense'],

                );

                $saved_id = $this->appinfo_model->_insert($fields);
                if ($saved_id) {
                    $appi_id = date('Ymd').$saved_id;
                    $fields = array(
                        'appi_id' => $appi_id,
                    );
                    $this->appinfo_model->_update($saved_id, $fields);
                    $conditions = array(
                            'appi_id' => $appi_id,
                        );
                    $tv_wall_data = $this->tv_wall_model->get($conditions);

                    $fields = array(
                        'appi_id' => $appi_id,
                        'tv_wall' => 'N',
                    );
                    if(isset($post['tv_wall']) && $post['tv_wall'] == 'Y'){
                        $fields['tv_wall'] = 'Y';
                    }
                    if(empty($tv_wall_data)){
                        $this->tv_wall_model->insert($fields);
                    }else{
                        $this->tv_wall_model->update($conditions, $fields);
                    }
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url("venue_rental/rent_application/edit/{$appi_id}/?"));
            }
        }

        $this->data['link_save'] = base_url("venue_rental/rent_application/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("venue_rental/rent_application/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('venue_rental/rent_application/add', $this->data);
    }

    public function edit($appi_id=NULL)
    {
        $this->data['choices']['room_type'] = $this->place_category_model->getChoices();
        $this->data['choices']['time_list'] = $this->reservation_time_model->getChoices();
        $this->data['room_countby'] = array(
            '1' => '人',
            '2' => '桌',
            '3' => '場地',
        );
        $this->data['page_name'] = 'edit';
        $conditions = array(
            'appi_id' => $appi_id,
        );
        $this->data['form'] = $this->appinfo_model->getFormDefault($this->appinfo_model->get($conditions));
        // jd($this->data['form'],1);
        $applicant_data = $this->unit_management_model->get($this->data['form']['app_id']);
        $this->data['room_use_list'] = $this->room_use_model->get_room_use_list($appi_id);

        
        $tv_wall_data = $this->tv_wall_model->get($conditions);

        

        $this->data['form']['tv_wall'] = $tv_wall_data['tv_wall'];
        $this->data['form']['app_name'] = $applicant_data['app_name'];
        $this->data['form']['is_public'] = $applicant_data['is_public'];
        $this->data['form']['contact_name'] = $applicant_data['contact_name'];
        $this->data['form']['tel'] = $applicant_data['tel'];
        $this->data['form']['fax'] = $applicant_data['fax'];
        $this->data['form']['zone'] = $applicant_data['zone'];
        $this->data['form']['addr'] = $applicant_data['addr'];
        $this->data['form']['email'] = $applicant_data['email'];
        $this->data['form']['start_date'] = '';
        $this->data['form']['end_date'] = '';
        $this->data['form']['addCountby'] = '';
        $this->data['form']['addCount'] = '';
        $this->data['form']['addDiscount'] = '';
        $this->data['form']['addNote'] = '';

        if(empty($this->data['form']['people'])){
            $this->data['form']['people'] = '0';
        }

        if(empty($this->data['form']['days'])){
            $this->data['form']['days'] = '0';
        }

        if ($post = $this->input->post()) {
            if ($this->_isVerify('edit') == TRUE) {

                $all_expense = $this->room_use_model->get_expense($appi_id);
                // jd($all_expense,1);
                $post['upd_date'] = date('Y-m-d H:i:s');
                $post['upd_user'] = $this->flags->user['username'];
                $fields = array(

                    'upd_date' => $post['upd_date'],
                    'upd_user' => $post['upd_user'],
                    'app_reason' => $post['app_reason'],
                    'memo' => $post['memo'],
                    'other_expense' => $post['other_expense'],
                    'total_expense' => $post['other_expense'] + $all_expense,
                    'billno' => $post['billno'],
                    'people' => $post['people'],
                    'days' => $post['days'],

                );

                $rs = $this->appinfo_model->update($conditions, $fields);
                if($rs) {
                    $fields = array(
                        'appi_id' => $appi_id,
                        'tv_wall' => 'N',
                    );
                    if(isset($post['tv_wall']) && $tv_wall_data['tv_wall'] != 'Y'){
                        $fields['tv_wall'] = 'Y';
                        $this->tv_wall_model->update($conditions, $fields);
                    }

                    $this->setAlert(1, '儲存成功');
                }

                redirect(base_url("venue_rental/rent_application/edit/{$appi_id}/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['link_save'] = base_url("venue_rental/rent_application/edit/{$appi_id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("venue_rental/rent_application/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('venue_rental/rent_application/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            foreach ($post['rowid'] as $appi_id) {
                $conditions = array(
                    'appi_id' => $appi_id,
                );
                $data = $this->appinfo_model->get($conditions);
                if($data){
                    // jd($data,1);
                    $del_conditions =array(
                        'appi_id' => $data['appi_id'],
                    );
                }
                $this->room_use_model->delete($del_conditions);
                $this->appinfo_model->delete($del_conditions);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("venue_rental/rent_application/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->appinfo_model->getVerifyConfig();

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

                case 'room_use_add':
                    $error = FALSE;
                    $fields = array();

                    if(empty($post['set_start_date'])){
                        $error = TRUE;
                    }
                    if(empty($post['set_end_date'])){
                        $error = TRUE;
                    }
                    if(empty($post['set_room_type'])){
                        $error = TRUE;
                    }
                    if(empty($post['room_id'])){
                        $error = TRUE;
                    }
                    if(empty($post['set_room_time'])){
                        $error = TRUE;
                    }
                    if(empty($post['addCount'])){
                        $error = TRUE;
                    }
                    if(empty($post['appi_id'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['data'] = array();
                    }else{

                        $set_start_date = addslashes($post['set_start_date']);
                        $set_end_date = addslashes($post['set_end_date']);
                        $set_room_type = addslashes($post['set_room_type']);
                        $room_id = addslashes($post['room_id']);
                        $set_room_time = addslashes($post['set_room_time']);
                        $addCount = addslashes($post['addCount']);
                        $appi_id = addslashes($post['appi_id']);
                        $addDiscount = addslashes($post['addDiscount']);
                        $addNote = addslashes($post['addNote']);

                        $fields = array(
                            'start_date' => $set_start_date,
                            'end_date' => $set_end_date,
                            'cat_id' => $set_room_type,
                            'room_id' => $room_id,
                            'use_period' => $set_room_time,
                            'appi_id' => $appi_id,
                        );
                        $conditions = array(
                            'appi_id' => $appi_id,
                        );
                        $appinfo_data = $this->appinfo_model->get($conditions);

                        if($appinfo_data){

                            $used_data = $this->room_use_model->get_used($fields);

                            if($used_data == ''){
                                $addDiscount = '0';
                                if(isset($post['addDiscount'])){
                                    $addDiscount = addslashes($post['addDiscount']);
                                }
                                $addNote = '';
                                if(isset($post['addNote'])){
                                    $addNote = addslashes($post['addNote']);
                                }
                                $conditions = array(
                                    'app_id' => $appinfo_data['app_id'],
                                );
                                $applicant_data = $this->unit_management_model->get($conditions);
                                $conditions = array(
                                    'room_id' => $room_id,
                                    'price_t' => $set_room_time,
                                );
                                $room_time_data = $this->venue_time_model->get($conditions);

                                $not_discount = 'N';
                                if($set_room_type == '02' || $set_room_type == '04'){
                                    $not_discount = 'Y';
                                }

                                $price1 = $room_time_data['price_a'];
                                $price2 = $room_time_data['price_b'];
                                $price3 = $room_time_data['price_c'];

                                $groupnum = $this->room_use_model->get_groupnum($appinfo_data['appi_id']);
                                $room_countby = $this->room_use_model->get_room_countby($room_id);

                                $days = ((strtotime($set_end_date)-strtotime($set_start_date)) / 86400) + 1;

                                for($i=0; $i<$days; $i++){
                                    $use_date = date("Y-m-d",strtotime("+{$i} day",strtotime($set_start_date)));
                                    $use_day = date("N",strtotime($use_date));
                                    $discount1 = '1';
                                    $discount2 = '1';
                                    $discount3 = '1';
                                    if($not_discount == 'N'){
                                        if($applicant_data['is_public'] == 'Y'){
                                            $discount2 = '0.8';
                                        }
                                        if($use_day == '6' || $use_day == '7'){
                                            $discount3 = '1.2';
                                            // $discount3 = '0.2';
                                        }
                                    }
                                    if($addDiscount > 0){
                                        $discount1 = $addDiscount;
                                    }
                                    // $expense = ((($price1+$price2)* $discount1* $discount2) + (($price1+$price2)* $discount3) + $price3) * $addCount;
                                    $expense = ((($price1+$price2)* $discount1* $discount2 * $discount3) + $price3) * $addCount;
                                    $room_use_fields = array(
                                        'appi_id' => $appinfo_data['appi_id'],
                                        'cat_id' => $set_room_type,
                                        'room_id' => $room_id,
                                        'use_date' => $use_date,
                                        'use_period' => $set_room_time,
                                        'unit' => $room_countby,
                                        'num' => $addCount,
                                        'expense' => $expense,
                                        'groupnum' => $groupnum,
                                        'groupnote' => $addNote,
                                    );
                                    if($addDiscount > 0){
                                        $room_use_fields['discount'] = $addDiscount;
                                    }
                                    $this->room_use_model->insert($room_use_fields);
                                    //$this->room_use_model->insert_test_price3($price3);
                                }
                                $all_expense = $this->room_use_model->get_expense($appinfo_data['appi_id']);
                                $conditions = array(
                                    'appi_id' => $appinfo_data['appi_id'],
                                );
                                $fields = array(
                                    'total_expense' => $appinfo_data['other_expense'] + $all_expense,
                                );
                                $this->appinfo_model->_update($conditions, $fields);

                                // jd($appinfo_data);
                                // jd($room_time_data);
                                // jd($applicant_data,1);

                                $result['status'] = TRUE;
                                // $result['data'] = $data;
                                $this->setAlert(1, '新增成功');
                            }else{
                                $this->setAlert(3, $used_data, 30);
                            }

                        }else{
                            $this->setAlert(3, '操作錯誤');
                        }

                    }

                    break;

                case 'room_use_add2':
                    $error = FALSE;
                    $fields = array();

                    if(empty($post['set_start_date'])){
                        $error = TRUE;
                    }
                    if(empty($post['set_end_date'])){
                        $error = TRUE;
                    }
                    if(empty($post['set_room_type'])){
                        $error = TRUE;
                    }
                    if(empty($post['room_use'])){
                        $error = TRUE;
                    }
                    if(empty($post['appi_id'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['data'] = array();
                    }else{

                        $set_start_date = addslashes($post['set_start_date']);
                        $set_end_date = addslashes($post['set_end_date']);
                        $set_room_type = addslashes($post['set_room_type']);
                        $set_room_time = '10';
                        $appi_id = addslashes($post['appi_id']);

                        $room_use_arry = explode(",,",$post['room_use']);

                        $conditions = array(
                            'appi_id' => $appi_id,
                        );
                        $appinfo_data = $this->appinfo_model->get($conditions);

                        if($appinfo_data){

                            $addDiscount = '0';
                            if(isset($post['addDiscount'])){
                                $addDiscount = addslashes($post['addDiscount']);
                            }
                            $addNote = '';
                            if(isset($post['addNote'])){
                                $addNote = addslashes($post['addNote']);
                            }
                            $conditions = array(
                                'app_id' => $appinfo_data['app_id'],
                            );
                            $applicant_data = $this->unit_management_model->get($conditions);

                            foreach($room_use_arry as $room_use_row){
                                $arryValue = explode("::",$room_use_row);

                                if(isset($arryValue['0']) && isset($arryValue['1'])){
                                    $room_id = addslashes($arryValue['0']);
                                    $addCount = addslashes($arryValue['1']);

                                    $conditions = array(
                                        'room_id' => $room_id,
                                        'price_t' => $set_room_time,
                                    );
                                    $room_time_data = $this->venue_time_model->get($conditions);


                                    $not_discount = 'N';
                                    if($set_room_type == '02' || $set_room_type == '04'){
                                        $not_discount = 'Y';
                                    }

                                    $price1 = $room_time_data['price_a'];
                                    $price2 = $room_time_data['price_b'];
                                    $price3 = $room_time_data['price_c'];

                                    $groupnum = $this->room_use_model->get_groupnum($appinfo_data['appi_id']);
                                    $room_countby = $this->room_use_model->get_room_countby($room_id);

                                    $days = ((strtotime($set_end_date)-strtotime($set_start_date)) / 86400) + 1;

                                    for($i=0; $i<$days; $i++){
                                        $use_date = date("Y-m-d",strtotime("+{$i} day",strtotime($set_start_date)));
                                        $use_day = date("N",strtotime($use_date));
                                        $discount1 = '1';
                                        $discount2 = '1';
                                        $discount3 = '0';
                                        if($not_discount == 'N'){
                                            if($applicant_data['is_public'] == 'Y'){
                                                $discount2 = '0.8';
                                            }
                                            if($use_day == '6' || $use_day == '7'){
                                                $discount3 = '0.2';
                                            }
                                        }
                                        if($addDiscount > 0){
                                            $discount1 = $addDiscount;
                                        }
                                        $expense = ((($price1+$price2)* $discount1* $discount2) + (($price1+$price2)* $discount3) + $price3) * $addCount;
                                        $room_use_fields = array(
                                            'appi_id' => $appinfo_data['appi_id'],
                                            'cat_id' => $set_room_type,
                                            'room_id' => $room_id,
                                            'use_date' => $use_date,
                                            'use_period' => $set_room_time,
                                            'unit' => $room_countby,
                                            'num' => $addCount,
                                            'expense' => $expense,
                                            'groupnum' => $groupnum,
                                            'groupnote' => $addNote,
                                        );
                                        if($addDiscount > 0){
                                            $room_use_fields['discount'] = $addDiscount;
                                        }
                                        $this->room_use_model->insert($room_use_fields);
                                    }
                                }

                            }

                            $all_expense = $this->room_use_model->get_expense($appinfo_data['appi_id']);
                            $conditions = array(
                                'appi_id' => $appinfo_data['appi_id'],
                            );
                            $fields = array(
                                'total_expense' => $appinfo_data['other_expense'] + $all_expense,
                            );
                            $this->appinfo_model->_update($conditions, $fields);

                            // jd($appinfo_data);
                            // jd($room_time_data);
                            // jd($applicant_data,1);

                            $result['status'] = TRUE;
                            // $result['data'] = $data;
                            $this->setAlert(1, '新增成功');


                        }else{
                            $this->setAlert(3, '操作錯誤');
                        }

                    }

                    break;

                case 'get_room_time':
                    $error = FALSE;

                    if(empty($post['room_id'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['data'] = array();
                    }else{
                        $room_id = addslashes($post['room_id']);
                        $data = $this->booking_place_model->get_room_time($room_id);
                        $room_countby = $this->booking_place_model->get_room_countby($room_id);
                        $result['status'] = TRUE;
                        $result['data'] = $data;
                        $result['room_countby'] = $room_countby['room_countby'];
                    }

                    break;

                case 'get_room':
                    $error = FALSE;

                    if(empty($post['room_type'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['data'] = array();
                    }else{
                        $room_type = addslashes($post['room_type']);
                        $data = $this->booking_place_model->get_room($room_type);
                        $result['status'] = TRUE;
                        $result['data'] = $data;
                    }

                    break;

                case 'del_room_use':
                    $error = FALSE;

                    if(empty($post['appi_id'])){
                        $error = TRUE;
                    }
                    if(empty($post['groupnum'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['data'] = array();
                    }else{
                        $conditions = array(
                            'appi_id' => addslashes($post['appi_id']),
                            'groupnum' => addslashes($post['groupnum']),
                        );
                        $this->room_use_model->delete($conditions);

                        $all_expense = $this->room_use_model->get_expense($conditions['appi_id']);
                        $conditions = array(
                            'appi_id' => $conditions['appi_id'],
                        );
                        $appinfo_data = $this->appinfo_model->get($conditions);
                        $fields = array(
                            'total_expense' => $appinfo_data['other_expense'] + $all_expense,
                        );
                        $this->appinfo_model->_update($conditions, $fields);

                        $result['status'] = TRUE;
                        $this->setAlert(3, '刪除成功');
                    }

                    break;

            }
        }

        echo json_encode($result);
    }


}
