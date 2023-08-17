<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Code_history extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('data/code_history_model');

        $this->data['type_id'] = array(
        	'' => '請選擇',
        	'02' => '職稱代碼',
        	'03' => '現職代碼',
        	'04' => '學歷代碼',
        	'05' => '選員代碼',
        	'07' => '鐘點費類別',
            '08' => '講師聘請類別代碼',
        	'09' => '班期屬性',
        	'10' => '研習方式(一)',
        	'11' => '報名方式',
            '14' => '銀行代碼',
        	'20' => '場地類別',
        	'21' => '場地單位',
        	'23' => '系列別',
            '26' => '代理人',
        	'31' => '場地預約時段',
        	'32' => '節次設定時間',
            '37' => '成績類別',
            '41' => '上下架日期',
        	'46' => '研習方式(二)',
        	'47' => '研習方式(三)',
        );

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }

        if (!isset($this->data['filter']['type_id'])) {
            $this->data['filter']['type_id'] = '';
        }

        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $conditions['type_id'] = $this->data['filter']['type_id'];

		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->code_history_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

		$this->data['list'] = $this->code_history_model->getList($attrs);

		$this->load->library('pagination');
        $config['base_url'] = base_url("data/code_history?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->layout->view('data/code_history/list', $this->data);
    }

}
