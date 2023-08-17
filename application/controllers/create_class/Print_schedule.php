<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Print_schedule extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
        }
        $this->load->model('create_class/print_schedule_model');
        $this->load->model('create_class/progress_model');
        $this->load->model('require_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['name'])) {
            $this->data['filter']['name'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['tmp_seq'])) {
            $this->data['filter']['tmp_seq'] = '';
        }
	}

	public function index()
	{
        $conditions = array();
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }
       
        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        $this->data['choices']['item_id'] = $this->getTemplate();
        //$this->data['choices']['item_id'][''] = '請選擇';
        

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->print_schedule_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
       
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['list'] = $this->print_schedule_model->getList($attrs);
        
        foreach($this->data['list'] as & $row){
            $row['link_detail']=base_url("create_class/print_schedule/print/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
            $row['link_note']=base_url("create_class/print_schedule/note/{$row['seq_no']}");
        }

      
		$this->load->library('pagination');
        $config['base_url'] = base_url("create_class/print_schedule?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
       
        $this->data['link_refresh'] = base_url("create_class/print_schedule");
		$this->layout->view('create_class/print_schedule/list', $this->data);
	}
    public function print($seq_no=null)
    {   
        
        if(!empty($_REQUEST["tmp_seq"])){
            $this->data['tmp_seq']=$_REQUEST["tmp_seq"];
        }else{
            $this->data['tmp_seq']=0;
        }
        
        $this->data['content']="";
        if($seq_no!=null){
            $this->db->select('year,class_no,term');
            $this->db->where('seq_no',$seq_no);
            $query=$this->db->get('require');
            $result=$query->result_array();

            $this->data['onlineCourse']=$this->print_schedule_model->getOnlineCourse($result); 
            $this->data['realCourse']=$this->print_schedule_model->getRealCourse($result); 
            $this->data['data']=$this->print_schedule_model->getResearcher($result);
            $this->data['roomCount']=$this->print_schedule_model->getRoomCount($result);
            $this->data['roomName']=$this->print_schedule_model->getRoomName($result);
            //$this->data['sel_number']=$this->print_schedule_model->getSelNumber($result);
            if(preg_match("/^10.254.250.198$/", $_SERVER["REMOTE_ADDR"])) {
                // var_dump($this->data['realCourse'][1]);exit();
            }
            
            if(empty($this->data['content'])){

                $content=$this->print_schedule_model->getMailLog($result);

                for($j=0;$j<count($content);$j++){
                    $temp = htmlspecialchars_decode($content[$j]['content'], ENT_HTML5|ENT_QUOTES);
                    $temp = stripslashes($temp);

	                $this->data['content'] = str_replace("本信件為系統自動發送，請不要直接回覆", "", $temp);
                }
                
            }else{
                $this->data['content'] = htmlspecialchars_decode($this->data['content'], ENT_HTML5|ENT_QUOTES);
                //die();
            }
            
        }
        //var_dump($this->data['realCourse']);
        $this->layout->view('create_class/print_schedule/detail', $this->data);
    }
    public function getTemplate()
    {
        $data=array();
        $this->db->select('tmp_seq,title');
        $this->db->where('item_id','01');
        $this->db->where('is_open','1');
        $this->db->order_by('tmp_seq');
        $query=$this->db->get('template');
        $result=$query->result_array();
    
        /*foreach ($result as $key) {
            $data[$key['item_id']] = $key['title'];
        }*/
        return $result;
        
    }
    public function note($seq_no=null)
    {   
        $this->db->select('note');
        $this->db->where('seq_no',$seq_no);
        $query=$this->db->get('require');
        $result=$query->result_array();
        $this->data['note']=$result;
        //var_dump($this->data['note'][0]['note']);
        $this->layout->view('create_class/print_schedule/note', $this->data);
    }
    public function save()
    {
        $query=$this->input->post();
        $condition=array();
        $condition=['class_content'=>addslashes($query['FCKeditor1'])];
        if(!empty($query)){
            $this->db->where('year', $query['year']);
            $this->db->where('class_no', $query['class_no']);
            $this->db->where('term', $query['term']);
            $this->db->update('require',$condition);
            $this->setAlert(1, '資料修改成功');
            redirect('create_class/print_schedule/');            
        }else{
            $this->setAlert(1, '資料修改失敗');
            redirect('create_class/print_schedule/','refresh');            
        }
        //die();
    }

    /*
        合併列印
    */
    public function mutiPrint()
    {   
        $seq_nos = $this->getFilterData('seq_nos', []);
        $schedule = array();

        foreach($seq_nos as $seq_no){
            $schedule[] = $this->getSchedule($seq_no);
        }
        

        $this->data['schedules'] = $schedule;
        // dd($this->data['schedules'], false);
        // dd($this->data['schedules']);
        $this->load->view('create_class/print_schedule/mutiPrint', $this->data);
    }

    public function mutiPrint2()   //20210617 線上簽核使用的訓練計畫
    {   
        $seq_nos = $this->getFilterData('seq_nos', []);
        $schedule = array();

        foreach($seq_nos as $seq_no){
            $schedule[] = $this->getSchedule($seq_no);
        }
        
        $this->data['schedules'] = $schedule;
        // dd($this->data['schedules'], false);
        // dd($this->data['schedules']);
        $this->load->view('create_class/print_schedule/mutiPrint2', $this->data);
    }    

    private function getSchedule($seq_no)
    {
        $phy_schedule = $this->progress_model->getPhySchedule($seq_no);
        $require = $this->require_model->find($seq_no);
        $online_schedule = $this->progress_model->getOnlineSchedule($seq_no);
        //var_dump($seq_no);
        
        //die();
        // 一個課程會有多的講座 將資料整理成一個
        $tmp_schedule = [];
        foreach($phy_schedule as $schedule){
            $key = $schedule->use_date.'-'.$schedule->use_period;
            if (empty($schedule->teacher_name)){
                $schedule->teacher_name = $schedule->name;
                //$schedule->description_array=$schedule->description;
            }

            if (empty($tmp_schedule[$key])){
                $tmp_schedule[$key] = $schedule;
                $tmp_schedule[$key]->teacher[] = $schedule->name;
            }else{
                if (empty($tmp_schedule[$key]->teacher)) $tmp_schedule[$key]->teacher = [];
                //if (array_search($schedule->teacher_name, $tmp_schedule[$key]->teacher) === false){ //2021-06-30 修正"老師會重複"問題
                if (array_search($schedule->teacher_name, $tmp_schedule[$key]->teacher) === false && array_search($schedule->name, $tmp_schedule[$key]->teacher) === false){    
                 
                    $tmp_schedule[$key]->teacher[] = $schedule->name;

                    //$tmp_schedule[$key]->teacher[] = $schedule->teacher_name;
                }             
            }
        }
        //var_dump($tmp_schedule);
        //die();
        
        
       
        //var_dump($tmp_schedule['2019-10-02-00']->teacher[0]);
        //die();
        $phy_schedule = $tmp_schedule;
        $test=array();
        $i=0;
        foreach($phy_schedule as $temp){
            $test[$i]=$temp;
            $i++;
        }
        $phy_schedule=$test;
/*        //2021-06-30 修正"合併BUG"問題
        $delete_index=[];
        for($i=0;$i<count($phy_schedule);$i++){
            for($j=0;$j<count($phy_schedule);$j++){
                if(isset($phy_schedule[$j])&&isset($phy_schedule[$i])){
                    if($phy_schedule[$i]->teacher==$phy_schedule[$j]->teacher && $phy_schedule[$i]->description==$phy_schedule[$j]->description && $phy_schedule[$i]->use_date==$phy_schedule[$j]->use_date&&$i!=$j&&!in_array($i,$delete_index)&&!in_array($j,$delete_index)){
                        $phy_schedule[$i]->to_time=$phy_schedule[$j]->to_time;
                        //unset($phy_schedule[$j]);
                        array_push($delete_index,$j);
                    }
                }
            }
        }
        for($m=0;$m<count($delete_index);$m++){
            unset($phy_schedule[$delete_index[$m]]);
        }
        $phy_schedule=array_values($phy_schedule);
*/        //
        //var_dump($phy_schedule);
        // dd($phy_schedule);
        $muti_room = false;
        $room_name = "";
        foreach ($phy_schedule as $schedule){
            if ($room_name == ""){
                $room_name = $schedule->room_name;
            }else if ($room_name != $schedule->room_name){
                $muti_room = true;
                break;
            }
        }
        
        $data = [
            'muti_room' => $muti_room,
            'room_name' => $room_name,
            'phy' => $phy_schedule,
            'require' => $require,
            'online' => $online_schedule
        ];

        
        //var_dump($data['require']->search_count);
        return $data;
    }
}
