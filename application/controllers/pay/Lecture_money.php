<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lecture_money extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Lecture_money_model');
    }

    public function index()
    {
        $start = isset($_GET['nstart'])?addslashes($_GET['nstart']):"";
        $end = empty($this->input->get('nend')) ? "" : addslashes($this->input->get('nend'));
        $act = isset($_GET['act'])?addslashes($_GET['act']):"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);

        $this->data['start']=$start;
        $this->data['end']=$end;

        
        if($act!=""){
            if($act=='csv'){
                $this->data['datas'] = $this->Lecture_money_model->exportLectureMoneyData($start,$end);
            }
        }else{
            $this->data['link_refresh'] = base_url("pay/lecture_money/");
            $this->layout->view('pay/lecture_money/list',$this->data);
        }
    }

}
