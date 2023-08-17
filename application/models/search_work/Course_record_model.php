<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Course_record_model extends Common_model
{
    public function getCourseRecordData($schedule, $name, $gender, $id, $location, $birthday, $classdate, $rows = "", $offset = "")
    {
        $Enter_ID_Number = $id;
        $Enter_Student_Name = $name;
        //班期名稱、、學員基本資料(局處、姓名、性別、生日)
        $query_class_name = $schedule;
        //上課日期
        $Select_Start_Day = $classdate;

        //局處
        $Enter_Description = $location;
        //性別
        $Enter_Gender = $gender;
        //生日
        $Select_BirthDay = $birthday;

        //組成 sql partial where constraint
        $No_Date = true;
        $query_cond_string = ' and ';
        if ((isset($Enter_ID_Number)) && $Enter_ID_Number != "") {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "O.ID like ".$this->db->escape("%" . addslashes($Enter_ID_Number) . "%");
        }
        if ((isset($Enter_Student_Name)) && $Enter_Student_Name != "") {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "V.name like ".$this->db->escape("%" . addslashes($Enter_Student_Name) . "%");
        }

        if ((isset($query_class_name)) && $query_class_name != "") {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "R.CLASS_NAME like ".$this->db->escape("%" . addslashes($query_class_name) . "%");
        }

        if ((isset($Select_Start_Day)) && ($Select_Start_Day !== "")) {

            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= " R.start_date1 =date(" . $this->db->escape(addslashes($Select_Start_Day)) . ") ";
        }
        //
        if ((isset($Enter_Description)) && ($Enter_Description !== "")) {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }
            $query_cond_string .= "C.name like ".$this->db->escape("%" . addslashes($Enter_Description) . "%");
        }

        //
        if ((isset($Enter_Gender)) && ($Enter_Gender !== "")) {

            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "V.Gender = " . $this->db->escape(addslashes($Enter_Gender)) . "";
        }

        if ((isset($Select_BirthDay)) && ($Select_BirthDay !== "")) {

            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= " V.BIRTHDAY =date(" . $this->db->escape(addslashes($Select_BirthDay)) . ") ";
        }

        if ($No_Date == true) {
            $query_cond_string = 'where 1=1';
        }

        //撈資料
        $data = array();
        $sql = ("
                select
                        O.ID, O.ST_NO,R.YEAR,R.CLASS_NAME,R.TERM,C.NAME AS DESCRIPTION,R.ROOM_CODE,R.START_DATE1,R.CLASS_NO CLASS_ID ,TI.NAME,
                        V.job_title as TITLE, V.name AS PNAME,R.seq_no
                from online_app O
                LEFT JOIN `require` R
                        ON O.YEAR=R.YEAR AND O.CLASS_NO=R.CLASS_NO AND O.TERM=R.TERM
                LEFT JOIN BS_user V
                        ON O.ID=V.idno
                LEFT JOIN title TI
                        ON V.job_title=TI.ID
                LEFT JOIN bureau C
                        ON O.BEAURAU_ID=C.bureau_id
                          WHERE
                         O.yn_sel not in ('2','4','5','6','7')  
                  " .
            $query_cond_string." order by R.START_DATE1 desc"
        );

        $limit = "";
        if ($rows != "" && $offset != "") {
            $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        } else if ($rows != "") {
            $limit = " limit " . intVal($rows);
        }

        $sql = $sql . " " . $limit;
        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function exportCourseRecordData($schedule, $name, $gender, $id, $location, $birthday, $classdate)
    {
        $Enter_ID_Number = $id;
        $Enter_Student_Name = $name;
        //班期名稱、、學員基本資料(局處、姓名、性別、生日)
        $query_class_name = $schedule;
        //上課日期
        $Select_Start_Day = $classdate;

        //局處
        $Enter_Description = $location;
        //性別
        $Enter_Gender = $gender;
        //生日
        $Select_BirthDay = $birthday;

        //組成 sql partial where constraint
        $No_Date = true;
        $query_cond_string = ' where ';
        if ((isset($Enter_ID_Number)) && $Enter_ID_Number != "") {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "O.ID like ".$this->db->escape("%" . addslashes($Enter_ID_Number) . "%");
        }
        if ((isset($Enter_Student_Name)) && $Enter_Student_Name != "") {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "V.name like ".$this->db->escape("%" . addslashes($Enter_Student_Name) . "%");
        }

        if ((isset($query_class_name)) && $query_class_name != "") {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "R.CLASS_NAME like ".$this->db->escape("%" . addslashes($query_class_name) . "%");
        }

        if ((isset($Select_Start_Day)) && ($Select_Start_Day !== "")) {

            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= " R.start_date1 =date(" . $this->db->escape(addslashes($Select_Start_Day)) . ") ";
        }
        //
        if ((isset($Enter_Description)) && ($Enter_Description !== "")) {
            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }
            $query_cond_string .= "C.name like ".$this->db->escape("%" . addslashes($Enter_Description) . "%");
        }

        //
        if ((isset($Enter_Gender)) && ($Enter_Gender !== "")) {

            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= "V.Gender = " . $this->db->escape(addslashes($Enter_Gender)) . "";
        }

        if ((isset($Select_BirthDay)) && ($Select_BirthDay !== "")) {

            if ($No_Date != true) {
                $query_cond_string .= " AND ";
            } else {
                $No_Date = false;
            }

            $query_cond_string .= " V.BIRTHDAY =date(" . $this->db->escape(addslashes($Select_BirthDay)) . ") ";
        }

        if ($No_Date == true) {
            $query_cond_string = 'where 1=1';
        }

        //撈資料
        $data = array();
        $sql = ("
                select
                        O.ID, O.ST_NO,R.YEAR,R.CLASS_NAME,R.TERM,C.NAME AS DESCRIPTION,R.ROOM_CODE,R.START_DATE1,R.CLASS_NO CLASS_ID ,TI.NAME,
                        V.job_title as TITLE, V.name AS PNAME
                from online_app O
                LEFT JOIN `require` R
                        ON O.YEAR=R.YEAR AND O.CLASS_NO=R.CLASS_NO AND O.TERM=R.TERM
                LEFT JOIN BS_user V
                        ON O.ID=V.idno
                LEFT JOIN title TI
                        ON V.job_title=TI.ID
                LEFT JOIN bureau C
                        ON O.BEAURAU_ID=C.bureau_id
                /*          WHERE
                        %s and O.yn_sel not in ('2','4','5','6','7')  */
                  " .
            $query_cond_string
        );
        $query = $this->db->query($sql);

        $rs = $this->QueryToArray($query);

        $filename = 'Export_Students_Record_Class_Schedule_Cases.csv';
        header("Content-type: application/csv"); //header抬頭設定
        header("Content-Disposition: attachment; filename=Export_Students_Record_Class_Schedule_Cases.csv");

        echo iconv('UTF-8', 'BIG5', "學號,");
        echo iconv('UTF-8', 'BIG5', "姓名,");
        echo iconv('UTF-8', 'BIG5', "年度/班期名稱/期別,");
        echo iconv('UTF-8', 'BIG5', "職稱,");
        echo iconv('UTF-8', 'BIG5', "報名單位,");
        echo iconv('UTF-8', 'BIG5', "教室(課程表),");
        echo iconv('UTF-8', 'BIG5', "上課日期 \r\n");

        for ($i = 0; $i < sizeof($rs); $i++) {
            $arr = $rs[$i];
            echo iconv('UTF-8', 'BIG5', $arr["ST_NO"]).',';
            echo iconv('UTF-8', 'BIG5//IGNORE', $arr["PNAME"]).',';
            echo iconv('UTF-8', 'BIG5', $arr["YEAR"] . "年" . $arr["CLASS_NAME"] . "(第" . $arr["TERM"] . "期)").',';
            echo iconv("UTF-8", 'BIG5', trim($arr["NAME"])).',';
            echo iconv("UTF-8", 'BIG5//IGNORE', $arr["DESCRIPTION"]).',';
            echo iconv("UTF-8", 'BIG5//IGNORE', $arr["ROOM_CODE"]).',';
            echo iconv('UTF-8', 'BIG5', substr($arr["START_DATE1"],0,10)).',';
            echo "\r\n";   
        }

    }

}
