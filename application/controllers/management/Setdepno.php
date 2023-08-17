<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setdepno extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/setdepno_model');
        $this->load->model('management/bureau_model');
        $this->load->model('management/beaurau_persons_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
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
        $conditions['year'] = $this->data['filter']['year'];

        if(!in_array("1", $this->flags->user['group_id'])){
            $conditions['worker'] = $this->flags->user['idno'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );

        $attrs['class_status'] = array('2','3');

        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $this->data['filter']['total'] = $total = $this->setdepno_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        $attrs['class_status'] = array('2','3');
        if ($this->data['filter']['class_name'] != '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }
        if ($this->data['filter']['sort'] != '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }

        $this->data['list'] = $this->setdepno_model->getList($attrs);
        // jd($this->data['list'],1);
        foreach ($this->data['list'] as & $row) {
            $row['link_add'] = base_url("management/setdepno/detail/{$row['seq_no']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("management/setdepno?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['setdepno'] = 'Y';
        $this->data['link_refresh'] = base_url("management/setdepno/");
        $this->layout->view('management/setdepno/list',$this->data);
    }

    public function detail($seq_no=NULL)
    {
        $class_data = $this->setdepno_model->get($seq_no);
        $this->data['class_data'] = $class_data;
        if($post = $this->input->post()){

            $delete_conditions = array(
                'year' => $class_data['year'],
                'class_no' => $class_data['class_no'],
                'term' => $class_data['term'],
            );
            $this->beaurau_persons_model->delete($delete_conditions);


            foreach($post['hddid'] as $key => $row ){
                if($post['person'][$key] != '' || $post['person2'][$key] != ''){
                    $insert_date = new DateTime();
                    $insert_date = $insert_date->format('Y-m-d H:i:s');
                    $fields = array(
                        'year' => $class_data['year'],
                        'class_no' => $class_data['class_no'],
                        'term' => $class_data['term'],
                        'beaurau' => $row,
                        'persons' => $post['person'][$key],
                        'persons_2' => $post['person2'][$key],
                        'cre_user' => $this->flags->user['username'],
                        'cre_date' => $insert_date,
                    );

                    $this->beaurau_persons_model->insert($fields);
                }
            }

            $this->setAlert(1, '修改成功');
            redirect(base_url("management/setdepno/detail/{$seq_no}"));

        }
        $conditions = array(
            'bureau_level' => '3',
        );
        $attrs = array(
            'year' => $class_data['year'],
            'class_no' => $class_data['class_no'],
            'term' => $class_data['term'],
            'where_special' => "(del_flag is null or del_flag !='C') and bureau_id like '379%'",
        );
        $this->data['bureau_list'] = $this->bureau_model->getList($attrs);
        // jd($this->data['bureau_list']);
        $this->data['setdepno'] = 'Y';
        $this->data['link_cancel'] = base_url("management/setdepno/?{$_SERVER['QUERY_STRING']}");
        $this->layout->view('management/setdepno/detail',$this->data);
    }

}
