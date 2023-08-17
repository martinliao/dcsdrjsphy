<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Card_rotation extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('other_work/card_rotation_model');
    }

    public function index()
    {
        $this->data['list'] = $this->card_rotation_model->getList();

        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("other_work/card_rotation/edit/{$row['id']}");
            $row['link_del'] = base_url("other_work/card_rotation/del/{$row['id']}");
        }

        $this->data['link_refresh'] = base_url("other_work/card_rotation/");
        $this->layout->view('other_work/card_rotation/list',$this->data);
    }

    public function add()
    {
        $post = $this->input->post();


        if(!empty($post)){
            $uploaddir = DIR_UPLOAD_CARD_ROTATION;
            if($_FILES["userfile"]["name"]<>""){
                $total_size = $_FILES["userfile"]["size"];

                //20211215 Roger 限制上傳檔案類型 start
                $file_name  = $_FILES['userfile']['name'] ;
                $file_size  = $_FILES['userfile']['size'];
                $file_tmp   = $_FILES['userfile']['tmp_name'];
                $exploded   = explode('.',$_FILES['userfile']['name']);
                $file_ext   = strtolower(end($exploded));
                $expensions = array("png","jpg");
        
                if(in_array($file_ext,$expensions)=== false){
                   $errors[]="extension not allowed, please choose a  csv file.";
                   $this->setAlert(2, '上傳檔案格式錯誤，僅能上傳jpg、png等圖片格式');
                            redirect(base_url("other_work/card_rotation/"));
                }
                //20211215 Roger 限制上傳檔案類型 end


                if($total_size < 1000000){
                    $filename = basename($_FILES['userfile']['name']);
                    $uploadfile = $uploaddir.$filename;
                    move_uploaded_file($_FILES["userfile"]["tmp_name"], iconv("utf-8", "big5", $uploadfile));
                    $url = base_url("files/upload_card_rotation/".basename($_FILES['userfile']['name']));
                    $chk_insert = $this->card_rotation_model->insertCardRotation($post['name'],$url,$this->flags->user['name']);

                    if($chk_insert){
                        $this->setAlert(1, '新增成功');
                    } else {
                        $this->setAlert(2, '新增失敗');
                    }

                     redirect(base_url("other_work/card_rotation/"));
                } else {
                    $this->setAlert(2, '檔案大小不能超過1MB');
                    redirect(base_url("other_work/card_rotation/"));
                }
            } else {
                $this->setAlert(2, '檔案不能為空');
                redirect(base_url("other_work/card_rotation/"));
            }
        }

        $this->layout->view('other_work/card_rotation/add',$this->data);
    }

    public function edit($id)
    {
        $post = $this->input->post();

        if(!empty($post)){
            $uploaddir = DIR_UPLOAD_CARD_ROTATION;
            if($_FILES["userfile"]["name"]<>""){
                $total_size = $_FILES["userfile"]["size"];
                

                //20211215 Roger 限制上傳檔案類型 start
                $file_name  = $_FILES['userfile']['name'] ;
                $file_size  = $_FILES['userfile']['size'];
                $file_tmp   = $_FILES['userfile']['tmp_name'];
                $exploded   = explode('.',$_FILES['userfile']['name']);
                $file_ext   = strtolower(end($exploded));
                $expensions = array("png","jpg");

                if(in_array($file_ext,$expensions)=== false){
                $errors[]="extension not allowed, please choose a  csv file.";
                $this->setAlert(2, '上傳檔案格式錯誤，僅能上傳jpg、png等圖片格式');
                            redirect(base_url("other_work/card_rotation/"));
                }
                //20211215 Roger 限制上傳檔案類型 end

                if($total_size < 1000000){
                    $filename = basename($_FILES['userfile']['name']);
                    $uploadfile = $uploaddir.$filename;
                    move_uploaded_file($_FILES["userfile"]["tmp_name"], iconv("utf-8", "big5", $uploadfile));
                    $url = base_url("files/upload_card_rotation/".basename($_FILES['userfile']['name']));
                    $chk_upd = $this->card_rotation_model->updateCardRotation($post['name'],$url,$id);

                    if($chk_upd){
                        $this->setAlert(1, '修改成功');
                    } else {
                        $this->setAlert(2, '修改失敗');
                    }

                     redirect(base_url("other_work/card_rotation/"));
                } else {
                    $this->setAlert(2, '檔案大小不能超過1MB');
                    redirect(base_url("other_work/card_rotation/"));
                }
            } else {
                $this->setAlert(2, '檔案不能為空');
                redirect(base_url("other_work/card_rotation/"));
            }
        }

        $this->data['list'] = $this->card_rotation_model->getInfo($id);

        $this->layout->view('other_work/card_rotation/edit',$this->data);
    }

    public function del($id){
        $this->card_rotation_model->deleteCardRotation($id);

        redirect(base_url("other_work/card_rotation/"));
    }
}
