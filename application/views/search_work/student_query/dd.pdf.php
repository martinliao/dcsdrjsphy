<?php
function QueryToArray($query){
	$arrAll = array();
	if($query->num_rows() > 0)
	{
		foreach($query->result_array() as $row)
		{
			array_push($arrAll,$row);
		}
	}
	return $arrAll;
 }
//include_once "init.inc.php";
//$db->debug = true;

define("MAX_TITLE_LEN", 9); 
define("MAX_BUREAU_LEN", 22); 

$data = array();
if(isset($_REQUEST['year']))
{
  $year = $this->input->get_post('year');
}
if(isset($_REQUEST['class_no']))
{
  $class_no = $this->input->get_post('class_no');
}
if(isset($_REQUEST['term']))
{
  $term = $this->input->get_post('term');
}
$ShowTel=true;
if(isset($_REQUEST['ShowTel']))
{
  $ShowTel = $this->input->get_post('ShowTel');
}
if(isset($_REQUEST['username']))
{
  $username = $this->input->get_post('username');
}
$tmp_seq = $this->input->get_post('tmp_seq');

if($this->input->post()){  	
    //9C用的
    $query_cond="";
    if($this->input->post('query_year')!="")
        $query_cond.=" and year=".$this->db->escape(addslashes($this->input->post('query_year')))." ";
    if($this->input->post('class_name'))
        $query_cond.=" and class_name like ".$this->db->escape("%".addslashes($this->input->post('class_name'))."%")."' ";
    if($this->input->post('class_name_s')){
        //上課日期起
    }
    if($this->input->post('class_name_e')){
        //上課日期迄
    }
    if($this->input->post('sex')){
        //性別
        $sex=$this->input->post('sex');
        switch($sex){
            case 1:
                $query_cond.=" a.id like '_1%' ";
            break;
            case 2:
                $query_cond.=" and a.id like '_2%' ";
            break;
        }
        //$query_cond.=" and sex='$sex' ";
    }
    if($this->input->post('studentName')){
        //學生姓名
        $studentName=$this->input->post('studentName');
        $query_cond.=" and (first_name like ".$this->db->escape("%".addslashes($studentName)."%")." 
                            or last_name like ".$this->db->escape("%".addslashes($studentName)."%")." 
                            or concat(first_name , last_name) like ".$this->db->escape("%".addslashes($studentName)."%").") ";
    }
    if($this->input->post('beaurau_id')){
        //局處
        $beaurau_id=$this->input->post('beaurau_id');
    }
    if($this->input->post('bYear_s')){
        //生日起
    }
    if($this->input->post('bYear_e')){
        //生日迄
    }
    //echo $query_cond;
    $sql="select a.st_no,a.beaurau_id, a.group_no,
          c.name as bureau_name, t.DESCRIPTION as title,
          name,
          case 
          when a.id like '_1%' then '1'
          when a.id like '_2%' then '2'
          end as sex,b.co_empdb_poftel as cell_phone,
          a.yn_sel
          from online_app a
	    left join BS_user b on a.id = b.idno
	    left join bureau c on c.bureau_id = b.bureau_id
	    left join `require` d on a.year=d.year and a.class_no=d.class_no and a.term=d.term
	    left join code_table t on b.title=t.item_id and t.type_id='02' 
	    where 1=1 $query_cond and a.yn_sel  in ('1','3','4','5','8') 
	    order by st_no";
}else{
	
    //7A 9A用的
    $sql = "SELECT
	A.st_no,
	A.beaurau_id,
	A.group_no,
	NVL (og.ou_gov, c. NAME) bureau_name,
	T .DESCRIPTION AS title,
	b.name AS NAME,
	CASE
WHEN A.ID LIKE '_1%' THEN
	'1'
WHEN A.ID LIKE '_2%' THEN
	'2'
END AS sex,
 b.office_tel AS cell_phone,
 A .yn_sel
FROM
	online_app A
LEFT JOIN BS_user b ON A.ID = b.idno
LEFT JOIN bureau c ON c.bureau_id = b.bureau_id
LEFT OUTER JOIN out_gov og ON b.idno = og.ID 
LEFT JOIN code_table T ON b.job_title = T.item_id
AND T .type_id = '02'
WHERE
	YEAR = ".$this->db->escape(addslashes($year))."
AND class_no = ".$this->db->escape(addslashes($class_no))."
AND term = ".$this->db->escape(addslashes($term))."
AND A.yn_sel IN ('1', '3', '4', '5', '8')
ORDER BY
    A.group_no,
	A.st_no"; 
}

//echo "sql:".$sql;
//exit(0);
$rs=$this->db->query($sql);
$rs = QueryToArray($rs);
//sys_log_insert_2(basename(__FILE__),$_SESSION['FUNCTION_ID'],'02','06');

//查詢是否有分組
$groupNumber = QueryToArray($this->db->query("select count(distinct group_no) as cnt from online_app where year=".$this->db->escape(addslashes($year))." and class_no=".$this->db->escape(addslashes($class_no))." and term=".$this->db->escape(addslashes($term))." and yn_sel  in ('1','3','8')"))[0]['cnt'];
if ($groupNumber > 0) {
    $isGroup = true;
} else {
    $isGroup = false;
}

$class_name = QueryToArray($this->db->query("select r.class_name from `require` r where r.year=".$this->db->escape(addslashes($year))." and r.class_no=".$this->db->escape(addslashes($class_no))." and r.term=".$this->db->escape(addslashes($term)).""))[0]['class_name'];

class MYPDF extends TCPDF {
    // Page footer
    function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('msungstdlight', '', 8);
        // Page number
        $this->Cell(0, 10, $this->getAliasNumPage(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$this->load->library('pdf/PHP_TCPDF');
$pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
//$pdf->setPrintFooter(false); // 使用印列頁碼時，$pdf->setPrintFooter(false);這行要mark掉
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->AddPage();
//$pdf->SetMargins(15,15,15,15);
$pdf->SetFont('msungstdlight', '', 8);
$pdf->SetAutoPageBreak(true, 13);

$title="臺北市政府公務人員訓練處           研習人員名冊";	
$title1=$year."年度  ".$class_name."  第".$term."期";

$beaurau_id = QueryToArray($this->db->query("select bureau_id as beaurau_id from BS_user where username = ".$this->db->escape(".".addslashes($username).".")." "));


//表頭
//$pdf->SetFontSize(12);
$pdf->SetFont('msungstdlight', 'B', 12 );
$pdf->Cell(180,5,$title,0,1,'C');
//$pdf->SetFontSize(10);
$pdf->SetFont('msungstdlight', 'B', 11 );
$pdf->Cell(180,5,$title1,0,1,'C');
$pdf->SetFont('msungstdlight', 'B', 10 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )

$layoutParameter = array();

//沒組別沒電話
$layoutParameter[0] = array(
    array(
    	'fieldName' => 'group_no',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 1,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'st_no',
        'titileName' => '學號',
    	'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'bureau_name',
        'titileName' => '服務單位',
    	'width' => 80,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'title',
        'titileName' => '職稱',
        'width' => 40,
        'align' => 'L',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'NAME',
        'titileName' => '姓名',
        'width' => 20,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'sex',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'cell_phone',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
    	'skip' => 1,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'note',
        'titileName' => '備註',
        'width' => 15,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 1
    )
);

//沒組別有電話
$layoutParameter[1] = array(
    array(
    	'fieldName' => 'group_no',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 1,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'st_no',
        'titileName' => '學號',
    	'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'bureau_name',
        'titileName' => '服務單位',
    	'width' => 60,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'title',
        'titileName' => '職稱',
        'width' => 40,
        'align' => 'L',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'NAME',
        'titileName' => '姓名',
        'width' => 20,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'sex',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'cell_phone',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'note',
        'titileName' => '備註',
        'width' => 15,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 1
    )
);


//有組別沒電話
$layoutParameter[2] = array(
    array(
    	'fieldName' => 'group_no',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'st_no',
        'titileName' => '學號',
    	'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'bureau_name',
        'titileName' => '服務單位',
    	'width' => 90,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'title',
        'titileName' => '職稱',
        'width' => 30,
        'align' => 'L',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'NAME',
        'titileName' => '姓名',
        'width' => 30,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'sex',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'cell_phone',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
    	'skip' => 1,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'note',
        'titileName' => '備註',
        'width' => 15,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 1
    )
);

//有組別有電話
$layoutParameter[3] = array(
    array(
    	'fieldName' => 'group_no',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'st_no',
        'titileName' => '學號',
    	'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'bureau_name',
        'titileName' => '服務單位',
    	'width' => 70,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'title',
        'titileName' => '職稱',
        'width' => 20,
        'align' => 'L',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'NAME',
        'titileName' => '姓名',
        'width' => 30,
        'align' => 'L',
        'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'sex',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'cell_phone',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 0
    ),
    array(
    	'fieldName' => 'note',
        'titileName' => '備註',
        'width' => 15,
        'align' => 'C',
    	'skip' => 0,
    	'end' => 1
    )
);


if ($isGroup && $ShowTel) {
    $contentLayout = $layoutParameter[3];
} else if ($isGroup && !$ShowTel) {
    $contentLayout = $layoutParameter[2];
} else if (!$isGroup && $ShowTel) {
    $contentLayout = $layoutParameter[1];
} else {
    $contentLayout = $layoutParameter[0];
}

	$pdf->SetFont('msungstdlight', 'B', 10 );
    //$pdf->SetFontSize(10);
    foreach ($contentLayout as $key => $value) {
        
        if ($value['skip'] == 1) {
            continue;
        }
        
        if ($value['end'] == 0) {
            $pdf->Cell($value['width'],10,$value['titileName'],1,0,'C');
        } else {
            $pdf->Cell($value['width'],10,$value['titileName'],1,1,'C');
            break;
        }
        
    }
    
    $page_num=42;//一頁顯示的資料筆數
    $i=1;
    $page=1;//頁碼
    $total=sizeof($rs);//總筆數
    $page_total=ceil($total/$page_num);//總頁數
    for ($j=0; $j < sizeof($rs); $j++) {
        $arr=$rs[$j]; 
        if($i>$page_num){
            $pdf->Cell(180,15,"第".$page."/".$page_total."頁",0,1,"C");
            $pdf->AddPage();
            $pdf->Cell(180,5,"",0,1,'C');
            //start 表頭
			$pdf->SetFont('msungstdlight', 'B', 12 );
            //$pdf->SetFontSize(12);
            $pdf->Cell(180,5,$title,0,1,'C');
			$pdf->SetFont('msungstdlight', 'B', 11 );
			//$pdf->SetFontSize(10);
			$pdf->Cell(180,5,$title1,0,1,'C');
			$pdf->SetFont('msungstdlight', 'B', 10 );
            
            foreach ($contentLayout as $key => $value) {
                
                if ($value['skip'] == 1) {
                    continue;
                }
                
                if ($value['end'] == 0) {
                    $pdf->Cell($value['width'],10,$value['titileName'],1,0,'C');
                } else {
                    $pdf->Cell($value['width'],10,$value['titileName'],1,1,'C');
                    break;
                }
                
            }
        	//end 表頭
            $i=1;
            $page++;
        }
        
		$pdf->SetFont('msungstdlight', 'B', 10 );
        if ($tmp_seq!='0') {
	         if ($beaurau_id==$arr["beaurau_id"]) {
	        	$pdf->SetFont('msungstdlight', 'U', 11 );          //設定文字格式SetFont('字體名稱', '底線', SIZE )
	        	$arr["st_no"] = "*".$arr["st_no"];
	        } else {
	        	$arr["st_no"] = $arr["st_no"];
	        	$pdf->SetFont('msungstdlight', 'B', 10 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
	        }
        }
        
		//$pdf->SetFont('msungstdlight', 'B', 10 );
        $arr["sex"] = ($arr["sex"]=="1"?"男":"女");
        $arr['note'] = '';
        if ($arr["yn_sel"] == 4) {
            $arr['note'] = '退訓';
        } else if ($arr["yn_sel"] == 5) {
            $arr['note'] = '未報到';
        }
        
        foreach ($contentLayout as $key => $value) {
                
            if ($value['skip'] == 1) {
                continue;
            }
			
			// truncate str if strlen is large
			if ($value["fieldName"]=='title') {
				$titlename = mb_substr($arr[$value["fieldName"]],0,MAX_TITLE_LEN,'utf8');
				if ($titlename!=$arr[$value["fieldName"]]) {
					$arr[$value["fieldName"]] = $titlename."#";
				}
			}	
			if ($value["fieldName"]=='bureau_name') {
				$bureau = mb_substr($arr[$value["fieldName"]],0,MAX_BUREAU_LEN,'utf8');
				if ($bureau!=$arr[$value["fieldName"]]) {
					$arr[$value["fieldName"]] = $bureau."#";
				}
			}	
            if ($value['end'] == 0) {
                $pdf->Cell($value["width"],6,$arr[$value["fieldName"]],0,0,$value["align"]);
            } else {
                $pdf->Cell($value["width"],6,$arr[$value["fieldName"]],0,1,$value["align"]);
                break;
            }
            
        }
        
        $i++;
    }


$pdf->Cell(180,15,"第".$page."/".$page_total."頁",0,1,"C");
$pdf->Output('record.pdf','D');
//header("Location: /base/admin/record.pdD");
?>