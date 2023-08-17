<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Bureau_course_time_model extends Common_model
{
    public function getBureauCourseTimeData($year, $ssd, $sed)
    {

        $query_year = $year;
        $search_start_date = $ssd;
        $search_end_date = $sed;
        //$search_start_date = substr($search_start_date,0,4) . substr($search_start_date,4,2) . substr($search_start_date,6,2);
        //$search_end_date = substr($search_end_date,0,4) . substr($search_end_date,4,2) . substr($search_end_date,6,2);

        $data = $this->getCourseHoursInfos21h($query_year, $search_start_date, $search_end_date);

        return $data;

        //return $this->QueryToArray($data);

    }

    public function exportBureauCourseTimeData($year, $ssd, $sed)
    {

        $query_year = $year;
        $search_start_date = $ssd;
        $search_end_date = $sed;
        //$search_start_date = substr($search_start_date,0,4) . substr($search_start_date,4,2) . substr($search_start_date,6,2);
        //$search_end_date = substr($search_end_date,0,4) . substr($search_end_date,4,2) . substr($search_end_date,6,2);

        $data = $this->getCourseHoursInfos21h($query_year, $search_start_date, $search_end_date);

        $filename = date("Ymd") . '.csv';

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv('UTF-8', 'BIG5', "臺北市政府公務人員訓練處,");
        echo iconv('UTF-8', 'BIG5', "各局處參訓學員上課時數資料統計表\r\n");
        echo iconv('UTF-8', 'BIG5', "".htmlspecialchars($search_start_date, ENT_HTML5|ENT_QUOTES)."至".htmlspecialchars($search_end_date, ENT_HTML5|ENT_QUOTES).",\r\n");
        echo iconv('UTF-8', 'BIG5', "類別,");
        echo iconv('UTF-8', 'BIG5', "次類別,");
        echo iconv('UTF-8', 'BIG5', "班期名稱,");
        echo iconv('UTF-8', 'BIG5', "上課時數,");
        foreach ($data['bureau'] as $bureau) {
            echo iconv('UTF-8', 'BIG5', $bureau['NAME'] . ",");
        }
        echo iconv('UTF-8', 'BIG5', "\r\n");

        foreach ($data['rows'] as $val) {
            echo iconv('UTF-8', 'BIG5', $val['type_name']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['bname']) . ',';
            echo iconv('UTF-8', 'BIG5', "{$val['year']}年 {$val['class_name']} (第{$val['term']}期)") . ',';
            echo iconv('UTF-8', 'BIG5', $val['count']) . ',';
            foreach ($val['bureausInfo'] as $count) {
                echo iconv('UTF-8', 'BIG5', $count) . ',';
            }
            echo "\r\n";
        }

    }

    /**
     * 21h 各類班期局處結訓人員上課時數統計表
     *
     * @param integer $queryYear 年度過濾
     * @param string $queryStartDate 開始時間
     * @param string $queryEndDate 結束時間
     */
    public function getCourseHoursInfos21h($queryYear, $queryStartDate, $queryEndDate)
    {

        $time_sql = "date_format(END_DATE1 ,'%Y-%m-%d') between " . $this->db->escape(addslashes($queryStartDate)) . " AND " . $this->db->escape(addslashes($queryEndDate)) . "";

        // 加入局處
        $data['bureau'] = $this->getBureaus($queryStartDate, $queryEndDate);

        // 撈各個局處的資料
        $sql = "SELECT class_no, year, term, type, `range`, beaurau_id, COUNT(*) * `range` AS count FROM
				(
					SELECT
						r.class_no, r.year, r.term, r.type, r.range,
                        IFNULL(o.OU_GOV,CASE WHEN bc.bureau_level = 4 THEN bc.parent_id
							WHEN bc.bureau_level = 5 THEN
							( SELECT parent_id FROM
								 bureau WHERE bureau_id = bc.parent_id )
							ELSE bc.bureau_id END) AS beaurau_id
			        FROM `require` r
		            LEFT JOIN online_app oa
		            	ON r.year=oa.year AND r.term=oa.term AND r.class_no=oa.class_no
		            left outer join out_gov o on o.ID = oa.id
		            LEFT JOIN bureau bc
						ON bc.bureau_id=oa.ori_beaurau_id
		            WHERE
		            	r.year=".$this->db->escape(addslashes($queryYear))." AND r.class_status IN ('2', '3') AND oa.yn_sel='1' AND $time_sql
	            )a
	            GROUP BY
	            	class_no, year, term, type, `range`, beaurau_id";
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        // echo json_encode($rs);
        // return;

        $beaurauInfos = array();
        for ($i = 0; $i < sizeof($rs); $i++) {
            $finded = false;
            foreach ($data['bureau'] as $bureau) {
                if ($bureau['BUREAU_ID'] === $rs[$i]['beaurau_id']) {
                    $finded = true;
                    break;
                }
            }
            if (!$finded) {
                // 其他類別，找不到一級的機關代碼都算在其他類別
                if (strpos($rs[$i]['beaurau_id'], '379') != 0 or $rs[$i]['beaurau_id'] == null or strpos($rs[$i]['beaurau_id'], '379') == null) {
//                    echo '<div>' . (strpos($rs[$i]['BEAURAU_ID'], '379') . '|' . $rs[$i]['BEAURAU_ID'] . '|' . $rs[$i]['class_no'] . '|' . $rs[$i]['term'] . '|' . $rs[$i]['COUNT'] . '|' . $rs[$i]['class_no']) . '</div>';
                    $index = sprintf('%s@%s@%s@%s', $rs[$i]['year'], $rs[$i]['class_no'], $rs[$i]['term'], 'OTHER');
                    if (isset($beaurauInfos[$index])) {
                        $beaurauInfos[$index] += $rs[$i]['count'];
                    } else {
                        $beaurauInfos[$index] = 0;
                    }

                }
            } else {
                // 計算台北市一級機關類別
                $index = sprintf('%s@%s@%s@%s', $rs[$i]['year'], $rs[$i]['class_no'], $rs[$i]['term'], $rs[$i]['beaurau_id']);
                $beaurauInfos[$index] = $rs[$i]['count'];
            }
        }

        // 撈主分類、次分類、班期名稱、總人數
        $sql = "SELECT
			   		rank() over(partition by type, bname order by year, class_no, term DESC) AS NO1,
			 		rank() over(partition by type, bname order by year DESC, class_no DESC, term) AS NO1D,
					rank() over(partition by type order by bname, year, class_no, term DESC) AS NO2,
			       	rank() over(partition by type order by bname DESC, year DESC, class_no DESC, term) AS NO2D,
					z.*
				FROM (
						SELECT
							X.brother_count,
							ct.description AS type_name,
              				sc.name AS bname,
							r.class_name, r.class_no, r.year, r.term, r.type,
							IFNULL(h_cnt.count*r.range, 0) AS count
						FROM `require` r
						LEFT JOIN code_table ct
							ON ct.type_id='23' AND r.type=ct.item_id
			         	LEFT OUTER JOIN second_category sc
			         		ON r.beaurau_id=sc.item_id AND sc.parent_id=r.type
						LEFT JOIN
							(
								SELECT year, term, class_no,
                                        max(case
                                              when (select case
                                                             when a.bureau_level = 4 then
                                                              (select b.del_flag
                                                                 from bureau b
                                                                where b.bureau_id = a.parent_id
                                                                  and date_format(abolish_date, '%Y-%m-%d') < ".$this->db->escape(addslashes($queryStartDate)).")
                                                             when a.bureau_level = 5 then
                                                              (select (select d.del_flag
                                                                         from bureau d
                                                                        where d.bureau_id = c.parent_id
                                                                          and date_format(abolish_date, '%Y-%m-%d') <
                                                                              ".$this->db->escape(addslashes($queryStartDate)).")
                                                                 from bureau c
                                                                where c.bureau_id = a.parent_id)
                                                             else
                                                              a.del_flag
                                                           end
                                                      from bureau a
                                                     where bureau_id = oa.beaurau_id
                                                       and date_format(abolish_date, '%Y-%m-%d') < ".$this->db->escape(addslashes($queryStartDate)).") = 'C' then
                                               0
                                              else
                                               1
                                            end) * COUNT(*) AS count
								FROM online_app oa
								WHERE oa.yn_sel='1'
								GROUP BY year, term, class_no
							) h_cnt
							ON h_cnt.year=r.year AND h_cnt.term=r.term AND h_cnt.class_no=r.class_no
		       			LEFT JOIN (
			            		SELECT type, count(*) as brother_count FROM
			            		(
				            		SELECT DISTINCT
				            			r.type, r.beaurau_id
				            		FROM `require` r
							        WHERE r.year=".$this->db->escape(addslashes($queryYear))." AND r.class_status IN ('2', '3') AND r.beaurau_id IS NOT NULL AND $time_sql
						        )a
						        group by type
			            	) X
			            	ON r.type=X.type
						WHERE
				            r.year=".$this->db->escape(addslashes($queryYear))." AND r.class_status IN ('2', '3') AND r.beaurau_id IS NOT NULL AND $time_sql
						ORDER BY
							r.type, sc.name, r.year, r.class_no, r.term DESC
					) z";

        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);

        //填資料
        $data['rows'] = array();
        // 計算小計、合計與總計
        $count = 0;
        $count1 = 0;
        $count2 = 0;
        foreach ($data['bureau'] as $bureau) {
            $bureauConter['subCounts'][$bureau['BUREAU_ID']] = 0;
            $bureauConter['classCounts'][$bureau['BUREAU_ID']] = 0;
            $bureauConter['totalCounts'][$bureau['BUREAU_ID']] = 0;
        }
        for ($i = 0; $i < sizeof($rs); $i++) {
            // 填入各局處的資料
            $rs[$i]['bureausInfo'] = array();
            foreach ($data['bureau'] as $bureau) {
                $index = sprintf(
                    '%s@%s@%s@%s',
                    $rs[$i]['year'],
                    $rs[$i]['class_no'],
                    $rs[$i]['term'],
                    $bureau['BUREAU_ID']
                );
                if (isset($beaurauInfos[$index])) {
                    //班-局處
                    $rs[$i]['bureausInfo'][] = $beaurauInfos[$index];
                    // 累積小計
                    $bureauConter['subCounts'][$bureau['BUREAU_ID']] += $beaurauInfos[$index];
                    $bureauConter['classCounts'][$bureau['BUREAU_ID']] += $beaurauInfos[$index];
                    $bureauConter['totalCounts'][$bureau['BUREAU_ID']] += $beaurauInfos[$index];
                } else {
                    $rs[$i]['bureausInfo'][] = '-';
                }
            }

            //小計
            $count1 += $rs[$i]['count'];
            //表示最後一筆（同類同局）
            if ($rs[$i]['NO1'] == '1') {
                //儲存小計
                $rs[$i]['SUB_COUNT'] = array(
                    'count' => $count1,
                );
                $rs[$i]['subCounts'] = $bureauConter['subCounts'];
                // 歸零
                foreach ($data['bureau'] as $bureau) {
                    $bureauConter['subCounts'][$bureau['BUREAU_ID']] = 0;
                }
                $count1 = 0;
            }

            //合計
            $count2 += $rs[$i]['count'];
            //表示最後一筆（同類）
            if ($rs[$i]['NO2'] == '1') {
                //儲存合計
                $rs[$i]['CLASS_COUNT'] = array(
                    'count' => $count2,
                );
                $rs[$i]['classCounts'] = $bureauConter['classCounts'];
                // 歸零
                foreach ($data['bureau'] as $bureau) {
                    $bureauConter['classCounts'][$bureau['BUREAU_ID']] = 0;
                }
                $count2 = 0;
            }

            // 總計
            $count += $rs[$i]['count'];

            $data['rows'][] = $rs[$i];
        }

        // 總計
        if (sizeof($data['rows']) != 0) {
            // 總計
            $data['rows'][sizeof($data['rows']) - 1]['TOTAL_COUNT'] = array(
                'count' => $count,
            );
            $data['rows'][sizeof($data['rows']) - 1]['totalCounts'] = $bureauConter['totalCounts'];
        }

        return $data;
    }

    /**
     *
     * Enter description here ...
     */
    public function getBureaus($queryStartDate, $queryEndDate)
    {
        // 抓局處 2012/10/04 宜賢說要顯示所有台北市的一級機關
        // bc.bureau_id, sc.sname|| ' '||bc.bureau_id AS name
        $sql = "SELECT DISTINCT
					bc.bureau_id as BUREAU_ID, sc.short_name AS NAME
				FROM bureau bc
				LEFT JOIN second_category sc
					ON bc.bureau_id=sc.item_id
				WHERE
					sc.short_name IS NOT NULL AND bc.bureau_level=3
					and (bc.abolish_date is null or date_format(bc.abolish_date,'%Y-%m-%d') between " . $this->db->escape(addslashes($queryStartDate)) . " and " . $this->db->escape(addslashes($queryEndDate)) . "  or date_format(bc.abolish_date,'%Y-%m-%d')>=" . $this->db->escape(addslashes($queryEndDate)) . " )
					and bureau_id != '379920000Z'
					ORDER BY bureau_id";
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);

        // 強制先加入台北市政府
        $bureau[] = array(
            'BUREAU_ID' => '379000000A',
            //'NAME'=>'臺北市政府 379000000A'
            'NAME' => '臺北市政府',
        );
        //填資料
        for ($i = 0; $i < sizeof($rs); $i++) {
            $bureau[] = $rs[$i];
        }
        // 加入其他
        $bureau[] = array(
            'BUREAU_ID' => 'OTHER',
            'NAME' => '其他',
        );

        return $bureau;
    }

}
