<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Learn_table_one extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statistics_paper/Learn_table_one_model');
    }

    public function index()
    {
        $thisyear = date("Y")-1911;
        $year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $onegov = isset($_GET['onegov'])?$_GET['onegov']:"";
        
        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['sess_year'] = $year;
        $this->data['sess_onegov'] = $onegov;
        $bureau_id = $this->flags->user['bureau_id'];
        $account = $this->flags->user['username'];
        
        if($year == "" || $onegov == ""){ //沒有初始值不搜尋
            
            // $this->data['test'] = $this->Learn_time_table_model->GetTimeInterval($year);
            $this->layout->view('statistics_paper/learn_table_one/list',$this->data);
        }else{ //有傳參數需要搜尋資料
            $range = $this->Learn_table_one_model->GetTimeInterval($year);
            $this->data['test'] = $range;
            $this->data['excel'] = $this->Learn_table_one_model->getDownLoadExcel($year,$bureau_id,$account,$range,$onegov);
        }
        
        $this->data['link_refresh'] = base_url("statistics_paper/learn_table_one/");
        
    }

}
