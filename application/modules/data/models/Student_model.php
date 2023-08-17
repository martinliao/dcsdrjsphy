<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_model extends MY_Model
{
    public $table = 'BS_user';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($user=array())
    {
        $data = array_merge(array(
            'name' => '',
            'en_name' => '',
            'gender' => '',
            'idno' => '',
            'birthday' => '',
            'email' => '',
            'office_email' => '',
            'bureau_id' => '',
            'bureau_name' => '',
            'out_gov_name' => '',
            'education' => '',
            'co_empdb_poftel' => '',
            'job_distinguish' => '',
            'cellphone' => '',
            'job_title' => '',
            'job_title_name' => '',
            'office_fax' => '',
            'retirement' => '',
            'departure' => '',
            'supervisor_id' => '',
            'job_level_id' => '',
            'job_level_name' => '',
        ), $user);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'name' => array(
                'field' => 'name',
                'label' => '姓名',
                'rules' => 'trim|required',
            ),
            'idno' => array(
                'field' => 'idno',
                'label' => '身分證字號',
                'rules' => 'trim|required|exact_length[10]|alpha_numeric|is_unique[BS_user.idno]|is_unique[BS_user.username]',
            ),
            'birthday' => array(
                'field' => 'birthday',
                'label' => '生日',
                'rules' => 'trim|required',
            ),
            // 'email' => array(
            //     'field' => 'email',
            //     'label' => 'E-Mail',
            //     'rules' => 'trim|valid_email',
            // ),
            'office_email' => array(
                'field' => 'office_email',
                'label' => '公司E-Mail',
                'rules' => 'trim|required|valid_email',
            ),
            'co_empdb_poftel' => array(
                'field' => 'co_empdb_poftel',
                'label' => '公司電話',
                'rules' => 'trim|required',
            ),
            'bureau_name' => array(
                'field' => 'bureau_name',
                'label' => '局處名稱',
                'rules' => 'trim|required',
            ),
            'job_title_name' => array(
                'field' => 'job_title_name',
                'label' => '職稱',
                'rules' => 'trim|required',
            )
        );

        return $config;
    }

    public function getList($attrs=array(), $returncount = false)
    {
        $params = array(
            'select' => 'BS_user.id, BS_user.idno, BS_user.name, BS_user.job_title, BS_user.departure, BS_user.retirement, job_title.name as job_title_name,bureau.name as bureau_name,BS_user.out_gov_name,BS_user.birthday,BS_user.co_empdb_poftel,office_fax,email,office_email',
            'order_by' => 'BS_user.id, BS_user.idno, BS_user.update_time desc',
        );
        $params['join'] = array(
                    array(
                        'table' => 'account_role',
                        'condition'=>'account_role.username = BS_user.username and account_role.group_id = 5',
                        'join_type'=>'inner',
                    ),
                    array(
                        'table' => 'bureau',
                        'condition'=>'bureau.bureau_id = BS_user.bureau_id',
                        'join_type'=>'left',
                    ),
                    array('table' => 'job_title',
                        'condition' => 'job_title.item_id = BS_user.job_title',
                        'join_type' => 'left'),

                );
        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
        }
        if (isset($attrs['rows'])) {
            $params['rows'] = $attrs['rows'];
        }
        if (isset($attrs['offset'])) {
            $params['offset'] = $attrs['offset'];
        }
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }

        $date_like = array();
        if (isset($attrs['idno'])) {
            $like_idno = array(
                array('field' => 'idno', 'value'=>$attrs['idno'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
        }
        if (isset($attrs['name'])) {
            $like_name = array(
                array('field' => 'BS_user.name', 'value'=>$attrs['name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($attrs['bname'])) {
            $like_bname = array(
                array('field' => 'bureau.name', 'value'=>$attrs['bname'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_bname);
        }

        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        if ($returncount){
            return $this->getData($params, 'array', true);
        }
        $data = $this->getData($params);

        return $data;
    }

    public function getListCount($attrs=array())
    {
        return $this->getList($attrs, true);
        /*
        $data = $this->getList($attrs);
        return count($data);
        */
    }

    public function getChoices($conditions=array())
    {
        $data = array();
        $users = $this->getList();
        foreach ($users as $user) {
            $data[$user['id']] = $user['name'];
        }

        return $data;
    }

    public function getBureau($bureau_id){
        $this->db->select('name');
        $this->db->where('bureau_id',$bureau_id);
        $query = $this->db->get('bureau');
        $result = $query->row_array();

        if(!empty($result)){
            return $result['name'];
        } else {
            return '';
        }
    }

    public function getEducation(){
        $data = array();
        $this->db->select('item_id,name');
        $this->db->from('education');
        $query = $this->db->get();
        $education = $query->result_array();

        foreach ($education as $key) {
            $data[$key['item_id']] = $key['name'];
        }

        return $data;
    }

    public function getJobDistinguish(){
        $data = array();
        $this->db->select('item_id,name');
        $this->db->from('job_distinguish');
        $query = $this->db->get();
        $job_distinguish = $query->result_array();

        foreach ($job_distinguish as $key) {
            $data[$key['item_id']] = $key['name'];
        }

        return $data;
    }

    public function getJobTitle($job_title){
        $this->db->select('name');
        $this->db->where('item_id',$job_title);
        $query = $this->db->get('job_title');
        $result = $query->row_array();

        if(!empty($result)){
            return $result['name'];
        } else {
            return '';
        }
    }

    public function getJobLevel($job_level_id){
        $this->db->select('name');
        $this->db->where('item_id',$job_level_id);
        $query = $this->db->get('job_level');
        $result = $query->row_array();

        if(!empty($result)){
            return $result['name'];
        } else {
            return '';
        }
    }

    public function getUserByAccount($username)
    {
        $conditions = array(
            'username' => $username,
        );

        return $this->get($conditions);
    }

    public function insertLog($log=array()){
        return $this->db->insert('user_modify_log', $log);
    }

    public function _insert($fields=array())
    {
        $fields['idno'] = strtoupper($fields['idno']);

        if (isset($fields['idno']) && $fields['idno'] != '') {
            $fields['password'] = md5($fields['idno']);
        }

        $fields['username'] = $fields['idno'];
        //$fields['user_group_id'] = '5';

        return $this->insert($fields, 'date_added');
    }

    public function _update($pk, $fields=array()) {
        return parent::update($pk, $fields);
    }

    public function checkIdnoExist($idno){
        $this->db->select('count(1) cnt');
        $this->db->where('idno',$idno);
        $query = $this->db->get('BS_user');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function getLog($id){
        $sql = sprintf("select user_modify_log.* from BS_user BS_user join user_modify_log user_modify_log on user_modify_log.idno = BS_user.idno where BS_user.id = '%s'",$id);
        $query = $this->db->query($sql);
        $data = $query->result_array();

        for($i=0;$i<count($data);$i++){
            $sql = sprintf("select name from BS_user where username = '%s'",$data[$i]['updater']);
            $query = $this->db->query($sql);
            $result = $query->result_array();
            if(!empty($result)){
                $data[$i]['name'] = $result[0]['name'];
            } else {
                $data[$i]['name'] = '';
            }     
        }

        return $data;
    }

    public function getName($username){
        $sql = sprintf("select name from BS_user where username = '%s'",$username);
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(!empty($result)){
            return $result[0]['name'];
        }    
    
        return '';
    }

    public function getSupervisorName($supervisor_id){
        $sql = sprintf("select name from supervisor_code where item_id = '%s'",$supervisor_id);
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(!empty($result)){
            return $result[0]['name'];
        }    
    
        return '';
    }

    public function getJobLevelName($job_level_id){
        $sql = sprintf("select name from job_level where item_id = '%s'",$job_level_id);
        $query = $this->db->query($sql);
        $result = $query->result_array();
        if(!empty($result)){
            return $result[0]['name'];
        }    
    
        return '';
    }

    public function checkRecord($idno){
        $this->db->select('count(1) cnt');
        $this->db->where('id',$idno);
        $this->db->where_in('yn_sel',array(1,3,8));
        $query = $this->db->get('online_app');
        $result = $query->result_array();

        if($result[0]['cnt'] > 0){
            return true;
        }

        return false;
    }

    public function getClassRecord($id)
    {
        $this->db->select('idno');
        $this->db->where('id',$id);
        $query = $this->db->get('BS_user');
        $result = $query->result_array();

        if(!empty($result)){
            $this->db->where('online_app.id',$result[0]['idno']);
        } else {
            return '';
        }

        $this->db->distinct();
        $this->db->select('online_app.id,online_app.st_no,r.year,r.class_name,r.term,r.start_date1,r.class_no as class_id,r.room_code,
                           t.description as title,bu.name as pname,t.description as name,bu.out_gov_name as bname,b1.name as bname2,b2.name as unit_name,r.is_assess,r.is_mixed,r.seq_no');
        $this->db->join('require as r','online_app.year=r.year and online_app.class_no=r.class_no and online_app.term=r.term','left')
                ->join('BS_user as bu','online_app.id=bu.idno','left')
                ->join('view_code_table as t','t.item_id = bu.job_title and t.type_id="02"','left')
                ->join('bureau as b1','bu.bureau_id=b1.bureau_id','left')
                ->join('bureau as b2','online_app.beaurau_id=b2.bureau_id','left')
                ->join('room_use as ru','ru.year=online_app.year and ru.term=online_app.term and ru.class_id=online_app.class_no and ru.use_period="01"','left')
                ->join('out_gov as og','bu.idno=og.id','left');
        $yn_sel=[1,3,8];
        $this->db->where_in('online_app.yn_sel',$yn_sel);
        $this->db->where('r.is_cancel !=',1);
        $this->db->where('r.is_cancel is NOT NULL', NULL, FALSE);
        $this->db->order_by('r.year desc,r.start_date1 desc');
        $query=$this->db->get('online_app');
        $result=$query->result_array();
        return $result;
    }

    public function updateOnlineApp($bureau_id,$idno){
        $this->db->set('beaurau_id',$bureau_id);
        $this->db->where('id',$idno);
        $this->db->where('yn_sel !=','1');
        
        if($this->db->update('online_app')){
            return true;
        }

        return false;
    }
    public function insertIntoUserModifyLog($new_idno,$old_idno){
        
        // Alex Chiou 2021-06-29
        // 更新所有 BS_user 關聯資料表的外國人學員身分證字號
        $insert_sql = "INSERT INTO `user_modify_log` (`idno`, `field`, `value`,`modify_time`,`updater`) SELECT '{$new_idno}', `field`, `value`, `modify_time`,`updater` FROM `user_modify_log` AS Table_B WHERE Table_B.idno = '{$old_idno}'";
        //var_dump($canteach_sql);die();
        $this->db->query($insert_sql); 

    }

    public function resign_excel(){
        $this->db->select('*');
        $query = $this->db->get('resign_member_to_confirm2');
        $datas = $query->result_array();
        //var_dump($datas);die();

        // 新增Excel物件
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定屬性
        $objPHPExcel->getProperties()->setCreator("PHP")
                    ->setLastModifiedBy("PHP")
                    ->setTitle("Orders")
                    ->setSubject("Subject")
                    ->setDescription("Description")
                    ->setKeywords("Keywords")
                    ->setCategory("Category");

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 將工作表命名
        $sheet->setTitle('待確認離職人員清單');

        $row = 1;
        //訂單編號、訂單總額、訂單狀態、訂單成立時間
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '身分證字號');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '姓名');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '性別');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '電子郵件');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '電話');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '異動日期');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '局處名稱');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '職稱');

        //列印資料
        foreach ($datas as $data) {
            $row++;
            $line = 'A';

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['idno']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['name']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".($data['gender']=='M'?'男':'女')));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['email']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['telephone']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['update_time']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['bureau_name']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['description']));
            $line++;

        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="待確認離職人員清單_'.date("Y_m_d").'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;

    }
    
    public function incumbency_excel($g_datas){

        $datas = $g_datas['list'];
        $date = substr($g_datas['date'],0,10);

        // 新增Excel物件
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定屬性
        $objPHPExcel->getProperties()->setCreator("PHP")
                    ->setLastModifiedBy("PHP")
                    ->setTitle("Orders")
                    ->setSubject("Subject")
                    ->setDescription("Description")
                    ->setKeywords("Keywords")
                    ->setCategory("Category");

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 將工作表命名
        $sheet->setTitle('在職清單');

        $row = 1;
        //訂單編號、訂單總額、訂單狀態、訂單成立時間
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '身分證字號');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '姓名');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '性別');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '電子郵件');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '電話');

        //列印資料
        foreach ($datas as $key => $data) {
            //if($key > 10){
            //    break;
            //}
            $row++;
            $line = 'A';

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02IDNO']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02NAME']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".($data['B01SEX']=='1'?'男':'女')));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02EMAIL']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02POFTEL']));
            $line++;

        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="在職清單_'.$date.'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;
    }

    public function incumbency_excel2($g_datas){

        $datas = $g_datas['list'];
        $date = substr($g_datas['date'],0,10);

        // 新增Excel物件
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定屬性
        $objPHPExcel->getProperties()->setCreator("PHP")
                    ->setLastModifiedBy("PHP")
                    ->setTitle("Orders")
                    ->setSubject("Subject")
                    ->setDescription("Description")
                    ->setKeywords("Keywords")
                    ->setCategory("Category");

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 將工作表命名
        $sheet->setTitle('在職清單');

        $row = 1;
        //訂單編號、訂單總額、訂單狀態、訂單成立時間
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '身分證字號');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, $row, '姓名');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, $row, '性別');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, $row, '電子郵件');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, $row, '電話');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, $row, '異動日期');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6, $row, '局處名稱');
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7, $row, '職稱');
        //列印資料
        foreach ($datas as $key => $data) {
            //if($key > 10){
            //    break;
            //}
            $row++;
            $line = 'A';

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02IDNO']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02NAME']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".($data['B01SEX']=='1'?'男':'女')));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02EMAIL']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['B02POFTEL']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['INSDATE']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['OA1ORGN']));
            $line++;

            $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$data['CODE_NAME']));
            $line++;
        }

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="在職清單_'.$date.'.xlsx"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;
    }




}

