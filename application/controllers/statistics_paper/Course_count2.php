<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_count2 extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('statistics_paper/course_count_model');
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
    }

    public function index()
    {

        //$ttt = $this->course_count_model->getReport4(91,110,'AA0561',1);
        //var_dump($ttt);die();
        if(isset($_GET['year'])){
            $this->data['b_csv'] = 1;
        }
        $thisyear = date("Y")-1911;
        //$year = isset($_GET['year'])?$_GET['year']:$thisyear;
        $year = isset($_GET['year'])?addslashes($_GET['year']):"";
        $season = isset($_GET['season'])?addslashes($_GET['season']):"";
        $series = isset($_GET['series'])?addslashes($_GET['series']):"";
        $type = isset($_GET['type'])?addslashes($_GET['type']):"";
        $startMonth = isset($_GET['startMonth'])?addslashes($_GET['startMonth']):"";
        $endMonth = isset($_GET['endMonth'])?addslashes($_GET['endMonth']):"";
        $start_date = isset($_GET['start_date'])?addslashes($_GET['start_date']):"";
        $end_date = isset($_GET['end_date'])?addslashes($_GET['end_date']):"";
        $query_class_name = isset($_GET['squery_class_name'])?addslashes($_GET['squery_class_name']):"";
        $pbox1 = isset($_GET['pbox1'])?addslashes($_GET['pbox1']):"0";
        $pbox2 = isset($_GET['pbox2'])?addslashes($_GET['pbox2']):"0";
        $pbox3 = isset($_GET['pbox3'])?addslashes($_GET['pbox3']):"0";
        $pbox4 = isset($_GET['pbox4'])?addslashes($_GET['pbox4']):"0";
        $pbox5 = isset($_GET['pbox5'])?addslashes($_GET['pbox5']):"0";
        $pbox6 = isset($_GET['pbox6'])?addslashes($_GET['pbox6']):"0";
        $cbox1 = isset($_GET['cbox1'])?addslashes($_GET['cbox1']):"0";
        $cbox2 = isset($_GET['cbox2'])?addslashes($_GET['cbox2']):"0";
        $cbox3 = isset($_GET['cbox3'])?addslashes($_GET['cbox3']):"0";
        $tbox1 = isset($_GET['tbox1'])?addslashes($_GET['tbox1']):"0";
        $tbox2 = isset($_GET['tbox2'])?addslashes($_GET['tbox2']):"0";
        $tbox3 = isset($_GET['tbox3'])?addslashes($_GET['tbox3']):"0";
        $tbox4 = isset($_GET['tbox4'])?addslashes($_GET['tbox4']):"0";
        $tbox5 = isset($_GET['tbox5'])?addslashes($_GET['tbox5']):"0";
        $tbox6 = isset($_GET['tbox6'])?addslashes($_GET['tbox6']):"0";
        $tbox7 = isset($_GET['tbox7'])?addslashes($_GET['tbox7']):"0";
        $tcbox1 = isset($_GET['tcbox1'])?addslashes($_GET['tcbox1']):"0";
        $tcbox2 = isset($_GET['tcbox2'])?addslashes($_GET['tcbox2']):"0";  
        $tcbox3 = isset($_GET['tcbox3'])?addslashes($_GET['tcbox3']):"0";    
        $search_ok = isset($_GET['search_ok'])?addslashes($_GET['search_ok']):"0";         
        $ssd = $start_date;
        $sed = $end_date;

        if($type == 1 || $type == 2  ){
            $dateRange = $this->course_count_model->getDataRange($year,$type,$season,$startMonth,$endMonth);
            $ssd =$dateRange[0]; 
            $sed =$dateRange[1]; 
        }
        else if($type == 0){
            if($year != ""){
                $dateRange = $this->course_count_model->getOneYear($year);
                $ssd =$dateRange[0]; 
                $sed =$dateRange[1]; 
            }
        }


        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['sess_year'] = $year;
        $this->data['sess_season'] = $season;
        $this->data['sess_series'] = $series;
        $this->data['sess_type'] = $type;
        $this->data['sess_startMonth'] = $startMonth;
        $this->data['sess_endMonth'] = $endMonth;
        $this->data['sess_start_date'] = $start_date;
        $this->data['sess_end_date'] = $end_date;
        $this->data['sess_query_class_name'] = $query_class_name;
        $this->data['sess_pbox1'] = $pbox1;
        $this->data['sess_pbox2'] = $pbox2;
        $this->data['sess_pbox3'] = $pbox3;
        $this->data['sess_pbox4'] = $pbox4;
        $this->data['sess_pbox5'] = $pbox5;
        $this->data['sess_pbox6'] = $pbox6;
        $this->data['sess_cbox1'] = $cbox1;
        $this->data['sess_cbox2'] = $cbox2;
        $this->data['sess_cbox3'] = $cbox3;
        $this->data['sess_tbox1'] = $tbox1;
        $this->data['sess_tbox2'] = $tbox2;
        $this->data['sess_tbox3'] = $tbox3;
        $this->data['sess_tbox4'] = $tbox4;
        $this->data['sess_tbox5'] = $tbox5;
        $this->data['sess_tbox6'] = $tbox6;
        $this->data['sess_tbox7'] = $tbox7;
        $this->data['sess_tcbox1'] = $tcbox1;
        $this->data['sess_tcbox2'] = $tcbox2;
        $this->data['sess_tcbox3'] = $tcbox3;
        $this->data['ssearch_ok'] = $search_ok;
        $this->data['link_refresh'] = base_url("statistics_paper/course_count2/");
        $this->data['datas'] = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array(
            'conditions' => $conditions,
        );

        if($year != "") {
            $this->data['datas'] = $this->course_count_model->getCourseCountData2Count($year,$ssd,$sed,$type,$series,$query_class_name);
        }
        else {
            $this->data['datas'] =array();
        }

        if(isset($this->data['datas'])){
            $this->data['filter']['total'] = $total = count($this->data['datas']);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
            //echo $total ;die();
        }else{
            $this->data['filter']['total'] = $total = 0;
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        }
        if($total > 0) {
            //echo $offset ;die();
            //$offset =  10;
            $this->data['datas'] = $this->course_count_model->getCourseCountData2($year,$ssd,$sed,$type,$series,$rows, $offset,$query_class_name);
        }

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );


        $this->load->library('pagination');
        $config['base_url'] = base_url("statistics_paper/course_count2?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        if($year != ""){
            
            $this->data['dayOfWeek'] = $this->course_count_model->getDayOfWeek();
            if(isset($_GET['iscsv'])  && $_GET['iscsv'] == 1  ){
                $select_cl = new StdClass();
                $select_cl->sess_pbox1 = $pbox1;
                $select_cl->sess_pbox2 = $pbox2;
                $select_cl->sess_pbox3 = $pbox3;
                $select_cl->sess_pbox4 = $pbox4;
                $select_cl->sess_pbox5 = $pbox5;
                $select_cl->sess_pbox6 = $pbox6;
                $select_cl->sess_cbox1 = $cbox1;
                $select_cl->sess_cbox2 = $cbox2;
                $select_cl->sess_cbox3 = $cbox3;
                $select_cl->sess_tbox1 = $tbox1;
                $select_cl->sess_tbox2 = $tbox2;
                $select_cl->sess_tbox3 = $tbox3;
                $select_cl->sess_tbox4 = $tbox4;
                $select_cl->sess_tbox5 = $tbox5;
                $select_cl->sess_tbox6 = $tbox6;
                $select_cl->sess_tbox7 = $tbox7;
                $select_cl->sess_tcbox1 = $tcbox1;
                $select_cl->sess_tcbox2 = $tcbox2;
                $select_cl->sess_tcbox3 = $tcbox3;
                //exec('php /data/html/base/ttt.php', $out);
                //exec('curl http://60.251.49.168:7021/base/admin/statistics_paper/course_count2?year=110&type=0&series=A&season=&startMonth=&endMonth=&start_date=&end_date=&iscsv=1&rows=30&squery_class_name=&pbox1=1&pbox2=1&pbox3=1&pbox4=1&pbox5=1&pbox6=1&cbox1=1&cbox2=1&cbox3=1&tbox1=1&tbox2=1&tbox3=1&tbox4=1&tbox5=1&tbox6=1&tbox7=1&tcbox1=1&tcbox2=1&tcbox3=1&search_ok=1', $out);
                //var_dump($out);die();

                $this->data['datas'] = $this->course_count_model->getCourseCountData2($year,$ssd,$sed,$type,$series,"","",$query_class_name);
                //echo "<PRE>"; var_dump($this->data['datas']);die();
                $this->course_count_model->csvexport2(date("Y-m-d"),$ssd,$sed,$series,$this->data['datas'],$this->data['dayOfWeek'],$select_cl);
            }
            else{
                $this->layout->view('statistics_paper/course_count2/list',$this->data);
            }
        }
        else{
            $this->layout->view('statistics_paper/course_count2/list',$this->data);
        }
        
    }

}
