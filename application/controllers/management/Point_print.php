<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Point_print extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('management/point_print_model');
        $this->load->model('management/point_create_model');
        $this->load->model('management/export_score_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions['online_app.year'] = $this->data['filter']['year'];

        if(!in_array("1", $this->flags->user['group_id'])){
            if(in_array("8", $this->flags->user['group_id'])){
                $conditions['r.worker'] = $this->flags->user['idno'];
            }
        }

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        // $attrs['where_special'] = "(year, class_no, term) in (select distinct year, class_no, term from online_app)";
        $this->data['filter']['total'] = $total = $this->point_print_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        // $attrs['where_special'] = "(year, class_no, term) in (select distinct year, class_no, term from online_app)";
        $this->data['list'] = $this->point_print_model->getList($attrs);
        foreach ($this->data['list'] as & $row) {
            $row['url'] = base_url("management/point_print/print_student_score/{$row['seq_no']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/point_print?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/point_print/");
        $this->layout->view('management/point_print/list',$this->data);
    }

    public function print_student_score($seq_no)
    {
        //查期別資料
        $require_data = $this->export_score_model->get($seq_no);

        if(empty($require_data)){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/point_print/'));
        }

        $termInfo = $require_data;
        $endDate = substr($termInfo['end_date1'], 0, 4).'年'.substr($termInfo['end_date1'], 5, 2).'月'.substr($termInfo['end_date1'], 8, 2);
        $startDate = substr($termInfo['start_date1'], 0, 4).'年'.substr($termInfo['start_date1'], 5, 2).'月'.substr($termInfo['start_date1'], 8, 2);

        //查詢成績 (用總分排序)
        $sortModel = array();
        $baseModel = $this->point_create_model->getScoreInfoByPkey($require_data['year'], $require_data['class_no'], $require_data['term'], true);
        // jd($baseModel);
        foreach ($baseModel as $row) {
            $sortModel[sprintf('%s%04d', $row['final_score']*100, $row['st_no'])] = $row;
        }
        krsort($sortModel);
        // jd($sortModel,1);

        $unsortModel = array();
        foreach ($sortModel as $subModel) {
            $unsortModel[] = $subModel;
        }

        //奇怪的排序方式
        $modelWinner = array();
        for ($i = 0; $i < 3; $i++) {
            if (isset($unsortModel[$i])) {
                array_push($modelWinner, $unsortModel[$i]);
                unset($unsortModel[$i]);
            }
        }
        $model = array();
        foreach ($unsortModel as $row) {
            $model[$row['st_no']] = $row;
        }
        ksort($model);

        $head = '
        <table width="100%"><tr><td align="center" style="font-size:130%;"><b>臺北市政府公務人員訓練處 '.$termInfo['year'].' 年度 '.$termInfo['class_name'].' 第 '.$termInfo['term'].' 期學員成績冊</b></td></tr></table>
        <br />
        <table width="100%"><tr><td align="center" style="font-size:130%;"><b>&lt;研習期間 '.$startDate.' 起至 '.$endDate.' 止&gt;</b></td></tr></table>
        <br />
        ';

        $outputHTML = '';
        $outputHTML .= '<table width="100%" boder="1" style="margin-left:auto; margin-right:auto;">';
        $outputHTML .= '<tr style="border-bottom: 1px #000 dashed;">';
        $outputHTML .= '<td align="center" width="10%" style="border-bottom: 1px #000 dashed;">名次</td>';
        $outputHTML .= '<td align="center" width="10%" style="border-bottom: 1px #000 dashed;">學號</td>';
        $outputHTML .= '<td align="center" width="30%" style="border-bottom: 1px #000 dashed;">服務單位</td>';
        $outputHTML .= '<td align="center" width="10%" style="border-bottom: 1px #000 dashed;">職稱</td>';
        $outputHTML .= '<td align="center" width="15%" style="border-bottom: 1px #000 dashed;">姓名</td>';
        $outputHTML .= '<td align="left" width="15%" style="border-bottom: 1px #000 dashed;">總成績</td>';
        $outputHTML .= '</tr>';

        $i = 1;
        foreach ($modelWinner as $row) {
            $outputHTML .= '<tr>';
            $outputHTML .= '<td align="center">' . $i++ . '</td>';
            $outputHTML .= '<td align="center">' . $row["st_no"] . '</td>';
            $outputHTML .= '<td align="left">' . $row["beaurau_name"] . '</td>';
            $outputHTML .= '<td align="left">' . $row["title_name"] . '</td>';
            $outputHTML .= '<td align="center">' . $row["name"] . '</td>';
            $outputHTML .= '<td align="left">' . $row["p_score"] . "(" . $row["final_score"] . ")". '</td>';
            $outputHTML .= '</tr>';
        }

        if (is_array($model) && count($model)>0) {
            $outputHTML .= '<tr style="border-bottom: 1px #000 dashed;">';
            $outputHTML .= '<td align="center" width="10%"></td>';
            $outputHTML .= '<td align="left" colspan="5"><b>&lt;以下用學號排序&gt;</b></td>';
            $outputHTML .= '</tr>';
        }

        foreach ($model as $row) {
            $outputHTML .= '<tr>';
            $outputHTML .= '<td align="center"></td>';
            $outputHTML .= '<td align="center">' . $row["st_no"] . '</td>';
            $outputHTML .= '<td align="left">' . $row["beaurau_name"] . '</td>';
            $outputHTML .= '<td align="left">' . $row["title_name"] . '</td>';
            $outputHTML .= '<td align="center">' . $row["name"] . '</td>';
            $outputHTML .= '<td align="left">' . $row["p_score"] . '</td>';
            $outputHTML .= '</tr>';
        }

        $outputHTML .= '<tr style="border-top: 1px #000 dashed;">';
        $outputHTML .= '<td align="left" style="border-top: 1px #000 dashed;" colspan="6">註記: 成績90分以上列優等，80分以上列甲等，70分以上列乙等，以此類推。另成績前 3 名分數及等第併列。</td>';
        $outputHTML .= '</tr>';
        $outputHTML .= '</table>';
        $outputHTML = $head . $outputHTML;

        echo $outputHTML;
    }
    
}
