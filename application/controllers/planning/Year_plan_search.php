<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Year_plan_search extends MY_Controller
{
    public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
        $this->load->model('planning/year_plan_search_model');
        $this->load->model('planning/course_introduct_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
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
        if (!isset($this->data['filter']['query_season'])) {
            $this->data['filter']['query_season'] = '';
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = '';
        }
        if (!isset($this->data['filter']['query_month_end'])) {
            $this->data['filter']['query_month_end'] = '';
        }
        if (!isset($this->data['filter']['query_start_date'])) {
            $this->data['filter']['query_start_date'] = '';
        }
        if (!isset($this->data['filter']['query_end_date'])) {
            $this->data['filter']['query_end_date'] = '';
        }
        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
        if (!isset($this->data['filter']['query_class_status'])) {
            $this->data['filter']['query_class_status'] = '';
        }
        
	}

    public function index()
    {   
        $this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $this->data['choices']['query_class_status']=['1'=>'草案','2'=>'確定計畫','3'=>'新增計畫'];
        
        $this->data['link_get_second_category'] = base_url("planning/year_plan_search/getSecondCategory");
        $this->data['link_refresh'] = base_url("planning/year_plan_search/");
        $this->data['link_detail'] = base_url("planning/year_plan_search/detail");
        $this->data['link_export'] = base_url("planning/year_plan_search/export");

        
		$this->layout->view('planning/year_plan_search/list', $this->data);

    }

    public function detail()
    {

        $this->data['page_name'] = 'detail';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        
        $conditions = array();
        $query=$this->input->post();
        
        if ($query['query_year'] !== '' ) {
            $conditions['require.year'] = $query['query_year'];
        }
        if ($query['query_class_status'] !== '' ) {
            $conditions['class_status'] = $query['query_class_status'];
        }
        
        if ($query['query_season'] !== '' ) {
            $conditions['reason'] = $query['query_season'];
        }

        if ($query['query_month_start'] !== '' ) {
            // $conditions['start_date1 >='] = ($query['query_year']+1911).'-'.$query['query_month_start'].'-01';
            $first_day = ($query['query_year']+1911).'-'.$query['query_month_start'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions["((start_date1 between '$first_day' and '$last_day') or (end_date1 between '$first_day' and '$last_day'))"] = null;
            
        }
        if ($query['query_type'] !== '' ) {
            $conditions['require.type'] = $query['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($query['query_type']);
        }else{
            echo "<script>alert(\"請選擇系列別\");</script>";
            redirect('planning/year_plan_search/','refresh');   
        }

        if ($query['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $query['query_second'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($query['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $query['query_class_name'];
        }
        //var_dump($attrs);
     
    
        $this->data['list'] = $this->year_plan_search_model->getList($attrs);
        $this->load->library('pagination');
        $this->data['link_get_second_category'] = base_url("planning/year_plan_search/getSecondCategory");
        $this->data['link_refresh'] = base_url("planning/year_plan_search/");
        
		$this->layout->view('planning/year_plan_search/detail', $this->data);
    }

    public function export()
    {

        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        
        $conditions = array();
        $query=$this->input->post();

       
        
        if ($query['query_year'] !== '' ) {
            $conditions['require.year'] = addslashes($query['query_year']);
        }

       
        if ($query['query_season'] !== '' ) {
            $conditions['reason'] = addslashes($query['query_season']);
        }

        if ($query['query_month_start'] !== '' ) {
            // $conditions['start_date1 >='] = ($query['query_year']+1911).'-'.$query['query_month_start'].'-01';
            $first_day = ($query['query_year']+1911).'-'.$query['query_month_start'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));
            $conditions["((start_date1 between '".addslashes($first_day)."' and '".addslashes($last_day)."') or (end_date1 between '".addslashes($first_day)."' and '".addslashes($last_day)."'))"] = null;
            
        }
        if ($query['query_type'] !== '' ) {
            $conditions['require.type'] = addslashes($query['query_type']);
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory(addslashes($query['query_type']));
        }else{
            echo "<script>alert(\"請選擇系列別\");</script>";
            redirect('planning/year_plan_search/','refresh');   
        }
        if ($query['query_second'] !== '' ) {
            $conditions['beaurau_id'] = addslashes($query['query_second']);
        }
        if ($query['query_class_status'] !== '' ) {
            $conditions['class_status'] = addslashes($query['query_class_status']);
        }


        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        
		$attrs = array(
            'conditions' => $conditions,
        );
        
        if ($query['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = addslashes($query['query_class_name']);
        }

        $this->data['filter']['total'] = $total = $this->year_plan_search_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($query['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = addslashes($query['query_class_name']);
        }
    
        $info = $this->year_plan_search_model->getList($attrs);

        if ($query['query_type']=='A') {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=plan.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $filename = 'plan.csv';
            echo iconv("UTF-8", "BIG5", "年度訓練計劃(行政系列)\r\n");
            echo iconv("UTF-8", "BIG5", "序號,");
            echo iconv("UTF-8", "BIG5", "次類別,");
            echo iconv("UTF-8", "BIG5", "所屬局處名稱,");
            echo iconv("UTF-8", "BIG5", "班期名稱,");
            echo iconv("UTF-8", "BIG5", "研習對象,");
            echo iconv("UTF-8", "BIG5", "初始期數,");
            echo iconv("UTF-8", "BIG5", "實際期數,");
            echo iconv("UTF-8", "BIG5", "每期人數,");
            echo iconv("UTF-8", "BIG5", "每期時數,");
            echo iconv("UTF-8", "BIG5", "人數合計,");
            echo iconv("UTF-8", "BIG5", "時數合計,");
            echo iconv("UTF-8", "BIG5", "權重,");
            echo iconv("UTF-8", "BIG5", "權重後時數,");
            echo iconv("UTF-8", "BIG5", "課程預定日期,");
            echo iconv("UTF-8", "BIG5", "天數,");
            echo iconv("UTF-8", "BIG5", "教室,");
            echo iconv("UTF-8", "BIG5", "環教班期,");
            echo iconv("UTF-8", "BIG5", "政策行銷班期,");
            echo iconv("UTF-8", "BIG5", "重大政策,");
            echo iconv("UTF-8", "BIG5", "開放退休人員選課,");
            echo iconv("UTF-8", "BIG5", "上課地點非公訓處,");
            echo iconv("UTF-8", "BIG5", "無須支應講座鐘點費,");
            echo iconv("UTF-8", "BIG5", "承辦人,");
            // echo iconv("UTF-8", "BIG5", "人數合計,");
            // echo iconv("UTF-8", "BIG5", "時數合計,");
            echo iconv("UTF-8", "BIG5", "取消開班,\r\n");
            $sum_people=0;
            $sum_time=0;
            $z=1;
            for ($i=0;$i<count($info);$i++) {
                
                echo "\"".iconv("UTF-8", "BIG5", $z."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['second_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['dev_type_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['class_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['respondant']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['base_term']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['no_persons']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['range']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']*$info[$i]['no_persons']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']*$info[$i]['range']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['weights']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['weights']*$info[$i]['range']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['each_term_date']."\",");

                $time1=$info[$i]['start_date1']; $time2=$info[$i]['end_date1']; 
                $diff=(strtotime($time2) - strtotime($time1))/ (60*60*24);
                echo "\"".iconv("UTF-8", "BIG5", $diff."\",");

                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['room_code']."\",");
                if($info[$i]['env_class'] == 'Y'){
                    echo "\"".iconv("UTF-8","BIG5","☆\",");
                  } else {
                    echo "\"".iconv("UTF-8","BIG5","\",");
                  }
                if($info[$i]['policy_class'] == 'Y'){
                    echo "\"".iconv("UTF-8","BIG5","☆\",");
                  } else {
                    echo "\"".iconv("UTF-8","BIG5","\",");
                  }

                $map_list='';
                if($info[$i]['map1'] == '1'){
                    $map_list = 'A營造永續環境 ';
                } 
                if($info[$i]['map2'] == '1'){
                    $map_list = 'B健全都市發展 ';
                }
                if($info[$i]['map3'] == '1') {
                    $map_list = 'C發展多元文化 ';
                }
                if($info[$i]['map4'] == '1') {
                    $map_list = 'D優化產業勞動 ';
                }
                if($info[$i]['map5'] == '1') {
                    $map_list = 'E強化社會支持 ';
                }
                if($info[$i]['map6'] == '1') {
                    $map_list = 'F打造優質教育 ';
                }
                if($info[$i]['map7'] == '1') {
                    $map_list = 'G精進健康安全 ';
                }
                if($info[$i]['map8'] == '1') {
                    $map_list = 'H精實良善治理 ';
                }
                if($info[$i]['map9'] == '1') {
                    $map_list = '樂活宜居(45項) ';
                }
                if($info[$i]['map10'] == '1') {
                    $map_list = '友善共融(31項) ';
                }
                if($info[$i]['map11'] == '1') {
                    $map_list = '創新活力(37項) ';
                }
                echo "\"".iconv("UTF-8", "BIG5",$map_list."\",");

                $special = '';
                $place='';
                if($info[$i]['not_hourfee'] == 'Y'){
                    $special = '☆';
                }
                if($info[$i]['not_location'] == 'Y') {
                    $place = '☆';
                   
                }
                // if($info[$i]['special_status'] == '9') {
                //     $special = $info[$i]['special_status_other'];
                //     $place="";
                // }
                $open_retirement="";
                if($info[$i]['open_retirement']=='Y'){
                    $open_retirement='☆';
                }
                echo "\"".iconv("UTF-8", "BIG5", $open_retirement."\",");
                echo "\"".iconv("UTF-8", "BIG5", $place."\",");
                echo "\"".iconv("UTF-8", "BIG5", $special."\",");

                echo "\"".iconv("UTF-8", "BIG5//IGNORE", $info[$i]['BS_name']."\",");
                // echo "\"".iconv("UTF-8", "BIG5//IGNORE", ""."\",");
                // echo "\"".iconv("UTF-8", "BIG5//IGNORE", ""."\",");
                echo "\"".iconv("UTF-8", "BIG5//IGNORE", $info[$i]['cancel_count']."\"\r\n");
                $sum_people+=$info[$i]['no_persons'];
                $sum_time+=$info[$i]['range'];
                $z++;
            }
            for($k=0;$k<7;$k++){
                echo "\"".iconv("UTF-8", "BIG5", ''."\",");
            }
            echo "\"".iconv("UTF-8", "BIG5",$sum_people."\",");
            echo "\"".iconv("UTF-8", "BIG5",$sum_time."\",");
        }

        if ($query['query_type']=='B') {
            header("Content-type: application/csv");
            header("Content-Disposition: attachment; filename=plan.csv");
            header("Pragma: no-cache");
            header("Expires: 0");
            $filename = 'plan.csv';
            echo iconv("UTF-8", "BIG5", "年度訓練計劃(發展系列)\r\n");
            echo iconv("UTF-8", "BIG5", "序號,");
            echo iconv("UTF-8", "BIG5", "次類別,");
            echo iconv("UTF-8", "BIG5", "系類別,");
            echo iconv("UTF-8", "BIG5", "班期名稱,");
            echo iconv("UTF-8", "BIG5", "研習對象,");
            echo iconv("UTF-8", "BIG5", "初始期數,");
            echo iconv("UTF-8", "BIG5", "實際期數,");
            echo iconv("UTF-8", "BIG5", "每期人數,");
            echo iconv("UTF-8", "BIG5", "每期時數(實體),");
            echo iconv("UTF-8", "BIG5", "每期時數(線上),");
            echo iconv("UTF-8", "BIG5", "每期時數(實+線),");
            echo iconv("UTF-8", "BIG5", "人數合計,");
            echo iconv("UTF-8", "BIG5", "時數合計(實體),");
            echo iconv("UTF-8", "BIG5", "時數合計,");
            echo iconv("UTF-8", "BIG5", "e大課程名稱,");
            echo iconv("UTF-8", "BIG5", "預定開班時間,");
            echo iconv("UTF-8", "BIG5", "環教班期,");
            echo iconv("UTF-8", "BIG5", "政策行銷班期,");
            echo iconv("UTF-8", "BIG5", "重大政策,");
            echo iconv("UTF-8", "BIG5", "開放退休人員選課,");

            echo iconv("UTF-8", "BIG5", "無須支應講座鐘點費,");
            echo iconv("UTF-8", "BIG5", "上課地點非公訓處,");
            echo iconv("UTF-8", "BIG5", "前一年承辦人,");
            echo iconv("UTF-8", "BIG5", "承辦人,");
            echo iconv("UTF-8", "BIG5", "取消開班,\r\n");
            $z=1;
            for ($i=0;$i<count($info);$i++) {
                
                echo "\"".iconv("UTF-8", "BIG5", $z."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['second_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['series_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['class_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['respondant']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['base_term']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['no_persons']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['range']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['online_total_hours']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['range']+$info[$i]['online_total_hours']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']*$info[$i]['no_persons']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']*$info[$i]['range']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['term']*($info[$i]['range']+$info[$i]['online_total_hours'])."\",");
                echo "\"".iconv("UTF-8", "BIG5", ''."\",");
         
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['each_term_date']."\",");

                
                if($info[$i]['env_class'] == 'Y'){
                    echo "\"".iconv("UTF-8","BIG5","☆\",");
                  } else {
                    echo "\"".iconv("UTF-8","BIG5","\",");
                  }
                if($info[$i]['policy_class'] == 'Y'){
                    echo "\"".iconv("UTF-8","BIG5","☆\",");
                  } else {
                    echo "\"".iconv("UTF-8","BIG5","\",");
                  }

                $map_list='';
                if($info[$i]['map1'] == '1'){
                      $map_list = 'A營造永續環境 ';
                  } 
                  if($info[$i]['map2'] == '1'){
                      $map_list = 'B健全都市發展 ';
                  }
                  if($info[$i]['map3'] == '1') {
                      $map_list = 'C發展多元文化 ';
                  }
                  if($info[$i]['map4'] == '1') {
                      $map_list = 'D優化產業勞動 ';
                  }
                  if($info[$i]['map5'] == '1') {
                      $map_list = 'E強化社會支持 ';
                  }
                  if($info[$i]['map6'] == '1') {
                      $map_list = 'F打造優質教育 ';
                  }
                  if($info[$i]['map7'] == '1') {
                      $map_list = 'G精進健康安全 ';
                  }
                  if($info[$i]['map8'] == '1') {
                      $map_list = 'H精實良善治理 ';
                }
                echo "\"".iconv("UTF-8", "BIG5",$map_list."\",");

                $special = '';
                $place="";
                if($info[$i]['not_hourfee'] == 'Y'){
                    $special = '☆';
                } 
                if($info[$i]['not_location'] == 'Y') {
                    $place = '☆';
                }
                // if($info[$i]['special_status'] == '9') {
                //     $special = $info[$i]['special_status_other'];
                // }
                $open_retirement="";
                if($info[$i]['open_retirement']=='Y'){
                    $open_retirement='☆';
                }
                echo "\"".iconv("UTF-8", "BIG5", $open_retirement."\",");
                echo "\"".iconv("UTF-8", "BIG5", $special."\",");
                echo "\"".iconv("UTF-8", "BIG5", $place."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['pre_worker']."\",");
                echo "\"".iconv("UTF-8", "BIG5", $info[$i]['BS_name']."\",");
                echo "\"".iconv("UTF-8", "BIG5//IGNORE", $info[$i]['cancel_count']."\"\r\n");
                $z++;
            }
        }


    }
}
