<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aa_chu2 extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/aa_chu2_model');
        $this->data['choices']['year'] = $this->_get_year_list();

        $this->data['choices']['season_List'] = array(
            '' => '請選擇季別',
            '1' => '第1季',
            '2' => '第2季',
            '3' => '第3季',
            '4' => '第4季',
        );

        $this->data['choices']['category'] = array(
            '' => '全選',
            'A' => '行政系列',
            'B' => '發展系列',
        );

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }
        if (!isset($this->data['filter']['season'])) {
            $this->data['filter']['season'] = '';
        }
        if (!isset($this->data['filter']['category'])) {
            $this->data['filter']['category'] = '';
        }
        if (!isset($this->data['filter']['show'])) {
            $this->data['filter']['show'] = '1';
        }

    }

    public function index()
    {
        // jd($this->data['filter'],1);

        $this->data['link_pdf'] = base_url("management/aa_chu2/print_pdf");
        $this->data['link_refresh'] = base_url("management/aa_chu2/");
        $this->layout->view('management/aa_chu2/list',$this->data);
    }

    public function print_pdf()
    {
        $this->load->library('pdf/PDF_Chinesess');
        $this->load->library('pdf/font/makefont/Makefont123');

        $query_condition = '';
        $query_condition .= " a.year = ".$this->db->escape(addslashes($this->data['filter']['year']))."  ";
        $query_condition .= "and a.reason = ".$this->db->escape(addslashes($this->data['filter']['season']))."  ";
        //原本的CODE 不分行政系列 發展系列全查

        if ($this->data['filter']['show'] == '2') {
            $query_condition .= " and (select count(*) from online_app where year=a.year and class_no=a.class_no and term=a.term and yn_sel in ('3','8','1')) <> 0";
        }
        $type_data = $this->aa_chu2_model->getList($query_condition);
        $type_query_data=array();
        $b['A']['A1']=0;
        $b['A']['A2']=0;
        $b['B']['A1']=0;
        $b['B']['A2']=0;
        foreach($type_data as $type_row){
            if($type_row['TYPE']=="A"){
            $type_query_data['A'][]=$type_row;
            $b['A']['A1']+=$type_row['seled_no_persons'];
            $b['A']['A2']+=$type_row['no_persons'];
            }
            if($type_row['TYPE']=="B"){
                $type_query_data['B'][]=$type_row;
                $b['B']['A1']+=$type_row['seled_no_persons'];
                $b['B']['A2']+=$type_row['no_persons'];
            }
        }

        $sql="with s1 as
            (select a.type,a.CLASS_NO,a.CLASS_NAME+'(第'+a.TERM+'期)' as CLASS_NAME,a.TERM,a.REASON,a.BEAURAU_ID,(select count(*) from online_app where year=a.year and class_no=a.class_no and term=a.term and yn_sel in ('3','8','1')) as seled_no_persons ,a.no_persons,b.name as description from `require` a
            left join second_category b on  a.beaurau_id=b.item_id and a.type=b.parent_id
            where  {$query_condition} and a.type in ('A') order by a.beaurau_id,a.term)
            ,s2 as
            (select  a.type,a.CLASS_NO,a.CLASS_NAME+'(第'+a.TERM+'期)' as CLASS_NAME,a.TERM,a.REASON,a.beaurau_id as BEAURAU_ID ,(select count(*) from online_app where year=a.year and class_no=a.class_no and term=a.term and yn_sel in ('3','8','1')) as seled_no_persons ,a.no_persons,b.name as description from `require` a
            left join second_category b on  a.beaurau_id=b.item_id and a.type=b.parent_id
            where  {$query_condition} and a.type in ('B')  order by a.beaurau_id,a.term) select * from s1 union all select * from s2 ";

        $all_count = $this->aa_chu2_model->get_all_count($sql);
        $test_arr=range("A","B");
        $beaurau_count = $this->aa_chu2_model->get_beaurau_count($test_arr, $query_condition);
        $pdf=new PDF_Chinesess();
        $pdf->Open();
        $pdf->AddPage();
        $pdf = $this->_get_pdf($pdf,$type_query_data,$all_count,$beaurau_count,$b);
        ob_clean();
        $pdf->Output();
    }

    public function _get_pdf(&$pdf,$query_data,$a1,$a2,$b)
    {
        $pdf->SetMargins(15,5,15,10);
        $pdf->AddBig5Font('fontA', '標楷體');  //fontA 可用習慣名稱
        $pdf->SetFont('fontA', 'B', 8 );          //設定文字格式SetFont('字體名稱', '粗體', SIZE )
        //$setTOP=22;
        $pdf->SetAutoPageBreak(true);

        $title="臺北市政府公務人員訓練處    ";

        $title.= "班級學員統計表";
        $title= @iconv("UTF-8","big5",$title);
        //表頭
        $pdf->SetFontSize(14);
        $pdf->Cell(180,10,$title,0,1,'C');

        //col=180
        $pdf->SetFontSize(12);
        $pdf->Cell(120,10,@iconv("UTF-8","big5","系列/局處/班期名稱"),1,0,'C');
        $pdf->Cell(30,10,@iconv("UTF-8","big5","實際選員人數"),1,0,'C');
        $pdf->Cell(30,10,@iconv("UTF-8","big5","計劃訓練人數"),1,1,'C');

        $pdf->Cell(120,10,@iconv("UTF-8","big5","總計"),0,0,'L');
        $pdf->Cell(30,10,$a1['A1'],0,0,'R');
        $pdf->Cell(30,10,$a1['A2'],0,1,'R');

        $page_num=25;//一頁顯示的資料筆數
        $i=1;
        $page=1;//頁碼
        //$total=$rs->RecordCount();//總筆數
        $total=count($query_data);//總筆數
        $page_total=ceil($total/$page_num);//總頁數
        if(!$page_total)
           $page_total=1;
        //while ($arr = $rs->FetchRow()):
        //print_r($query_data);
        $j=1;
        $tmp1="";
        $tmp2="";
        $tmp3="";
        foreach($query_data as $key=>$val){
           foreach($val as $key1=>$val1){
               //print_r($val1);
               if($val1['TYPE']=='A'){
                   $name="行政系列";
                   $name_id='A';
               }
               elseif($val1['TYPE']=='B'){
                   $name="發展系列";
                   $name_id='B';
               }
               $name=@iconv("UTF-8","big5",$name);
               if($tmp1<>$name){
                   $pdf->Cell(120,10,$name,0,0,'L');
                    $pdf->Cell(30,10,trim($b[$key]['A1']),0,0,'R');
                    $pdf->Cell(30,10,trim($b[$key]['A2']),0,1,'R');
                    $tmp1=$name;
               }
               if($tmp2<>$val1['description']){
                   //echo $name_id.$row['BEAURAU_ID'];
                   $pdf->Cell(120,10,"     ".@iconv("UTF-8","big5",$val1['description']),0,0,'L');
                   if(isset($a2[$name_id.$val1['BEAURAU_ID']]['A1'])){
                   	$pdf->Cell(30,10,trim($a2[$name_id.$val1['BEAURAU_ID']]['A1']),0,0,'R');
                   }else{
                   	$pdf->Cell(30,10, '',0,0,'R');
                   }
                   if(isset($a2[$name_id.$val1['BEAURAU_ID']]['A2'])){
                   	$pdf->Cell(30,10,trim($a2[$name_id.$val1['BEAURAU_ID']]['A2']),0,1,'R');
                   }else{
                   	$pdf->Cell(30,10, '',0,1,'R');
                   }

                    $tmp2=$val1['description'];
               }
                //$word_name = $val1['CLASS_NAME']."第".$val1['TERM']."期";
                $pdf->Cell(120,10,"          ".@iconv("UTF-8","big5",$val1['CLASS_NAME']),0,0,'C');
            $pdf->Cell(30,10,$val1['seled_no_persons'],0,0,'R');
            $pdf->Cell(30,10,$val1['no_persons'],0,1,'R');
            $i++;
            $j++;
            }
        }
        $pdf->Cell(180,15,@iconv("UTF-8","big5","第").$page."/".$page_total.@iconv("UTF-8","big5","頁"),0,1,"C");
        return $pdf;

    }

    public function _get_year_list()
    {
        $year_list = array();

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1910;

        for($i=$this_yesr; $i>=90; $i--){
            $year_list[$i] = $i;
        }
        // jd($year_list,1);
        return $year_list;
    }

}
