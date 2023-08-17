<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

define('SEND_FILE_FOLDER', './files/upload_files/progress/email/');
define("KEY_PHY", "ADLE3WE2R");
// error_reporting(0);
class Progress extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === false) {
            redirect(base_url('welcome'));
        }

        $this->load->model('create_class/progress_model');
        $this->load->model('create_class/volunteer_model');
        $this->load->model('data/template_list_model');
        $this->load->model("trainingchange/stud_modify_model");
        $this->load->model([
            "mail_log_model",
            "bs_user_model",
            "send_mail_file_model",
            "dining_teacher_model",
            "room_use_model",
            "dining_student_model",
            "require_exterm_model",
            "require_model",
            "online_app_model",
        ]);

        $this->data['class_type'] = array(
            'A' => '行政',
            'B' => '發展',
        );

        $this->data['choices']['season'] = array(
            '' => '請選擇季別',
            '1' => '第一季',
            '2' => '第二季',
            '3' => '第三季',
            '4' => '第四季',
        );

        $this->data['choices']['sort'] = array(
            '1' => '開班起日',
            '2' => '班期代碼',
        );

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '1';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }

        if (!isset($this->data['filter']['season'])) {
            $this->data['filter']['season'] = '';
        }
        if (!isset($this->data['filter']['checkAllClass'])) {
            $this->data['filter']['checkAllClass'] = '';
        }

        if (empty($this->data['filter']['start_date']) && empty($this->data['filter']['end_date'])) {

            $m = date("m");
            $y = date("Y");
            if ($m <= 3) {
                $this->data['filter']['start_date'] = $y . "-" . '01-01';
                $this->data['filter']['end_date'] = $y . "-" . '03-31';
            } else if ($m <= 6) {
                $this->data['filter']['start_date'] = $y . "-" . '04-01';
                $this->data['filter']['end_date'] = $y . "-" . '06-30';
            } else if ($m <= 9) {
                $this->data['filter']['start_date'] = $y . "-" . '07-01';
                $this->data['filter']['end_date'] = $y . "-" . '09-30';
            } else if ($m <= 12) {
                $this->data['filter']['start_date'] = $y . "-" . '10-01';
                $this->data['filter']['end_date'] = $y . "-" . '12-31';
            }
        }
        /* 應由前端直接修改 input 的值而不是後端自行判斷
    if($this->data['filter']['season'] != ''){
    $m=date("m");
    $y=date("Y");
    if($this->data['filter']['season'] == '1'){
    $this->data['filter']['start_date'] = $y."-".'01-01';
    $this->data['filter']['end_date'] = $y."-".'03-31';
    }
    if($this->data['filter']['season'] == '2'){
    $this->data['filter']['start_date'] = $y."-".'04-01';
    $this->data['filter']['end_date'] = $y."-".'06-30';
    }
    if($this->data['filter']['season'] == '3'){
    $this->data['filter']['start_date'] = $y."-".'07-01';
    $this->data['filter']['end_date'] = $y."-".'09-30';
    }
    if($this->data['filter']['season'] == '4'){
    $this->data['filter']['start_date'] = $y."-".'10-01';
    $this->data['filter']['end_date'] = $y."-".'12-31';
    }
    }
     */

    }

    public function index()
    {
        if(!isset($_GET['isexcel'])){
            $unassess = $this->progress_model->getUnAssess($this->flags->user['idno']);
            foreach ($unassess as $require) {
                echo "<script>alert('".htmlspecialchars($require->class_name, ENT_HTML5|ENT_QUOTES)." 第 ".htmlspecialchars($require->term, ENT_HTML5|ENT_QUOTES)." 期 講座評估未選')</script>";
            }
        }
       
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if(isset($_GET['isexcel']) && intval($_GET['isexcel']) == 1){
            $rows = 0;
        }

        $conditions = array();
        $checkAllClass = array();
        $conditions['worker'] = $this->flags->user['idno'];
        if ($this->data['filter']['checkAllClass'] != '') {
            $checkAllClass[] = $this->data['filter']['checkAllClass'];
        }

       
        if($this->data['filter']['start_date'] !== '' && $this->data['filter']['end_date'] !== ''){
            $tmp_condition = "((\"{$this->data['filter']['start_date']}\" between start_date1 and end_date1) or (\"{$this->data['filter']['end_date']}\" between start_date1 and end_date1) or ((start_date1 >= \"{$this->data['filter']['start_date']}\") and (end_date1 <= \"{$this->data['filter']['end_date']}\")))";
            $conditions[$tmp_condition] = null;
        } else if ($this->data['filter']['start_date'] !== '') {
            $conditions['start_date1 >='] = $this->data['filter']['start_date'];
        } else if ($this->data['filter']['end_date'] !== '') {
            $conditions['end_date1 <='] = $this->data['filter']['end_date'];
        }
      

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_no'] != '') {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['class_name'] != '') {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['sort'] != '') {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['checkAllClass'] != '') {
            unset($attrs['conditions']['worker']);
        }

        $this->data['filter']['total'] = $total = $this->progress_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page - 1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['sort'] != '') {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['class_no'] != '') {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['class_name'] != '') {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['checkAllClass'] != '') {
            unset($attrs['conditions']['worker']);
        }
        $this->data['list'] = $this->progress_model->getList($attrs);

        if(isset($_GET['isexcel']) && intval($_GET['isexcel']) == 1){
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

            $sheet->setCellValue('A1','系列');
            $sheet->setCellValue('B1','年度');
            $sheet->setCellValue('C1','班期代碼');
            $sheet->setCellValue('D1','班期名稱');
            $sheet->setCellValue('E1','期別');
            $sheet->setCellValue('F1','班期性質');
            $sheet->setCellValue('G1','帶班完成');
            $sheet->setCellValue('H1','開班起日');
            $sheet->setCellValue('I1','開班迄日');
            $sheet->setCellValue('J1','期程');
            $sheet->setCellValue('K1','教室');
            $sheet->setCellValue('L1','預計人數');
            $sheet->setCellValue('M1','實招人數');
            $sheet->setCellValue('N1','擬評估講師');
            $sheet->setCellValue('O1','名冊');
            $sheet->setCellValue('P1','承辦人');
            $sheet->setCellValue('Q1','課表');
            $sheet->setCellValue('R1','e大研習紀錄');
            $sheet->setCellValue('S1','Mail給老師');
            $sheet->setCellValue('T1','Mail給人事');
            $sheet->setCellValue('U1','異動否');
            $sheet->setCellValue('V1','Mail給學員');
            $sheet->setCellValue('W1','Mail給單位承辦人');
            $sheet->setCellValue('X1','Mail給未錄取');
            $sheet->setCellValue('Y1','Mail報名資訊');
            $sheet->setCellValue('Z1','取消開班');
            $sheet->setCellValue('AA1','進度查詢');

            $excel_rows = 2;
            for($i=0;$i<count($this->data['list']);$i++){
                $sheet->setCellValue('A'.$excel_rows, $this->data['class_type'][$this->data['list'][$i]['type']]);
                $sheet->setCellValue('B'.$excel_rows, $this->data['list'][$i]['year']);
                $sheet->setCellValue('C'.$excel_rows, $this->data['list'][$i]['class_no']);
                $sheet->setCellValue('D'.$excel_rows, $this->data['list'][$i]['class_name']);
                $sheet->setCellValue('E'.$excel_rows, $this->data['list'][$i]['term']);

                if ($this->data['list'][$i]['is_assess'] == '1' && $this->data['list'][$i]['is_mixed'] == '1') {
                    $sheet->setCellValue('F'.$excel_rows, '混成');
                } else {
                    if ($this->data['list'][$i]['is_assess'] == '1') {
                        $sheet->setCellValue('F'.$excel_rows, '考核');
                    } else {
                        $sheet->setCellValue('F'.$excel_rows, '');
                    }
                }

                if ($this->data['list'][$i]['isend'] == 'Y') {
                    $sheet->setCellValue('G'.$excel_rows, '是');
                } else {
                    $sheet->setCellValue('G'.$excel_rows, '否');
                }
                
                $sheet->setCellValue('H'.$excel_rows, $this->data['list'][$i]['start_date1_format']);
                $sheet->setCellValue('I'.$excel_rows, $this->data['list'][$i]['end_date1_format']);
                $sheet->setCellValue('J'.$excel_rows, $this->data['list'][$i]['range']);
                $sheet->setCellValue('K'.$excel_rows, $this->data['list'][$i]['sname']);
                $sheet->setCellValue('L'.$excel_rows, $this->data['list'][$i]['no_persons']);
                $sheet->setCellValue('M'.$excel_rows, $this->data['list'][$i]['true_count']);

                if ($this->data['list'][$i]['ISEVALUATE'] == 'Y' && $this->data['list'][$i]['teacher_assess_count'] > 0) {
                    $sheet->setCellValue('N'.$excel_rows, $this->data['list'][$i]['teacher_count']);
                } else {
                    $sheet->setCellValue('N'.$excel_rows, '');
                }

                if ($this->data['list'][$i]['student_count'] > '0') {
                    $student_list_url =base_url("student_list_pdf.php").'?uid='.$this->data['uid'].'&year='.$this->data['list'][$i]['year'].'&class_no='.$this->data['list'][$i]['class_no'].'&term='.$this->data['list'][$i]['term'].'&tmp_seq=0&ShowRetirement=1';
                    $sheet->setCellValue('O'.$excel_rows, $student_list_url);
                } else {
                    $sheet->setCellValue('O'.$excel_rows, '');
                }

                $sheet->setCellValue('P'.$excel_rows, $this->data['list'][$i]['name']);

                if ($this->data['list'][$i]['course_count'] > '0') {
                    $course_count_url = base_url("create_class/print_schedule/print/{$this->data['list'][$i]['seq_no']}");
                    $sheet->setCellValue('Q'.$excel_rows, $course_count_url);
                } else {
                    $sheet->setCellValue('Q'.$excel_rows, '');
                }

                if ($this->data['list'][$i]['is_mixed'] > '0') {
                    $sheet->setCellValue('R'.$excel_rows, '是');
                } else {
                    $sheet->setCellValue('R'.$excel_rows, '');
                }

                if ($this->data['list'][$i]['course_count'] > '0') {
                    if ($this->data['list'][$i]['mail_teacher_count'] > '0') {
                        $sheet->setCellValue('S'.$excel_rows, '是');
                    } else {
                        $sheet->setCellValue('S'.$excel_rows, '否');
                    }
                } else {
                    $sheet->setCellValue('S'.$excel_rows, '');
                }
                
                if ($this->data['list'][$i]['student_count'] > '0') {
                    if ($this->data['list'][$i]['mail_mag_count'] > '0') {
                        $sheet->setCellValue('T'.$excel_rows, '是');
                    } else {    
                        $sheet->setCellValue('T'.$excel_rows, '否');
                    }
                }
                
                if ($this->data['list'][$i]['sd_modify'] == '1') {
                    $sheet->setCellValue('U'.$excel_rows, '是');
                } else {
                    $sheet->setCellValue('U'.$excel_rows, '否');
                }
                
                if($this->data['list'][$i]['student_count']>0) {
                    if ($this->data['list'][$i]['mail_student_count'] > '0') {
                        $sheet->setCellValue('V'.$excel_rows, '是');
                    } else {
                        $sheet->setCellValue('V'.$excel_rows, '否');
                    }
                } else {
                    $sheet->setCellValue('V'.$excel_rows, '');
                }

                if ($this->data['list'][$i]['CONTACTOR_EMAIL'] != '') {
                    if ($this->data['list'][$i]['mail_undertaker_count'] > '0') {
                        $sheet->setCellValue('W'.$excel_rows, '是');
                    } else {
                        $sheet->setCellValue('W'.$excel_rows, '否');
                    }
                } else {
                    $sheet->setCellValue('W'.$excel_rows, '');
                }
                
                if ($this->data['list'][$i]['mail_norecd_count'] > '0') {
                    $sheet->setCellValue('X'.$excel_rows, '是');
                } else {
                    $sheet->setCellValue('X'.$excel_rows, '否');
                }
                
                if ($this->data['list'][$i]['mail_adm_b_count'] > '0') {
                    $sheet->setCellValue('Y'.$excel_rows, '是');
                } else {
                    $sheet->setCellValue('Y'.$excel_rows, '否');
                }
                
                if ($this->data['list'][$i]['is_cancel'] == '1') {
                    $sheet->setCellValue('Z'.$excel_rows, '是');
                } else {
                    $sheet->setCellValue('Z'.$excel_rows, '否');
                }

                $params_array = array();
                $params_array['year'] = $this->data['list'][$i]['year'];
                $params_array['class_no'] = $this->data['list'][$i]['class_no'];
                $params_array['term'] = $this->data['list'][$i]['term'];
                $current_status = $this->progress_model->getClassSchedule($params_array);
                $sheet->setCellValue('AA'.$excel_rows, $current_status);

                $excel_rows++;
            }

            $style_array = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PHPExcel_Style_Border::BORDER_THIN
                    )
                )                                                                                                                                                       );
            $sheet->getStyle('A1:AA'.($excel_rows-1))->applyFromArray($style_array);

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

            header('Content-Type:application/csv;charset=UTF-8');
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
            header("Content-Type:application/force-download");
            header("Content-Type:application/vnd.ms-excel;");
            header("Content-Type:application/octet-stream");
            header('Content-Disposition: attachment;filename="'.generatorRandom(10).'.xlsx"');
            header("Content-Transfer-Encoding:binary");
            $objWriter->save('php://output');

            exit;
        }

        // jd($this->data['list']);
        // foreach ($this->data['list'] as & $row) {
        //     $row['link_edit'] = base_url("create_class/progress/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
        // }
        $this->load->library('pagination');
        $config['base_url'] = base_url("create_class/progress?" . $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_check_status'] = base_url("create_class/progress/checkStatus");
        $this->data['link_get_end_status'] = base_url("create_class/progress/setClassEnd");
        $this->data['link_refresh'] = base_url("create_class/progress/");
        $this->data['uid'] = $this->flags->user['id'];
        $this->layout->view('create_class/progress/list', $this->data);
    }
    /*
    e大研習紀錄
     */

    /**
     * @param int (舊)$who, 1 => 老師, 2 => 人事, 3 => 學員, 4 => 單位承辦人, 5 => 給未錄取, 6 => 報名資訊
     * MAIL給 1:學生 2:老師 3:人事  4:單位承辦人 5 調訊通知(補發) 6:學員(補發) 7:學員成績 8:取消開班 9:未錄取 10:報名資訊 11:寄送研習紀錄給人事(16F)
     */
    public function mail_select($who)
    {
        // 檢查以下參數若無則給予 null 避免error
        $this->getFilterData(['year', 'class_no', 'term', 'start_date', 'end_date', 'class_name', 'checkAllClass']);
        $params = $this->getFilterData(['year', 'class_no', 'term']);

        $config = [
            [
                'field' => 'year',
                'label' => 'year',
                'rules' => 'required',
            ], [
                'field' => 'class_no',
                'label' => 'class_no',
                'rules' => 'required',
            ], [
                'field' => 'term',
                'label' => 'term',
                'rules' => 'required',
            ],
        ];


        $this->form_validation->set_data($params);
        $this->form_validation->set_rules($config);

        if ($this->form_validation->run() != false) {
            $this->data['require'] = $this->progress_model->getRequire($params);
            if (!empty($this->data['require'])) {
                $this->data['link_confirm'] = base_url("create_class/progress/mail_to/" . $who . "?{$_SERVER['QUERY_STRING']}");

                switch ($who) {
                    case '1':
                        $this->data['list'] = $this->progress_model->getStudent($params);
                        $this->data['list_header'] = ['狀態', '學號', '局處', '姓名', '電話', 'E-mail'];
                        $this->data['email_who'] = "學生";
                        $this->data['_LOCATION']['name'] = "課程表及名冊mail給學生作業";
                        //echo'hello';
                        //var_dump($this->data['list']);

                        break;
                    case '2':
                        $this->data['list'] = $this->progress_model->getTeacher($params['class_no'], $params['year'], $params['term']);
                        $this->data['list_header'] = ['講師', '課程', 'E-mail', '<input type=checkbox name=all2 id=all2 onclick=chk_all2(this.checked)>寄送電子簽章'];//20210611 Roger加入全選方塊
                        $this->data['email_who'] = "老師";
                        $this->data['_LOCATION']['name'] = "課程表及名冊mail給老師作業";
                        

                        break;
                    case '3':
                        $this->data['list'] = $this->progress_model->getPersonnel($params['class_no'], $params['year'], $params['term']);
                        $this->data['list_header'] = ['姓名', '局處', '電話', 'E-mail'];
                        $this->data['email_who'] = "人事";
                        $this->data['_LOCATION']['name'] = "課程表及名冊mail給人事作業";
                        break;
                    case '8':
                        //$this->data['list'] = $this->progress_model->getStudent($params);
                        
                        $status=$this->input->get('status');
                        if($status==0){
                            $dd = $this->progress_model->resetCancel($params['year'],$params['class_no'],$params['term']);
                            
                            if (isset($_SERVER['HTTP_REFERER'])) {
                                redirect($_SERVER['HTTP_REFERER']);
                            } else {
                                redirect(base_url('create_class/progress'));
                            }                            
                        }
                            
                        
                        $this->data['list']=$this->progress_model->getAllStudent($params['class_no'],$params['year'],$params['term']);
                        
                        $this->data['list'] = array_merge($this->data['list'], $this->progress_model->getPersonnel($params['class_no'], $params['year'], $params['term']));
                        $this->data['list_header'] = ['狀態', '學號', '局處', '姓名', '電話', 'Email'];
                        $this->data['email_who'] = "學生";
                        $this->data['_LOCATION']['name'] = "課程表及名冊mail給學生作業";
                        // 如果沒有學員則直接取消開班
                        //$allStudnet = $this->online_app_model->getStudent($params);
                        $allStudnet = $this->online_app_model->getStudent($params);
                       
                        //var_dump($mail_data['phy_schedule']);
                            //var_dump($query->result());
                            //die();
                            //var_dump($allStudnet);
                            //$this->data['list']=$this->progress_model->getAllStudent($params['class_no'],$params['year'],$params['term']);
                        //}  
                        if (empty($allStudnet)) {
                            $result = $this->require_model->update($params, ['is_cancel' => 1]);
                            if ($result) {
                                $this->setAlert(3, "取消開班完成");
                            } else {
                                $this->setAlert(3, "取消開班時發生異常");
                            }

                            if (isset($_SERVER['HTTP_REFERER'])) {
                                redirect($_SERVER['HTTP_REFERER']);
                            } else {
                                redirect(base_url('create_class/progress'));
                            }
                        }

                        break;
                    case '9':
                        $this->data['list'] = $this->progress_model->getNoPass($params['class_no'], $params['year'], $params['term']);
                        $this->data['list_header'] = ['姓名', '局處', '電話', 'E-mail'];
                        $this->data['email_who'] = "未錄取學員及人事";
                        $this->data['_LOCATION']['name'] = "mail給未錄取學員及人事作業";
                        break;
                    case '10':
                        for ($i = 0; $i < 3; $i++) {
                            $this->data['list'][$i]['data'] = $this->progress_model->getBureau($i);
                            
                        }

                        $this->data['list'][0]['who'] = "一級局處";
                        $this->data['list'][1]['who'] = "二級局處";
                        $this->data['list'][2]['who'] = "學校";

                        $this->data['list_header'] = ['局處', '電話', 'E-mail'];
                        $this->data['email_who'] = "人事報名資訊";
                        break;
                    default:
                        die("非法操作");
                        break;
                }

                $this->data['link_cancel'] = base_url("create_class/progress?{$_SERVER['QUERY_STRING']}");
                $this->data['who'] = $who;
                $this->data['condition'] = $params;
                // $this->data['_LOCATION']['name'] = "mail給".$this->data['email_who']."作業";
                $this->layout->view('create_class/progress/mail_select', $this->data);
            } else {
                $this->setAlert(3, "找不到該班期資訊");
                redirect(base_url("create_class/progress"));
            }
        } else {
            $this->setAlert(3, "非法操作 !");
            redirect(base_url("create_class/progress"));
        }

    }

    public function mail_to($who)
    {
        $params = ['year', 'class_no', 'term'];
        $params = $this->getFilterData($params);
        $post = $this->input->post();

        if (!isset($post['email'])) {
            if (!empty($this->input->get("email"))) {
                $post['email'] = [$this->input->get("email")];
            }
        }


        if (!empty($post['email'])) {
            $require = $this->progress_model->getRequire($params);
            $class_name = $require->class_name;
            $class_total_name = $params['year'] . " 年度 " . $class_name . " 第 " . $params['term'];
            $mail_title = [
                '0' => "公訓處 : " . $class_total_name . " 期 課表及名冊",
                '1' => "公訓處 : " . $class_total_name . " 期 研習通知",
                '2' => "公訓處 : " . $class_total_name . " 期 課表及名冊",
                '3' => "公訓處調訓通知 : " . $class_total_name . " 期 課表及名冊，請轉知參訓同仁。",
                '8' => "(取消開班通知) 公訓處 : " . $class_total_name . " 期 課表及名冊",
                '9' => "(未錄取通知) 公訓處 : " . $class_total_name . " 期",
            ];

            // 信件標題
            $mail_title = (empty($mail_title[$who])) ? $mail_title['0'] : $mail_title[$who];
            // 收件人名單
            $email = (!empty($post['email'])) ? join(",", $post['email']) : "";

            // 範本標題
            $this->data['templates']['mail_content_template'] = $this->template_list_model->getData(['conditions' => ['item_id' => '01', 'is_open' => 1], 'order_by' => 'tmp_seq'], 'object');

            

            $this->data['templates']['course_content_template'] = $this->template_list_model->getData(['conditions' => ['item_id' => '02', 'is_open' => 1], 'order_by' => 'tmp_seq'], 'object');

            $this->data['send_email'] = base_url('create_class/progress/send_email/' . $who . "?{$_SERVER['QUERY_STRING']}");
            $this->data['who'] = $who;
            $this->data['condition'] = $params;
            $this->data['data']['email'] = $email;
            $this->data['data']['mail_title'] = $mail_title;
            $this->data['link_cancel'] = "history_go_back";

            if (isset($post['signatures'])) {
                $this->data['signatures'] = $post['signatures'];
            }
           


            $this->layout->view('create_class/progress/mail_to', $this->data);

        } else {
            $this->setAlert(3, "請選擇Email !");
            redirect(base_url("create_class/progress/mail_select/" . $who . "?{$_SERVER['QUERY_STRING']}"));
        }

    }
    public function send_email($who)
    {

        $this->load->helper("progress");
        $progress_helper = new progress_helper();

        $params = ['year', 'class_no', 'term'];
        $params = $this->getFilterData($params);
        // 檢查檔案大小
        $total_size = 0;

        if (!empty($_FILES['email_file']) && count(array_filter($_FILES['email_file']['name'])) != 0) {
            if (!fileExtensionCheck($_FILES['email_file']['name'], ['odt', 'ods', 'odp', 'docx', 'xlsx', 'pptx', 'doc', 'xls', 'ppt', 'zip', 'rar', 'jpg', 'png', 'gif', 'pdf'])){
                $this->setAlert(3, "不允許的檔案格式");
                redirect(base_url("create_class/progress"));
            }

            foreach ($_FILES['email_file']['size'] as $size) {
                $total_size += $size;
                if (($size / 1024 / 1024) > 2) {
                    echo "<script>alert('檔案超過2MB，請分成50人寄送');</script>";
                }
            }
        }

        if (($total_size / 1024) / 1024 > 5) {
            die("上傳檔案總和超過5MB");
        }

        $post = $this->input->post();
        $class_info = $this->progress_model->getRequire($params);
        $email_list = explode(",", $post['email']);
        $email_list = array_filter($email_list);

        if (!empty($class_info)) {

            $s_name = [
                '0' => '研習人員名冊',
                '8' => '取消開班人員名冊',
                '9' => '未錄取人員名冊',
            ];
            $mail_data = [
                'mail_content' => $post['mail_content'],
                'course_content' => $post['course_content'],
                'class_info' => $class_info,
            ];

            switch ($who) {
                case '1':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '2':
                    if (empty($post['signatures'])) {
                        $post['signatures'] = [];
                    }

                    $from = $class_info->worker_email;
                    $mail_data['signatures'] = $this->progress_model->getSignatureLinks($params, $post['signatures'], $email_list);
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                case '3':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '8':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '9':
                    $mail_data['course_content'] = "";
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '10':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    break;
                default:
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
            }
            $replace_data = $this->progress_model->getReplaceData($params);

            $mail_data['course_content'] = replaceEmailContent($mail_data['course_content'], $replace_data);
            $mail_data['mail_content'] = replaceEmailContent($mail_data['mail_content'], $replace_data);

            $email_content = arrangeEmailContent($mail_data); // progress helper

            

        } else {
            $this->setAlert(2, "找不到該班期資訊");
            redirect(base_url("create_class/progress"));
        }

        if ($post['send'] == "true") {
            $config['upload_path'] = SEND_FILE_FOLDER;
            $config['max_size'] = '5120';
            $config['allowed_types'] = '*';

            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            // 上傳檔案
            $files = $_FILES;
            $upload_file_info = [];

            if (!empty($files)) {
                for ($i = 0; $i < count($files['email_file']['name']); $i++) {
                    $_FILES['email_file']['name'] = $files['email_file']['name'][$i];
                    $_FILES['email_file']['type'] = $files['email_file']['type'][$i];
                    $_FILES['email_file']['tmp_name'] = $files['email_file']['tmp_name'][$i];
                    $_FILES['email_file']['error'] = $files['email_file']['error'][$i];
                    $_FILES['email_file']['size'] = $files['email_file']['size'][$i];
                    if (!empty($_FILES['email_file']['name'])) {
                        if (!$this->upload->do_upload('email_file')) {
                            $error = $this->upload->display_errors();
                            echo $error;die;
                        } else {
                            $upload_file_info[] = $this->upload->data();
                        }
                    }
                }
            }
            //因應舊系統格式
            $len = strlen(SEND_FILE_FOLDER);
            $upload_folder = substr(SEND_FILE_FOLDER, 2, $len);

            // 開始寄信
            $this->load->library('email');

            // 如果沒有設定 信件來自誰
            if (!isset($from)) {
                $from = "pstc_member@gov.taipei";
            }

            $worker_content = "收件者 : ".join(", ", $email_list)."<br>";

            // 也寄給承辦人一封
            if (!empty($class_info->worker_email)) {
                $email_list["worker"] = $class_info->worker_email;
            }

            foreach ($upload_file_info as $key => $file) {
                if (file_exists($file['full_path'])) {
                    $send_mail_file = [
                        "year" => $params['year'],
                        "class_no" => $params['class_no'],
                        "term" => $params['term'],
                        "file_path" => $upload_folder . $file['file_name'],
                        "send_date" => date("Y-m-d H:i:s"),
                        "cre_user" => $this->flags->user['username'],
                        "cre_date" => date("Y-m-d H:i:s"),
                    ];
                    $this->send_mail_file_model->insert($send_mail_file);
                }
            }

            $send = true;
            $error_email = [];
            

            foreach ($email_list as $key => $email) {
                $email = trim($email);
                $email_content = arrangeEmailContent($mail_data, $key); // progress helper
                
                
                // 清除紀錄
                $this->email->clear(true);
                // 設定標題及內容
                $this->email->from($from, '臺北市政府公務人員訓練處');
                
                
                    $this->email->to($email);
                // }

                $this->email->subject($post['title']);
                if ($key === "worker") {
                    $this->email->message($worker_content.$email_content);
                }else{
                    $this->email->message($email_content);
                }
                
                foreach ($upload_file_info as $key => $file) {
                    if (file_exists($file['full_path'])) {
                        $this->email->attach($file['full_path']);
                    }
                }
                    
                $send =  $this->email->send();
                if (!$send) $error_email[] = $email;
            }

            $email_content = arrangeEmailContent($mail_data); // progress helper
            $query_string = "class_no=" . $params['class_no'] . "&year=" . $params['year'] . "&term=" . $params['term'];

            //dd($this->email->print_debugger());
            if (count($error_email) == 0){
                $this->setAlert(2, "寄送成功");
            }else{
                $error_message = "部份信箱寄送失敗，請修正後再重新補發!\\n".htmlspecialchars(join("\\n", $error_email), ENT_HTML5|ENT_QUOTES);
            }
            
            if($who == 1 || $who == 3){
                $email_content2 = arrangeEmailContent($mail_data, null, false);
            } else {
                $email_content2 = null;
            }

            // 新增寄送信件紀錄
            $mail_info = ['title' => $post['title'], 'content' => $email_content, 'content2' => $email_content2];
            if ($who != 10) { // 報名資訊不用紀錄
                $progress_helper->insertMailLog($params, $mail_info, $who);
            }else{
                $progress_helper->insertMailLog($params, $mail_info, "A");
            }

            $filterQueryString = filterQueryString($_SERVER['QUERY_STRING']);
            $redirect_url = base_url("create_class/progress?{$filterQueryString}");
            // 後續處理
            switch ($who) {
                case '1': // 學生
                    $this->require_model->update($params, ["mail_date" => date("Y-m-d H:i:s")]);
                    $redirect_url = base_url("create_class/progress/student_change_setting?" . $filterQueryString);
                    break;
                case '3': //人事
                    // 寄給人事後將選員改為調訓
                    $condition = array_merge($params, ['yn_sel' => 3]);
                    //$this->progress_model->insertStudLog($params);
                    
                    $this->progress_model->insertStudLog($params);
                        //die();
                    //}
                    $this->progress_model->setOnlineApp($condition, ['yn_sel' => '8']);
                    

                    //將用餐統計人數修改為調訓人數
                    $training_people = $this->progress_model->getTrainingPeople($params);
                    $update_data = [];
                    $update_data['persons_1'] = "if(is_null(persons_1), persons_1, {$training_people})";
                    $update_data['persons_2'] = "if(is_null(persons_2), persons_2, {$training_people})";
                    $update_data['persons_3'] = "if(is_null(persons_3), persons_3, {$training_people})";

                    $this->dining_student_model->update($params, $update_data);
                    // 新增 ???
                    $room_use_dates = $this->room_use_model->getClassRoomUse($params);
                    
                    foreach ($room_use_dates as $room_use) {
                        $this->dining_teacher_model->spAddDiningTeacher($params, $room_use->use_date, $this->flags->user['username']);
                        $this->dining_student_model->spAddDiningStudent($params, $room_use->use_date, $this->flags->user['username']);
                    }
                    
                    
                    $require_exterm = [
                        "year" => $class_info->year,
                        "class_no" => $class_info->class_no,
                        "term" => $class_info->term,
                        "cre_date" => date("Y-m-d"),
                        "seq" => $class_info->seq_no,
                        "class_content2" => $mail_data['course_content'],
                    ];

                    $this->require_exterm_model->insert($require_exterm);

                    // 導向寄送給學員頁面
                    $redirect_url = base_url("create_class/progress/mail_select/1?" . $filterQueryString);
                    break;
                case '8': //取消開班
                    $del_booking_room = $this->progress_model->delBookingRoom(addslashes($params['year']), addslashes($params['class_no']), addslashes($params['term']));
                    $update_room_blank = $this->progress_model->updateRoomToBlank(addslashes($params['year']), addslashes($params['class_no']), addslashes($params['term']));

                    $volunteer_course_id = $this->volunteer_model->getVolunteerClassId(addslashes($params['year']), addslashes($params['class_no']), addslashes($params['term']));
                    if($volunteer_course_id > 0){
                        $course_list = $this->volunteer_model->getCourseInfo(intval($volunteer_course_id));
                        
                        for($i=0;$i<count($course_list);$i++){
                            $volunteer_mail_content = '';
                            $user_info = array();
                            $user_info = $this->volunteer_model->getVolunteerApplyUser(intval($course_list[$i]['id']));

                            for($j=0;$j<count($user_info);$j++){
                                if(!empty($user_info[$j]['email']) && !empty($user_info[$j]['name'])){
                                    $date_array = explode('-',addslashes($course_list[$i]['date']));
                                    $volunteer_date = ($date_array[0]-1911).'年'.$date_array[1].'月'.$date_array[2].'日 '.$course_list[$i]['start_time'].'~'.$course_list[$i]['end_time'];
                                    $volunteer_mail_content = sprintf("Dear '%s' 先生/小姐您好:<br>感謝您支持:臺北市政府公務人員訓練處志工隊之志願服務，<br>有關您選填:'%s'<br>班期名稱:'%s'<br>原為正取人員，因上述志工管理者已為您取消報名，<br>故原定報名服務班次已取消！特來信通知，萬分感謝！！<br><br><br><font style='color:red'>此封信件為系統發出的信件，請勿直接回覆，謝謝！</font>", $user_info[$j]['name'], $volunteer_date, $course_list[$i]['name']);

                                    // 清除紀錄
                                    $this->email->clear(true);
                                    // 設定標題及內容
                                    $this->email->from("pstc_member@gov.taipei", '臺北市政府公務人員訓練處');
                                    $this->email->to($user_info[$j]['email']);
                                    $this->email->subject('志工報名取消通知');
                                    $this->email->message($volunteer_mail_content);
                                    $volunteer_send = $this->email->send();
                                }
                            }

                            $this->volunteer_model->delVolunteerCalendarApply(intval($course_list[$i]['id']));
                        }

                        $del_volunteer_calendar = $this->volunteer_model->delVolunteerCalendar(intval($volunteer_course_id));
                        $del_volunteer_course = $this->volunteer_model->delVolunteerCourse(intval($volunteer_course_id));
                    }
                    
                    $result = $progress_helper->cancelRequire($params);

                    if ($result && $del_booking_room && $update_room_blank) {
                        $this->setAlert(3, "取消開班完成");
                    } else {
                        $this->setAlert(3, "取消開班時發生異常");
                    }
                default:
                    # code...
                    break;
            }

            if (count($error_email) > 0){
                echo "<script>alert('{$error_message}')</script>";
                echo "<script>location.href='".$redirect_url."';</script>";
                die;
            }

            redirect($redirect_url);
            

        } else if ($post['send'] == "false") {
            // 預覽
            // $error_email = ["pstc_pppiii@gov.taipei","pstc_pif999@gov.taipei","fetbrook@gmail.com"];
            // $error_message = "部份信箱寄送失敗，請修正後再重新補發!\\n".join("\\n", $error_email);
            // echo "<script>alert('{$error_message}')</script>";

            echo xss_clean($email_content);
        }

    }
    public function student_change_setting()
    {

        $this->data['_LOCATION']['name'] = "學員異動作業設定";
        $this->data['link_cancel'] = base_url("create_class/progress?{$_SERVER['QUERY_STRING']}");
        $this->data['history_back'] = true;

        $params = ['year', 'class_no', 'term'];
        $params = $this->getFilterData($params); // 班期資訊

        if ($params['year'] == null || $params['class_no'] == null || $params['term'] == null) die("缺少參數");

        $validation = false;
        $post = $this->input->post();

        if (!empty($post)) {
            // 按下儲存
            $config = [
                [
                    'field' => 'sd_modify', 'label' => '異動否', 'rules' => 'required',
                ], [
                    'field' => 'sd_cnt', 'label' => '人數', 'rules' => 'required',
                ], [
                    'field' => 'sd_cancel', 'label' => '取消參訓', 'rules' => 'required',
                ], [
                    'field' => 'sd_change', 'label' => '互調', 'rules' => 'required',
                ], [
                    'field' => 'sd_chgterm', 'label' => '換期', 'rules' => 'required',
                ], [
                    'field' => 'sd_another', 'label' => '換員', 'rules' => 'required',
                ], [
                    'field' => 'sd_wantchg', 'label' => '異動媒合否', 'rules' => 'required',
                ], [
                    'field' => 'sd_edate', 'label' => '截止日期', 'rules' => 'required',
                ], [
                    'field' => 'co_sheet_open_sdate', 'label' => '開放顯示錄取名冊(起)', 'rules' => 'required',
                ], [
                    'field' => 'co_sheet_open_edate', 'label' => '開放顯示錄取名冊(訖)', 'rules' => 'required',
                ],
            ];
            $this->form_validation->set_rules($config);
            $validation = $this->form_validation->run();
            if ($validation != false) {
                $post['sd_cnt'] = (int) $post['sd_cnt']; //預防有人亂填寫入資料庫型態錯誤
                $post['sd_edate_h_m'] = (empty($post['sd_edate_h_m'])) ? "23:59" : addslashes($post['sd_edate_h_m']);

                $stud_modify = [
                    'sd_modify' => addslashes($post['sd_modify']),
                    'sd_cnt' => addslashes($post['sd_cnt']),
                    'sd_cancel' => addslashes($post['sd_cancel']),
                    'sd_change' => addslashes($post['sd_change']),
                    'sd_chgterm' => addslashes($post['sd_chgterm']),
                    'sd_another' => addslashes($post['sd_another']),
                    'sd_wantchg' => addslashes($post['sd_wantchg']),
                    'sd_edate' => addslashes($post['sd_edate']),
                    'sd_edate_h_m'=> addslashes($post['sd_edate_h_m'])
                ];
                if (empty($post['co_sheet_open_sdate']) || empty($post['co_sheet_open_edate'])){
                    $this->require_model->update($params, 
                        [
                            'co_open_member_sheet' => 'N',
                            'co_sheet_open_sdate' => null, 
                            'co_sheet_open_edate' => null
                        ]
                    );
                }else{
                    $this->require_model->update($params, 
                        [
                            'co_open_member_sheet' => 'Y',
                            'co_sheet_open_sdate' => addslashes($post['co_sheet_open_sdate']), 
                            'co_sheet_open_edate' => addslashes($post['co_sheet_open_edate'])
                        ]
                    );
                }
                

                $this->stud_modify_model->update($params, $stud_modify);
                $this->setAlert(2, "設定成功");
                redirect(base_url("create_class/progress/student_change_setting?class_no=" . $params['class_no'] . "&year=" . $params['year'] . "&term=" . $params['term'] . "&history_back=" . ($this->data['history_back'] - 1)));
            } else {
                $this->data['history_back']--;
            }

        }

        if (!$validation) {
            //沒按下儲存或者參數有錯
            $stud = $this->stud_modify_model->find($params['year'], $params['class_no'], $params['term']);
            $this->data['link_save'] = base_url("create_class/progress/student_change_setting?class_no=" . $params['class_no'] . "&year=" . $params['year'] . "&term=" . $params['term']);
            $this->data["page_name"] = "";

            if (!empty($stud)) {
                if (empty($stud['year'])) {
                    $stud = [
                        "year" => $params['year'],
                        "class_no" => $params['class_no'],
                        "term" => $params['term'],
                    ];
                    $this->stud_modify_model->insert($stud);
                }
                $stud = $this->stud_modify_model->find($params['year'], $params['class_no'], $params['term']);
            } else {
                die("找不到該班期資訊");
            }
            $stud = array_merge($stud, $post);

            if (empty($stud["sd_edate"])) {
                if (!empty($stud['start_date1'])) {
                    $start_date1 = new DateTime($stud['start_date1']);
                    $start_date1->sub(new DateInterval('P1D'));
                    $stud["sd_edate"] = $start_date1->format("Y-m-d");
                }
            }

            $stud['sd_edate_h_m'] = (empty($stud["sd_edate_h_m"])) ? "23:59" : $stud["sd_edate_h_m"];

            $this->data['stud'] = $stud;
            //var_dump($this->data['stud']);
            $this->layout->view('trainingchange/student_change', $this->data);
        }

    }
    /*
    擬評估講師
     */
    public function setEvaluationTeacher($id)
    {
        $post = $this->input->post();


        if (!empty($post)) {
            $post['cre_user'] = $this->flags->user['idno'];
            $setEvaluationTeacher = $this->progress_model->setEvaluationTeacher($post);
        }

        $this->data['list'] = $this->progress_model->getEvaluationTeacher($id);
        $this->data['list2'] = $this->progress_model->getEvaluationOther($id); //20211112 Roger 列出其它問卷

        foreach ($this->data['list2'] as $key => $list2) {
            
            $this->data['list2'][$key]['sing_count'] = $this->progress_model->checkHasAnswer($list2['cmid'],$list2['fid']);  //查詢已經有幾筆問卷的回答
        }
        $this->data['form_list'] = $this->progress_model->getFormList();

        /* $this->data['form_list'] = $this->progress_model->getFormList();
        $this->data['question_id'] = $this->progress_model->getSvClassManagementFormFid($id);
        if($this->data['question_id'] == '0'){
            $this->data['question_id'] = '99';
        } */
        
        $cmid = $this->progress_model->getSvClassManagementFormCmid($id);

        if(!empty($cmid)){
            $this->data['cmid'] = $cmid[0]['id'];
            $this->data['anonymous'] = $cmid[0]['anonymous'];
            $this->data['anonymous_url'] = 'https://dcsdcourse.taipei.gov.tw/survey/user_formList?idno=guest&cmid='.$cmid[0]['id'];

            if($this->data['list'][0]['isevaluate_no_teacher'] == 'Y'){
                $this->data['special_evaluate_date'] = $this->progress_model->getSpecialEvaluationDate($this->data['cmid']);
            }
        } else {
            $this->data['cmid'] = 0;
            $this->data['anonymous'] = '';
        }

        $this->data['default_start_date'] = $this->progress_model->getDefaultDate($id);

        if(!empty($this->data['default_start_date'])){
            $this->data['default_start_date'] = date("Y-m-d",strtotime($this->data['default_start_date']));
        }

        $this->data['default_end_date'] = date("Y-m-d",strtotime($this->data['default_start_date']."+1 day"));

        $this->data['link_view'] = base_url("create_class/progress/viewForm/{$this->data['cmid']}");
        $this->data['link_save'] = base_url("create_class/progress/setEvaluationTeacher/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_insent'] = base_url("create_class/progress/insentsv/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_del'] = base_url("create_class/progress/delsv/{$id}/?");
        $this->data['link_cancel'] = base_url("create_class/progress/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('create_class/progress/set_evaluation_teacher', $this->data);
    }

    //20211115 Roger 新增特殊問卷
    public function insentsv($id)
    {
        $post = $this->input->post();

        if(strtotime($post['standard_date2']) > strtotime($post['standard_date_end2'])){
            $this->setAlert(4, "起始日期不能大於訖日");
            
            redirect(base_url("create_class/progress/setEvaluationTeacher/{$id}/?{$_SERVER['QUERY_STRING']}"));
        }

        if (!empty($post)) {
            if ($post['other_sl']=='insert'){
                
                $post['cre_user'] = $this->flags->user['idno'];
                $post['cmid'] = $this->progress_model->getSvClassManagementFormCmid($id)[0]['id'];
                $setEvaluationOther = $this->progress_model->instEvaluationOther($post);
            }else if ($post['other_sl']=='update'){
                if (!$post['rowid_other']){
                    $this->setAlert(3, "您尚未選取問卷");
                }
                $setEvaluationOther = $this->progress_model->update_other($post);
            }
            
        }
        redirect(base_url("create_class/progress/setEvaluationTeacher/{$id}/?{$_SERVER['QUERY_STRING']}"));

    }
    //20211115 Roger 刪除特殊問卷
    public function delsv($id)
    {
        $get = $this->input->get();
        //var_dump($get);die();
        if (!empty($get)) {
            
            $delsv = $this->progress_model->deltEvaluationOther($get['cmid'],$get['fid']);
        }
        redirect(base_url("create_class/progress/setEvaluationTeacher/{$id}/?{$_SERVER['QUERY_STRING']}"));

    }

    public function checkStatus()
    {
        $year = addslashes($this->input->post('year'));
        $class_no = addslashes($this->input->post('class_no'));
        $term = addslashes($this->input->post('term'));

        $wait_confirm = $this->progress_model->checkWaitConfirm($year,$class_no,$term);
        $app_seq = $this->progress_model->checkAppSeq($year,$class_no,$term);

        if($wait_confirm[0]['cnt'] == '0' && $app_seq[0]['cnt'] == '0'){
            echo 'OK';
        } else if($wait_confirm[0]['cnt'] > 0 && $app_seq[0]['cnt'] > 0){
            echo 'WA';
        } else if($wait_confirm[0]['cnt'] > 0){
            echo 'W';
        } else if($app_seq[0]['cnt'] > 0){
            echo 'A';
        }
    }
    
    public function setClassEnd()
    {
        $year = addslashes($this->input->post('year'));
        $class_no = addslashes($this->input->post('class_no'));
        $term = addslashes($this->input->post('term'));
        $is_end = addslashes($this->input->post('is_end'));

        // $season = $this->input->post('season');
        // $login_name = $this->input->post('login_name');
        // $start_date = $this->input->post('start_date');
        // $end_date = $this->input->post('end_date');

        $online_app_count = $this->progress_model->getOnlineAppCount($year, $class_no, $term);

        if ($online_app_count > 0) {
            $require_data = $this->progress_model->getAssessMix($year, $class_no, $term);

            $isAssess = 0;
            $isMixed = 0;

            if (!empty($require_data)) {
                if (!empty($require_data[0]['is_assess'])) {
                    $isAssess = 1;
                }

                if (!empty($require_data[0]['is_mixed'])) {
                    $isMixed = 1;
                }
            }

            if ($isAssess && $isMixed) {
                $stuList = $this->progress_model->getOnlineAppID($year, $class_no, $term);

                if (count($stuList) > 0) {
                    for ($i = 0; $i < count($stuList); $i++) {
                        $chk1 = $this->progress_model->updateOnlineApp($year, $class_no, $term, $stuList[$i]['id']);
                        if (!$chk1) {
                            echo 'onlineApp_error';
                            exit;
                        }
                    }

                    $chk2 = $this->progress_model->updateRequire($year, $class_no, $term, $is_end);

                    $cancel_stuList = $this->progress_model->getCancelStudent($year, $class_no, $term);

                    if (count($cancel_stuList) > 0) {
                        $cmid = $this->progress_model->getCmid($year, $class_no, $term);

                        if(!empty($cmid)){
                            for($i=0;$i<count($cancel_stuList);$i++){
                                $rid = $this->progress_model->getRid($cmid[0]['id'],$cancel_stuList[$i]['id']);
                                if(!empty($rid)){
                                    $this->progress_model->delReply($cmid[0]['id'],$cancel_stuList[$i]['id']);
                                    for($j=0;$j<count($rid);$j++){
                                        $rqid = $this->progress_model->getRqid($rid[$j]['id']);
                                        if(!empty($rqid)){
                                            $this->progress_model->delReplyQuestion($rid[$j]['id'],$cancel_stuList[$i]['id']);
                                            for($k=0;$k<count($rqid);$k++){
                                                $this->progress_model->delReplyAnswer($rqid[$k]['id'],$cancel_stuList[$i]['id']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($chk1 && $chk2) {
                        echo 'OK';
                    } else {
                        echo 'require_error';
                    }
                }
            } else if (!$isMixed && $isAssess) {
                $stuList = $this->progress_model->getOnlineAppID2($year, $class_no, $term);

                if (count($stuList) > 0) {
                    for ($i = 0; $i < count($stuList); $i++) {
                        $chk1 = $this->progress_model->updateOnlineApp($year, $class_no, $term, $stuList[$i]['id']);
                        if (!$chk1) {
                            echo 'onlineApp_error';
                            exit;
                        }
                    }

                    $chk2 = $this->progress_model->updateRequire($year, $class_no, $term, $is_end);

                    $cancel_stuList = $this->progress_model->getCancelStudent($year, $class_no, $term);

                    if (count($cancel_stuList) > 0) {
                        $cmid = $this->progress_model->getCmid($year, $class_no, $term);

                        if(!empty($cmid)){
                            for($i=0;$i<count($cancel_stuList);$i++){
                                $rid = $this->progress_model->getRid($cmid[0]['id'],$cancel_stuList[$i]['id']);
                                if(!empty($rid)){
                                    $this->progress_model->delReply($cmid[0]['id'],$cancel_stuList[$i]['id']);
                                    for($j=0;$j<count($rid);$j++){
                                        $rqid = $this->progress_model->getRqid($rid[$j]['id']);
                                        if(!empty($rqid)){
                                            $this->progress_model->delReplyQuestion($rid[$j]['id'],$cancel_stuList[$i]['id']);
                                            for($k=0;$k<count($rqid);$k++){
                                                $this->progress_model->delReplyAnswer($rqid[$k]['id'],$cancel_stuList[$i]['id']);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    if ($chk1 && $chk2) {
                        echo 'OK';
                    } else {
                        echo 'require_error';
                    }
                }
            } else {
                $chk1 = $this->progress_model->updateRequire($year, $class_no, $term, $is_end);

                $cancel_stuList = $this->progress_model->getCancelStudent($year, $class_no, $term);

                if (count($cancel_stuList) > 0) {
                    $cmid = $this->progress_model->getCmid($year, $class_no, $term);

                    if(!empty($cmid)){
                        for($i=0;$i<count($cancel_stuList);$i++){
                            $rid = $this->progress_model->getRid($cmid[0]['id'],$cancel_stuList[$i]['id']);
                            if(!empty($rid)){
                                $this->progress_model->delReply($cmid[0]['id'],$cancel_stuList[$i]['id']);
                                for($j=0;$j<count($rid);$j++){
                                    $rqid = $this->progress_model->getRqid($rid[$j]['id']);
                                    if(!empty($rqid)){
                                        $this->progress_model->delReplyQuestion($rid[$j]['id'],$cancel_stuList[$i]['id']);
                                        for($k=0;$k<count($rqid);$k++){
                                            $this->progress_model->delReplyAnswer($rqid[$k]['id'],$cancel_stuList[$i]['id']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }

                $chk2 = $this->progress_model->updateOnlineAppLoop($year, $class_no, $term);

                if ($chk1 && $chk2) {
                    echo 'OK';
                } else {
                    echo 'require_error';
                }
            }
        } else {
            echo '12B';
        }
    }
    /*
    重設取消開班
     */
    public function cancelRequire()
    {

        $class_info = $this->getFilterData(['year', 'class_no', 'term'], null, true); // 班期資訊
        if (empty($class_info)) {
            $this->setAlert(3, "參數異常");
        } else {
            $this->require_model->update($class_info, ['is_cancel' => null]);
            $this->setAlert(3, "完成重設取消開班");
        }
        if (isset($_SERVER['HTTP_REFERER'])) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(base_url('create_class/progress'));
        }

    }
    /*
    進度查詢
     */
    public function query_schedule()
    {
        $this->data['form'] = $this->getFilterData(['year', 'class_no', 'term']);
        $this->data['form']['class_name'] = $this->progress_model->getClassName($this->data['form']);
        $this->data['form']['schedule'] = $this->progress_model->getClassSchedule($this->data['form']);
        $this->layout->view('create_class/progress/class_schedule', $this->data);
    }

    /*
    選擇轉寄給哪些學員
     */
    public function select_student_turn()
    {
        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        if ($post = $this->input->post()) {
            $this->send_to_student();
            die();
        }
        $this->data['require'] = $this->require_model->find($class_info);
        $this->data['link_confirm'] = base_url("create_class/progress/select_student_turn?year={$class_info['year']}&class_no={$class_info['class_no']}&term={$class_info['term']}");
        $this->data['students'] = $this->progress_model->getStudent($class_info, $this->flags->user['bureau_id']);
        $this->data['list_header'] = ['狀態', '學號', '局處', '姓名', '電話', 'E-mail'];
        $this->data['email_who'] = "學生";
        $this->data['_LOCATION']['name'] = "課程表及名冊mail給學生作業(轉寄)";
        $this->layout->view('create_class/progress/select_student_turn', $this->data);
    }

    /*
    轉寄給學生
     */
    public function send_to_student()
    {

        $this->load->library('email');

        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        $email_list = $this->input->post('email');
        
            //$email_list=$email_list.',tobychen5487@gmail.com';
            //var_dump($this->flags->user['email']);
            
        $email_list[count($email_list)]=$this->flags->user['email'];
            //var_dump($email_list);
            //die();
       // }
        // 取得    最後一次寄給學員的email歷史紀錄
        $require = $this->require_model->find($class_info);
        $mail_log = $this->mail_log_model->find($class_info, 1);
        $files = $this->send_mail_file_model->getList($class_info);
        $send = true;

        if (!empty($mail_log)) {
            $user_list = $this->progress_model->getCourseUserList(1, $class_info);
            if(!empty($user_list) && $mail_log->chk_cre_date > 1604246400){
                $this->load->helper("progress");
                $user_list_table = getUserList($user_list);
                $mail_log->body = $mail_log->body2.$user_list_table;
            }


            $from = "pstc_member@gov.taipei";

            $email_list = join(",", $email_list);
            $email_list = explode(",", $email_list);
            $email_list = array_filter($email_list);


            foreach ($email_list as $email) {
                $email = trim($email);

                // 清除紀錄
                $this->email->clear(true);
                // 設定標題及內容
                $this->email->from($from, '臺北市政府公務人員訓練處');
                $this->email->to($email);
                $this->email->subject($mail_log->subject . "(轉寄)");
                //$this->email->subject($mail_log->subject);
                $this->email->message($mail_log->body);

                foreach ($files as $key => $file) {
                    if (file_exists(FCPATH . $file->file_path)) {
                        $this->email->attach(FCPATH . $file->file_path);
                    }
                }

                $send = $send && $this->email->send();
            }
            $seq_no = $this->mail_log_model->getSeqNo();
            $now = date("Y-m-d H:i:s");
            $mail_log = [
                "seq" => $seq_no->seq + 1,
                "year" => $require->year,
                "class_no" => $require->class_no,
                "term" => $require->term,
                "subject" => "(轉寄)" . $mail_log->subject,
                "body" => $mail_log->body,
                "cre_user" => $this->flags->user['username'],
                "cre_date" => $now,
                "mail_type" => 6,
            ];

            if ($send) {
                $this->setAlert(2, "轉寄成功");
                $this->mail_log_model->insert($mail_log);
            } else {
                // dd($this->email->print_debugger());
                $this->setAlert(4, "部分轉寄失敗");
            }
            redirect(base_url("create_class/progress/select_student_turn?year={$class_info['year']}&class_no={$class_info['class_no']}&term={$class_info['term']}"));
        } else {
            die("找不到該班期的寄信歷史紀錄");
        }
    }
    
    /*
    e大研習紀錄
     */
    public function onlineRecord()
    {
        $this->load->helper("progress");
        $query_condition = $this->getFilterData(['query_type', 'unfinish_course_id']);
        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        $this->data['onlines'] = $this->require_model->getOnlineRequire($class_info);
        $this->data['require'] = $this->progress_model->getRequire($class_info);
        if ($post = $this->input->post()) {
            if (empty($post['emails'])) {
                $this->setAlert(3, "請選擇學員!");
                redirect(base_url("create_class/progress/onlineRecord?{$_SERVER['QUERY_STRING']}"));
            }

            $mail_data = [
                'mail_content' => $post['mail_content'],
                'course_content' => '',
                'class_info' => $this->progress_model->getRequire($class_info),
            ];

            

            $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($class_info);
            $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($class_info);
            $mail_data['s_name'] = "研習人員名冊";
            $mail_data['user_list'] = $this->progress_model->getCourseUserList(1, $class_info);
            $replace_data = $this->progress_model->getReplaceData($class_info);
            $mail_data['course_content'] = replaceEmailContent($mail_data['course_content'], $replace_data);
            $mail_data['mail_content'] = replaceEmailContent($mail_data['mail_content'], $replace_data);
            $email_content = arrangeEmailContent($mail_data); // progress helper



            $email_list = $post['emails'];
            $title = [
                "un_register" => "提醒加入臺北e大會員)公訓處：{$mail_data['class_info']->year}年度 {$mail_data['class_info']->class_name} 第{$mail_data['class_info']->term}期 課前通知",
                "un_finish" => "線上課程閱讀通知 公訓處：{$mail_data['class_info']->year}年度 {$mail_data['class_info']->class_name} 第{$mail_data['class_info']->term}期",
            ];

            $send = true;

            foreach ($email_list as $key => $email) {
                $email = trim($email);

                // 清除紀錄
                $this->email->clear(true);
                // 設定標題及內容
                $this->email->from("pstc_member@gov.taipei", '臺北市政府公務人員訓練處');
                $this->email->to($email);

                // $this->email->to("blin9533@gmail.com"); // debug

                $this->email->subject($title[$query_condition['query_type']]);
                $this->email->message($email_content);

                $send = $send && $this->email->send();
            }

            if ($send) {
                $this->setAlert(2, "寄送成功");
            } else {
                $this->setAlert(4, "部分寄送失敗");
            }

            redirect(base_url("create_class/progress/onlineRecord?{$_SERVER['QUERY_STRING']}"));

        }

        if (empty($query_condition['query_type'])) {
            $this->data['list'] = [];
        } else {
            if ($query_condition['query_type'] == "un_register") {

                $this->data['list'] = $this->online_app_model->getUnRegister($class_info);
                $list = [];
                foreach ($this->data['list'] as $row) {
                    if (!$this->queryUserRegisterEda($row->id)) {
                        $list[] = $row;
                    }
                }
                $this->data['list'] = $list;

            } else if ($query_condition['query_type'] == "un_finish" && !empty($query_condition['unfinish_course_id'])) {

                $this->data['list'] = $this->online_app_model->getUnFinish($class_info);
                $list = [];
                foreach ($this->data['list'] as $row) {
                    $course_id = [];

                    if ((int) $query_condition['unfinish_course_id'] === -1) {
                        foreach ($this->data['onlines'] as $online) {
                            if ($online->elearn_id > 1) {
                                $course_id[] = $online->elearn_id;
                            }
                        }
                    } else {
                        $course_id = [$query_condition['unfinish_course_id']];
                    }
                    // dd($_SERVER['SERVER_ADDR']);

                    if (count($this->queryCompletedEda($row, $course_id, $class_info))>0) {
                        $list[] = $row;
                    }
                }
                $this->data['list'] = $list;
            } else {
                $this->data['list'] = [];
            }
        }

        $this->data['templates']['course_content_template'] = $this->template_list_model->getData(['conditions' => ['item_id' => '02', 'is_open' => 1], 'order_by' => 'tmp_seq'], 'object');

        $condition = $this->getFilterData(['year', 'class_no', 'term']);

        // $this->data['send_email'] = base_url('create_class/progress/send_email/' . '1' . "?{$_SERVER['QUERY_STRING']}");

        $this->layout->view("create_class/progress/onlineRecord", $this->data);
    }

    /**
     * query that's user register for Eda
     * @param $id string 身分字號
     * @return ture or false
     */
    public function queryUserRegisterEda($id)
    {
        $idno = OLD_DES::encrypt(KEY_PHY, addslashes($id));
        $idno = OLD_DES::base64url_encode($idno);

        $data['idno'] = $idno;
        $data['mode'] = '1';

        $url = "https://elearning.taipei/get_data.php";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 600);
        
        $output = curl_exec($ch);
        if ($output === false){
            var_dump(curl_error($ch));
            die;
        }

        curl_close($ch);

        if ($output > 0) {
            return true;
        }
        return false;
    }

/**
 * query that's user completed for Eda
 * @param $row object 學員資料
 * @param $cAay array elearn課程id
 * @return array (no completed)
 */
    public function queryCompletedEda($row, $cAay, $class_info)
    {
        $Y = (int) date('Y', strtotime($row->start_date1));
        $m = (int) date('m', strtotime($row->start_date1));

        if ($m == 1) {
            $Y = $Y - 1;
        }

        $retuenAry = array();
        $cNum = count($cAay);
        $classIn = "";
        $Yyear = intval($class_info['year']) + 1911;
        for ($i = 0; $i < count($cAay); $i++) {
            $classIn .= sprintf("'%s', ", $cAay[$i]);
        }
        if (strlen($classIn) > 2) {
            $classIn = substr($classIn, 0, strlen($classIn) - 2);
        }

        for ($i = $Yyear; $i >= $Y; $i--) {
            $finishAry = array();
            if ($i == date("Y")) {
                $table = "mdl_fet_course_history";
            } else {
                $table = "mdl_fet_course_history_" . $i;
            }

            $idno = OLD_DES::encrypt(KEY_PHY, addslashes($row->id));
            $idno = OLD_DES::base64url_encode($idno);

            $data['idno'] = $idno;
            $data['table'] = $table;
            $data['classIn'] = $classIn;
            $data['mode'] = '2';

            $url = "https://elearning.taipei/get_data.php";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $output = curl_exec($ch);

            if ($output === false){
                var_dump(curl_error($ch));
                die;
            }

            $finishAry = unserialize($output);
            curl_close($ch);

            $a = array_diff($cAay, $finishAry); // 未完成的課程代碼
            $retuenAry = array_merge($retuenAry, $finishAry);

        }

        $retuenAry = array_unique($retuenAry);
        foreach ($retuenAry as $k => $v) {
            if (($key = array_search($v, $cAay)) !== false) {
                unset($cAay[$key]);
            }
        }

        if ($cAay) {
            return $cAay;
        } else {
            return array();
        }
    }

    public function viewForm($cmid){
        $this->data['form'] = $this->progress_model->getSvClassManagementFormid($cmid);
        //20211203 Roger 在問卷名稱上新增
        $this->data['courseinfo'] = $this->progress_model->getcourseinfo9a($cmid);
        $this->layout->view('create_class/progress/viewform', $this->data);
    }

    public function updateSVOrder(){
        // $this->progress_model->updateSVOrder();
        // die('end');
    }

}
