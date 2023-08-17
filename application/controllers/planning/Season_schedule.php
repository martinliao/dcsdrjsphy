<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Season_schedule extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/season_schedule_model');
        $this->load->model('planning/course_introduct_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_season'])) {
            $this->data['filter']['query_season'] = '';
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = '';
        }
        if (!isset($this->data['filter']['query_month_end'])) {
            $this->data['filter']['query_month_end'] = '';
        }
        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
        if (!isset($this->data['filter']['query_reason'])) {
            $this->data['filter']['query_reason'] = '';
        }
    
    }

    public function index()
	{
		

		$this->data['page_name'] = 'list';
        $this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';

        $conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }


        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['require.type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($this->data['filter']['query_type']);
            //die();
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }
        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
           
        }

        if ($this->data['filter']['query_month_end'] !== '' ) {
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_end'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));

            $conditions['start_date1 <='] = $last_day;
        }

       
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );
        
        $this->data['filter']['total'] = $total = $this->season_schedule_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            //'offset' => $offset,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }
    
		$this->data['list'] = $this->season_schedule_model->getList($attrs);
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/season_schedule?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;

        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/season_schedule/getSecondCategory");
		$this->data['link_confirm'] = '';
        $this->data['link_refresh'] = base_url("planning/season_schedule/");
        $this->data['link_detail'] = base_url("planning/season_schedule/");
        $this->data['link_export'] = base_url("planning/season_schedule/export");
       
		$this->layout->view('planning/season_schedule/list', $this->data);
    }
    
    public function export()
    {
		$conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }


        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }

        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }
        
        if ($this->data['filter']['query_month_start'] !== '' ) {
            $conditions['start_date1 >='] = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_start'].'-01';
           
        }

        if ($this->data['filter']['query_month_end'] !== '' ) {
            $first_day = ($this->data['filter']['query_year']+1911).'-'.$this->data['filter']['query_month_end'].'-01';
            $last_day = date('Y-m-d', strtotime("$first_day +1 month -1 day"));

            $conditions['start_date1 <='] = $last_day;
        }

           

        $attrs = array(
            'conditions' => $conditions,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

		$info = $this->season_schedule_model->getList($attrs);

		// 新增Excel物件
        $this->load->library('excel');
        $objPHPExcel = new PHPExcel();

        // 設定屬性
        $objPHPExcel->getProperties()->setCreator("PHP")
                    ->setLastModifiedBy("PHP")
                    ->setTitle("Orders")
                    ->setSubject("Subject")
                    ->setDescription("Description")
                    ->setKeywords("Keywords")
                    ->setCategory("Category");

        // 設定操作中的工作表
        $objPHPExcel->setActiveSheetIndex(0);
        $sheet = $objPHPExcel->getActiveSheet();

        // 將工作表命名
        $sheet->setTitle('List');

        $sheet->setCellValue('A1','系列別');
        $sheet->setCellValue('B1','次類別');
        $sheet->setCellValue('C1','班期名稱');
        $sheet->setCellValue('D1','班期性質');
        $sheet->setCellValue('E1','開班起訖日');
        $sheet->setCellValue('F1','期別');
        $sheet->setCellValue('G1','時數');
        $sheet->setCellValue('H1','承辦人');
        $sheet->setCellValue('I1','電話');
        

        $rows = 2;
        $sum_people=0;
        $sum_time=0;
        for($i=0;$i<count($info);$i++){
        	$sheet->setCellValue('A'.$rows,$info[$i]['type_name']);
	        $sheet->setCellValue('B'.$rows,$info[$i]['bureau_name']);
            $sheet->setCellValue('C'.$rows,$info[$i]['class_name']);
            $course='';
            if($info[$i]['is_assess']==1 && $info[$i]['is_mixed']==1){
                $course='混成';
            }else{
                $course='考核';
            }
            $time=substr($info[$i]['start_date1'],0,10).'~'.substr($info[$i]['end_date1'],0,10);
	        $sheet->setCellValue('D'.$rows,$course);
            $sheet->setCellValue('E'.$rows,$time);
	        $sheet->setCellValue('F'.$rows,$info[$i]['term']);
	        $sheet->setCellValue('G'.$rows,$info[$i]['range']);
	        $sheet->setCellValue('H'.$rows,$info[$i]['bu_name']);
	        $sheet->setCellValue('I'.$rows,$info[$i]['tel']);
            $rows++;
            
        }
            

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        header('Content-Type:application/csv;charset=UTF-8');
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header('Content-Disposition: attachment;filename="'.generatorRandom(10).'.xls"');
        header("Content-Transfer-Encoding:binary");
        $objWriter->save('php://output');

        exit;
	}

}
