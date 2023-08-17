<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_finish_attribute_count_model extends Common_model
{
        function getTrainingStudentStatistics ($queryYear, $queryStartDate, $queryEndDate)
	{
		$queryYear = $this->db->escape(addslashes($queryYear));
		$queryStartDate = $this->db->escape(addslashes($queryStartDate));
		$queryEndDate = $this->db->escape(addslashes($queryEndDate));

		// 學歷區分
                $degreesql = "SELECT item_id, description FROM code_table WHERE type_id='04' ORDER BY item_id DESC";
         
                $Degreequery = $this->db->query($degreesql);

                $DegreeData = $this->QueryToArray($Degreequery);
                

		//填資料
		$data['degree'] = array();
		$degreeSql = '';
                
                for ($i = 0; $i < sizeof($DegreeData); $i++)
                {
			$data['degree'][] = $DegreeData[$i];
	  		$degreeSql .= sprintf("
  					(
		            	select count(*)
		            	from online_app oa
		            	LEFT JOIN BS_user vaa
		            		ON oa.id=vaa.idno
		            	where
		            		oa.yn_sel IN ('1') AND oa.year=a.year AND oa.class_no=a.class_no AND oa.term=a.term AND
		            		oa.CO_EDUCATION=".$DegreeData[$i]['item_id']."
		            ) as degree_".$DegreeData[$i]['item_id'].", " );
		}

		// 現職區分
                $jobsql = "SELECT item_id, description FROM code_table WHERE type_id='03'";
                
                $jobquery = $this->db->query($jobsql);

                $jobData = $this->QueryToArray($jobquery);
        

		//填資料
		$data['job'] = array();
                $jobSql = '';
        
		for ($i = 0; $i < sizeof($jobData); $i++) {
			$data['job'][] = $jobData[$i];
			if($jobData[$i]['item_id'] == '01'){
				$new_item_id = "AND oa.CO_POSITION in ('01','1')";
			} elseif($jobData[$i]['item_id'] == '02'){
				$new_item_id = "AND oa.CO_POSITION in ('02','2')";
			} elseif($jobData[$i]['item_id'] == '03'){
				$new_item_id = "AND oa.CO_POSITION in ('03','3')";
			} elseif($jobData[$i]['item_id'] == '04'){
				$new_item_id = "AND oa.CO_POSITION in ('04','4')";
			} elseif($jobData[$i]['item_id'] == '05'){
				$new_item_id = "AND oa.CO_POSITION in ('05','5')";
			} elseif($jobData[$i]['item_id'] == '06'){
				$new_item_id = "AND oa.CO_POSITION in ('06','6')";
			} elseif($jobData[$i]['item_id'] == '07'){
				$new_item_id = "AND oa.CO_POSITION in ('07','7')";
			} elseif($jobData[$i]['item_id'] == '08'){
				$new_item_id = "AND oa.CO_POSITION in ('08','8')";
			} elseif($jobData[$i]['item_id'] == '09'){
				$new_item_id = "AND oa.CO_POSITION in ('09','9')";
			} elseif($jobData[$i]['item_id'] == '10'){
				$new_item_id = "AND oa.CO_POSITION = '10'";
			} elseif($jobData[$i]['item_id'] == '11'){
				$new_item_id = "AND (oa.CO_POSITION = '11' or oa.CO_POSITION is null)";
			}

	  		$jobSql .= sprintf("
	  					(
			            	select count(*)
			            	from online_app oa
			            	LEFT JOIN BS_user vaa
			            		ON oa.id=vaa.idno
			            	where
			            		oa.yn_sel IN ('1') AND oa.year=a.year AND oa.class_no=a.class_no AND oa.term=a.term ".$new_item_id."
			            ) as job_".$jobData[$i]['item_id'].", " );
		}


		$time_sql = "END_DATE1 between " . $queryStartDate . " AND " . $queryEndDate . "";


                
		$sql = "SELECT
            rank() over(partition by type, description order by year, class_no, term) AS NO1,
            rank() over(partition by type, description order by year DESC, class_no DESC, term DESC) AS NO1D,
            rank() over(partition by type order by description, year, class_no, term) AS NO2,
            rank() over(partition by type order by description DESC, year DESC, class_no DESC, term DESC) AS NO2D,
                Z.* FROM (
                        select
                                a.type, a.class_name, a.class_no, a.year, a.term, IFNULL(a.range, 0) as `range`,
                                X.brother_count,
                                sc.name as description,
                                ct.description AS series,
                            IFNULL( (select count(*) from online_app where yn_sel IN ('1') and year=a.year and class_no=a.class_no and term=a.term), 0) as gcount,
                            IFNULL( (select count(*) from online_app join BS_user on online_app.id = BS_user.idno where online_app.yn_sel IN ('1') and online_app.year=a.year and online_app.class_no=a.class_no and online_app.term=a.term AND BS_user.gender = 'M'), 0) as gcountm,
                            IFNULL( (select count(*) from online_app join BS_user on online_app.id = BS_user.idno where online_app.yn_sel IN ('1') and online_app.year=a.year and online_app.class_no=a.class_no and online_app.term=a.term AND BS_user.gender = 'F'), 0) as gcountf,
                            IFNULL( round(a.`range`*(select count(*) from online_app where yn_sel IN ('1') and year=a.year and class_no=a.class_no and term=a.term)/6), 0) as lcount,
                            IFNULL( (select count(*) from online_app join BS_user on online_app.id = BS_user.idno where online_app.yn_sel IN ('1') and online_app.year=a.year and online_app.class_no=a.class_no and online_app.term=a.term and BS_user.gender = 'M'), 0) as reg_mcount,
                            IFNULL( (select count(*) from online_app join BS_user on online_app.id = BS_user.idno where online_app.yn_sel IN ('1') and online_app.year=a.year and online_app.class_no=a.class_no and online_app.term=a.term and BS_user.gender = 'F'), 0) as reg_fcount,
                            {$degreeSql} {$jobSql}
                    (
                            select count(*)
                            from online_app oa
                            LEFT JOIN BS_user vaa
                                    ON oa.id=vaa.idno
                            where
                                    oa.yn_sel IN ('1') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term and
                                    FLOOR(oa.age) <= 20
                    ) as ycount_0_20,
                    (
                            select count(*)
                            from online_app oa
                            LEFT JOIN BS_user vaa
                                    ON oa.id=vaa.idno
                            where
                                    oa.yn_sel IN ('1') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term and
                                    FLOOR(oa.age) > 20 AND
                                    FLOOR(oa.age) <= 30
                    ) as ycount_21_30,
                    (
                            select count(*)
                            from online_app oa
                            LEFT JOIN BS_user vaa
                                    ON oa.id=vaa.idno
                            where
                                    oa.yn_sel IN ('1') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term and
                                    FLOOR(oa.age) > 30 AND
                                    FLOOR(oa.age) <= 40
                    ) as ycount_31_40,
                    (
                            select count(*)
                            from online_app oa
                            LEFT JOIN BS_user vaa
                                    ON oa.id=vaa.idno
                            where
                                    oa.yn_sel IN ('1') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term and
                                    FLOOR(oa.age) > 40 AND
                                    FLOOR(oa.age) <= 50
                    ) as ycount_41_50,
                    (
                            select count(*)
                            from online_app oa
                            LEFT JOIN BS_user vaa
                                    ON oa.id=vaa.idno
                            where
                                    oa.yn_sel IN ('1') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term and
                                    FLOOR(oa.age) > 50 AND
                                    FLOOR(oa.age) <= 60
                    ) as ycount_51_60,
                    (
                            select count(*)
                            from online_app oa
                            LEFT JOIN BS_user vaa
                                    ON oa.id=vaa.idno
                            where
                                    oa.yn_sel IN ('1') and oa.year=a.year and oa.class_no=a.class_no and oa.term=a.term and
                                    FLOOR(oa.age) > 60
                    ) as ycount_60
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
                                            xsc.name, xsc.item_id
                                    FROM `require` XR
                                    LEFT JOIN second_category xsc
                                            ON XR.type=xsc.parent_id AND XR.beaurau_id=xsc.item_id
                                    WHERE  XR.class_status IN ('2', '3')  AND XR.year=".$queryYear." AND $time_sql
                            ) zz
                            group by type
                    ) X
                    ON a.type=X.type
                where
                    a.class_status IN ('2', '3') 
                  AND $time_sql AND a.year=".$queryYear."
                order by
                    a.type,
                    sc.item_id,
                    a.year, a.class_no, a.term
                ) Z order by  type , NO2 ";

        $datassql = $this->db->query($sql);

        // $data['rows'] = $this->QueryToArray($datassql);
        $resultJobData = $this->QueryToArray($datassql);
        
        //$data[] = array();
        
	//$data['rows'] = array();


       //填資料
		// for ($i = 0; $i < sizeof($datas); $i++) {
                //         $data['rows'][] = $datas[$i];
                // }

		// 計算小計、合計與總計
		$gcount  = 0;
		$gcountm = 0;
		$gcountf = 0;
		$range   = 0;
		$lcount  = 0;
		$ycount_0_20   = 0;
		$ycount_21_30  = 0;
		$ycount_31_40  = 0;
		$ycount_41_50  = 0;
		$ycount_51_60  = 0;
                $ycount_60     = 0;
                
		// 歸零現職計數器
		foreach ($data['job'] as $job) {
                        $jobCounts[$job['item_id']] = 0;
		}
		// 歸零學歷計數器
		foreach ($data['degree'] as $degree) {
			$degreeCounts[$degree['item_id']] = 0;
		}

		$gcount1  = 0;
		$gcountm1 = 0;
		$gcountf1 = 0;
		$range1   = 0;
		$lcount1  = 0;
		$ycount_0_201   = 0;
		$ycount_21_301  = 0;
		$ycount_31_401  = 0;
		$ycount_41_501  = 0;
		$ycount_51_601  = 0;
		$ycount_601     = 0;
		// 歸零現職計數器
		foreach ($data['job'] as $job) {
			$jobCounts1[$job['item_id']] = 0;
		}
		// 歸零學歷計數器
		foreach ($data['degree'] as $degree) {
			$degreeCounts1[$degree['item_id']] = 0;
		}

		$gcount2  = 0;
		$gcountm2 = 0;
		$gcountf2 = 0;
		$range2   = 0;
		$lcount2  = 0;
		$ycount_0_202   = 0;
		$ycount_21_302  = 0;
		$ycount_31_402  = 0;
		$ycount_41_502  = 0;
		$ycount_51_602  = 0;
		$ycount_602     = 0;
		// 歸零現職計數器
		foreach ($data['job'] as $job) {
			$jobCounts2[$job['item_id']] = 0;
		}
		// 歸零學歷計數器
		foreach ($data['degree'] as $degree) {
			$degreeCounts2[$degree['item_id']] = 0;
                }
                


                for ($ji = 0 ; $ji < sizeof($resultJobData); $ji++) {
                        // foreach ($data['rows'] as $index=>$row) {

			// 把現職區分統計資料放到子項目中
			$resultJobData[$ji]['job'] = array();
			foreach ($data['job'] as $job) {
				$resultJobData[$ji]['job'][$job['item_id']] = $resultJobData[$ji]['job_'.$job['item_id']];
			}

			// 把學歷統計資料放到子項目中
			$resultJobData[$ji]['degree'] = array();
			foreach ($data['degree'] as $degree) {
				$resultJobData[$ji]['degree'][$degree['item_id']] = $resultJobData[$ji]['degree_'.$degree['item_id']];
			}

			//小計
			$gcount1  += $resultJobData[$ji]['gcount'];
			$gcountm1 += $resultJobData[$ji]['gcountm'];
			$gcountf1 += $resultJobData[$ji]['gcountf'];
			$range1   += $resultJobData[$ji]['range'];
			$lcount1  += $resultJobData[$ji]['lcount'];
			$ycount_0_201  += $resultJobData[$ji]['ycount_0_20'];
			$ycount_21_301 += $resultJobData[$ji]['ycount_21_30'];
			$ycount_31_401 += $resultJobData[$ji]['ycount_31_40'];
			$ycount_41_501 += $resultJobData[$ji]['ycount_41_50'];
			$ycount_51_601 += $resultJobData[$ji]['ycount_51_60'];
			$ycount_601    += $resultJobData[$ji]['ycount_60'];
			// 現職計數器
			foreach ($data['job'] as $job) {
				$jobCounts1[$job['item_id']] += $resultJobData[$ji]['job_'.$job['item_id']];
			}
			// 學歷計數器
			foreach ($data['degree'] as $degree) {
				$degreeCounts1[$degree['item_id']] += $resultJobData[$ji]['degree_'.$degree['item_id']];
			}

			//表示最後一筆（同類同局）
			if ($resultJobData[$ji]['NO1D']=='1') {
				//儲存小計
				$resultJobData[$ji]['SUB_COUNT'] = array(
					'gcount' => $gcount1,
					'gcountm' => $gcountm1,
					'gcountf' => $gcountf1,
					'range' => $range1,
					'lcount' => $lcount1,
					'ycount_0_20'  => $ycount_0_201,
					'ycount_21_30' => $ycount_21_301,
					'ycount_31_40' => $ycount_31_401,
					'ycount_41_50' => $ycount_41_501,
					'ycount_51_60' => $ycount_51_601,
					'ycount_60'    => $ycount_601,
					'jobCounts'    => $jobCounts1,
					'degreeCounts' => $degreeCounts1,
				);
				$gcount1  = 0;
				$gcountm1 = 0;
				$gcountf1 = 0;
				$range1   = 0;
				$lcount1  = 0;
				$ycount_0_201   = 0;
				$ycount_21_301  = 0;
				$ycount_31_401  = 0;
				$ycount_41_501  = 0;
				$ycount_51_601  = 0;
				$ycount_601     = 0;
				// 歸零現職計數器
				foreach ($data['job'] as $job) {
					$jobCounts1[$job['item_id']] = 0;
				}
				// 歸零學歷計數器
				foreach ($data['degree'] as $degree) {
					$degreeCounts1[$degree['item_id']] = 0;
				}
			}

			//合計
			$gcount2  += $resultJobData[$ji]['gcount'];
			$gcountm2 += $resultJobData[$ji]['gcountm'];
			$gcountf2 += $resultJobData[$ji]['gcountf'];
			$range2   += $resultJobData[$ji]['range'];
			$lcount2  += $resultJobData[$ji]['lcount'];
			$ycount_0_202  += $resultJobData[$ji]['ycount_0_20'];
			$ycount_21_302 += $resultJobData[$ji]['ycount_21_30'];
			$ycount_31_402 += $resultJobData[$ji]['ycount_31_40'];
			$ycount_41_502 += $resultJobData[$ji]['ycount_41_50'];
			$ycount_51_602 += $resultJobData[$ji]['ycount_51_60'];
			$ycount_602    += $resultJobData[$ji]['ycount_60'];
			// 現職計數器
			foreach ($data['job'] as $job) {
				$jobCounts2[$job['item_id']] += $resultJobData[$ji]['job_'.$job['item_id']];
			}
			// 學歷計數器
			foreach ($data['degree'] as $degree) {
				$degreeCounts2[$degree['item_id']] += $resultJobData[$ji]['degree_'.$degree['item_id']];
			}

			//表示最後一筆（同類）
			if ($resultJobData[$ji]['NO2D']=='1') {
				//儲存合計
				$resultJobData[$ji]['CLASS_COUNT'] = array(
					'gcount' => $gcount2,
					'gcountm' => $gcountm2,
					'gcountf' => $gcountf2,
					'range' => $range2,
					'lcount' => $lcount2,
					'ycount_0_20'  => $ycount_0_202,
					'ycount_21_30' => $ycount_21_302,
					'ycount_31_40' => $ycount_31_402,
					'ycount_41_50' => $ycount_41_502,
					'ycount_51_60' => $ycount_51_602,
					'ycount_60'    => $ycount_602,
					'jobCounts'    => $jobCounts2,
					'degreeCounts' => $degreeCounts2,
				);
				$gcount2  = 0;
				$gcountm2 = 0;
				$gcountf2 = 0;
				$range2   = 0;
				$lcount2  = 0;
				$ycount_0_202   = 0;
				$ycount_21_302  = 0;
				$ycount_31_402  = 0;
				$ycount_41_502  = 0;
				$ycount_51_602  = 0;
				$ycount_602     = 0;
				// 歸零現職計數器
				foreach ($data['job'] as $job) {
					$jobCounts2[$job['item_id']] = 0;
				}
				// 歸零學歷計數器
				foreach ($data['degree'] as $degree) {
					$degreeCounts2[$degree['item_id']] = 0;
				}
			}

			// 總計
			$gcount  += $resultJobData[$ji]['gcount'];
			$gcountm += $resultJobData[$ji]['gcountm'];
			$gcountf += $resultJobData[$ji]['gcountf'];
			$range   += $resultJobData[$ji]['range'];
			$lcount  += $resultJobData[$ji]['lcount'];
			$ycount_0_20  += $resultJobData[$ji]['ycount_0_20'];
			$ycount_21_30 += $resultJobData[$ji]['ycount_21_30'];
			$ycount_31_40 += $resultJobData[$ji]['ycount_31_40'];
			$ycount_41_50 += $resultJobData[$ji]['ycount_41_50'];
			$ycount_51_60 += $resultJobData[$ji]['ycount_51_60'];
			$ycount_60    += $resultJobData[$ji]['ycount_60'];
			// 現職計數器
			foreach ($data['job'] as $job) {
				$jobCounts[$job['item_id']] += $resultJobData[$ji]['job_'.$job['item_id']];
			}
			// 學歷計數器
			foreach ($data['degree'] as $degree) {
				$degreeCounts[$degree['item_id']] += $resultJobData[$ji]['degree_'.$degree['item_id']];
			}

			if ($ji===count($resultJobData)-1) {
				$resultJobData[$ji]['TOTAL_COUNT'] = array(
					'gcount'  => $gcount,
					'gcountm' => $gcountm,
					'gcountf' => $gcountf,
					'range'   => $range,
					'lcount'  => $lcount,
					'ycount_0_20'  => $ycount_0_20,
					'ycount_21_30' => $ycount_21_30,
					'ycount_31_40' => $ycount_31_40,
					'ycount_41_50' => $ycount_41_50,
					'ycount_51_60' => $ycount_51_60,
					'ycount_60'    => $ycount_60,
					'jobCounts'    => $jobCounts,
					'degreeCounts' => $degreeCounts,
				);
			}
                }
                
                $data["jobInfo"] = $resultJobData;

		



		return $data;
	}



            



    public function csvexport($filename, $query_start_date, $query_end_date, $datas)
    {
        $filename = iconv("UTF-8", "BIG5", '統計報表-各類班期結訓人員屬性統計表.csv');

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "各類班期結訓人員屬性統計表\r\n");
        //echo iconv("UTF-8","BIG5",$query_start_date."至".$query_end_date."\r\n");

        echo iconv("UTF-8", "BIG5", "類別,");
        echo iconv("UTF-8", "BIG5", "次類別,");
        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "結訓人數,");
        echo iconv("UTF-8", "BIG5", "訓練期程,");

        echo iconv("UTF-8", "BIG5", "訓練人天次,");
        echo iconv("UTF-8", "BIG5", "男,");
		echo iconv("UTF-8", "BIG5", "女,");
		
        foreach ($datas["degree"] as $datadd){
			echo iconv("UTF-8", "BIG5", $datadd["description"]). ',';
		}
		
        echo iconv("UTF-8", "BIG5", "20以下,");
        echo iconv("UTF-8", "BIG5", "21-30,");
        echo iconv("UTF-8", "BIG5", "31-40,");
        echo iconv("UTF-8", "BIG5", "41-50,");
        echo iconv("UTF-8", "BIG5", "51-60,");
		echo iconv("UTF-8", "BIG5", "60以上,");
		
		foreach ($datas["job"] as $datajd){
			echo iconv("UTF-8", "BIG5", $datajd["description"]). ',';
		}
		
        echo "\r\n";
		
		

        foreach ($datas["jobInfo"] as $val) {
            echo iconv("UTF-8", "BIG5", $val["series"]) . ',';
            echo iconv("UTF-8", "BIG5", $val["description"]) . ',';
            echo iconv("UTF-8", "BIG5", $val["class_name"]."第".$val["term"]."期") . ',';
            echo iconv("UTF-8", "BIG5", $val["gcount"]) . ',';
			echo iconv("UTF-8", "BIG5", $val["range"]) . ',';
			
            echo iconv("UTF-8", "BIG5", $val["lcount"]) . ',';
            echo iconv("UTF-8", "BIG5", $val["reg_mcount"]) . ',';
			echo iconv("UTF-8", "BIG5", $val["reg_fcount"]) . ',';
			
			foreach ($val["degree"] as $datajid){
				echo iconv("UTF-8", "BIG5", $datajid) . ',';
			}
			
            
            echo iconv("UTF-8", "BIG5", $val["ycount_0_20"]) . ',';
            echo iconv("UTF-8", "BIG5", $val["ycount_21_30"]) . ',';
            echo iconv("UTF-8", "BIG5", $val["ycount_31_40"]) . ',';
            echo iconv("UTF-8", "BIG5", $val["ycount_41_50"]) . ',';
            echo iconv("UTF-8", "BIG5", $val["ycount_51_60"]) . ',';
			echo iconv("UTF-8", "BIG5", $val["ycount_60"]) . ',';
			
			foreach ($val["job"] as $datajij){
				echo iconv("UTF-8", "BIG5", $datajij) . ',';
			}
			

            // echo iconv("UTF-8", "BIG5", "\r\n") . ',';
                       
            echo "\r\n";
        }
    }

}