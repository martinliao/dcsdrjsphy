<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Lecture_tax_search_model extends Common_model
{
    public function getAddressSearch()
    {
        $cityArr[] = array();
        $subcityArr[] = array();
        $query = $this->db->query("select * from co_city");
        $dataCity = $this->QueryToArray($query);

        if($dataCity) foreach ($dataCity as $data) {
            $cityArr[$data['city']] = $data['city_name'];
        }

        $query = $this->db->query("select * from co_subcity");
        $dataSubCity = $this->QueryToArray($query);

        if($dataSubCity) foreach ($dataSubCity as $data) {
            $subcityArr[$data['city'].$data['subcity']] = $data['subcity_name'];
        }

        return [$cityArr, $subcityArr];
    }

    public function getInitialSearch() {
        $query = $this->db->query("select * from code_table where type_id = '35' ORDER BY item_id");
        return $this->QueryToArray($query);

    }

    public function saveRemark($year, $id, $remark) {
        $sql = "SELECT year, teacher_id, remark FROM hour_bill_remark WHERE year = ".$this->db->escape(addslashes($year))." and teacher_id = ".$this->db->escape(addslashes($id))."";
        $rs = $this->QueryToArray($this->db->query($sql));
        if (sizeof($rs)) {
            $sql = "update hour_bill_remark set remark=".$this->db->escape(addslashes($remark))." where year = ".$this->db->escape(addslashes($year))." and teacher_id = ".$this->db->escape(addslashes($id))." ";
            $rs = $this->db->query($sql);
        } else {
            $sql = "insert into hour_bill_remark(year, teacher_id, remark) values(".$this->db->escape(addslashes($year)).", ".$this->db->escape(addslashes($id)).", ".$this->db->escape(addslashes($remark)).")";
            $rs = $this->db->query($sql);
        }
        echo "修改成功";
    }

    public function getLectureTaxSearch($teacher, $uniformid, $start_date, $end_date, $year, $lists)
    {





        $totalData = array();
        // $year = $year + 1911;
        for ($i = 0 ; $i < sizeof($lists); $i++) {
            $where = "between ".$this->db->escape(addslashes($start_date))." and ".$this->db->escape(addslashes($end_date))."";
            if ($uniformid!= ""){
                $where .= " AND A.TEACHER_ID = ".$this->db->escape(addslashes($uniformid))." ";
            }
            // custom (b) by chiahua 加上講座姓名查詢
            if ($teacher!= ""){
                $where .= " AND A.TEACHER_NAME like ".$this->db->escape("%".addslashes($teacher)."%")." ";
            }
            
          
            $sql = "SELECT A.teacher_ID, A.teacher_NAME, B1.rpno,
                        (select remark from hour_bill_remark where year = ".$this->db->escape(addslashes($year))." and teacher_id = A.teacher_id) remark, SUM(A.HOUR_FEE) AS HOUR_FEE, SUM(A.TAX) AS TAX,SUM(A.H_TAX) AS H_TAX 
                    FROM hour_bill A
                    LEFT JOIN teacher B1 ON A.teacher_ID = B1.IDNO AND B1.teacher_type='1' 
                    LEFT JOIN teacher B2 ON A.teacher_ID = B2.IDNO AND B2.teacher_type='2' 
                    WHERE NVL(B1.identity_type, B2.identity_type) = ".$this->db->escape(addslashes($lists[$i]['item_id']))." AND A.BILL_DATE {$where} AND (B1.DEL_FLAG is null or B1.DEL_FLAG='N' or B2.DEL_FLAG is null or B2.DEL_FLAG='N')
                    GROUP BY A.teacher_ID, A.teacher_NAME
                    ORDER BY A.teacher_ID ASC";
            $query = $this->db->query($sql);
            $datas =  $this->QueryToArray($query);

            for($p = 0 ; $p < sizeof($datas); $p++){
                $datas[$p]["DESCRIPTION"] = $lists[$i]['description'];
                $datas[$p]["address"] = $this->selectTeacherAddress($i, $datas[$p]["teacher_ID"]);
            }
            
            $totalData[$lists[$i]['item_id']] = $datas;
        }

        return $totalData;
    }

    public function selectTeacherAddress($index, $thearId){
        // $fields['TEACHER_ID']
        if($index == 1) {   // 外國人
            $sql = "select f.fid, t.county as CITY, t.district as SUBCITY, t.route as ADDR from teacher t join fid f on t.idno=f.id  where t.IDNO = '{$thearId}'";
        }
        else {
            $sql = "select t.county as CITY, t.district as SUBCITY, t.route as ADDR from teacher t where t.IDNO = '{$thearId}'";
        }
        
        $query = $this->db->query($sql);
        $town = $this->getAddressSearch();
    
        $address = $this->QueryToArray($query);
        $teacher_addr = '';
        if(sizeof($address) != 0){
           
            $teacher_addr .= $town[0][$address[0]['CITY']];
            $teacher_addr .= $town[1][$address[0]['CITY'].$address[0]['SUBCITY']];
            $teacher_addr .= $address[0]['ADDR'];
        }
        return $teacher_addr;

    }

    public function csvexport($filename, $query_start_date, $query_end_date, $data, $dayOfWeek)
    {
        if ($filename == "") {
            $filename = date("Ymd") . '.csv';
        } else {
            $filename = $filename . '.csv';
        }

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv('UTF-8', 'BIG5', "類別,");
        echo iconv('UTF-8', 'BIG5', "流水號,");
        echo iconv('UTF-8', 'BIG5', "身分證字號,");
        echo iconv('UTF-8', 'BIG5', "姓名,");
        echo iconv('UTF-8', 'BIG5', "地址,");
        echo iconv('UTF-8', 'BIG5', "鐘點費,");
        echo iconv('UTF-8', 'BIG5', "所得稅,");
        echo iconv('UTF-8', 'BIG5', "實付金額,");
        echo iconv('UTF-8', 'BIG5', "備註 \r\n");
        
        $count =1;

        // echo json_encode($data);

        for($i = 1; $i <=4; $i++ ){
            $amt1 = 0;
            $amt2 = 0;
            $amt3 = 0;  

            if(sizeof($data[$i]) > 0) {
                if($i == 1){
                    echo iconv('UTF-8', 'BIG5', "1.個人") . ',';
                }
                else if($i == 2){
                    echo iconv('UTF-8', 'BIG5', "2.公司行號") . ',';
                }
                else if($i == 3){
                    echo iconv('UTF-8', 'BIG5', "3.外國人") . ',';
                }
                else if($i == 4){
                    echo iconv('UTF-8', 'BIG5', "4.無身分證") . ',';
                }

                echo iconv('UTF-8', 'BIG5', '' ) . ','; 
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo "\r\n";

                foreach ($data[$i] as $val) {
                    echo iconv('UTF-8', 'BIG5', '' ) . ','; 
                    echo iconv('UTF-8', 'BIG5', $count ) . ',';
                    echo iconv('UTF-8', 'BIG5', $val["rpno"]!=''?$val["rpno"]:$val["teacher_ID"]) . ',';
                    echo iconv('UTF-8', 'BIG5//IGNORE', $val["teacher_NAME"]) . ',';
                    echo iconv('UTF-8', 'BIG5', $val["address"]) . ',';
                    echo iconv('UTF-8', 'BIG5', $val["HOUR_FEE"]) . ',';
                    echo iconv('UTF-8', 'BIG5', $val["TAX"]) . ',';
                    echo iconv('UTF-8', 'BIG5', $val["HOUR_FEE"] - $val["TAX"]) . ',';
                    echo iconv('UTF-8', 'BIG5', $val["remark"] ) . ',';
                    echo "\r\n";
    
                    $amt1 += $val["HOUR_FEE"];
                    $amt2 += $val["TAX"];
                    $amt3 += ($val["HOUR_FEE"] - $val["TAX"]);
                    $count++;
                }

                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ','; 
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo iconv('UTF-8', 'BIG5', '小計' ) . ',';
                echo iconv('UTF-8', 'BIG5', $amt1 ) . ',';
                echo iconv('UTF-8', 'BIG5', $amt2 ) . ',';
                echo iconv('UTF-8', 'BIG5', $amt3 ) . ',';
                echo iconv('UTF-8', 'BIG5', '' ) . ',';
                echo "\r\n";
            }
        }
    }
}
