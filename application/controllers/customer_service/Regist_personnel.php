<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Regist_personnel extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('customer_service/regist_personnel_model');
        $this->load->model('customer_service/lux_account_log_model');
        $this->load->model('management/online_app_model');
        $this->load->model('management/lux_course_block_factor_model');
        $this->load->model('management/beaurau_persons_model');
        $this->load->model('management/phydisabled_model');
        $this->load->model('management/stud_modifylog_model');
        $this->load->model('planning/createclass_model');
        $this->load->model('data/bureau_manage_model');
        $this->load->model('data/job_title_model');
        $this->load->model('system/account_role_model');

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
        //apply_s_date apply_e_date apply_s_date2 apply_e_date2  沒看到是因為 日期條件 不能報名也是
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
        $conditions['year'] = $this->data['filter']['year'];
        // $conditions['app_type'] = '1';
        // $conditions['req_beaurau'] = $this->flags->user['bureau_id'];
        $attrs = array(
            'conditions' => $conditions,
        );

        $attrs['class_status'] = array('2','3');
        $attrs['where_special'] = "( IFNULL(isend, '')='' or isend ='N') and ((app_type = '0' or app_type is null) or (app_type = '2' and '{$this->flags->user['bureau_id']}' = limit_beaurau ) )";

        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['filter']['total'] = $total = $this->regist_personnel_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        $attrs['class_status'] = array('2','3');
        $attrs['where_special'] = "(IFNULL(isend, '')='' or isend ='N') and ((app_type = '0' or app_type is null) or (app_type = '2' and '{$this->flags->user['bureau_id']}' = limit_beaurau ) )";
        if ($this->data['filter']['class_name'] != '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['sort'] != '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        $this->data['list'] = $this->regist_personnel_model->getList($attrs);
        // jd($this->data['list'],1);
        foreach ($this->data['list'] as & $row) {
            $row['link_regist'] = base_url("customer_service/regist_personnel/regist/{$row['seq_no']}");
            //$row['link_class'] = base_url("create_class/print_schedule/print/{$row['seq_no']}");
            $row['link_class'] = base_url("create_class/print_schedule/mutiPrint2?seq_nos%5B%5D={$row['seq_no']}");
            $row['link_report_detial'] = base_url("customer_service/regist_personnel/signup_change_report_detial/{$row['seq_no']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("customer_service/regist_personnel?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("customer_service/regist_personnel/");
        
        $temp=[];
        for($i=0;$i<count($this->data['list']);$i++){
            if($this->data['list'][$i]['app_type']=='2' && $this->data['list'][$i]['limit_beaurau']==$this->flags->user['bureau_id']){
                $temp[$i]=$this->data['list'][$i];
            }else if($this->data['list'][$i]['app_type']!='2'){
                $temp[$i]=$this->data['list'][$i];
            }
        }
        $this->data['list']=$temp;
            //var_dump($temp);
            //var_dump($this->data['list']);
        //}

        $this->layout->view('customer_service/regist_personnel/list', $this->data);
    }

    public function bureau()
    {
        if (!isset($this->data['filter']['bureau_page'])) {
            $this->data['filter']['bureau_page'] = '1';
        }
        if (!isset($this->data['filter']['bureau_q'])) {
            $this->data['filter']['bureau_q'] = '';
        }
        if (!isset($this->data['filter']['key1'])) {
            $this->data['filter']['key1'] = 'N';
        }

        $page = $this->data['filter']['bureau_page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array();
        $attrs['conditions'] = $conditions;
        if ($this->data['filter']['key1'] !== 'Y' ) {
            $attrs['where_special'] = "(del_flag<>'C' or del_flag is  null)";
        }
        if ($this->data['filter']['bureau_q'] !== '' ) {
            $attrs['name'] = $this->data['filter']['bureau_q'];
        }
        // jd($attrs);
        $total_query_records = $this->bureau_manage_model->getBureauListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $this->data['total_page'] = ceil($total_query_records / $rows);

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['bureau_list'] = $this->bureau_manage_model->getBureauList($attrs);
        // jd($this->data['bureau_list'],1);
        $this->load->view('customer_service/regist_personnel/co_beaurau', $this->data);
    }

    public function co_title()
    {
        if (!isset($this->data['filter']['bureau_page'])) {
            $this->data['filter']['bureau_page'] = '1';
        }
        if (!isset($this->data['filter']['bureau_q'])) {
            $this->data['filter']['bureau_q'] = '';
        }
        if (!isset($this->data['filter']['key1'])) {
            $this->data['filter']['key1'] = 'N';
        }

        $page = $this->data['filter']['bureau_page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array();
        $attrs['conditions'] = $conditions;

        if ($this->data['filter']['bureau_q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['bureau_q'];
        }
        // jd($attrs);
        $total_query_records = $this->job_title_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $this->data['total_page'] = ceil($total_query_records / $rows);

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['title_list'] = $this->job_title_model->getList($attrs);
        // jd($this->data['bureau_list'],1);
        $this->load->view('customer_service/regist_personnel/co_title', $this->data);
    }

    public function regist($seq_no=NULL)
    {
        $this->data['class'] = $this->regist_personnel_model->get($seq_no);
        $this->data['class']['canapp'] = 'N';
        $regist_date_now = new DateTime('now');
        $regist_now = $regist_date_now->format('Y-m-d');
        if(strtotime($this->data['class']['apply_s_date']) <= strtotime($regist_now) && strtotime($this->data['class']['apply_e_date']) >= strtotime($regist_now)){
            $this->data['class']['canapp'] = 'Y';
        }
        if(strtotime($this->data['class']['apply_s_date2']) <= strtotime($regist_now) && strtotime($this->data['class']['apply_e_date2']) >= strtotime($regist_now)){
            $this->data['class']['canapp'] = 'Y';
        }
        // jd($this->data['class']);
        if(!isset($this->data['class'])){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('customer_service/regist_personnel/'));
        }
        $conditions = array(
            'year' => $this->data['class']['year'],
            'class_no' => $this->data['class']['class_no'],
            'term' => $this->data['class']['term'],
            'beaurau' => $this->flags->user['bureau_id'],
        );
        $this->data['beaurau_persons'] = $this->beaurau_persons_model->get($conditions);
        if(empty($this->data['beaurau_persons']['persons'])){
            $this->data['beaurau_persons']['persons'] = 0;
        }
        if(empty($this->data['beaurau_persons']['persons'])){
            $this->data['beaurau_persons']['persons_2'] = 0;
        }
        $conditions = array(
            'year' => $this->data['class']['year'],
            'class_no' => $this->data['class']['class_no'],
            'term' => $this->data['class']['term'],
            'beaurauId' => $this->flags->user['bureau_id'],
        );
        $regist_count = $this->online_app_model->getCurrentBureauPersonNo($conditions);
        $this->data['beaurau_persons']['currentNo'] = $regist_count;
        $conditions = array(
            'year' => $this->data['class']['year'],
            'class_no' => $this->data['class']['class_no'],
            'term' => $this->data['class']['term'],
            'yn_sel' => '2',
            'cre_user' => $this->flags->user['username'],
        );
        $this->data['regist_list'] = $this->online_app_model->getList($conditions);
        $this->data['class']['max_order'] = $this->online_app_model->getThisMaxOrder($conditions);
        // jd($this->data['regist_list']);
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
                        $insertOrder = $this->online_app_model->getThisMaxOrder($conditions);
                        $conditions = array(
                            'idno' => $row_selID,
                        );
                        $person = $this->user_model->_get($conditions);
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
                            if($insert_fields["year"]==''||$insert_fields["class_no"]==''||$insert_fields["term"]==''||$insert_fields["id"]==''||$insert_fields["beaurau_id"]==''){
                                redirect(base_url("customer_service/regist_personnel/regist/{$seq_no}"));
                            }

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
            redirect(base_url("customer_service/regist_personnel/regist/{$seq_no}"));
        }

        $this->data['import'] = base_url("customer_service/regist_personnel/regist_import/{$seq_no}");
        $this->data['regist_csv'] = base_url("customer_service/regist_personnel/regist_csv/{$seq_no}");
        $this->data['regist_pdf'] = base_url("customer_service/regist_personnel/regist_pdf/{$seq_no}");
        $this->data['student_detail_edit'] = base_url("customer_service/regist_personnel/student_detail_edit?");
        $this->data['link_cancel'] = base_url("customer_service/regist_personnel/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('customer_service/regist_personnel/regist', $this->data);
    }

    public function regist_pdf($seq_no=NULL)
    {
        $class_data = $this->regist_personnel_model->get($seq_no);
        $conditions = array(
            'year' => $class_data['year'],
            'class_no' => $class_data['class_no'],
            'term' => $class_data['term'],
            'yn_sel' => '2',
            'cre_user' => $this->flags->user['username'],
        );
        $regist_list = $this->online_app_model->getList($conditions);

        foreach($regist_list as $regist_list_key => $regist_list_row){
            $regist_list[$regist_list_key]['rownum'] = $regist_list_key+1;
        }

        $groupNumber = $this->online_app_model->get_group_number($conditions);

        if ($groupNumber > 0) {
            $isGroup = true;
        } else {
            $isGroup = false;
        }

        $this->load->library('pdf/PDF_Chinesess');
        $pdf = new PDF_Chinesess();

        $pdf->AddPage();

        $pdf->SetMargins(7,5,10,10);
        $pdf->AddBig5Font('uni', '黑体');
        $pdf->SetFont('uni', 'B', 8 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
        //$setTOP=22;
        $pdf->SetAutoPageBreak(false);

        //$class_name = iconv("UTF-8","BIG5",$class_name);
        $title="臺北市政府公務人員訓練處           報名人員名冊";
        $title1=$class_data['year']."年度  ".$class_data['class_name']."  第".$class_data['term']."期";

        $beaurau_id = $this->flags->user['bureau_id'];

        //表頭
        $pdf->SetFontSize(12);
        $pdf->Cell(180,5,iconv("utf-8","big5",$title),0,1,'C');
        $pdf->SetFontSize(10);
        $pdf->Cell(180,5,iconv("utf-8","big5",$title1),0,1,'C');

        $layoutParameter = array();

        //沒組別沒電話
        $layoutParameter[0] = array(
            array(
                'fieldName' => 'group_no',
                'titileName' => '組別',
                'width' => 10,
                'align' => 'C',
                'skip' => 1,
                'end' => 0
            ),
            array(
                'fieldName' => 'rownum',
                'titileName' => '序號',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'insert_order',
                'titileName' => '優先順序',
                'width' => 20,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'name',
                'titileName' => '姓名',
                'width' => 40,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'beaurau_name',
                'titileName' => '局處',
                'width' => 80,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'title',
                'titileName' => '職稱',
                'width' => 30,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'cell_phone',
                'titileName' => '電話',
                'width' => 30,
                'align' => 'C',
                'skip' => 1,
                'end' => 0
            ),
            array(
                'fieldName' => 'NOTE',
                'titileName' => '備註',
                'width' => 15,
                'align' => 'C',
                'skip' => 0,
                'end' => 1
            )
        );

        //沒組別有電話
        $layoutParameter[1] = array(
            array(
                'fieldName' => 'group_no',
                'titileName' => '組別',
                'width' => 10,
                'align' => 'C',
                'skip' => 1,
                'end' => 0
            ),
            array(
                'fieldName' => 'st_no',
                'titileName' => '學號',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'beaurau_name',
                'titileName' => '服務單位',
                'width' => 60,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'title',
                'titileName' => '職稱',
                'width' => 40,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'name',
                'titileName' => '姓名',
                'width' => 20,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'sex',
                'titileName' => '性別',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'cell_phone',
                'titileName' => '電話',
                'width' => 30,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'NOTE',
                'titileName' => '備註',
                'width' => 15,
                'align' => 'C',
                'skip' => 0,
                'end' => 1
            )
        );


        //有組別沒電話
        $layoutParameter[2] = array(
            array(
                'fieldName' => 'group_no',
                'titileName' => '組別',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'st_no',
                'titileName' => '學號',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'beaurau_name',
                'titileName' => '服務單位',
                'width' => 90,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'title',
                'titileName' => '職稱',
                'width' => 30,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'name',
                'titileName' => '姓名',
                'width' => 30,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'sex',
                'titileName' => '性別',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'cell_phone',
                'titileName' => '電話',
                'width' => 30,
                'align' => 'C',
                'skip' => 1,
                'end' => 0
            ),
            array(
                'fieldName' => 'NOTE',
                'titileName' => '備註',
                'width' => 15,
                'align' => 'C',
                'skip' => 0,
                'end' => 1
            )
        );

        //有組別有電話
        $layoutParameter[3] = array(
            array(
                'fieldName' => 'group_no',
                'titileName' => '組別',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'st_no',
                'titileName' => '學號',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'beaurau_name',
                'titileName' => '服務單位',
                'width' => 70,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'title',
                'titileName' => '職稱',
                'width' => 20,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'name',
                'titileName' => '姓名',
                'width' => 30,
                'align' => 'L',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'sex',
                'titileName' => '性別',
                'width' => 10,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'cell_phone',
                'titileName' => '電話',
                'width' => 30,
                'align' => 'C',
                'skip' => 0,
                'end' => 0
            ),
            array(
                'fieldName' => 'NOTE',
                'titileName' => '備註',
                'width' => 15,
                'align' => 'C',
                'skip' => 0,
                'end' => 1
            )
        );

        $ShowTel = FALSE;
        if ($isGroup && $ShowTel) {
            $contentLayout = $layoutParameter[3];
        } else if ($isGroup && !$ShowTel) {
            $contentLayout = $layoutParameter[2];
        } else if (!$isGroup && $ShowTel) {
            $contentLayout = $layoutParameter[1];
        } else {
            $contentLayout = $layoutParameter[0];
        }

            $pdf->SetFontSize(10);
            foreach ($contentLayout as $key => $value) {

                if ($value['skip'] == 1) {
                    continue;
                }

                if ($value['end'] == 0) {
                    $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,0,'C');
                } else {
                    $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,1,'C');
                    break;
                }

            }

            $page_num=50;//一頁顯示的資料筆數
            $i=1;
            $page=1;//頁碼
            $total=count($regist_list);//總筆數
            $page_total=ceil($total/$page_num);//總頁數

            foreach($regist_list as $arr){
                if($i>$page_num){
                    $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");

                    $pdf->AddPage();
                    $pdf->Cell(180,5,"",0,1,'C');
                    //start 表頭
                    $pdf->SetFontSize(12);
                    $pdf->Cell(180,5,$title,0,1,'C');
                    $pdf->SetFontSize(10);
                    $pdf->Cell(180,5,$title1,0,1,'C');
                    $pdf->SetFontSize(10);

                    foreach ($contentLayout as $key => $value) {

                        if ($value['skip'] == 1) {
                            continue;
                        }

                        if ($value['end'] == 0) {
                            $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,0,'C');
                        } else {
                            $pdf->Cell($value['width'],10,iconv("utf-8","big5",$value['titileName']),1,1,'C');
                            break;
                        }

                    }

                    //end 表頭
                    $i=1;
                    $page++;
                }
                $tmp_seq = '0';
                if ($tmp_seq!='0') {
                     if ($beaurau_id==$arr["beaurau_id"]) {
                        $pdf->SetFont('uni', 'U', 9 );          //設定文字格式SetFont('字體名稱', '底線', SIZE )
                        $arr["st_no"] = "*".$arr["st_no"];
                    } else {
                        $arr["st_no"] = $arr["st_no"];
                        $pdf->SetFont('uni', 'B', 8 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
                    }
                }
                // jd($arr);
                $arr["sex"] = ($arr["sex"]=="M"?"男":"女");
                $arr['NOTE'] = '';
                if ($arr["yn_sel"] == 4) {
                    $arr['NOTE'] = '退訓';
                } else if ($arr["yn_sel"] == 5) {
                    $arr['NOTE'] = '未報到';
                }

                foreach ($contentLayout as $key => $value) {

                    if ($value['skip'] == 1) {
                        continue;
                    }

                    if ($value['end'] == 0) {
                        // jd($arr);
                        // jd($value["width"]);
                        // jd($arr[$value["fieldName"]]);
                        // jd($value["align"]);
                        $pdf->Cell($value["width"],5,iconv("utf-8","big5//TRANSLIT",$arr[$value["fieldName"]]),0,0,$value["align"]);
                    } else {
                        $pdf->Cell($value["width"],5,iconv("utf-8","big5",$arr[$value["fieldName"]]),0,1,$value["align"]);
                        break;
                    }

                }

                $i++;
            }

        $note = iconv("utf-8","big5","【本表單為系統報名成功畫面，實際開課日期與錄取名單，將以Email發送研習訊息予研習人員與貴單位人事人員，請多加留意】");
        $pdf->Cell(180,15,iconv("utf-8","big5","第".$page."/".$page_total."頁"),0,1,"C");
        //Setting the text color to red
        $pdf->SetTextColor(194,8,8);
        $pdf->SetFont('uni', 'B', 9 );
        $pdf->Cell(189,15,$note,0,1,"C");
        ob_clean();
        $pdf->Output();
        ob_end_flush();

    }

    public function regist_csv($seq_no=NULL)
    {
        $class_data = $this->regist_personnel_model->get($seq_no);
        $conditions = array(
            'year' => $class_data['year'],
            'class_no' => $class_data['class_no'],
            'term' => $class_data['term'],
            'yn_sel' => '2',
            'cre_user' => $this->flags->user['username'],
        );
        $regist_list = $this->online_app_model->getList($conditions);

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=file.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $filename = 'file.csv';
        //表頭
        //------------------------------------------------------------------------------------
        echo iconv("UTF-8","BIG5","{$class_data['year']}年{$class_data['class_name']}第{$class_data['term']}期,");
        echo "\r\n";
        echo iconv("UTF-8","BIG5","序號,");
        echo iconv("UTF-8","BIG5","優先順序,");
        echo iconv("UTF-8","BIG5","身份證字號,");
        echo iconv("UTF-8","BIG5","姓名,");
        echo iconv("UTF-8","BIG5","局處,");
        echo iconv("UTF-8","BIG5","報名時間");
        echo "\r\n";
        //------------------------------------------------------------------------------------

        //資料
        //------------------------------------------------------------------------------------

          $i = 1;
          foreach($regist_list as $fields){
            echo iconv("UTF-8","BIG5",$i) . ",";
            echo iconv("UTF-8","BIG5",$fields['insert_order']) . ",";
            echo iconv("UTF-8","BIG5",$fields['id']) . ",";
            echo iconv("UTF-8","BIG5",$fields['name']) . ",";
            echo iconv("UTF-8","BIG5",$fields['beaurau_name']). ",";
            echo iconv("UTF-8","BIG5",$fields['insert_date']);

            echo "\r\n";
            $i = $i + 1;

          }
    }

    public function regist_import($seq_no=NULL)
    {
        $this->data['class'] = $this->regist_personnel_model->get($seq_no);
        // jd($this->data['class']);
        if(!isset($this->data['class'])){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('customer_service/regist_personnel/'));
        }
        $this->data['page_name'] = 'Upload';
        $from = 'taker';
        $massage = '';
        if ($post = $this->input->post()) {

            if ($this->_isVerify('add') == TRUE) {

                // upload file

                if (isset($_FILES['upload']) && $_FILES['upload']['tmp_name'] != '') {

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
                                    $insertOrder = $this->online_app_model->getThisMaxOrder($conditions);
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
                        $alreadySign = $this->online_app_model->getCurrentBureauPersonNo($conditions);
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
        $this->data['link_cancel'] = base_url("customer_service/regist_personnel/regist/{$seq_no}");
        $this->layout->view('customer_service/regist_personnel/upload_csv', $this->data);
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
        $this->data['link_save_phydisabled'] = base_url("customer_service/regist_personnel/phydisabled/{$id}");
        $this->load->view('customer_service/regist_personnel/phydisabledchg', $this->data);
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

    public function signup_change_report_detial($seq_no=NULL)
    {
        $this->data['class'] = $this->regist_personnel_model->get($seq_no);
        jd($this->data['class'],1);
    }

    public function student_new($row=NULL)
    {
        $this->data['student_new']['row'] = $row;

        $this->load->view('customer_service/regist_personnel/student_new', $this->data);
    }

    public function student_detail_edit()
    {
        $this->data['student_detail_edit']['errorMsg'] = '';
        if($get = $this->input->get()){
            if(empty($get['id']) && empty($get['row'])){
                $this->data['student_detail_edit']['errorMsg'] = '操作錯誤';
            }
        }
        $id = $get['id'];
        $row = $get['row'];
        $conditions = array(
            'idno' => $id,
        );
        $person = $this->user_model->get($conditions);
        $conditions = array(
            'item_id' => $person['job_title'],
        );
        $job_title = $this->job_title_model->get($conditions);
        $conditions = array(
            'bureau_id' => $person['bureau_id'],
        );
        $beaurau_old = $this->bureau_manage_model->get($conditions);
        $conditions = array(
            'bureau_id' => $this->flags->user['bureau_id'],
        );
        $beaurau_new = $this->bureau_manage_model->get($conditions);

        $this->data['student_edit'] = array(
            'name' => $person['name'],
            'title' => $person['job_title'],
            't_name' => $job_title['name'],
            'email' => $person['email'],
            'office_tel' => $person['office_tel'],
            'b_name_old' => $beaurau_old['name'],
            'bid_new' => $beaurau_new['bureau_id'],
            'bname_new' => $beaurau_new['name'],
            'row' => $row,
            'id' => $id,
            'idno' => $person['idno'],

        );
        // jd($this->data['student_edit']);
        $this->data['student_edit']['row'] = $row;

        $this->load->view('customer_service/regist_personnel/student_detail_edit', $this->data);
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

                            if($person['bureau_id'] != $this->flags->user['bureau_id']){
                                $result['msg'] = '非所屬人員,需要修改個資!';
                                $result['url'] = base_url("customer_service/regist_personnel/student_detail_edit?id={$post['id']}&row={$post['row']}");
                                break;
                            }

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

                            $person['phy_url'] = base_url("customer_service/regist_personnel/phydisabled/{$person['id']}");
                            $result['status'] = TRUE;
                            $result['person'] = $person;
                            $result['msg'] = '';
                        }else{
                            $result['msg'] = '無此身分證，將開啟新增學員基本資料頁！';
                            $result['row_id'] = intval($post['row']);
                            $result['url'] = base_url("data/student_manger/add");
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
                                'modify_item' => '取消報名',
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

                case 'check_name':

                    $error = FALSE;

                    if(empty($post['name'])){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        $conditions = array(
                            'name' => $post['name'],
                            'bureau_id' => $this->flags->user['bureau_id'],
                        );
                        $person = $this->user_model->get($conditions);
                        if($person){
                            $result['msg'] = '已有同姓名資料, 其身分證:['.$person['idno'].'],請再確認是否要新增此學員!!';
                        }else{
                            $result['status'] = TRUE;
                        }

                    }

                    break;

                case 'check_idno':

                    $error = FALSE;

                    if(empty($post['idno'])){
                        $error = TRUE;
                    }
                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        $idno = $this->make_semiangle($post['idno']);
                        $conditions = array(
                            // 'idno' => $person['idno'],
                            'idno' => $idno,
                        );
                        $person = $this->user_model->get($conditions);
                        if($person){
                            // $result['msg'] = '已有同身分證:['.$person['idno'].'],請再確認是否要新增此學員!!';
                            $result['msg'] = '已有同身分證:['.$idno.'],請再確認是否要新增此學員!!';
                        }else{
                            $result['status'] = TRUE;
                        }

                    }

                    break;

                case 'student_new':

                    $error = FALSE;
                    $idno = $this->make_semiangle($post['personal_id']);
                    $conditions = array(
                        // 'idno' => $post['personal_id'],
                        'idno' => $idno,
                    );
                    $person = $this->user_model->get($conditions);

                    if($person){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        $name=addslashes($post['name']);
                        $title=addslashes($post['title']);
                        $t_name=addslashes($post['t_name']);
                        $gender=addslashes($post['gender']);
                        // $personal_id=addslashes($post['personal_id']);
                        $personal_id=addslashes($idno);
                        $bid=addslashes($post['bid']);
                        $bname=addslashes($post['bname']);
                        $email=addslashes($post['email']);
                        $office_tel=addslashes($post['office_tel']);
                        $edu_level=addslashes($post['edu_level']);
                        $job_Distinguish=addslashes($post['job_Distinguish']);
                        $bir_year=addslashes($post['bir_year']);
                        $bir_month=addslashes($post['bir_month']);
                        $bir_day=addslashes($post['bir_day']);
                        $bir="{$bir_year}"."-"."{$bir_month}"."-"."{$bir_day}";
                        if ($gender == ""){
                            $gender = "M";
                        }

                        $fields = array(
                            // 'user_group_id' => '5',
                            'username' => $personal_id,
                            'password' => md5('123456'),
                            'name' => $name,
                            'enable' => '1',
                            'job_title' => $title,
                            'idno' =>$personal_id,
                            'gender' => $gender,
                            'bureau_id' => $bid,
                            'bureau_name' => $bname,
                            'company' => $bid.' - '.$bname,
                            //'email' => $email,
                            'office_email'=>$email,
                            'co_empdb_email' => $email,
                            'office_tel' => $office_tel,
                            'co_empdb_poftel'=>$office_tel,
                            'education' => $edu_level,
                            'job_distinguish' => $job_Distinguish,
                            'birthday' => $bir,
                            'msg_reserved' => '0',
                            'hid' => '0',
                        );
                        $save_id = $this->user_model->insert($fields);
                        if($save_id){
                            $insert_date = new DateTime();
                            $insert_date = $insert_date->format('Y-m-d H:i:s');
                            $fields = array(
                                'username' => $personal_id,
                                'group_id' => '5',
                                'cre_user' => $this->flags->user['username'],
                                'upd_user' => $this->flags->user['username'],
                                'cre_date' => $insert_date,
                                'upd_date' => $insert_date,
                            );

                            $saved_id = $this->account_role_model->insert($fields);
                            $result['status'] = TRUE;
                        }

                    }

                    break;

                case 'student_edit':

                    $error = FALSE;
                    // jd($post,1);
                    $conditions = array(
                        'idno' => $post['id'],
                    );
                    $person = $this->user_model->get($conditions);

                    if(empty($person)){
                        $error = TRUE;
                    }

                    if($error === TRUE){
                        $result['msg'] = '操作錯誤';
                    }else{
                        $name=addslashes($post['name']);
                        $title=addslashes($post['title']);
                        $bid=addslashes($post['bid']);
                        $bname=addslashes($post['bname']);
                        $email=addslashes($post['email']);
                        $office_tel=addslashes($post['office_tel']);

                        $fields = array(
                            'name' => $name,
                            'job_title' => $title,
                            'bureau_id' => $bid,
                            'bureau_name' => $bname,
                            'company' => $bid.' - '.$bname,
                            'office_email' => $email,
                            'office_tel' => $office_tel,
                        );
                        $conditions = array(
                            'idno' => $post['id'],
                        );
                        $save_id = $this->user_model->update($conditions, $fields);
                        if($save_id){
                            $fields = array();
                            $fields['job_title'] = array(
                                'personal_id' => $person['idno'],
                                'field' => 'job_title',
                                'value' => $title,
                                'key_user' => $this->flags->user['username'],
                            );
                            $fields['email'] = array(
                                'personal_id' => $person['idno'],
                                'field' => 'co_empdb_email',
                                'value' => $email,
                                'key_user' => $this->flags->user['username'],
                            );
                            $fields['office_tel'] = array(
                                'personal_id' => $person['idno'],
                                'field' => 'co_empdb_poftel',
                                'value' => $office_tel,
                                'key_user' => $this->flags->user['username'],
                            );
                            $fields['bureau_name'] = array(
                                'personal_id' => $person['idno'],
                                'field' => 'bureau_name',
                                'value' => $bname,
                                'key_user' => $this->flags->user['username'],
                            );
                            foreach($fields as $row){
                                if($row['value'] != $person[$row['field']]){
                                    $this->lux_account_log_model->insert($row, 'insert_time');
                                }
                            }

                            $result['status'] = TRUE;
                        }

                    }

                    break;

            }
        }

        echo json_encode($result);
    }
    // 全形改半形 + 去空白
    public function make_semiangle($str){
        $arr = array(   '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
                        '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
                        'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
                        'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
                        'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
                        'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
                        'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
                        'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
                        'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
                        'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
                        'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
                        'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
                        'ｙ' => 'y', 'ｚ' => 'z', '（' => '(', '）' => ')', '〔'  => '[',
                        '〕'  => ']', '【'  => '[', '】'  => ']', '〖' => '[', '〗' => ']',
                        '“'  => '[', '”'  => ']', '‘'  => '[', '\'' => ']', '｛' => '{',
                        '｝' => '}', '《'  => '<', '》'  => '>', '％' => '%', '＋' => ' ',
                        '—'  => '-', '－' => '-', '～' => '-', '：' => ':', '。' => '.',
                        '、'  => ',', '，' => '.', '、' => '.', '；' => ',', '？' => '?',
                        '！' => '!', '…'  => '-', '‖'  => '|', '”' => '"', '‵' => '`',
                        '‘'  => '`', '｜' => '|', '〃' => '"','　' => '' ,' '=>'');
        return strtr($str, $arr);
    }
}
