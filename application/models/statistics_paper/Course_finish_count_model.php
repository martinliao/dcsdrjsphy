<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_finish_count_model extends Common_model
{
    public function getCourseFinishCountData($queryYear, $queryStartDate, $queryEndDate, $searchTopic, $mixAndAssess='')
    {

        if (!empty($queryStartDate) && !empty($queryEndDate) && !empty($queryYear)) {


            if ($searchTopic == '1') {


                $CourseFinishCountData =  $this->getTrainingStudentInfosTopic($queryYear, $queryStartDate, $queryEndDate, $mixAndAssess);
                $data = $this->getCountData($CourseFinishCountData);
            } else {


                $CourseFinishCountData =  $this->getTrainingStudentInfos($queryYear, $queryStartDate, $queryEndDate, $mixAndAssess);
                $data = $this->getCountData($CourseFinishCountData);
            }
        }

        return $data;
    }

    public function getTrainingStudentInfosTopic($queryYear, $queryStartDate, $queryEndDate, $mixAndAssess='')
    {

        if($mixAndAssess == 'Y'){
            $where = ' and a.is_mixed = 1 and a.is_assess = 1 ';
        } else {
            $where = '';
        }

        $sql = "SELECT
        rank() over(partition by type, description order by year, class_no, term) AS NO1,
        rank() over(partition by type, description order by year DESC, class_no DESC, term DESC) AS NO1D,
        rank() over(partition by type order by item_id, year, class_no, term) AS NO2,
        rank() over(partition by type order by item_id DESC, year DESC, class_no DESC, term DESC) AS NO2D,
     Z.* FROM (
              select
              a.type, a.class_name, a.class_no, a.year, a.term,a.env_class, nvl(a.range, 0) AS `range`,DATE_FORMAT(end_date1,'%m') as month,
              X.brother_count,
              sc.name as description,
              ct.description AS series,
              nvl((select count(*) from online_app where yn_sel='1' and year=a.year and class_no=a.class_no and term=a.term), 0) as gcount,
              nvl((select count(*) from online_app where yn_sel='1' and year=a.year and class_no=a.class_no and term=a.term AND SUBSTR(ID,2,1)='1'), 0) as gcountm,
              nvl((select count(*) from online_app where yn_sel='1' and year=a.year and class_no=a.class_no and term=a.term AND SUBSTR(ID,2,1)='2'), 0) as gcountf,
              nvl(round(a.range*(select count(*) from online_app where yn_sel in ('1') and year=a.year and class_no=a.class_no and term=a.term)/6), 0) as lcount,
              nvl(round(a.range*(select count(*) from online_app where yn_sel in ('1') and year=a.year and class_no=a.class_no and term=a.term and substr(id,2,1)='1' )/6), 0) as mcount,
              nvl(round(a.range*(select count(*) from online_app where yn_sel in ('1') and year=a.year and class_no=a.class_no and term=a.term and substr(id,2,1)='2' )/6), 0) as fcount,
              nvl ((
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
                ) AS rcount,
                a.IS_ASSESS,
                a.IS_MIXED,
                a.policy_class,
                a.map1,
                a.map2,
                a.map3,
                a.map4,
                a.map5,
                a.map6,
                a.map7,
                a.map8,
                sc.item_id
                    from `require` a
                LEFT JOIN second_category sc
                        ON a.type=sc.parent_id AND a.beaurau_id=sc.item_id
                LEFT JOIN code_table ct
                        ON a.type=ct.item_id AND type_id='23'
                LEFT JOIN (
                                select type, count(*) as brother_count FROM
                                (
                                        SELECT DISTINCT
                                                XR.type,
                                                xsc.name, xsc.item_id AS cate_id
                                        FROM `require` XR
                                        LEFT JOIN second_category xsc
                                                ON XR.type=xsc.parent_id AND XR.beaurau_id=xsc.item_id
                                        WHERE XR.class_status IN ('2', '3') AND XR.is_cancel =0  AND XR.year=".$this->db->escape(addslashes($queryYear))." AND END_DATE1 BETWEEN ".$this->db->escape(addslashes($queryStartDate))." AND ".$this->db->escape(addslashes($queryEndDate))."
                                ) zz
                                group by type
                        ) X
                        ON a.type=X.type
                where
                a.year=".$this->db->escape(addslashes($queryYear)).$where." AND a.class_status IN ('2', '3') AND a.type not in ('C','O') AND a.IS_CANCEL = 0 
                                AND a.END_DATE1 BETWEEN ".$this->db->escape(addslashes($queryStartDate))." AND ".$this->db->escape(addslashes($queryEndDate))."
                order by
                        a.type,
                        sc.item_id,
                        a.year, a.class_no, a.term 
                      ) Z 
                       order by type , NO2 " ;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function getTrainingStudentInfos($queryYear, $queryStartDate, $queryEndDate ,$mixAndAssess='')
    {
        if($mixAndAssess == 'Y'){
            $where = ' and a.is_mixed = 1 and a.is_assess = 1 ';
        } else {
            $where = '';
        }

        $sql = "SELECT
        rank() over(partition by type, description order by year, class_no, term) AS NO1,
        rank() over(partition by type, description order by year DESC, class_no DESC, term DESC) AS NO1D,
        rank() over(partition by type order by item_id, year, class_no, term) AS NO2,
        rank() over(partition by type order by item_id DESC, year DESC, class_no DESC, term DESC) AS NO2D,
     Z.* FROM (
              select
              a.type, a.class_name, a.class_no, a.year, a.term,a.env_class, nvl(a.range, 0) AS `range`,DATE_FORMAT(end_date1,'%m') as month,
              X.brother_count,
              sc.name as description,
              ct.description AS series,
              nvl((select count(*) from online_app where yn_sel='1' and year=a.year and class_no=a.class_no and term=a.term), 0) as gcount,
              nvl((select count(*) from online_app join BS_user on online_app.id = BS_user.idno where online_app.yn_sel='1' and online_app.year=a.year and online_app.class_no=a.class_no and online_app.term=a.term AND BS_user.gender = 'M'), 0) as gcountm,
              nvl((select count(*) from online_app join BS_user on online_app.id = BS_user.idno where online_app.yn_sel='1' and online_app.year=a.year and online_app.class_no=a.class_no and online_app.term=a.term AND BS_user.gender = 'F'), 0) as gcountf,
              nvl(round(a.range*(select count(*) from online_app where yn_sel in ('1') and year=a.year and class_no=a.class_no and term=a.term)/6), 0) as lcount,
              nvl(round(a.range*(select count(*) from online_app where yn_sel in ('1') and year=a.year and class_no=a.class_no and term=a.term and substr(id,2,1)='1' )/6), 0) as mcount,
              nvl(round(a.range*(select count(*) from online_app where yn_sel in ('1') and year=a.year and class_no=a.class_no and term=a.term and substr(id,2,1)='2' )/6), 0) as fcount,
              nvl ((
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
                ) AS rcount,
                a.IS_ASSESS,
                a.IS_MIXED,
                a.policy_class,
                a.map1,
                a.map2,
                a.map3,
                a.map4,
                a.map5,
                a.map6,
                a.map7,
                a.map8,
                sc.item_id
                    from `require` a
                LEFT JOIN second_category sc
                        ON a.type=sc.parent_id AND a.beaurau_id=sc.item_id
                LEFT JOIN code_table ct
                        ON a.type=ct.item_id AND type_id='23'
                LEFT JOIN (
                                select type, count(*) as brother_count FROM
                                (
                                    SELECT DISTINCT
                                                XR.type,
                                                xsc.name, xsc.item_id AS cate_id
                                        FROM `require` XR
                                        LEFT JOIN second_category xsc
                                                ON XR.type=xsc.parent_id AND XR.beaurau_id=xsc.item_id
                                        WHERE XR.class_status IN ('2', '3') AND XR.is_cancel =0  AND XR.year=".$this->db->escape(addslashes($queryYear))." AND END_DATE1 BETWEEN ".$this->db->escape(addslashes($queryStartDate))." AND ".$this->db->escape(addslashes($queryEndDate))."
                                ) zz
                                group by type
                        ) X
                        ON a.type=X.type
                where
                a.year=".$this->db->escape(addslashes($queryYear)).$where." AND a.class_status IN ('2', '3') AND a.type not in ('C','O') AND a.IS_CANCEL = 0 
                                AND a.END_DATE1 BETWEEN ".$this->db->escape(addslashes($queryStartDate))." AND ".$this->db->escape(addslashes($queryEndDate))."
                order by
                        a.type,
                        sc.item_id,
                        a.year, a.class_no, a.term 
                      ) Z order by type , NO2 ";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function csvexport($filename, $query_start_date, $query_end_date, $searchTopic, $data)
    {
        $filename = iconv("UTF-8", "BIG5", '統計報表-各類班期結訓人數.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "各類班期結訓人數\r\n");
        //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

        echo iconv("UTF-8", "BIG5", "系列,");
        echo iconv("UTF-8", "BIG5", "單位類別,");

        if ($searchTopic == '1') {
            echo iconv("UTF-8", "BIG5", "策略主題,");
        }

        echo iconv("UTF-8", "BIG5", "班期名稱/期別,");
        echo iconv("UTF-8", "BIG5", "班期性質,");
        echo iconv("UTF-8", "BIG5", "期數,");

        echo iconv("UTF-8", "BIG5", "結訓人數,");
        echo iconv("UTF-8", "BIG5", "結訓(男),");
        echo iconv("UTF-8", "BIG5", "結訓(女),");
        echo iconv("UTF-8", "BIG5", "訓練期程,");
        echo iconv("UTF-8", "BIG5", "訓練人天次,");

        echo iconv("UTF-8", "BIG5", "人天次(男),");
        echo iconv("UTF-8", "BIG5", "人天次(女),");
        echo iconv("UTF-8", "BIG5", "環教班期,");
        echo iconv("UTF-8", "BIG5", "政策行銷班期,");
        echo iconv("UTF-8", "BIG5", "退休人員數\r\n");


        foreach ($data as $val) {
            echo iconv("UTF-8", "BIG5", $val['series']) . ',';
            echo iconv("UTF-8", "BIG5", $val['description']) . ',';

            if ($searchTopic == '1') {

                if ($val['map1'] == '1')
                    echo iconv("UTF-8", "BIG5", "A營造永續環境,");
                elseif ($val['map2'] == '1')
                    echo iconv("UTF-8", "BIG5", "B健全都市發展,");
                elseif ($val['map3'] == '1')
                    echo iconv("UTF-8", "BIG5", "C發展多元文化,");
                elseif ($val['map4'] == '1')
                    echo iconv("UTF-8", "BIG5", "D優化產業勞動,");
                elseif ($val['map5'] == '1')
                    echo iconv("UTF-8", "BIG5", "E強化社會支持,");
                elseif ($val['map6'] == '1')
                    echo iconv("UTF-8", "BIG5", "F打造優質教育,");
                elseif ($val['map7'] == '1')
                    echo iconv("UTF-8", "BIG5", "G精進健康安全,");
                elseif ($val['map8'] == '1')
                    echo iconv("UTF-8", "BIG5", "H精實良善治理,");
                else
                    echo iconv("UTF-8", "BIG5", ",");
            }


            echo iconv("UTF-8", "BIG5", $val['class_name'] . '(第'.$val['term'].'期)') .',';

            if ('1' == $val['IS_ASSESS'] && '1' == $val['IS_MIXED']) {
                echo iconv("UTF-8", "BIG5", "混成,");
            } else if ('1' == $val['IS_ASSESS']) {
                echo iconv("UTF-8", "BIG5", "考核,");
            }
            else {
                echo iconv("UTF-8", "BIG5", ",");
            }

            echo iconv("UTF-8", "BIG5", 1) . ',';

            echo iconv("UTF-8", "BIG5", $val['gcount']) . ',';
            echo iconv("UTF-8", "BIG5", $val['gcountm']) . ',';
            echo iconv("UTF-8", "BIG5", $val['gcountf']) . ',';
            echo iconv("UTF-8", "BIG5", $val['range']) . ',';
            echo iconv("UTF-8", "BIG5", $val['lcount']) . ',';

            echo iconv("UTF-8", "BIG5", $val['mcount']) . ',';
            echo iconv("UTF-8", "BIG5", $val['fcount']) . ',';
            echo iconv("UTF-8", "BIG5", $val['env_class']) . ',';
            echo iconv("UTF-8", "BIG5", $val['policy_class']) . ',';
            echo iconv("UTF-8", "BIG5", $val['rcount']) . ',';

            echo "\r\n";
        }
    }

    public function getCountData($datas)
    {
        $data = array();


        for ($i = 0; $i < sizeof($datas); $i++) {
            $data[] = $datas[$i];
        }

        // 計算小計、合計與總計
        $gcount  = 0;
        $gcountm = 0;
        $gcountf = 0;
        $range   = 0;
        $lcount  = 0;
        $mcount  = 0;
        $fcount  = 0;
        $rcount  = 0;

        $gcount1  = 0;
        $gcountm1 = 0;
        $gcountf1 = 0;
        $range1   = 0;
        $lcount1  = 0;
        $mcount1  = 0;
        $fcount1  = 0;
        $rcount1  = 0;

        $gcount2  = 0;
        $gcountm2 = 0;
        $gcountf2 = 0;
        $range2   = 0;
        $lcount2  = 0;
        $mcount2  = 0;
        $fcount2  = 0;
        $rcount2  = 0;

        foreach ($data as $index => $row) {
            //小計
            $gcount1  += $row['gcount'];
            $gcountm1 += $row['gcountm'];
            $gcountf1 += $row['gcountf'];
            $range1   += $row['range'];
            $lcount1  += $row['lcount'];
            $mcount1  += $row['mcount'];
            $fcount1  += $row['fcount'];
            $rcount1  += $row['rcount'];
            //表示最後一筆（同類同局）
            if ($data[$index]['NO1D'] == '1') {
                //儲存小計
                $data[$index]['SUB_COUNT'] = array(
                    'gcount' => $gcount1,
                    'gcountm' => $gcountm1,
                    'gcountf' => $gcountf1,
                    'range' => $range1,
                    'lcount' => $lcount1,
                    'mcount' => $mcount1,
                    'fcount' => $fcount1,
                    'rcount' => $rcount1,
                );
                $gcount1  = 0;
                $gcountm1 = 0;
                $gcountf1 = 0;
                $range1   = 0;
                $lcount1  = 0;
                $mcount1  = 0;
                $fcount1  = 0;
                $rcount1  = 0;
            }

            //合計
            $gcount2  += $row['gcount'];
            $gcountm2 += $row['gcountm'];
            $gcountf2 += $row['gcountf'];
            $range2   += $row['range'];
            $lcount2  += $row['lcount'];
            $mcount2  += $row['mcount'];
            $fcount2  += $row['fcount'];
            $rcount2  += $row['rcount'];
            //表示最後一筆（同類）
            if ($data[$index]['NO2D'] == '1') {
                //儲存合計
                $data[$index]['CLASS_COUNT'] = array(
                    'gcount' => $gcount2,
                    'gcountm' => $gcountm2,
                    'gcountf' => $gcountf2,
                    'range' => $range2,
                    'lcount' => $lcount2,
                    'mcount' => $mcount2,
                    'fcount' => $fcount2,
                    'rcount' => $rcount2,
                );
                $gcount2  = 0;
                $gcountm2 = 0;
                $gcountf2 = 0;
                $range2   = 0;
                $lcount2  = 0;
                $mcount2  = 0;
                $fcount2  = 0;
                $rcount2  = 0;
            }

            // 總計
            $gcount  += $row['gcount'];
            $gcountm += $row['gcountm'];
            $gcountf += $row['gcountf'];
            $range   += $row['range'];
            $lcount  += $row['lcount'];
            $mcount  += $row['mcount'];
            $fcount  += $row['fcount'];
            $rcount  += $row['rcount'];
            if ($index === count($data) - 1) {
                $data[$index]['TOTAL_COUNT'] = array(
                    'gcount'  => $gcount,
                    'gcountm' => $gcountm,
                    'gcountf' => $gcountf,
                    'range'   => $range,
                    'lcount'  => $lcount,
                    'mcount'  => $mcount,
                    'fcount'  => $fcount,
                    'rcount'  => $rcount,
                );
            }
        }

        return $data;
    }
}
