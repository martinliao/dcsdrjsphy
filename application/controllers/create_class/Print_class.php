<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Print_class extends MY_Controller
{   
    public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
        }
        $this->load->model('create_class/print_class_model');
        
        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y')-1911;
        }
        if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
        }
       
	}

	public function index()
	{
        $conditions = array();
        if ($this->data['filter']['query_year'] !== '' ) {
            $conditions['year'] = $this->data['filter']['query_year'];
        }
       
        if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
        }

        
        
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

		$attrs = array(
            'conditions' => $conditions,
        );

        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['filter']['total'] = $total = $this->print_class_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
       
        if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }

        $this->data['list'] = $this->print_class_model->getList($attrs);
        
        foreach($this->data['list'] as & $row){
            $row['link_export']=base_url("create_class/print_class/export/{$row['seq_no']}?{$_SERVER['QUERY_STRING']}");
        }
		
		$this->load->library('pagination');
        $config['base_url'] = base_url("create_class/print_class?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
       
        $this->data['link_refresh'] = base_url("create_class/print_class/");
		$this->layout->view('create_class/print_class/list', $this->data);
    }
    public function export($seq_no=null)
    {
        $this->load->library('pdf/PDF_Chinesess');
        $this->load->library('pdf/font/makefont/Makefont123');
        if($seq_no !=null){
            $this->db->select('year,class_no,term,sc.name as bureau_name,class_name');
            $this->db->join('sub_category as sc','sc.type=require.type and sc.cate_id=require.beaurau_id','left');
            $this->db->where('seq_no',$seq_no);
            $query=$this->db->get('require');
            $result=$query->result_array();
        }
        $this->db->select('ru.hrs,ru.year,ru.class_id,ru.term,ru.use_id,vct.description as name,ru.use_date,ru.use_period');
        $this->db->distinct('ru.hrs');
        $this->db->join('view_code_table as vct','vct.item_id=ru.use_id and vct.type_id="17"');
        $this->db->from('room_use as ru');
        $query=$this->db->get_compiled_select();

        $this->db->select('sum(temp.hrs) as hrs,temp.year,temp.class_id,temp.term,temp.name');
        $this->db->from('('.$query.') as temp');
        $this->db->where('temp.year',$result[0]['year']);
        $this->db->where('temp.class_id',$result[0]['class_no']);
        $this->db->where('temp.term',$result[0]['term']);
        $this->db->group_by(array('temp.year','temp.class_id','temp.term','temp.name','temp.use_id'));
        $final=$this->db->get()->result_array();

        $pdf=new PDF_Chinesess();
        $pdf->AddGBFont('simhei', '黑体');
        $pdf->AddPage();
        $pdf->SetFont('simhei', '', 13);

    }
}