<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_manger extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		$this->load->model('data/student_model');

		
		$this->load->model('data/job_title_model');
		$this->load->model('data/out_gov_model');
		$this->load->model('system/account_role_model');

		$this->data['choices']['group'] = $this->user_group_model->getChoices();
		$this->data['choices']['member'] = $this->student_model->getChoices();
		// $this->data['choices']['job_title'] = $this->job_title_model->getChoices();

		$this->data['choices']['supervisor'] =array(
			'' => '請選擇',
		    '1' => '首長',
		    '2' => '副首長',
		    '3' => '一級主管',
		    '4' => '二級主管',
		    '5' => '三級主管',
		    '6' => '四級以下主管',
		    '7' => '一級副主管',
		    '8' => '二級副主管',
		    '9' => '三級副主管',
		    'A' => '幕僚長',
		    'B' => '副幕僚長',
		);

		if (empty($this->data['filter']['user_group_id'])) {
			$this->data['filter']['user_group_id'] = 'all';
		}

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['idno'])) {
            $this->data['filter']['idno'] = '';
        }
        if (!isset($this->data['filter']['name'])) {
            $this->data['filter']['name'] = '';
        }
        if (!isset($this->data['filter']['bname'])) {
            $this->data['filter']['bname'] = '';
        }
        if (!isset($this->data['filter']['departure'])) {
            $this->data['filter']['departure'] = '';
        }
        if (!isset($this->data['filter']['retirement'])) {
            $this->data['filter']['retirement'] = '';
        }
		//20211111 Roger 是否顯示退休
		if (!isset($this->data['filter']['showretirement'])) {
            $this->data['filter']['showretirement'] = '';
        
		}

		if(in_array(6,$this->flags->user['group_id'])){
			$this->data['is_edap'] = true; 
		}
		
	}

	public function index()
	{
		$this->data['page_name'] = 'list';

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

		

        
        if ($this->data['filter']['departure']  != '') {
            $conditions['(departure in ("1","0") or departure is null)'] = null;
        } 
        if ($this->data['filter']['retirement']  != '') {
            $conditions['(retirement in ("1","0") or retirement is null)'] = null;
        } 
		//20211111 Roger 是否顯示退休
		if ($this->data['filter']['showretirement']  != '') {
			$conditions['(showretirement in ("1","0") or showretirement is null)'] = null;
		}

        if ($this->data['filter']['departure']  == '' && $this->data['filter']['retirement']  == '') {
        	$conditions['(retirement  not in ("0") or retirement is null) and (departure  not in ("0") or departure is null)'] = null;
        }
        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['idno'] != '') {
            $attrs['idno'] = $this->data['filter']['idno'];
        }
        if ($this->data['filter']['name']  != '') {
            $attrs['name'] = $this->data['filter']['name'];
        }
        if ($this->data['filter']['bname']  != '') {
            $attrs['bname'] = $this->data['filter']['bname'];
        }

        // $attrs['where_special'] = " username in (select username from account_role where group_id = '5')";
        if ($this->flags->user['bureau_id'] == '379680000A') {

		} elseif (substr($this->flags->user['username'], 0, 4) == 'edap') {
			$attrs['where_special'] = "  BS_user.bureau_id = '{$this->flags->user['bureau_id']}'";
		} else {
			$attrs['where_special'] = "  BS_user.username = '{$this->flags->user['username']}'";
		}

		

        if(isset($this->data['filter']['post']) && $this->data['filter']['post'] == 'post'){
        	
        	$this->data['filter']['total'] = $total = $this->student_model->getListCount($attrs);
        }else{
        	$this->data['filter']['total'] = $total = '0';
        }

        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['idno'] != '') {
            $attrs['idno'] = $this->data['filter']['idno'];
        }
        if ($this->data['filter']['name']  != '') {
            $attrs['name'] = $this->data['filter']['name'];
        }
        if ($this->data['filter']['bname']  != '') {
            $attrs['bname'] = $this->data['filter']['bname'];
        }
        // $attrs['where_special'] = " username in (select username from account_role where group_id = '5')";
        if ($this->flags->user['bureau_id'] == '379680000A') {

		} elseif (substr($this->flags->user['username'], 0, 4) == 'edap') {
			$attrs['where_special'] = "  BS_user.bureau_id = '{$this->flags->user['bureau_id']}'";
		} else {
			$attrs['where_special'] = "  BS_user.username = '{$this->flags->user['username']}'";
		}
        if(isset($this->data['filter']['post']) && $this->data['filter']['post'] == 'post'){

        	$this->data['list'] = $this->student_model->getList($attrs);
        }else{
        	$this->data['list'] = array();
        }
		// jd($this->db->last_query());
		foreach ($this->data['list'] as & $row) {
			$row['link_edit'] = base_url("data/student_manger/edit/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
			$row['link_log'] = base_url("data/student_manger/log/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
			$row['link_delete'] = base_url("data/student_manger/delete/{$row['id']}/?{$_SERVER['QUERY_STRING']}");
			$check_record = $this->student_model->checkRecord($row['idno']);

			if($check_record){
				$row['link_record'] = base_url("data/student_manger/class_record/{$row['id']}");
			}
		}

		$this->data['extra_rule'] = $this->check_ip_permiision();

		$this->load->library('pagination');
        $config['base_url'] = base_url("data/student_manger?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

		// $this->data['link_add'] = base_url("data/student_manger/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_delete'] = base_url("data/student_manger/delete/?{$_SERVER['QUERY_STRING']}");
		$this->data['url_add'] = base_url("data/student_manger/add");
		$this->data['link_refresh'] = base_url("data/student_manger/");

		$this->layout->view('data/student_manger/list', $this->data);
	}

	public function add()
	{

		$this->data['page_name'] = 'add';
		$this->data['form'] = $this->student_model->getFormDefault();
		$this->data['choices']['education'] = $this->student_model->getEducation();
		$this->data['choices']['education'][''] = '請選擇';
		$this->data['choices']['job_distinguish'] = $this->student_model->getJobDistinguish();
		$this->data['choices']['job_distinguish'][''] = '請選擇';
		$this->data['choices']['gender'] = array(''=>'請選擇','M'=>'男','F'=>'女');
		$this->data['choices']['departure'] = array('1'=>'否','0'=>'是');
		$this->data['choices']['retirement'] = array('1'=>'否','0'=>'是');
		$this->data['choices']['showretirement'] = array('1'=>'否','0'=>'是'); //20211111 Roger 是否顯示退休

		// default form data
		if (isset($this->data['filter']['user_group_id'])) {
			$this->data['form']['user_group_id'] = $this->data['filter']['user_group_id'];
		}
		if ($post = $this->input->post()) {

			foreach ($post as $key => $value) {
				// $value = $this->convertStrType($value);
				if($key == 'en_name'){
					continue;
				}
				$post[$key] = $this->make_semiangle(addslashes($value));
			}
			if(!isset($post['pid'])){
				if ($this->_isVerify('add') == TRUE) {
					$other = false;
					if(!empty($post['out_gov_name'])){
						$post['bureau_name'] = '其他';
						$post['bureau_id'] = 'D0004';
						$other = true;
					}

					$row_id = intval($post['row_id']);
					unset($post['row_id']);
					
					unset($post['job_title_name']);
					// $post['user_group_id'] = '5';
					$post['enable'] = '1';
					$saved_id = $this->student_model->_insert($post);
					if ($saved_id) {
						if(!empty($post['out_gov_name'])){
							$fields = array(
								'id' => $post['idno'],
								'ou_gov' => $post['out_gov_name'],
							);
							$this->out_gov_model->insert($fields);
						}
						$insert_date = new DateTime();
	                    $insert_date = $insert_date->format('Y-m-d H:i:s');
		                $fields = array(
		                	'username' => $post['idno'],
	        				'group_id' => '5',
	        				'cre_user' => $this->flags->user['username'],
	        				'upd_user' => $this->flags->user['username'],
	        				'cre_date' => $insert_date,
	        				'upd_date' => $insert_date,
		                );

		                $saved_id = $this->account_role_model->insert($fields);
		                if($other){
		                	$this->setAlert(2, '資料新增成功，由於您輸入了私立機關名稱，因此局處名稱將自動修改為其他。');
		                } else {
		                	$this->setAlert(1, '資料新增成功');
		                }
					}

					echo '<script>
							window.opener.again_show('.$row_id.',\''.htmlspecialchars($post['idno'],ENT_HTML5|ENT_QUOTES).'\');
							window.close();
						</script>';
				}
			}else{
				$check_idno = $this->student_model->checkIdnoExist($post['pid']);

				if($check_idno){
					$this->setAlert(2, '行政管理系統已有此員工資料, 無法新增!');
					redirect(base_url('data/student_manger'));
				}

				$check_gender = substr($post['pid'], 1,1);
				if($check_gender == '2'){
					$this->data['form']['gender'] = 'F';
				} else if($check_gender == '1'){
					$this->data['form']['gender'] = 'M';
				} else {
					$this->data['form']['gender'] = '';
				}

				$this->data['form']['idno'] = $post['pid'];
			}
		}

		$this->data['link_save'] = base_url("data/student_manger/add");
		$this->data['row_id'] = intval($post['row_id']);
	
		// $this->data['link_save'] = base_url("data/student_manger/add/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/student_manger/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/student_manger/add', $this->data);
	}
	// private function next_fchar($matches){
	//     global $queue;
	//     return $queue[$matches[1]];
	// }
	public function edit($id=NULL)
	{
		$this->data['page_name'] = 'edit';
		$this->data['form'] = $this->student_model->getFormDefault($this->student_model->get($id));

		if(isset($this->data['form']['bureau_id']) && !empty($this->data['form']['bureau_id'])){
            $this->data['form']['bureau_name'] = $this->student_model->getBureau($this->data['form']['bureau_id']);
        }

        if(isset($this->data['form']['job_title']) && !empty($this->data['form']['job_title'])){
            $this->data['form']['job_title_name'] = $this->student_model->getJobTitle($this->data['form']['job_title']);
        }

        if(isset($this->data['form']['job_level_id']) && !empty($this->data['form']['job_level_id'])){
            $this->data['form']['job_level_name'] = $this->student_model->getJobLevel($this->data['form']['job_level_id']);
        }

		$this->data['choices']['education'] = $this->student_model->getEducation();
		$this->data['choices']['education'][''] = '請選擇';
		$this->data['choices']['job_distinguish'] = $this->student_model->getJobDistinguish();
		$this->data['choices']['job_distinguish'][''] = '請選擇';
		$this->data['choices']['gender'] = array('M'=>'男','F'=>'女');
		$this->data['choices']['departure'] = array('1'=>'否','0'=>'是');
		$this->data['choices']['retirement'] = array('1'=>'否','0'=>'是');
		$this->data['choices']['showretirement'] = array('1'=>'否','0'=>'是'); //20211111 Roger 是否顯示退休

		if ($post = $this->input->post()) {
			foreach ($post as $key => $value) {
				if($key == 'en_name'){
					continue;
				}
				// $value = $this->convertStrType($value);
				$post[$key] = $this->make_semiangle(addslashes($value));
			}

			if(empty($post['birthday'])){
				$post['birthday'] = null;
			}
				
			$old_data = $this->student_model->get($id);
			if ($this->_isVerify('edit', $old_data) == TRUE) {
				$other = false;
				if(!empty($post['out_gov_name'])){
					$post['bureau_name'] = '其他';
					$post['bureau_id'] = 'D0004';
					$other = true;
				}
				unset($post['job_title_name']);
				unset($post['row_id']);

				$rs = $this->student_model->_update($id, $post);
				
				if ($rs) {
					$this->student_model->updateOnlineApp($post['bureau_id'],$old_data['idno']);
					if(!empty($post['out_gov_name'])){
						$conditions = array(
							'id' => $this->data['form']['idno'],
						);
						$out_gov_data = $this->out_gov_model->get($conditions);

						if($out_gov_data){

							$fields = array(
								'ou_gov' => $post['out_gov_name'],
							);
							$this->out_gov_model->update($conditions, $fields);
						}else{

							$fields = array(
								'id' => $this->data['form']['idno'],
								'ou_gov' => $post['out_gov_name'],
							);
							$this->out_gov_model->insert($fields);
						}

					}
					foreach ($post as $key => $value) {
						if($value != $old_data[$key]){
							$log = array(
										'field' => $key,
										'value' => $value,
										'idno' => $old_data['idno'],
										'modify_time' => date('Y-m-d H:i:s'),
										'updater' => $this->flags->user['username']
									);
							$this->student_model->insertLog($log);
						}
					}
					// var_dump($post['idno']);die();
					// 學員修改身分證字號  Alex-Chiou  2021-06-29
					if (isset($post['idno'])){
						$new_idno = addslashes($post['idno']);
						$old_idno = $old_data['idno'];
						// 更新所有 BS_user 關聯資料表的外國人學員身分證字號
						// user_modify_log
						$this->db->trans_start();
						$insert_sql1 = "INSERT INTO `user_modify_log` (`idno`, `field`, `value`,`modify_time`,`updater`) SELECT '{$new_idno}', `field`, `value`, `modify_time`,`updater` FROM `user_modify_log` AS Table_B WHERE Table_B.idno = '{$old_idno}'";
						$this->db->query($insert_sql1); 
				         
						// out_gov
						$insert_sql2 = "INSERT INTO `out_gov` (`id`, `ou_gov`) SELECT '{$new_idno}', `ou_gov` FROM `out_gov` AS Table_B WHERE Table_B.id = '{$old_idno}'";
						$this->db->query($insert_sql2); 

						// online_app
						$insert_sql3 = "INSERT INTO `online_app` (`year`, `class_no`,`term`,`id`,`beaurau_id`,`yn_sel`,`st_no`,`sel_date`,`stop_date`,`stop_reason`,`seq`,`insert_date`,`insert_order`,`priority`,`random_no`,`modi_num`,`score`,`p_score`,`s_v_hour`,`cre_user`,`cre_date`,`upd_user`,`upd_date`,`memo`,`check_in`,`group_no`,`ori_term`,`co_csv_pass`,`co_csv_pass_date`,`co_csv_memo`,`co_csv_modi_un`,`age`,`ori_beaurau_id`,`ori_title`,`ori_gender`,`co_position`,`co_education`,`yn_sel_backup`,`online_ready`,`notpass_desc`,`is_assess`,`mark`) SELECT `year`, `class_no`,`term`,'{$new_idno}',`beaurau_id`,`yn_sel`,`st_no`,`sel_date`,`stop_date`,`stop_reason`,`seq`,`insert_date`,`insert_order`,`priority`,`random_no`,`modi_num`,`score`,`p_score`,`s_v_hour`,`cre_user`,`cre_date`,`upd_user`,`upd_date`,`memo`,`check_in`,`group_no`,`ori_term`,`co_csv_pass`,`co_csv_pass_date`,`co_csv_memo`,`co_csv_modi_un`,`age`,`ori_beaurau_id`,`ori_title`,`ori_gender`,`co_position`,`co_education`,`yn_sel_backup`,`online_ready`,`notpass_desc`,`is_assess`,`mark` FROM `online_app` AS Table_B WHERE Table_B.id = '{$old_idno}'";
						$this->db->query($insert_sql3); 
						$this->db->trans_complete();
					}

                    // END 2021-06-29

					if($other){
	                	$this->setAlert(2, '資料編輯成功，由於您輸入了私立機關名稱，因此局處名稱將自動修改為其他。');
	                } else {
	                	$this->setAlert(2, '資料編輯成功');
	                }
					
				}
				redirect(base_url("data/student_manger/?{$_SERVER['QUERY_STRING']}"));
			}
		}

		$this->data['link_save'] = base_url("data/student_manger/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
		$this->data['link_cancel'] = base_url("data/student_manger/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/student_manger/edit', $this->data);
	}

	public function delete()
	{
		if ($post = $this->input->post()) {
			foreach ($post['rowid'] as $id) {
				$delete_user = $this->student_model->get($id);

				$conditions = array(
					'username' => $delete_user['username'],
					'group_id !=' => '5',
				);
				$role_data = $this->account_role_model->get($conditions);

				if(empty($role_data)){
					$rs = $this->user_model->delete($id);
					if($rs){
						$delete_array['username'] = $delete_user['username'];
						$this->account_role_model->delete($delete_array);
					}
				}

			}
			$this->setAlert(2, '資料刪除成功(如果帳號有學員以外的權限則無法刪除)');
		}

		redirect(base_url("data/student_manger/?{$_SERVER['QUERY_STRING']}"));
	}

	public function log($id)
	{
		$this->data['form'] = $this->student_model->getLog($id);

		for($i=0;$i<count($this->data['form']);$i++){
            $this->data['form'][$i]['name'] = $this->student_model->getName($this->data['form'][$i]['updater']);
          	
          	if($this->data['form'][$i]['field'] == 'supervisor_id'){
          		$supervisor_name = $this->student_model->getSupervisorName($this->data['form'][$i]['value']);
          		$this->data['form'][$i]['detail'] = $this->data['form'][$i]['detail'] = '修改'.$this->chIpt($this->data['form'][$i]['field']).'為:'.$supervisor_name;
          	} else if($this->data['form'][$i]['field'] == 'job_level_id'){
          		$job_level_name = $this->student_model->getJobLevelName($this->data['form'][$i]['value']);
          		$this->data['form'][$i]['detail'] = $this->data['form'][$i]['detail'] = '修改'.$this->chIpt($this->data['form'][$i]['field']).'為:'.$job_level_name;
          	} else {
          		$this->data['form'][$i]['detail'] = '修改'.$this->chIpt($this->data['form'][$i]['field']).'為:'.$this->chVal($this->data['form'][$i]['field'],$this->data['form'][$i]['value']);
          	}
        }

		$this->layout->view('data/student_manger/log', $this->data);
	}

	private function chIpt($s)
	{
	    $return_str = $s;
	    $ctAry = array(array("N"=>"name", "C"=>"姓名"),
				        array("N"=>"gender", "C"=>"性別"),
				        array("N"=>"birthday", "C"=>"出生日期"),
				        array("N"=>"email", "C"=>"私人Email"),
				        array("N"=>"office_email", "C"=>"公司Email"),
				        array("N"=>"bureau_name", "C"=>"局處名稱"),
				        array("N"=>"bureau_id", "C"=>"局處(代碼)名稱"),
				        array("N"=>"out_gov_name", "C"=>"私立機關名稱"),
				        array("N"=>"education", "C"=>"學歷"),
				        array("N"=>"co_empdb_poftel", "C"=>"公司電話"),
				        array("N"=>"job_distinguish", "C"=>"現職區分"),
				        array("N"=>"job_title", "C"=>"職稱"),
				        array("N"=>"office_fax", "C"=>"公司傳真"),
				        array("N"=>"departure", "C"=>"離職"),
				        array("N"=>"retirement", "C"=>"退休"),
						array("N"=>"shworetirement", "C"=>"不顯示退休"),
				        array("N"=>"supervisor_id", "C"=>"主管級別"),
				        array("N"=>"job_level_id", "C"=>"現支官職等"));
	    for ($i=0; $i < count($ctAry); $i++) {
	        if($ctAry[$i]["N"]==$s) {
	            $return_str = $ctAry[$i]["C"];
	            break;
	    	}
		}
    	return $return_str;
	}

	private function chVal($kind, $s)
	{
	    $return_str = $s;
	    if($kind=="gender") {
	        if($s=="M") {
	            $return_str = "男";
	        }
	        else {
	            $return_str = "女";
	        }
	    }
	    elseif($kind=="departure" || $kind=="retirement") {
	        if($s==0) {
	            $return_str = "是";
	        }
	        else {
	            $return_str = "否";
	        }
	    }
	    return $return_str;
	}

	public function class_record($id)
	{
		$this->data['list'] = $this->student_model->getClassRecord($id);
		$this->data['now']=date('Y/m/d H:i');
		$this->data['link_export']= base_url("data/student_manger/export/{$id}");
		$this->layout->view('data/student_manger/class_record', $this->data);
	}

	public function export($id)
    {
        $info=$this->student_model->getClassRecord($id);

        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=student_list.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $filename = date('Ymd').'.csv';
        echo iconv("UTF-8", "BIG5", "臺北市政府公務人員訓練處,");
        echo iconv("UTF-8", "BIG5", "單一學員上課紀錄查詢\r\n");
        echo iconv("UTF-8", "BIG5", "編號,");
        echo iconv("UTF-8", "BIG5", "身分證字號,");
        echo iconv("UTF-8", "BIG5", "姓名,");
        echo iconv("UTF-8", "BIG5", "年度/班期名稱/期別,");
        echo iconv("UTF-8", "BIG5", "職稱,");
        echo iconv("UTF-8", "BIG5", "報名單位,");
        echo iconv("UTF-8", "BIG5", "教室(課程表),");            
        echo iconv("UTF-8", "BIG5", "開課日期,\r\n");
        

        $k=1;
        for ($i=0;$i<count($info);$i++) {
            echo "\"".iconv("UTF-8", "BIG5", $k."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['id']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['pname']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['year']."年".$info[$i]['class_name']."(第".$info[$i]['term']."期)"."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['name']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['unit_name']."\",");
            echo "\"".iconv("UTF-8", "BIG5", $info[$i]['room_code']."\",");
            echo "\"".iconv("UTF-8", "BIG5", substr($info[$i]['start_date1'],0,10)."\"\r\n");
   
           $k++;
        }
            
    }

	private function _isVerify($action='add', $old_data=array())
	{
		$config = $this->student_model->getVerifyConfig();
		if ($action == 'edit') {
			$config['idno']['rules'] = '';
		}
		$this->form_validation->set_rules($config);
		$this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
		// $this->form_validation->set_message('required', '請勿空白');
		return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
	}

	public function batch_import()
	{

		$ero = 0;
		$echo_msg = '';
		///get file
		if(isset($_FILES["file"]) && $_FILES["file"]["name"] != '')
		{
			$echo_msg .= "<br><hr><br><b>上傳檔案預覽 【紅底：代表資料已存在或須修正；綠底：代表可匯入】</b><br><br>";

			if ($_FILES["file"]["error"] > 0)
			{
				$echo_msg .= "檔案上傳錯誤，請重新上傳！" . $_FILES["file"]["error"] . "<br>";
			}
			else
			{
				if (!fileExtensionCheck($_FILES['file']['name'], ['csv'])){
					$this->setAlert(3, '檔案格式錯誤');
					redirect(base_url("data/student_manger/batch_import"));                        
				}  				
				#echo "Upload: " . $_FILES["file"]["name"] . "<br>";
			  	#echo "Type: " . $_FILES["file"]["type"] . "<br>";
			  	#echo "Size: " . ($_FILES["file"]["size"] / 1024) . " kB<br>";
			  	#echo "Stored in: " . $_FILES["file"]["tmp_name"];
				//rename($_FILES["file"]["tmp_name"], $_FILES["file"]["tmp_name"].'.bak');
				$row = 0;
				$title = "";
				$table_body = "";
				$echo_msg .= "<center>";
				if (($handle = fopen($_FILES["file"]["tmp_name"], "r")) !== FALSE)
				{
					$table_body .= "<table id=itable name=itable width=100% border=1 style=border-collapse:collapse; borderColor=black >";
					$table_body .= "<th width=8%>狀態</th><th width=5%>姓名<br>(必填)</th><th width=8%>身分證字號<br>(必填)</th><th width=8%>性別<br>格式:M、F<br>(必填)</th><th width=8%>出生日期<br>(yyyy/mm/dd)<br>(必填)</th>";
					$table_body .= "<th width=8%>Email</th><th width=8%>公司Email<br>(必填)</th><th width=5%>局處名稱代碼<br>(必填)</th><th width=10%>外機關名稱全銜</th><th width=6% style='text-align:left'>學歷(必填)<br>20.國(初)以下<br>30.高中(職)<br>40.專科<br>50.大學<br>60.碩士<br>70.博士</th>";
					$table_body .= "<th width=8% style='text-align:left'>現職區分<br>11.其他<br>01.簡任主管<br>02.簡任非主管<br>03.荐任主管<br>04.荐任非主管<br>05.委任主管<br>06.委任非主管<br>07.警察消防主管<br>08.警察消防非主管<br>09.約聘僱人員<br>10.技工工友<br>(必填)</th><th>公司電話<br>(格式:02-12345678)<br>(必填)</th><th>公司傳真<br>(格式:02-12345678)</th><th>職稱<br>(必填)</th><th>手機號碼</th>";

				    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE)
				    {
				    	///查詢身分證是否存在
						$tmp_flag = 0;
						$status = 0;///status
				    	if($row>0)
						{
							$this->load->helper('idno');

							if(Idno::checkNumber($data[1]))///check idno
							{
								$conditions = array(
									'idno' => $data[1],
								);
								$id_exists = $this->student_model->getCount($conditions);
								$status = $id_exists;
							}
							else
							{
								$status = -1;///IDNO驗證錯誤
							}
						}
				        $num = count($data);
				        $row++;
				        for ($c=0,$tmp_str = ""; $c < $num; $c++)
				        {
				        	if($c==1&&$status!=0)
							{
								$tmp_str .= "<td><input type=input value=".$data[$c]." /></td>";
								if(false== $this->chkdata($c, $data[$c]))
								{

									$tmp_flag++;
									$echo_msg .= $c."<br>";
								}
							}
							else
							{
								if($row==1)///取第一個欄位
								{
									$title .= iconv('big5', 'UTF-8', $data[$c]);
								}
								else
								{
									$tmp_str .= "<td>".iconv('big5', 'UTF-8', $data[$c]) . "</td>";
									if($c!=1)
									{
										if(false== $this->chkdata($c, iconv('big5', 'UTF-8', $data[$c])))
										{
											$tmp_flag++;
											$ero++;
											if($c!='')
											{

											$echo_msg .= "<p style='text-align:left'>第".($row-1)."行,";
											switch ($c) {
												case 0:
											        $echo_msg .= "姓名(必填)";
											        break;
											    case 1:
											     	$echo_msg .= "身分證字號(必填)";
											        break;
											    case 2:
											  	  $echo_msg .= "性別(格式:M、F)(必填)";
											        break;
											    case 3:
											       $echo_msg .= "出生日期(格式:yyyy/mm/dd)(必填)";
											        break;
											    case 4:
											      $echo_msg .= "Email";
											        break;
											    case 5:
											  	  $echo_msg .= "公司Email(必填)";
											        break;
												case 6:
											        $echo_msg .= "局處名稱代碼(必填)";
											        break;
											    case 7:
											     	$echo_msg .= "外機關名稱(必填)";
											        break;
											    case 8:
											  	  $echo_msg .= "學歷（必填)";
											        break;
											    case 9:
											       $echo_msg .= "現職區分(必填)";
											        break;
											    case 10:
											      $echo_msg .= "公司電話(格式:02-12345678)(必填)";
											        break;
											    case 11:
											  	  $echo_msg .= "公司傳真(格式:02-12345678)(必填)";
											        break;
												case 12:
											  	  $echo_msg .= "職稱代碼(必填)";
											        break;
											    case 13:
											  	  $echo_msg .= "手機號碼";
											        break;
												}
												$echo_msg .= "欄位有誤.</p>";
											}
										}
									}
								}
							}
				        }

						if($row>1)
						{
							if($status!=0)
							{
								$table_body .=  "<tr bgcolor=#ffaad5 id=test><td><input type='checkbox' onclick='upd(this)'><font color=red>身分字號".($status==-1?"錯誤":"已存在")."</font></td>".$tmp_str;
							}
							elseif($tmp_flag>0)
							{

								$table_body .=  "<tr bgcolor=#ffaad5 id=test><td><font color=red>資料格式錯誤</font></td>".$tmp_str;
							}
							elseif($tmp_flag==0 && $status==0)
							{
								$table_body .=  "<tr bgcolor=#A6FFA6 id=test><td>可新增</td>".$tmp_str;
							}
						}
				    }
				    fclose($handle);
					$table_body .=  "</table>";
				}
				$table_body .= "<br></br><input type=button value='開始匯入' style='width:120px;height:40px;font-size:20px;' onclick=comfirmTo('$ero') />";

				$echo_msg .= $table_body;

			}
		}
		$echo_msg .= "</center>";
		$this->data['echo_msg'] = $echo_msg;

		$this->data['link_cancel'] = base_url("data/student_manger/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('data/student_manger/batch_import', $this->data);
	}

	public function userunicode($id)
	{
		$id = strtoupper($id);
		//建立字母分數陣列
		$headPoint = array(
			'A'=>1,'I'=>39,'O'=>48,'B'=>10,'C'=>19,'D'=>28,
			'E'=>37,'F'=>46,'G'=>55,'H'=>64,'J'=>73,'K'=>82,
			'L'=>2,'M'=>11,'N'=>20,'P'=>29,'Q'=>38,'R'=>47,
			'S'=>56,'T'=>65,'U'=>74,'V'=>83,'W'=>21,'X'=>3,
			'Y'=>12,'Z'=>30
		);
		//建立加權基數陣列
		$multiply = array(8,7,6,5,4,3,2,1);

		//檢查身份字格式是否正確
		if (preg_match("/^[a-zA-Z][1-2][0-9]+$/",$id) AND strlen($id) == 10)
		{
			//切開字串
			$len = strlen($id);
			for($i=0; $i<$len; $i++)
			{
				$stringArray[$i] = substr($id,$i,1);
			}
			//取得字母分數
			$total = $headPoint[array_shift($stringArray)];
			//取得比對碼
			$point = array_pop($stringArray);
			//取得數字分數
			$len = count($stringArray);
			for($j=0; $j<$len; $j++)
			{
				$total += $stringArray[$j]*$multiply[$j];
			}
			//計算餘數碼並比對
			$last = (($total%10) == 0 )?0:(10-($total%10));
			if ($last != $point)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		else
		{
		   return false;
		}
	}

	///檢查資料格式
	public function chkdata($row, $data="")
	{
		#echo "<br>".$row."-".$data.":";
		if(($row==4 || $row==7) && strlen($data)==0)
		{
			return true;
		}
		elseif($row==0)///姓名 非空~<15
		{
			if(strlen($data)==0 || strlen($data)>15)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
		elseif($row==2)
		{
			if(strtoupper($data)=="F" || strtoupper($data)=="M")
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		elseif($row==3)
		{
			/// yyyy/mm/dd
			if(preg_match("/^\d{4}\/\d{1,2}\/\d{1,2}$/", $data))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		elseif($row==4 || $row==5)
		{
			$emailRule = "/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z]+$/";
			if(strlen($data)==0 && $row==4)
				return true;
			if(preg_match($emailRule, $data))
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		elseif($row==6)///局處代碼 、外機關名稱、職稱代碼
		{
			if(strlen($data)>0 && strlen($data)<40)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		elseif($row==8)///學歷
		{
			switch($data)
			{
				case 20:
				case 30:
				case 40:
				case 50:
				case 60:
				case 70:
					return true; break;
			}
			return false;
		}
		elseif($row==9)///現職
		{
			//echo "現職:".$data."<br>";
			switch($data)
			{
				case 11:
				case 1:
				case 2:
				case 3:
				case 4:
				case 5:
				case 6:
				case 7:
				case 8:
				case 9:
				case 10:
					return true; break;
			}
			return false;
		}
		elseif($row==10 || $row==11 && strlen($data) > 0)///公司電話 公司傳真
		{
			if(strlen($data)==0 && $row==11)
				return true;
			$data = htmlspecialchars($data);
			/*if(preg_match("/^((/(/d{3}/))|(/d{3}/-))?(/(0/d{2,3}/)|0/d{2,3}-)?[1-9]/d{6,7}.[1-9]{1,3}$/", $data))
			{
				return true;
			}
			else
			{
				return false;
			}*/
			return true;
		}
		elseif(($row==12) && strlen($data)==0)
		{
			return false;
		}
		return true;
		//return false;
	}

	public function ajax($action)
	{
		$post = $this->input->post();

		$result = array(
			'status' => FALSE,
			'message' => '',
		);

		if ($action && $post) {

			switch ($action) {

				case 'import':

					$name = addslashes($post['name']); //姓名
					$idno = addslashes($post['idno']); //字號
					$idno = strtoupper($idno);
					$bday = addslashes($post['bday']); //生日
					$gender = addslashes($post['gender']); //性別
					$email = addslashes($post['email']); //信箱
					$gemail = addslashes($post['gemail']); //公司信箱
					$gname = addslashes($post['gname']); //局處名稱
					if(!empty($gname)){
						$bureau_name = $this->student_model->getBureau($gname);
					}					
					$goname = addslashes($post['goname']); //外機關名
					if($goname == ''){
						$goname = NULL;
					}
					$edu = addslashes($post['edu']); //學歷
					$pjob = addslashes($post['pjob']); //現職
					$gphone = addslashes($post['gphone']); //公司電話
					$gfax = addslashes($post['gfax']); //公司傳真
					$job = addslashes($post['job']); //職稱
					$cell_phone = addslashes($post['cell_phone']); //手機
					$is_id_number = addslashes($post['is_id_number']); //身份證字號是否可用
					$conditions = array(
						'name' => $job,
					);
					$job_title_data = $this->job_title_model->get($conditions);
					$job_title = $job_title_data['item_id'];
					if($job_title == ''){
						$job_title = 'WD00';
					}

					if(!empty($bday)){
						$b_date = new DateTime($bday);
						$bday = $b_date->format('Y-m-d');
					}

					if($is_id_number=='1') //insert
					{
					///main
						$fields = array(
							'username' => $idno,
							'password' => md5('123456'),
							'name' => $name,
							'enable' => '1',
							'job_title' => $job_title,
							'idno' => $idno,
							'gender' => $gender,
							'bureau_id' => $gname,
							'bureau_name' => $bureau_name,
							'email' => $email,
							'office_email' => $gemail,
							'office_tel' => $gphone,
							'co_empdb_poftel' => $gphone,
							'office_fax' => $gfax,
							'education' => $edu,
							'job_distinguish' => $pjob,
							'birthday' => $bday,
							'msg_reserved' => '0',
							'hid' => '0',
							'cellphone' => $cell_phone,
							'retirement' => '1',
							'showretirement' => '1',
							'departure' => '1',
							'out_gov_name' => $goname,
						);
						// jd($fields,1);
						$rs = $this->student_model->insert($fields);
						if($rs) {
							$fields = array(
								'id' => $idno,
								'ou_gov' => $goname,
							);
							$this->out_gov_model->insert($fields);

							$insert_date = new DateTime();
		                    $insert_date = $insert_date->format('Y-m-d H:i:s');
			                $fields = array(
			                	'username' => $idno,
		        				'group_id' => '5',
		        				'cre_user' => $this->flags->user['username'],
		        				'upd_user' => $this->flags->user['username'],
		        				'cre_date' => $insert_date,
		        				'upd_date' => $insert_date,
			                );

			                $this->account_role_model->insert($fields);
							$result['message'] = $name.":".$idno." 新增成功";
						}
						else {
							$result['message'] = $name.":".$idno." 新增失敗";
						}
					}
					elseif($is_id_number=='2') { //update

						$fields = array(
							'username' => $idno,
							'name' => $name,
							'job_title' => $job_title,
							'gender' => $gender,
							'bureau_id' => $gname,
							'bureau_name' => $bureau_name,
							'email' => $email,
							'office_email' => $gemail,
							'office_tel' => $gphone,
							'co_empdb_poftel' => $gphone,
							'office_fax' => $gfax,
							'education' => $edu,
							'co_position' => $pjob,
							'birthday' => $bday,
							'cellphone' => $cell_phone,
							'job_distinguish' => $pjob,
							'out_gov_name' => $goname,
						);
						// jd($fields,1);
						$conditions = array(
							'idno' => $idno,
						);
						$rs = $this->student_model->update($conditions, $fields);

						if($rs) {
							$conditions = array(
								'id' => $idno,
							);
							$out_count = $this->out_gov_model->getCount($conditions);

							if($out_count > 0){
								$fields = array(
									'ou_gov' => $goname,
								);
								if(!empty($idno)){
									$this->out_gov_model->update($idno, $fields);
								}

							}else{
								$fields = array(
									'id' => $idno,
									'ou_gov' => $goname,
								);
								$this->out_gov_model->insert($fields);
							}

							$result['message'] = $name.":".$idno." 更新成功";
						}
						else {
							$result['message'] = $name.":".$idno." 更新失敗";
						}
					}
					else {
						$result['message'] = $name.":".$idno." 新增失敗,可能是身份字號已經存在或格式錯誤.";
					}


					break;
			}
		}
		echo json_encode($result);
	}

	private function check_ip_permiision(){
		# Extra IP Rule
		//ip清單
		$allowIP = array('211.79.136.201','211.79.136.202','211.79.136.203','211.79.136.204','211.79.136.205','211.79.136.206','163.29.39.6');
		$extraRule = false;

		if(getenv('HTTP_X_FORWARDED_FOR')){
		    $x_ip = split(',', getenv('HTTP_X_FORWARDED_FOR'));
		    $ip = $x_ip[0];
		} else {
		    $ip = getenv('REMOTE_ADDR');
		}
		
	    if (in_array($ip,$allowIP)) {
	        $extraRule = true;
	    }
		
		//跳開JJ帳號
		if ($this->flags->user['username']=='vitasy') {
			$extraRule = true;
		}

		return $extraRule;
	}

	public function ajax_toggle($field)
	{
		$result = array(
			'status' => FALSE,
			'message' => '',
		);

		if ($post = $this->input->post()) {
			$rs = $this->student_model->update($post['pk'], array($field=>$post['value']));
			if ($rs) {
				$result['status'] = TRUE;
			}
		}

		echo json_encode($result);
	}
	// old
	public function convertStrType($str) {
		$dbc = array( 
		'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' , 
		'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' , 
		'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' , 
		'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' , 
		'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' , 
		'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' , 
		'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' , 
		'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' , 
		'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' , 
		'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' , 
		'ｙ' , 'ｚ'
		);

		$sbc = array( //半形
		'A', 'B', 'C', 'D', 'E', 
		'F', 'G', 'H', 'I', 'J', 
		'K', 'L', 'M', 'N', 'O', 
		'P', 'Q', 'R', 'S', 'T', 
		'U', 'V', 'W', 'X', 'Y', 
		'Z', 'a', 'b', 'c', 'd', 
		'e', 'f', 'g', 'h', 'i', 
		'j', 'k', 'l', 'm', 'n', 
		'o', 'p', 'q', 'r', 's', 
		't', 'u', 'v', 'w', 'x', 
		'y', 'z'
		);

		return str_replace( $dbc, $sbc, $str ); //全形到半形
	}
	// 全形改半形 + 去空白
	public function make_semiangle($str){
		$arr = array(   '０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
		                '５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
		                'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
		                'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
		                'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
		                'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
		                'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
		                'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
		                'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
		                'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
		                'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
		                'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
		                'ｙ' => 'y', 'ｚ' => 'z', '（' => '(', '）' => ')', '〔'  => '[',
		                '〕'  => ']', '【'  => '[', '】'  => ']', '〖' => '[', '〗' => ']',
		                '“'  => '[', '”'  => ']', '‘'  => '[', '\'' => ']', '｛' => '{',
		                '｝' => '}', '《'  => '<', '》'  => '>', '％' => '%', '＋' => ' ',
		                '—'  => '-', '－' => '-', '～' => '-', '：' => ':', '。' => '.',
		                '、'  => ',', '，' => '.', '、' => '.', '；' => ',', '？' => '?',
		                '！' => '!', '…'  => '-', '‖'  => '|', '”' => '"', '‵' => '`',
		                '‘'  => '`', '｜' => '|', '〃' => '"','　' => '' ,' '=>'');
		return strtr($str, $arr);
	}

	public function download_resign_excel()
	{
		$this->student_model->resign_excel();
	}

	public function download_incumbency_excel($date)
	{

		$fix_date = substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2)." 00:00:00";

		$dbConn = new PDO("sqlsrv:server=210.69.61.110,1433;Database=TCGHR_DB","dcsdtcghr","dcsd1202");
		$dbConn->setAttribute(PDO::SQLSRV_ATTR_ENCODING, PDO::SQLSRV_ENCODING_UTF8);
		//$sql_list = sprintf("SELECT * FROM TCGBT02M WHERE INSDATE = '%s'",$fix_date);	//正式
		$sql_list = sprintf("SELECT * FROM TCGBT02M_ORG_TIT WHERE INSDATE = '%s'",$fix_date); //有含職稱incumbency_excel2 要用
		//$sql_list = sprintf("SELECT top 1 a.*, b.CODE_NAME, c.OA1ORGN FROM TCGBT02M a left join Basic_Code b on a.B02TITCOD = b.CODE_CODE left join TCGOA01M c on a.B02SORCOD = c.OA1ORG WHERE a.INSDATE = '%s'",$fix_date);

		$stmt = $dbConn->prepare($sql_list);
		$stmt->execute();
		//$row_insdate = $stmt->fetch(PDO::FETCH_ASSOC);
		$row['list'] = $stmt->fetchAll();
		$row['date'] = $fix_date;


		/*
		$test_count = 0;
		foreach ($row_list as $key => $value) {
			if($key > 76000){
				$test_count++;
				echo "Test".$test_count."<BR>";
			}
		}
		*/

		//var_dump(count($row_list));

		$this->student_model->incumbency_excel2($row);
	}

}
