<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Bureau_participate_count_model extends Common_model
{
    public function getBureauParticipateCountData($year, $ssd, $sed, $changemode)
    {

        $query_year = $year;
        $search_start_date = $ssd;
        $search_end_date = $sed;
        //$search_start_date = substr($search_start_date,0,4) . substr($search_start_date,4,2) . substr($search_start_date,6,2);
        //$search_end_date = substr($search_end_date,0,4) . substr($search_end_date,4,2) . substr($search_end_date,6,2);

        $data = $this->getModifyInfoByDateRange($query_year, $search_start_date, $search_end_date, $changemode);

        return $data;

        //return $this->QueryToArray($data);

    }

    public function exportBureauParticipateCountData($year, $ssd, $sed, $changemode)
    {

        $query_year = $year;
        $search_start_date = $ssd;
        $search_end_date = $sed;
        //$search_start_date = substr($search_start_date,0,4) . substr($search_start_date,4,2) . substr($search_start_date,6,2);
        //$search_end_date = substr($search_end_date,0,4) . substr($search_end_date,4,2) . substr($search_end_date,6,2);

        $data = $this->getModifyInfoByDateRange($query_year, $search_start_date, $search_end_date, $changemode);

        $filename = date("Ymd") . '.csv';

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv('UTF-8', 'BIG5', "臺北市政府公務人員訓練處,");
        echo iconv('UTF-8', 'BIG5', "月各類訓練班期統計資料表\r\n");
        echo iconv('UTF-8', 'BIG5', "".htmlspecialchars($search_start_date, ENT_HTML5|ENT_QUOTES)."至".htmlspecialchars($search_end_date, ENT_HTML5|ENT_QUOTES).",\r\n");
        echo iconv('UTF-8', 'BIG5', "系列,");
        echo iconv('UTF-8', 'BIG5', "班期名稱,");
        echo iconv('UTF-8', 'BIG5', "期別,");
        echo iconv('UTF-8', 'BIG5', "開班日期,");
        echo iconv('UTF-8', 'BIG5', "承辦人,");
        for ($i = 0; $i < sizeof($data['bureau']) - 1; $i++) {
            if (!is_null($data['bureau'][$i]['NAME']) && !empty($data['bureau'][$i]['NAME'])) {
                $bname = $data['bureau'][$i]['NAME'];
            } else {
                $bname = $data['bureau'][$i]['BUREAU_ID'];
            }
            //互調
            if ($changemode['cs'] == '1') {
                echo iconv('UTF-8', 'BIG5', $bname . "-互調,");
            }
            //換員
            if ($changemode['cp'] == '1') {
                echo iconv('UTF-8', 'BIG5', $bname . "-換員,");
            }
            //換期
            if ($changemode['ct'] == '1') {
                echo iconv('UTF-8', 'BIG5', $bname . "-換期,");
            }
            //取消
            if ($changemode['rm'] == '1') {
                echo iconv('UTF-8', 'BIG5', $data['bureau'][$i]['NAME'] . "-取消,");
            }
        }
        echo iconv('UTF-8', 'BIG5', "\r\n");

        foreach ($data['rows'] as $val) {
            echo iconv('UTF-8', 'BIG5', $val['series']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['class_name']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['term']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['start_date1']) . ',';
            echo iconv('UTF-8', 'BIG5', $val['worker_name']) . ',';
            for ($i = 0; $i < sizeof($val['bureausInfo']) - sizeof($changemode); $i++) {
                echo iconv('UTF-8', 'BIG5', $val['bureausInfo'][$i]) . ',';
            }
            echo "\r\n";
        }

    }

    /**
     * 11I 各局處參訓學員異動情形統計表
     *
     * @param integer $queryYear 年度過濾
     * @param string $queryStartDate 開始時間
     * @param string $queryEndDate 結束時間
     */
    public function getModifyInfoByDateRange($queryYear, $queryStartDate, $queryEndDate, $changemode)
    {
        $data['rows'] = array();
        $time_sql = "date_format(r.END_DATE1 ,'%Y-%m-%d') between " . $this->db->escape(addslashes($queryStartDate)) . " AND " . $this->db->escape(addslashes($queryEndDate)) . "";

        // 加入局處
        $data['bureau'] = $this->getBureaus($queryStartDate, $queryEndDate);
        // 加入「小計」欄位
        $data['bureau'][] = array('NAME' => '小計', 'BUREAU_ID' => 'TOTAL');

        $sql = "SELECT
					r.class_no, r.year, r.term, r.type,
					smc.modify_item, smc.beaurau_id, smc.count
		        FROM `require` r
	            LEFT JOIN
	            	(
	            		SELECT
	            			sm.year, sm.class_no, sm.term, sm.modify_item, bc.parent_id AS beaurau_id, COUNT(*) AS count
	            		FROM stud_modifylog sm

				        LEFT JOIN (
							    SELECT
							        bureau_id,
							        CASE
			                            WHEN bureau_level <= 3 THEN bureau_id
			                            ELSE parent_id
			                        END AS parent_id
							    FROM bureau
		                    ) bc
					        ON bc.bureau_id=sm.s_beaurau_id
	            		GROUP BY sm.year, sm.class_no, sm.term, sm.modify_item, bc.parent_id
	            	) smc
	            	ON r.year=smc.year AND r.class_no=smc.class_no AND r.term=smc.term
	            WHERE
	            	r.year=".$this->db->escape(addslashes($queryYear))." AND r.class_status IN ('2', '3') AND
					$time_sql
	            ORDER BY
	            	r.type, r.class_no, r.term
				";

        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        // echo json_encode($rs);
        // return;
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
                $index = sprintf('%s@%s@%s@%s@', $rs[$i]['year'], $rs[$i]['class_no'], $rs[$i]['term'], 'OTHER') . $rs[$i]['modify_item'];
                if (isset($data['rowsDetail'][$index])) {
                    $data['rowsDetail'][$index] += $rs[$i]['count'];
                } else {
                    $data['rowsDetail'][$index] = 0;
                }

            } else {
                // 計算台北市一級機關類別
                $index = sprintf('%s@%s@%s@%s@', $rs[$i]['year'], $rs[$i]['class_no'], $rs[$i]['term'], $rs[$i]['beaurau_id']) . $rs[$i]['modify_item'];
                $data['rowsDetail'][$index] = $rs[$i]['count'];
            }
        }

        $sql = "SELECT
					rank() over(partition by type order by year DESC, class_no DESC, term DESC) AS NO1,
					rank() over(partition by type order by year, class_no, term) AS NO1D,
                    Z.type,Z.class_name,Z.class_no,Z.year,Z.term,
                    DATE_FORMAT(Z.start_date1,'%Y/%m/%d') as start_date1,DATE_FORMAT(Z.end_date1,'%Y/%m/%d') as end_date1,
                    Z.series,Z.worker_name FROM (
					SELECT
						r.type, r.class_name, r.class_no, r.year, r.term, r.start_date1, r.end_date1,
						ct.description AS series,
						vaa.name AS worker_name
			        FROM `require` r
			        LEFT JOIN require_list rl
			            ON rl.class_no=r.class_no AND rl.year=r.year AND rl.term=r.term
		            LEFT JOIN code_table ct
		            	ON r.type=ct.item_id AND type_id='23'
		            LEFT JOIN BS_user vaa
		            	ON r.worker=vaa.idno
		            WHERE
		                rl.mail_mag_count > 0
		            	AND r.year=".$this->db->escape(addslashes($queryYear))." AND r.class_status IN ('2', '3') AND
						$time_sql
		            ORDER BY
		            	r.type, r.class_no, r.term
				) Z";
		
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        // echo json_encode($rs);
        // return;
        $data['hasdata']=0;
        //填資料
        $data['totalInfos'] = array();
        for ($k = 0; $k < sizeof($rs); $k++) {
            $arr=$rs[$k];
			// 加入各局處的統計值
			$bureausInfo = array();
			$totalCount['cs'] = 0;
			$totalCount['cp'] = 0;
			$totalCount['ct'] = 0;
			$totalCount['rm'] = 0;
			for ($i = 0; $i < count($data['bureau'])-1; $i++) {
				$bureau = $data['bureau'][$i];
				$index = sprintf('%s@%s@%s@%s@', $arr['year'], $arr['class_no'], $arr['term'], $bureau['BUREAU_ID']);
				// 互調
		  		if ($changemode['cs'] == '1') {
		  			if (!isset($data['totalInfos'][$bureau['BUREAU_ID']]['cs'])) {
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['cs'] = 0;
				  	}
		  			if (isset($data['rowsDetail'][$index.'互調'])) {
		  				$bureausInfo[] = $data['rowsDetail'][$index.'互調'];
		  				$totalCount['cs'] += $data['rowsDetail'][$index.'互調'];
		  				// 計算類別小計
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['cs'] += $data['rowsDetail'][$index.'互調'];
		  			} else {
		  				$bureausInfo[] = '-';
		  			}
		  		}
				// 換員
		  		if ($changemode['cp'] == '1') {
		  			if (!isset($data['totalInfos'][$bureau['BUREAU_ID']]['cp'])) {
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['cp'] = 0;
				  	}
		  			if (isset($data['rowsDetail'][$index.'換員'])) {
		  				$bureausInfo[] = $data['rowsDetail'][$index.'換員'];
		  				$totalCount['cp'] += $data['rowsDetail'][$index.'換員'];
		  				// 計算類別小計
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['cp'] += $data['rowsDetail'][$index.'換員'];
		  			} else {
		  				$bureausInfo[] = '-';
		  			}
		  		}
				// 換期
		  		if ($changemode['ct'] == '1') {
		  			if (!isset($data['totalInfos'][$bureau['BUREAU_ID']]['ct'])) {
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['ct'] = 0;
				  	}
		  			if (isset($data['rowsDetail'][$index.'換期'])) {
		  				$bureausInfo[] = $data['rowsDetail'][$index.'換期'];
		  				$totalCount['ct'] += $data['rowsDetail'][$index.'換期'];
		  				// 計算類別小計
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['ct'] += $data['rowsDetail'][$index.'換期'];
		  			} else {
		  				$bureausInfo[] = '-';
		  			}
		  		}
				// 取消
		  		if ($changemode['rm'] == '1') {
		  			if (!isset($data['totalInfos'][$bureau['BUREAU_ID']]['rm'])) {
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['rm'] = 0;
				  	}
		  			if (isset($data['rowsDetail'][$index.'取消'])) {
		  				$bureausInfo[] = $data['rowsDetail'][$index.'取消'];
		  				$totalCount['rm'] += $data['rowsDetail'][$index.'取消'];
		  				// 計算類別小計
				  		$data['totalInfos'][$bureau['BUREAU_ID']]['rm'] += $data['rowsDetail'][$index.'取消'];
		  			} else {
		  				$bureausInfo[] = '-';
		  			}
		  		}
            }
            $cnt=0;
			// 加入互調「小計」欄位
            if ($changemode['cs'] == '1') {
                $cnt++;
                $bureausInfo[] = $totalCount['cs'];
                $data['cs']='1';
            }
            // 加入換員「小計」欄位
            if ($changemode['cp'] == '1') {
                $cnt++;
                $bureausInfo[] = $totalCount['cp'];
                $data['cp']='1';
            }
            // 加入換期「小計」欄位
            if ($changemode['ct'] == '1') {
                $cnt++;
                $bureausInfo[] = $totalCount['ct'];
                $data['ct']='1';
            }
            // 加入取消「小計」欄位
            if ($changemode['rm'] == '1') {
                $cnt++;
                $bureausInfo[] = $totalCount['rm'];
                $data['rm']='1';
            }
			$arr['bureausInfo'] = $bureausInfo;

	  		// 表示已經到這個分類的最後一筆，就把分類小計放這裡
	  		if ($arr['NO1D']==='1') {
	  			$arr['totalInfos'] = array();
	  			foreach ($data['totalInfos'] as $totalInfo) {
	  				foreach ($totalInfo as $count) {
	  					$arr['totalInfos'][] = $count;
	  				}
	  			}
	  			// 計算分類小計
	  			$sumTotal = array();
	  			for ($j = 0; $j < $cnt; $j++) {
	  				$sumTotal[$j] = 0;
	  			}
	  			for ($i = 0; $i < count($arr['totalInfos']); $i+=$cnt) {
	  				for ($j = 0; $j < $cnt; $j++) {
	  					$sumTotal[$j] += $arr['totalInfos'][$i+$j];
	  				}
	  			}
	  			for ($j = 0; $j < $cnt; $j++) {
	  				$arr['totalInfos'][] = $sumTotal[$j];
	  			}
	  			$data['totalInfos'] = array();
	  		}
	  		
			// 計算總計
	  		if (isset($arr['totalInfos'])) {
	  			foreach ($arr['totalInfos'] as $index=>$count) {
	  				if ( !isset($data['sumTotalCount'][$index]) ) {
                          $data['sumTotalCount'][$index] = 0;
	  				}
	  				$data['sumTotalCount'][$index] += $count;
	  			}
	  		}
	  		
              $data['rows'][] = $arr;
              $data['cnt']=$cnt;
              $data['bcnt']=count($data['bureau']);
              $data['hasdata']=1;
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
					bc.BUREAU_ID, sc.short_name AS NAME
				FROM bureau bc
				LEFT JOIN second_category sc
					ON bc.BUREAU_ID=sc.item_id
				WHERE
					sc.short_name IS NOT NULL AND bc.bureau_level=3
					and (bc.abolish_date is null or date_format(bc.abolish_date,'%Y-%m-%d') between " . $this->db->escape(addslashes($queryStartDate)) . " and " . $this->db->escape(addslashes($queryEndDate)) . "  or date_format(bc.abolish_date,'%Y-%m-%d')>=" . $this->db->escape(addslashes($queryEndDate)) . " )
					and BUREAU_ID != '379920000Z'
					ORDER BY BUREAU_ID";
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
