<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_type extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('management/certificate_list_model');
        $this->load->model('management/certificate_image_model');
        $this->load->model('management/require_grade_model');
        $this->load->model('management/certificate_type_model');


        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['queryType'])) {
            $this->data['filter']['queryType'] = '';
        }
        if (!isset($this->data['filter']['type_title_name'])) {
            $this->data['filter']['type_title_name'] = '';
        }
    }

    public function index()
    {
        if ($action = $this->input->post('action')){
            $this->db->set('value',  $this->input->post('qRcodeTimeisOneYear'));
            $this->db->set('updated_at', date('Y-m-d H:i:s'));
            $this->db->where('setting_name', 'qRcodeTimeisOneYear')->update('certificate_setting');
            $this->setAlert(1, '設定成功');
            redirect(base_url('/management/certificate_type'));
        }        

        $this->data['qRcodeTimeisOneYear'] = $this->db->where('setting_name', 'qRcodeTimeisOneYear')->get('certificate_setting')->row()->value;
        /*
        $this->data['save_url'] = base_url("management/certificate_type/{$seq_no}?".$this->getQueryString());
        $this->data['view_cer_url'] = base_url("management/certificate_list/cer_pdf");
        $this->data['base_cer_url'] = base_url("management/certificate_list/");
        $this->data['bg_select_option'] = $this->get_image_option('1');
        $this->data['signature_select_option'] = $this->get_image_option('2');
        $this->data['seal_select_option'] = $this->get_image_option('3');
        
        if($post = $this->input->post()){
            if($post['action'] == 'view_cer'){  //預覽證書
                $this->insert_file_temp($post);

            }else{  //新增
                $image_id = $this->insert_file_return_id($post);

                $insert_fields = array(
                    'title' => $post['title'],
                    'demo_text' => $post['demo_text'],
                    'bg_file_id' => $image_id['bg_file'],
                    'seal_file_id' => $image_id['seal_file'],
                    'signature_file_id' => $image_id['signature_file'],
                    );
                    //var_dump($insert_fields);die();
                //寫入DBcertificate_type
                $this->certificate_type_model->insert($insert_fields);
                $this->setAlert(2, '書證版型新增成功'); //無用
                 

            }
        }
        $this->layout->view('management/certificate_type/type_add',$this->data);
        */
        $this->data['page_name'] = 'list';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        //if ($this->data['filter']['all'] == '' ) {
            //$conditions['worker'] = $this->flags->user['idno'];
       // }
        $attrs = array(
            'conditions' => $conditions,
        );


        if ($this->data['filter']['type_title_name'] !== '' ) {
            $attrs['type_title_name'] = $this->data['filter']['type_title_name'];
        }       

        $this->data['filter']['total'] = $total = $this->certificate_type_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->certificate_type_model->getList($attrs);
        foreach($this->data['list'] as & $row){ 
            if($row['category'] == '1'){
                $row['detail'] = base_url("management/certificate_type/type_edit/{$row['id']}");
            } else if($row['category'] == '2'){
                $row['detail'] = base_url("management/certificate_type/en_type_edit/{$row['id']}");
            }
            //$row['del'] = base_url("management/certificate_type/type_del/{$row['id']}");
        }



        $this->load->library('pagination');
        $config['base_url'] = base_url("management/certificate_type?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        
        $this->layout->view('management/certificate_type/list',$this->data);
    }


    public function type_add()
    {
        $this->data['type_action'] = "add";    //設定為ADD模式
        $this->data['save_url'] = base_url("management/certificate_type/type_add");
        $this->data['view_cer_url'] = base_url("management/certificate_list/cer_pdf");
        $this->data['base_cer_url'] = base_url("management/certificate_list/");
        $this->data['bg_select_option'] = $this->get_image_option('1');
        $this->data['signature_select_option'] = $this->get_image_option('2');
        $this->data['seal_select_option'] = $this->get_image_option('3');
        $this->data['type_data'] = array("special_type"=>"1");  //2021-12-29 預設勾選 ""特殊排版""        
        if($post = $this->input->post()){
            if($post['action'] == 'view_cer'){  //預覽證書
                $this->insert_file_temp($post);

            }else{  //新增 if($post['action'] == 'add'){}
                $image_id = $this->insert_file_return_id($post);

            
                $insert_fields = array(
                                    'title' => $post['title'],
                                    'demo_text' => $post['demo_text'],
                                    'bg_file_id' => $image_id['bg_file'],
                                    'seal_file_id' => $image_id['seal_file'],
                                    'signature_file_id' => $image_id['signature_file'],
                                    'special_type' => $post['s_special_type'],
                                    'category' => '1',	
                                );
                
                    //var_dump($insert_fields);die();
                //寫入DBcertificate_type
                //$this->certificate_type_model->insert($insert_fields);
                if($this->certificate_type_model->insert($insert_fields)){
                    $this->setAlert(1, '書證版型新增成功');
                    redirect("management/certificate_type");
                } else {
                    $this->setAlert(2, '書證版型新增失敗');
                }


            }
        }
        $this->layout->view('management/certificate_type/type',$this->data);
    }

    public function en_type_add()
    {
        $this->data['type_action'] = "add";    //設定為ADD模式
        $this->data['save_url'] = base_url("management/certificate_type/en_type_add");
        $this->data['view_cer_url'] = base_url("management/certificate_list/en_cer_pdf");
        $this->data['base_cer_url'] = base_url("management/certificate_list/");
        $this->data['bg_select_option'] = $this->get_image_option('4');
        $this->data['signature_select_option'] = $this->get_image_option('5');
        $this->data['seal_select_option'] = $this->get_image_option('6');
        // $this->data['type_data'] = array("special_type"=>"1");  //2021-12-29 預設勾選 ""特殊排版""        
        if($post = $this->input->post()){
            if($post['action'] == 'view_cer'){  //預覽證書
                $this->insert_file_temp_en($post);

            }else{  //新增 if($post['action'] == 'add'){}
                $image_id = $this->insert_file_return_id_en($post);

                $insert_fields = array(
                                    'title' => addslashes($post['title']),
                                    'demo_text' => addslashes($post['demo_text']),
                                    'bg_file_id' => $image_id['bg_file'],
                                    'seal_file_id' => $image_id['seal_file'],
                                    'signature_file_id' => $image_id['signature_file'],
                                    'category' => '2',
                                    'qr_top_text' => addslashes($post['qr_top_text']),
                                    'qr_bottom_text' => addslashes($post['qr_bottom_text']),	
                                );
        
                //寫入DBcertificate_type
                //$this->certificate_type_model->insert($insert_fields);
                if($this->certificate_type_model->insert($insert_fields)){
                    $this->setAlert(1, '書證版型新增成功');
                    redirect("management/certificate_type");
                } else {
                    $this->setAlert(2, '書證版型新增失敗');
                }


            }
        }
        $this->layout->view('management/certificate_type/type_en',$this->data);
    }

    private function insert_file_return_id($data){  //ok
        //初次使用載入upload(設定內容無影響)
        $config['upload_path'] = './files/certificate/bg/';
        $config['allowed_types'] = 'jpg|png';
        $config['max_size'] = '5120';
        $this->load->library('upload', $config);
        
        // 上傳邊框
        if($data['bg_select'] == '-1'){
            $config['upload_path'] = './files/certificate/bg/';
            $config['allowed_types'] = 'jpg|png';
            $config['max_size'] = '5120';
            //$config['file_name'] = '8989889'; //指定上傳檔名
            //$config['overwrite'] = true; //檔案存在複寫
            $this->load->initialize('upload', $config);
            if (!$this->upload->do_upload('bg_file')){
                $error = array('error' => $this->upload->display_errors());
                $image_id['bg_file'] = "";
            }else{
                $file_info = $this->upload->data();
                $data['bg_file'] = $file_info['file_name'];
                $bg_file_name = $data['bg_file'];
                //DB新增certificate_image資料
                if($bg_file_name != ""){
                    $insert_fields = array(
                        'title_name' => substr($bg_file_name,0,-4),
                        'file_name' => $bg_file_name,
                        'file_type' => '1',
                        );
                
                    $image_id['bg_file'] = $this->certificate_image_model->insert($insert_fields);                      
                } 
            }
        }else{
            $image_id['bg_file'] = $data['bg_select'];
        }


        // 上傳簽字章
        if($data['signature_select'] == '-1'){
            $config2['upload_path'] = './files/certificate/signature/';
            $config2['allowed_types'] = 'png';
            $config2['max_size'] = '5120';
            $this->upload->initialize($config2);    //初始化
            if (!$this->upload->do_upload('signature_file')){
                $error = array('error' => $this->upload->display_errors());
                $image_id['signature_file'] = "";
            }else{
                $file_info = $this->upload->data();
                $data['signature_file'] = $file_info['file_name'];
                $signature_file_name = $data['signature_file'];
                //DB新增certificate_image資料
                if($signature_file_name != ""){
                    $insert_fields = array(
                        'title_name' => substr($signature_file_name,0,-4),
                        'file_name' => $signature_file_name,
                        'file_type' => '2',
                        );
                
                    $image_id['signature_file'] = $this->certificate_image_model->insert($insert_fields);                      
                } 
            }
        }else{
            $image_id['signature_file'] = $data['signature_select'];
        }        



        // 上傳官防章
        if($data['seal_select'] == '-1'){
            $config3['upload_path'] = './files/certificate/seal/';
            $config3['allowed_types'] = 'png';
            $config3['max_size'] = '5120';
            $this->upload->initialize($config3);    //初始化
            if (!$this->upload->do_upload('seal_file')){
                $error = array('error' => $this->upload->display_errors());
                $image_id['seal_file'] = "";
            }else{
                $file_info = $this->upload->data();
                $data['seal_file'] = $file_info['file_name'];
                $seal_file_name = $data['seal_file'];
                //DB新增certificate_image資料
                if($seal_file_name != ""){
                    $insert_fields = array(
                        'title_name' => substr($seal_file_name,0,-4),
                        'file_name' => $seal_file_name,
                        'file_type' => '3',
                        );
                
                    $image_id['seal_file'] = $this->certificate_image_model->insert($insert_fields);                      
                } 
            }
        }else{
            $image_id['seal_file'] = $data['seal_select'];
        }



        return $image_id;
    }

    private function insert_file_return_id_en($data){  //ok
        //初次使用載入upload(設定內容無影響)
        $config['upload_path'] = './files/certificate/bg_en/';
        $config['allowed_types'] = 'jpg|png';
        $config['max_size'] = '5120';
        $this->load->library('upload', $config);
        
        // 上傳邊框
        if($data['bg_select'] == '-1'){
            $config['upload_path'] = './files/certificate/bg_en/';
            $config['allowed_types'] = 'jpg|png';
            $config['max_size'] = '5120';
            //$config['file_name'] = '8989889'; //指定上傳檔名
            //$config['overwrite'] = true; //檔案存在複寫
            $this->load->initialize('upload', $config);
            if (!$this->upload->do_upload('bg_file')){
                $error = array('error' => $this->upload->display_errors());
                $image_id['bg_file'] = "";
            }else{
                $file_info = $this->upload->data();
                $data['bg_file'] = $file_info['file_name'];
                $bg_file_name = $data['bg_file'];
                //DB新增certificate_image資料
                if($bg_file_name != ""){
                    $insert_fields = array(
                        'title_name' => substr($bg_file_name,0,-4),
                        'file_name' => $bg_file_name,
                        'file_type' => '4',
                        );
                
                    $image_id['bg_file'] = $this->certificate_image_model->insert($insert_fields);                      
                } 
            }
        }else{
            $image_id['bg_file'] = $data['bg_select'];
        }


        // 上傳簽字章
        if($data['signature_select'] == '-1'){
            $config2['upload_path'] = './files/certificate/signature_en/';
            $config2['allowed_types'] = 'png';
            $config2['max_size'] = '5120';
            $this->upload->initialize($config2);    //初始化
            if (!$this->upload->do_upload('signature_file')){
                $error = array('error' => $this->upload->display_errors());
                $image_id['signature_file'] = "";
            }else{
                $file_info = $this->upload->data();
                $data['signature_file'] = $file_info['file_name'];
                $signature_file_name = $data['signature_file'];
                //DB新增certificate_image資料
                if($signature_file_name != ""){
                    $insert_fields = array(
                        'title_name' => substr($signature_file_name,0,-4),
                        'file_name' => $signature_file_name,
                        'file_type' => '5',
                        );
                
                    $image_id['signature_file'] = $this->certificate_image_model->insert($insert_fields);                      
                } 
            }
        }else{
            $image_id['signature_file'] = $data['signature_select'];
        }        



        // 上傳官防章
        if($data['seal_select'] == '-1'){
            $config3['upload_path'] = './files/certificate/seal_en/';
            $config3['allowed_types'] = 'png';
            $config3['max_size'] = '5120';
            $this->upload->initialize($config3);    //初始化
            if (!$this->upload->do_upload('seal_file')){
                $error = array('error' => $this->upload->display_errors());
                $image_id['seal_file'] = "";
            }else{
                $file_info = $this->upload->data();
                $data['seal_file'] = $file_info['file_name'];
                $seal_file_name = $data['seal_file'];
                //DB新增certificate_image資料
                if($seal_file_name != ""){
                    $insert_fields = array(
                        'title_name' => substr($seal_file_name,0,-4),
                        'file_name' => $seal_file_name,
                        'file_type' => '6',
                        );
                
                    $image_id['seal_file'] = $this->certificate_image_model->insert($insert_fields);                      
                } 
            }
        }else{
            $image_id['seal_file'] = $data['seal_select'];
        }



        return $image_id;
    }
    
    public function get_file_name_by_id(){
        if($_POST['fid'] != ''){
            if (preg_match("/^[1-9]{1}[0-9]*+$/", $_POST['fid'])) {     //檢查是否為正整數且不可為零
                //echo "條件符合";
                $conditions = array(
                    'id' => $_POST['fid'],
                );
                $person = $this->certificate_image_model->get($conditions);
                echo htmlspecialchars($person['file_name'], ENT_HTML5|ENT_QUOTES);
            } else {
                //echo "條件不符合";
            }   
        }
        //var_dump($_POST['fid']);
        //var_dump($person);
        //var_dump($person['file_name']);
    }

    private function insert_file_temp($data){       //預覽用OK
        // 上傳邊框
        $config['upload_path'] = './files/certificate/temp/';
        $config['allowed_types'] = 'jpg|png';
        $config['max_size'] = '5120';
        $config['file_name'] = $this->flags->user['idno'].'_bg';
        $config['overwrite'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('bg_file')){
            $error = array('error' => $this->upload->display_errors());
        }else{
            $file_info = $this->upload->data();
            $data['bg_file'] = $file_info['file_name'];
        }

        // 上傳簽字章
        $config2['upload_path'] = './files/certificate/temp/';
        $config2['allowed_types'] = 'jpg|png';
        $config2['max_size'] = '102400';
        $config2['file_name'] = $this->flags->user['idno'].'_signature';
        $config2['overwrite'] = true;
        $this->upload->initialize($config2);    //初始化
        //$this->load->library('upload', $config2);

        if (!$this->upload->do_upload('signature_file')){
            $error = array('error' => $this->upload->display_errors());
        }else{
            $file_info = $this->upload->data();
            $data['signature_file'] = $file_info['file_name'];
        }

        // 上傳官防章
        $config3['upload_path'] = './files/certificate/temp/';
        $config3['allowed_types'] = 'jpg|png';
        $config3['max_size'] = '102400';
        $config3['file_name'] = $this->flags->user['idno'].'_seal';
        $config3['overwrite'] = true;
        $this->upload->initialize($config3);    //初始化
        //$this->load->library('upload', $config2);

        if (!$this->upload->do_upload('seal_file')){
            $error = array('error' => $this->upload->display_errors());
        }else{
            $file_info = $this->upload->data();
            $data['seal_file'] = $file_info['file_name'];
        }

        //$sort = $this->vote_item_model->getVoteMaxSort($data['vote_id']);
        //$data['sort'] = $sort + 1;

        //return "OK";
    }

    private function insert_file_temp_en($data){       //預覽用OK
        // 上傳邊框
        $config['upload_path'] = './files/certificate/temp_en/';
        $config['allowed_types'] = 'jpg|png';
        $config['max_size'] = '5120';
        $config['file_name'] = $this->flags->user['idno'].'_bg';
        $config['overwrite'] = true;

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('bg_file')){
            $error = array('error' => $this->upload->display_errors());
        }else{
            $file_info = $this->upload->data();
            $data['bg_file'] = $file_info['file_name'];
        }

        // 上傳簽字章
        $config2['upload_path'] = './files/certificate/temp_en/';
        $config2['allowed_types'] = 'jpg|png';
        $config2['max_size'] = '102400';
        $config2['file_name'] = $this->flags->user['idno'].'_signature';
        $config2['overwrite'] = true;
        $this->upload->initialize($config2);    //初始化
        //$this->load->library('upload', $config2);

        if (!$this->upload->do_upload('signature_file')){
            $error = array('error' => $this->upload->display_errors());
        }else{
            $file_info = $this->upload->data();
            $data['signature_file'] = $file_info['file_name'];
        }

        // 上傳官防章
        $config3['upload_path'] = './files/certificate/temp_en/';
        $config3['allowed_types'] = 'jpg|png';
        $config3['max_size'] = '102400';
        $config3['file_name'] = $this->flags->user['idno'].'_seal';
        $config3['overwrite'] = true;
        $this->upload->initialize($config3);    //初始化
        //$this->load->library('upload', $config2);

        if (!$this->upload->do_upload('seal_file')){
            $error = array('error' => $this->upload->display_errors());
        }else{
            $file_info = $this->upload->data();
            $data['seal_file'] = $file_info['file_name'];
        }

        //$sort = $this->vote_item_model->getVoteMaxSort($data['vote_id']);
        //$data['sort'] = $sort + 1;

        //return "OK";
    }

    public function get_image_option($type){ //TYPE 1 = bg_option, 2 = signature_option,3 = seal_option 
        $image_option = $this->certificate_image_model->get_image_option($type);
        //var_dump($image_option);
        return $image_option;
    }


    public function type_del($type_id)
    {
        //echo "888";die();
        $this->certificate_type_model->delete($type_id);    
        $this->setAlert(1, '刪除成功');
        redirect("management/certificate_type");
        //$this->layout->view('management/certificate_type/type_add',$this->data);
    }

    public function type_edit($type_id)
    {
        $this->data['type_data'] = $this->certificate_type_model->get($type_id);
        //var_dump($this->data['type_data']);die();
        $this->data['type_action'] = "edit";    //設定為edit模式
        $this->data['save_url'] = base_url("management/certificate_type/type_edit/".$type_id);
        $this->data['view_cer_url'] = base_url("management/certificate_list/cer_pdf");
        $this->data['base_cer_url'] = base_url("management/certificate_list/");
        $this->data['bg_select_option'] = $this->get_image_option('1');
        $this->data['signature_select_option'] = $this->get_image_option('2');
        $this->data['seal_select_option'] = $this->get_image_option('3');
        
        if($post = $this->input->post()){
            if($post['action'] == 'view_cer'){  //預覽證書
                $this->insert_file_temp($post);

            }else{  //新增 if($post['action'] == 'edit'){}
                $image_id = $this->insert_file_return_id($post);
                //var_dump($image_id);die();
                $k = $type_id;
                if($k != ''){
                    $upd_conditions = array(
                        'id' => $k,
                    );
                    $update_fields = array(
                        'title' => $post['title'],
                        'demo_text' => $post['demo_text'],
                        'bg_file_id' => $image_id['bg_file'],
                        'seal_file_id' => $image_id['seal_file'],
                        'signature_file_id' => $image_id['signature_file'],
                        'special_type' => $post['s_special_type'],
                    );
                    if($this->certificate_type_model->update($upd_conditions, $update_fields)){
                        $this->setAlert(1, '書證版型更新成功');
                        //redirect("management/certificate_type");  
                        redirect("management/certificate_type/type_edit/".$type_id);  //20210825 改為儲存後停留在同一頁
                    } else {
                        $this->setAlert(2, '書證版型更新失敗');
                    }                    
                }else{
                    $this->setAlert(2, '書證版型更新失敗');
                }


                //$this->setAlert(1, '書證版型更新成功'); //無用
                 

            }
        }
        $this->layout->view('management/certificate_type/type',$this->data);
    }

    public function en_type_edit($type_id)
    {
        $this->data['type_data'] = $this->certificate_type_model->get($type_id);
        //var_dump($this->data['type_data']);die();
        $this->data['type_action'] = "edit";    //設定為edit模式
        $this->data['save_url'] = base_url("management/certificate_type/en_type_edit/".$type_id);
        $this->data['view_cer_url'] = base_url("management/certificate_list/en_cer_pdf");
        $this->data['base_cer_url'] = base_url("management/certificate_list/");
        $this->data['bg_select_option'] = $this->get_image_option('4');
        $this->data['signature_select_option'] = $this->get_image_option('5');
        $this->data['seal_select_option'] = $this->get_image_option('6');
        
        if($post = $this->input->post()){
            if($post['action'] == 'view_cer'){  //預覽證書
                $this->insert_file_temp_en($post);

            }else{  //新增 if($post['action'] == 'edit'){}
                $image_id = $this->insert_file_return_id_en($post);
                //var_dump($image_id);die();
                $k = $type_id;
                if($k != ''){
                    $upd_conditions = array(
                        'id' => $k,
                    );
                    $update_fields = array(
                        'title' => $post['title'],
                        'demo_text' => $post['demo_text'],
                        'bg_file_id' => $image_id['bg_file'],
                        'seal_file_id' => $image_id['seal_file'],
                        'signature_file_id' => $image_id['signature_file'],
                        'qr_top_text' => $post['qr_top_text'],
                        'qr_bottom_text' => $post['qr_bottom_text'],
                    );
                    if($this->certificate_type_model->update($upd_conditions, $update_fields)){
                        $this->setAlert(1, '書證版型更新成功');
                        //redirect("management/certificate_type");  
                        redirect("management/certificate_type/en_type_edit/".$type_id);  //20210825 改為儲存後停留在同一頁
                    } else {
                        $this->setAlert(2, '書證版型更新失敗');
                    }                    
                }else{
                    $this->setAlert(2, '書證版型更新失敗');
                }


                //$this->setAlert(1, '書證版型更新成功'); //無用
                 

            }
        }
        $this->layout->view('management/certificate_type/type_en',$this->data);
    }
    
    public function delete_file_by_id()
    {
        if($post = $this->input->post()){
            $id = $post['id'];
            $file_sl = $post['file_sl']; //重整select option使用

            if ($file_sl == 'bg'){
                $file_type = '1';
            }elseif ($file_sl == 'signature'){
                $file_type = '2';
            }elseif ($file_sl == 'seal'){
                $file_type = '3';
            }

            $allow = array('bg','signature','seal','temp');

            if(!in_array($file_sl,$allow)){
                die('非法操作');
            }

            //讀取檔案路徑
            $file_path = './files/certificate/'.$file_sl.'/'.$this->certificate_type_model->get_certificate_file_name($id);
            //刪除certificate_image資料
            $del_certificate_image = $this->certificate_type_model->del_certificate_image($id);
            //將已刪除的certificate_type欄為設為零
            $del_certificate_type = $this->certificate_type_model->del_certificate_type($id,$file_sl);
            //刪除實體檔案

            
            if(file_exists($file_path)){
                unlink($file_path);//將檔案刪除
                //echo $file_path;
            }else{
                //echo '不存在';
            }


            //回傳新的option內容  ok
            
            $get_option_datas = $this->get_image_option($file_type);
            echo '<option value="-1">新增</option>';
            foreach ($get_option_datas as $get_option_data){
               echo '<option value="'.htmlspecialchars($get_option_data['id'],ENT_HTML5|ENT_QUOTES).'" >'.htmlspecialchars($get_option_data['title_name'],ENT_HTML5|ENT_QUOTES).'</option>';
            }
            
            //echo $file_sl;
           


        }
    }

    public function delete_file_by_id_en()
    {
        if($post = $this->input->post()){
            $id = $post['id'];
            $file_sl = $post['file_sl']; //重整select option使用

            if ($file_sl == 'bg'){
                $file_type = '4';
            }elseif ($file_sl == 'signature'){
                $file_type = '5';
            }elseif ($file_sl == 'seal'){
                $file_type = '6';
            }

            $allow = array('bg','signature','seal','temp');

            if(!in_array($file_sl,$allow)){
                die('非法操作');
            }

            $file_sl_en = $file_sl.'_en';
            //讀取檔案路徑
            $file_path = './files/certificate/'.$file_sl_en.'/'.$this->certificate_type_model->get_certificate_file_name($id);
            //刪除certificate_image資料
            $del_certificate_image = $this->certificate_type_model->del_certificate_image($id);
            //將已刪除的certificate_type欄為設為零
            $del_certificate_type = $this->certificate_type_model->del_certificate_type($id,$file_sl);
            //刪除實體檔案

            
            if(file_exists($file_path)){
                unlink($file_path);//將檔案刪除
                //echo $file_path;
            }else{
                //echo '不存在';
            }


            //回傳新的option內容  ok
            
            $get_option_datas = $this->get_image_option($file_type);
            echo '<option value="-1">新增</option>';
            foreach ($get_option_datas as $get_option_data){
               echo '<option value="'.htmlspecialchars($get_option_data['id'],ENT_HTML5|ENT_QUOTES).'" >'.htmlspecialchars($get_option_data['title_name'],ENT_HTML5|ENT_QUOTES).'</option>';
            }
            
            //echo $file_sl;
           


        }
    }


}
