<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher_manger_2 extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('data/teacher_model_2');
        $this->load->model('data/hire_category_model');
        $this->load->model('data/bank_code_model');
        $this->load->model('teacher_log_model');

        $this->data['choices']['education'] = $this->teacher_model_2->getEducation();
        $this->data['choices']['education'][''] = '請選擇';
        $this->data['choices']['teacher_type'] = $this->teacher_model_2->teacher_type;
        $this->data['choices']['hire_type'] = $this->hire_category_model->getChoices();
        $this->data['choices']['identity_type'] = $this->teacher_model_2->identity_type;
        //print_r($this->data['choices']['identity_type']);die();

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['identity_type'])) {
            $this->data['filter']['identity_type'] = '3';
        }
        if (!isset($this->data['filter']['idno'])) {
            $this->data['filter']['idno'] = '';
        }
        if (!isset($this->data['filter']['old_idno'])) {
            $this->data['filter']['old_idno'] = '';
        }
        if (!isset($this->data['filter']['rpno'])) {
            $this->data['filter']['rpno'] = '';
        }
        if (!isset($this->data['filter']['name'])) {
            $this->data['filter']['name'] = '';
        }
        if (!isset($this->data['filter']['course_name'])) {
            $this->data['filter']['course_name'] = '';
        }
        if (!isset($this->data['filter']['queryFile'])) {
            $this->data['filter']['queryFile'] = '';
        }
        //print_r($this->data['filter']['identity_type']);die();

    }

    public function index()
    {

        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions['del_flag !='] = 'Y';

        $attrs = array(
            'conditions' => $conditions,
        );

        // $this->getFilterData($this->input->get());
        //var_dump($data['filter']); die();

        if (empty($this->input->get())){

            $attrs['identity_type'] ='3';
         
            $this->data['filter']['total'] = $total = $this->teacher_model_2->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs['rows'] = $rows;
            $attrs['offset']=$offset;
            //var_dump($attrs);die();
            
            $this->data['list'] = $this->teacher_model_2->getList($attrs);
            //var_dump($this->data['list']);die();

        }else{
            //var_dump($this->data['filter']);die();
            
            if ($this->data['filter']['identity_type'] !== '' ) {
                $attrs['identity_type'] = $this->data['filter']['identity_type'];
            }
            if ($this->data['filter']['idno'] !== '' ) {
                $attrs['idno'] = $this->data['filter']['idno'];
            }
            if ($this->data['filter']['old_idno'] !== '' ) {
                $attrs['old_idno'] = $this->data['filter']['old_idno'];
            }
            if ($this->data['filter']['rpno'] !== '' ) {
                $attrs['rpno'] = $this->data['filter']['rpno'];
            }
            if ($this->data['filter']['name'] !== '' ) {
                $attrs['name'] = $this->data['filter']['name'];
            }

            //var_dump($attrs);die();
            $this->data['filter']['total'] = $total = $this->teacher_model_2->getListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs = array(
                'conditions' => $conditions,
                'rows' => $rows,
                'offset' => $offset,
            );
            if ($this->data['filter']['identity_type'] !== '' ) {
                $attrs['identity_type'] = $this->data['filter']['identity_type'];
            }
            if ($this->data['filter']['idno'] !== '' ) {
                $attrs['idno'] = $this->data['filter']['idno'];
            }
            if ($this->data['filter']['old_idno'] !== '' ) {
                $attrs['old_idno'] = $this->data['filter']['old_idno'];
            }
            if ($this->data['filter']['rpno'] !== '' ) {
                $attrs['rpno'] = $this->data['filter']['rpno'];
            }
            if ($this->data['filter']['name'] !== '' ) {
                $attrs['name'] = $this->data['filter']['name'];
            }
            if ($this->data['filter']['course_name'] !== '' ) {
                $attrs['course_name'] = $this->data['filter']['course_name'];
            }
            if ($this->data['filter']['queryFile'] !== '' ) {
                $attrs['queryFile'] = $this->data['filter']['queryFile'];
            }
            //print_r($attrs);die();
            $this->data['list'] = $this->teacher_model_2->getList($attrs);
            //print_r($this->data['list']);die();
        }
        
        // jd($this->data['list']);

        foreach ($this->data['list'] as & $row) {
            //$row['link_edit'] = base_url("data/teacher_manger_2/edit/{$row['id']}/{$row['idno']}/{$row['old_idno']}/?{$_SERVER['QUERY_STRING']}");
            $row['link_edit'] = base_url("data/teacher_manger_2/edit/?id={$row['id']}&idno={$row['idno']}&old_idno={$row['old_idno']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("data/teacher_manger_2?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        //$this->data['link_add_teacher'] = base_url("data/teacher_manger_2/add/?{$_SERVER['QUERY_STRING']}");
        //$this->data['link_delete'] = base_url("data/teacher_manger_2/delete/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("data/teacher_manger_2/");

        $this->layout->view('data/teacher_manger_2/list_2', $this->data);
        //$old_id = 'FOR0000002';
        //var_dump($this->teacher_model_2->check_hour_traffic_tax($old_id));
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        $this->data['u_id'] = $this->flags->user['id'];
        $this->data['form'] = $this->teacher_model_2->getFormDefault();
        $this->data['form']['bank_code_name'] = '';

        //$this->load->helper(array('form','url'));
        //$this->load->library('form_validation');

        /*
            一般搜尋表單點擊新增 或者 新增 講師/助教 身份
            一般搜尋表單點擊新增 輸入 id(身份證)
            新增 講師/助教 身份 輸入 id(身份證), teacher_type(講師/助教)
        */

        $id = $this->getFilterData('id');
        $teacher_type = $this->getFilterData('teacher_type');
        if (isset($id)){
            if (isset($teacher_type)){
                //新增 講師/助教 身份
                $this->data['form'] = $this->teacher_model_2->getFormDefault($this->teacher_model_2->_get($id,'idno'));
                $conditions = array(
                    'item_id' => $this->data['form']['bank_code'],
                );
                $bank_code_data = $this->bank_code_model->get($conditions);
                $this->data['form']['bank_code_name'] = $bank_code_data['item_id'].'  '.$bank_code_data['name'];
                $this->data['form']['teacher_type'] = $teacher_type;
                // dd($this->data['form']);
            }else{
                $this->data['form']['idno'] = $id;
                // 一般搜尋表單點擊新增
            }
        }

        if($this->input->get('key')){
            $add_id = $this->teacher_model_2->getTeacherIdByIdno(strtoupper(trim($this->input->get('key'))));
            
            if($add_id > 0){
                redirect(base_url("data/teacher_manger_2/edit/".$add_id.'/?'));
            } else {
                $this->data['form']['idno'] = $this->input->get('key');
            }

        }

        if ($post = $this->input->post()) {
            // jd($this->input->post('teacher_type'),1);
            $identity_type = $this->input->post('identity_type');
            if ($this->_isVerify('add',null,$identity_type) == TRUE) {

                /*$this->form_validation->set_rules('bank_account','帳號','numeric');
                if($this->form_validation->run()==FALSE){
                    $this->setAlert(2,'帳號請填入數字，不可有特殊字元');
                    redirect(base_url("data/teacher_manger_2/add/?{$_SERVER['QUERY_STRING']}"));
                }*/

                if (isset($_FILES['upload']) && $_FILES['upload']['tmp_name'] != '') {
                    if (!fileExtensionCheck($_FILES['upload']['name'], ['jpg', 'png'])){
                        $this->setAlert(3, '檔案格式錯誤');
                        redirect(base_url("data/teacher_manger_2?{$_SERVER['QUERY_STRING']}"));                        
                    }                        
                    $image = $this->_upload_image($_FILES['upload']);
                    $post['image'] = $image['pelative'] . $image['new_name'];
                }
                $this->teacher_model_2->canteach_update($post);
                $conditions = [
                    "idno" => $post['idno'],
                    "teacher_type" => $post['teacher_type']
                ];
                $insert_date = new DateTime();
                $insert_date = $insert_date->format('Y-m-d H:i:s');
                $post['date_added'] = $insert_date;
                if(!empty($post['demand'])){
                    $post['special_require_date'] = $insert_date;
                }
                if(empty($post['another_name'])){
                    $post['another_name'] = null;
                }
                $post['idno'] = strtoupper($post['idno']);
                $post['teacher'] = ($post['teacher_type'] == 1) ? 'Y' : 'N';
                $city_data = $this->teacher_model_2->get_city($post['zipcode']);
                $post['county'] = $city_data['city'];
                $post['district'] = $city_data['subcity'];
                $saved_id = $this->teacher_model_2->_update_or_create($conditions, $post);
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }
                redirect(base_url('data/teacher_manger_2'));
            }
        }

        $this->data['link_save2'] = base_url("data/teacher_manger_2/add/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("data/teacher_manger_2/?{$_SERVER['QUERY_STRING']}");
        $this->data['show_bank'] = base_url("data/teacher_manger_2/show_bank");
        $this->layout->view('data/teacher_manger_2/add', $this->data);
    }

    public function edit($id=NULL, $idno=NULL, $old_idno=NULL)
    {
        $this->load->helper("html");
        $id = $_GET["id"];
        $idno = $_GET["idno"];
        $old_idno = $_GET["old_idno"];
        
        //Alex Chiou 2021-6-25
        //$hour_traffic_taxs = $this->teacher_model_2->check_hour_traffic_tax($old_idno);
        //var_dump($hour_traffic_taxs);die();
        /* if (sizeof($hour_traffic_taxs)>0){
            foreach ($hour_traffic_taxs as $hour_traffic_tax){
            if (is_null($hour_traffic_tax->status)){
                $status = '無狀態' ;
            }else{
                $status = $hour_traffic_tax->status;
            }
            //var_dump($status);die();
            $message .=  '此外國人講師正在請款中不能修改身分證字號:'.$old_idno.'\r請款的班期課程為:'.$hour_traffic_tax->year.'/'.$hour_traffic_tax->class_no.'/'.$hour_traffic_tax->term.'/'.$hour_traffic_tax->class_name.'/'.$status.'\r';
            }
            $url_back= base_url('data/teacher_manger_2');
            //var_dump($url_back);
            echo "<script type='text/javascript'>alert('$message');</script>";
            echo "<script type='text/javascript'> document.location.href='$url_back';</script>";
            //redirect(base_url("data/teacher_manger"));
        } */
        //var_dump($idno);
        // Alex Chiou
        $this->data['page_name'] = 'edit';
        $this->data['u_id'] = $this->flags->user['id'];
        $this->data['form'] = $this->teacher_model_2->getFormDefault($this->teacher_model_2->_get($id));
        $conditions = array(
            'item_id' => $this->data['form']['bank_code'],
        );
        $bank_code_data = $this->bank_code_model->get($conditions);
        $this->data['form']['bank_code_name'] = $bank_code_data['item_id'].'  '.$bank_code_data['name'];
        //$this->load->helper(array('form','url'));
        //$this->load->library('form_validation');

        if ($post = $this->input->post()) {

            /*$this->form_validation->set_rules('bank_account','帳號','numeric');
            if($this->form_validation->run()==FALSE){
                $this->setAlert(2,'帳號請填入數字，不可有特殊字元');
                redirect(base_url("data/teacher_manger_2/edit/{$id}?{$_SERVER['QUERY_STRING']}"));
            }*/

            

            $old_data = $this->teacher_model_2->_get($id);
            
            // jd($post,1);
            $identity_type = $this->input->post('identity_type');
            if ($this->_isVerify('edit', $old_data, $identity_type) == TRUE) {

                // upload image
                if (isset($_FILES['upload']) && $_FILES['upload']['name'] != '') {
                    if (!fileExtensionCheck($_FILES['upload']['name'], ['jpg', 'png'])){
                        $this->setAlert(3, '檔案格式錯誤');
                        redirect(base_url("data/teacher_manger_2?{$_SERVER['QUERY_STRING']}"));                        
                    }                    
                    $image = $this->_upload_image($_FILES['upload']);
                    $post['image'] = $image['pelative'] . $image['new_name'];

                    // delete old image
                    if ($old_data['image'] != '') {
                        $this->_remove_image(DIR_MEDIA . $old_data['image']);
                    }
                }

                // delete image
                if ($post['image'] == '') {
                    if ($old_data['image'] != '') {
                        $this->_remove_image(DIR_MEDIA . $old_data['image']);
                    }
                }
                $this->teacher_model_2->canteach_update($post);
                if(!empty($post['demand'])){
                    $insert_date = new DateTime();
                    $insert_date = $insert_date->format('Y-m-d H:i:s');
                    $post['special_require_date'] = $insert_date;
                }

                if(empty($post['another_name'])){
                    $post['another_name'] = null;
                }

                $post['teacher'] = ($post['teacher_type'] == 1) ? 'Y' : 'N';
                $city_data = $this->teacher_model_2->get_city($post['zipcode']);
                $post['county'] = $city_data['city'];
                $post['district'] = $city_data['subcity'];
                $post['del_flag'] = 'N';
                $rs = $this->teacher_model_2->_update($id, $post);
                unset($post['del_flag']);
                
                // 更新外師身分證字號
                //print_r($post);die();
                $this->teacher_model_2->updateAll($post["idno"],$post);//同時更改講座及助教的資料
                //}
                

                if ($rs) {
                    $this->setAlert(2, '資料編輯成功');
                }

                redirect(base_url("data/teacher_manger_2/?{$_SERVER['QUERY_STRING']}"));
            }
        }

        $this->data['link_save2'] = base_url("data/teacher_manger_2/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("data/teacher_manger_2/?{$_SERVER['QUERY_STRING']}");
        $this->data['show_bank'] = base_url("data/teacher_manger_2/show_bank");
        $this->layout->view('data/teacher_manger_2/edit', $this->data);
    }

    public function delete()
    {
        if ($post = $this->input->post()) {
            // if($this->flags->user['id']=='11'){
            //     jd($post['rowid'],1);
            // }
            // $this->teacher_model_2->canteach_delete($post);
            foreach ($post['rowid'] as $id) {
                $fields = array(
                    'del_flag' => 'Y',
                );
                $rs = $this->teacher_model_2->update($id, $fields);
            }
            $this->setAlert(2, '資料刪除成功');
        }

        redirect(base_url("data/teacher_manger_2/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array(), $identity_type)
    {
        $config = $this->teacher_model_2->getVerifyConfig();
        if($identity_type == 4){
            $config['idno']['rules'] = 'trim|is_unique[teacher.rpno]';
        }

        if ($action == 'add') {
            $rpno = $this->input->post('rpno');
            if(!empty($rpno)){
                $config['rpno']['rules'] = 'trim|exact_length[10]|alpha_numeric|is_unique[teacher.rpno]';
                if($identity_type == 4){
                    $config['idno']['rules'] = 'trim|is_unique[teacher.rpno]';
                } else {
                    $config['idno']['rules'] = 'trim|exact_length[10]|alpha_numeric|is_unique[teacher.rpno]';
                }
            }
        }
        if ($action == 'edit') {
            $idno = $this->input->post('idno');
            if ($old_data['idno'] == $idno) {
                if($identity_type == 4){
                    $config['idno']['rules'] = 'trim';
                } else {
                    $config['idno']['rules'] = 'trim|exact_length[10]|alpha_numeric';
                }
            }
            $rpno = $this->input->post('rpno');
            if ($old_data['rpno'] == $rpno) {
                $config['rpno']['rules'] = 'trim|exact_length[10]|alpha_numeric';
            }
        }

        $this->form_validation->set_rules($config);
        //$this->form_validation->set_message('bank_account','帳號請填入數字，不可有特殊字元');
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        // $this->form_validation->set_message('required', '請勿空白');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

    /*public function validate_bank($str)
    {
        if($str=='未提供帳號'||is_numeric($str)){
            return true;
        }

        $this->form_validation->set_message('bank_account','testest');
        return false;
    }*/

    public function teacher_combo_import()
    {
        $chk_aType = array("1","2","3","4");
        $chk_aEdu = array("20","30","40","50","60","70");
        $chk_aIsTeach = array("1","2");
        $chk_aSource = array("I","O","P","Q","R");
        $cnt_csv_head = 28;
        $massage = '';
        if($post = $this->input->post()){
            if (isset($_FILES['aCSV']) && $_FILES['aCSV']['tmp_name'] != '') {
                $file = fopen(sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($_FILES['aCSV']['tmp_name']),"r");
                $i = '0';
                $import_susess = '0';
                $import_falut = '0';
                while(! feof($file))
                {
                    $data = fgetcsv($file);
                    if($i == '0'){
                        $i++;
                        continue;
                    }
                    if(!is_array($data)){
                        continue;
                    }
                    foreach($data as & $row){
                        $row = iconv('big5', 'UTF-8//IGNORE', $row);
                    }
                    $data[0] = strtoupper(trim($data[0]));
                    // jd($data,1);
                    if(count($data) != $cnt_csv_head) { $EACH_IMP_RESULT[$i] = ' 欄位數不符';  continue;}// 欄位跟表頭不符
                    $EACH_STATUS = true;
                    $EACH_IMP_RESULT[$i] = array();
                    if($data[0] == ''){
                        $EACH_IMP_RESULT[$i][] = ' 身份證字號';
                        $EACH_STATUS = false;
                    }

                    if(!in_array($data[1], $chk_aType))
                    {
                        $EACH_IMP_RESULT[$i][] = ' 身分別代碼錯誤';
                        $EACH_STATUS = false;
                    }

                    if($data[2] == ''){
                        $EACH_IMP_RESULT[$i][] = ' 出生年月日';
                        $EACH_STATUS = false;
                    }

                    if($data[3] == ''){
                        $EACH_IMP_RESULT[$i][] = ' 姓名必填';
                        $EACH_STATUS = false;
                    }

                    if($data[5] == ''){
                        $EACH_IMP_RESULT[$i][] = ' 任職機關必填';
                        $EACH_STATUS = false;
                    }

                    if($data[6] == ''){
                        $EACH_IMP_RESULT[$i][] = ' 職稱必填';
                        $EACH_STATUS = false;
                    }

                    if(!in_array($data[7], $chk_aEdu))
                    {
                        $EACH_IMP_RESULT[$i][] = ' 學歷代碼錯誤';
                        $EACH_STATUS = false;
                    }

                    if($data[9] == '')
                    {
                        $EACH_IMP_RESULT[$i][] = ' 郵遞區號必填';
                        $EACH_STATUS = false;
                    }

                    if($data[10] == '')
                    {
                        $EACH_IMP_RESULT[$i][] = ' 通訊地址必填';
                        $EACH_STATUS = false;
                    }

                    if($data[19] == '')
                    {
                        $EACH_IMP_RESULT[$i][] = ' 銀行(郵局)分行必填';
                        $EACH_STATUS = false;
                    }

                    if($data[20] == '')
                    {
                        $EACH_IMP_RESULT[$i][] = ' 銀行帳號必填';
                        $EACH_STATUS = false;
                    }

                    if(!in_array($data[26], $chk_aIsTeach))
                    {
                        $EACH_IMP_RESULT[$i][] = ' 講師或助教代碼錯誤';
                        $EACH_STATUS = false;
                    }
                    if(!in_array($data[27], $chk_aSource))
                    {
                        $EACH_IMP_RESULT[$i][] = ' 聘請類別代碼錯誤';
                        $EACH_STATUS = false;
                    }
                    // jd($EACH_IMP_RESULT[$i],1);
                    $insert_date = new DateTime();
                    $insert_date = $insert_date->format('Y-m-d H:i:s');
                    $fields = array(
                        'idno' => $data[0],
                        'identity_type' => $data[1],
                        'birthday' => $data[2],
                        'name' => $data[3],
                        'another_name' => $data[4],
                        'institution' => $data[5],
                        'job_title' => $data[6],
                        'education' => $data[7],
                        'major' => $data[8],
                        'zipcode' => $data[9],
                        'route' => $data[10],
                        'h_tel' => $data[11],
                        'h_tel2' => $data[12],
                        'c_tel' => $data[13],
                        'c_tel2' => $data[14],
                        'mobile' => $data[15],
                        'fax' => $data[16],
                        'email' => $data[17],
                        'email2' => $data[18],
                        'bank_code' => $data[19],
                        'bank_account' => $data[20],
                        'account_name' => $data[3],
                        'contact_person' => $data[21],
                        'contact_tel' => $data[22],
                        'experience' => $data[23],
                        'demand' => $data[24],
                        'introduction' => $data[25],
                        'teacher_type' => $data[26],
                        'hire_type' => $data[27],
                    );

                    if($EACH_STATUS == true){
                        $fields['date_added'] = $insert_date;
                        $fields['teacher'] = ($fields['teacher_type'] == '1') ? 'Y' : 'N';
                        $city_data = $this->teacher_model_2->get_city($fields['zipcode']);
                        $fields['county'] = $city_data['city'];
                        $fields['district'] = $city_data['subcity'];


                        $log_fields = array(
                            'action' => '新增(整批匯入)',
                            'action_dt' => $insert_date,
                            'action_user' => $this->flags->user['username'],
                            'id' => $data[0],
                            'birth' => $data[2],
                            'name' => $data[3],
                            'alias' => $data[4],
                            'position' => $data[6],
                            'corp' => $data[5],
                            'edu' => $data[7],
                            'school' => $data[8],
                            'career' => $data[23],
                            'source' => $data[27],
                            'addr' => $data[10],
                            'telo' => $data[13],
                            'telh' => $data[11],
                            'telh2' => $data[12],
                            'telo2' => $data[14],
                            'mobil' => $data[15],
                            'fax' => $data[16],
                            'email' => $data[17],
                            'email2' => $data[18],
                            'contactor' => $data[21],
                            'ctel' => $data[22],
                            'account' => $data[20],
                            'zone' => $data[9],
                            'bankid' => $data[19],
                            'cre_user' => $this->flags->user['username'],
                            'cre_date' => $insert_date,
                            'upd_user' => $this->flags->user['username'],
                            'upd_date' => $insert_date,
                            'special_require' => $data[24],
                            'introduce' => $data[25],
                            'id_type' => $data[1],
                            'teacher_type' => $data[26],
                        );
                        $log_fields['teacher'] = ($fields['teacher_type'] == '1') ? 'Y' : 'N';
                        $log_fields['assistant'] = ($fields['teacher_type'] == '1') ? 'N' : 'Y';
                        $log_fields['city'] = $city_data['city'];
                        $log_fields['subcity'] = $city_data['subcity'];

                        $conditions = array(
                            'idno' => $fields['idno'],
                            'teacher_type' => $fields['teacher'],
                            'teacher' => $fields['teacher_type'],
                        );
                        $ifExist = $this->teacher_model_2->get($conditions);
                        $fields['del_flag'] = 'N';

                        if($ifExist){
                            $rs = $this->teacher_model_2->update($conditions, $fields);
                            if($ifExist['del_flag'] == 'Y'){
                                $log_fields['action'] = '新增(整批匯入)';
                                $massage .= "第{$i}筆 新增成功 <br>\n";
                                $this->teacher_log_model->insert($log_fields);
                            }else{
                                $log_fields['action'] = '修改(整批匯入)';
                                $massage .= "第{$i}筆 更新成功 <br>\n";
                                $this->teacher_log_model->insert($log_fields);
                            }

                        }else{
                            $saved_id = $this->teacher_model_2->insert($fields);
                            $massage .= "第{$i}筆 新增成功 <br>\n";
                            $this->teacher_log_model->insert($log_fields);
                        }
                    }else{
                        $massage .= "第{$i}筆 匯入失敗 [".implode(', ', $EACH_IMP_RESULT[$i])." ]<br>\n";
                    }

                }
            }
            $this->data['echo_msg'] = $massage;
        }
        $this->data['import_save'] = base_url("data/teacher_manger_2/teacher_combo_import");
        $this->data['import_cancel'] = base_url("data/teacher_manger_2/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('data/teacher_manger_2/teacher_combo_import', $this->data);
    }

    public function show_bank()
    {
        if (!isset($this->data['filter']['search_page'])) {
            $this->data['filter']['search_page'] = '1';
        }
        if (!isset($this->data['filter']['key'])) {
            $this->data['filter']['key'] = '';
        }

        $page = $this->data['filter']['search_page'];
        $rows = 15;

        $conditions = array();
        $conditions['(cancel_flag is null or cancel_flag != "Y")'] = null;
        $attrs = array();
        $attrs['conditions'] = $conditions;

        if ($this->data['filter']['key'] != '') {
            $attrs['q'] = $this->data['filter']['key'];
        }

        // jd($attrs);
        $total_query_records = $this->bank_code_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $this->data['total_page'] = ceil($total_query_records / $rows);

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->bank_code_model->getList($attrs);
        $this->load->view('data/teacher_manger_2/pop_bank', $this->data);
    }

    private function _upload_image($image)
    {
        $file_info = pathinfo(basename($image['name']));

        $new_name = generatorRandom(12) .'.'. strtolower($file_info['extension']);

        $pelative = 'data/teacher/';
        $path = DIR_MEDIA. $pelative;
        $path_thumb = $path . 'thumb/';
        $source_image = $path . basename($new_name);
        $new_image = $path_thumb . basename($new_name);

        if (!is_dir($path_thumb)) {
            mkdir($path_thumb, 0777, true);
        }

        if (isset($image['tmp_name'])) {
            move_uploaded_file(sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($image['tmp_name']), $source_image);
        }

        $this->load->library('image_lib');
        $this->image_lib->clear();
        $config = array(
            'source_image' => $source_image,
            'new_image' => $new_image,
            'width' => 100,
            'height' => 100,
            'image_library' => 'gd2',
            'create_thumb' => TRUE,
            'maintain_ratio' => FALSE,
            'master_dim' => 'auto',
            'thumb_marker' => '',
        );

        $this->image_lib->initialize($config);

        $result = array(
            'status' => FALSE,
            'path' => $path,
            'pelative' => $pelative,
            'new_name' => $new_name,
            'origin_name' => $file_info['basename'],
            'extension' => $file_info['extension'],
            'filename' =>  $file_info['filename'],
        );
        if ($this->image_lib->resize()) {
            $result['status'] = TRUE;
        }

        return $result;
    }

    private function _remove_image($path)
    {
        $path_thumb = str_replace('teacher/', 'teacher/thumb/', $path);

        if (file_exists(DIR_MEDIA."data/teacher/".basename($path))) {
            unlink(DIR_MEDIA."data/teacher/".basename($path));
        }
        
        if (file_exists(DIR_MEDIA."data/teacher/thumb/".basename($path_thumb))) {
            unlink(DIR_MEDIA."data/teacher/thumb/".basename($path_thumb));
        }
    }

    public function edit_log(){
        // dd($this->data);
        $idno = $this->getFilterData('idno');
        $teacher_type = $this->getFilterData('teacher_type');
        $this->data['teacher'] = $this->teacher_model_2->_get($idno, 'idno');
        $this->data['teacher_logs'] = $this->teacher_log_model->getLogByTeacher($idno, $teacher_type);
        $this->data['_LOCATION']['name'] = "講座/助教異動紀錄";
        $this->data['_LOCATION']['function']['name'] = "講座/助教異動紀錄";
        $this->layout->view('data/teacher_manger_2/edit_log', $this->data);
    }

    public function teacher_is_unique(){
        $idno = $this->input->post('idno');
        $teacher_type = $this->input->post('teacher_type');
        if (empty($idno) || empty($teacher_type)){
            $this->form_validation->set_message('teacher_is_unique', '缺少身份證或者講師或助教');
            return false;
        }else{
            $result = empty($this->teacher_model_2->getTeacherByType($idno, $teacher_type));
            if ($result == false) $this->form_validation->set_message('teacher_is_unique', '該身份證：講師類型講師或助教已存在');
            return $result;
        }
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

                case 'getAutoID':

                    $error = FALSE;

                    if(empty($post['type'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{

                    	$autoID = $this->teacher_model_2->getAutoID($post['type']);
                    	$result['status'] = TRUE;
                    	$result['autoid'] = $autoID;
                    }


                    break;

            }
        }

        echo json_encode($result);
    }
}
