<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_count_model extends Common_model
{
    public function getCourseCountData($year, $start_date, $end_date, $type,$series,$rows="", $offset="")
    {

        $where = "";
        $orderby = "";
        $series = $this->db->escape($series);
        $start_date = $this->db->escape($start_date);
        $end_date = $this->db->escape($end_date);
        $sql = "select distinct SUBSTR(a.class_no,1,1) class_NO,a.year,a.term,b.name description,a.class_name
        ,round((select count(*) from online_app where  yn_sel not in ('6') and year=a.year and class_no=a.class_no
        and term=a.term)) as gcount,a.No_Persons,a.SELED_NO_PERSONS,r.TRUE_COUNT,a.TYPE

                from `require` a
                LEFT  JOIN second_category b ON a.beaurau_id=b.item_id and b.parent_id=a.type
                LEFT JOIN online_app O ON a.YEAR=O.YEAR AND a.CLASS_NO=O.CLASS_NO AND a.TERM=O.TERM
                LEFT JOIN require_list r
                                ON a.YEAR = r.YEAR
                                AND a.CLASS_NO = r.CLASS_NO
                                AND a.TERM = r.TERM

                WHERE 1=1 and a.type = ".$series." and (( a.apply_s_date>=DATE(" . $start_date . ")  and  DATE(" . $end_date . ") >= a.apply_s_date )
                or (a.apply_e_date>=DATE(" . $start_date . ") and  DATE(" . $end_date . ") >=a.apply_e_date ))
                order by class_no,description, class_name ";

        $sql = $sql . " " . $where . " " . $orderby;

        $limit = "";
        if($rows != "" && $offset != "") {
        $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
        $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function getCourseCountData2($year, $start_date, $end_date, $type,$series,$rows="", $offset="",$query_class_name="")
    {
        $year = $this->db->escape($year);
        $start_date = $this->db->escape($start_date);
        $end_date = $this->db->escape($end_date);
        $type = $this->db->escape($type);
        $series = $this->db->escape_like_str($series);
        $rows = $this->db->escape_like_str($rows);
        $offset = $this->db->escape_like_str($offset);
        $query_class_name = $this->db->escape_like_str($query_class_name);
        //,nvl((select count(*) from stud_modifylog WHERE year=a.year AND class_no=a.class_no AND term=a.term AND `modify_item` = '選員'),'') as choose //選員測試速度 需15分鐘以上(真實可能還要在好幾倍時間)
        //$this->stud_modifylog_model->getModifyLogByRequire($condition);//選員 原始 需修改
        $where = " and a.class_name LIKE '%".$query_class_name."%'";
        if(isset($year)){
           //$where .= " and a.year = ".$year;
        }
       // $orderby = "order by type,class_no,description, class_name ";
        $orderby = "order by type, start_date1, class_no_id, term ";   //2022-01-24        
        //2021_11_09 新增  結訓人數 = gcount2, 訓練人天次 = lcount, 環教班期 = env_class, 政策行銷班期 = policy_class, 退休人員數 = rcount, 所屬局處名稱 = dev_type_name, 承辦單位 = req_beaurau_name
        $sql = "select distinct SUBSTR(a.class_no,1,1) class_NO,a.year,a.term,b.name description,a.class_name
        ,round((select count(*) from online_app where  yn_sel not in ('6') and year=a.year and class_no=a.class_no
        and term=a.term)) as gcount,a.No_Persons,a.SELED_NO_PERSONS,r.TRUE_COUNT,a.TYPE
        ,nvl((select count(*) from online_app where yn_sel='1' and year=a.year and class_no=a.class_no and term=a.term), 0) as gcount2
        ,nvl(round(a.range*(select count(*) from online_app where yn_sel in ('1') and year=a.year and class_no=a.class_no and term=a.term)/6), 0) as lcount
        ,nvl((select room_name from venue_information where ROOM_ID = a.room_code LIMIT 1), '') as room
        ,nvl((select peoples from select_class_people where year=a.year and class_no=a.class_no and term=a.term LIMIT 1), '') as peoples
        ,a.env_class
        ,a.policy_class
        ,r.start_date1
        ,a.range
        ,a.map1
        ,a.map2
        ,a.map3
        ,a.map4
        ,a.map5
        ,a.map6
        ,a.map7
        ,a.map8
        ,nvl((select name from bureau where a.dev_type = bureau_id),'') as dev_type_name
        ,nvl((select name from bureau where a.req_beaurau = bureau_id),'') as req_beaurau_name
        ,a.class_no AS class_no_id
        ,a.content
        ,nvl ((
            SELECT
                count(*) 
            FROM
                online_app online_app_r join BS_user BS_user_r on online_app_r.id = BS_user_r.idno
            WHERE
                online_app_r.yn_sel = '1' 
                AND online_app_r.year = a.year 
                AND online_app_r.class_no = a.class_no 
                AND online_app_r.term = a.term 
                AND BS_user_r.retirement = '0'
                ),
            0 
        ) AS rcount
        ,nvl((SELECT name FROM `BS_user` WHERE `idno` = a.worker), a.contactor) as worker_fix
                from `require` a
                LEFT  JOIN second_category b ON a.beaurau_id=b.item_id and b.parent_id=a.type
                LEFT JOIN online_app O ON a.YEAR=O.YEAR AND a.CLASS_NO=O.CLASS_NO AND a.TERM=O.TERM
                LEFT JOIN require_list r
                                ON a.YEAR = r.YEAR
                                AND a.CLASS_NO = r.CLASS_NO
                                AND a.TERM = r.TERM

                 WHERE 1=1 and a.class_status in ('2','3') and a.type LIKE '%".$series."%' and ((".$start_date." between a.start_date1 and a.end_date1) or (".$end_date." between a.start_date1 and a.end_date1) or ((a.start_date1 >= ".$start_date.") and (a.end_date1 <= ".$end_date.")))
                ";

        $sql = $sql . " " . $where . " " . $orderby;

        $limit = "";
        if($rows != "" && $offset != "") {
        $limit = " limit " . $rows . " offset " . $offset;
        }
        else if($rows != "") {
        $limit = " limit " . $rows;
        }
       // $limit = " limit 5";    //TEST
        $sql = $sql . " " . $limit;

        $query = $this->db->query($sql);
        $datas = $this->QueryToArray($query);
        //echo $rows; die();

        
        //if($rows < 10 ){
            //$rows = 10;
        //}
        //$rows = 1;  //暫時取消
        //整合教師資料     count($datas) 

        //"nvl((SELECT group_concat(b.description) FROM room_use a LEFT JOIN canteach c ON a.use_id = c.course_code LEFT JOIN code_table b ON c.course_code = b.ITEM_ID AND b.TYPE_ID = '17' WHERE c.ID=t.idno AND a.`year` = r.year AND a.class_id = r.class_no AND a.term = r.term GROUP BY description), '') as course_detail,"
        
        //取得老師問卷分數
        //$temp_teacher_sc = $this->getReport4(91,110,'AA0561',1);
        

        for ($i=0; $i < count($datas)  ; $i++) {
            //2022-03-31 強制列出所有日期
            $year_d = $this->db->escape($datas[$i]['year']);
            $class_no_id_d = $this->db->escape($datas[$i]['class_no_id']);
            $term_d = $this->db->escape($datas[$i]['term']);
            $sql = 'SELECT GROUP_CONCAT(A.use_date) AS fix_date FROM (SELECT DISTINCT left(`use_date`,10) AS use_date,`class_id`  FROM `room_use` WHERE `year` = '.$year_d.' AND `class_id` = '.$class_no_id_d.' AND `term` = '.$term_d.' order by use_date) A';      
            $query = $this->db->query($sql);
            $date_res = $this->QueryToArray($query);
            //var_dump($date_res[0]["fix_date"]);die();
            if($date_res[0]["fix_date"] != ''){
                $datas[$i]['start_date1'] = $date_res[0]["fix_date"];
            }



            $temp_teacher_sc = $this->getReport4(91,$datas[$i]['year'],$datas[$i]['class_no_id'],$datas[$i]['term']);

            $where = " AND r.year='".$datas[$i]['year']."' AND r.class_no='".$datas[$i]['class_no_id']."' AND r.term='".$datas[$i]['term']."' ";
            //die($where);
            $sql = "SELECT DISTINCT
                    t.name, t.another_name AS alias,t.idno, t.id, t.major AS SCHOOL, t.birthday AS birth, t.career, t.email, t.institution AS corp, t.zipcode AS zone, t.job_title AS position, t.h_tel AS telo, t.h_tel2 AS telh, t.mobile AS mobil,
                    t.teacher as teacher,t.teacher as assistant,r.seq_no,htt.hour_fee,htt.traffic_fee,
                    nvl((SELECT group_concat(course_code.name) FROM courseteacher ctr JOIN course_code ON course_code.item_id = ctr.course_code WHERE ctr.year = r.year and ctr.class_no = r.class_no and ctr.term = r.term and ctr.teacher_id	 = t.idno), (SELECT group_concat(course_code.name) FROM courseteacher ctr JOIN course_code ON course_code.item_id = ctr.course_code WHERE ctr.year = r.year and ctr.class_no = r.class_no and ctr.term = r.term)) as course_detail,
                    concat(cc.city_name,csc.subcity_name,t.route) AS addr,
                    (
                        SELECT name
                        FROM hire_category hc
                        WHERE hc.item_id = t.hire_type
                    ) as DESCRIPTION,
                    (
                        SELECT name
                        FROM education hc
                        WHERE hc.item_id = t.education
                    ) as NAME,
                    r.class_name, r.type, r.year, r.class_no, r.term,r.range_real,
                    ru.use_date,t.teacher_type
                FROM teacher t
                INNER JOIN co_city cc
                    ON cc.city=t.county
                INNER JOIN co_subcity csc
                    ON csc.city=t.county AND csc.subcity=t.district

                INNER JOIN room_use ru
                    ON ru.teacher_id = t.idno and t.teacher=ru.isteacher
                INNER JOIN `require` r
                    ON ru.year = r.year and ru.term = r.term and ru.class_id = r.class_no
                LEFT JOIN `hour_traffic_tax` htt
                    ON htt.year = r.year and htt.term = r.term and htt.class_no = r.class_no and htt.teacher_id = t.idno and htt.use_date = ru.use_date              
                WHERE t.name NOT IN ('教務組', '教務組1', '總務組' ,'綜企組')
                $where
                ORDER BY ru.use_date
                ";
            $query = $this->db->query($sql);
            $teacherData = $this->QueryToArray($query); 
            //$temp_teacher_sc[$teacherData[0]['idno']] 
            
            foreach ($teacherData as $tdat_key => $tdata) {
                $teacherData[$tdat_key]['report_score'] = $temp_teacher_sc[$teacherData[$tdat_key]['idno']];
            }
            $datas[$i]["teachers"] = $teacherData;
            if(4==1){
            echo "<PRE>";
            var_dump($teacherData);echo "</PRE>";die();
            }

        }


        //20220401 重新列出老師課程 
        for($i=0;$i<count($datas);$i++){
            //var_dump($i);die();
            for($j=0;$j<count($datas[$i]);$j++){
                //var_dump($this->data['datas'][$i]);die();
                $c_year = $datas[$i]['teachers'][$j]['year'];
                $c_class_no = $datas[$i]['teachers'][$j]['class_no'];
                $c_term = $datas[$i]['teachers'][$j]['term'];
                $c_teacher_id = $datas[$i]['teachers'][$j]['idno'];
                $c_use_date = $datas[$i]['teachers'][$j]['use_date'];
            //var_dump($idno);die();
                if ($c_year){
                    $datas[$i]['teachers'][$j]['course_detail'] = $this->getteachercourse($c_year, $c_class_no, $c_term, $c_use_date,$c_teacher_id);
                }else{
                    break;
                }
            }

        }

        //return $this->QueryToArray($query);
        return $datas;

    }    


    public function getCourseCountData2Count($year, $start_date, $end_date, $type,$series,$query_class_name="")
    {
        $year = $this->db->escape($year);
        $start_date = $this->db->escape($start_date);
        $end_date = $this->db->escape($end_date);
        $type = $this->db->escape($type);
        $series = $this->db->escape_like_str($series);
 
        $query_class_name = $this->db->escape_like_str($query_class_name);
        $where = " and a.class_name LIKE '%".$query_class_name."%'";
        if(isset($year)){
            //$where .= " and a.year = '".$year."'";
        }
        $orderby = "order by type,class_no,description, class_name ";
        $sql = "select distinct SUBSTR(a.class_no,1,1) class_NO,a.year,a.term,b.name description,a.class_name
                from `require` a
                LEFT  JOIN second_category b ON a.beaurau_id=b.item_id and b.parent_id=a.type
                LEFT JOIN online_app O ON a.YEAR=O.YEAR AND a.CLASS_NO=O.CLASS_NO AND a.TERM=O.TERM
                LEFT JOIN require_list r
                                ON a.YEAR = r.YEAR
                                AND a.CLASS_NO = r.CLASS_NO
                                AND a.TERM = r.TERM

                WHERE 1=1 and a.class_status in ('2','3') and a.type LIKE '%".$series."%' and ((".$start_date." between a.start_date1 and a.end_date1) or (".$end_date." between a.start_date1 and a.end_date1) or ((a.start_date1 >= ".$start_date.") and (a.end_date1 <= ".$end_date.")))
                ";

        $sql = $sql . " " . $where . " " . $orderby;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }


    public function csvexport($filename, $query_start_date, $query_end_date,$series, $data, $dayOfWeek)
    {
        $filename = iconv("UTF-8", "BIG5", '查詢作業-各班人員報名人數.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "各班人員報名人數\r\n");
        //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

        echo iconv("UTF-8", "BIG5", "類別,");
        echo iconv("UTF-8", "BIG5", "次類別,");
        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "年度,");
        echo iconv("UTF-8", "BIG5", "期別,");
        echo iconv("UTF-8", "BIG5", "本期人數,");
        echo iconv("UTF-8", "BIG5", "報名人數,");
        echo iconv("UTF-8", "BIG5", "結訓人數\r\n");

        foreach ($data as $val) {
            echo iconv("UTF-8", "BIG5", $val['TYPE']=='A'?'行政系列':'發展系列') . ',';
            echo iconv("UTF-8", "BIG5", $val['description']) . ',';
            echo iconv("UTF-8", "BIG5", $val['class_name']) . ',';
            echo iconv("UTF-8", "BIG5", $val['year']) . ',';
            echo iconv("UTF-8", "BIG5", $val['term']) . ',';
            echo iconv("UTF-8", "BIG5", $val['No_Persons']) . ',';
            echo iconv("UTF-8", "BIG5", $val['gcount']) . ',';
            echo iconv("UTF-8", "BIG5", $val['true_count']) . ',';
            echo "\r\n";
        }
    }

    public function csvexport2($filename, $query_start_date, $query_end_date,$series, $data, $dayOfWeek, $select_cl)
    {
        //var_dump($select_cl->sess_tbox1);die();

        // 新增Excel物件
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定屬性
        $objPHPExcel->getProperties()->setCreator("PHP")
                    ->setLastModifiedBy("PHP")
                    ->setTitle("Orders")
                    ->setSubject("Subject")
                    ->setDescription("Description")
                    ->setKeywords("Keywords")
                    ->setCategory("Category");

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 將工作表命名
        $sheet->setTitle('多維度訓練統計報表');

        // 合併儲存格
        // $sheet->mergeCells('A1:D2');

        $row = 1;
        //訂單編號、訂單總額、訂單狀態、訂單成立時間
        $title_line = 0;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '系列');

        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '次類別');

        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '局處名稱');
        
        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '承辦機關');

        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '策略主題');

        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '班期名稱');

        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '年度'); 
/*      
        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '期別');
*/
        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '訓練期程');

        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '上課日期');

        if($select_cl->sess_pbox1 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '計畫人數');
        }

        if($select_cl->sess_pbox2 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '報名人數');
        }

        if($select_cl->sess_pbox3 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '結訓人數');
        }

        if($select_cl->sess_pbox4 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '訓練人天次');
        }

        if($select_cl->sess_pbox5 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '退休人員數');
        }

        if($select_cl->sess_pbox6 == 1){    
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '選員人數');
        }
		
        if($select_cl->sess_cbox1 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '環教班期');
        }

        if($select_cl->sess_cbox2 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '政策行銷班期'); 
        }

        if($select_cl->sess_cbox3 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '上課教室');
        }


        $title_line++;
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '班期承辦人');     //固定
        

        if($select_cl->sess_tbox1 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '授課講座');
        }

        if($select_cl->sess_tbox2 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '任職機關');
        }

        if($select_cl->sess_tbox3 == 1){
            $title_line++;      
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '職稱');
        }

        if($select_cl->sess_tbox4 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '生日');
        }

        if($select_cl->sess_tbox5 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '學歷');
        }

        if($select_cl->sess_tbox6 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '聘請類別');
        }

        if($select_cl->sess_tbox7 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '課程內容');
        }

        if($select_cl->sess_tcbox1 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '鐘點費');
        }

        if($select_cl->sess_tcbox2 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '交通費');   
        }

        if($select_cl->sess_tcbox3 == 1){
            $title_line++;
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($title_line, $row, '評估分數');   
        }

        $row = 2;
        //$objPHPExcel->getActiveSheet() -> getColumnDimension("A") -> setAutoSize(true);
        // jd($this->data['list'],1);
        //$word = 'A';

        foreach ($data as $list_row) {
            $fix_table = false;
            if(count($list_row['teachers']) > 1){
                $fix_rowspan = count($list_row['teachers'])-1;
                $fix_table = true;
                //echo $fix_rowspan;die();
            }
            //$mergeLine //列合併 mergeCells($mergeLine)  $mergeLine = 'A2:A5';
            $line = 'A';
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".($list_row['TYPE']=='A'?'行政系列':'發展系列')));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".($list_row['TYPE']=='A'?'行政系列':'發展系列')));
            }
            
            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['description']));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['description']));
            }
 
            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['dev_type_name']));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['dev_type_name']));
            }

            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['req_beaurau_name']));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['req_beaurau_name']));
            }


            if ($list_row['map1'] == '1'){
                $map = "A營造永續環境";    
            }elseif ($list_row['map2'] == '1'){
                $map = "B健全都市發展";
            }elseif ($list_row['map3'] == '1'){
                $map = "C發展多元文化";
            }elseif ($list_row['map4'] == '1'){
                $map = "D優化產業勞動";
            }elseif ($list_row['map5'] == '1'){
                $map = "E強化社會支持";
            }elseif ($list_row['map6'] == '1'){
                $map = "F打造優質教育";
            }elseif ($list_row['map7'] == '1'){
                $map = "G精進健康安全";
            }elseif ($list_row['map8'] == '1'){
                $map = "H精實良善治理";
            }else{
                $map = "";
            }
            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$map));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$map));
            }

            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['class_name']."(第".$list_row['term']."期)"));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['class_name']."(第".$list_row['term']."期)"));
            }

            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['year']));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['year']));
            }
/*
            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['term']));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['term']));
            }
*/
            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['range']));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['range']));
            }
            
            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row["start_date1"]));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".substr($list_row["start_date1"],0,10)));
            }

            if($select_cl->sess_pbox1 == 1){
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['No_Persons']));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['No_Persons']));
                }
            }

            if($select_cl->sess_pbox2 == 1){
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['gcount']));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['gcount']));
                }
            }

            if($select_cl->sess_pbox3 == 1){
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['gcount2']));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['gcount2']));
                }
            }

            if($select_cl->sess_pbox4 == 1){
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['lcount']));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['lcount']));
                }
            }

            if($select_cl->sess_pbox5 == 1){
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['rcount']));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['rcount']));
                }
            }

            if($select_cl->sess_pbox6 == 1){
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['peoples']));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['peoples']));
                }
            }
			
            if($select_cl->sess_cbox1 == 1){
                $temp_c1 = $list_row["env_class"] == "Y"?"☆":"";
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$temp_c1));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$temp_c1));
                }
            }

            if($select_cl->sess_cbox2 == 1){
                $temp_c2 = $list_row["policy_class"] == "Y"?"☆":"";
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$temp_c2));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$temp_c2));
                }
            }

            if($select_cl->sess_cbox3 == 1){
                $line++;
                if($fix_table){
                    $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                    $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['room']));
                }else{
                    $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['room']));
                }
            }

            $line++;
            if($fix_table){
                $mergeLine = $line.$row.":".$line.($row+$fix_rowspan);
                $objPHPExcel->getActiveSheet()->mergeCells($mergeLine)->setCellValue($line.$row, trim("\t".$list_row['worker_fix']));
            }else{
                $objPHPExcel->getActiveSheet()->setCellValue($line.$row, trim("\t".$list_row['worker_fix']));
            }

           
            if(count($list_row['teachers'])>0){
                //老師資料(1對多)
                foreach ($list_row["teachers"] as $teacher){
                    $teacher_line = $line;

                    if($select_cl->sess_tbox1 == 1){
                        $teacher_line++;
                        if ($teacher['teacher_type']==1){
                            $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['name']));
                        }else if ($teacher['teacher_type']==2){
                            $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['name']."(助)"));
                        }
                    }

                    if($select_cl->sess_tbox2 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['corp']));
                    }

                    if($select_cl->sess_tbox3 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['position']));
                    }

                    if($select_cl->sess_tbox4 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".substr($teacher["birth"],0,10)));
                    }

                    if($select_cl->sess_tbox5 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['NAME']));
                    }

                    if($select_cl->sess_tbox6 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['DESCRIPTION']));
                    }

                    if($select_cl->sess_tbox7 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['course_detail']));
                    }

                    if($select_cl->sess_tcbox1 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['hour_fee']));
                    }

                    if($select_cl->sess_tcbox2 == 1){
                        $teacher_line++;
                        if($teacher["traffic_fee"]==-1){
                            $teacher["traffic_fee"] = "0";
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['traffic_fee']));
                    }

                    if($select_cl->sess_tcbox3 == 1){
                        $teacher_line++;
                        $objPHPExcel->getActiveSheet()->setCellValue($teacher_line.$row, trim("\t".$teacher['report_score']));
                    }

                    $row++;
                    
                    /*
                    if($key==0){
                        if($sess_tbox1 == 1){
                            echo "<td>".$teacher["name"]."</td>";
                        }
                        if($sess_tbox2 == 1){
                            echo "<td>".$teacher["corp"]."</td>";
                        }
                        if($sess_tbox3 == 1){
                            echo "<td>".$teacher["position"]."</td>";
                        }
                        if($sess_tbox4 == 1){
                            echo "<td>".substr($teacher["birth"],0,10)."</td>";
                        }
                        if($sess_tbox5 == 1){
                            echo "<td>".$teacher["NAME"]."</td>";
                        }
                        if($sess_tbox6 == 1){
                            echo "<td>".$teacher["DESCRIPTION"]."</td>";
                        }
                        if($sess_tbox7 == 1){
                            echo "<td>".$teacher["course_detail"]."</td>";
                        }
                        if($sess_tcbox1 == 1){
                            echo "<td>".$teacher["hour_fee"]."</td>";
                        }
                        if($sess_tcbox2 == 1){
                            echo "<td>".$teacher["traffic_fee"]."</td>";
                        } 
                    }*/
                }                
            }else{
                $row++;
            }


        	//$objPHPExcel->getActiveSheet()->getCellByColumnAndRow(12, $row)->setValueExplicit($list_row['rcount'], PHPExcel_Cell_DataType::TYPE_STRING);


	        //$word++;
	        //$objPHPExcel->getActiveSheet() -> getColumnDimension("{$word}") -> setAutoSize(true);
		    //$row++;
		}

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="多維度訓練統計報表.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;

        /*
        $filename = iconv("UTF-8", "BIG5", '多維度訓練統計報表.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "多維度訓練統計報表,");
        echo iconv("UTF-8", "BIG5", "各班人員報名人數\r\n");
        //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

        echo iconv("UTF-8", "BIG5", "類別,");
        echo iconv("UTF-8", "BIG5", "次類別,");
        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "年度,");
        echo iconv("UTF-8", "BIG5", "期別,");
        echo iconv("UTF-8", "BIG5", "本期人數,");
        echo iconv("UTF-8", "BIG5", "報名人數,");
        echo iconv("UTF-8", "BIG5", "結訓人數\r\n");

        foreach ($data as $val) {
            echo iconv("UTF-8", "BIG5", $val['TYPE']=='A'?'行政系列':'發展系列') . ',';
            echo iconv("UTF-8", "BIG5", $val['description']) . ',';
            echo iconv("UTF-8", "BIG5", $val['class_name']) . ',';
            echo iconv("UTF-8", "BIG5", $val['year']) . ',';
            echo iconv("UTF-8", "BIG5", $val['term']) . ',';
            echo iconv("UTF-8", "BIG5", $val['No_Persons']) . ',';
            echo iconv("UTF-8", "BIG5", $val['gcount']) . ',';
            echo iconv("UTF-8", "BIG5", $val['true_count']) . ',';
            echo "\r\n";
        }
        */
    }

	//整期分析圖(分數-百分位)
	public function getReport4($fid,$year,$class,$ladder)
	{
		
        $output = array();
		$condition="";
		$condition2="";
		if(isset($class)!=null){
			$condition=$condition." and cm.class=".$this->db->escape(addslashes($class))."";
			$condition2=$condition2." and class_no=".$this->db->escape(addslashes($class))."";
		};
		if(isset($year)!=null){
			$condition=$condition." and cm.year=".$this->db->escape(addslashes($year))."";
			$condition2=$condition2." and YEAR=".$this->db->escape(addslashes($year))."";
		};
		if(isset($ladder)!=null){
			$condition=$condition." and cm.ladder=".$this->db->escape(addslashes($ladder))."";
			$condition2=$condition2." and term=".$this->db->escape(addslashes($ladder))."";
		};
		if(isset($fid)!=null){
			$condition=$condition." and r.fid=".$this->db->escape(addslashes($fid))."";
		};

		// 課程老師題組平均分數
		$sql = sprintf("SELECT
							a.`name`,						
							b.teacher,
							b.course,
							b.formName,
							d.qid,
							h.name as group_name,
							h.id as group_id,
							f.question,
							e.answer
						FROM
							SV_ClassManagement a
							JOIN SV_ClassManagementForm b ON a.id = b.cmid
							JOIN SV_Reply c ON b.cmid = c.cmid 
							AND b.id = c.cmfid
							JOIN SV_ReplyQuestion d ON c.id = d.rid
							JOIN SV_ReplyAnswer e ON d.id = e.rqid
							JOIN SV_Question f ON d.qid = f.id 
							JOIN SV_FormGroup fg ON b.fid = fg.fid
							JOIN SV_GroupQuestion g ON f.id = g.qid 
							AND g.gid = fg.gid
							JOIN SV_Group h on g.gid = h.id
						WHERE
							a.`year` = %s 
							AND a.class = %s 
							AND ladder = %s 
							and c.fid = %s
							and f.type in (5)
							and e.answer != ''
						ORDER BY
							b.courseDate,
							b.order,
							g.order",
                            $this->db->escape(addslashes($year)),
                            $this->db->escape(addslashes($class)),
                            $this->db->escape(addslashes($ladder)),
                            $this->db->escape(addslashes($fid))
                        );
		$query = $this->db->query($sql);
		$result = $query->result_array();
        //var_dump($result );die();
		$tmp_data = array();
		$tmp_index = 0;
		$tmp_group_index = 0;
		for($i=0;$i<count($result);$i++){
			if($fid == 91){
				$tmp_key = $result[$i]['teacher'].'-'.$result[$i]['course'];
			} else {
				$tmp_key = $result[$i]['qid'];
			}

			if($i == '0'){
				$tmp_data[$tmp_key][$result[$i]['group_id']]['group_name'] = $result[$i]['group_name'];
				$tmp_data[$tmp_key][$result[$i]['group_id']]['count'] = 1;
				$tmp_data[$tmp_key][$result[$i]['group_id']]['score'] = $result[$i]['answer'];
			} else {
				if(isset($tmp_data[$tmp_key])){
					if(isset($tmp_data[$tmp_key][$result[$i]['group_id']])){
						$tmp_data[$tmp_key][$result[$i]['group_id']]['count']++;
						$tmp_data[$tmp_key][$result[$i]['group_id']]['score'] += $result[$i]['answer'];
					} else {
						$tmp_data[$tmp_key][$result[$i]['group_id']]['group_name'] = $result[$i]['group_name'];
						$tmp_data[$tmp_key][$result[$i]['group_id']]['count'] = 1;
						$tmp_data[$tmp_key][$result[$i]['group_id']]['score'] = $result[$i]['answer'];
					}
				} else {
					$tmp_data[$tmp_key][$result[$i]['group_id']]['group_name'] = $result[$i]['group_name'];
					$tmp_data[$tmp_key][$result[$i]['group_id']]['count'] = 1;
					$tmp_data[$tmp_key][$result[$i]['group_id']]['score'] = $result[$i]['answer'];
				}
			}
		}
	

		foreach ($tmp_data as $key => $value) {
			ksort($tmp_data[$key]);
		}

		if(sizeof($tmp_data)==0){
			return ($output);
		}

		//所有學生
		$query = $this->db->query("SELECT personal_id FROM SV_online_app
		WHERE 1=1 ".$condition2
		);
		$datas2 = $this->QueryToArray($query);

		$sql = sprintf("SELECT
							a.id as cmid,b.id as cmfid 
						FROM
							SV_ClassManagement a
							JOIN SV_ClassManagementForm b ON a.id = b.cmid 
						WHERE
							a.year = %s 
							AND a.class = %s 
							AND a.ladder = %s
							AND b.fid = %s",
                            $this->db->escape(addslashes($year)),
                            $this->db->escape(addslashes($class)),
                            $this->db->escape(addslashes($ladder)),
                            $this->db->escape(addslashes($fid))
                        );

		$query = $this->db->query($sql);
		$cmfid_list = $query->result_array();

		//學生作答次數
		$receive = 0;
		for($i=0;$i<count($datas2);$i++){
			$check_status = true;
			for($j=0;$j<count($cmfid_list);$j++){
				$sql = sprintf("SELECT
									count(1) AS cnt 
								FROM
									SV_Reply 
								WHERE
									cmid = '%s' 
									AND cmfid = '%s' 
									AND sid = '%s' 
									AND fid = '%s'",$cmfid_list[$j]['cmid'],$cmfid_list[$j]['cmfid'],$datas2[$i]['personal_id'],$fid);

				$query = $this->db->query($sql);
				$check_count = $query->result_array();

				if($check_count[0]['cnt'] == '0'){
					$check_status = false;
					break;
				} 
			}

			if($check_status){
				$receive++;
			}
		}

		$output['grouparray'] = array();
		$class_total_average = 0;
		
		foreach ($tmp_data as $key => $value) {
			if($fid == 91){
				$tmp_array = array();
				$info = explode('-', $key);

				$sql = sprintf("select idno from teacher where idno = '%s'",$info[0]);
				$query = $this->db->query($sql);
				$teacher_name = $query->result_array();
				$tmp_array['teachername'] = $teacher_name[0]['idno'];

				$sql = sprintf("select name from course_code where item_id = '%s'",$info[1]);
				$query = $this->db->query($sql);
				$course_name = $query->result_array();
				$tmp_array['use_name'] = $course_name[0]['name'];
			} else {
				$tmp_array = array();
				$sql = sprintf("select question from SV_Question where id = '%s'",$key);
				$query = $this->db->query($sql);
				$question_name = $query->result_array();
				$tmp_array['use_name'] = $question_name[0]['question'];
			}

			$i=0;
			$total_scoreaverage=0;
			foreach ($value as $key2 => $value2) {
				$tmp_array['detail'][$i]['groupname'] = $value2['group_name'];
				$tmp_array['detail'][$i]['scoreaverage'] = round(($value2['score']/$value2['count'])*20, 2);
				$total_scoreaverage += $tmp_array['detail'][$i]['scoreaverage'];
				$i++;
			}

			$class_total_average += round(($total_scoreaverage/$i), 2);

			array_push($output['grouparray'], $tmp_array);
		}
        //var_dump($output['grouparray']);die();

		for($i=0;$i<count($output['grouparray']);$i++){
			$new_index = count($output['grouparray'][$i]['detail']);
			$tmp_total = 0;

			$ydata_final = '';
			if($fid == 91){
				$str = $output['grouparray'][$i]['use_name'].'('.$output['grouparray'][$i]['teachername'].')';
			} else {
				$str = $output['grouparray'][$i]['use_name'];
			}
			$len = mb_strlen($str, 'UTF-8');
			$k = 0;
			if($len > 12){
				while ($len > 12) {
					$ydata_final .= mb_substr($str, (12*$k), 12, 'UTF-8').'<br/><br/><br/>';
					$len -= 12;
					$k++;
				}
				$ydata_final .= mb_substr($str, -($len), null,'UTF-8');
			} else {
				$ydata_final = $str;
			}
            
            $new_output['yData'] = array();
			array_push($new_output['yData'], $ydata_final);
			for($j=0;$j<count($output['grouparray'][$i]['detail']);$j++){
				$new_output['xData'][$new_index]['name'] = $output['grouparray'][$i]['detail'][$j]['groupname'];
				$new_output['xData'][$new_index]['legendIndex'] = $j+1;
				$new_output['xData'][$new_index]['data'][] = $output['grouparray'][$i]['detail'][$j]['scoreaverage'];
				$tmp_total += $output['grouparray'][$i]['detail'][$j]['scoreaverage'];
				$new_index--;
			}
			if($fid == 91){
				$new_output['xData'][$new_index]['name'] = '個別課程平均值';
				$new_output['xData'][$new_index]['legendIndex'] = 4;
				$new_output['xData'][$new_index]['data'][] = round($tmp_total/3, 2)."?".$output['grouparray'][$i]['teachername'];

                //2021-11-17 計算老師課程平均分數 多筆平均
                if(isset($tt[$output['grouparray'][$i]['teachername']])){
                    $tt[$output['grouparray'][$i]['teachername']] = ($tt[$output['grouparray'][$i]['teachername']]+round($tmp_total/3, 2))/2;
                }else{
                    $tt[$output['grouparray'][$i]['teachername']] = round($tmp_total/3, 2); 
                }
                
			}
		}

        $output = $tt;
			
		


		return ($output);
	}

    //20220401 Roger 取得當日課程
    public function getteachercourse($year, $class_no, $term, $use_date,$teacher_id)
    {        
        $this->db->DISTINCT();
        $this->db->select("cc.name as cname");
        $this->db->from('periodtime p');
        $this->db->join('course_code cc', "cc.item_id = p.course_code");
        $this->db->join('room_use ru', "p.id = ru.use_period AND p.course_date = ru.use_date");
        $this->db->where("p.year",$year);
        $this->db->where("p.term",$term);
        $this->db->where("p.class_no",$class_no);
        $this->db->where("p.course_date",$use_date);
        $this->db->where("ru.teacher_id",$teacher_id);
        
        $query = $this->db->get();
        $courses = $query->result_array();
        //var_dump($grade);die();
        foreach ($courses as $key => $row) {
            $course = $course.$row['cname'].", ";
        }

        $course = substr($course , 0 , -2);
        return $course;
    }

}
