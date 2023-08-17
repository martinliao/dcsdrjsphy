<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Passport_print extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('management/point_print_model');
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
            $row['url'] = base_url("management/passport_print/print_learning_passport_stickers_pdf/{$row['seq_no']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/passport_print?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/passport_print/");
        $this->layout->view('management/passport_print/list',$this->data);
    }

    public function print_learning_passport_stickers_pdf($seq_no)
    {
        $require_data = $this->export_score_model->get($seq_no);

        if(empty($require_data)){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/passport_print/'));
        }

        $require_data['count'] = $this->point_print_model->get_count($require_data['year'], $require_data['class_no'], $require_data['term']);

        $name_list = $this->point_print_model->get_app_name($require_data['year'], $require_data['class_no'], $require_data['term'],'Y');
        $arr_stack = array();
        $ddc = 0;
        foreach($name_list as $arr){
        $no = str_pad($arr['st_no'],2,'0',STR_PAD_LEFT);
        $tb =
        $no.' <table width="100%" border="1">
          <tr>
            <td align="center">臺北市政府<br>公務人員訓練處</td>
            <td align="center">'.$require_data['class_name'].'</td>
            <td align="center">'.$require_data['start_date1'].'<br>'.$require_data['end_date1'].'</td>
            <td align="center">'.$require_data['range'].'小時<br>'.$arr['name'].'</td>
            <td align="center"><font face="標楷體" color="blue">臺北市政府</font><br><font face="標楷體" color="blue">公務人員訓練處</font><br><font face="標楷體" color="blue">終身學習認證章</font></td>
          </tr>
        </table>
        ';
            $arr_stack[$ddc]=$tb ;
            $ddc = $ddc+1;
        }

        $outputHTML = "";
        $count = intval($require_data ['count']);
        $ddd = 0;
        for ($i = 1; $i <= $count; $i+=2) {
            if ($i>1 && (($i%33)==1 ||($i%33)==2))
                $outputHTML .= '<br><br><br><br>';
            $outputHTML .= '<table width="100%">';
            $outputHTML .= '<tr>';
            // $outputHTML .= '<td width="3%" align="center">' . $i . '</td>';
            $outputHTML .= '<td width="44%" align="left">' . $arr_stack[$ddd] . '</td>';
            $outputHTML .= '<td width="7%">&nbsp;</td>';
            if ($count === $i) {
                $outputHTML .= '<td width="44%" align="left"></td>';
            } else {
                $outputHTML .= '<td width="44%" align="left">' . $arr_stack[++$ddd] . '</td>';
            }
            $outputHTML .= '</tr>';
            $outputHTML .= '</table>';
            $ddd++;
        }

        $this->load->library('pdf/PHP_TCPDF');

        $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->AddPage();
        //$pdf->SetMargins(15,15,15,15);
        $pdf->SetFont('msungstdlight', '', 8);
        $pdf->SetAutoPageBreak(true, 13);

        //$pdf->SetAutoPageBreak(false);
        //ECHO $outputHTML;
        $pdf->writeHTML($outputHTML, true, 0, true, 0, 'C');
        $pdf->lastPage();
        $pdf->Output();

    }

}
