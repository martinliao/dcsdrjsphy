<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_record extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('management/class_record_model');
    }

    public function index()
    {
        $this->data['show_class'] = base_url("management/class_record/show_class");
        $this->data['importCSV'] = base_url("management/class_record/import");
        $this->data['exportCSV'] = base_url("management/class_record/export");
        $this->layout->view('management/class_record/list',$this->data);
    }
    public function show_class()
    {
        if (!isset($this->data['filter']['show_page'])) {
            $this->data['filter']['show_page'] = '1';
        }
        if (!isset($this->data['filter']['key'])) {
            $this->data['filter']['key'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = '';
        }
        $page = $this->data['filter']['show_page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        if ($this->data['filter']['query_year'] != '') {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        $attrs = array();
        $attrs['conditions'] = $conditions;

        if ($this->data['filter']['key'] != '') {
            $attrs['class_name'] = $this->data['filter']['key'];
        }

        // jd($attrs);
        $total_query_records = $this->class_record_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $this->data['total_page'] = ceil($total_query_records / $rows);

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['show_list'] = $this->class_record_model->getList($attrs);
        $this->load->view('management/class_record/co_class_name', $this->data);
    }

    public function import()
    {
        $ynseldiv = array(
            '1' => '結訓',
            '2' => '報名',
            '3' => '選員',
            '4' => '退訓',
            '5' => '未報到',
            '6' => '取消報名',
            '7' => '取消參訓',
        );
        $massage = '';
        $script = '';
        if($post = $this->input->post()){
            $val_ary = explode("||",$post['val']);

            $str_sql = '';
            foreach($val_ary as $val_row){
                $tmp = explode(",",$val_row);
                if($str_sql==''){
                    $str_sql = "((R.year ='{$tmp[0]}' and R.class_no='{$tmp[1]}' and R.term='{$tmp[2]}')";
                }else{
                    $str_sql = $str_sql." or "."(R.year ='{$tmp[0]}' and R.class_no='{$tmp[1]}' and R.term='{$tmp[2]}')";
                }
            }
            $str_sql .= ')';

            if (isset($_FILES['courseSetupfile']) && $_FILES['courseSetupfile']['tmp_name'] != '') {
                if (!fileExtensionCheck($_FILES['courseSetupfile']['name'], ['csv'])){
                    $this->setAlert(3, "不允許的檔案格式");
                    redirect(base_url("management/class_record/import?val=".$post['val']));
                }                
                $file = fopen(sys_get_temp_dir().DIRECTORY_SEPARATOR.basename($_FILES['courseSetupfile']['tmp_name']),"r");
                $i = 1;
                $import_susess = '0';
                $import_falut = '0';
                while(! feof($file))
                {
                    $data = fgetcsv($file);
                    if($i == 1){
                        $i++;
                        continue;
                    }

                    if($data){
                        foreach($data as & $row){
                            $row = iconv('big5', 'UTF-8//IGNORE', $row);
                            $row = strtoupper(trim($row));
                        }
                        $conditions = array(
                            'idno' => $data[0],
                        );
                        $person = $this->user_model->_get($conditions);
                        if($person){
                            $conditions['bureau_id'] = $person['bureau_id'];
                            $conditions['where_special'] = $str_sql;
                            $import_susess++;
                            $list = $this->class_record_model->get_regist_list($conditions);
                            // jd($list);
                            foreach($list as $p_row){
                                $class_name = sprintf('%s年 %s 第%s期', $p_row['year'], $p_row['class_name'], $p_row['term']);
                                $class_date = sprintf('%s～%s', $p_row['start_date1'], $p_row['end_date1']);
                                if(!empty($yn_sel)){
                                    $yn_sel = $ynseldiv[$p_row["yn_sel"]];
                                }else{
                                    $yn_sel = '-';
                                }
                                $script = $script . " add_item('{$data[0]}','{$person["name"]}','{$p_row["beaurau_name"]}','{$person["job_title_name"]}','{$class_name}','{$class_date}','{$yn_sel}');\n ";
                            }
                        }else{
                            $massage .= $data[0]." 無此帳號"."<br>";
                            $import_falut++;
                        }

                    }
                }

                fclose($file);
                $massage .= "匯入成功".$import_susess."筆<br>";
                $massage .= "匯入失敗".$import_falut."筆<br>";
            }
        }
        $this->data['form']['massage'] =  $massage;
        $this->data['form']['script'] =  $script;
        $this->load->view('management/class_record/class_record_import', $this->data);
    }

    public function export()
    {
        if($post = $this->input->post()){
            $this->data['outputcsv'] =  $post['outputcsv'];
        }
        $this->load->view('management/class_record/class_record_export', $this->data);
    }

}
