<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Regist_undertaker extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/regist_undertaker_model');
        $this->load->model('management/online_app_model');
        $this->load->model('management/lux_course_block_factor_model');
        $this->load->model('management/beaurau_persons_model');
        $this->load->model('management/phydisabled_model');
        $this->load->model('management/stud_modifylog_model');
        $this->load->model('planning/createclass_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }

    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $conditions['year'] = $this->data['filter']['year'];
        $conditions['app_type'] = '1';
        $conditions['req_beaurau'] = $this->flags->user['bureau_id'];
        $attrs = array(
            'conditions' => $conditions,
        );

        $attrs['class_status'] = array('2','3');
        $attrs['where_special'] = "IFNULL(isend, '')='' or isend ='N'";

        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['filter']['total'] = $total = $this->regist_undertaker_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        $attrs['class_status'] = array('2','3');
        $attrs['where_special'] = "(IFNULL(isend, '')='' or isend ='N')";
        if ($this->data['filter']['class_name'] != '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['sort'] != '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        $this->data['list'] = $this->regist_undertaker_model->getList($attrs);
        // jd($this->data['list'],1);
        foreach ($this->data['list'] as & $row) {
            $row['link_regist'] = base_url("management/regist_undertaker/regist/{$row['seq_no']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/regist_undertaker?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/regist_undertaker/");

        $this->layout->view('management/regist_undertaker/list', $this->data);
    }

    public function regist($seq_no=NULL)
    {
        $this->data['class'] = $this->regist_undertaker_model->get($seq_no);
        if(!isset($this->data['class'])){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/regist_undertaker/'));
        }
        $conditions = array(
            'year' => $this->data['class']['year'],
            'class_no' => $this->data['class']['class_no'],
            'term' => $this->data['class']['term'],
            'yn_sel' => '2',
        );
        $this->data['regist_list'] = $this->online_app_model->getList($conditions);
        $this->data['class']['max_order'] = $this->online_app_model->getMaxOrder($conditions);

        if($post = $this->input->post()){
            foreach($post['selID'] as $key => $row_selID){
                if(!empty($row_selID)){
                    $regist_conditions = array(
                        'id' => $row_selID,
                        'year' => $this->data['class']['year'],
                        'class_no' => $this->data['class']['class_no'],
                        'term' => $this->data['class']['term'],
                    );
                    $regist_status = $this->online_app_model->getRegist($regist_conditions);
                    if($regist_status < '1'){
                        $regist_del = $this->online_app_model->getDel($regist_conditions);
                        $conditions = array(
                            'idno' => $row_selID,
                        );
                        $person = $this->user_model->_get($conditions);
                        $conditions = array(
                            'year' => $this->data['class']['year'],
                            'class_no' => $this->data['class']['class_no'],
                            'term' => $this->data['class']['term'],
                            'beaurau_id' => $person['bureau_id'],
                        );
                        $insertOrder = $this->online_app_model->getMaxOrder($conditions);
                        $insert_date = new DateTime();
                        $insert_date = $insert_date->format('Y-m-d H:i:s');
                        if($regist_del != 0){
                            $conditions = array(
                                'id' => $row_selID,
                                'year' => $this->data['class']['year'],
                                'class_no' => $this->data['class']['class_no'],
                                'term' => $this->data['class']['term'],
                            );
                            $fields = array(
                                'yn_sel' => '2',
                                'insert_order' => $insertOrder,
                                'upd_user' => $this->flags->user['username'],
                                'upd_date' => $insert_date,
                            );
                            $this->online_app_model->update($conditions, $fields);
                            $fields = array(
                                'year' => $this->data['class']['year'],
                                'class_no' => $this->data['class']['class_no'],
                                'term' => $this->data['class']['term'],
                                'beaurau_id' => $this->flags->user['bureau_id'],
                                'id' => $row_selID,
                                'modify_item' => '報名',
                                'modify_date' => $insert_date,
                                'o_id' => $row_selID,
                                'n_term' => $this->data['class']['term'],
                                'upd_user' => $this->flags->user['username'],
                                's_beaurau_id' => $person['bureau_id'],
                            );
                            $this->stud_modifylog_model->insert($fields);
                        }else{
                            $conditions = array(
                                'year' => $this->data['class']['year'],
                                'class_no' => $this->data['class']['class_no'],
                                'term' => $this->data['class']['term'],
                            );
                            $priority = $this->createclass_model->getPriority($conditions);

                            $insert_fields = array(
                                'year' => $this->data['class']['year'],
                                'class_no' => $this->data['class']['class_no'],
                                'term' => $this->data['class']['term'],
                                'id' => $row_selID,
                                'beaurau_id' => $person['bureau_id'],
                                'yn_sel' => '2',
                                'insert_order' => $insertOrder,
                                'insert_date' => $insert_date,
                                'cre_user' => $this->flags->user['username'],
                                'cre_date' => $insert_date,
                                'upd_user' => $this->flags->user['username'],
                                'upd_date' => $insert_date,
                                'priority' => $priority,
                            );
                            $this->online_app_model->insert($insert_fields);
                            $fields = array(
                                'year' => $this->data['class']['year'],
                                'class_no' => $this->data['class']['class_no'],
                                'term' => $this->data['class']['term'],
                                'beaurau_id' => $this->flags->user['bureau_id'],
                                'id' => $row_selID,
                                'modify_item' => '報名',
                                'modify_date' => $insert_date,
                                'o_id' => $row_selID,
                                'n_term' => $this->data['class']['term'],
                                'upd_user' => $this->flags->user['username'],
                                's_beaurau_id' => $person['bureau_id'],
                            );
                            $this->stud_modifylog_model->insert($fields);
                        }

                    }
                }
            }
            redirect(base_url("management/regist_undertaker/regist/{$seq_no}"));
        }

        $this->data['import'] = base_url("management/regist_undertaker/regist_import/{$seq_no}");
        $this->data['link_cancel'] = base_url("management/regist_undertaker/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/regist_undertaker/regist', $this->data);
    }

    public function regist_import($seq_no=NULL)
    {
        $this->data['class'] = $this->regist_undertaker_model->get($seq_no);
        // jd($this->data['class']);
        if(!isset($this->data['class'])){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/regist_undertaker/'));
        }
        $this->data['page_name'] = 'Upload';
        $from = 'mag';
        $massage = '';
        if ($post = $this->input->post()) {

            if ($this->_isVerify('add') == TRUE) {

                // upload file

                if (isset($_FILES['upload']) && $_FILES['upload']['tmp_name'] != '') {
                    if (!fileExtensionCheck($_FILES['upload']['name'], ['csv'])){
                        $this->setAlert(3, "不允許的檔案格式");
                        redirect(base_url("create_class/regist_undertaker/regist_import/".$seq_no));
                    }
                    // jd($_FILES);
                    $file = fopen(sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($_FILES['upload']['tmp_name']),"r");
                    $i = 1;
                    $successCount = 0;
                    while(! feof($file))
                    {
                        $data = fgetcsv($file);
                        if($i == 1){
                            $i++;
                            continue;
                        }

                        if($data){
                            foreach($data as & $row){
                                $row = iconv('big5', 'UTF-8//IGNORE', $row);
                                $row = strtoupper(trim($row));
                                // jd($row);
                            }
                            $isValid = TRUE;
                            $conditions = array(
                                'idno' => $data[0],
                                'year' => $this->data['class']['year'],
                                'class_no' => $this->data['class']['class_no'],
                                'term' => $this->data['class']['term'],
                            );

                            $person_check = $this->_check_person($conditions);
                            if($person_check != 'success'){
                                $massage .= '<font color="red">'.$data[0].' '.$person_check.'</font><br>';
                                continue;
                            }

                            $conditions = array(
                                'idno' => $data[0],
                            );
                            $person = $this->user_model->_get($conditions);

                            if(empty($person)){
                                $massage .= '<font color="red">'.$data[0].' 實體查無此人，請新增此學員資料。</font><br>';
                                continue;
                            }

                            if (strcmp($from, 'mag') != 0) {
                                $beaurauId = $this->flags->user['bureau_id'];
                                if ($person['bureau_id'] != $beaurauId) {
                                    $massage .= '<font color="red">'.$data[0].' 非所屬人員</font><br>';
                                    $isValid = false;
                                    continue;
                                }
                            }

                            if ($isValid === TRUE) {
                                $conditions = array(
                                    'id' => $data[0],
                                    'year' => $this->data['class']['year'],
                                    'class_no' => $this->data['class']['class_no'],
                                    'term' => $this->data['class']['term'],
                                );
                                $regist_status = $this->online_app_model->getRegist($conditions);
                                if($regist_status>0){
                                    $massage .= '<font color="red">'.$data[0].' 已報名</font><br>';
                                    continue;
                                }
                            }

                            $checkEroll = $this->_checkErollmentCondition($data[0], $this->data['class']['year'], $this->data['class']['class_no'], $this->data['class']['term']);

                            if($checkEroll != ''){
                                $massage .= '<font color="red">'.$data[0].'<br>';
                                $massage .= $checkEroll;
                                $massage .= '</font><br>';
                                $isValid = FALSE;
                            }

                            if(!empty($person['bureau_id'])){
                                $conditions = array(
                                    'year' => $this->data['class']['year'],
                                    'class_no' => $this->data['class']['class_no'],
                                    'term' => $this->data['class']['term'],
                                    'beaurau' => $person['bureau_id'],
                                );
                                $beaurau_persons = $this->beaurau_persons_model->get($conditions);
                                if($beaurau_persons && $beaurau_persons['persons_2']>0){
                                    $regist_count = $this->online_app_model->get_regist($conditions);
                                    if($beaurau_persons['persons_2'] <= $regist_count){
                                        $massage .= '<font color="red">'.$data[0].' 超過該局處的配當人數</font><br>';
                                        continue;
                                    }
                                }
                            }

                            $insertFlag = $isValid || (strcmp($from, 'mag') == 0);

                            if ($insertFlag === TRUE) {
                                if (strcmp($from, 'mag') != 0) {  //非管理者
                                    $conditions = array(
                                        'year' => $this->data['class']['year'],
                                        'class_no' => $this->data['class']['class_no'],
                                        'term' => $this->data['class']['term'],
                                        'yn_sel' => '2',
                                    );
                                    $insertOrder = $this->online_app_model->getMaxOrder($conditions);
                                }else{
                                    $insertOrder = ($i-1);
                                }

                                $regist_conditions = array(
                                    'id' => $data[0],
                                    'year' => $this->data['class']['year'],
                                    'class_no' => $this->data['class']['class_no'],
                                    'term' => $this->data['class']['term'],
                                );
                                $regist_status = $this->online_app_model->getRegist($regist_conditions);
                                if($regist_status < '1'){
                                    $regist_del = $this->online_app_model->getDel($regist_conditions);
                                    $conditions = array(
                                        'idno' => $data[0],
                                    );

                                    $insert_date = new DateTime();
                                    $insert_date = $insert_date->format('Y-m-d H:i:s');
                                    if($regist_del != 0){
                                        $conditions = array(
                                            'id' => $data[0],
                                            'year' => $this->data['class']['year'],
                                            'class_no' => $this->data['class']['class_no'],
                                            'term' => $this->data['class']['term'],
                                        );
                                        $fields = array(
                                            'yn_sel' => '2',
                                            'insert_order' => $insertOrder,
                                            'upd_user' => $this->flags->user['username'],
                                            'upd_date' => $insert_date,
                                        );
                                        $this->online_app_model->update($conditions, $fields);
                                        $fields = array(
                                            'year' => $this->data['class']['year'],
                                            'class_no' => $this->data['class']['class_no'],
                                            'term' => $this->data['class']['term'],
                                            'beaurau_id' => $this->flags->user['bureau_id'],
                                            'id' => $data[0],
                                            'modify_item' => '報名',
                                            'modify_date' => $insert_date,
                                            'o_id' => $data[0],
                                            'n_term' => $this->data['class']['term'],
                                            'upd_user' => $this->flags->user['username'],
                                            's_beaurau_id' => $person['bureau_id'],
                                        );
                                        $this->stud_modifylog_model->insert($fields);
                                    }else{
                                        $conditions = array(
                                            'year' => $this->data['class']['year'],
                                            'class_no' => $this->data['class']['class_no'],
                                            'term' => $this->data['class']['term'],
                                        );
                                        $priority = $this->createclass_model->getPriority($conditions);

                                        $insert_fields = array(
                                            'year' => $this->data['class']['year'],
                                            'class_no' => $this->data['class']['class_no'],
                                            'term' => $this->data['class']['term'],
                                            'id' => $data[0],
                                            'beaurau_id' => $person['bureau_id'],
                                            'yn_sel' => '2',
                                            'insert_order' => $insertOrder,
                                            'insert_date' => $insert_date,
                                            'cre_user' => $this->flags->user['username'],
                                            'cre_date' => $insert_date,
                                            'upd_user' => $this->flags->user['username'],
                                            'upd_date' => $insert_date,
                                            'priority' => $priority,
                                        );
                                        $this->online_app_model->insert($insert_fields);
                                        $fields = array(
                                            'year' => $this->data['class']['year'],
                                            'class_no' => $this->data['class']['class_no'],
                                            'term' => $this->data['class']['term'],
                                            'beaurau_id' => $this->flags->user['bureau_id'],
                                            'id' => $data[0],
                                            'modify_item' => '報名',
                                            'modify_date' => $insert_date,
                                            'o_id' => $data[0],
                                            'n_term' => $this->data['class']['term'],
                                            'upd_user' => $this->flags->user['username'],
                                            's_beaurau_id' => $person['bureau_id'],
                                        );
                                        $this->stud_modifylog_model->insert($fields);
                                    }
                                    $successCount = $successCount +1;
                                    $massage .= '<font color="green">'.$data[0].'匯入成功!!</font><br>';
                                }else{
                                    $massage .= '<font color="red">'.$data[0].' 已報名</font><br>';
                                }

                            }


                        }


                    }

                    if( isset($beaurauId) && !empty($beaurauId)){
                        $conditions = array(
                            'year' => $this->data['class']['year'],
                            'class_no' => $this->data['class']['class_no'],
                            'term' => $this->data['class']['term'],
                            'beaurauId' => $beaurauId,
                        );
                        $alreadySign = getCurrentBureauPersonNo($conditions);
                        if ($alreadySign > 0) {
                            $massage .= '<font color="red">共'.$alreadySign.'人已報名</font><br>';
                        }
                    }

                    $massage .= '匯入結束 共匯入成功: '.$successCount.' 筆';

                    fclose($file);

                }

            }

        }
        $this->data['form']['file'] =  '';
        $this->data['form']['massage'] =  $massage;
        $this->data['link_cancel'] = base_url("management/regist_undertaker/regist/{$seq_no}");
        $this->layout->view('management/regist_undertaker/upload_csv', $this->data);
    }

    private function _isVerify($action='add', $old_data=array())
    {

        $config = array(
            'file' => array(
                'field' => 'file',
                'label' => '上傳csv檔案',
                'rules' => 'trim|required',
            ),
        );

        $this->form_validation->set_rules($config);

        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;

    }

    public function phydisabled($id=NULL)
    {
        $this->data['phydisabled_ary'] = array("","視障","聽障","肢障");

        $person = $this->user_model->get($id);
        $conditions = array(
            'gid' => $person['idno'],
        );
        $phydisabled_data = $this->phydisabled_model->get($conditions);
        $this->data['memo'] = '';
        if($phydisabled_data){
            $this->data['memo'] = $phydisabled_data['memo'];
        }
        $this->data['memo_status'] = '0';
        if (in_array($this->data['memo'], $this->data['phydisabled_ary'])){
            $this->data['memo_status'] = 1;
        }

        $this->data['set_phydisabled'] = '';
        if($post = $this->input->post()){
            $conditions = array(
                'gid' => $person['idno'],
            );
            $this->phydisabled_model->delete($conditions);
            if (in_array($post['memo'], $this->data['phydisabled_ary'])){
                $fields = array(
                    'gid' => $person['idno'],
                    'memo' => $post['memo'],
                );
            }else{
                $fields = array(
                    'gid' => $person['idno'],
                    'memo' => $post['other_memo'],
                );
            }
            $this->phydisabled_model->insert($fields);
            $this->data['set_phydisabled'] = 'Y';
            $this->data['phydisabled_id'] = $person['idno'];
        }
        $this->data['link_save_phydisabled'] = base_url("management/regist_undertaker/phydisabled/{$id}");
        $this->load->view('management/regist_undertaker/phydisabledchg', $this->data);
    }

    public function _check_person($conditions=array())
    {

        $block_setting = $this->lux_course_block_factor_model->getBlockSetting($conditions);

        if($block_setting['0']!=='0') {
            $tmpVal = $block_setting['0'];
            if($this->online_app_model->ckeckFactor_1($conditions['idno'], $conditions['class_no'], $conditions['year']-$tmpVal)) { //判斷條件1
                // echo "success";
            }
            else {
                return $this->proessEnd(1, $tmpVal);
            }
        }
        if($block_setting['1']!=='0') {
            $tmpVal = $block_setting[1];
            $tmpFlag = $this->online_app_model->ckeckFactor_2($conditions['idno'], $tmpVal, $conditions['year']);
            if($tmpFlag=='0') { //判斷條件2
                // echo "success";
            }
            else {
                return $this->proessEnd(2, $block_setting[1], $tmpFlag);
            }
        }
        if(is_array($block_setting['2'])) {
            if(count($block_setting['2'])>0) { //判斷條件3
                $tmpVal = $block_setting['2'];
                for ($i=0; $i < count($tmpVal); $i++) {
                    $tmpFlag = $this->online_app_model->ckeckFactor_3($conditions['idno'], $tmpVal[$i]["text"], $conditions['year']);
                    if($tmpFlag==1) { //判斷條件2
                        // echo "success";
                    }else {
                        return $this->proessEnd($tmpFlag);
                    }
                }
            }
        }

        return $this->proessEnd(0);

    }

    public function proessEnd($kind, $val1=0, $val2=0, $val3=0, $val4=0) {
        //預計顯示訊息
        //0).條件通過
        //1).xxx於1年內重複參加本研習，無法報名。
        //2).xxx已修滿(groupName)x門課程，無法報名。
        //3).xxx需參訓完設定必修班期結訓後，才得參訓本班期。[條件必修]
        //4).xxx已參訓完設定擋修班期結訓後，不得參訓本班期。[條件擋修]
        $message = "success";
        if($kind===1) {
            $message = sprintf("於%s年內重複參加本研習，無法報名。", $val1);
        }
        elseif($kind===2) {
            $message = sprintf("已修過群組(%s)內之%d門課程限制，無法報名。", $val1, $val2);
        }
        elseif($kind===3) {
            $message = sprintf("需參訓完設定必修班期結訓後，才得參訓本班期。", $val1);
        }
        elseif($kind===4) {
            $message = sprintf("已參訓完設定擋修班期結訓後，不得參訓本班期。", $val1);
        }
        else {

        }

        return $message;
    }

    function _checkErollmentCondition ($id, $year, $class_no, $term) {
        $conditions = array(
            'year' => $year,
            'class_no' => $class_no,
            'term' => $term,
        );
        $isStart = $this->createclass_model->get($conditions);
        $errorMsg = '';
        $conditions['id'] = $id;
        if(empty($isStart['limit_start'])){
            $isStart['limit_start'] = 'Y';
        }
        if(empty($isStart['limit1_start'])){
            $isStart['limit1_start'] = 'Y';
        }
        if($isStart['limit_start'] == 'Y'){
            $errorMsg1 = $this->online_app_model->checkErollmentLimit1($conditions);
            if(!empty($errorMsg1)){
                $errorMsg .= $errorMsg1 .'<br>';
            }
        }
        if($isStart['limit1_start'] == 'Y'){
            $errorMsg2 = $this->online_app_model->checkErollmentLimit2($conditions);
            if(!empty($errorMsg2)){
                $errorMsg .= $errorMsg2 .'<br>';
            }
        }

        $errorMsg3 = $this->online_app_model->checkErollmentLimit3($conditions);
        if(!empty($errorMsg3)){
            $errorMsg .= $errorMsg3 .'<br>';
        }
        return $errorMsg;
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

                case 'check_person':
                    $error = FALSE;

                    if(empty($post['id'])){
                        $error = TRUE;
                    }
                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['class_no'])){
                        $error = TRUE;
                    }
                    if(empty($post['term'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        $post['id'] = strtoupper($post['id']);
                        $conditions = array(
                            'idno' => $post['id'],
                        );
                        $person = $this->user_model->_get($conditions);

                        if($person){

                            $conditions = array(
                                'id' => $post['id'],
                                'year' => $post['year'],
                                'class_no' => $post['class_no'],
                                'term' => $post['term'],
                            );
                            $regist_status = $this->online_app_model->getRegist($conditions);

                            if($regist_status>0){
                                $result['msg'] = '已報名!';
                                break;
                            }

                            $conditions = array(
                                'year' => $post['year'],
                                'class_no' => $post['class_no'],
                                'term' => $post['term'],
                                'repeat_sign' => 'N',
                            );
                            $regist_repeat  = $this->createclass_model->getCount($conditions);
                            if($regist_repeat>0){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'id' => $post['id'],
                                );
                                $is_repeat = $this->online_app_model->repeat_sign($conditions);
                                if($is_repeat>0){
                                    $result['msg'] = '【本學員已報名別期，本班不可重複報名，如有疑問請洽班期承辦人處理】';
                                    break;
                                }
                            }

                            if(!empty($person['bureau_id'])){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'beaurau' => $person['bureau_id'],
                                );
                                $beaurau_persons = $this->beaurau_persons_model->get($conditions);
                                if($beaurau_persons && $beaurau_persons['persons_2']>0){
                                    $regist_count = $this->online_app_model->get_regist($conditions);
                                    if($beaurau_persons['persons_2'] <= $regist_count){
                                        $result['msg'] = '超過該局處的配當人數';
                                        break;
                                    }
                                }
                            }

                            $checkEroll = $this->_checkErollmentCondition($post['id'], $post['year'], $post['class_no'], $post['term']);

                            if($checkEroll != ''){
                                $result['msg'] = $checkEroll;
                                break;
                            }

                            $conditions = array(
                                'idno' => $post['id'],
                                'year' => $post['year'],
                                'class_no' => $post['class_no'],
                                'term' => $post['term'],
                            );

                            $person_check = $this->_check_person($conditions);

                            if($person_check != 'success'){
                                $result['msg'] = $person_check;
                                break;
                            }

                            $person['phy_url'] = base_url("management/regist_undertaker/phydisabled/{$person['id']}");
                            $result['status'] = TRUE;
                            $result['person'] = $person;
                            $result['msg'] = '';
                        }else{
                            $result['msg'] = '無此身分證號!';
                        }

                    }

                    break;

                case 'all_cancel':

                    $error = FALSE;

                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['class_no'])){
                        $error = TRUE;
                    }
                    if(empty($post['term'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        foreach($post['chkID'] as $key => $chkID ){
                            if($chkID){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $chkID,
                                );
                                $upd_date = new DateTime();
                                $upd_date = $upd_date->format('Y-m-d H:i:s');
                                $fields = array(
                                    'yn_sel' => '6',
                                    'upd_user' => $this->flags->user['username'],
                                    'upd_date' => $upd_date,
                                );

                                $this->online_app_model->update($conditions, $fields);
                                $stud_data = $this->online_app_model->get($conditions);
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'beaurau_id' => $stud_data['beaurau_id'],
                                    'insert_order >' => $stud_data['insert_order'],
                                );

                                $this->online_app_model->update_order($conditions);
                                $conditions = array(
                                    'idno' => $chkID,
                                );
                                $person = $this->user_model->_get($conditions);
                                $fields = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'beaurau_id' => $this->flags->user['bureau_id'],
                                    'id' => $chkID,
                                    'modify_item' => '取消',
                                    'modify_date' => $upd_date,
                                    'o_id' => $chkID,
                                    'n_term' => $post['term'],
                                    'upd_user' => $this->flags->user['username'],
                                    's_beaurau_id' => $person['bureau_id'],
                                );
                                $this->stud_modifylog_model->insert($fields);

                            }
                        }
                        $result['status'] = TRUE;
                    }

                    break;

                case 'regist_del':

                    $error = FALSE;

                    if(empty($post['id'])){
                        $error = TRUE;
                    }
                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['class_no'])){
                        $error = TRUE;
                    }
                    if(empty($post['term'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        $conditions = array(
                            'year' => $post['year'],
                            'class_no' => $post['class_no'],
                            'term' => $post['term'],
                            'id' => $post['id'],
                        );
                        $upd_date = new DateTime();
                        $upd_date = $upd_date->format('Y-m-d H:i:s');
                        $fields = array(
                            'yn_sel' => '6',
                            'upd_user' => $this->flags->user['username'],
                            'upd_date' => $upd_date,
                        );
                        $this->online_app_model->update($conditions, $fields);

                        $stud_data = $this->online_app_model->get($conditions);
                        $conditions = array(
                            'year' => $post['year'],
                            'class_no' => $post['class_no'],
                            'term' => $post['term'],
                            'beaurau_id' => $stud_data['beaurau_id'],
                            'insert_order >' => $stud_data['insert_order'],
                        );

                        $this->online_app_model->update_order($conditions);
                        $conditions = array(
                            'idno' => $post['id'],
                        );
                        $person = $this->user_model->_get($conditions);
                        $fields = array(
                                'year' => $post['year'],
                                'class_no' => $post['class_no'],
                                'term' => $post['term'],
                                'beaurau_id' => $this->flags->user['bureau_id'],
                                'id' => $post['id'],
                                'modify_item' => '取消',
                                'modify_date' => $upd_date,
                                'o_id' => $post['id'],
                                'n_term' => $post['term'],
                                'upd_user' => $this->flags->user['username'],
                                's_beaurau_id' => $person['bureau_id'],
                            );
                            $this->stud_modifylog_model->insert($fields);
                        $result['status'] = TRUE;
                    }

                    break;

                case 'regist_edit':

                    $error = FALSE;

                    if(empty($post['year'])){
                        $error = TRUE;
                    }
                    if(empty($post['class_no'])){
                        $error = TRUE;
                    }
                    if(empty($post['term'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{

                        foreach($post['chkID'] as $key => $chkID ){
                            if($chkID){
                                $conditions = array(
                                    'year' => $post['year'],
                                    'class_no' => $post['class_no'],
                                    'term' => $post['term'],
                                    'id' => $chkID,
                                );
                                $upd_date = new DateTime();
                                $upd_date = $upd_date->format('Y-m-d H:i:s');
                                $fields = array(
                                    'insert_order' => $post['chkNO'][$key],
                                    'upd_user' => $this->flags->user['username'],
                                    'upd_date' => $upd_date,
                                );

                                $this->online_app_model->update($conditions, $fields);

                            }
                        }
                        $result['status'] = TRUE;
                    }

                    break;

            }
        }

        echo json_encode($result);
    }

}
