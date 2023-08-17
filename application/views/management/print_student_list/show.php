<?php
include_once "init.inc.php";
//$db->debug = true;

define("MAX_TITLE_LEN", 9); 
define("MAX_BUREAU_LEN", 22); 

$data = array();
if(isset($_REQUEST['year']))
{
  $year = mysqli_real_escape_string($db, sanitize($_REQUEST['year']));
}
if(isset($_REQUEST['class_no']))
{
  $class_no = mysqli_real_escape_string($db, sanitize($_REQUEST['class_no']));
}
if(isset($_REQUEST['term']))
{
  $term = mysqli_real_escape_string($db, sanitize($_REQUEST['term']));
}
if(isset($_REQUEST['ShowTel']))
{
  $ShowTel = mysqli_real_escape_string($db, sanitize($_REQUEST['ShowTel']));
}

$tmp_seq = mysqli_real_escape_string($db, sanitize($_REQUEST['tmp_seq']));

if($_POST){     
    //9C用的
    $query_cond="";
    if($_POST['query_year']!="")
        $query_cond.=" and year='".mysqli_real_escape_string($db,$_POST['query_year'])."' ";
    if($_POST['class_name'])
        $query_cond.=" and class_name like '%".mysqli_real_escape_string($db,$_POST['class_name'])."%' ";
    if($_POST['class_name_s']){
        //上課日期起
    }
    if($_POST['class_name_e']){
        //上課日期迄
    }
    if($_POST['sex']){
        //性別
        $sex=$_POST['sex'];
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
    if($_POST['studentName']){
        //學生姓名
        $studentName=$_POST['studentName'];
        $query_cond.=" and (first_name like '%".mysqli_real_escape_string($db,$studentName)."%' 
                            or last_name like '%".mysqli_real_escape_string($db,$studentName)."%' 
                            or first_name || last_name like '%".mysqli_real_escape_string($db,$studentName)."%') ";
    }
    if($_POST['beaurau_id']){
        //局處
        $beaurau_id=mysqli_real_escape_string($db,$_POST['beaurau_id']);
    }
    if($_POST['bYear_s']){
        //生日起
    }
    if($_POST['bYear_e']){
        //生日迄
    }
    //echo $query_cond;
    $sql="select a.st_no,a.beaurau_id, a.group_no,
          c.name as bureau_name, t.DESCRIPTION as title,
          b.first_name || b.last_name as name,
          case 
          when a.id like '_1%' then '1'
          when a.id like '_2%' then '2'
          end as sex,b.CO_EMPDB_POFTEL as cell_phone,
          a.yn_sel
          from ONLINE_APP a
        left join vm_all_account b on a.id = b.personal_id
        left join bureau_code c on c.bureau_id = b.beaurau_id
        left join require d on a.year=d.year and a.class_no=d.class_no and a.term=d.term
        left join code_table t on b.title=t.item_id and t.type_id='02' 
        where 1=1 $query_cond and a.yn_sel  in ('1','3','4','5','8') 
        order by st_no";
}else{
    
    //7A 9A用的
    $sql = "SELECT
    A .st_no,
    A .beaurau_id,
    A .group_no,
    NVL (og.ou_gov, c. NAME) bureau_name,
    T .DESCRIPTION AS title,
    b.first_name || b.last_name AS NAME,
    CASE
WHEN A . ID LIKE '_1%' THEN
    '1'
WHEN A . ID LIKE '_2%' THEN
    '2'
END AS sex,
 b.office_tel AS cell_phone,
 A .yn_sel
FROM
    ONLINE_APP A
LEFT JOIN vm_all_account b ON A . ID = b.personal_id
LEFT JOIN bureau_code c ON c.bureau_id = b.beaurau_id
LEFT OUTER JOIN OUT_GOV og ON b.PERSONAL_ID = og. ID 
LEFT JOIN code_table T ON b.title = T .item_id
AND T .type_id = '02'
WHERE
    YEAR = '$year'
AND class_no = '$class_no'
AND term = '{$term}'
AND A .yn_sel IN ('1', '3', '4', '5', '8')
ORDER BY
    TO_NUMBER (A .group_no),
    A .st_no"; 
}

//echo "sql:".$sql;
//exit(0);
$rs=$db->query($sql);
sys_log_insert_2(basename(__FILE__),mysqli_real_escape_string($db, $_SESSION['FUNCTION_ID']),'02','06');

//查詢是否有分組
$groupNumber = $db->GetOne("select count(distinct group_no) from online_app where year='".mysqli_real_escape_string($db,$year)."' and class_no='".mysqli_real_escape_string($db,$class_no)."' and term='".mysqli_real_escape_string($db,$term)."' and yn_sel  in ('1','3','8')");
if ($groupNumber > 0) {
    $isGroup = true;
} else {
    $isGroup = false;
}

$class_name = $db->GetOne("select r.class_name from require r where r.year='".mysqli_real_escape_string($db,$year)."' and r.class_no='".mysqli_real_escape_string($db,$class_no)."' and r.term='".mysqli_real_escape_string($db,$term)."'");

require('fpdf1/chinese-unicode.php');
//require('font/makefont/makefont.php');
$pdf=new PDF_Unicode();
$pdf->Open();
$pdf->AddPage();

$pdf->SetMargins(7,5,10,10);
$pdf->AddUniCNShwFont('uni');  //fontA 可用習慣名稱
//$pdf->SetFont('uni', 'B', 8 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
//$setTOP=22;
$pdf->SetAutoPageBreak(false);
//$pdf->SetAutoPageBreak(true);

//$class_name = iconv("UTF-8","BIG5",$class_name);
$title="臺北市政府公務人員訓練處           研習人員名冊"; 
$title1=$year."年度  ".$class_name."  第".$term."期";

$beaurau_id = $db->GetOne("select beaurau_id from vm_all_account where username = '".mysqli_real_escape_string($db,$_SESSION['Login']['username'])."' ");


//表頭
//$pdf->SetFontSize(12);
$pdf->SetFont('uni', 'B', 12 );
$pdf->Cell(180,5,$title,0,1,'C');
//$pdf->SetFontSize(10);
$pdf->SetFont('uni', 'B', 11 );
$pdf->Cell(180,5,$title1,0,1,'C');
$pdf->SetFont('uni', 'B', 10 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )

$layoutParameter = array();

//沒組別沒電話
$layoutParameter[0] = array(
    array(
        'fieldName' => 'GROUP_NO',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 1,
        'end' => 0
    ),
    array(
        'fieldName' => 'ST_NO',
        'titileName' => '學號',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'BUREAU_NAME',
        'titileName' => '服務單位',
        'width' => 80,
        'align' => 'L',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'TITLE',
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
        'fieldName' => 'SEX',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'CELL_PHONE',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
        'skip' => 1,
        'end' => 0
    ),
    array(
        'fieldName' => 'NOTE',
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
        'fieldName' => 'GROUP_NO',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 1,
        'end' => 0
    ),
    array(
        'fieldName' => 'ST_NO',
        'titileName' => '學號',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'BUREAU_NAME',
        'titileName' => '服務單位',
        'width' => 60,
        'align' => 'L',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'TITLE',
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
        'fieldName' => 'SEX',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'CELL_PHONE',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'NOTE',
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
        'fieldName' => 'GROUP_NO',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'ST_NO',
        'titileName' => '學號',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'BUREAU_NAME',
        'titileName' => '服務單位',
        'width' => 90,
        'align' => 'L',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'TITLE',
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
        'fieldName' => 'SEX',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'CELL_PHONE',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
        'skip' => 1,
        'end' => 0
    ),
    array(
        'fieldName' => 'NOTE',
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
        'fieldName' => 'GROUP_NO',
        'titileName' => '組別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'ST_NO',
        'titileName' => '學號',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'BUREAU_NAME',
        'titileName' => '服務單位',
        'width' => 70,
        'align' => 'L',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'TITLE',
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
        'fieldName' => 'SEX',
        'titileName' => '性別',
        'width' => 10,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'CELL_PHONE',
        'titileName' => '電話',
        'width' => 30,
        'align' => 'C',
        'skip' => 0,
        'end' => 0
    ),
    array(
        'fieldName' => 'NOTE',
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

    $pdf->SetFont('uni', 'B', 10 );
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
    $total=$rs->RecordCount();//總筆數
    $page_total=ceil($total/$page_num);//總頁數
    
    while ($arr = $rs->FetchRow()):
        if($i>$page_num){
            $pdf->Cell(180,15,"第".$page."/".$page_total."頁",0,1,"C");
            $pdf->AddPage();
            $pdf->Cell(180,5,"",0,1,'C');
            //start 表頭
            $pdf->SetFont('uni', 'B', 12 );
            //$pdf->SetFontSize(12);
            $pdf->Cell(180,5,$title,0,1,'C');
            $pdf->SetFont('uni', 'B', 11 );
            //$pdf->SetFontSize(10);
            $pdf->Cell(180,5,$title1,0,1,'C');
            $pdf->SetFont('uni', 'B', 10 );
            
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
        
        $pdf->SetFont('uni', 'B', 10 );
        if ($tmp_seq!='0') {
             if ($beaurau_id==$arr["BEAURAU_ID"]) {
                $pdf->SetFont('uni', 'U', 11 );          //設定文字格式SetFont('字體名稱', '底線', SIZE )
                $arr["ST_NO"] = "*".$arr["ST_NO"];
            } else {
                $arr["ST_NO"] = $arr["ST_NO"];
                $pdf->SetFont('uni', 'B', 10 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
            }
        }
        
        //$pdf->SetFont('uni', 'B', 10 );
        $arr["SEX"] = ($arr["SEX"]=="1"?"男":"女");
        $arr['NOTE'] = '';
        if ($arr["YN_SEL"] == 4) {
            $arr['NOTE'] = '退訓';
        } else if ($arr["YN_SEL"] == 5) {
            $arr['NOTE'] = '未報到';
        }
        
        foreach ($contentLayout as $key => $value) {
                
            if ($value['skip'] == 1) {
                continue;
            }
            
            // truncate str if strlen is large
            if ($value["fieldName"]=='TITLE') {
                $titlename = mb_substr($arr[$value["fieldName"]],0,MAX_TITLE_LEN,'utf8');
                if ($titlename!=$arr[$value["fieldName"]]) {
                    $arr[$value["fieldName"]] = $titlename."#";
                }
            }   
            if ($value["fieldName"]=='BUREAU_NAME') {
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
    endwhile;


$pdf->Cell(180,15,"第".$page."/".$page_total."頁",0,1,"C");
$pdf->Output();;
?>