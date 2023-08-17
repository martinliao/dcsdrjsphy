<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Point_greate_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/point_greate_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions['online_app.count >'] = '0';

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }

        $attrs['where_special'] = " class_status in ('2','3') ";
        $this->data['filter']['total'] = $total = $this->point_greate_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }

        $attrs['where_special'] = " class_status in ('2','3') ";
        $this->data['list'] = $this->point_greate_model->getList($attrs);

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/point_greate_list?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/point_greate_list/");
        $this->data['link_seq_pdf'] = base_url("management/point_greate_list/print_student_seq_pdf");
        $this->data['link_bureau'] = base_url("management/point_greate_list/print_student_bureau");
        $this->layout->view('management/point_great_list/list',$this->data);
    }

    public function print_student_seq_pdf()
    {
        $post = $post = $this->input->post();
        $pkey = NULL;
        if(isset($post['pkey'])){
            $pkey = $post['pkey'];
        }
        if (is_null($pkey) || !is_array($pkey) || count($pkey)===0) {
            echo '請選擇班期';
            exit();
        }

        // 建立pkeys
        $pkeys = array();
        foreach ($pkey as $subPkey) {
            $infos = explode(',', $subPkey);
            array_push($pkeys, array(
                'year' => $infos[0],
                'class_no' => $infos[1],
                'term' => $infos[2]
            ));
        }

        //計算年度
        $max = intval($pkeys[0]['year']);
        $min = intval($pkeys[0]['year']);
        foreach ($pkeys as $pkeyInfo) {
            if (intval($pkeyInfo['year']) > $max) {
                $max = intval($pkeyInfo['year']);
            }
            if (intval($pkeyInfo['year']) < $min) {
                $min = intval($pkeyInfo['year']);
            }
        }

        //計算列印日期
        $year = (date('Y')-1911);
        if (!empty($post['print_year'])) {
            $year = $post['print_year'];
        }
        $month = date('m');
        if (!empty($post['print_month'])) {
            $month = $post['print_month'];
        }
        $day = date('d');
        if (!empty($post['print_day'])) {
            $day = $post['print_day'];
        }

        $model = $this->point_greate_model->getSeqInfoByPkeyList($pkeys);

        $head  = '<table width="100%"><tr><td align="center"><font size="18">臺北市政府公務人員訓練處</font></td></tr></table><br />';
        $head .= '<table width="100%"><tr><td align="center"><font size="14"> '.htmlspecialchars($min, ENT_HTML5|ENT_QUOTES).'-'.htmlspecialchars($max, ENT_HTML5|ENT_QUOTES).' 年度 績優學員名冊</font></td></tr></table>';
        $head .= '<table width="98%"><tr><td align="right"><font size="12">頒獎日期：'.htmlspecialchars($year, ENT_HTML5|ENT_QUOTES).'/'.htmlspecialchars($month, ENT_HTML5|ENT_QUOTES).'/'.htmlspecialchars($day, ENT_HTML5|ENT_QUOTES).'</font></td></tr></table>';

        $outputHTML  = '<center>';
        $outputHTML .= '<table width="90%" border="1" cellpadding="1" cellspacing="1">';
        $outputHTML .= '<tr>';
        $outputHTML .= '<td align="center" width="5%">編號</td>';
        $outputHTML .= '<td align="center" width="30%">單位</td>';
        $outputHTML .= '<td align="center" width="20%">姓名</td>';
        $outputHTML .= '<td align="center" width="25%">班期名稱</td>';
        $outputHTML .= '<td align="center" width="10%">名次</td>';
        $outputHTML .= '<td align="center" width="20%">備註</td>';
        $outputHTML .= '</tr>';

        foreach ($model as $arr) {

            $outputHTML .= '<tr>';
            $outputHTML .= '<td align="center">' . $arr["count"] . '</td>';
            if (empty($arr["beaurau_name"])) {
                $arr["beaurau_name"] = "&nbsp;";
            }
            $outputHTML .= '<td align="center">' . $arr["beaurau_name"] . '</td>';

            if ($arr["name"]==""){
                $tmp = "&nbsp;";
            } else {
                $tmp = $arr['name'].'<br />'.$arr['title_name'];
            }
            $outputHTML .= '<td align="center">' . $tmp . '</td>';

            if ($arr['keyno']==1) {
                $outputHTML .= '<td align="center" rowspan="'.$arr['with_count'].'">' . "{$arr['year']}年度 {$arr['class_name']} 第{$arr['term']}期" . '</td>';
            }


            switch ($arr["keyno"]) {
                case 1:
                    $rankText = '第一名';
                    break;
                case 2:
                    $rankText = '第二名';
                    break;
                case 3:
                    $rankText = '第三名';
                    break;
                default:
                    $rankText = '&nbsp;';
                    break;
            }
            $outputHTML .= '<td align="center">' . $rankText . '</td>';
            if(!isset($arr["memo"])){
                $arr["memo"] = '';
            }
            if ($arr["memo"]==""){
                $tmp = "&nbsp;";
            } else{
                $tmp = $arr["memo"];
            }
            $outputHTML .= '<td align="left">'   . $tmp . '</td>';
            $outputHTML .= '</tr>';
        }

        $outputHTML .= '<tr>';
        $outputHTML .= '<td align="left" colspan="6">合計'   . count($model) . '名</td>';
        $outputHTML .= '</tr>';

        $outputHTML .= '</table></center>';
        $outputHTML = $head . $outputHTML;

        // HTML 輸出
        if (isset($this->data['filter']['output_html'])) {
            echo $outputHTML;exit();
        }

        $this->load->library('pdf/PHP_TCPDF');

        // PDF 輸出
        $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage();
        $pdf->SetFont('msungstdlight', '', 12);
        $pdf->SetAutoPageBreak(true);
        $pdf->writeHTML($outputHTML, true, 0, true, 0);
        $pdf->lastPage();
        // ob_clean();
        $pdf->Output();
    }

    public function print_student_bureau()
    {
        $post = $post = $this->input->post();
        $pkey = NULL;
        if(isset($post['pkey'])){
            $pkey = $post['pkey'];
        }
        if (is_null($pkey) || !is_array($pkey) || count($pkey)===0) {
            echo '參數錯誤';
            exit();
        }
        // 建立pkeys
        $sort = 1;
        $pkeys = array();
        foreach ($pkey as $subPkey) {
            $infos = explode(',', $subPkey);
            array_push($pkeys, array(
                'year' => $infos[0],
                'class_no' => $infos[1],
                'term' => $infos[2]
            ));
        }

        //取資料
        $model = $this->point_greate_model->getSeqInfoByPkeyList($pkeys);

        //移除重複
        $buNames = array();
        foreach ($model as $array) {
            if (!empty($array['beaurau_name'])) {
                $buNames[] = $array['beaurau_name'];
            }
        }
        if(!empty($buNames)){
            $buNames=implode(",",$buNames);
        }
        $this->data['buNames'] = $buNames;
        $this->load->view("management/point_great_list/print_student_bureau", $this->data);
    }

}
