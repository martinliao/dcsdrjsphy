<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Tax_search_model extends Common_model
{
    public function getTaxSearchData($year,$startMonth,$endMonth)
    {

        $sql = "select TAX,TAX_RATE,H_TAX,H_TAX_RATE from co_tax";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query)[0];

    }

    public function exportTaxSearchData0($year,$startMonth,$endMonth)
    {

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=list.csv");  
        header("Pragma: no-cache"); 
        header("Expires: 0");
        $filename = 'list.csv';  

        //查詢1
        //------------------------------------------------------------------------------------
        $d1 = $year;
        $d2 = $startMonth;
        $d3 = $endMonth;


        //sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','06');    
        //------------------------------------------------------------------------------------

        //表頭
        //------------------------------------------------------------------------------------
        echo iconv("UTF-8","BIG5","投保單位代號,");
        echo iconv("UTF-8","BIG5","統一編號,");
        echo iconv("UTF-8","BIG5","所得人身分證號,");
        echo iconv("UTF-8","BIG5","姓名,");
        echo iconv("UTF-8","BIG5","所得給付日期,");
        echo iconv("UTF-8","BIG5","所得類別,");
        echo iconv("UTF-8","BIG5","所得(收入)給付金額(股利所得時，請填列股利淨額),");
        echo iconv("UTF-8","BIG5","給付當月投保金額,");
        echo iconv("UTF-8","BIG5","本次股利所屬期間，以雇主身分加保之投保總金額,");
        echo iconv("UTF-8","BIG5","股利註記,");
        echo iconv("UTF-8","BIG5","信託註記,");
        echo iconv("UTF-8","BIG5","扣取時可扣抵稅額,");
        echo iconv("UTF-8","BIG5","年度確定可扣抵稅額,");
        echo iconv("UTF-8","BIG5","除權(息)基準日,");
        echo iconv("UTF-8","BIG5","特殊註記,");
        echo iconv("UTF-8","BIG5","扣繳補充保險費金額,");
        echo iconv("UTF-8","BIG5","資料註記");
        echo "\r\n";
        //------------------------------------------------------------------------------------

        //資料
        //------------------------------------------------------------------------------------

        $year = $d1+1911;
        $month_s = $d2."/01";

        if($d3+1==13)
        {
            $date_s=$year."/".$month_s;
            $date_e = ($year+1)."/01/01";
            
        }
        else
        {	
            $date_s=$year."/".$month_s;
            $date_e = $year."/".($d3+1)."/01";

        }
        $sql = "select distinct concat(year(a.entry_date)-1911,date_format(a.entry_date,'%m%d')) as entry_date, b.*, (case when t.id_type='3' then f.fid else t.idno end) as id 
                from hour_traffic_tax a	
                        right join hour_bill b on a.bill_date = b.bill_date and a.teacher_id=b.teacher_id 
                        join teacher t on t.IDno = b.teacher_id
                        left join fid f on f.id = t.idno
                where (t.id_type = '1' or t.id_type = '3') and a.entry_date between date(".$this->db->escape(addslashes($date_s)).") and date(".$this->db->escape(addslashes($date_e)).")-1 and b.h_tax is not null and b.h_tax <> 0
                    Order by entry_date, b.teacher_id";

        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);

        for ($i=0; $i < sizeof($rs); $i++) {
            $fields=$rs[$i];
            echo '123579340,';
            echo '04120610,';
            echo iconv("UTF-8","BIG5",$fields['id']).",";
            echo iconv("UTF-8","BIG5//IGNORE",$fields['teacher_acct_name']).",";
            echo iconv("UTF-8","BIG5",$fields['entry_date']).",";
            echo '63,';
            echo iconv("UTF-8","BIG5",$fields['hour_fee']).",";
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo '';
            echo "\r\n";
        }
    }

    public function exportTaxSearchData1($year,$startMonth,$endMonth)
    {

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=teacher.csv");  
        header("Pragma: no-cache"); 
        header("Expires: 0");
        $filename = 'file.csv';  

        //查詢1
        //------------------------------------------------------------------------------------
        $d1 = $year;
        $d2 = $startMonth;
        $d3 = $endMonth;

        //sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','06');    
        //------------------------------------------------------------------------------------

        //表頭
        //------------------------------------------------------------------------------------
        echo iconv("UTF-8","BIG5","統一編號,");
        echo iconv("UTF-8","BIG5","所得人身分證號,");
        echo iconv("UTF-8","BIG5","所得人姓名,");
        echo iconv("UTF-8","BIG5","所得人地址");

        echo "\r\n";
        //------------------------------------------------------------------------------------

        //資料
        //------------------------------------------------------------------------------------
        $year = $d1+1911;
        $month_s = $d2."/01";

        if($d3+1==13)
        {
            $date_s=$year."/".$month_s;
            $date_e = ($year+1)."/01/01";
            
        }
        else
        {	
            $date_s=$year."/".$month_s;
            $date_e = $year."/".($d3+1)."/01";

        }

        $sql = "SELECT distinct  case when id_type='3' then f.fid else t.id end as id,t.NAME, t.account_name as ACCT_NAME, t.route as ADDR,CC.CITY_NAME,CS.SUBCITY_NAME,C.DESCRIPTION FROM teacher t 
                        LEFT JOIN code_table C ON C.TYPE_ID='14' AND C.ITEM_ID=t.bank_code 
                        LEFT JOIN co_city CC on t.county=CC.CITY
                        LEFT JOIN co_subcity CS on t.district=CS.SUBCITY
                        LEFT JOIN fid f on t.id = f.id
                        WHERE (t.id_type = '1' or t.id_type = '3') and
                        t.IDno IN (select distinct b.teacher_id from hour_traffic_tax a
                        left join hour_bill b on a.bill_date = b.bill_date and a.teacher_id=b.teacher_id
                        where a.entry_date between date(".$this->db->escape(addslashes($date_s)).") and date(".$this->db->escape(addslashes($date_e)).")-1 and b.h_tax is not null and b.h_tax <>0)
                        Order by ID";
                        
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);

        for ($i=0; $i < sizeof($rs); $i++) {
            $fields=$rs[$i]; 
            echo '04120610,';
            echo iconv("UTF-8","BIG5",$fields['id']) . ",";
            echo iconv("UTF-8","BIG5//IGNORE",$fields['ACCT_NAME']) . ",";
            echo iconv("UTF-8","BIG5",$fields['CITY_NAME'].$fields['SUBCITY_NAME'].$fields['ADDR']);
            echo "\r\n";
        }
    }

    public function exportTaxSearchData2($year,$startMonth,$endMonth)
    {

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=list.csv");  
        header("Pragma: no-cache"); 
        header("Expires: 0");
        $filename = 'list.csv';  

        //查詢1
        //------------------------------------------------------------------------------------
        $d1 = $year;
        $d2 = $startMonth;
        $d3 = $endMonth;


        //sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','06');    
        //------------------------------------------------------------------------------------

        //表頭
        //------------------------------------------------------------------------------------
        echo iconv("UTF-8","BIG5","投保單位代號,");
        echo iconv("UTF-8","BIG5","統一編號,");
        echo iconv("UTF-8","BIG5","所得人身分證號,");
        echo iconv("UTF-8","BIG5","姓名,");
        echo iconv("UTF-8","BIG5","所得給付日期,");
        echo iconv("UTF-8","BIG5","所得類別,");
        echo iconv("UTF-8","BIG5","所得(收入)給付金額,");
        echo iconv("UTF-8","BIG5","給付當月投保金額,");
        echo iconv("UTF-8","BIG5","本次股利所屬期間，以雇主身分加保之投保總金額,");
        echo iconv("UTF-8","BIG5","股利註記,");
        echo iconv("UTF-8","BIG5","信託註記,");
        echo iconv("UTF-8","BIG5","扣取時可扣抵稅額,");
        echo iconv("UTF-8","BIG5","年度確定可扣抵稅額,");
        echo iconv("UTF-8","BIG5","除權(息)基準日");

        echo "\r\n";
        //------------------------------------------------------------------------------------

        //資料
        //------------------------------------------------------------------------------------

        $year = $d1+1911;
        $month_s = $d2."/01";

        if($d3+1==13)
        {
            $date_s=$year."/".$month_s;
            $date_e = ($year+1)."/01/01";
            
        }
        else
        {	
            $date_s=$year."/".$month_s;
            $date_e = $year."/".($d3+1)."/01";

        }
        $sql = "select distinct concat(year(a.entry_date)-1911,date_format(a.entry_date,'%m%d')) as entry_date, b.*, (case when t.id_type='3' then f.fid else t.idno end) as id 
                from hour_traffic_tax a	
                        right join hour_bill b on a.bill_date = b.bill_date and a.teacher_id=b.teacher_id 
                        join teacher t on t.IDno = b.teacher_id
                        left join fid f on f.id = t.idno
                where (t.id_type = '1' or t.id_type = '3') and a.entry_date between date(".$this->db->escape(addslashes($date_s)).") and date(".$this->db->escape(addslashes($date_e)).")-1 and b.h_tax is not null and b.h_tax <> 0
                    Order by entry_date, b.teacher_id";
			
        $rs = $this->db->query($sql);
        $rs = $this->QueryToArray($rs);
        for ($i=0; $i < sizeof($rs); $i++) {
            $fields=$rs[$i];
            echo '123579340,';
            echo '04120610,';
            echo iconv("UTF-8","BIG5",$fields['id']).",";
            echo iconv("UTF-8","BIG5//IGNORE",$fields['teacher_acct_name']).",";
            echo iconv("UTF-8","BIG5",$fields['entry_date']).",";
            echo '63,';
            echo iconv("UTF-8","BIG5",$fields['hour_fee']).",";
            echo iconv("UTF-8","BIG5",$fields['h_tax']).",";
            //echo '0,';
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo ',';
            echo '';
            echo "\r\n";
        }

    }

}
