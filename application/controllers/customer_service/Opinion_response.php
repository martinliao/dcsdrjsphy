<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Opinion_response extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('customer_service/opinion_response_model');
        $this->load->model('planning/set_startdate_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_sort'])) {
            $this->data['filter']['query_sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['start_month'])) {
            $this->data['filter']['start_month'] = date('m');
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['checkAll'])) {
            $this->data['filter']['checkAll'] = '';
        }

    }

    public function index()
    {
        $this->data['page_name'] = 'list';
       
        $this->data['choices']['start_month']=['01'=>1,'02'=>2,'03'=>3,'04'=>4,'05'=>5,'06'=>6,'07'=>7,'08'=>8,'09'=>9,'10'=>10,'11'=>11,'12'=>12];
   
        $conditions=array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }
        $conditions['type']='B';
        $conditions['class_status']=[2,3];

        if ($this->data['filter']['checkAll'] !== 'on' ) {
            $conditions['worker']=$this->flags->user['idno'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $conditions['class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['start_month'] !== '' ) {

            $year = $conditions['year']+1911 ;
            $start_date =$year.'-'.$this->data['filter']['start_month'].'-01';
            $end_date   = $year.'-'.date('m-d', strtotime("$start_date +1 month -1 day"));

            $conditions['start_date'] = $start_date;
            $conditions['end_date'] = $end_date;
        }
       
        
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
       
        
        $this->data['filter']['total'] = $total = $this->opinion_response_model->getListCount($conditions);

        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $data = array(
            'rows' => $rows,
            'offset' => $offset,
            'conditions'=>$conditions,
        );
        
        
        $this->data['list'] = $this->opinion_response_model->getList($data);
        foreach ($this->data['list'] as & $row) {
            $row['detail']=base_url("customer_service/opinion_response/detail/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
        }
        //var_dump($this->data['list'][0]['detail']);
        
        $this->load->library('pagination');
        $config['base_url'] = base_url("customer_service/opinion_response?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        
        $this->pagination->initialize($config);
        //$this->data['link_get_second_category'] = base_url("planning/season_schedule/getSecondCategory");
        //$this->data['link_confirm']='';
        $this->data['link_refresh'] = base_url("customer_service/opinion_response/");
        
        
        $this->layout->view('customer_service/opinion_response/list', $this->data);
    }

    public function edit($seq_no)
    {
        $post=$this->input->post();
        if(isset($post['year'])&&isset($post['term'])&&isset($post['class_no'])&&isset($post['item'])&&$post['mode']=='save'){
            $result=$this->opinion_response_model->saveSuggest($post);
            if($result){
                $this->setAlert(1,'儲存成功!');
                redirect("customer_service/opinion_response/detail/{$seq_no}");
            }
        }
        $this->data['item']=$this->input->get('item');
        $this->data['list']=$this->opinion_response_model->getDetailItem($seq_no);
        $this->data['save_19c']= base_url("customer_service/opinion_response/edit/{$seq_no}?item={$this->data['item']}");
        //$this->data['save']= base_url("customer_service/opinion_response/edit/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("customer_service/opinion_response/edit/{$seq_no}?item={$this->data['item']}");
        $this->layout->view('customer_service/opinion_response/edit',$this->data);
    }
    public function detail($seq_no)
    {
        $this->data['group_id']=$this->flags->user['group_id'];
        $this->data['filter']=$this->input->get();
        /*$query_class_name='';
        $start_month='';
        $checkAll='';
        $query_class_no='';
        $year='';*/
        //$query_year='';
        if(isset($this->data['filter']['query_class_name'])){
            $query_class_name=$this->data['filter']['query_class_name'];
            $query_class_name=urlencode($query_class_name);
        }
        if(isset($this->data['filter']['start_month'])){
            $start_month=$this->data['filter']['start_month'];
        }

        if(isset($this->data['filter']['checkAll'])){
            $checkAll=$this->data['filter']['checkAll'];
        }
        if(isset($this->data['filter']['query_class_no'])){
            $query_class_no=$this->data['filter']['query_class_no'];
        }
        if(isset($this->data['filter']['query_year'])){
            $query_year=$this->data['filter']['query_year'];
        }
        if(isset($this->data['filter']['rows'])){
            $rows=$this->data['filter']['rows'];
            //var_dump($rows);
        }

        
        
        //$url='query_class_name='.$query_class_name.'&'.$start_month.'&'.$checkAll.'&'.$query_class_no.'&'.$query_year;
       
        //var_dump($query_class_name);
        $this->data['list']=$this->opinion_response_model->getDetail($seq_no);
        foreach ($this->data['list'] as & $row) {
            $row['edit']=base_url("customer_service/opinion_response/edit/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        }
        $get=$this->input->get();
        //var_dump($get);
        //die();
        if(isset($get['year'])&&isset($get['class_no'])&&isset($get['term'])&&isset($get['mode'])){

            $result=$this->opinion_response_model->controlAnnouce($get);

            if($result){
                $this->setAlert(1,'操作成功!');
                redirect("customer_service/opinion_response/detail/{$seq_no}?query_year={$get['year']}&query_class_no={$get['class_no']}",'refresh');
                
            }else{
                $this->setAlert(1,'操作失敗! 無資料可操作');
                redirect("customer_service/opinion_response/detail/{$seq_no}?query_year={$get['year']}&query_class_no={$get['class_no']}",'refresh');
            }
        }

        $this->data['annouce'] = base_url("customer_service/opinion_response/detail/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->data['link_refresh'] = base_url("customer_service/opinion_response/detail/{$seq_no}?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('customer_service/opinion_response/detail',$this->data);
    }

    public function ajax()
    {
        $post = $this->input->post();
        $result=$this->opinion_response_model->courseSuggest($post);
        //var_dump($post);

        if($result){
           echo 'OK'; 
        }else{
           echo 'no';
        }
        
    }


}
