<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $allowMethod = ['download_cer_pdf','download_en_cer_pdf'];
        $this->load->helper('des_helper');

        if (($this->router->fetch_method() === 'download_cer_pdf' || $this->router->fetch_method() === 'download_en_cer_pdf') && $this->getFilterData('pdfkey') != null){

        }else{
            if ($this->flags->is_login === FALSE) {
                redirect(base_url('welcome'));
            }            
        }
        
        $this->load->model('management/certificate_list_model');
        $this->load->model('management/certificate_image_model');
        $this->load->model('management/require_grade_model');
        $this->load->model('management/online_app_model');
        $this->load->model('management/online_app_score_model');
        $this->load->model('data/grade_category_model');
        $this->load->model('management/certificate_type_model');
        $this->load->model('management/certificate_list_table_model');
        $this->load->model('management/certificate_user_list_model');

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
        //查詢所有班期
        if ($this->data['filter']['all'] == '' ) {
            $conditions['worker'] = $this->flags->user['idno'];
        }

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

        $attrs['where_special'] = " class_status in ('2','3') and IFNULL(is_cancel, '0') = '0' ";

        if($this->data['filter']['queryType'] == 'Y'){
            $attrs['where_special'] .= " and (year, class_no, term) in (select distinct year, class_no, term from require_grade) ";
        }
        if($this->data['filter']['queryType'] == 'N'){
            $attrs['where_special'] .= " and (year, class_no, term) not in (select distinct year, class_no, term from require_grade) ";
        }

        $this->data['filter']['total'] = $total = $this->certificate_list_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['list'] = $this->certificate_list_model->getList($attrs);

        /* foreach($this->data['list'] as & $row){
            $row['detail'] = base_url("management/certificate_list/detail/{$row['seq_no']}");
        } */
        foreach($this->data['list'] as & $row){       //20210609 Roger add
            $row['detail'] = base_url("management/certificate_list/cer_list/{$row['seq_no']}"); //mark (待修改)
        }

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/certificate_list?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);


        $this->data['link_refresh'] = base_url("management/certificate_list/");
        $this->layout->view('management/certificate_list/list',$this->data);
    }

    public function cer_list($seq_no)  
    {
        $this->data['detail_data'] = $this->certificate_list_model->get($seq_no);

        $this->data['list'] = $this->certificate_list_table_model->get_list_new($seq_no);   //取得證書清單
        //var_dump($this->data['list']);die();
        $this->layout->view('management/certificate_list/cer_list',$this->data);
    }

    public function edit_certificate_list()  //Roger 20210609 add
    {
        
        $this->layout->view('management/certificate_list/edit_certificate_list',$this->data);
    }


    public function add($seq_no)
    {
        $this->data['detail_data'] = $this->certificate_list_model->get($seq_no);
        $this->data['type_list'] = $this->certificate_type_model->get_all_list_new(1); //取得全部樣板資料certificate_type
        $this->data['model'] = $this->certificate_list_model->getScoreInfoByPkey($this->data['detail_data']['year'], $this->data['detail_data']['class_no'], $this->data['detail_data']['term']);  
        $this->data['view_cer_url'] = base_url("management/certificate_list/cer_pdf/0");
        $this->data['save_url'] = base_url("management/certificate_list/add/{$seq_no}");

        $this->data['cer_list_data']['cer_date'] = date("Y-m-d");
        

        $conditions = array(
            'id' => $this->data['type_list'][0]['id'],
        );
        $temp_type_detail = $this->certificate_type_model->get($conditions); //取得第一筆樣板詳細資料certificate_type certificate_image
        //var_dump($temp_type_detail);die();
        $temp_ids =array();
        array_push($temp_ids,$temp_type_detail['bg_file_id'],$temp_type_detail['signature_file_id'],$temp_type_detail['seal_file_id']);
        $temp_file_name = array('bg_file_name','signature_file_name','seal_file_name');
        $file_name = array();
        $this->data['special_type'] = $temp_type_detail['special_type'];    //取得是否為特殊格式																										 
        foreach ($temp_ids as $key => $value) {
            $conditions = array(
                'id' => $value,
            );
            $image = $this->certificate_image_model->get($conditions);
            $file_name[$temp_file_name[$key]] = $image['file_name'];
            //array_push($temp_file_name,$image['file_name']);                   
        } 
        $this->data['file_name'] = $file_name; //取得第一筆樣板檔案名稱

        if($post = $this->input->post()){
            //var_dump($post);die();
            if(isset($post['doAction']) && $post['doAction'] == 'save'){
                //新增certificate_list ok
                $seq_no = $post['seq_no'];
                $cer_type = $post['cer_type'];
                $cer_name = $post['certificate_name'];
                $cer_number = $post['post_certificate_number'];
                $cer_unit = $post['cer_unit'];
                $cer_text = $post['demo_text'];
                $cer_date = $post['cer_date'];
                $fields = array(
                    'seq_no' => $seq_no,
                    'type_id' => $cer_type,
                    'cer_name' => $cer_name,
                    'cer_number' => $cer_number,
                    'cer_unit' => $cer_unit,
                    'cer_text' => $cer_text,
                    'cer_date' => $cer_date,
                );
                //寫入DB certificate_list OK (新增)
                $cer_list_id = $this->certificate_list_table_model->insert($fields); //正式用
                
                $conditions = array(
                    'seq_no' => $seq_no,
                    'cer_list_id' => $cer_list_id,
                );
                $class_all_user_list = $this->certificate_user_list_model->get_list_by_seq_no($conditions); //找出目前此課程的此書證的資料
                $del_user_list_idno_array = array();    //(為了找出沒勾選的並刪除)
                foreach ($class_all_user_list as $value) {
                    array_push($del_user_list_idno_array,$value['idno']);
                }
                //var_dump($class_all_user_list);die();

                //處理已勾選項目
                $chkPerson_Ary = $_POST["chkPerson"];
                if(count($chkPerson_Ary) >0 ){ 
                    foreach ($chkPerson_Ary as $idno => $on) {
                        $del_user_list_idno_array = array_diff($del_user_list_idno_array, array($idno));   //去除有勾選的

                        $conditions = array(
                            'seq_no' => $seq_no,
                            'idno' => $idno,
                            'cer_list_id' => $cer_list_id,
                        );

                        $user_list = array(
                            'cer_list_id' => $cer_list_id,
                            'idno' => $idno,
                            'st_no' => '',
                            'seq_no' => $seq_no,
                            'rank' => $_POST["scoreInfo_Rank"][$idno],
                            'del' => '0',                                    
                        );
                        //var_dump($user_list);die();
                        $temp_data = $this->certificate_user_list_model->get($conditions);
                        if($temp_data){   //如果已有資料 (更新)
                            $upd_conditions = array(
                                'id' => $temp_data['id'],
                            );
                            $this->certificate_user_list_model->update($upd_conditions, $user_list); //正式用
                        }else{  //沒資料 (新增)
                            $this->certificate_user_list_model->insert($user_list); //正式用
                        }
                        
                    }
                }
                //var_dump($del_user_list_idno_array);die();
                //處理已存在未勾選項目
                foreach ($del_user_list_idno_array as $idno) {
                    $conditions = array(
                        'idno' => $idno,
                        'cer_list_id' => $cer_list_id,
                        'seq_no' => $seq_no,
                    );
                    $this->certificate_user_list_model->soft_delete_by_idno($conditions); //正式用
                }
                redirect(base_url("management/certificate_list/cer_list/{$seq_no}"));
            }
           
        }



        $this->layout->view('management/certificate_list/detail',$this->data);
    }

    public function en_add($seq_no)
    {
        $this->data['detail_data'] = $this->certificate_list_model->get($seq_no);
        $this->data['type_list'] = $this->certificate_type_model->get_all_list_new(2); //取得全部樣板資料certificate_type
        $this->data['model'] = $this->certificate_list_model->getScoreInfoByPkey($this->data['detail_data']['year'], $this->data['detail_data']['class_no'], $this->data['detail_data']['term']);  
        $this->data['view_cer_url'] = base_url("management/certificate_list/en_cer_pdf/0");
        $this->data['save_url'] = base_url("management/certificate_list/en_add/{$seq_no}");
        $this->data['cer_list_data']['cer_date'] = date("Y-m-d");
        
        $conditions = array(
            'id' => $this->data['type_list'][0]['id'],
        );
        $temp_type_detail = $this->certificate_type_model->get($conditions); //取得第一筆樣板詳細資料certificate_type certificate_image
        
        $temp_ids =array();
        array_push($temp_ids,$temp_type_detail['bg_file_id'],$temp_type_detail['signature_file_id'],$temp_type_detail['seal_file_id']);
        $temp_file_name = array('bg_file_name','signature_file_name','seal_file_name');
        $file_name = array();
        																									 
        foreach ($temp_ids as $key => $value) {
            $conditions = array(
                'id' => $value,
            );
            $image = $this->certificate_image_model->get($conditions);
            $file_name[$temp_file_name[$key]] = $image['file_name'];
            //array_push($temp_file_name,$image['file_name']);                   
        } 
        $this->data['file_name'] = $file_name; //取得第一筆樣板檔案名稱

        if($post = $this->input->post()){
            if(isset($post['doAction']) && $post['doAction'] == 'save'){
                //新增certificate_list ok
                $seq_no = intval($post['seq_no']);
                $cer_type = intval($post['cer_type']);
                $cer_name = addslashes($post['certificate_name']);
                $cer_number = addslashes($post['post_certificate_number']);
                $cer_unit = addslashes($post['cer_unit']);
                $cer_text = addslashes($post['demo_text']);
                $cer_qr_top_txt = addslashes($post['qr_top_text']);
                $cer_qr_bottom_txt = addslashes($post['qr_bottom_text']);
                $cer_date = addslashes($post['cer_date']);
                $fields = array(
                    'seq_no' => $seq_no,
                    'type_id' => $cer_type,
                    'cer_name' => $cer_name,
                    'cer_number' => $cer_number,
                    'cer_unit' => $cer_unit,
                    'cer_text' => $cer_text,
                    'qr_top_text' => $cer_qr_top_txt,
                    'qr_bottom_text' => $cer_qr_bottom_txt,
                    'cer_date' => $cer_date,
                );
                //寫入DB certificate_list OK (新增)
                $cer_list_id = $this->certificate_list_table_model->insert($fields); //正式用
                
                $conditions = array(
                    'seq_no' => $seq_no,
                    'cer_list_id' => $cer_list_id,
                );
                $class_all_user_list = $this->certificate_user_list_model->get_list_by_seq_no($conditions); //找出目前此課程的此書證的資料
                $del_user_list_idno_array = array();    //(為了找出沒勾選的並刪除)
                foreach ($class_all_user_list as $value) {
                    array_push($del_user_list_idno_array,$value['idno']);
                }

                //處理已勾選項目
                $chkPerson_Ary = $_POST["chkPerson"];
                if(count($chkPerson_Ary) >0 ){ 
                    foreach ($chkPerson_Ary as $idno => $on) {
                        $del_user_list_idno_array = array_diff($del_user_list_idno_array, array($idno));   //去除有勾選的

                        $conditions = array(
                            'seq_no' => $seq_no,
                            'idno' => $idno,
                            'cer_list_id' => $cer_list_id,
                        );

                        $user_list = array(
                            'cer_list_id' => $cer_list_id,
                            'idno' => $idno,
                            'st_no' => '',
                            'seq_no' => $seq_no,
                            'rank' => $_POST["scoreInfo_Rank"][$idno],
                            'del' => '0',                                    
                        );
                        //var_dump($user_list);die();
                        $temp_data = $this->certificate_user_list_model->get($conditions);
                        if($temp_data){   //如果已有資料 (更新)
                            $upd_conditions = array(
                                'id' => $temp_data['id'],
                            );
                            $this->certificate_user_list_model->update($upd_conditions, $user_list); //正式用
                        }else{  //沒資料 (新增)
                            $this->certificate_user_list_model->insert($user_list); //正式用
                        }
                        
                    }
                }
            
                //處理已存在未勾選項目
                foreach ($del_user_list_idno_array as $idno) {
                    $conditions = array(
                        'idno' => $idno,
                        'cer_list_id' => $cer_list_id,
                        'seq_no' => $seq_no,
                    );
                    $this->certificate_user_list_model->soft_delete_by_idno($conditions); //正式用
                }
                redirect(base_url("management/certificate_list/cer_list/{$seq_no}"));
            }
           
        }

        $this->layout->view('management/certificate_list/en_detail',$this->data);
    }

    public function edit()
    {
        $seq_no = $_GET['seq'];
        $cer_list_id = $_GET['cid'];
        $this->data['detail_data'] = $this->certificate_list_model->get($seq_no);
        $this->data['type_list'] = $this->certificate_type_model->get_all_list_new(1); //取得全部樣板資料certificate_type
        $this->data['model'] = $this->certificate_list_model->getScoreInfoByPkey($this->data['detail_data']['year'], $this->data['detail_data']['class_no'], $this->data['detail_data']['term']);  
      
        $this->data['cer_list_data'] = $this->certificate_list_table_model->get($cer_list_id);
        if($this->data['cer_list_data']['cer_date'] == ''){
            $this->data['cer_list_data']['cer_date'] = date("Y-m-d");
        }
        
        $rank_data = array(
            'seq_no' => $seq_no,
            'cer_list_id' => $cer_list_id,
        );
        $this->data['rank_data'] = $this->certificate_user_list_model->get_rank_data($rank_data);
        $this->data['view_cer_url'] = base_url("management/certificate_list/cer_pdf/0");
        $this->data['save_url'] = base_url("management/certificate_list/edit?seq={$seq_no}&cid={$cer_list_id}");
        $this->data['rank_data'] = $this->certificate_user_list_model->get_rank_data($rank_data);


        $is_check = array(
            'seq_no' => $seq_no,
            'cer_list_id' => $cer_list_id,
        );
        $this->data['cer_check'] = $this->certificate_user_list_model->is_check($is_check); //取得發證人員
        //var_dump($this->data['cer_check']);die();
        $conditions = array(
            'id' => $this->data['type_list'][0]['id'],
        );
        $temp_type_detail = $this->certificate_type_model->get($conditions); //取得第一筆樣板詳細資料certificate_type certificate_image
        //var_dump($temp_type_detail);die();
        $temp_ids =array();
        array_push($temp_ids,$temp_type_detail['bg_file_id'],$temp_type_detail['signature_file_id'],$temp_type_detail['seal_file_id']);
        $temp_file_name = array('bg_file_name','signature_file_name','seal_file_name');
        $file_name = array();
        $this->data['special_type'] = $temp_type_detail['special_type'];    //取得是否為特殊格式																										 
        foreach ($temp_ids as $key => $value) {
            $conditions = array(
                'id' => $value,
            );
            $image = $this->certificate_image_model->get($conditions);
            $file_name[$temp_file_name[$key]] = $image['file_name'];
            //array_push($temp_file_name,$image['file_name']);                   
        } 
        $this->data['file_name'] = $file_name; //取得第一筆樣板檔案名稱

        if($post = $this->input->post()){
            //var_dump($post);die();
            if(isset($post['doAction']) && $post['doAction'] == 'save'){
                //新增certificate_list ok
                $seq_no = $post['seq_no'];
                $cer_type = $post['cer_type'];
                $cer_name = $post['certificate_name'];
                $cer_number = $post['post_certificate_number'];
                $cer_unit = $post['cer_unit'];
                $cer_text = $post['demo_text'];
                $cer_date = $post['cer_date'];
                $fields = array(
                    'seq_no' => $seq_no,
                    'type_id' => $cer_type,
                    'cer_name' => $cer_name,
                    'cer_number' => $cer_number,
                    'cer_unit' => $cer_unit,
                    'cer_text' => $cer_text,
                    'cer_date' => $cer_date,
                ); 
                //寫入DB certificate_list OK (更新)
                $upd_conditions = array(
                    'id' => $cer_list_id,
                );
                $this->certificate_list_table_model->update($upd_conditions,$fields); //正式用
                
                $conditions = array(
                    'seq_no' => $seq_no,
                    'cer_list_id' => $cer_list_id,
                );
                $class_all_user_list = $this->certificate_user_list_model->get_list_by_seq_no($conditions); //找出目前此課程的此書證的資料
                $del_user_list_idno_array = array();    //(為了找出沒勾選的並刪除)
                foreach ($class_all_user_list as $value) {
                    array_push($del_user_list_idno_array,$value['idno']);
                }
                //var_dump($del_user_list_idno_array);die();

                //處理已勾選項目
                $chkPerson_Ary = $_POST["chkPerson"];
                if(count($chkPerson_Ary) >0 ){ 
                    foreach ($chkPerson_Ary as $idno => $on) {
                        $del_user_list_idno_array = array_diff($del_user_list_idno_array, array($idno));   //去除有勾選的

                        $conditions = array(
                            'seq_no' => $seq_no,
                            'idno' => $idno,
                            'cer_list_id' => $cer_list_id,
                        );

                        $user_list = array(
                            'cer_list_id' => $cer_list_id,
                            'idno' => $idno,
                            'st_no' => '',
                            'seq_no' => $seq_no,
                            'rank' => $_POST["scoreInfo_Rank"][$idno],
                            'del' => '0',                                    
                        );
                        //var_dump($user_list);die();
                        $temp_data = $this->certificate_user_list_model->get($conditions);
                        if($temp_data){   //如果已有資料 (更新)
                            $upd_conditions = array(
                                'id' => $temp_data['id'],
                            );
                            $this->certificate_user_list_model->update($upd_conditions, $user_list); //正式用
                        }else{  //沒資料 (新增)
                            $this->certificate_user_list_model->insert($user_list); //正式用
                        }
                        
                    }
                }
                //var_dump($del_user_list_idno_array);die();
                //處理已存在未勾選項目
                foreach ($del_user_list_idno_array as $idno) {
                    $conditions = array(
                        'idno' => $idno,
                        'cer_list_id' => $cer_list_id,
                        'seq_no' => $seq_no,
                    );
                    $this->certificate_user_list_model->soft_delete_by_idno($conditions); //正式用
                }
                $this->setAlert(1, '儲存成功');
                //redirect(base_url("management/certificate_list/cer_list/{$seq_no}"));
                redirect(base_url("management/certificate_list/edit?seq={$seq_no}&cid={$cer_list_id}"));
            }
           
        }



         //var_dump('888');die();
        //var_dump($this->data['model']);die();
        /*
        $cid_list = $this->certificate_list_model->get_cid_list($this->data['detail_data']['year'], $this->data['detail_data']['class_no'], $this->data['detail_data']['term']);
        $new_cid_list = array();
        for($i=0;$i<count($cid_list);$i++){
            if($cid_list != '-1'){
                array_push($new_cid_list, $cid_list[$i]['elearn_id']);
            }
        }
        // jd($this->data['model']);
        foreach($this->data['model'] as & $row){
            $row['listData'] = $this->certificate_list_model->query_online_app($this->data['detail_data']['class_no'], $this->data['detail_data']['year'], $this->data['detail_data']['term'], $row['id']);
            $row['checkCourseFinish'] = 0;
            if(count($new_cid_list)>0) {
                //介接
                // jd($row);
                // jd($cid_list);
                $row['checkCourseFinish'] = $this->checkCourseFinish(md5($row['id']), implode(", ", $new_cid_list),$this->data['detail_data']['year'], $this->data['detail_data']['start_date1'])?1:-1;
            }
        }
*/
        
        
        $this->layout->view('management/certificate_list/detail',$this->data);
    }

    public function en_edit()
    {
        $seq_no = intval($_GET['seq']);
        $cer_list_id = intval($_GET['cid']);
        $this->data['detail_data'] = $this->certificate_list_model->get($seq_no);
        $this->data['type_list'] = $this->certificate_type_model->get_all_list_new(2); //取得全部樣板資料certificate_type
        $this->data['model'] = $this->certificate_list_model->getScoreInfoByPkey($this->data['detail_data']['year'], $this->data['detail_data']['class_no'], $this->data['detail_data']['term']);  
      
        $this->data['cer_list_data'] = $this->certificate_list_table_model->get($cer_list_id);
        if($this->data['cer_list_data']['cer_date'] == ''){
            $this->data['cer_list_data']['cer_date'] = date("Y-m-d");
        }
        
        $rank_data = array(
            'seq_no' => $seq_no,
            'cer_list_id' => $cer_list_id,
        );
        $this->data['rank_data'] = $this->certificate_user_list_model->get_rank_data($rank_data);
        $this->data['view_cer_url'] = base_url("management/certificate_list/en_cer_pdf/0");
        $this->data['save_url'] = base_url("management/certificate_list/en_edit?seq={$seq_no}&cid={$cer_list_id}");
        $this->data['rank_data'] = $this->certificate_user_list_model->get_rank_data($rank_data);

        $is_check = array(
            'seq_no' => $seq_no,
            'cer_list_id' => $cer_list_id,
        );
        $this->data['cer_check'] = $this->certificate_user_list_model->is_check($is_check); //取得發證人員
      
        $conditions = array(
            'id' => $this->data['type_list'][0]['id'],
        );
        $temp_type_detail = $this->certificate_type_model->get($conditions); //取得第一筆樣板詳細資料certificate_type certificate_image
        
        $temp_ids =array();
        array_push($temp_ids,$temp_type_detail['bg_file_id'],$temp_type_detail['signature_file_id'],$temp_type_detail['seal_file_id']);
        $temp_file_name = array('bg_file_name','signature_file_name','seal_file_name');
        $file_name = array();
        																								 
        foreach ($temp_ids as $key => $value) {
            $conditions = array(
                'id' => $value,
            );
            $image = $this->certificate_image_model->get($conditions);
            $file_name[$temp_file_name[$key]] = $image['file_name'];
            //array_push($temp_file_name,$image['file_name']);                   
        } 
        $this->data['file_name'] = $file_name; //取得第一筆樣板檔案名稱

        if($post = $this->input->post()){
            if(isset($post['doAction']) && $post['doAction'] == 'save'){
                //新增certificate_list ok
                $seq_no = intval($post['seq_no']);
                $cer_type = intval($post['cer_type']);
                $cer_name = addslashes($post['certificate_name']);
                $cer_number = addslashes($post['post_certificate_number']);
                $cer_unit = addslashes($post['cer_unit']);
                $cer_text = addslashes($post['demo_text']);
                $cer_qr_top_text = addslashes($post['qr_top_text']);
                $cer_qr_bottom_text = addslashes($post['qr_bottom_text']);
                $cer_date = addslashes($post['cer_date']);
                $fields = array(
                    'seq_no' => $seq_no,
                    'type_id' => $cer_type,
                    'cer_name' => $cer_name,
                    'cer_number' => $cer_number,
                    'cer_unit' => $cer_unit,
                    'cer_text' => $cer_text,
                    'qr_top_text' => $cer_qr_top_text,
                    'qr_bottom_text' => $cer_qr_bottom_text,
                    'cer_date' => $cer_date,
                ); 
                //寫入DB certificate_list OK (更新)
                $upd_conditions = array(
                    'id' => $cer_list_id,
                );
                $this->certificate_list_table_model->update($upd_conditions,$fields); //正式用
                
                $conditions = array(
                    'seq_no' => $seq_no,
                    'cer_list_id' => $cer_list_id,
                );
                $class_all_user_list = $this->certificate_user_list_model->get_list_by_seq_no($conditions); //找出目前此課程的此書證的資料
                $del_user_list_idno_array = array();    //(為了找出沒勾選的並刪除)
                foreach ($class_all_user_list as $value) {
                    array_push($del_user_list_idno_array,$value['idno']);
                }
    
                //處理已勾選項目
                $chkPerson_Ary = $_POST["chkPerson"];
                if(count($chkPerson_Ary) >0 ){ 
                    foreach ($chkPerson_Ary as $idno => $on) {
                        $del_user_list_idno_array = array_diff($del_user_list_idno_array, array($idno));   //去除有勾選的

                        $conditions = array(
                            'seq_no' => $seq_no,
                            'idno' => $idno,
                            'cer_list_id' => $cer_list_id,
                        );

                        $user_list = array(
                            'cer_list_id' => $cer_list_id,
                            'idno' => $idno,
                            'st_no' => '',
                            'seq_no' => $seq_no,
                            'rank' => $_POST["scoreInfo_Rank"][$idno],
                            'del' => '0',                                    
                        );
                       
                        $temp_data = $this->certificate_user_list_model->get($conditions);
                        if($temp_data){   //如果已有資料 (更新)
                            $upd_conditions = array(
                                'id' => $temp_data['id'],
                            );
                            $this->certificate_user_list_model->update($upd_conditions, $user_list); //正式用
                        }else{  //沒資料 (新增)
                            $this->certificate_user_list_model->insert($user_list); //正式用
                        }
                        
                    }
                }
            
                //處理已存在未勾選項目
                foreach ($del_user_list_idno_array as $idno) {
                    $conditions = array(
                        'idno' => $idno,
                        'cer_list_id' => $cer_list_id,
                        'seq_no' => $seq_no,
                    );
                    $this->certificate_user_list_model->soft_delete_by_idno($conditions); //正式用
                }
                $this->setAlert(1, '儲存成功');
                //redirect(base_url("management/certificate_list/cer_list/{$seq_no}"));
                redirect(base_url("management/certificate_list/en_edit?seq={$seq_no}&cid={$cer_list_id}"));
            }
           
        }

        $this->layout->view('management/certificate_list/en_detail',$this->data);
    }

    /**
     * 檢查某學員的特定課程是否都完成
     * @param resource md5(idno) $umid
     * @param resource int array $cid_list
     * @return bool tru on success, false otherwise
     */
    public function checkCourseFinish($umd5, $cid_list, $Yyear, $start_date)
    {
        $Y = (int)date('Y', strtotime($start_date));
        $m = (int)date('m', strtotime($start_date));

        if($m == 1){
            $Y = $Y-1;
        }

        if(strlen($cid_list)==0) {
            return false;
        }

        $corTotal = 1;
        for($i=0;$i<strlen($cid_list);$i++) {
            if($cid_list[$i]==",") {
                $corTotal++;
            }
        }

        $Yyear = intval($Yyear)+1911;

        $cid_array = array();

        for($i=$Yyear; $i>=$Y; $i--) {
            if($i==date("Y")) {
                $table = "mdl_fet_course_history";
            }
            else {
                $table = "mdl_fet_course_history_".$i;
            }
           
            $idno = OLD_DES::encrypt('ADLE3WE2R',$umd5);
            $idno = rtrim(strtr(base64_encode($idno), '+/', '-_'), '=');

            $data['idno'] = $idno;
            $data['table'] = $table;
            $data['cid_list'] = $cid_list;
            $data['mode'] = '3';

            $url = "http://elearning.taipei/get_data.php";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $output = curl_exec($ch);

            curl_close($ch);

            $output = unserialize($output);
            // $sql = sprintf("SELECT a.courseid FROM $table a JOIN
            //     mdl_fet_pid b ON a.userid=b.uid
            //     WHERE a.courseid in (%s)
            //     AND md5(b.idno)='%s'
            //     AND a.timecomplete>0 ", $cid_list, $umd5);

            // $result = mysql_query($sql) or die('MySQL query error');
            // $row = mysql_fetch_array($result);

            // while($row = mysql_fetch_array($result,MYSQL_ASSOC)) {
            //     if(!in_array($row['courseid'],$cid_array)){
            //         $cid_array[] = $row['courseid'];
            //     }
            // }

            if($output) {
                if(count($output)==$corTotal) {
                    return true;
                }
            }
        }

        return false;
    }

    public function year_to_cyear($your_date)   //去除時間並將西元轉民國
    {     
        $temp_date = $your_date;
        $temp_year = substr($temp_date, 0, 4);
        $temp_m_d = substr($temp_date, 4, 6);

        //將西元轉換民國
        $c_year = $temp_year - 1911;
    
    return ($c_year.$temp_m_d);
    }   

    public function get_img_file_id() 
    {     
        //$_POST['fid'] =2;
        if($_POST['fid'] != ''){
            if (preg_match("/^[1-9]{1}[0-9]*+$/", $_POST['fid'])) {     //檢查是否為正整數且不可為零
                //echo "條件符合";
                $conditions = array(
                    'id' => $_POST['fid'],
                );
                $person = $this->certificate_type_model->get($conditions);
                $temp_ids =array();
                array_push($temp_ids,$person['bg_file_id'],$person['signature_file_id'],$person['seal_file_id']);
                $temp_file_name = array('bg_file_name','signature_file_name','seal_file_name');
                $file_name = array();
                foreach ($temp_ids as $key => $value) {
                    $conditions = array(
                        'id' => $value,
                    );
                    $image = $this->certificate_image_model->get($conditions);
                    $file_name[$temp_file_name[$key]] = $image['file_name'];                
                }
                $file_name['demo_text'] = $person['demo_text']; //取得demo_text
                $file_name['special_type'] = $person['special_type']; //取得special_type
                $file_name['qr_top_text'] = $person['qr_top_text'];
                $file_name['qr_bottom_text'] = $person['qr_bottom_text'];																			  
                echo json_encode($file_name);
            } else {
                echo "";
            }   
        }     

    }    

    public function cer_pdf()  //瀏覽證書
    {
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $base_url = $protocol.$_SERVER['HTTP_HOST'];

        if($post = $this->input->post()){
            //預覽用
            $year = $this->year_to_cyear(date('Y'));
            $month = date('n');
            $day = date('j');

            $course_year = "100";
            $course_name = "夜間日語高級班";
            $term = "2";
            $user_name = "甸甸𧠐";
            if(isset($post['certificate_number'])){
                $cer_number = addslashes($post['certificate_number']);
            }else{
                $cer_number = "111111111111"; 
            } 
            $temp_start_date = "2020-07-22";
            $temp_end_date = "2020-08-22";
            $total_time = "22";
            $temp_real_end_date = "2020-08-23";
            $unit_name = "XXX局";
            $job_title = "工友";
            $rank = "一";
            $unit = addslashes($post['unit']);



            if($post['special_type']!=''){
                $special_type = addslashes($post['special_type']);
            }else{
                $special_type = '';
            }										  													  				  											 
 
            //背景圖
            if($post['bg_file_name']==''){
                $bg_jpg_url ='';
            }else{
                switch ($post['bg_path']) { //0使用正常路徑 1使用temp路徑 2不使用路徑
                    case '0':
                        $bg_jpg_url = DIR_UPLOAD_CERTS.'bg/'.addslashes($post['bg_file_name']);
                        break;

                    case '1':
                        $bg_jpg_url = DIR_UPLOAD_CERTS.'temp/'.$this->flags->user['idno'].'_bg.jpg';
                        break;

                    case '2':
                        $bg_jpg_url ='';
                        break;

                    default:
                        $bg_jpg_url ='';
                        break;
                }
            }

            //簽字章
            if($post['signature_file_name']==''){
                $signature_png ='';
            }else{
                switch ($post['signature_path']) { //0使用正常路徑 1使用temp路徑 2不使用路徑
                    case '0':
                        $signature_png = DIR_UPLOAD_CERTS.'signature/'.addslashes($post['signature_file_name']);
                        break;

                    case '1':
                        $signature_png = DIR_UPLOAD_CERTS.'temp/'.$this->flags->user['idno'].'_signature.png';
                        break;

                    case '2':
                        $signature_png ='';
                        break;

                    default:
                        $signature_png ='';
                        break;
                }
            }

            //官印
            if($post['seal_file_name']==''){
                $Official_seal_png ='';
            }else{
                switch ($post['seal_path']) { //0使用正常路徑 1使用temp路徑 2不使用路徑
                    case '0':
                        $Official_seal_png = DIR_UPLOAD_CERTS.'seal/'.addslashes($post['seal_file_name']);
                        break;

                    case '1':
                        $Official_seal_png = DIR_UPLOAD_CERTS.'temp/'.$this->flags->user['idno'].'_seal.png';
                        break;

                    case '2':
                        $Official_seal_png ='';
                        break;

                    default:
                        $Official_seal_png ='';
                        break;
                }
            }
            $content_text = addslashes($post['content_text']);

            //將西元轉民國並去除時間
            $start_date =  $this->year_to_cyear($temp_start_date);
            $end_date =  $this->year_to_cyear($temp_end_date);
            $real_end_date =  $this->year_to_cyear($temp_real_end_date);
            

            //套用參數
            $content_text = str_replace('<<姓名>>', '<b>'.$this ->print_name($user_name).'</b>', $content_text);
            $content_text = str_replace('<<課程年度>>', $course_year, $content_text);
            $content_text = str_replace('<<班期名稱>>', $course_name, $content_text);
            $content_text = str_replace('<<期別>>', $term, $content_text);
            $content_text = str_replace('<<開訓日期>>', $start_date, $content_text);
            $content_text = str_replace('<<時數>>', $total_time, $content_text);

            //$content_text = str_replace('<<日期迄>>', $end_date, $content_text);    //2022-01-17 客戶要求 日期迄 與 結訓日期 對調
            //$content_text = str_replace('<<結訓日期>>', $real_end_date, $content_text); //2022-01-17 客戶要求 結訓日期 與 日期迄 對調
            $content_text = str_replace('<<結訓日期>>', $end_date, $content_text); 
            $content_text = str_replace('<<日期迄>>', $real_end_date, $content_text); 

            $content_text = str_replace('<<服務單位>>', $unit_name, $content_text);
            $content_text = str_replace('<<職稱>>', $job_title, $content_text);
            $content_text = str_replace('<<名次>>', $rank, $content_text);
            $to_pdf['content_text'] =$content_text; 
            $to_pdf['bg_jpg_url'] =$bg_jpg_url; 
            $to_pdf['signature_png'] =$signature_png; 
            $to_pdf['Official_seal_png'] =$Official_seal_png;
            $to_pdf['unit'] =$unit;
            $to_pdf['cer_number'] =$cer_number;
            $to_pdf['year'] =$year;
            $to_pdf['month'] =$month;
            $to_pdf['day'] =$day;
            $to_pdf['course_name'] = $course_name;
            $to_pdf['action'] = 'view';
			$to_pdf['special_type'] = $special_type;													
            $this->_creat_pdf($to_pdf,'');        
        }
    }

    public function en_cer_pdf()  //瀏覽證書
    {
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $base_url = $protocol.$_SERVER['HTTP_HOST'];

        if($post = $this->input->post()){
            //預覽用
            $year = $this->year_to_cyear(date('Y'));
            $month = date('n');
            $day = date('j');

            $course_year = "2021";
            $term = "4";
            $user_name = "Mei-ling Liu";
            if(isset($post['certificate_number'])){
                $cer_number = addslashes($post['certificate_number']);
            }else{
                $cer_number = "111111111111"; 
            } 
            $temp_start_date = "2021-08-03";
            $temp_end_date = "2021-09-10";
            $total_time = "22";								  													  				  											 
 
            //背景圖
            if($post['bg_file_name']==''){
                $bg_jpg_url ='';
            }else{
                switch ($post['bg_path']) { //0使用正常路徑 1使用temp路徑 2不使用路徑
                    case '0':
                        $bg_jpg_url = DIR_UPLOAD_CERTS.'bg_en/'.addslashes($post['bg_file_name']);
                        break;

                    case '1':
                        $bg_jpg_url = DIR_UPLOAD_CERTS.'temp_en/'.$this->flags->user['idno'].'_bg.jpg';
                        break;

                    case '2':
                        $bg_jpg_url ='';
                        break;

                    default:
                        $bg_jpg_url ='';
                        break;
                }
            }

            //簽字章
            if($post['signature_file_name']==''){
                $signature_png ='';
            }else{
                switch ($post['signature_path']) { //0使用正常路徑 1使用temp路徑 2不使用路徑
                    case '0':
                        $signature_png = DIR_UPLOAD_CERTS.'signature_en/'.addslashes($post['signature_file_name']);
                        break;

                    case '1':
                        $signature_png = DIR_UPLOAD_CERTS.'temp_en/'.$this->flags->user['idno'].'_signature.png';
                        break;

                    case '2':
                        $signature_png ='';
                        break;

                    default:
                        $signature_png ='';
                        break;
                }
            }

            //官印
            if($post['seal_file_name']==''){
                $Official_seal_png ='';
            }else{
                switch ($post['seal_path']) { //0使用正常路徑 1使用temp路徑 2不使用路徑
                    case '0':
                        $Official_seal_png = DIR_UPLOAD_CERTS.'seal_en/'.addslashes($post['seal_file_name']);
                        break;

                    case '1':
                        $Official_seal_png = DIR_UPLOAD_CERTS.'temp_en/'.$this->flags->user['idno'].'_seal.png';
                        break;

                    case '2':
                        $Official_seal_png ='';
                        break;

                    default:
                        $Official_seal_png ='';
                        break;
                }
            }
            
            $content_text = addslashes($post['content_text']);
            $start_date =  date('F', strtotime($temp_start_date)).' '.ltrim(date('d', strtotime($temp_start_date)),'0');
            $end_date =  date('F', strtotime($temp_end_date)).' '.ltrim(date('d', strtotime($temp_end_date)),'0').', '.date('Y', strtotime($temp_end_date));
            $content_text = str_replace('<<課程年度>>', $course_year, $content_text);
           
            if($term == 1){
                $term_txt = $term.'st';
            } else if($term == 2){
                $term_txt = $term.'nd';
            } else if($term == 3){
                $term_txt = $term.'rd';
            } else {
                $term_txt = $term.'th';
            }

            $content_text = str_replace('<<期別>>', $term_txt, $content_text);
            $content_text = str_replace('<<開訓日期>>', $start_date, $content_text);
            $content_text = str_replace('<<時數>>', $total_time, $content_text);
            $content_text = str_replace('<<結訓日期>>', $end_date, $content_text); 
           
            $to_pdf['content_text'] =$content_text; 
            $to_pdf['bg_jpg_url'] =$bg_jpg_url; 
            $to_pdf['signature_png'] =$signature_png; 
            $to_pdf['Official_seal_png'] =$Official_seal_png;
            $to_pdf['cer_number'] =$cer_number;
            $to_pdf['user_name'] =$user_name;
            $to_pdf['qrcode_top_text'] =addslashes($post['qrcode_top_text']);
            $to_pdf['qrcode_bottom_text'] =addslashes($post['qrcode_bottom_text']);
            $to_pdf['current_date'] = date('F d, Y', time());
            $to_pdf['action'] = 'view';
															
            $this->_creat_en_pdf($to_pdf,'');        
        }
    }

    public function admin_download_cer_pdf()   
    {
        if($post = $this->input->post()){
            //var_dump($post);
            //生成以選取idno
           
            foreach ($post["chkPerson"] as $key => $value) {
                $idno_array[]=$key;
            }
            if($post["view_one"]=='view_one'){
                foreach ($idno_array as $key2 => $value) {
                    if($key2 > 0){
                        unset($idno_array[$key2]);
                    }
                }
            }
            //var_dump($idno_array);//$post["scoreInfo_Rank"][$idno]
            $cer_date = addslashes($post["cer_date"]);
            //$year = $this->year_to_cyear(date('Y'));
            //$month = date('n');
            //$day = date('j');
            $year = $this->year_to_cyear(substr($cer_date, 0,4));
            $month = substr($cer_date, 5,2);
            $day = substr($cer_date, -2);
            //取得檔案名稱
            $cer_pdf_data = $this->certificate_user_list_model->get_file_name_by_type_id(addslashes($post["cer_type"]));
            if ($cer_pdf_data['bg_file'] != ""){  //20210805 Roger 為了任意背景，空值就不給檔案路徑
                $bg_jpg_url = DIR_UPLOAD_CERTS.'bg/'.$cer_pdf_data['bg_file'];
            }else{
                $bg_jpg_url = "";
            }            
            if ($cer_pdf_data['signature_file'] != ""){  //20210805 Roger 為了任意簽字章，空值就不給檔案路徑
                $signature_png = DIR_UPLOAD_CERTS.'signature/'.$cer_pdf_data['signature_file'];
            }else{
                $signature_png = "";
            }
            if ($cer_pdf_data['seal_file'] != ""){  //20210805 Roger 為了任意關防章，空值就不給檔案路徑
                $Official_seal_png = DIR_UPLOAD_CERTS.'seal/'.$cer_pdf_data['seal_file'];
            }else{
                $Official_seal_png = "";
            }

            $special_type = $cer_pdf_data['special_type'];  //2021-11-22 新增 是否為特殊格式
            
            $unit = $post['cer_unit'];
            if(isset($post['post_certificate_number'])){
                $cer_number = addslashes($post['post_certificate_number']);
            }else{
                $cer_number = "111111111111"; 
            } 
            //取得課程資料
            $course_data = $this->certificate_user_list_model->get_course_data_by_seq_no(addslashes($post["seq_no"]));
            $course_year = $course_data['course_year'];
            $course_name = $course_data['course_name'];
            $term = $course_data['term'];
            $total_time = $course_data['total_time'];            
            $temp_start_date = $course_data['temp_start_date'];      
            $temp_end_date = $course_data['temp_end_date'];          
            $temp_real_end_date = $course_data['temp_real_end_date']; 
            
            //將西元轉民國並去除時間
            $start_date =  $this->year_to_cyear($temp_start_date);
            $end_date =  $this->year_to_cyear($temp_end_date);
            $real_end_date =  $this->year_to_cyear($temp_real_end_date);


            //生成PDF資料
            foreach ($idno_array as $key => $idno) {
                //抓取使用者資料
                $user_data = $this->certificate_user_list_model->get_user_data_by_idno($idno); 
                //帶入資料
                $content_text = $post["demo_text"];
                $content_text = str_replace('<<姓名>>', '<b>'.$this ->print_name($user_data['user_name']).'</b>', $content_text);    //20210804 加上<B>之後來讓css控制樣式
                $content_text = str_replace('<<課程年度>>', $course_year, $content_text);
                $content_text = str_replace('<<班期名稱>>', $course_name, $content_text);
                $content_text = str_replace('<<期別>>', $term, $content_text);
                $content_text = str_replace('<<開訓日期>>', $start_date, $content_text);
                $content_text = str_replace('<<時數>>', $total_time, $content_text);

                //$content_text = str_replace('<<日期迄>>', $end_date, $content_text);    //2022-01-17 客戶要求 日期迄 與 結訓日期 對調
                //$content_text = str_replace('<<結訓日期>>', $real_end_date, $content_text); //2022-01-17 客戶要求 結訓日期 與 日期迄 對調
                $content_text = str_replace('<<結訓日期>>', $end_date, $content_text); 
                $content_text = str_replace('<<日期迄>>', $real_end_date, $content_text); 
        
                $content_text = str_replace('<<服務單位>>', $user_data['unit_name'], $content_text);
                $content_text = str_replace('<<職稱>>', $user_data['job_title'], $content_text);
                $content_text = str_replace('<<名次>>', $post["scoreInfo_Rank"][$idno], $content_text);
                $to_pdf[$key]['content_text'] =$content_text; 
                $to_pdf[$key]['bg_jpg_url'] =$bg_jpg_url; 
                $to_pdf[$key]['signature_png'] =$signature_png; 
                $to_pdf[$key]['Official_seal_png'] =$Official_seal_png;
                $to_pdf[$key]['unit'] =$unit;
                $to_pdf[$key]['cer_number'] =$cer_number;
                $to_pdf[$key]['year'] =$year;
                $to_pdf[$key]['month'] =$month;
                $to_pdf[$key]['day'] =$day;
                $to_pdf[$key]['course_name'] = $course_name;
                $to_pdf[$key]['action'] = 'view';
                $to_pdf[$key]['special_type'] = $special_type;  //2021-11-22 新增 是否為特殊格式
            }

            //var_dump($post);
            //die();
            $this->_creat_pdf($to_pdf,'admin');        
        }
    }  

    public function admin_download_en_cer_pdf()   
    {
        if($post = $this->input->post()){
            //生成以選取idno
            foreach ($post["chkPerson"] as $key => $value) {
                $idno_array[]=$key;
            }
            if($post["view_one"]=='view_one'){
                foreach ($idno_array as $key2 => $value) {
                    if($key2 > 0){
                        unset($idno_array[$key2]);
                    }
                }
            }
            
            $cer_date = addslashes($post["cer_date"]);
           
            //取得檔案名稱
            $cer_pdf_data = $this->certificate_user_list_model->get_file_name_by_type_id(addslashes($post["cer_type"]));
            if ($cer_pdf_data['bg_file'] != ""){  //20210805 Roger 為了任意背景，空值就不給檔案路徑
                $bg_jpg_url = DIR_UPLOAD_CERTS.'bg_en/'.$cer_pdf_data['bg_file'];
            }else{
                $bg_jpg_url = "";
            }            
            if ($cer_pdf_data['signature_file'] != ""){  //20210805 Roger 為了任意簽字章，空值就不給檔案路徑
                $signature_png = DIR_UPLOAD_CERTS.'signature_en/'.$cer_pdf_data['signature_file'];
            }else{
                $signature_png = "";
            }
            if ($cer_pdf_data['seal_file'] != ""){  //20210805 Roger 為了任意關防章，空值就不給檔案路徑
                $Official_seal_png = DIR_UPLOAD_CERTS.'seal_en/'.$cer_pdf_data['seal_file'];
            }else{
                $Official_seal_png = "";
            }

            if(isset($post['post_certificate_number'])){
                $cer_number = addslashes($post['post_certificate_number']);
            }else{
                $cer_number = "111111111111"; 
            } 
            //取得課程資料
            $course_data = $this->certificate_user_list_model->get_course_data_by_seq_no(addslashes($post["seq_no"]));
            $course_year = $course_data['course_year'];
            $course_name = $course_data['course_name'];
            $term = $course_data['term'];
            $total_time = $course_data['total_time'];            
            $temp_start_date = $course_data['temp_start_date'];      
            $temp_end_date = $course_data['temp_end_date'];          
            $temp_real_end_date = $course_data['temp_real_end_date']; 
            
            $start_date =  date('F', strtotime($temp_start_date)).' '.ltrim(date('d', strtotime($temp_start_date)),'0');
            $end_date =  date('F', strtotime($temp_end_date)).' '.ltrim(date('d', strtotime($temp_end_date)),'0').', '.date('Y', strtotime($temp_end_date));
           
            //生成PDF資料
            foreach ($idno_array as $key => $idno) {
                //抓取使用者資料
                $user_data = $this->certificate_user_list_model->get_user_data_by_idno($idno); 
                //帶入資料
                $content_text = addslashes($post["demo_text"]);
                $content_text = str_replace('<<課程年度>>', $course_year, $content_text);

                if($term == 1){
                    $term_txt = $term.'st';
                } else if($term == 2){
                    $term_txt = $term.'nd';
                } else if($term == 3){
                    $term_txt = $term.'rd';
                } else {
                    $term_txt = $term.'th';
                }

                $content_text = str_replace('<<期別>>', $term, $content_text);
                $content_text = str_replace('<<開訓日期>>', $start_date, $content_text);
                $content_text = str_replace('<<時數>>', $total_time, $content_text);
                $content_text = str_replace('<<結訓日期>>', $end_date, $content_text); 
        
                $to_pdf[$key]['content_text'] =$content_text; 
                $to_pdf[$key]['bg_jpg_url'] =$bg_jpg_url; 
                $to_pdf[$key]['signature_png'] =$signature_png; 
                $to_pdf[$key]['Official_seal_png'] =$Official_seal_png;
                $to_pdf[$key]['user_name'] =$user_data['en_name']; 
                $to_pdf[$key]['cer_number'] =$cer_number;
                $to_pdf[$key]['qrcode_top_text'] =addslashes($post['qrcode_top_text']);
                $to_pdf[$key]['qrcode_bottom_text'] =addslashes($post['qrcode_bottom_text']);
                $to_pdf[$key]['current_date'] = date('F d, Y', strtotime($cer_date));
                $to_pdf[$key]['course_name'] = $course_name;
                $to_pdf[$key]['action'] = 'view';
            }

            $this->_creat_en_pdf($to_pdf,'admin');        
        }
    }  

    public function download_cer_pdf($cer_user_list_id)   
    {
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $base_url = $protocol.$_SERVER['HTTP_HOST'];

        $data = $this->_get_pdf_data_by_user_list_id($cer_user_list_id);
        $year = $this->year_to_cyear(date('Y'));
        $month = date('n');
        $day = date('j');
        $cer_date = $data["cer_date"];
        //var_dump($cer_date);die();
        $year = $this->year_to_cyear(substr($cer_date, 0,4));
        $month = substr($cer_date, 5,2);
        $day = substr($cer_date, -2);

        $course_year = $data['course_year'];
        $course_name = $data['course_name'];
        $term = $data['term'];
        $user_name = $data['user_name'];
        $cer_number = $data['cer_number'];
        $temp_start_date = $data['temp_start_date'];
        $temp_end_date = $data['temp_end_date'];
        $total_time = $data['total_time'];
        $temp_real_end_date = $data['temp_real_end_date'];
        $unit_name = $data['unit_name'];
        $job_title = $data['job_title'];
        $rank = $data['rank'];
        $unit = $data['unit'];
        $content_text = $data['content_text'];
		
        if($data['bg_file']!=""){
            $bg_jpg_url = DIR_UPLOAD_CERTS.'bg/'.$data['bg_file'];
        }
        if($data['signature_file']!=""){
            $signature_png = DIR_UPLOAD_CERTS.'signature/'.$data['signature_file'];
        }
        if($data['seal_file']!=""){
            $Official_seal_png = DIR_UPLOAD_CERTS.'seal/'.$data['seal_file'];
        }
        $special_type = $data['special_type'];  //2021-11-22 新增 是否為特殊格式		 

        //將西元轉民國並去除時間
        $start_date =  $this->year_to_cyear($temp_start_date);
        $end_date =  $this->year_to_cyear($temp_end_date);
        $real_end_date =  $this->year_to_cyear($temp_real_end_date);
        

        //套用參數
        $content_text = str_replace('<<姓名>>', '<b>'.$this ->print_name($user_name).'</b>', $content_text);
        $content_text = str_replace('<<課程年度>>', $course_year, $content_text);
        $content_text = str_replace('<<班期名稱>>', $course_name, $content_text);
        $content_text = str_replace('<<期別>>', $term, $content_text);
        $content_text = str_replace('<<開訓日期>>', $start_date, $content_text);
        $content_text = str_replace('<<時數>>', $total_time, $content_text);

        //$content_text = str_replace('<<日期迄>>', $end_date, $content_text);    //2022-01-17 客戶要求 日期迄 與 結訓日期 對調
        //$content_text = str_replace('<<結訓日期>>', $real_end_date, $content_text); //2022-01-17 客戶要求 結訓日期 與 日期迄 對調
        $content_text = str_replace('<<結訓日期>>', $end_date, $content_text); 
        $content_text = str_replace('<<日期迄>>', $real_end_date, $content_text); 

        $content_text = str_replace('<<服務單位>>', $unit_name, $content_text);
        $content_text = str_replace('<<職稱>>', $job_title, $content_text);
        $content_text = str_replace('<<名次>>', $rank, $content_text);
        $to_pdf['content_text'] =$content_text; 
        $to_pdf['bg_jpg_url'] =$bg_jpg_url; 
        $to_pdf['signature_png'] =$signature_png; 
        $to_pdf['Official_seal_png'] =$Official_seal_png;
        $to_pdf['unit'] =$unit;
        $to_pdf['cer_number'] =$cer_number;
        $to_pdf['year'] =$year;
        $to_pdf['month'] =$month;
        $to_pdf['day'] =$day;
        $to_pdf['course_name'] = $course_name;
        $to_pdf['action'] = 'download';
        $to_pdf['special_type'] = $special_type;  //2021-11-22 新增 是否為特殊格式
        $to_pdf['id'] = $data['id'];
        $to_pdf['idno'] = $data['idno'];
        $to_pdf['user_name'] = $data['user_name'];
        $to_pdf['term'] = $data['term'];
        $to_pdf['cer_name'] = $data['cer_name'];
        $pdfkey = $this->input->get('pdfkey');
        if (!empty($pdfkey)){
            $pdfkey = DES::decode($pdfkey , "fetapei#@1");
            $pdfkey = explode("_", $pdfkey);
            if (count($pdfkey) != 3 || $pdfkey[0] != $to_pdf['id'] || $pdfkey[1] != $to_pdf['idno']){
                die('發生錯誤');
            }
        }
         
        $this->_creat_pdf($to_pdf,'');
    }   

    public function download_en_cer_pdf($cer_user_list_id)   
    {
        $protocol = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $base_url = $protocol.$_SERVER['HTTP_HOST'];

        $data = $this->_get_pdf_data_by_user_list_id($cer_user_list_id);
        $year = $this->year_to_cyear(date('Y'));
        $month = date('n');
        $day = date('j');
        $cer_date = $data["cer_date"];
        //var_dump($cer_date);die();
        $year = $this->year_to_cyear(substr($cer_date, 0,4));
        $month = substr($cer_date, 5,2);
        $day = substr($cer_date, -2);

        $course_year = $data['course_year'];
        $course_name = $data['course_name'];
        $term = $data['term'];
        $user_name = $data['user_name'];
        $cer_number = $data['cer_number'];
        $temp_start_date = $data['temp_start_date'];
        $temp_end_date = $data['temp_end_date'];
        $total_time = $data['total_time'];
        $temp_real_end_date = $data['temp_real_end_date'];
        $unit_name = $data['unit_name'];
        $job_title = $data['job_title'];
        $rank = $data['rank'];
        $unit = $data['unit'];
        $content_text = $data['content_text'];
		
        if($data['bg_file']!=""){
            $bg_jpg_url = DIR_UPLOAD_CERTS.'bg_en/'.$data['bg_file'];
        }
        if($data['signature_file']!=""){
            $signature_png = DIR_UPLOAD_CERTS.'signature_en/'.$data['signature_file'];
        }
        if($data['seal_file']!=""){
            $Official_seal_png = DIR_UPLOAD_CERTS.'seal_en/'.$data['seal_file'];
        }

        //將西元轉民國並去除時間
        $start_date =  date('F', strtotime($temp_start_date)).' '.ltrim(date('d', strtotime($temp_start_date)),'0');
        $end_date =  date('F', strtotime($temp_end_date)).' '.ltrim(date('d', strtotime($temp_end_date)),'0').', '.date('Y', strtotime($temp_end_date));
        
        if($term == 1){
            $term_txt = $term.'st';
        } else if($term == 2){
            $term_txt = $term.'nd';
        } else if($term == 3){
            $term_txt = $term.'rd';
        } else {
            $term_txt = $term.'th';
        }
        
        //套用參數
        $content_text = str_replace('<<課程年度>>', $course_year, $content_text);
        $content_text = str_replace('<<期別>>', $term, $content_text);
        $content_text = str_replace('<<開訓日期>>', $start_date, $content_text);
        $content_text = str_replace('<<時數>>', $total_time, $content_text);
        $content_text = str_replace('<<結訓日期>>', $end_date, $content_text); 

        $to_pdf['content_text'] =$content_text; 
        $to_pdf['bg_jpg_url'] =$bg_jpg_url; 
        $to_pdf['signature_png'] =$signature_png; 
        $to_pdf['Official_seal_png'] =$Official_seal_png;
        $to_pdf['unit'] =$unit;
        $to_pdf['cer_number'] =$cer_number;
        $to_pdf['year'] =$year;
        $to_pdf['month'] =$month;
        $to_pdf['day'] =$day;
        $to_pdf['course_name'] = $course_name;
        $to_pdf['action'] = 'download';
        $to_pdf['id'] = $data['id'];
        $to_pdf['idno'] = $data['idno'];
        $to_pdf['user_name'] = $data['en_name'];
        $to_pdf['qrcode_top_text'] =$data['qr_top_text'];
        $to_pdf['qrcode_bottom_text'] =$data['qr_bottom_text'];
        $to_pdf['current_date'] = date('F d, Y', strtotime($cer_date));;
        $to_pdf['term'] = $data['term'];
        $to_pdf['cer_name'] = $data['cer_name'];
        $pdfkey = $this->input->get('pdfkey');
        if (!empty($pdfkey)){
            $pdfkey = DES::decode($pdfkey , "fetapei#@1");
            $pdfkey = explode("_", $pdfkey);
            if (count($pdfkey) != 3 || $pdfkey[0] != $to_pdf['id'] || $pdfkey[1] != $to_pdf['idno']){
                die('發生錯誤');
            }
        }
         
        $this->_creat_en_pdf($to_pdf,'');
    }   

    private function _creat_pdf($data,$mode)   
    {   
        $need_fix_srt = array('𤧟'=>' <img src="'.HTTP_FIX_FILE.'1.png" /> '
                            ,'𡝮'=>' <img src="'.HTTP_FIX_FILE.'2.png" /> '
                            ,'𧃙'=>' <img src="'.HTTP_FIX_FILE.'3.png" /> '
                            ,'𦮽'=>' <img src="'.HTTP_FIX_FILE.'4.png" /> '
                            ,'𥺉'=>' <img src="'.HTTP_FIX_FILE.'5.png" /> '
                            ,'𧮮'=>' <img src="'.HTTP_FIX_FILE.'6.png" /> '
                            ,'𦭳'=>' <img src="'.HTTP_FIX_FILE.'7.png" /> '
                            ,'𧂂'=>' <img src="'.HTTP_FIX_FILE.'8.png" /> '
                            ,'𤦬'=>' <img src="'.HTTP_FIX_FILE.'9.png" /> '
                            ,'𧠐'=>' <img src="'.HTTP_FIX_FILE.'10.png" /> ');
        if($mode == 'admin'){
            $this->load->library('pdf/PHP_TCPDF');
            ob_end_clean(); 
            $pdf = new PHP_TCPDF();
            $pdf->setPrintHeader(false); //不要頁首 背景滿版必須參數
            $pdf->setPrintFooter(false); //不要頁尾 背景滿版必須參數
            $pdf->setFontSubsetting(true);//有用到的字才放到文件中
            //$pdf->SetFont('kaiu', '', 12, '', false); //設定字型   標楷體要設成false 才不會破碎
            //$pdf->SetFont('kaiu', '', 12, '', false); //設定字型
            $pdf->SetFont('droidsansfallback', '', 12, '', false); //設定字型
            // set margins 背景滿版必須參數
            $pdf->SetMargins(0, 0, 0, true); 

            // set auto page breaks false  背景滿版必須參數
            $pdf->SetAutoPageBreak(false, 0); 
                
            foreach ($data as $key => $val) {
                $pdf->AddPage('P', 'A4');
                // Display image on full page 背景 
                if($val['bg_jpg_url'] != ""){  //20210805 Roger 檔案位置是空的就不顯示
                    $pdf->Image($val['bg_jpg_url'], 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);
                }
        
                //簽字章
                if($val['signature_png'] != ""){  //20210805 Roger 檔案位置是空的就不顯示
                $pdf->Image($val['signature_png'], 55, 160, 100, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
                }
                
                // 官印
                if($val['Official_seal_png'] != ""){  //20210805 Roger 檔案位置是空的就不顯示
                $pdf->Image($val['Official_seal_png'], 65, 190, 80, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
                }

                //獎狀 單位
                $text = "<font size=\"32\" style=\"text-align:center\">{$val['unit']}</font>";
                $pdf->writeHTMLCell(0, 15, 0, 60, $text, 0, 0, 0, 0, 'L', false);

                //全部共用 字號
                $text = "<font size=\"13\" style=\"text-align:center\"> {$val['cer_number']} </font>"; //20211224 Roger 修改
                //$text = "<font size=\"13\" style=\"text-align:center\">北市訓教字第 {$val['cer_number']} 號</font>";
                if($val['special_type']==1){    //2021-11-22 新增 是否為特殊格式
                    $pdf->writeHTMLCell(0, 15, 0, 75, $text, 0, 0, 0, 0, 'L', false);   //特殊用
                }else{
                    $pdf->writeHTMLCell(0, 15, 100, 81, $text, 0, 0, 0, 0, 'C', false); 
                }																						 
				

                //獎狀 內容
                $temp_content = $val['content_text'];

                //修正替換超過xFFFF難字
                foreach ($need_fix_srt as $key => $value) {
                    $temp_content = str_replace($key,$value,$temp_content);
                }
                
                
                if($val['special_type']==1){    //2021-11-22 新增 是否為特殊格式
                    //特殊用
                    $text = "<style>
                    b {
                        font-size: 26pt;
                        font-weight:900px;
                        color: #000;
                        //line-height:36px;
                        
                    }
                    font{
                        text-align:center;
                        letter-spacing:1px;
                        line-height:36px;
                        font-size:18pt;
                    }
                    img {
                        height : 30px;
                        width : auto;
                    }
                    </style><font>{$temp_content}</font>";         
                    $pdf->writeHTMLCell(165, 30, 26, 100, $text, 0, 0, 0, 0, 'L', false);    //特殊用        
                }else{
                    $text = "<style>
                    b {
                        font-size: 26pt;
                        font-weight:900px;
                        color: #000;
                        //line-height:30px;
                    }
                    font{
                        letter-spacing:5px;
                        //line-height:40px;
                        line-height:36px;
                        font-size:18pt;
                    }
                    img {
                        height : 30px;
                        width : auto;
                    }
                    </style><font>{$temp_content}</font>";
                    $pdf->writeHTMLCell(165, 30, 26, 90, $text, 0, 0, 0, 0, 'L', false);
                }

                //全部共用 時間
                //$ttt = html_entity_decode("&#x27810;");
                if($val['special_type']==1){    //2021-11-22 新增 是否為特殊格式
                    $footer_time = "<font size=\"20\" style=\"text-align:center;letter-spacing:8px;\">中華民國&nbsp;{$val['year']} 年 {$val['month']} 月 {$val['day']} 日</font>"; //特殊用
                }else{
                    $footer_time = "<font size=\"20\" style=\"text-align:center;letter-spacing:8px;\">中 華 民 國&nbsp;{$val['year']} 年 {$val['month']} 月 {$val['day']} 日</font>";
                } 				 
                $pdf->writeHTMLCell(170, 30, 20, 245, $footer_time, 0, 0, 0, 0, 'C', false);
            }


            $out_file_name = $data[0]['course_name'].'.pdf';
            if($data[0]['action'] == 'download'){
                $pdf->Output($out_file_name, 'D');    //下載
            }else{
                $pdf->Output($out_file_name, 'I');    //線上看
            }


        }else{  
            $this->load->library('pdf/PHP_TCPDF');
            ob_end_clean(); 
            $pdf = new PHP_TCPDF();
            $pdf->setPrintHeader(false); //不要頁首 背景滿版必須參數
            $pdf->setPrintFooter(false); //不要頁尾 背景滿版必須參數
            $pdf->setFontSubsetting(true);//有用到的字才放到文件中
            //$pdf->SetFont('kaiu', '', 12, '', false); //設定字型
            $pdf->SetFont('droidsansfallback', '', 12, '', false); //設定字型
            
            // set margins 背景滿版必須參數
            $pdf->SetMargins(0, 0, 0, true); 

            // set auto page breaks false  背景滿版必須參數
            $pdf->SetAutoPageBreak(false, 0); 
                

            $pdf->AddPage('P', 'A4');
            // Display image on full page 背景 

            $pdf->Image($data['bg_jpg_url'], 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);
            //簽字章
            $pdf->Image($data['signature_png'], 55, 160, 100, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
            // 官印
            $pdf->Image($data['Official_seal_png'], 65, 190, 80, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
            
            //獎狀 單位
            $text = "<font size=\"32\" style=\"text-align:center\">{$data['unit']}</font>";
            $pdf->writeHTMLCell(0, 15, 0, 60, $text, 0, 0, 0, 0, 'L', false);

            //全部共用 字號
            $text = "<font size=\"13\" style=\"text-align:center\"> {$data['cer_number']} </font>"; //20211224 Roger 修改
            //$text = "<font size=\"13\" style=\"text-align:center\">北市訓教字第 {$data['cer_number']} 號</font>";
            if($data['special_type']==1){   //2021-11-22 新增 是否為特殊格式
                $pdf->writeHTMLCell(0, 15, 0, 75, $text, 0, 0, 0, 0, 'L', false);   //特殊用
            }else{
                $pdf->writeHTMLCell(0, 15, 100, 81, $text, 0, 0, 0, 0, 'C', false); 
            }
            //獎狀 內容
            $temp_content = $data['content_text'];

            //修正替換超過xFFFF難字
            foreach ($need_fix_srt as $key => $value) {
                $temp_content = str_replace($key,$value,$temp_content);
            }

            if($data['special_type']==1){   //2021-11-22 新增 是否為特殊格式
                //特殊用
                $text = "<style>
                b {
                    font-size: 26pt;
                    font-weight:900px;
                    color: #000;
                    //line-height:36px;
                    
                }
                font{
                    text-align:center;
                    letter-spacing:1px;
                    line-height:36px;
                    font-size:18pt;
                }
                img {
                    height : 30px;
                    width : auto;
                }
                </style><font>{$temp_content}</font>";         
                $pdf->writeHTMLCell(165, 30, 26, 100, $text, 0, 0, 0, 0, 'L', false);    //特殊用        
            }else{
                $text = "<style>
                b {
                    font-size: 26pt;
                    font-weight:900px;
                    color: #000;
                    //line-height:30px;
                    
                }
                font{
                    letter-spacing:5px;
                    //line-height:40px;
                    line-height:36px;
                    font-size:18pt;
                }
                img {
                    height : 30px;
                    width : auto;
                }
                </style><font>{$temp_content}</font>";
                $pdf->writeHTMLCell(165, 30, 26, 90, $text, 0, 0, 0, 0, 'L', false);
            }

            //全部共用 時間
            //$ttt = html_entity_decode("&#x27810;");
            if($data['special_type']==1){   //2021-11-22 新增 是否為特殊格式
                $footer_time = "<font size=\"20\" style=\"text-align:center;letter-spacing:8px;\">中華民國&nbsp;{$data['year']} 年 {$data['month']} 月 {$data['day']} 日</font>"; //特殊用
            }else{
                $footer_time = "<font size=\"20\" style=\"text-align:center;letter-spacing:8px;\">中 華 民 國&nbsp;{$data['year']} 年 {$data['month']} 月 {$data['day']} 日</font>";
            }			
            $pdf->writeHTMLCell(170, 30, 20, 245, $footer_time, 0, 0, 0, 0, 'C', false);
            
            $qrcode_manufactured_date = $this->createTapeiPassQRCode($data);
            if ($qrcode_manufactured_date != null){
                $qrcode_manufactured_date = date("Y-m-d", strtotime($qrcode_manufactured_date));
                $qrcode_manufactured_date = '有效期限：'.$qrcode_manufactured_date;
            }
            
            $pdf->writeHTMLCell(170, 10, 25, 172, "<div>驗證QRcode</div>", 0, 0, 0, 0, 'C', false);
            $pdf->writeHTMLCell(170, 10, 25, 228, "<div>僅限台北通App掃描<br>".$qrcode_manufactured_date."</div>", 0, 0, 0, 0, 'C', false);
            $taipeiPassQrcode = DIR_ROOT.'admin/images/certificate_pdf_qrcode/dcsdpdf_'.$data['id'].'.png';
            $pdf->Image($taipeiPassQrcode, 90, 190, 40, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
            $require = $this->certificate_user_list_model->getCourseByCrt($data['id']);
            if (!empty($this->input->get('pdfkey'))){
                $data['action'] = '';
                $pdf->addPage();
                $classSchedule = $this->certificate_user_list_model->getPhyScheduleForCertificate($data['id']);
                $scheduleStyle = "
                <style>
                table, td, th{
                    border:1px solid #000;
                    text-align:center;
                }             
                </style>
                ";
                $scheduleHtml = "";
            
                foreach ($classSchedule as $key => $course){
                    $course->hrs = floor($course->hrs * 10) / 10;
                    $scheduleHtml .= "<tr>";
                    $scheduleHtml .= "<td style=\"width:10%\">".($key+1)."</td>";
                    $scheduleHtml .= "<td style=\"width:60%\">".$course->name."</td>";
                    $scheduleHtml .= "<td style=\"width:30%\">".$course->hrs."</td>";
                    $scheduleHtml .= "</tr>";
                }

                $onlineScheduleHtml = "";
                
                $classOnlineSchedule = $this->certificate_user_list_model->getOnlineScheduleForCertificate($data['id']);
                foreach ($classOnlineSchedule as $key => $course){
                    $course->hours = floor($course->hours * 10) / 10;
                    $onlineScheduleHtml .= "<tr>";
                    $onlineScheduleHtml .= "<td style=\"width:10%\">".($key+1)."</td>";
                    $onlineScheduleHtml .= "<td style=\"width:60%\">".$course->class_name."</td>";
                    $onlineScheduleHtml .= "<td style=\"width:30%\">".$course->hours."</td>";
                    $onlineScheduleHtml .= "</tr>";
                }
                               
                $pdf->writeHTMLCell(170, 10, 20, 0, "<div>".$require->year.'年度 '.$require->class_name." 第".$require->term."期</div>", 0, 0, 0, 0, 'C', false);

                $scheduleOutput = $scheduleStyle."<table><thead><tr><th style=\"width:10%;background-color: blue;color:#FFF\">項次</th><th style=\"width:60%;background-color: blue;color:#FFF\">課程</th><th style=\"width:30%;background-color: blue;color:#FFF\">時數</th></tr></thead><tbody>".$scheduleHtml."</tbody></table>"."<br>";

                if ($onlineScheduleHtml != ""){
                    $scheduleOutput .= "<table><thead><tr><th style=\"width:10%;background-color: blue;color:#FFF\">項次</th><th style=\"width:60%;background-color: blue;color:#FFF\">線上課程</th><th style=\"width:30%;background-color: blue;color:#FFF\">時數</th></tr></thead><tbody>".$onlineScheduleHtml."</tbody></table>";
                }

                $pdf->writeHTMLCell(170, 30, 20, 20, $scheduleOutput, 0, 0, 0, 0, 'C', false);              
            }      

            $out_file_name = $data['user_name'].$data['course_name'].'第'.$data['term'].'期'.$data['cer_name'].'.pdf';                 
            // $out_file_name = 'certificate.pdf';
            if($data['action'] == 'download'){
                $pdf->Output($out_file_name, 'D');    //下載
            }else{
                $pdf->Output($out_file_name, 'I');    //線上看
            }
        }
        
        exit;
        
    } 

    private function _creat_en_pdf($data,$mode)   
    {   
        if($mode == 'admin'){
            $this->load->library('pdf/PHP_TCPDF');
            ob_end_clean(); 
            $pdf = new PHP_TCPDF();
            $pdf->setPrintHeader(false); //不要頁首 背景滿版必須參數
            $pdf->setPrintFooter(false); //不要頁尾 背景滿版必須參數
            $pdf->setFontSubsetting(true);//有用到的字才放到文件中
            $pdf->SetFont('times', '', 12, '', false); //設定字型
            // set margins 背景滿版必須參數
            $pdf->SetMargins(0, 0, 0, true); 

            // set auto page breaks false  背景滿版必須參數
            $pdf->SetAutoPageBreak(false, 0); 
                
            foreach ($data as $key => $val) {
                $pdf->AddPage('P', 'A4');
                // Display image on full page 背景 
                if($val['bg_jpg_url'] != ""){  //20210805 Roger 檔案位置是空的就不顯示
                    $pdf->Image($val['bg_jpg_url'], 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);
                }
        
                //簽字章
                if($val['signature_png'] != ""){  //20210805 Roger 檔案位置是空的就不顯示
                $pdf->Image($val['signature_png'], 55, 160, 100, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
                }
                
                // 官印
                if($val['Official_seal_png'] != ""){  //20210805 Roger 檔案位置是空的就不顯示
                $pdf->Image($val['Official_seal_png'], 65, 190, 80, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
                }

                //全部共用 字號
                $text = "<font size=\"13\" style=\"text-align:center\"> {$val['cer_number']} </font>";
                $pdf->writeHTMLCell(0, 15, 0, 72, $text, 0, 0, 0, 0, 'L', false); 


                $text = " <style>
                b {
                    font-size: 20pt;
                    font-weight:900px;
                }
                </style><font size=\"18\"> <b>This certificate is awarded to</b></font>";
                $pdf->writeHTMLCell(0, 15, 0, 80, $text, 0, 0, 0, 0, 'C', false); 

                $text = "<style>
                b {
                    font-size: 24pt;
                    font-weight:900px;
                }
                </style><b>{$val['user_name']}</b>"; 
                $pdf->writeHTMLCell(0, 15, 0, 90, $text, 0, 0, 0, 0, 'C', false);

                $text = "<font size=\"18\"><b>for</b></font>";
                $pdf->writeHTMLCell(0, 15, 0, 100, $text, 0, 0, 0, 0, 'C', false); 

                //獎狀 內容
                $temp_content = $val['content_text'];

                $text = "<style>
                b {
                    font-size: 20pt;
                    font-weight:900px;
                }
                font{
                    letter-spacing:2px;
                    //line-height:40px;
                    line-height:26px;
                    font-size:20pt;
                }
                img {
                    height : 30px;
                    width : auto;
                }
                </style><font style=\"text-align:center\">{$temp_content}</font>";
                $pdf->writeHTMLCell(165, 30, 26, 110, $text, 0, 0, 0, 0, 'L', false);
            
                $footer_time = "<font size=\"20\" style=\"text-align:center;letter-spacing:8px;\">{$val['current_date']}</font>";
                $pdf->writeHTMLCell(170, 30, 20, 245, $footer_time, 0, 0, 0, 0, 'C', false);
                
                $pdf->writeHTMLCell(170, 10, 25, 172, "<div>{$val['qrcode_top_text']}</div>", 0, 0, 0, 0, 'C', false);
                $pdf->writeHTMLCell(170, 10, 25, 228, "<div>{$val['qrcode_bottom_text']}</div>", 0, 0, 0, 0, 'C', false);
            }


            $out_file_name = $data[0]['course_name'].'.pdf';
            if($data[0]['action'] == 'download'){
                $pdf->Output($out_file_name, 'D');    //下載
            }else{
                $pdf->Output($out_file_name, 'I');    //線上看
            }


        }else{  
            $this->load->library('pdf/PHP_TCPDF');
            ob_end_clean(); 
            $pdf = new PHP_TCPDF();
            $pdf->setPrintHeader(false); //不要頁首 背景滿版必須參數
            $pdf->setPrintFooter(false); //不要頁尾 背景滿版必須參數
            $pdf->setFontSubsetting(true);//有用到的字才放到文件中
            //$pdf->SetFont('kaiu', '', 12, '', false); //設定字型
            $pdf->SetFont('times', '', 12, '', false); //設定字型
            
            // set margins 背景滿版必須參數
            $pdf->SetMargins(0, 0, 0, true); 

            // set auto page breaks false  背景滿版必須參數
            $pdf->SetAutoPageBreak(false, 0); 
                

            $pdf->AddPage('P', 'A4');
            // Display image on full page 背景 

            $pdf->Image($data['bg_jpg_url'], 0, 0, 210, 297, 'JPG', '', '', true, 200, '', false, false, 0, false, false, true);
            //簽字章
            $pdf->Image($data['signature_png'], 55, 160, 100, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);
            // 官印
            $pdf->Image($data['Official_seal_png'], 65, 190, 80, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);

            //全部共用 字號
            $text = "<font size=\"13\" style=\"text-align:center\"> {$data['cer_number']} </font>";
            $pdf->writeHTMLCell(0, 15, 0, 72, $text, 0, 0, 0, 0, 'L', false); 

            $text = " <style>
            b {
                font-size: 20pt;
                font-weight:900px;
            }
            </style><font size=\"15\"> <b>This certificate is awarded to</b></font>";
            $pdf->writeHTMLCell(0, 15, 0, 80, $text, 0, 0, 0, 0, 'C', false); 

            $text = "<style>
            b {
                font-size: 24pt;
                font-weight:900px;
            }
            </style><b>{$data['user_name']}</b>"; 
            $pdf->writeHTMLCell(0, 15, 0, 90, $text, 0, 0, 0, 0, 'C', false);

            $text = "<font size=\"18\"><b>for</b></font>";
            $pdf->writeHTMLCell(0, 15, 0, 100, $text, 0, 0, 0, 0, 'C', false); 

            //獎狀 內容
            $temp_content = $data['content_text'];

            $text = "<style>
            b {
                font-size: 20pt;
                font-weight:900px;
            }
            font{
                letter-spacing:2px;
                //line-height:40px;
                line-height:26px;
                font-size:20pt;
            }
            img {
                height : 30px;
                width : auto;
            }
            </style><font style=\"text-align:center\">{$temp_content}</font>";
            $pdf->writeHTMLCell(165, 30, 26, 110, $text, 0, 0, 0, 0, 'L', false);
         
            $footer_time = "<font size=\"20\" style=\"text-align:center;letter-spacing:8px;\"><b>{$data['current_date']}</b></font>";
            $pdf->writeHTMLCell(170, 30, 20, 255, $footer_time, 0, 0, 0, 0, 'C', false);
            
            $pdf->writeHTMLCell(170, 10, 25, 172, "<div>{$data['qrcode_top_text']}</div>", 0, 0, 0, 0, 'C', false);
            $pdf->writeHTMLCell(170, 10, 25, 228, "<div>{$data['qrcode_bottom_text']}</div>", 0, 0, 0, 0, 'C', false);

            $qrcode_manufactured_date = $this->createTapeiPassQRCode($data,'2');
            if ($qrcode_manufactured_date != null){
                $qrcode_manufactured_date = date("Y-m-d", strtotime($qrcode_manufactured_date));
                $qrcode_manufactured_date = '有效期限：'.$qrcode_manufactured_date;
            }

            $taipeiPassQrcode = DIR_ROOT.'admin/images/certificate_pdf_qrcode/dcsdpdf_'.$data['id'].'.png';
            $pdf->Image($taipeiPassQrcode, 90, 190, 40, 0, 'PNG', '', '', true, 100, '', false, false, 0, false, false, true);

            $out_file_name = $data['user_name'].'第'.$data['term'].'期'.$data['cer_name'].'.pdf';                 
            // $out_file_name = 'certificate.pdf';
            if($data['action'] == 'download'){
                $pdf->Output($out_file_name, 'D');    //下載
            }else{
                $pdf->Output($out_file_name, 'I');    //線上看
            }
        }
        
        exit;
        
    } 

    private function _get_pdf_data_by_user_list_id($cer_user_list_id)
    {     
        if(!empty($cer_user_list_id)){
           $cer_pdf_data = $this->certificate_user_list_model->get_pdf_data_by_user_list_id($cer_user_list_id); 
        }else{
            return "";
        }

        return $cer_pdf_data;
    }  


    public function delete_cer_list()
    {    
        //$seq_no,$cer_list_id
        $seq_no = addslashes($_GET['seq']);
        $cer_list_id = addslashes($_GET['cid']); 

        if(!empty($seq_no) && !empty($cer_list_id)){
            $conditions = array(
                'seq_no' => $seq_no,
                'cer_list_id' => $cer_list_id,
            );
            $this->certificate_list_table_model->delete($cer_list_id); //刪除Certificate_list資料
            $this->certificate_user_list_model->delete_cer_user_data($conditions); //刪除Certificate_list資料
            // 刪除外製證書
            $certificate_others = $this->db->where('certificatefile_list_id', $cer_list_id)->get('certificate_other')->result();
            foreach ($certificate_others as $certificate_other){
                $this->certificate_list_model->deleteOtherCertificate($certificate_other->id);
            }            
        }

        redirect(base_url("management/certificate_list/cer_list/{$seq_no}"));
    }   


    public function print_name($user_name)   //20210809 Roger 將姓名插入全型空白
    {
                $user_names = preg_split("/(?<!^)(?!$)/u", $user_name);
                
                for($i = 0;$i < count($user_names);$i++){
                    $user_name2 = $user_name2.$user_names[$i]."  ";
                }
                $user_name2 = rtrim($user_name2, "  ");
                return $user_name2;

    }

    public function createTapeiPassQRCode($data,$category=1)
    {
        $temp_file_name = 'images/certificate_pdf_qrcode/dcsdpdf_'.basename($data['id']).'.png';
        $certificate_user_list = $this->certificate_user_list_model->getCertificate($data['id']);
        // if (!file_exists(DIR_ROOT."admin/".$temp_file_name)){
        if (empty($certificate_user_list->qrcode_is_one_year) || $certificate_user_list->qrcode_is_one_year != $certificate_user_list->qRcodeTimeisOneYearSetting){
            $manufactured_date= (strtotime(date('Y/m/d H:i:s'))+60*60*24*365)*1000;

            if ($certificate_user_list->qRcodeTimeisOneYearSetting == 'N'){
                $manufactured_date = null;
            }

            $pdfkey = $data['id']."_".$data['idno']."_".$manufactured_date;
            $pdfkey = DES::encode($pdfkey , "fetapei#@1");
            
            if($category == '1'){
                $apidata = [
                    'pdfkey' => $pdfkey,
                    'certificate_id' => $data['id'],
                    'qrcodetype' => 'certificate',
                    'extime' => $manufactured_date
                ];
            } else if($category == '2'){
                $apidata = [
                    'pdfkey' => $pdfkey,
                    'certificate_id' => $data['id'],
                    'enqrcodetype' => 'certificate',
                    'extime' => $manufactured_date
                ];
            }
            
            $apidata = json_encode($apidata);
            // 範例網址 https://id.taipei/tpcd/taipeipass-app/scan?type=verify_fetch_callback&id=7a3a8e89-fc84-4862-ae8d-4266d929c803&data={data}&checkCode={checkCode}
            $qrcodeurlParams = [
                'type'=>'verify_fetch_callback',
                'id' => '7a3a8e89-fc84-4862-ae8d-4266d929c803',
                'data' => $apidata
            ];
            
            $qrcodeurlParams['data'] = urlencode($qrcodeurlParams['data']);
            $checkString = implode('',$qrcodeurlParams);
            $qrcodeurlParams['checkCode'] = substr(hash('sha256', $checkString), 0, 6);
            $value = "https://id.taipei/tpcd/taipeipass-app/scan?".http_build_query($qrcodeurlParams);  

            include(DIR_ROOT.'api/phpqrcode/phpqrcode.php');
            $errorCorrectionLevel = 'L';//容錯級別
            $matrixPointSize = 8;//產生QRcode圖片大小
                        
            //產生QRcode圖片
            QRcode::png($value, DIR_ROOT.'api/phpqrcode/qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2);
            $logo = DIR_ROOT.'api/phpqrcode/logo.png';//準備好的logo圖片
            $QR = DIR_ROOT.'api/phpqrcode/qrcode.png';//已經產生的原始QRcode圖片 
            
            if ($logo !== FALSE) {
                $QR = imagecreatefromstring(file_get_contents($QR));
                $logo = imagecreatefromstring(file_get_contents($logo));
                $QR_width = imagesx($QR);//QRcode圖片寬度
                $QR_height = imagesy($QR);//QRcode圖片高度
                $logo_width = imagesx($logo);//logo圖片寬度
                $logo_height = imagesy($logo);//logo圖片高度
                $logo_qr_width = $QR_width / 5;
                $scale = $logo_width/$logo_qr_width;
                $logo_qr_height = $logo_height/$scale;
                $from_width = ($QR_width - $logo_qr_width) / 2;
                //重新組合圖片並調整大小
                // imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);       

                $newwidth = $newheight = 120;
                $img = imagecreatetruecolor($newwidth, $newheight);
                imagecopyresized($img, $QR, 0, 0, 0, 0, $newwidth, $newheight, $QR_width, $QR_height);
                //輸出圖片
               
                imagepng($img, DIR_ROOT."admin/".$temp_file_name); 
                $manufactured_date = date("Y-m-d H:i:s", $manufactured_date / 1000);  
                $this->certificate_user_list_model->updateCertificateQrocdeInfo($data['id'], $certificate_user_list->qRcodeTimeisOneYearSetting, $manufactured_date);
                if ( $certificate_user_list->qRcodeTimeisOneYearSetting == 'N'){
                    return null;
                }else{
                    return $manufactured_date;
                }
            }
        }else{
            if ( $certificate_user_list->qRcodeTimeisOneYearSetting == 'N'){
                return null;
            }else{
                return $certificate_user_list->qrcode_manufactured_date;
            }            
        }
                      
    }

    // 新增外製證書畫面
    public function addOtherCertificate($seq_no)
    {
        $seq_no = $this->uri->segment(4);
        $query = $this->db->where('seq_no', $seq_no)->get('require');
        $this->data['require'] = $query->row();
        $this->data['online_apps'] = $this->certificate_list_model->getScoreInfoByPkey($this->data['require']->year, $this->data['require']->class_no, $this->data['require']->term);

        if ($this->input->method() == 'post'){
            $certificatefile_list = array(
                'seq_no' => $seq_no,
                'type_id' => 0,
                'cer_name' => '外製書證',
                'cer_number' => '',
                'cer_unit' => '',
                'cer_text' => '',
                'cer_date' => date('Y-m-d'),
            );
    
            $this->db->insert('certificate_list', $certificatefile_list);
            $certificatefile_list_id = $this->db->insert_id();
            $result = $this->uploadfile($certificatefile_list_id);
            if ($result['status'] === true){
                $this->setAlert(2, $result['message']);
            }else{
                $this->setAlert(3, $result['message']);                
            }
            
            redirect(base_url('management/certificate_list/editOtherCertificate/'.$certificatefile_list_id));
        }

        $this->layout->view('management/certificate_list/addOtherCertificate',$this->data);        
    }

    public function editOtherCertificate($certificatefile_list_id)
    {         
        $certificatefile_list = $this->db->where('id', $certificatefile_list_id)->get('certificate_list')->row();
        $query = $this->db->where('seq_no', $certificatefile_list->seq_no)->get('require');
        $this->data['require'] = $query->row();
        $this->data['online_apps'] = $this->certificate_list_model->getScoreInfoByPkey($this->data['require']->year, $this->data['require']->class_no, $this->data['require']->term);

        if ($this->input->method() == 'post'){
            $result = $this->uploadfile($certificatefile_list_id, 'insert');
            if ($result['status'] === true){
                $this->setAlert(2, $result['message']);
            }else{
                $this->setAlert(3, $result['message']);                
            }
            redirect(base_url('management/certificate_list/editOtherCertificate/'.$certificatefile_list_id));
        }

        $certificate_others = $this->db->where('certificatefile_list_id', $certificatefile_list_id)->get('certificate_other')->result_array();
        $tmpcertificate_others = array();
        foreach ($certificate_others as $certificate_other){
            $tmp_pathinfo = pathinfo($certificate_other['cer_name']);
            $certificate_other['link'] = base_url('files/upload_cert_other/'.$certificate_other['certificatefile_list_id'].'_'.$certificate_other['id'].".".$tmp_pathinfo['extension']);
            $tmpcertificate_others[$certificate_other['idno']] = $certificate_other;
        }  
        $this->data['certificate_others'] = $tmpcertificate_others;
        $this->layout->view('management/certificate_list/addOtherCertificate',$this->data);            
    }

    private function uploadfile($certificatefile_list_id){
        $certificateUpload = array();
        // 取得並整理 批次上傳的檔案
        if (!empty(array_filter($_FILES['otherCertificateBatch']['name']))){
            $batchfile = $_FILES['otherCertificateBatch'];
            $nameArray = array_column($this->data['online_apps'], 'name', 'id');

            for($i=0;$i<count($batchfile['name']);$i++){
                foreach ($nameArray as $idno => $name){
                    if (strpos($batchfile['name'][$i], $name) !== false){
                        $certificateUpload[$idno] = [
                            'name' => $batchfile['name'][$i],
                            'type' => $batchfile['type'][$i],
                            'tmp_name' => $batchfile['tmp_name'][$i],
                            'error' => $batchfile['error'][$i],
                            'size' => $batchfile['size'][$i]
                        ];
                        unset($nameArray[$idno]);
                        break;
                    }	               
                }  
            }
        }

        // 取得並整理 個別上傳的檔案
        if (!empty(array_filter($_FILES['otherCertificate']['name']))){
            $batchfile = $_FILES['otherCertificate'];

            foreach (array_keys(array_filter($batchfile['name'])) as $idno){
                $certificateUpload[$idno] = [
                    'name' => $batchfile['name'][$idno],
                    'type' => $batchfile['type'][$idno],
                    'tmp_name' => $batchfile['tmp_name'][$idno],
                    'error' => $batchfile['error'][$idno],
                    'size' => $batchfile['size'][$idno]
                ];   
            }
        }

        // 整理已上傳過的檔案名單
        $certificate_others = $this->db->where('certificatefile_list_id', $certificatefile_list_id)->get('certificate_other')->result_array();  
        $certificate_others = array_column($certificate_others, 'idno'); 

        // 開始新增 外製證書資料 以及上傳檔案
        $config = array(
            'upload_path' => DIR_UPLOAD_CERT_OTHER,
            'allowed_types' => 'pdf|png|jpg',
            'max_size' => "20000" // 單位為KB
        );

        $this->load->library('upload'); 
        $this->upload->initialize($config); 
        $success = 0;
        foreach ($certificateUpload as $idno => $certificatefile){
            if (in_array($idno, $certificate_others)){
                continue;
            }

            $this->db->trans_begin();

            $certificate_other = array(
                'certificatefile_list_id' => $certificatefile_list_id,
                'idno' => $idno,
                'cer_name' => $certificatefile['name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            );

            $this->db->insert('certificate_other', $certificate_other);
            $certificate_other_id = $this->db->insert_id();                
            
            if (!empty($certificate_other_id)){
                $_FILES['otherCertificateBatch'] = $certificatefile;
                $tmp_pathinfo = pathinfo($_FILES['otherCertificateBatch']['name']);
                $_FILES['otherCertificateBatch']['name'] = $certificatefile_list_id.'_'.$certificate_other_id.'.'.$tmp_pathinfo['extension'];

                if ( ! $this->upload->do_upload('otherCertificateBatch')){
                    $this->db->trans_rollback();
                    return [
                        'status' => false,
                        'message' => '部分上傳失敗，目前成功'.$success.'筆; 錯誤訊息如下'.$this->upload->display_errors()
                    ];
                }else{
                    $this->db->trans_commit();
                    $success++;
                }
            }

        }

        return [
            'status' => true,
            'message' => '上傳成功，成功'.$success.'筆'
        ]; 
    }

    public function deleteOtherCertificate($otherCertificate_id)
    {
        if ($this->input->method() == 'post'){
            if (!empty($otherCertificate_id)){
                $otherCertificate = $this->db->where('id', $otherCertificate_id)->get('certificate_other')->row();
                $this->certificate_list_model->deleteOtherCertificate($otherCertificate_id);
                $this->setAlert(2, '刪除成功');
                redirect(base_url('management/certificate_list/editOtherCertificate/'.$otherCertificate->certificatefile_list_id));
            }
        }
    }
}
