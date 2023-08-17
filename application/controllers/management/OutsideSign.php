<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class OutsideSign extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('management/outsideSign_model');
        $this->load->model('statistics_paper/Course_finish_count_model');
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $type = isset($_GET['type'])?$_GET['type']:"";
        $startMonth = isset($_GET['startMonth'])?$_GET['startMonth']:"";
        $endMonth = isset($_GET['endMonth'])?$_GET['endMonth']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $class_name = isset($_GET['class_name'])?$_GET['class_name']:"";
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 0){
            if($year != ""){
                $dateRange = $this->Course_finish_count_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1]; 
            } 
        } elseif ($type == 2) {
            $ssd = ($year+1911).'-'.$startMonth.'-01';
            $sed = date('Y-m-t', strtotime($ssd)); 
        }

        $this->data['sess_year'] = $year;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_class_name'] = $class_name;
     	
        $this->data['list'] = array();
        if(!empty($this->input->get())){
            $this->data['list'] = $this->outsideSign_model->getList($ssd,$sed,$class_name);
            
            foreach ($this->data['list'] as & $row) {
                if($row['side'] == 'I'){
                    $row['link_edit'] = base_url("management/OutsideSign/insideEdit/{$row['id']}?{$_SERVER['QUERY_STRING']}");
                    $row['link_record'] = base_url("management/OutsideSign/insideSignRecord/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
                    $row['link_delete'] = base_url("management/OutsideSign/insideDelete/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
                } else if($row['side'] == 'O'){
                    $row['link_edit'] = base_url("management/OutsideSign/outsideEdit/{$row['id']}?{$_SERVER['QUERY_STRING']}");
                    $row['link_record'] = base_url("management/OutsideSign/outsideSignRecord/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
                    $row['link_delete'] = base_url("management/OutsideSign/outsideDelete/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
                }
                
                $row['url'] = "https://dcsdcourse.taipei.gov.tw/outsideSign.php?id=".$row['id'];   

                $file_path = '/var/www/html/base/admin/images/outsign/outsign_'.intval($row['id']).'.png';
                if(file_exists($file_path)) {
                    $row['link_show_qrcode'] = base_url("management/OutsideSign/showQrcodeInfo/{$row['id']}");
                }
            }
        }

     	$this->data['link_insideAdd'] = base_url("management/OutsideSign/insideAdd");
    	$this->data['link_outsideAdd'] = base_url("management/OutsideSign/outsideAdd");

        $this->layout->view('management/OutsideSign/list',$this->data);
    }

    public function insideAdd()
    {
    	$this->data['page_name'] = 'add';

    	$post = $this->input->post();
    	if(!empty($post)){
            $post['side'] = 'I';
            $post['creator'] = $this->flags->user['name'];
            $post['creator_id'] = $this->flags->user['id'];
            $post['create_date'] = date('Y-m-d');

    		$insert_id = $this->outsideSign_model->addClassInfo($post);

            if($insert_id > 0){
                $course_date = addslashes($_POST['course_date']).' 23:59:59';
                $deadline = (strtotime($course_date))*1000;

                $this->createQrcode($insert_id, $deadline);
                $this->setAlert(1, '新增成功');
            } else {
                $this->setAlert(2, '新增失敗');
            }
            
            redirect(base_url("management/OutsideSign"));
    	}

        $this->data['link_action'] = base_url('management/OutsideSign/insideAdd');
    	$this->data['link_cancel'] = base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}");
    	$this->layout->view('management/OutsideSign/insideAdd',$this->data);
    }

    public function outsideAdd()
    {
    	$this->data['page_name'] = 'add';

        $post = $this->input->post();
        if(!empty($post)){
            $post['side'] = 'O';
            $post['creator'] = $this->flags->user['name'];
            $post['creator_id'] = $this->flags->user['id'];
            $post['create_date'] = date('Y-m-d');
            $insert_id = $this->outsideSign_model->addClassInfo($post);

            if($insert_id > 0){
                $course_date = addslashes($_POST['course_date']).' 23:59:59';
                $deadline = (strtotime($course_date))*1000;

                $this->createQrcode($insert_id, $deadline);
                $this->setAlert(1, '新增成功');
            } else {
                $this->setAlert(2, '新增失敗');
            }
            redirect(base_url("management/OutsideSign/importStudent/{$insert_id}"));
        }

        $this->data['link_action'] = base_url('management/OutsideSign/outsideAdd');
        $this->data['link_cancel'] = base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}");
    	$this->layout->view('management/OutsideSign/outsideAdd',$this->data);
    }

    private function createQrcode($insert_id, $deadline)
    {
        $data = [
            'id' => $insert_id,
            'type_outside' => 'outside',
            'extime' => $deadline
        ];
        
        $data = json_encode($data);
        
        $qrcodeurlParams = [
            'type'=>'verify_fetch_callback',
            'id' => '7a3a8e89-fc84-4862-ae8d-4266d929c803',
            'data' => $data
        ];
        
        $qrcodeurlParams['data'] = urlencode($qrcodeurlParams['data']);
        $checkString = implode('',$qrcodeurlParams);
        $qrcodeurlParams['checkCode'] = substr(hash('sha256', $checkString), 0, 6);
        $qrcodeurl = "https://id.taipei/tpcd/taipeipass-app/scan?".http_build_query($qrcodeurlParams);

        include(DIR_ROOT.'api/phpqrcode/phpqrcode.php');
        $value =  $qrcodeurl ; //QRcode内容網址
        $errorCorrectionLevel = 'L';//容錯級別
        $matrixPointSize = 8;//產生QRcode圖片大小

        //產生QRcode圖片
        QRcode::png($value, DIR_ROOT.'api/phpqrcode/qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);
        $logo = DIR_ROOT.'api/phpqrcode/logo.png';//準備好的logo圖片
        $QR = DIR_ROOT.'api/phpqrcode/qrcode.png';//已經產生的原始QRcode圖片 
        
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//QRcode圖片寬度
            $QR_height = imagesy($QR);//QRcode圖片高度
            $logo_width = imagesx($logo);//logo圖片寬度
            $logo_height = imagesy($logo);//logo圖片高度
            $logo_qr_width = $QR_width / 5;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新組合圖片並調整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);       

            // $newwidth = $newheight = 120;
            // $img = imagecreatetruecolor($newwidth, $newheight);
            // imagecopyresized($img, $QR, 0, 0, 0, 0, $newwidth, $newheight, $QR_width, $QR_height);

            //輸出圖片
            $temp_file_name = 'images/outsign/outsign_'.$insert_id.'.png';
            imagepng($QR, DIR_ROOT."admin/".$temp_file_name); 
        }
    }

    public function showQrcodeInfo($id)
    {
        $id = intval($id);
        $info = $this->outsideSign_model->getInfo($id);

        if(!empty($info)){
            $this->data['class_info'] =  $info[0]['class_name'];
            $this->data['course_date'] = '上課日期：'.(date('Y',strtotime($info[0]['course_date']))-1911).'/'.date('m',strtotime($info[0]['course_date'])).'/'.date('d',strtotime($info[0]['course_date']));
        }  else {
            $this->data['class_info'] = '';
            $this->data['course_date'] = '';
        }

        $this->data['info'] = $info;
        $this->data['file'] = '/base/admin/images/outsign/outsign_'.$id.'.png';
        $this->layout->view('management/OutsideSign/showQrcodeInfo',$this->data);
    }

    public function insideEdit($id)
    {   
        $post = $this->input->post();
        if(!empty($post)){
            $checkExist = $this->outsideSign_model->checkClassExist($id);

            if(!$checkExist){
                $this->setAlert(2, '修改失敗或查無此班期');
                redirect(base_url("management/OutsideSign"));
            }

            $status = $this->outsideSign_model->updateClassInfo($post,$id);

            if($status){
                $temp_file_name = DIR_ROOT."admin/".'images/outsign/outsign_'.$id.'.png';
        
                if(!file_exists($temp_file_name)){
                    $course_date = addslashes($post['course_date']).' 23:59:59';
                    $deadline = (strtotime($course_date))*1000;
                    
                    $this->createQrcode($id, $deadline);
                }

                $this->setAlert(1, '修改成功');
            } else {
                $this->setAlert(2, '修改失敗');
            }
            redirect(base_url("management/OutsideSign/insideEdit/{$id}/?{$_SERVER['QUERY_STRING']}"));
        }

        $this->data['info'] = $this->outsideSign_model->getInfo($id);
        $this->data['link_action'] = base_url("management/OutsideSign/insideEdit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_cancel'] = base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}");

        $this->layout->view('management/OutsideSign/insideAdd',$this->data);
    }

    public function outsideEdit($id)
    {
        $post = $this->input->post();
        if(!empty($post)){
            $checkExist = $this->outsideSign_model->checkClassExist($id);

            if(!$checkExist){
                $this->setAlert(2, '修改失敗或查無此班期');
                redirect(base_url("management/OutsideSign"));
            }

            $status = $this->outsideSign_model->updateClassInfo($post,$id);

            if($status){
                $temp_file_name = DIR_ROOT."admin/".'images/outsign/outsign_'.$id.'.png';
        
                if(!file_exists($temp_file_name)){
                    $course_date = addslashes($post['course_date']).' 23:59:59';
                    $deadline = (strtotime($course_date))*1000;
                    
                    $this->createQrcode($id, $deadline);
                }
        
                $this->setAlert(1, '修改成功');
            } else {
                $this->setAlert(2, '修改失敗');
            }
            redirect(base_url("management/OutsideSign/outsideEdit/{$id}/?{$_SERVER['QUERY_STRING']}"));
        }

        $this->data['info'] = $this->outsideSign_model->getInfo($id);
        $this->data['link_action'] = base_url("management/OutsideSign/outsideEdit/{$id}");
        $this->data['link_cancel'] = base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_importStudent'] = base_url("management/OutsideSign/importStudent/{$id}");
        $this->data['link_refresh'] = base_url("management/OutsideSign/outsideEdit/{$id}/?{$_SERVER['QUERY_STRING']}");

        $this->layout->view('management/OutsideSign/outsideAdd',$this->data);
    }

    public function insideDelete($id)
    {
        $status = $this->outsideSign_model->insideDelete($id);

        if($status){
            $this->setAlert(1, '刪除成功');
        } else {
            $this->setAlert(2, '刪除失敗');
        }
        redirect(base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}"));
    }

    public function outsideDelete($id)
    {
        $status = $this->outsideSign_model->outsideDelete($id);

        if($status){
            $this->setAlert(1, '刪除成功');
        } else {
            $this->setAlert(2, '刪除失敗');
        }
        redirect(base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}"));
    }

    public function importStudent($id)
    {
        $checkExist = $this->outsideSign_model->checkClassExist($id);

        if(!$checkExist){
            $this->setAlert(2, '新增失敗或查無此班期');
            redirect(base_url("management/OutsideSign"));
        }

        $this->data['id'] = $id;
        $import = $this->input->post('import');

        if($import == 'N'){
            $maxNO = $this->outsideSign_model->getStudentMaxNo($id);

            $data = array();
            $data['id'] = $id;
            $data['no'] = $maxNO;
            $data['idno'] = strtoupper(trim($this->input->post('idno')));
            $data['name'] = trim($this->input->post('name'));

            $status = $this->outsideSign_model->addStudent($data);

            if($status){
                $this->outsideSign_model->updateStudentCount($id);
                $this->setAlert(1, '新增成功');
            } else {
                $this->setAlert(2, '新增失敗');
            }
            redirect(base_url("management/OutsideSign/importStudent/{$id}"));
        } else if($import == 'Y'){
            if(isset($_FILES['myfile']['name'])){
                if(basename($_FILES['myfile']['name']) == 'importStudent.csv'){
                    $uploaddir = DIR_UPLOAD_FILES;
                    $uploadfile = $uploaddir.basename($_FILES['myfile']['name']);
                    $uploadfile = iconv("utf-8", "big5", $uploadfile);

                    if (move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile)) {    
                        $fp = fopen ($uploadfile,"r") or die("無法開啟");
                        $maxNO = $this->outsideSign_model->getStudentMaxNo($id);
                        $data = array();
                        $data['id'] = $id;
                        $row = 0;
                        $success = 0;
                        $fail = 0;
                        while(!feof($fp)){
                            $content = fgets($fp);
                            $content = mb_convert_encoding($content, 'UTF-8', 'BIG5');
                            $fields = explode(",",$content);

                            if($row == '1' && count($fields) == 2 && !empty($fields[0]) && !empty($fields[1])){
                                $data['no'] = $maxNO;
                                $data['idno'] = $fields[0];
                                $data['name'] = trim($fields[1]);
                                
                                $status = $this->outsideSign_model->addStudent($data);

                                if($status){
                                    $this->outsideSign_model->updateStudentCount($id);
                                    $success++;
                                    $maxNO++;
                                } else {
                                    $fail++;
                                }
                            }

                            $row = 1;
                        }

                        $this->setAlert(1, '資料匯入完成<br>'.'成功:'.$success.'筆<br>'.'失敗'.$fail.'筆');
                        redirect(base_url("management/OutsideSign/importStudent/{$id}"));
                    }
                }
            }
        }
        $this->data['link_home'] = base_url("management/OutsideSign");
        $this->layout->view('management/OutsideSign/importStudent',$this->data);
    }

    public function insideSignRecord($id)
    {   
        $list = $this->outsideSign_model->getInsideSignList($id);

        $this->data['list'] = $this->arrange($list);
        $this->data['link_export'] = base_url("management/OutsideSign/insideExport/{$id}");
        $this->data['link_cancel'] = base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/OutsideSign/signRecord',$this->data);
    }

    public function outsideSignRecord($id)
    {   
        $list = $this->outsideSign_model->getOutsideSignList($id);

        $this->data['list'] = $this->arrange($list);
        $this->data['link_export'] = base_url("management/OutsideSign/outsideExport/{$id}");
        $this->data['link_cancel'] = base_url("management/OutsideSign/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/OutsideSign/signRecord',$this->data);
    }

    public function insideExport($id){
        $list = $this->outsideSign_model->getInsideSignList($id);
        $list = $this->arrange($list,'export');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=card_record.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $filename = 'card_record.csv';

        if(!empty($list)){
            $class_name = $list[0]['class_name'];
        } else {
            $class_name = '';
        }

        echo iconv("UTF-8", "BIG5", $class_name . "學員刷卡紀錄\r\n");
        echo iconv("UTF-8", "BIG5", "學號,");
        echo iconv("UTF-8", "BIG5", "姓名,");
        echo iconv("UTF-8", "BIG5", "刷卡日期,");
        echo iconv("UTF-8", "BIG5", "簽到時間,");
        echo iconv("UTF-8", "BIG5", "簽退時間,");
        echo iconv("UTF-8", "BIG5", "刷卡紀錄,");
        echo iconv("UTF-8", "BIG5", "時數(應/未)\r\n");
        
        foreach ($list as $key => $value) {
            $value['signLog'] = str_replace("<br>","、",$value['signLog']);
            $value['hours'] = str_replace("/","|",$value['hours']);

            echo iconv("UTF-8", "BIG5", $value['no'].",");
            echo iconv("UTF-8", "BIG5//IGNORE", $value['name'].",");
            echo iconv("UTF-8", "BIG5", $value['sign_date'].",");
            echo iconv("UTF-8", "BIG5", $value['signInTime'].",");
            echo iconv("UTF-8", "BIG5", $value['signOutTime'].",");
            echo iconv("UTF-8", "BIG5", $value['signLog'].",");
            echo iconv("UTF-8", "BIG5", $value['hours']."\r\n");
        }

        exit;
    }

    public function outsideExport($id)
    {
        $list = $this->outsideSign_model->getOutsideSignList($id);
        $list = $this->arrange($list,'export');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=card_record.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $filename = 'card_record.csv';

        if(!empty($list)){
            $class_name = $list[0]['class_name'];
        } else {
            $class_name = '';
        }

        echo iconv("UTF-8", "BIG5", $class_name . "學員刷卡紀錄\r\n");
        echo iconv("UTF-8", "BIG5", "學號,");
        echo iconv("UTF-8", "BIG5", "姓名,");
        echo iconv("UTF-8", "BIG5", "刷卡日期,");
        echo iconv("UTF-8", "BIG5", "簽到時間,");
        echo iconv("UTF-8", "BIG5", "簽退時間,");
        echo iconv("UTF-8", "BIG5", "刷卡紀錄,");
        echo iconv("UTF-8", "BIG5", "時數(應/未)\r\n");
        
        foreach ($list as $key => $value) {
            $value['signLog'] = str_replace("<br>","、",$value['signLog']);
            $value['hours'] = str_replace("/","|",$value['hours']);
            
            echo iconv("UTF-8", "BIG5", $value['no'].",");
            echo iconv("UTF-8", "BIG5//IGNORE", $value['name'].",");
            echo iconv("UTF-8", "BIG5", $value['sign_date'].",");
            echo iconv("UTF-8", "BIG5", $value['signInTime'].",");
            echo iconv("UTF-8", "BIG5", $value['signOutTime'].",");
            echo iconv("UTF-8", "BIG5", $value['signLog'].",");
            echo iconv("UTF-8", "BIG5", $value['hours']."\r\n");
        }

        exit;
    }

    public function arrange($list,$type=null)
    {
        $actually = 0; //實到人數
        foreach ($list as $key => $value) {
            $signInTime = $this->outsideSign_model->getSignTime($value['id'],$value['idno'],'min');
            $list[$key]['signInTime'] = $signInTime[0]['sign_time'];
        
            $signOutTime = $this->outsideSign_model->getSignTime($value['id'],$value['idno'],'max');
            $list[$key]['signOutTime'] = $signOutTime[0]['sign_time'];
            
            $signLog = $this->outsideSign_model->getSignTime($value['id'],$value['idno']);

            if($signInTime[0]['sign_time'] == $signOutTime[0]['sign_time']){
                $list[$key]['signOutTime'] = '';
            }

            $tmpSignLog = '';
            for($i=0;$i<count($signLog);$i++) {
                if($signLog[$i]['type']=='台北通'){
                    if($type == 'export'){
                        $tmpSignLog .= $signLog[$i]['sign_time'].'(通)';
                    } else {
                        $tmpSignLog .= $signLog[$i]['sign_time'].'<font color="blue">(通)</font>';
                    }
                } else {
                    $tmpSignLog .= $signLog[$i]['sign_time'];
                }
                
                if((count($signLog)-1) != $i){
                    $tmpSignLog .= '<br>';
                }
            }

            if(!empty($tmpSignLog)){
                $list[$key]['signLog'] = $tmpSignLog;
            } else {
                $list[$key]['signLog'] = '';
            }

            if($list[$key]['signInTime'] == '' || $list[$key]['signOutTime'] == ''){
                $list[$key]['hours'] = $list[$key]['hours'].'/'.$list[$key]['hours'];
            } else {
                $actually++;
                $tmp_hours = ceil((strtotime($list[$key]['signOutTime']) - strtotime($list[$key]['signInTime']))/3600);

                if($tmp_hours >= $list[$key]['hours']){
                    $tmp_hours = 0;
                } else {
                    $tmp_hours = $list[$key]['hours'] - $tmp_hours;
                }
                $list[$key]['hours'] = $list[$key]['hours'].'/'.$tmp_hours;
            }
        }

        $total = count($list);
        $list[$total]['total'] = $total;
        $list[$total]['actually'] = $actually;
        $list[$total]['not_arrived'] = $total-$actually;

        return $list;
    }

}
