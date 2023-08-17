<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_plan_count_model extends Common_model
{
    public function getCoursePlanCountData($year, $season, $smonth, $emonth, $search_start_date, $search_end_date)
    {

        $where = "";
        $orderby = "";
        $sql = " SELECT
        a.type as class_no
        ,a.seq_no
        ,a.range
        ,b.name as  description
        ,a.class_name,a.term
        ,date_format(a.start_date1, '%m-%d') as SEL_S_DATE
        ,date_format(a.end_date1, '%m-%d') as SEL_E_DATE
        ,a.no_persons
        ,(select count(*) from online_app where yn_sel IN ('1','2','3','4','5', '8') and year=a.year and class_no=a.class_no and term=a.term) as Pcount
        ,(select count(*) from online_app where yn_sel='1' and year=a.year and class_no=a.class_no and term=a.term) as gcount
        ,a.range*(select count(*) from online_app where yn_sel='1' and year=a.year and class_no=a.class_no and term=a.term)/6 as lcount
        ,a.worker, (v.name) workername ,a.select_number as scount
        FROM `require` a
        LEFT JOIN second_category b ON a.beaurau_id=b.item_id and b.parent_id=a.type
        left join BS_user v on v.idno=a.worker
        where
            1=1 and a.class_status !=1";
        // if($type != 0){
        //     $where = " WHERE ((STR_TO_DATE('".$start_date."','YYYY-MM-DD')<= c.use_date and c.use_date <= STR_TO_DATE('".$end_date."','YYYY-MM-DD') ))";
        // }
        // if($year != ""){
        //     $where = $this->sqlWhere($where,'a.year',$year);
        // }

        // $where = $where . " ";

        if ((!empty($apply_s_date)) && (!empty($apply_e_date))) {
            $where .= " and ( (  date('{$apply_s_date}') between start_date1 and end_date1  or  date(".$this->db->escape(addslashes($apply_e_date)).")  between start_date1 and end_date1 )) ";
        } elseif ((!empty($apply_s_date)) && (empty($apply_e_date))) {
            $where .= " and ( date_format(start_date1 ,'%Y-%m-%d')>='$apply_s_date') ";
        } elseif ((empty($apply_s_date)) && (!empty($apply_e_date))) {
            $where .= " and ( date_format(end_date1 ,'%Y-%m-%d')<='$apply_e_date') ";
        }if ((!empty($apply_s_date1)) && (!empty($apply_e_date1))) {
            $where .= " and ( (  date('{$apply_s_date1}' ) between APPLY_S_DATE and APPLY_E_DATE  or  date(".$this->db->escape(addslashes($apply_e_date1)).")  between APPLY_S_DATE and APPLY_E_DATE )) ";
        } elseif ((!empty($apply_s_date1)) && (empty($apply_e_date1))) {
            $where .= " and ( date_format(APPLY_S_DATE ,'%Y-%m-%d')>='$apply_s_date1') ";
        } elseif ((empty($apply_s_date1)) && (!empty($apply_e_date1))) {
            $where .= " and ( date_format(APPLY_E_DATE ,'%Y-%m-%d')<='$apply_e_date1') ";
        }

        //
        $search_year = $year;
        $search_season = $season;
        $search_month_start = $smonth;
        $search_month_end = $emonth;
        if (!empty($search_year)) {
            $where .= " and a.year = ".$this->db->escape(addslashes($search_year))." ";
        }

        if (!empty($search_season) && ($search_season >= 1 && $search_season <= 4)) {
            $where .= " and a.Reason = ".$this->db->escape(addslashes($search_season))." ";
        }



        //撈資料
        $data = array();

        if ($search_start_date != "" && $search_end_date != "") {
            $query_start_date = $search_start_date;
            $query_end_date = $search_end_date;
            $where .= " and ( start_date1 BETWEEN date (".$this->db->escape(addslashes($query_start_date)).")
                AND date (".$this->db->escape(addslashes($query_end_date)).") ) ";
        }

        $orderby = " order by a.type
        ,b.name
        ,a.year,a.class_no,a.term
        ,a.start_date1 ";
        $sql = $sql . " " . $where . " " . $orderby;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function csvexport($filename, $query_start_date, $query_end_date, $data, $dayOfWeek)
    {
        $filename = iconv('UTF-8', 'BIG5', '週開班情況表' . date("Ymd") . '.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv('UTF-8', 'BIG5', "臺北市政府公務人員訓練處,");
        echo iconv('UTF-8', 'BIG5', "週開班情況表\r\n");
        echo iconv('UTF-8', 'BIG5', $query_start_date . "至" . $query_end_date . "\r\n");
        echo iconv('UTF-8', 'BIG5', "系列,");
        echo iconv('UTF-8', 'BIG5', "單位/類別,");
        echo iconv('UTF-8', 'BIG5', "班期名稱,");
        echo iconv('UTF-8', 'BIG5', "期別,");
        echo iconv('UTF-8', 'BIG5', "研習期程,");
        echo iconv('UTF-8', 'BIG5', "研習日期,");
        echo iconv('UTF-8', 'BIG5', "計劃人數,");
        echo iconv('UTF-8', 'BIG5', "調訓人數,");
        echo iconv('UTF-8', 'BIG5', "結訓人數,");
        echo iconv('UTF-8', 'BIG5', "人天次,");
        echo iconv('UTF-8', 'BIG5', "帶班人員 \r\n");

        foreach ($data as $val) {
            echo iconv('UTF-8', 'BIG5', $val['class_no'] == 'A' ? "行政系列" : "發展系列") . ',';
            echo iconv('UTF-8', 'BIG5', $val['description']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['class_name']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['term']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['range']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['SEL_S_DATE'] . "~" . $val['SEL_E_DATE']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['no_persons']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['Pcount']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['gcount']) . ',';
            echo iconv('UTF-8', 'BIG5', ceil($val['lcount'])) . ',';
            echo iconv('UTF-8', 'BIG5', $val['workername']) . ',';
            echo "\r\n";
        }
        exit;
    }

}
