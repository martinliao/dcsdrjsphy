<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lecture_tax_search extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pay/Lecture_tax_search_model');
    }

    public function index()
    {
        $this->data['address'] = $this->Lecture_tax_search_model->getAddressSearch();
        $this->data['rsAll'] = $this->Lecture_tax_search_model->getInitialSearch();

        $teacher = isset($_GET['teacher'])?$_GET['teacher']:"";
        $uniformid = isset($_GET['uniformid'])?$_GET['uniformid']:"";
        $start_date = isset($_GET['start_date'])?$_GET['start_date']:"";
        $end_date = isset($_GET['end_date'])?$_GET['end_date']:"";
        $year = isset($_GET['year'])?$_GET['year']:"";
        $teacherid = isset($_GET['teacherid'])?$_GET['teacherid']:"";
        $remark = isset($_GET['remark'])?$_GET['remark']:"";

        $this->data['sess_teacher'] = $teacher;
        $this->data['sess_uniformid'] = $uniformid;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_year'] = $year;
        $this->data['link_refresh'] = base_url("pay/lecture_tax_search/");
        $this->data['datas'] = array("1"=>array(),"2"=>array(),"3"=>array(),"4"=>array());
        if($year != ""){
            $this->data['dayOfWeek'] = $this->Lecture_tax_search_model->getDayOfWeek();
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $this->data['datas'] = $this->Lecture_tax_search_model->getLectureTaxSearch($teacher, $uniformid, $start_date, $end_date, $year, $this->data['rsAll']);
                $this->Lecture_tax_search_model->csvexport(date("Y-m-d"),"","",$this->data['datas'],$this->data['dayOfWeek']);
            }
            else if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 2) {
                $this->Lecture_tax_search_model->saveRemark($year, $teacherid, $remark);
                $this->data['datas'] = $this->Lecture_tax_search_model->getLectureTaxSearch($teacher, $uniformid, $start_date, $end_date, $year, $this->data['rsAll']);
                $this->layout->view('pay/lecture_tax_search/list',$this->data);
            }
            else{
                $this->data['datas'] = $this->Lecture_tax_search_model->getLectureTaxSearch($teacher, $uniformid, $start_date, $end_date, $year, $this->data['rsAll']);
                $this->layout->view('pay/lecture_tax_search/list',$this->data);
            }
        }
        else{
            $this->layout->view('pay/lecture_tax_search/list',$this->data);
        }
        
    }

}
