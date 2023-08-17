<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Teach_pay_list_model extends Common_model
{
    public function getTeachPayListSearch($appno, $workname, $start_date, $end_date)
    {
    	$where = "1=1";
    	if ($appno!=""){
		  $where .= " AND A1.APP_SEQ = ".$this->db->escape(addslashes($appno))." ";
		} 
		if ($workname!=""){
		  $where .= " AND C.NAME LIKE ".$this->db->escape("%".addslashes($workname)."%")." ";
		} 
		if ($start_date!="" && $end_date!=""){
		  $where .= " AND (A1.BILL_DATE between date(".$this->db->escape(addslashes($start_date)).") and date(".$this->db->escape(addslashes($end_date)).")) ";
		}
		else{
		  $where .= " AND A1.ENTRY_DATE IS NULL ";
		} 
        
        
      
        $sql = "SELECT A1.*, C.NAME AS WORKER_NAME, A2.TEACH_CNT, A3.CLASS_CNT FROM 
			(
			 SELECT APP_SEQ, BILL_DATE, PAY_DATE, UPD_USER, ENTRY_DATE FROM hour_app 
			 WHERE DEL_FLAG IS NULL AND CASH is NULL GROUP BY APP_SEQ, BILL_DATE, PAY_DATE, UPD_USER, ENTRY_DATE
			) A1
			LEFT JOIN
			(
			  SELECT APP_SEQ, COUNT(*) AS TEACH_CNT FROM
			  (
			    SELECT DISTINCT B.APP_SEQ, A.TEACHER_ID FROM hour_traffic_tax A
			    JOIN hour_app B ON A.SEQ = B.SEQ AND B.DEL_FLAG IS NULL
			  ) b GROUP BY APP_SEQ
			) A2 ON A1.APP_SEQ = A2.APP_SEQ
			LEFT JOIN
			(
			  SELECT APP_SEQ, COUNT(*) AS CLASS_CNT FROM
			  (
			    SELECT DISTINCT B.APP_SEQ, A.YEAR, A.CLASS_NO, A.TERM FROM hour_traffic_tax A
			    JOIN hour_app B ON A.SEQ = B.SEQ AND B.DEL_FLAG IS NULL
			  ) c GROUP BY APP_SEQ
			) A3 ON A1.APP_SEQ = A3.APP_SEQ
			LEFT JOIN view_all_account C ON A1.UPD_USER = C.USERNAME WHERE ";

       
        $orderby = " order by APP_SEQ DESC";

        $sql = $sql . " " . $where . " " . $orderby;

		$data = $this->QueryToArray($this->db->query($sql));

        return $data;
	}

    public function getTeachPayListByAppnoSearch($appno) {
		$sql = "SELECT * FROM hour_traffic_tax where seq in (select seq from hour_app where app_seq = ".$this->db->escape(addslashes($appno)).") order by use_date";
		$query = $this->db->query($sql);

		$data = $this->QueryToArray($query);
		for($i = 0 ; $i < sizeof($data); $i++){
			// $amt1 = $amt1 + $fields['HOUR_FEE'];
          	// $amt2 = $amt2 + ($fields['TRAFFIC_FEE'] <=0 ? 0 : $fields['TRAFFIC_FEE']);
          	// $amt3 = $amt3 + $fields['SUBTOTAL'];
			$data[$i]["description"] = $this->getDescription($data[$i]["teacher_id"]);
			$data[$i]["bank_name"] = $this->getBankname($data[$i]["teacher_bank_id"]);
		}
		return $data;
	}
	
	public function getDescription($teacherid){
		$sql = "select C.DESCRIPTION from teacher T left join code_table C on T.hire_type = C.ITEM_ID and C.TYPE_ID = '08' where T.idno = ".$this->db->escape(addslashes($teacherid))."";
		$query = $this->db->query($sql);

		$data = $this->QueryToArray($query);
		$description = "";
		if(sizeof($data)!=0){
			$description = $data[0]["DESCRIPTION"];
		}
		return $description;
	}

	public function getBankname($teacherbankid) {
		$sql = "select DESCRIPTION from code_table where TYPE_ID=14 and ITEM_ID = '".$teacherbankid."'";
		$query = $this->db->query($sql);
		
		$data = $this->QueryToArray($query);
		$description = "";
		if(sizeof($data)!=0){
			$description = $data[0]["DESCRIPTION"];
		}
		return $description;
	}

	public function setdt($outdt,$mtlist){
		$tmp = "市庫支票";
		$billdt = $outdt;

		$sql = "select nvl(max(bill_seq),0) + 1 as bill_seq from hour_traffic_tax where bill_date = date(".$this->db->escape(addslashes($billdt)).")";
		$billseq = $this->QueryToArray($this->db->query($sql))[0]['bill_seq'];
		
		$mtList = $mtlist;
		$arry = explode(",,",$mtList);
		for ($x=0;$x<count($arry);$x++){
			if ($arry[$x]!=""){
			$sql = "SELECT SEQ as SEQ FROM hour_app WHERE APP_SEQ = ".$this->db->escape(addslashes($arry[$x]))."";
			$rs = $this->db->query($sql);
			$rs = $this->QueryToArray($rs);
			for ($i=0; $i < sizeof($rs); $i++) {
				$fields=$rs[$i];
				$this->SP_ADD_HOUR_BILL($billdt,$billseq,$tmp,$fields['SEQ']); 
			}
			$sql = "UPDATE hour_app SET BILL_DATE = date(".$this->db->escape(addslashes($billdt)).") WHERE APP_SEQ = ".$this->db->escape(addslashes($arry[$x]))."";
			$this->db->query($sql);
			$sql = "UPDATE hour_traffic_tax SET BILL_DATE = date(".$this->db->escape(addslashes($billdt)).") WHERE 
			seq in (select seq from hour_app where app_seq = ".$this->db->escape(addslashes($arry[$x])).")";
			$this->db->query($sql);
			}
		}
		return("設定成功");  
	}

	public function canceldt($mtlist){
		$tmp = "請款確認";
		$billdt = "";
		$billseq = "";

		$mtList = $mtlist;
		$arry = explode(",,",$mtList);
		for ($x=0;$x<count($arry);$x++){
			if ($arry[$x]!=""){
			$sql = "SELECT SEQ AS SEQ FROM hour_app WHERE APP_SEQ = ".$this->db->escape(addslashes($arry[$x]))."";
			$rs = $this->db->query($sql);
			$rs = $this->QueryToArray($rs);
			for ($i=0; $i < sizeof($rs); $i++) { 
				$fields=$rs[$i];
				$this->SP_ADD_HOUR_BILL($billdt,$billseq,$tmp,$fields['SEQ']);
			}
			$sql = "UPDATE hour_app SET BILL_DATE = null WHERE APP_SEQ = ".$this->db->escape(addslashes($arry[$x]))."";
			$this->db->query($sql);
			}
		}
		return("設定成功");
	}

	public function setimd($outimd,$mtlist){

		$billdt = $outimd;
		$mtList = $mtlist;
	
		$arry = explode(",,",$mtList);

		for ($x=0;$x<count($arry);$x++)
		{
			if ($arry[$x]!="")
			{
				$sql = "UPDATE hour_traffic_tax SET ENTRY_DATE = date(".$this->db->escape(addslashes($billdt)).") WHERE seq in (select seq from hour_app where app_seq = ".$this->db->escape(addslashes($arry[$x])).") ";
				$this->db->query($sql);
				$sql = "UPDATE hour_app SET ENTRY_DATE = date(".$this->db->escape(addslashes($billdt)).") WHERE APP_SEQ = ".$this->db->escape(addslashes($arry[$x]))."";
				$this->db->query($sql);
			}
		}
		return("設定成功");
	}

	public function cancelimd($mtlist){
		$billdt = "";
		$mtList = $mtlist;
		$arry = explode(",,",$mtList);
		for ($x=0;$x<count($arry);$x++){
			if ($arry[$x]!=""){
			
			$sql = "UPDATE hour_traffic_tax SET ENTRY_DATE = null WHERE seq in (select seq from hour_app where app_seq = ".$this->db->escape(addslashes($arry[$x])).") ";
			$this->db->query($sql);
			
			$sql = "UPDATE hour_app SET ENTRY_DATE = null WHERE APP_SEQ = ".$this->db->escape(addslashes($arry[$x]))."";
			$this->db->query($sql);
			}
		}
		return("設定成功");  
	}

	public function SP_ADD_HOUR_BILL($BillDt, $BillSeq, $strStatus, $inSeq){
		/*取的所得稅設定值*/
		
		$getTAX = $this->QueryToArray($this->db->query("select TAX from co_tax where TAX is not null"))[0]['TAX'];

		$newRATE = $this->QueryToArray($this->db->query("select TAX_RATE from co_tax where TAX_RATE is not null"))[0]['TAX_RATE'];

		$getHTAX = $this->QueryToArray($this->db->query("select H_TAX from co_tax where H_TAX is not null"))[0]['H_TAX'];

		$getHTAXRATE = $this->QueryToArray($this->db->query("select H_TAX_RATE from co_tax  where H_TAX_RATE is not null"))[0]['H_TAX_RATE'];

		/*取出原資料的內容*/

		$oldBillDt = $this->QueryToArray($this->db->query("select BILL_DATE from hour_traffic_tax WHERE SEQ = ".$this->db->escape(addslashes($inSeq)).""));
		if(sizeof($oldBillDt)==0){
			return;
		}else{
			$oldBillDt = $oldBillDt[0]['BILL_DATE'];
		}

		$pid = $this->QueryToArray($this->db->query("select TEACHER_ID from hour_traffic_tax  WHERE SEQ = ".$this->db->escape(addslashes($inSeq)).""));
		if(sizeof($pid)==0){
			return;
		}else{
			$pid = $pid[0]['TEACHER_ID'];
		}

		$getType = $this->QueryToArray($this->db->query("select ISTEACHER from hour_traffic_tax WHERE SEQ = ".$this->db->escape(addslashes($inSeq)).""));
		if(sizeof($getType)==0){
			return;
		}else{
			$getType = $getType[0]['ISTEACHER'];
		}

		/*只有本國人要計算稅率*/

		$getTAXRATE = $this->QueryToArray($this->db->query("select (CASE WHEN id_type = 1 THEN ".$this->db->escape(addslashes($newRATE))." ELSE 0 END) as TAXRATE from `teacher` where idno = ".$this->db->escape(addslashes($pid))." and teacher = ".$this->db->escape(addslashes($getType))." and (DEL_FLAG is NULL OR DEL_FLAG = '' or DEL_FLAG in ('N'))"));
		if(sizeof($getTAXRATE)==0){
			return;
		}else{
			$getTAXRATE = $getTAXRATE[0]['TAXRATE'];
		}

		/*刪除該筆資料原出單日的內容, 並重算原出單日該ID的資料*/

		$this->db->query("delete from hour_bill where bill_date = ".$this->db->escape(addslashes($oldBillDt))." and teacher_id = ".$this->db->escape(addslashes($pid))."");

		$this->db->query("INSERT INTO hour_bill

		(BILL_DATE, TEACHER_ID, TEACHER_NAME, TEACHER_BANK_TYPE, TEACHER_BANK_ID, TEACHER_ACCOUNT, TEACHER_ACCT_NAME, HOUR_FEE, TRAFFIC_FEE, SUBTOTAL)

		SELECT BILL_DATE, TEACHER_ID, TEACHER_NAME, TEACHER_BANK_TYPE, TEACHER_BANK_ID, TEACHER_ACCOUNT, TEACHER_ACCT_NAME,

		SUM(HOUR_FEE) AS HOUR_FEE, SUM(TRAFFIC_FEE) AS TRAFFIC_FEE, SUM(SUBTOTAL) AS SUBTOTAL

		FROM hour_traffic_tax

		WHERE STATUS IN ('市庫支票','請款確認')

		AND bill_date = ".$this->db->escape(addslashes($oldBillDt))." and teacher_id = ".$this->db->escape(addslashes($pid))." and seq not in (".$this->db->escape(addslashes($inSeq)).")

		GROUP BY BILL_DATE, TEACHER_ID, TEACHER_NAME, TEACHER_BANK_TYPE, TEACHER_BANK_ID, TEACHER_ACCOUNT, TEACHER_ACCT_NAME");

		/*計算稅率and二代健保費*/

		$this->db->query("UPDATE hour_bill SET

		tax_rate = (case when hour_fee > ".$this->db->escape(addslashes($getTAX))." then ".$this->db->escape(addslashes($getTAXRATE))." else 0 end),

		tax = case when hour_fee > ".$this->db->escape(addslashes($getTAX))." then hour_fee * ".$this->db->escape(addslashes($getTAXRATE))." else 0 end,

		h_tax_rate = (case when hour_fee > ".$this->db->escape(addslashes($getHTAX))." then ".$this->db->escape(addslashes($getHTAXRATE))." else 0 end),

		h_tax = case when hour_fee > ".$this->db->escape(addslashes($getHTAX))." then hour_fee * ".$this->db->escape(addslashes($getHTAXRATE))." else 0 end

		where bill_date = ".$this->db->escape(addslashes($oldBillDt))." and teacher_id = ".$this->db->escape(addslashes($pid))."");

		/*計算實付*/

		$this->db->query("UPDATE hour_bill SET

		AFTERTAX = SUBTOTAL - (tax+ H_TAX)

		where bill_date = ".$this->db->escape(addslashes($oldBillDt))." and teacher_id = ".$this->db->escape(addslashes($pid))."");

		/*更新出單日期*/

		$this->db->query("update hour_traffic_tax set status = ".$this->db->escape(addslashes($strStatus)).", bill_date = date(".$this->db->escape(addslashes($BillDt))."), bill_seq = ".$this->db->escape(addslashes($BillSeq))."

		where seq = ".$this->db->escape(addslashes($inSeq))."");

		/*刪除該筆資料新出單日的內容, 並重算新出單日該ID的資料*/

		$this->db->query("delete from hour_bill where (BILL_DATE, TEACHER_ID) IN (SELECT BILL_DATE, TEACHER_ID FROM hour_traffic_tax WHERE SEQ = ".$this->db->escape(addslashes($inSeq)).")");

		$this->db->query("INSERT INTO hour_bill

		(BILL_DATE, TEACHER_ID, TEACHER_NAME, TEACHER_BANK_TYPE, TEACHER_BANK_ID, TEACHER_ACCOUNT, TEACHER_ACCT_NAME, HOUR_FEE, TRAFFIC_FEE, SUBTOTAL)

		SELECT BILL_DATE, TEACHER_ID, TEACHER_NAME, TEACHER_BANK_TYPE, TEACHER_BANK_ID, TEACHER_ACCOUNT, TEACHER_ACCT_NAME,

		SUM(HOUR_FEE) AS HOUR_FEE, SUM(TRAFFIC_FEE) AS TRAFFIC_FEE, SUM(SUBTOTAL) AS SUBTOTAL

		FROM hour_traffic_tax

		WHERE STATUS IN ('市庫支票','請款確認') AND bill_seq is not null 
		/*清除出單日(退單)則不 insert hour_bill (20131219)*/

		AND (BILL_DATE, TEACHER_ID) IN (SELECT BILL_DATE, TEACHER_ID FROM hour_traffic_tax WHERE SEQ = ".$this->db->escape(addslashes($inSeq)).")

		GROUP BY BILL_DATE, TEACHER_ID, TEACHER_NAME, TEACHER_BANK_TYPE, TEACHER_BANK_ID, TEACHER_ACCOUNT, TEACHER_ACCT_NAME");

		/*計算稅率*/

		$this->db->query("UPDATE hour_bill SET

		tax_rate = (case when hour_fee > $getTAX then $getTAXRATE else 0 end),

		tax = case when hour_fee > $getTAX then hour_fee * $getTAXRATE else 0 end,

		h_tax_rate = 0,

		h_tax = 0

		where (bill_date, teacher_id) in

		(SELECT BILL_DATE, TEACHER_ID FROM hour_traffic_tax WHERE SEQ = ".$this->db->escape(addslashes($inSeq)).")");

		/*計算二代健保費(只針對 1:個人,3: 外國人)*/

		$this->db->query("UPDATE hour_bill SET

		h_tax_rate = (case when hour_fee > ".$this->db->escape(addslashes($getHTAX))." then ".$this->db->escape(addslashes($getHTAXRATE))." else 0 end),

		h_tax = case when hour_fee > ".$this->db->escape(addslashes($getHTAX))." then hour_fee * ".$this->db->escape(addslashes($getHTAXRATE))." else 0 end

		where (bill_date, teacher_id) in

		(SELECT H.BILL_DATE, H.TEACHER_ID FROM hour_traffic_tax H JOIN teacher T ON H.Teacher_ID = T.id WHERE H.SEQ = ".$this->db->escape(addslashes($inSeq))." and (T.id_type = '1' or T.id_type = '3')) ");

		/*計算實付*/

		$this->db->query("UPDATE hour_bill SET

		AFTERTAX = SUBTOTAL - (tax+ H_TAX)

		where (bill_date, teacher_id) in

		(SELECT BILL_DATE, TEACHER_ID FROM hour_traffic_tax WHERE SEQ = ".$this->db->escape(addslashes($inSeq)).")");

	}

	function update_13D_hourapp($appseq){

        $today = date('Y-m-d');
        $sql = sprintf("UPDATE hour_app SET cash = 'Y',entry_date = date(
                                '%s 00:00:00'
                            ) WHERE app_seq = %s",$today,$this->db->escape(addslashes($appseq)));
//'YYYY/MM/DD HH24:MI:SS'
        if ($this->db->query($sql) === false) {
            $this->db->query($sql);
            return "設定失敗";
        } else {
            $sql = sprintf("SELECT seq FROM hour_app WHERE app_seq = %s",$this->db->escape(addslashes($appseq)));

			$seqModel = $this->db->query($sql);
			$seqModel = $this->QueryToArray($seqModel);
			for ($i=0; $i < sizeof($seqModel); $i++) { 
				$seqList=$seqModel[$i];
				$sql_upd = sprintf("UPDATE hour_traffic_tax SET entry_date = date('%s 00:00:00') WHERE seq = %s",$today,$this->db->escape(addslashes($seqList['seq'])));
                $this->db->query($sql_upd);
			}
            return '設定成功';
        }
    }

}
