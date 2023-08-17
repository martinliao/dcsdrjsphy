<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Point_maintain extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('management/point_maintain_model');
        $this->load->model('management/require_grade_model');
        $this->load->model('data/grade_category_model');

        $this->data['choices']['queryType'] = array(
            '' => '全部',
            'Y' => '已有設定類別',
            'N' => '尚未設定類別',
        );

        $date_now = new DateTime('now');
        $year_now = $date_now->format('Y');
        $this_yesr = $year_now - 1911;

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }

        if (!isset($this->data['filter']['year'])) {
            $this->data['filter']['year'] = $this_yesr;
        }
        if (!isset($this->data['filter']['queryType'])) {
            $this->data['filter']['queryType'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
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
        //教務組承辦人
        if (!in_array("1", $this->flags->user['group_id'])){
            $conditions['worker'] = $this->flags->user['idno'];
        }
        
        //var_dump($this->flags->user);
        $conditions['year'] = $this->data['filter']['year'];

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] != '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $attrs['where_special'] = " class_status in ('2','3') and is_cancel = '0' ";

        if($this->data['filter']['queryType'] == 'Y'){
            $attrs['where_special'] .= " and (year, class_no, term) in (select distinct year, class_no, term from require_grade) ";
        }
        if($this->data['filter']['queryType'] == 'N'){
            $attrs['where_special'] .= " and (year, class_no, term) not in (select distinct year, class_no, term from require_grade) ";
        }

        $this->data['filter']['total'] = $total = $this->point_maintain_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->point_maintain_model->getList($attrs);

        foreach($this->data['list'] as & $row){
            $row['detail'] = base_url("management/point_maintain/detail/{$row['seq_no']}");
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/point_maintain?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("management/point_maintain/");
        $this->layout->view('management/point_maintain/list',$this->data);
    }

    public function detail($seq_no)
    {

        $this->data['detail_data'] = $this->point_maintain_model->get($seq_no);

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $this->data['choices']['grade_type'] = $this->grade_category_model->getChoices();

        $conditions = array();
        $conditions['year'] = $this->data['detail_data']['year'];
        $conditions['class_no'] = $this->data['detail_data']['class_no'];
        $conditions['term'] = $this->data['detail_data']['term'];

        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['filter']['total'] = $total = $this->require_grade_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->require_grade_model->getList($attrs);

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/point_maintain/detail?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->layout->view('management/point_maintain/detail',$this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';

        $post = $this->input->post();
        if(isset($post['year'])){
          $this->data['detail_data']['year'] = $post['year'];
        }
        if(isset($post['class_no'])){
          $this->data['detail_data']['class_no'] = $post['class_no'];
        }
        if(isset($post['class_name'])){
          $this->data['detail_data']['class_name'] = $post['class_name'];
        }
        if(isset($post['term'])){
          $this->data['detail_data']['term'] = $post['term'];
        }
        $conditions = array(
            'year' => $this->data['detail_data']['year'],
            'class_no' => $this->data['detail_data']['class_no'],
            'term' => $this->data['detail_data']['term'],
        );
        $this->data['choices']['grade_type'] = $this->grade_category_model->getChoicesAdd($conditions);
        $this->data['choices']['grade_type'] = array('' => '請選擇') + $this->data['choices']['grade_type'];
        $this->data['class'] = $this->point_maintain_model->get($conditions);

        $this->data['set_to_terms'] = '';
        $conditions = array(
            'year' => $post['year'],
            'class_no' => $post['class_no'],
        );

        $this->data['form'] = array(
            'grade_type' => '',
            'proportion' => '0',
        );

        if(isset($post['grade_type'])){

            $insert_date = new DateTime('now');
            $insert_date = $insert_date->format('Y-m-d H:i:s');
            $fields = array(
                'year' => $post['year'],
                'class_no' => $post['class_no'],
                'term' => $post['term'],
                'class_name' => $post['class_name'],
                'grade_type' => $post['grade_type'],
                'proportion' => $post['proportion'],
            );

            $this->require_grade_model->insert($fields);
            $this->setAlert(1, '新增成功!');
            redirect(base_url("management/point_maintain/detail/{$this->data['class']['seq_no']}"));
        }


        $this->data['link_save_file'] = base_url("management/point_maintain/add?".$this->getQueryString());
        $this->data['back_to_detail'] = base_url("management/point_maintain/detail");
        $this->layout->view('management/point_maintain/add',$this->data);
    }

    public function edit($id)
    {
        $this->data['page_name'] = 'edit';

        $post = $this->input->post();
        if(isset($post['year'])){
          $this->data['detail_data']['year'] = $post['year'];
        }
        if(isset($post['class_no'])){
          $this->data['detail_data']['class_no'] = $post['class_no'];
        }
        if(isset($post['class_name'])){
          $this->data['detail_data']['class_name'] = $post['class_name'];
        }
        if(isset($post['term'])){
          $this->data['detail_data']['term'] = $post['term'];
        }
        $require_grade = $this->require_grade_model->get($id);
        $conditions = array(
            'year' => $this->data['detail_data']['year'],
            'class_no' => $this->data['detail_data']['class_no'],
            'term' => $this->data['detail_data']['term'],
        );
        $this->data['class'] = $this->point_maintain_model->get($conditions);
        $conditions['grade_type'] = $require_grade['grade_type'];
        $this->data['choices']['grade_type'] = $this->grade_category_model->getChoicesEdit($conditions);

        $this->data['set_to_terms'] = '';
        $conditions = array(
            'year' => $post['year'],
            'class_no' => $post['class_no'],
        );

        $this->data['form'] = array(
            'grade_type' => $require_grade['grade_type'],
            'proportion' => $require_grade['proportion'],
        );

        if(isset($post['grade_type'])){

            $fields = array(
                'grade_type' => $post['grade_type'],
                'proportion' => $post['proportion'],
            );
            $this->require_grade_model->update($id, $fields);

            $this->setAlert(1, '修改成功!');
            redirect(base_url("management/point_maintain/detail/{$this->data['class']['seq_no']}"));
        }


        $this->data['link_save_file'] = base_url("management/point_maintain/edit/{$id}?".$this->getQueryString());
        $this->data['back_to_detail'] = base_url("management/point_maintain/detail");
        $this->layout->view('management/point_maintain/add',$this->data);
    }

    public function delete()
    {
        if($post = $this->input->post()){

            $conditions = array(
                'id' => $post['id'],
            );
            $old_data = $this->require_grade_model->get($conditions);
            $conditions = array(
                'year' => $old_data['year'],
                'class_no' => $old_data['class_no'],
                'term' => $old_data['term'],
            );
            $class = $this->point_maintain_model->get($conditions);

            if(empty($old_data)){
                $this->setAlert(3, '操作錯誤');
                redirect(base_url("management/point_maintain/detail/{$class['seq_no']}"));
            } else {

                $this->require_grade_model->delete($post['id']);

                $this->setAlert(1, '刪除成功!');
                redirect(base_url("management/point_maintain/detail/{$class['seq_no']}"));
            }
        }

    }

}
