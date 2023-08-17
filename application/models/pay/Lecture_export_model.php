<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Lecture_export_model extends Common_model
{
    public function getLectureExportData($rows="", $offset="")
    {

        $sql = "select * from hour_traffic_code";

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . $rows . " offset " . $offset;
        }
        else if($rows != "") {
          $limit = " limit " . $rows;
        }

        $sql = $sql . " " . $limit;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }

    public function exportLectureExportData()
    {

        $sql = "select * from hour_traffic_code";

        $query = $this->db->query($sql);

        $rs = $this->QueryToArray($query);

        $filename = '13L.csv';
        header("Content-type: application/csv");    //header抬頭設定
        header("Content-Disposition: attachment; filename=".$filename);
        
        echo iconv('UTF-8', 'BIG5', "姓名, \t");
        echo iconv('UTF-8', 'BIG5', "身分證, \t");
        echo iconv('UTF-8', 'BIG5', "所得人代號, \n");
        $i = 0;
        for ($i=0; $i < sizeof($rs); $i++) {
            echo iconv('UTF-8', 'BIG5//IGNORE', $rs[$i]["name"].", \t");
            echo iconv('UTF-8', 'BIG5', $rs[$i]["idno"].", \t");
            echo iconv('UTF-8', 'BIG5', $rs[$i]["code"].", \n");
        }

    }

    public function getLectureExportDataById($uid)
    {

        $sql = "select * from hour_traffic_code where id =".$this->db->escape(addslashes($uid));

        $query = $this->db->query($sql);

        return $this->QueryToArray($query)[0];

    }

    public function updateLectureExportData($name,$id,$code,$uid)
    {
        $sql = sprintf("select count(1) cnt from hour_traffic_code where (code = %s or idno = %s) and id != %s",$this->db->escape(addslashes($code)),$this->db->escape(addslashes($id)),$this->db->escape(addslashes($uid)));
        $rs = $this->db->query($sql);
        $result = $this->QueryToArray($rs);
        $cnt = $result[0]['cnt'];

        if($cnt > 0){
            return("所得人代號或身分證重複");
        } else {
            $sql = sprintf("update hour_traffic_code set idno=%s, name=%s, code=%s where id = %s",$this->db->escape(addslashes($id)),$this->db->escape(addslashes($name)),$this->db->escape(addslashes($code)),$this->db->escape(addslashes($uid)));
            if($this->db->query($sql)){
                return("修改成功");
            } else {
                return("修改失敗");
            }
        }
    }

    public function insertLectureExportData($name,$id,$code)
    {
        $sql = sprintf("select count(1) cnt from hour_traffic_code where code = %s or idno = %s",$this->db->escape(addslashes($code)),$this->db->escape(addslashes($id)));
        $rs = $this->db->query($sql);
        $result = $this->QueryToArray($rs);
        $cnt = $result[0]['cnt'];

        if($cnt > 0){
            return("所得人代號或身分證重複");
        } else {
            $sql = sprintf("insert into hour_traffic_code(IDNO,NAME,CODE) values(%s,%s,%s)",$this->db->escape(addslashes($id)),$this->db->escape(addslashes($name)),$this->db->escape(addslashes($code)));
            if($this->db->query($sql)){
                return("新增成功");
            } else {
                return("新增失敗");
            }
        }

    }

}
