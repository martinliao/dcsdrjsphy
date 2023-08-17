<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Course_introduct extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('planning/course_introduct_model');
        //$this->load->model('planning/set_startdate_model');

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
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
        if (!isset($this->data['filter']['query_season'])) {
            $this->data['filter']['query_season'] = '';
        }
        if (!isset($this->data['filter']['query_month_start'])) {
            $this->data['filter']['query_month_start'] = '';
        }
        if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
        if (!isset($this->data['filter']['query_reason'])) {
            $this->data['filter']['query_reason'] = '';
        }
        if (!isset($this->data['filter']['query_class_status'])) {
            $this->data['filter']['query_class_status'] = '';
        }
        if (!isset($this->data['filter']['query_start_date'])) {
            $this->data['filter']['query_start_date'] = '';
        }
        if (!isset($this->data['filter']['query_end_date'])) {
            $this->data['filter']['query_end_date'] = '';
        }
        if (!isset($this->data['filter']['respondant'])) {
            $this->data['filter']['respondant'] = '';
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

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }


        if ($this->data['filter']['query_start_date'] !== '' ) {
            $conditions['start_date1 >='] = $this->data['filter']['query_start_date'];
        }

        if ($this->data['filter']['query_end_date'] !== '' ) {
            $conditions['start_date1 <='] = $this->data['filter']['query_end_date'];
        }

        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['require.type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($this->data['filter']['query_type']);
        
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }

       
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );
        
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['respondant'] = $this->data['filter']['query_class_name'];
        }


        $this->data['filter']['total'] = $total = $this->course_introduct_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
        );
    
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['respondant'] !== '' ) {
            $attrs['respondant'] = $this->data['filter']['respondant'];
        }


		$this->data['list'] = $this->course_introduct_model->getList($attrs);
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/course_introduct?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;

        $this->pagination->initialize($config);
        $this->data['link_get_second_category'] = base_url("planning/course_introduct/getSecondCategory");
		$this->data['link_confirm'] = '';
        $this->data['link_refresh'] = base_url("planning/course_introduct/");
        $this->data['link_detail'] = base_url("planning/course_introduct/");
        $this->data['link_export'] = base_url("planning/course_introduct/export");
       
		$this->layout->view('planning/course_introduct/list', $this->data);
    }
    
    public function export()
    {
		$conditions = array();

        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        if ($this->data['filter']['query_season'] !== '' ) {
            $conditions['reason'] = $this->data['filter']['query_season'];
        }

        if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['require.type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->course_introduct_model->getSecondCategory($this->data['filter']['query_type']);

        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }
        
        if ($this->data['filter']['query_start_date'] !== '' ) {
            $conditions['start_date1 >='] = $this->data['filter']['query_start_date'];
        }

        if ($this->data['filter']['query_end_date'] !== '' ) {
            $conditions['start_date1 <='] = $this->data['filter']['query_end_date'];
        }
           

        $attrs = array(
            'conditions' => $conditions,
        );
        
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['query_class_name'];
        }
        if ($this->data['filter']['respondant'] !== '' ) {
            $attrs['respondant'] = $this->data['filter']['respondant'];
        }


		$info = $this->course_introduct_model->getList($attrs);

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
        $sheet->setCellValue('D1','研習對象');
        $sheet->setCellValue('E1','期別');
        $sheet->setCellValue('F1','每期人數');
        $sheet->setCellValue('G1','合計人數');
        $sheet->setCellValue('H1','期程(小時)');
        $sheet->setCellValue('I1','合計時數');
        $sheet->setCellValue('J1','研習目標');
        $sheet->setCellValue('K1','課程內容');
        $sheet->setCellValue('L1','開課日期');

        $rows = 2;
        $sum_people=0;
        $sum_time=0;
        for($i=0;$i<count($info);$i++){
        	$sheet->setCellValue('A'.$rows,$info[$i]['type_name']);
	        $sheet->setCellValue('B'.$rows,$info[$i]['bureau_name']);
	        $sheet->setCellValue('C'.$rows,$info[$i]['class_name']);
	        $sheet->setCellValue('D'.$rows,$info[$i]['respondant']);
	        $sheet->setCellValue('E'.$rows,$info[$i]['max_term']);
	        $sheet->setCellValue('F'.$rows,$info[$i]['no_persons']);
	        $sheet->setCellValue('G'.$rows,$info[$i]['total_persons']);
	        $sheet->setCellValue('H'.$rows,$info[$i]['range']);
	        $sheet->setCellValue('I'.$rows,$info[$i]['total_range']);
            $sheet->setCellValue('J'.$rows,$info[$i]['obj']);
            $sheet->setCellValue('K'.$rows,$info[$i]['content']);
	        $sheet->setCellValue('L'.$rows,substr($info[$i]['start_date1'],0,10));
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
        
        redirect(base_url('planning/course_introduct'),'refresh');
	}
}
