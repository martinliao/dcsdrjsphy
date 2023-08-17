<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_info extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("create_class/class_info_model");
        $this->data['filter']['sort'] = (!isset($this->data['filter']['sort'])) ? '' : $this->data['filter']['sort'];
    }

    public function index()
    {
        $this->load->library('pagination');
        $param = $this->input->get();
        $param['rows'] = (empty($param['rows'])) ? 10 : $param['rows']; 
        $param['filter']['sort'] = (!isset($this->data['filter']['sort'])) ? '' : $this->data['filter']['sort'];
        $view_data = $this->data;
        $view_data['list'] = $this->class_info_model->getList($param, $param['rows']);
        $config['base_url'] = base_url("create_class/class_info?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $view_data['list']['count'];
        $config['per_page'] = $param['rows'];
        $this->pagination->initialize($config); 
        $this->layout->view('create_class/classinfo/list', $view_data);
    }

    public function store()
    {
        $now = new DateTime('now');
        $config = [
            "upload_path" => './files/upload_files/class_info/',
            "file_name" => $now->format("YmdHis"),
            "allowed_types" => 'pdf',
            "max_size" => '5120' // 5MB
        ];
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('class_info_file')){
            $upload_data = $this->upload->data();
            $class_info = [
                "id" => $now->format("YmdHis"), 
                "cre_user" => $this->flags->user["username"],
                "cre_date" => $now->format("Y-m-d"),
                "fname" => $upload_data['file_name']
            ];

            $fields = ["title", "start_date", "end_date"];
            foreach ($fields as $field) {
                $class_info[$field] = $this->input->post($field);
            }
            $this->class_info_model->insert($class_info);
            redirect("/create_class/class_info");
        }else{
            $error = array('error' => $this->upload->display_errors());
            echo "<pre>";
            print_r($error["error"]);
            die;
        }
        
    }

    public function show($id){
        $class_info = $this->class_info_model->get(["id" => $id]);
        if (!empty($class_info)){
            header("Content-type: application/pdf");
            readfile("./files/upload_files/class_info/".$class_info["fname"]); 
        }else{
            die("找不到該班期資訊");
        }
    }

    public function edit($id)
    {
        if ($post = $this->input->post()){
            $now = new DateTime('now');
            $post['upd_user'] = $this->flags->user["username"];
            $post['upd_date'] = $now->format("Y-m-d");
            $this->class_info_model->update($id, $post);
            redirect("/create_class/class_info");
        }
        $view_data = $this->data;
        $view_data['classinfo'] = $this->class_info_model->get($id);
        $this->layout->view('create_class/classinfo/edit', $view_data);
    }

    public function delete($id)
    {
        $class_info = $this->class_info_model->get($id);
        if (!empty($class_info)){
            $this->class_info_model->delete($id);
            $file = "./files/upload_files/class_info/".$class_info["fname"];
            if (file_exists($file) && !empty($class_info["fname"])){
                unlink($file);
            }            
        }
        redirect("/create_class/class_info");
    }

}
