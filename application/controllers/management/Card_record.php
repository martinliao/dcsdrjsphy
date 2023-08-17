<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Card_record extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === false) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/card_record_model');
        $this->load->model('management/Vegetarian_management_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }

        if (!isset($this->data['filter']['start_date1'])) {
            $this->data['filter']['start_date1'] = '';
        }

        if (!isset($this->data['filter']['hand_people_num'])) {
            $this->data['filter']['hand_people_num'] = '';
        }
        if (!isset($this->data['filter']['disabled_people_num'])) {
            $this->data['filter']['disabled_people_num'] = '';
        }

        if (!isset($this->data['filter']['checkAllClass'])) {
            $this->data['filter']['checkAllClass'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }

    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $conditions = array();
        if ($this->data['filter']['start_date1'] !== '') {
            $now_date=$this->data['filter']['start_date1'];
            $conditions['periodtime.course_date'] = $this->data['filter']['start_date1'];
            $this->data['use_date']=$this->data['filter']['start_date1'];
        } else {
            $now_date = date("Y-m-d");
            $this->data['filter']['start_date1'] = $now_date;
            $conditions['periodtime.course_date'] = $now_date;
            $this->data['use_date']=$now_date;
        }

        $attrs = array(
            'conditions' => $conditions,

        );
        $this->data['list'] = $this->card_record_model->getList($attrs);
       
        foreach ($this->data['list'] as &$row) {
            $row['link_detail'] = base_url("management/card_record/detail/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
            $row['link_export'] = base_url("management/card_record/export/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
            $row['link_import'] = base_url("management/card_record/import/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
            $row['link_add'] = base_url("management/card_record/add/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
            $row['link_new'] = base_url("management/card_record/new/{$row['seq_no']}?use_date={$now_date}");
            $row['link_patrol'] = base_url("management/card_record/patrol/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
        }
        $this->data['data3'] = $this->Vegetarian_management_model->getVegetarianSearch3($now_date);

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/card_record?" . $this->getQueryString(array(), array('page')));
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("management/card_record/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/card_record/list', $this->data);
    }
    public function detail($seq_no)
    {
        $conditions = array();
        $this->db->select('year,class_no,term');
        $this->db->where('seq_no',$seq_no);
        $query=$this->db->get('require');
        $result=$query->result_array();

        if ($seq_no != null) {
            //$conditions['seq_no'] = $seq_no;
            $conditions['online_app.year']=$result[0]['year'];
            $conditions['online_app.class_no']=$result[0]['class_no'];
            $conditions['online_app.term']=$result[0]['term'];
        }

        if ($this->data['filter']['start_date1'] !== '') {
            $conditions['room_use.use_date'] = $this->data['filter']['start_date1'];
        } else {
            $now_date = date("Y-m-d");
            $conditions['room_use.use_date'] = $now_date;
        }

        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['list'] = $this->card_record_model->test($attrs);
        $this->data['link_sort'] = base_url("management/card_record/sort/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/card_record/detail', $this->data);

    }
    public function sort($seq_no = null)
    {

        $conditions = array();
        $sort = array();
        //$use_date=$_REQUEST['start_date1'];
        //var_dump($use_date);
        if ($this->data['filter']['seq_no'] !== '') {
            $conditions['require.seq_no'] = $this->data['filter']['seq_no'];
            //$conditions['require.seq_no']=$seq_no;
        }

        if ($this->data['filter']['use_date'] !== '') {
            $conditions['room_use.use_date'] = $this->data['filter']['use_date'];
        }
        //$conditions['require.seq_no']=$use_date;

        $sort['sort'] = true;

        $attrs = array(
            'conditions' => $conditions,
            'sort' => $sort,
        );

        $this->data['list'] = $this->card_record_model->test($attrs);
        //echo"<pre>";
        //var_dump( $this->data['list']);
        $this->data['lose'] = array();
        for ($i = 0; $i < count($this->data['list']); $i++) {
            if ($this->data['list'][$i]['LOGIN_TIME'] == null) {
                $this->data['lose'][$i] = $this->data['list'][$i];
            }
        }
        //echo"<pre>";
        //var_dump( $this->data['lose']);
        $this->data['link_detail'] = base_url("management/card_record/detail/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->layout->view("management/card_record/sort", $this->data);

    }
    function new($seq_no) 
    {
        $conditions = array();
        $query = array();
        //$query=$this->input->post();
        $use_date=$this->input->get('use_date');
        //var_dump($use_date);
        if ($seq_no != null && $use_date!=null) {
            $this->db->select('class_no,year,term');
            $this->db->where('seq_no', $seq_no);
            //$this->db->Where('use_date',$use_date);
            $query = $this->db->get('require');
            $query = $query->result_array();
            $conditions['class_no'] = $query[0]['class_no'];
            $conditions['year'] = $query[0]['year'];
            $conditions['term'] = $query[0]['term'];

            $this->db->select('id');
            $this->db->where('class_no', $conditions['class_no']);
            $this->db->where('year', $conditions['year']);
            $this->db->where('term', $conditions['term']);
            $yn_sel=[1,3,4,8];
            $this->db->where_in('yn_sel',$yn_sel);
            $student_id=$this->db->get('online_app')->result_array();
            $phydisabled=$this->card_record_model->getPhyDisabled($student_id);
            //var_dump($phydisabled);

            $this->db->select('hand_people_num as hpn ,disabled_people_num as dpn');
            $this->db->where('class_no', $conditions['class_no']);
            $this->db->where('year', $conditions['year']);
            $this->db->where('term', $conditions['term']);
            $this->db->where('use_date',$use_date);
            $search = $this->db->get('card_record_people_num')->result_array();
            if (empty($search)) {
                $search[0]['hpn'] = 0;
                $search[0]['dpn'] = count($phydisabled);
            }
            $this->data['num'] = $search;
        }

        if ($query = $this->input->post()) {
            $conditions['hand_people_num'] = $query['hand_people_num'];
            $conditions['disabled_people_num'] = $query['disabled_people_num'];
            $conditions['enable'] = $query['enable'];
            $conditions['use_date']=$use_date;
            $this->db->where('class_no', $conditions['class_no']);
            $this->db->where('year', $conditions['year']);
            $this->db->where('term', $conditions['term']);
            $this->db->where('use_date',$use_date);
            $search = $this->db->get('card_record_people_num')->result_array();
           

            if (!empty($search)) {
                $this->db->where('class_no', $conditions['class_no']);
                $this->db->where('year', $conditions['year']);
                $this->db->where('term', $conditions['term']);
                $this->db->where('use_date',$use_date);

                $post = $this->db->update('card_record_people_num', $conditions);
            } else {
                $post = $this->db->insert('card_record_people_num', $conditions);
            }
            if ($post) {
                $this->setAlert(1, '資料新增成功');
                redirect("management/card_record/?{$_SERVER['QUERY_STRING']}", 'refresh');
            }
        }

        //$this->data['list'] = $this->card_record_model->getList($attrs);
        $this->data['link_refresh'] = base_url("management/card_record/new/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->data['link_index'] = base_url("management/card_record/");
        $this->data['link_new'] = base_url("management/card_record/new/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/card_record/new', $this->data);
    }
    public function add($seq_no = null)
    {
        if (!isset($_REQUEST['start_date1'])) {
            $use_date = date('Y-m-d');
        }
        $conditions = array();
        $query = $this->input->post();
        if ($seq_no != null) {
            $conditions['seq_no'] = $seq_no;
            $this->db->select('year,class_no,term');
            $this->db->where('seq_no', $seq_no);
            $result = $this->db->get('require');
            $result = $result->result_array();

        }

        if (!empty($query)) {
            /*$this->db->select('*');
            $this->db->where('gid',$query['gid']);
            $this->db->where('use_date',$query['use_date']);
            $temp=$this->db->get('card_log');
            $compar=$temp->result_array();
            if(count($compar)>=2){
                $this->setAlert(1, '補登失敗此會員已有紀錄');
                redirect('management/card_record/');
            }*/

            $this->db->select('id', 'year', 'class_no', 'term');
            $this->db->where('id', $query['gid']);
            $this->db->where('year', $result[0]['year']);
            $this->db->where('class_no', $result[0]['class_no']);
            $this->db->where('term', $result[0]['term']);
            $comparison = $this->db->get('online_app');
            $comparison = $comparison->result_array();
            $query['type'] = '補登';
            //var_dump($query);
            //die();
            if (!empty($comparison)) {
                if ($query['gid'] == null) {
                    $this->setAlert(1, '補登失敗身分證未填寫');
                    redirect("management/card_record/?{$_SERVER['QUERY_STRING']}");
                }
                if ($query['pass_time'] == null) {
                    $this->setAlert(1, '補登失敗刷卡時間未填寫');
                    redirect("management/card_record/?{$_SERVER['QUERY_STRING']}");
                }
                $this->db->trans_start();
                $this->db->insert('card_log', $query);
                $this->setAlert(1, '補登成功');
                $this->db->trans_complete();
                redirect("management/card_record/?{$_SERVER['QUERY_STRING']}");
            } else {
                $this->setAlert(1, '補登失敗此會員不在此門課');
                redirect("management/card_record/?{$_SERVER['QUERY_STRING']}");
            }
        }

        if ($this->data['filter']['start_date1'] !== '') {
            $conditions['room_use.use_date'] = $this->data['filter']['start_date1'];
        } else {
            $conditions['room_use.use_date'] = date('Y-m-d');
        }
        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['list'] = $this->card_record_model->test($attrs);
        $this->data['link_add'] = base_url("management/card_record/add/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->data['link_sort'] = base_url("management/card_record/sort/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/card_record/add', $this->data);

    }
    public function export($seq_no)
    {
        $conditions = array();
        if ($seq_no != null) {
            $conditions['seq_no'] = $seq_no;
            $year = $this->getClassYear($seq_no);
        }
        if ($this->data['filter']['start_date1'] !== '') {
            $conditions['room_use.use_date'] = $this->data['filter']['start_date1'];
        } else {
            $now_date = date("Y-m-d");
            $this->data['filter']['start_date1'] = $now_date;
            $conditions['room_use.use_date'] = $now_date;
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        $info = $this->card_record_model->test($attrs);

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=card_record.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $filename = 'card_record.csv';
        echo iconv("UTF-8", "BIG5", $year.'年' . $info[0]['class_name'] . '第' . $info[0]['term'] . '期' . "學員刷卡紀錄\r\n");
        echo iconv("UTF-8", "BIG5", "期別,");
        echo iconv("UTF-8", "BIG5", "組別,");
        echo iconv("UTF-8", "BIG5", "學號,");
        echo iconv("UTF-8", "BIG5", "服務單位,");
        echo iconv("UTF-8", "BIG5", "職稱,");
        echo iconv("UTF-8", "BIG5", "姓名,");
        echo iconv("UTF-8", "BIG5", "刷卡日期,");
        echo iconv("UTF-8", "BIG5", "簽到時間,");
        echo iconv("UTF-8", "BIG5", "簽退時間,");
        echo iconv("UTF-8", "BIG5", "時數(應/未),");
        echo iconv("UTF-8", "BIG5", "備註,\r\n");

        for ($i = 0; $i < count($info); $i++) {
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['term'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['group_no'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['st_no'] . "\",");
            $bureau = '';
            if ($info[$i]['ou_gov'] != null) {
                $bureau = $info[$i]['ou_gov'];
            } else {
                $bureau = $info[$i]['bureau_name'];
            }

            echo "\"" . iconv("UTF-8", "BIG5", $bureau . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['title'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5//IGNORE", $info[$i]['user_name'] . "\",");
            $date = substr($info[$i]['use_date'], 0, 10);
            $date = str_replace('-', ' ', $date);
            echo "\"" . iconv("UTF-8", "BIG5", $date . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['LOGIN_TIME'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['LOGOUT_TIME'] . "\",");
            if ($info[$i]['unstudyhours'] == null) {
                $info[$i]['unstudyhours'] = 0;
            }
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['SUM_HOURS']."|".$info[$i]['unstudyhours'] . "\",");
            //echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['unstudyhours'] . "\",");

            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['remark'] . "\"\r\n");
        }

    }

    public function import($seq_no)
    {
        $d=$this->input->get('start_date1');
        if ($this->data['filter']['start_date1'] !== '') {
            $date = $this->data['filter']['start_date1'];
        } else {
            $now_date = date("Y-m-d");
            $date = $now_date;
        }

        if ($seq_no != null) {
            $year = $this->getClassYear($seq_no);
        }
        //var_dump($date);
        //var_dump($d);

        //die();

        if (isset($_FILES['myfile']['name'])) {
            if (basename($_FILES['myfile']['name']) == 'card_record.csv') {
                //die();
                $uploaddir = DIR_UPLOAD_FILES;
                $uploadfile = $uploaddir . basename($_FILES['myfile']['name']);
                $uploadfile = iconv("utf-8", "big5", $uploadfile);
                if (move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile)) {
                    $fp = fopen($uploadfile, "r") or die("無法開啟");
                    $data = array();
                    $row = 0;
                    $success = 0;
                    $fail = 0;
                    while (!feof($fp)) {
                        $content = fgets($fp);
                        //$content = mb_convert_encoding($content, 'UTF-8', 'BIG5');
                        $fields = explode(",", $content);
                        
                        if ($row == '1' && count($fields) == 9 && !empty($fields[0]) && !empty($fields[1]) && !empty($fields[2]) && !empty($fields[3])
                            && !empty($fields[4]) && !empty($fields[5]) && !empty($fields[7]) && !empty($fields[8])) {

                            for ($i = 7; $i < 9; $i++) {
                                $data['class_no'] = trim($fields[1]);
                                $data['term'] = trim($fields[2]);
                                $data['gid'] = trim($fields[5]);
                                $data['year'] = $year;
                                $data['use_date'] = $date;
                                //$data['pass_time'] = trim($fields[$i]);
                                $data['pass_time'] = str_replace(":","",trim($fields[$i]));  //2021-07-29 修改匯入時間格式，排除冒號
                                
                                $cnt=$this->card_record_model->getCardLog($data);
                                if($cnt!=0){
                                    $this->setAlert(1,'刷卡紀錄時間重複! 請檢查刷到(退)時間');
                                    redirect(base_url('management/card_record'));
                                }


                                //if (!empty($data['gid']) && !empty($data['use_date']) && !empty($data['pass_time'])&&strlen(trim($fields[7]))==6&&strlen(trim($fields[8]))==6) {
                                if (!empty($data['gid']) && !empty($data['use_date']) && !empty($data['pass_time'])&&strlen(str_replace(":","",trim($fields[7])))==6&&strlen(str_replace(":","",trim($fields[8])))==6) {                           
                                    $saved_status = $this->card_record_model->InsertByImport($data);
                                    if ($saved_status) {
                                        $success++;
                                    } else {
                                        $fail++;
                                    }
                                }
                                
                            }
                        }
                        $row = 1;
                    }
                    
                    $this->setAlert(1, '資料匯入成功<br>' . '成功:' . $success . '筆<br>');
                    
                    redirect(base_url('management/card_record'));
                }
            }else{
                $this->setAlert(1, '檔名有誤 請檢查!');
                redirect(base_url('management/card_record'));
            }
        }
        //var_dump(basename($_FILES['myfile']['name']));
        $this->data['link_import'] = base_url("management/card_record/import/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        //var_dump($this->data['link_import']);

        $this->layout->view('management/card_record/import', $this->data);
    }
    public function getClassYear($seq_no)
    {

        $this->db->select('year');
        $this->db->where('seq_no', $seq_no);
        $query = $this->db->get('require');
        $query = $query->result_array();
        //var_dump($query[0]);
        //die();
        if (!empty($query)) {
            return $query[0]['year'];
        } else {
            return '';
        }
    }
    public function patrol($seq_no)
    {
        $this->db->select('*');
        $this->db->where('seq_no',$seq_no);
        $temp=$this->db->get('require');
        $this->data['course_info']=$temp->result_array();
        $classroom=$this->data['course_info'][0]['room_code'];
        $this->data['patrol_date_show'] = date('Y-m-d');
        $this->data['use_time'] = date('His');
        //$this->data['patrol_date'] = date('Y-m-d H:i:s');
        $query = $this->input->post();
        if(isset($query['patrol_date_show'])&&isset($query['patrol_time_show'])&&$query['mode']!='del'){
            if(strlen($query['patrol_time_show'])==6&&substr($query['patrol_time_show'],0,2)<24&&substr($query['patrol_time_show'],2,2)<60&&substr($query['patrol_time_show'],4,2)<60){
                $query['patrol_time_show']=substr($query['patrol_time_show'],0,2).":".substr($query['patrol_time_show'],2,2).":".substr($query['patrol_time_show'],4,2);
                $query['patrol_date']=$query['patrol_date_show']." ".$query['patrol_time_show'];
            }else{
                $this->setAlert('1','時間輸入錯誤');
                redirect("management/card_record/patrol/".$seq_no."?{$_SERVER['QUERY_STRING']}",'refresh');

            }
        }
       
        //var_dump($query);
        //die();
        if (isset($query['mode'])) {
            if ($query['mode'] == 'del') {
                $this->db->where('id', $query['item_id']);
                $this->db->delete('card_record_patrol');
                $this->setAlert('1', '刪除成功');
                redirect("management/card_record/patrol/".$seq_no."?{$_SERVER['QUERY_STRING']}",'refresh');
            }
        }

        if (!empty($query) && $query['mode'] != 'del') {
            if (!empty($query)) {
                if ($query['patrol_date_show'] == null) {
                    $this->setAlert(1, '新增失敗 請填寫日期');
                    redirect("management/card_record/patrol/".$seq_no."?{$_SERVER['QUERY_STRING']}",'refresh');
                }
                if ($query['patrol_time_show'] == null) {
                    $this->setAlert(1, '新增失敗 請填寫時間');
                    redirect("management/card_record/patrol/".$seq_no."?{$_SERVER['QUERY_STRING']}",'refresh');
                }
                if ($query['real_number'] == null) {
                    $this->setAlert(1, '新增失敗 請填寫人數');
                    redirect("management/card_record/patrol/".$seq_no."?{$_SERVER['QUERY_STRING']}",'refresh');
                }
                $ins=array(
                    'seq_no'=>addslashes($query['seq_no']),
                    'patrol_date'=>addslashes($query['patrol_date']),
                    'patrol_person'=>addslashes($query['patrol_person']),
                    'real_number'=>addslashes($query['real_number']),
                    'note'=>addslashes($query['note'])
                );
                $this->db->trans_start();
                $this->db->insert('card_record_patrol', $ins);
                $this->setAlert(1, '新增成功');
                $this->db->trans_complete();
                redirect("management/card_record/patrol/".$seq_no."?{$_SERVER['QUERY_STRING']}",'refresh');
            }
        }

        if ($this->data['filter']['start_date1'] !== '') {
            $conditions['room_use.use_date'] = $this->data['filter']['start_date1'];
        } else {
            $now_date = date("Y-m-d");
            $conditions['room_use.use_date'] = $now_date;
        }

        $this->db->select('*');
        $this->db->where('seq_no', $seq_no);
        $this->db->order_by('patrol_date desc');
        $query = $this->db->get('card_record_patrol');
        $this->data['list'] = $query->result_array();
       

        $this->data['seq_no'] = $seq_no;
        $this->data['name'] = $this->flags->user['name'];
        $this->data['link_refresh'] = base_url("management/card_record/patrol/" . $seq_no."?{$_SERVER['QUERY_STRING']}");

        $this->data['go_back'] = base_url("management/card_record/?{$_SERVER['QUERY_STRING']}");

        $this->data['save'] = base_url("management/card_record/patrol/" . $seq_no."?{$_SERVER['QUERY_STRING']}");
        //$this->data['save'] = base_url("management/card_record/patrol/{$seq_no}?{$_SERVER['QUERY_STRING']}");

        $this->data['management'] = base_url("management/card_record/detail/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->data['seat'] = base_url('management/print_table/roomSeat/' . $seq_no.'?type=6&classroom='.$classroom);
        $this->layout->view('management/card_record/patrol', $this->data);
    }

}
