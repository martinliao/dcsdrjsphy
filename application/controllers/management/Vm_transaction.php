<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vm_transaction extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model([
            "require_model", 
            "online_app_model", 
            "bs_user_model", 
            "management/org_detail_model", 
            "stud_modifylog_model", 
            "card_log_model"
        ]);
        if(!isset($this->data['filter']['query_year'])){
            $this->data['filter']['query_year']=date('Y')-1911;
        }
    }

    public function index()
    {
        $condition = $this->getFilterData(['term', 'class_no', 'class_name']);
        //$condition['year'] = $this->getFilterData('year', date("Y")-1911);
        if($this->data['filter']['query_year']!=""){
            $condition['year']=$this->data['filter']['query_year'];
        }
        $this->data['requires'] = $this->require_model->getList($condition);
        $this->data['link_refresh'] = base_url("management/vm_transaction/");
        $this->layout->view('management/vm_transaction/list',$this->data);
    }
    public function detail()
    {

        $this->data['group_id']=$this->flags->user['group_id'];
        //var_dump($this->data['group_id']);

        $class_info = $this->getFilterData(['term', 'class_no', 'year'], null, true);
        if ($class_info === false ){
            $this->setAlert(3, "缺少部份參數");
            redirect(base_url("management/vm_transaction"));
        }
        
        $this->data['require'] = $this->require_model->find($class_info);
        if (empty($this->data['require'])){
            $this->setAlert(3, "該班期不存在");
            redirect(base_url("management/vm_transaction"));            
        }

        if ($post = $this->input->post()){         
            $idno = $this->getFilterData('id');
            
            if (empty($idno)){
                $this->setAlert(3, "缺少部份參數");
                redirect(base_url("management/vm_transaction"));                
            }

            $can_change = $this->card_log_model->getStudentCardLog($class_info, $idno);
            if (count($can_change) > 0){
                $this->setAlert(3, "該學員已有刷卡紀錄無法異動");
                redirect(base_url("management/vm_transaction/detail?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}&".getLastPageQuery()));               
            }

            if (!empty($post['action'])){
                switch ($post['action']) {
                    case 'exchange':
                        $status = $this->exchange($idno, $post['exchange']);
                        if ($status){
                            $this->setAlert(2, "互調完成");
                        }
                        break;
                    case 'modify':
                        $status = $this->modify($idno, $post['modify']);
                        if ($status){
                            $this->setAlert(2, "換員完成");
                        }
                        break;
                    case 'cancel':
                        $status =$this->cancelStudent($idno);
                        if ($status){
                            $this->setAlert(2, "取消參訓完成");
                        }                        
                        break;  
                    case 'change_term':
                        $status = $this->change_term($idno, $post['change_term']);
                        if ($status){
                            $this->setAlert(2, "換期成功");
                        }
                        break;
                    default:
                        # code...
                        break;
                }
                redirect(base_url("management/vm_transaction/detail?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}&".getLastPageQuery()));
            }
        }
        
        $this->data['online_apps'] = $this->online_app_model->getVmTransaction($class_info);
        $this->data['link_refresh'] = base_url("management/vm_transaction/detail?{$_SERVER['QUERY_STRING']}");
        // 取得這個班的所有班期資訊
        $class_info = $this->getFilterData(['class_no', 'year']);
        // $this->data['link_cancel'] = 'history_go_back';
        $this->data['class_all_term_infos'] = $this->require_model->getClassAllTermInfo($class_info);
        $this->layout->view('management/vm_transaction/detail',$this->data);
    }
    /*
        互調 流程
        1. 檢查學員是否存在
        2. 檢查學員有無報名該班其他期 (狀態為報名(2)、取消報名(6)、取消參訓(7)不算)
        3. 清除 要變換的班期 報名紀錄
        4. 互相變換班期
    */
    private function exchange($idno, $exchange){   
        
        $exchange_user[0] = $this->bs_user_model->find(['idno' => $idno]); 
        $exchange_user[1] = $this->bs_user_model->find(['idno' => $exchange]); 

        if (empty($exchange_user[0]) || empty($exchange_user[1])){
            $this->setAlert(3, "學員ID不存在");
            return false;
        }
        
        $condition = $this->getFilterData(['class_no', 'year']);
        $condition['id'] = $exchange_user[1]->idno;
        $condition['yn_sel_not_in'] = ['2', '6', '7'];

        $online_apps = $this->online_app_model->getList($condition);

        $terms = [];
        foreach($online_apps as $online_app){
            $terms[] = $online_app->term;
        }

        $exchange_user[0]->term = $this->getFilterData('term');
        $exchange_user[1]->term = $terms[0];

        if (count($terms) > 1){
            $this->setAlert(3, "該學員參與兩個以上班期");
            return false;
        }else if (in_array($exchange_user[0]->term, $terms) === true){
            $this->setAlert(3, "此學員已報名此班期");
            return false;
        }else if(count($terms) == 0){
            $this->setAlert(3, "此學員未報名其他期");
            return false;
        }

        $condition = $this->getFilterData(['class_no', 'year']);
        $condition['id'] = $exchange_user[0]->idno;
        $condition['term'] = $exchange_user[1]->term;
        $condition['yn_sel_not_in'] = ['2', '6', '7'];
        // 檢查 $exchange_user[0] 是否曾報名另一期別(狀態為報名(2)、取消報名(6)、取消參訓(7)不算)
        $online_apps = $this->online_app_model->getList($condition);

        if (empty($online_apps)){
            // 執行互調動作
            $class_info = $this->getFilterData(['class_no', 'year']);
            $result = $this->online_app_model->exchange($class_info, $exchange_user);

            if ($result){
                // 互調完成紀錄
                $require = $this->require_model->find($class_info);
                $org_detail = [
                    "user_id" => $exchange_user[0]->idno,
                    "year" => $require->year,
                    "term" => $result[0]['term'],
                    "classname" => $require->class_name,
                    "status" => "換班",
                    "unit" => $exchange_user[0]->bc_name,
                    "time" => date("Y-m-d")
                ];
                $this->org_detail_model->insert($org_detail);
                $org_detail['user_id'] = $exchange_user[1]->idno;
                $org_detail['term'] = $result[1]['term'];
                $org_detail['unit'] = $exchange_user[1]->bc_name;
                $this->org_detail_model->insert($org_detail);

                $stud_modifylog = [
                    "year" => $require->year,
                    "class_no" => $require->class_no,
                    "term" => $exchange_user[0]->term,
                    "beaurau_id" => $this->flags->user['bureau_id'],
                    "id" => $exchange_user[1]->idno,
                    "modify_item" => "互調",
                    "modify_date" => date("Y-m-d H:i:s"),
                    "modify_log" => "from",
                    "o_id" => $exchange_user[0]->idno,
                    "n_term" => $exchange_user[1]->term,
                    "upd_user" => $this->flags->user['username'],
                    "s_beaurau_id" => $exchange_user[1]->bureau_id
                ];

                $this->stud_modifylog_model->insert($stud_modifylog);  

                $stud_modifylog = [
                    "year" => $require->year,
                    "class_no" => $require->class_no,
                    "term" => $exchange_user[1]->term,
                    "beaurau_id" => $this->flags->user['bureau_id'],
                    "id" => $exchange_user[0]->idno,
                    "modify_item" => "互調",
                    "modify_date" => date("Y-m-d H:i:s"),
                    "modify_log" => "from",
                    "o_id" => $exchange_user[1]->idno,
                    "n_term" => $exchange_user[0]->term,
                    "upd_user" => $this->flags->user['username'],
                    "s_beaurau_id" => $exchange_user[1]->bureau_id
                ];

                $this->stud_modifylog_model->insert($stud_modifylog);

                return true;
            }
            
        }else{
            return false;
        }

        /* else if ($this->require_model->isExchangeTerm($condition['year'], $condition['class_no'], $pkterm) === false){
            // 此學員所在的期別不允許異動
            $this->setAlert(2, "此學員所在的期別不允許異動");
            return false;
        } */
    }

    private function modify($idno, $modify){
        $class_info = $this->getFilterData(['class_no', 'year', 'term']);
        // 檢查是否有輸入的這個人
        $origin_user = $this->bs_user_model->find(['idno' => $idno]);
        $user = $this->bs_user_model->find(['idno' => $modify]);
        if (empty($user)){
            $this->setAlert(3, "學員ID不存在");
            return false;
        }

        $condition = $class_info;
        $condition['id'] = $origin_user->idno;
        $origin_online_app = $this->online_app_model->getList($condition, true);


        //檢查是否已報名
        $condition = $this->getFilterData(['class_no', 'year']);
        $condition['id'] = $modify;
        $condition['yn_sel_not_in'] = ['6', '7'];
        $online_apps = $this->online_app_model->getList($condition);
        $terms = [];
        foreach($online_apps as $online_app){
            $terms[] = $online_app->term;
        }
        $term = $this->getFilterData('term');
        if (in_array($term, $terms) === true){
            $this->setAlert(3, "此學員已報名此班期");
            return false;
        }

        //檢查參訓條件
        $checks = $this->require_model->checkErollmentCondition($modify, $class_info);
        $check_msg = "";

        foreach($checks as $check){
            if ($check['status'] == false){
                $check_msg .= $check["msg"]."<br>";
            }
        } 

        if (!empty($check_msg)){
            $this->setAlert(3, $check_msg);
            return false;
        }

        // 檢查新的學員否已選員
       $condition = $class_info;
       $condition['yn_sel'] = "8";
       $condition['id'] = $modify;
       $checkChoice = $this->online_app_model->getList($condition, true);

       if (empty($checkChoice)){
            // 取得這個身分證號在這門課的優先順序
            $insert_order = $this->online_app_model->getInsertOrder($class_info, $user->bureau_id);
            $priority = $this->require_model->getPriority($class_info);
            $priority = (empty($priority)) ? '' : $priority;

            $condition = $class_info;
            $condition['yn_sel_in'] = [6,7];
            $condition['id'] = $modify;
            $check_apply = $this->online_app_model->getList($condition, true);

            $apply_data = [
                "yn_sel" => "8",
                "st_no" => $origin_online_app->st_no,
                "group_no" => $origin_online_app->group_no,
                "upd_date" => date("Y-m-d H:i:s"),
                "upd_user" => $this->flags->user["username"],
                "priority" => $priority,
                "beaurau_id" => $user->bureau_id,
                "insert_order" => $insert_order
            ];


            // 檢查有無報名過 有則 yn_sel = 8 沒有則新增
            $apply_data = array_merge($apply_data, $class_info);
            if (!empty($check_apply)){
                $condition = $class_info;
                $condition['id'] = $user->idno;
                $this->online_app_model->update($condition, $apply_data);
            }else{
                $apply_data["id"] = $user->idno;
                $apply_data["insert_date"] = date("Y-m-d H:i:s");             
                $apply_data["cre_date"] = date("Y-m-d H:i:s");
                $apply_data["cre_user"] = $this->flags->user["username"];
                $this->online_app_model->insert($apply_data);
            }
            
            // 新增紀錄
            $stud_modifylog = $class_info;
            $stud_modifylog['beaurau_id'] = $this->flags->user['bureau_id'];
            $stud_modifylog['id'] = $modify;
            $stud_modifylog['modify_item'] = "報名";
            $stud_modifylog['modify_date'] = date("Y-m-d H:i:s");
            $stud_modifylog['o_id'] = $modify;
            $stud_modifylog['n_term'] = $class_info['term'];
            $stud_modifylog['upd_user'] = $this->flags->user["username"];

            $this->stud_modifylog_model->insert($stud_modifylog);  
            
            // 變更舊學員報名狀態為 6 (取消報名)
            $condition = $class_info;
            $condition['id'] = $origin_user->idno;
            $data = [
                "yn_sel" => '6',
                "st_no" => null,
                "beaurau_id" => $user->bureau_id
            ];

            $this->online_app_model->update($condition, $data);

            $condition = $class_info;
            $condition['beaurau_id'] = $user->bureau_id;
            $online_app = $this->online_app_model->getList($condition, true);
            
            $org_detail = [
                "user_id" => $origin_user->idno,
                "year" => $class_info['year'],
                "term" => $class_info['term'],
                "classname" => $online_app->class_name,
                "status" => "換員",
                "unit" => $online_app->bc_name,
                "time" => date("Y-m-d")
            ];
            $this->org_detail_model->insert($org_detail);

            $org_detail = [
                "user_id" => $user->idno,
                "year" => $class_info['year'],
                "term" => $class_info['term'],
                "classname" => $online_app->class_name,
                "status" => "換員",
                "unit" => $online_app->bc_name,
                "time" => date("Y-m-d")
            ];
            $this->org_detail_model->insert($org_detail);

            $stud_modifylog = $class_info;
            $stud_modifylog['beaurau_id'] = $this->flags->user['bureau_id'];
            $stud_modifylog['st_no'] = $online_app->st_no;
            $stud_modifylog['id'] = $user->idno;
            $stud_modifylog['modify_item'] = "換員";
            $stud_modifylog['modify_date'] = date("Y-m-d H:i:s");
            $stud_modifylog['modify_log'] = "from";
            $stud_modifylog['o_id'] = $origin_user->idno;
            $stud_modifylog['n_term'] = $class_info['term'];
            $stud_modifylog['upd_user'] = $this->flags->user["username"];
            $stud_modifylog['s_beaurau_id'] = $user->bureau_id;

            $this->stud_modifylog_model->insert($stud_modifylog);

            $stud_modifylog['st_no'] = $online_app->st_no;
            $stud_modifylog['id'] = $origin_user->idno;
            $stud_modifylog['modify_log'] = "to";
            $stud_modifylog['o_id'] = $user->idno;
            $stud_modifylog['s_beaurau_id'] = $origin_user->bureau_id;

            $this->stud_modifylog_model->insert($stud_modifylog); 


            $this->setAlert(2, "換員成功");
       }




    }

    private function cancel($term){

    }    

    private function change_term($idno, $term)
    {
        $class_info = $this->getFilterData(['class_no', 'year', 'term']);
        $new_class_info = $class_info;
        $new_class_info['term'] = $term;

        $student = $this->bs_user_model->find(['idno' => $idno]);
        if (empty($student)){
            $this->setAlert(3, "學員ID不存在");
            return false;
        }  

        //檢查是否已報名
        $enrolle_info = $this->online_app_model->getEnrollData($new_class_info, $idno);
        $enrolled = ['2', '6', '7'];
        

        if (empty($enrolle_info)){
            $is_enrolled = false;
        }else{
            $is_enrolled = !(in_array($enrolle_info->yn_sel, $enrolled));
        }

        if($term=='請選擇期別'){
            $this->setAlert(3,"請選擇期別");
            return false;
        }
        
        //檢查要換過去的班期的學員人數是否達上限
        $class_info_change['class_no']=$class_info['class_no'];
        $class_info_change['year']=$class_info['year'];
        $class_info_change['term']=$term;
        $sd_cnt=$this->stud_modifylog_model->getStudModify($class_info_change);
        $sd=$this->online_app_model->getEnroll($class_info_change);
        $sd_cnt_after=$sd_cnt[0]['sd_cnt'];
        $sd_after=count($sd)+1;
        $group_id=$this->flags->user['group_id'];
        //&& !in_array(1,$group_id) &&!in_array(8,$group_id)
        if($sd_after>$sd_cnt_after && !in_array(1,$group_id) && !in_array(8,$group_id)){
            $this->setAlert(3,"超過該班期的限制人數");
            return false;
        }
            


        /*
        檢查是否曾報名過 
        有則更新並刪除舊班期 
        無則將舊班期更新 
        */
        if (!$is_enrolled){
            // 檢查異動限制人數
            $result = $this->checkEnrollmentPersonNo($new_class_info);

            if ($result['status'] == false){
                $this->setAlert(3, $result['msg']);
                return false;
            }
           
            $new_st_no = $this->online_app_model->getNewStNo($new_class_info);

            if (!empty($enrolle_info)){
                $condition = $new_class_info;
                $condition['id'] = $idno;
                $update_data = [
                    "st_no" => $new_st_no,
                    "yn_sel" => '8'
                ];
                $this->online_app_model->update($condition, $update_data);
                $condition = $class_info;
                $condition['id'] = $idno;                
                $this->online_app_model->delete($condition);
            }else{
                $condition = $class_info;
                $condition['id'] = $idno;
                $update_data = [
                    "st_no" => $new_st_no,
                    "yn_sel" => '8',
                    "term" => $term
                ];
                $this->online_app_model->update($condition, $update_data);
            }            
        }else{
            $this->setAlert(3, "此學員已報名此班期");
            return false;
        }

       
        // 新增log
        $stud_modifylog = $class_info;
        $stud_modifylog['beaurau_id'] = $this->flags->user['bureau_id'];
        $stud_modifylog['st_no'] = $new_st_no;
        $stud_modifylog['id'] = $student->idno;
        $stud_modifylog['modify_item'] = "換期";
        $stud_modifylog['modify_date'] = date("Y-m-d H:i:s");
        $stud_modifylog['modify_log'] = "to";
        $stud_modifylog['o_id'] = $student->idno;
        $stud_modifylog['upd_user'] = $this->flags->user["username"];
        $stud_modifylog['s_beaurau_id'] = $student->bureau_id;
        $stud_modifylog['n_term'] = $new_class_info['term'];

        $this->stud_modifylog_model->insert($stud_modifylog); 

        $stud_modifylog['modify_log'] = "from";
        $stud_modifylog['term'] = $new_class_info['term'];
        $stud_modifylog['n_term'] = $class_info['term'];
        $this->stud_modifylog_model->insert($stud_modifylog);

        $this->setAlert(3, "換期成功");
        return true;
    }

    private function checkEnrollmentPersonNo($class_info){
        $maxNo = $this->online_app_model->getCurrentClassPersonNo($class_info);
        $currentNo = $this->require_model->getMaxClassPersonNo($class_info);
        if (($maxNo > 0) && ($maxNo <= $currentNo)) {
            $reuslt = [
                'status' => false,
                'msg' => '超過該班期的限制人數'
            ];
        }else{
            $reuslt = [
                'status' => true
            ];
        }
        return $reuslt;
    }
    /**
     * 取消學員在此班期的參訓
     * @param string $idno 學員 這個班期的學生  
     */
    private function cancelStudent($idno){
        
        $student = $this->bs_user_model->find(['idno' => $idno]);
        if (empty($student)){
            $this->setAlert(3, "學員ID不存在");
            return false;
        }

        $class_info = $this->getFilterData(['year', 'class_no', 'term']);
        $condition = $class_info;
        $condition['id'] = $idno;
        $update_data = [
            "yn_sel" => '7'
        ];
        //變更狀態到取消參訓
        $this->online_app_model->update($condition, $update_data);
        $now = new DateTime('now');
        $now->sub(new DateInterval('PT8H'));
        $now = $now->format("Y-m-d H:i:s");

        $online_app = $this->online_app_model->getList($condition, true);
          
        $org_detail = [
            "user_id" => $student->idno,
            "year" => $class_info['year'],
            "term" => $class_info['term'],
            "classname" => $online_app->class_name,
            "status" => "結訓",
            "unit" => $online_app->bc_name,
            "time" => $now
        ];
        $this->org_detail_model->insert($org_detail);

        $stud_modifylog = $class_info;
        $stud_modifylog['beaurau_id'] = $this->flags->user['bureau_id'];
        $stud_modifylog['id'] = $student->idno;
        $stud_modifylog['modify_item'] = "取消";
        $stud_modifylog['modify_date'] = date("Y-m-d H:i:s");
        $stud_modifylog['o_id'] = $student->idno;
        $stud_modifylog['upd_user'] = $this->flags->user["username"];
        $stud_modifylog['s_beaurau_id'] = $student->bureau_id;
        $stud_modifylog['n_term'] = $class_info['term'];
        $this->stud_modifylog_model->insert($stud_modifylog); 

        return true;        
    }
    public function bureaus()
    {
        $this->data['group_id']=$this->flags->user['group_id'];
        $class_info = $this->getFilterData(['term', 'class_no', 'year'], null, true);
        if ($class_info === false ){
            $this->setAlert(3, "缺少部份參數");
            redirect(base_url("management/vm_transaction"));
        }
        //var_dump($class_info['term']);
        $this->db->select('sd_change,sd_chgterm');
        $this->db->where('year',$class_info['year']);
        $this->db->where('class_no',$class_info['class_no']);
        $this->db->where('term',$class_info['term']);
        $control=$this->db->get('stud_modify');
        $this->data['control']=$control->result_array();
        //var_dump( $this->data['control'][0]);
        
        $this->data['require'] = $this->require_model->find($class_info);

        if (empty($this->data['require'])){
            $this->setAlert(3, "該班期不存在");
            redirect(base_url("management/vm_transaction"));            
        }

        $now = date("Y-m-d H:i:s");
        $sd_date_time = $this->data['require']->sd_edate." ".$this->data['require']->sd_edate_h_m;
        //var_dump($now);
        //die();
        if ($now > $sd_date_time){
            $this->setAlert(3, "超過異動截止日期");
            redirect(base_url("customer_service/class_info_ha"));
        }
        

        if ($post = $this->input->post()){         
            $idno = $this->getFilterData('id');
            
            if (empty($idno)){
                $this->setAlert(3, "缺少部份參數");
                redirect(base_url("management/vm_transaction"));                
            }

            $can_change = $this->card_log_model->getStudentCardLog($class_info, $idno);
            if (count($can_change) > 0){
                $this->setAlert(3, "該學員已有刷卡紀錄無法異動");
                redirect(base_url("management/vm_transaction/bureaus?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}"));               
            }

          

            if (!empty($post['action'])){
                
                
                switch ($post['action']) {
                    case 'modify':
                        $b_change_user = $this->bs_user_model->find(['idno' => $post['modify']]);
                        $change_user = $this->bs_user_model->find(['idno' => $idno]); 
                        if (isset($change_user) && isset($b_change_user)){
                            if ($change_user->bureau_id != $b_change_user->bureau_id){
                                $this->setAlert(1, "非所屬人員");
                                redirect(base_url("management/vm_transaction/bureaus?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}"));
                            }
                        }
                        
                        $status = $this->modify($idno, $post['modify']);
                        if ($status){
                            $this->setAlert(2, "換員完成");
                        }
                        break;
                    case 'cancel':
                        $status =$this->cancelStudent($idno);
                        if ($status){
                            $this->setAlert(2, "取消參訓完成");
                        }                        
                        break;  
                    case 'exchange':
                        $status = $this->exchange($idno, $post['exchange']);
                        if ($status){
                            $this->setAlert(2, "互調完成");
                        }
                        break;
                    case 'change_term':
                         $status = $this->change_term($idno, $post['change_term']);
                        if ($status){
                            $this->setAlert(2, "換期成功");
                        }
                        break;
                }
                redirect(base_url("management/vm_transaction/bureaus?year={$class_info['year']}&term={$class_info['term']}&class_no={$class_info['class_no']}"));
            }
        }
        $this->data['online_apps'] = $this->online_app_model->getVmTransaction($class_info, $this->flags->user['bureau_id']);
        $this->data['link_refresh'] = base_url("management/vm_transaction/detail");


        // 取得這個班的所有班期資訊
        $class_info = $this->getFilterData(['class_no', 'year']);
        $this->data['link_cancel'] = 'history_go_back';
        $this->data['class_all_term_infos'] = $this->require_model->getClassAllTermInfo($class_info);

        $this->layout->view('management/vm_transaction/bureaus',$this->data);        
    }

    public function ajax()
    {
        $class_info=$this->input->post();

        
        $sd_cnt=$this->stud_modifylog_model->getStudModify($class_info);
        $sd=$this->online_app_model->getEnroll($class_info);

        if(empty($sd_cnt)){
            $sd_cnt=0;
        }else{
           $sd_cnt=$sd_cnt[0]['sd_cnt']; 
        }
        
        $sd_after=count($sd)+1;
        
        if($sd_after>$sd_cnt){
            $message='不能換班';
        }else{
            $message='可以換班';
        }
        $result=[$sd_after,$sd_cnt];
        //$result=json_encode($result);
        //return $result;
        echo json_encode($message);
    }
}
