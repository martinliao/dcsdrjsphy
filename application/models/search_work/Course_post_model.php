<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_post_model extends Common_model
{
    public function getCoursePostData($year, $reason, $type, $BEAURAU, $month, $query_apply_s_date
        , $query_apply_e_date, $sort_filed, $sort_type, $rows="", $offset="") {
        $Select_Year = $year + 1911;
        $query_cond = "";
        if ($year != '') {
            $query_cond .= " and year=".$this->db->escape(addslashes($year))." ";
        }


        if ($reason != '') {
            $query_cond .= " and reason=".$this->db->escape(addslashes($reason))." ";
        }

        if ($type != '') {
            $query_cond .= " and type=".$this->db->escape(addslashes($type))." ";
        }

        if ($BEAURAU != '') {
            $query_cond .= " and BEAURAU_ID=".$this->db->escape(addslashes($BEAURAU))." ";
        }

        if ($month == '') {
            $Select_Month = '01';
        } else {
            $Select_Month = $month;
            if ($Select_Month == 12) {
                $START_Year = $Select_Year + 1;
                $START_Month = 1;
            } else {
                $START_Year = $Select_Year;
                $START_Month = $Select_Month + 1;
            }
            $query_cond .= " and start_date1 >= date(".$this->db->escape(addslashes($Select_Year."-".$Select_Month."-01")).")
                                AND start_date1 < date(".$this->db->escape(addslashes($START_Year."-".$START_Month."-01")).") ";

        }

        if (($query_apply_s_date != '') || ($query_apply_e_date != '')) {
            if (($query_apply_s_date != '') && ($query_apply_e_date != '')) {
                $query_cond .= " and start_date1 >= date(".$this->db->escape(addslashes($query_apply_s_date)).")
                                AND start_date1 < date(".$this->db->escape(addslashes($query_apply_e_date)).") ";
            } elseif (($query_apply_s_date == '') && ($query_apply_e_date != '')) {
                $query_cond .= " AND start_date1 < date(".$this->db->escape(addslashes($query_apply_e_date)).") ";
            } elseif (($query_apply_s_date != '') && ($query_apply_e_date == '')) {
                $query_cond .= " AND start_date1 >= date(".$this->db->escape(addslashes($query_apply_s_date)).") ";
            }

        }



        if (($sort_filed != "") && ($sort_type != "")) {
            $query_cond .= " order by " . $sort_filed . " " . $sort_type;
        } else {
            $query_cond .= " order by class_no ";
        }

        $sql = "select `require`.*,BS_user.name as worker_name,agent_set.ext1, code_table.DESCRIPTION from `require` join BS_user on require.worker = BS_user.idno join agent_set on require.worker = agent_set.item_id
        left join code_table on code_table.item_id = `require`.type and code_table.type_id ='23' where 1=1 " . $query_cond . " ";
        $where = "";
        $orderby = "";
        
        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }
        
        $sql = $sql . " " . $where . " " . $orderby . " " . $limit;

        $query = $this->db->query($sql);

       
        
        $dataArr = $this->QueryToArray($query);

        for ($i = 0; $i < sizeof($dataArr); $i++) {
            $dataArr[$i]['listArr'] = $this->getCourseInfo($dataArr[$i]);
            for($j=0;$j<count($dataArr[$i]['listArr']);$j++){
                if(isset($dataArr[$i]['listArrange'][$dataArr[$i]['listArr'][$j]['use_id']])){
                    if($dataArr[$i]['listArr'][$j]['teacher_type'] == '1'){
                        $dataArr[$i]['listArrange'][$dataArr[$i]['listArr'][$j]['use_id']]['teacher_list'] .= $dataArr[$i]['listArr'][$j]['teacher_name'].' 老師<br>';
                    } else if($dataArr[$i]['listArr'][$j]['teacher_type'] == '2'){
                        $dataArr[$i]['listArrange'][$dataArr[$i]['listArr'][$j]['use_id']]['teacher_list'] .= $dataArr[$i]['listArr'][$j]['teacher_name'].' 助教<br>';
                    }
                } else {
                    $dataArr[$i]['listArrange'][$dataArr[$i]['listArr'][$j]['use_id']]['DESCRIPTION'] = $dataArr[$i]['listArr'][$j]['DESCRIPTION'];
                    if($dataArr[$i]['listArr'][$j]['teacher_type'] == '1'){
                        $dataArr[$i]['listArrange'][$dataArr[$i]['listArr'][$j]['use_id']]['teacher_list'] = $dataArr[$i]['listArr'][$j]['teacher_name'].' 老師<br>';
                    } else if($dataArr[$i]['listArr'][$j]['teacher_type'] == '2'){
                        $dataArr[$i]['listArrange'][$dataArr[$i]['listArr'][$j]['use_id']]['teacher_list'] = $dataArr[$i]['listArr'][$j]['teacher_name'].' 助教<br>';
                    }
                }
            }
        }

        // print_r($dataArr);
        return $dataArr;

    }

    public function getDatas($arr)
    {
        $sql = "select DESCRIPTION from course c left join code_table cd  on c.course_code = cd.item_id and cd.type_id='17' where c.year = ".$this->db->escape(addslashes($arr['year']))." and c.class_no =
        ".$this->db->escape(addslashes($arr['class_no']))." and c.term = ".$this->db->escape(addslashes($arr['term']))." and description not IN ('報到(含班務說明)','報到程序與註冊安排','報到暨班務說明','報到','班務介紹')  ";
// echo $sql. " ";
        $result = $this->getSQlResult($sql);

        return $result;
    }

    public function getCourseInfo($arr){
        $sql = sprintf("SELECT
                            course_code.NAME AS DESCRIPTION,
                            room_use.use_id,
                            teacher.NAME AS teacher_name,
                            teacher.teacher_type 
                        FROM
                            room_use
                            JOIN periodtime ON room_use.`year` = periodtime.`year` 
                            AND room_use.class_id = periodtime.class_no 
                            AND room_use.term = periodtime.term 
                            AND room_use.use_id = periodtime.course_code 
                            AND room_use.use_date = periodtime.course_date
                            JOIN course_code ON room_use.use_id = course_code.item_id
                            JOIN teacher ON room_use.teacher_id = teacher.idno 
                            AND room_use.isteacher = teacher.teacher 
                        WHERE
                            room_use.`year` = %s 
                            AND room_use.class_id = %s 
                            AND room_use.term = %s 
                            AND course_code.NAME NOT IN ( '報到(含班務說明)', '報到程序與註冊安排', '報到暨班務說明', '報到', '班務介紹' ) 
                        GROUP BY
                            room_use.use_id,
                            room_use.teacher_id 
                        ORDER BY
                            periodtime.course_date,
                            periodtime.from_time,
                            teacher.teacher_type",$this->db->escape(addslashes($arr['year'])),$this->db->escape(addslashes($arr['class_no'])),$this->db->escape(addslashes($arr['term'])));
        
        $result = $this->getSQlResult($sql);

        return $result;
    }

    public function getSeData()
    {
        $sql = sprintf("
            SELECT ITEM_ID,DESCRIPTION FROM code_table WHERE TYPE_ID='23' order by item_id
            "
        );

        $result = $this->getSQlResult($sql);

        $datas = $this->getSubSeData($result);
       
        return $datas;
        
    }

    public function getSubSeData($data){
        for($i = 0 ; $i < sizeof($data); $i++){
            $sql ="SELECT item_id,NAME
            FROM  second_category
            WHERE parent_id=".$this->db->escape(addslashes($data[$i]['ITEM_ID']))." and enable='1'";

            $data[$i]["sub"] = $this->getSQlResult($sql);
        }

        return $data;
       
    }

    public function getSQlResult($sql)
    {
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function csvexport($filename, $query_start_date, $query_end_date, $dayOfWeek,$year,$season,$firstSeries,$bureau_id,$month,$start_date,$end_date,$sort_filed,$sort_type,$checkArr,$searchData)
    {
        $data = array();
        
        $data = $this->getCoursePostData($year,$season,$firstSeries,$bureau_id,$month,$start_date,$end_date,$sort_filed,$sort_type);
       
        if($filename == ""){
            $filename = date("Ymd") . '.csv';
        }
        else{
            $filename = $filename. '.csv';
        }
        
        

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv('UTF-8', 'BIG5', "系列別,");
        echo iconv('UTF-8', 'BIG5', "年度,");
        echo iconv('UTF-8', 'BIG5', "班期名稱,");
        echo iconv('UTF-8', 'BIG5', "期別,");
        echo iconv('UTF-8', 'BIG5', "起迄日期,");
        echo iconv('UTF-8', 'BIG5', "研習時數,");
        echo iconv('UTF-8', 'BIG5', "研習對象,");
        echo iconv('UTF-8', 'BIG5', "課程內容,");
        echo iconv('UTF-8', 'BIG5', "講座,");
        echo iconv('UTF-8', 'BIG5', "承辦人/分機\r\n");
        
        $checkIndex = array();
        if(!empty($checkArr) && $checkArr != 'all'){
            $checkArr = substr($checkArr, 0, -1);
            $checkIndex = explode(",", $checkArr);
        }

        

        foreach ($data as $val) {
            if(count($checkIndex)>0){
                if(!in_array($val['seq_no'], $checkIndex)){
                    continue;
                }
            }

            foreach ($val['listArrange'] as $listItem) {
                $listItem['teacher_list'] = str_replace('<br>', '、', $listItem['teacher_list']);
                $listItem['teacher_list'] = mb_substr($listItem['teacher_list'], 0, -1, 'utf8');
                $listItem['DESCRIPTION'] = str_replace(',', '，', $listItem['DESCRIPTION']);
                $listItem['DESCRIPTION'] = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $listItem['DESCRIPTION']);
                $val['respondant'] = str_replace(',', '，', $val['respondant']);
                $val['respondant'] = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', $val['respondant']);
                echo iconv('UTF-8', 'BIG5', $val['DESCRIPTION']) . ',';
                echo iconv('UTF-8', 'BIG5', $val['year']) . ',';
                echo iconv('UTF-8', 'BIG5', $val['class_name']) . ',';
                echo iconv('UTF-8', 'BIG5', $val['term']) . ',';
                echo iconv('UTF-8', 'BIG5', substr($val['start_date1'],0,-8) . "~" . substr($val['end_date1'],0,-8)) . ',';
                echo iconv('UTF-8', 'BIG5', $val['range']) . ',';
                echo iconv('UTF-8', 'BIG5', $val['respondant']) . ',';
                echo iconv('UTF-8', 'BIG5', $listItem['DESCRIPTION']) . ',';
                echo iconv('UTF-8', 'BIG5', $listItem['teacher_list']) . ',';
                echo iconv('UTF-8', 'BIG5', $val['worker_name'].'/'.$val['ext1']) . ',';
                echo "\r\n";
            }
        }
    }

}
