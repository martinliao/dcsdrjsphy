<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Pay_query_model extends Common_model
{
    public function getPayQuerySearch($year, $month, $rows="", $offset="")
    {
        if (empty($year)) {
            $query_year = date("Y") - 1911;
        }
        else {
            $query_year = $year;
        }

        $query_month="";
        
        $set_month = "";
        if ($month != "") {
            if ($month < 10) {
                $query_month = "0" . $month;
            }
            else {
                $query_month = $month;
            }

            $set_month = " and date_format(hor.USE_DATE, '%m') = ".$this->db->escape(addslashes($query_month))."";
        }

        $sql = "select
                    h1.APP_SEQ,
                    codeTbl. type,
                    codeTbl. NAME,
                    hor.CLASS_NAME,
                    hor.term,
                    hor.START_DATE,
                    hor.END_DATE,
                    hor.hrs,
                    hor.unit_hour_fee,
                    hor.HOUR_FEE,
                    hor.REMARK,
                    case when hor.TRAFFIC_FEE < 0 then 0 else hor.TRAFFIC_FEE end TRAFFIC_FEE,
                    hor.teacher_name,
                    hor.SEQ,
                    (select name AS FIRST_NAME from BS_user where idno = hor.WORKER_ID) workername
                from hour_traffic_tax hor
                LEFT OUTER JOIN `require` req on hor.YEAR = req.YEAR and hor.TERM=req.TERM and hor.class_no= req.class_no
                LEFT JOIN hour_app h1 on hor.SEQ = h1.SEQ
                join ( SELECT parent_id as type, item_id as CATE_ID, case when parent_id = 'A' then short_name else NAME end NAME
                    FROM second_category WHERE enable = '1'
                ) codeTbl on req.BEAURAU_ID = codeTbl.CATE_ID
                where (isnull(req.IS_CANCEL='0')='0')
                and hor.ENTRY_DATE is not null
                and date_format(hor.START_DATE, '%Y') - 1911 <= ".$this->db->escape(addslashes($query_year))."
                and date_format(hor.END_DATE, '%Y') - 1911 >= ".$this->db->escape(addslashes($query_year))." " . $set_month . "
                GROUP BY
                    h1.APP_SEQ,
                    codeTbl.TYPE,
                    codeTbl.NAME,
                    hor.CLASS_NAME,
                    hor.term,
                    hor.START_DATE,
                    hor.END_DATE,
                    hor.hrs,
                    hor.unit_hour_fee,
                    hor.HOUR_FEE,
                    hor.TRAFFIC_FEE,
                    hor.teacher_name,
                    hor.SEQ,
                    hor.WORKER_ID,
                    hor.REMARK
                ORDER BY TYPE";

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

    public function csvexport($filename, $query_start_date, $query_end_date, $dayOfWeek,$year, $month)
    {
        $data = $this->getPayQuerySearch($year, $month);

        if ($filename == "") {
            $filename = date("Ymd") . '.csv';
        } else {
            $filename = $filename . '.csv';
        }

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo ($year . iconv("UTF-8", "big5", "年度 班期經費狀況"));
        echo "\r\n";
        echo iconv("UTF-8", "big5", "類別,");
        echo iconv("UTF-8", "big5", "次類別,");
        echo iconv("UTF-8", "big5", "班期名稱,");
        echo iconv("UTF-8", "big5", "期別,");
        echo iconv("UTF-8", "big5", "帶班人員,");
        echo iconv("UTF-8", "big5", "開課日期,");
        echo iconv("UTF-8", "big5", "講座,");
        echo iconv("UTF-8", "big5", "鐘點,");
        echo iconv("UTF-8", "big5", "費用,");
        echo iconv("UTF-8", "big5", "鐘點費,");
        echo iconv("UTF-8", "big5", "交通費");

        echo "\r\n";

        foreach ($data as $val) {
            echo ($val["type"] . iconv("UTF-8", "big5", "系列 ,"));
            echo (iconv("UTF-8", "big5", $val['NAME']) . ",");
            echo (iconv("UTF-8", "big5", $val['CLASS_NAME']) . ",");
            echo (iconv("UTF-8", "big5", $val['term']) . ",");
            echo (iconv("UTF-8", "big5", $val['workername']) . ",");
            echo ($val['START_DATE'] . '-' . $val['END_DATE'] . ",");
            echo (iconv("UTF-8", "big5//IGNORE", $val['teacher_name']) . ",");
            echo (iconv("UTF-8", "big5", $val['hrs']) . ",");
            echo (iconv("UTF-8", "big5", $val['unit_hour_fee']) . ",");
            echo (iconv("UTF-8", "big5", $val['HOUR_FEE']) . ",");
            echo (iconv("UTF-8", "big5", $val['TRAFFIC_FEE']) . ",");
            echo "\r\n";
        }

    }

}
