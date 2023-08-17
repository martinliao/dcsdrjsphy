<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_special_list_model extends Common_model
{
    public function getCourseSpecialListData($start_date, $end_date, $rows="", $offset="")
    {
        $search_start_date = $start_date ." 00:00:01";
        $search_end_date=$end_date." 23:59:59";
        $data['rows'] = $this->getTeacherSpecialInfoByDate($search_start_date, $search_end_date, $rows, $offset);

        return $data;

    }

    public function exportCourseSpecialListData($start_date, $end_date)
    {
        //撈資料
        $data = array();
        $search_start_date = $start_date ." 00:00:01";
        $search_end_date = $end_date." 23:59:59";
        $data = $this->getTeacherSpecialInfoByDate($search_start_date, $search_end_date);
        
        // 寫檔
        $filename = date("Ymd").'.csv';
                
        header("Content-type: application/csv");    //header抬頭設定
        header("Content-Disposition: attachment; filename=$filename");
                
        echo iconv("UTF-8","BIG5","臺北市政府公務人員訓練處,");
        echo iconv("UTF-8","BIG5","講座特殊需求清單\n");
        echo iconv("UTF-8","BIG5",htmlspecialchars($search_start_date, ENT_HTML5|ENT_QUOTES)."至".htmlspecialchars($search_end_date, ENT_HTML5|ENT_QUOTES)."\r\n");
        echo iconv("UTF-8","BIG5","姓名, \t");
        echo iconv("UTF-8","BIG5","任職機關, \t");
        echo iconv("UTF-8","BIG5","特殊需求, \t");
        echo iconv("UTF-8","BIG5","特殊需求輸入或異動日期, \n");
                    
        foreach ($data as $val) {
            echo iconv("UTF-8","BIG5//IGNORE",$val['name']).",\t";
            echo iconv("UTF-8","BIG5",$val['corp']).",\t";
            echo iconv("UTF-8","BIG5",$val['special_require']).",\t";
            echo iconv("UTF-8","BIG5",$val['SPECIAL_DATE']) .",\n";
        }        

    }

    /**
	 * 12H 功能講座特殊需求查詢
	 *
	 * @param string $queryStartDate 異動日期開始時間
	 * @param string $queryEndDate 異動日期結束時間
	 */
	function getTeacherSpecialInfoByDate ($queryStartDate, $queryEndDate, $rows="", $offset="")
	{

		$sql = "SELECT R.name,R.institution AS corp,R.demand as special_require, date_format(R.SPECIAL_REQUIRE_DATE, '%Y-%m-%d') AS SPECIAL_DATE
				FROM teacher R
		 		WHERE (
		 			R.SPECIAL_REQUIRE_DATE
		 		BETWEEN
		 			date(".$this->db->escape(addslashes($queryStartDate)).")
		 			AND
		 			date(".$this->db->escape(addslashes($queryEndDate)).")
				)";

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;

        $rows = $this->db->query($sql);
        $rows = $this->QueryToArray($rows);

			//填資料
        $data = array();
        for ($i=0; $i < sizeof($rows); $i++) { 
            $data[] = $rows[$i];
        }

		return $data;
	}

}
