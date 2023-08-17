<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_match extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('student/student_match_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['detail_page'])) {
            $this->data['filter']['detail_page'] = '1';
        }

        if (!isset($this->data['filter']['class_name'])) {
            $this->data['filter']['class_name'] = '';
        }
        if (!isset($this->data['filter']['class_no'])) {
            $this->data['filter']['class_no'] = '';
        }
        if (!isset($this->data['filter']['ck'])) {
            $this->data['filter']['ck'] = '';
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
        $conditions['require.year'] = $this->data['filter']['year'];
        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['class_name'] !== '' ) {
            $attrs['class_name'] = $this->data['filter']['class_name'];
        }
        if ($this->data['filter']['class_no'] !== '' ) {
            $attrs['class_no'] = $this->data['filter']['class_no'];
        }

        $DEF_MANAGE_ID = '1';// 系統－總管理者
        $DEF_HR_ID = '6';// 局處－人事
        $DEF_WORKER_ID = '8';// 教務組－承辦人

        if (in_array($DEF_MANAGE_ID, $this->flags->user['group_id'])) {
            $attrs['where_special'] = 'require.class_status in (2, 3)';
            $this->data['filter']['total'] = $total = $this->student_match_model->get_MANAGE_ListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs['rows'] = $rows;
            $attrs['offset'] = $offset;

            $this->data['list'] = $this->student_match_model->get_MANAGE_List($attrs);
            $mode = 'MANAGE';
        }else if(in_array($DEF_HR_ID, $this->flags->user['group_id'])){
            $beaurauid = $this->flags->user['bureau_id'];
            $attrs['where_special'] = "require.class_status in (2, 3) and oa.beaurau_id='{$beaurauid}' and oa.yn_sel in ('3','8')";
            $this->data['filter']['total'] = $total = $this->student_match_model->get_HR_ListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs['rows'] = $rows;
            $attrs['offset'] = $offset;

            $this->data['list'] = $this->student_match_model->get_HR_List($attrs);
            $mode = 'HR';
        }else if(in_array($DEF_WORKER_ID, $this->flags->user['group_id'])){
            $personal_id = $this->flags->user['idno'];
            $attrs['where_special'] = "require.class_status in (2, 3) and require.worker='{$personal_id}' ";
            $this->data['filter']['total'] = $total = $this->student_match_model->get_WORKER_ListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs['rows'] = $rows;
            $attrs['offset'] = $offset;

            $this->data['list'] = $this->student_match_model->get_WORKER_List($attrs);
            $mode = 'WORKER';
        }else{
            $currentId = $this->flags->user['idno'];
            $attrs['where_special'] = "require.class_status in (2, 3) and oa.yn_sel in ('3','8') and oa.id='{$currentId}' ";
            $this->data['filter']['total'] = $total = $this->student_match_model->get_STUDENT_ListCount($attrs);
            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs['rows'] = $rows;
            $attrs['offset'] = $offset;

            $this->data['list'] = $this->student_match_model->get_STUDENT_List($attrs);
            $mode = 'STUDENT';
        }

        unset($attrs['rows']);
        unset($attrs['offset']);

        // 系統－總管理者  局處－人事  客服中心－承辦人  客服中心－話務人員
        if ((in_array('1', $this->flags->user['group_id'])) || (in_array('6', $this->flags->user['group_id'])) || (in_array('15', $this->flags->user['group_id']))|| (in_array('17', $this->flags->user['group_id'])))
        {
            $attrs['where_special'] = 'require.class_status in (2, 3)';
            if($this->data['filter']['ck'] != ''){
                $attrs['where_special'] .= " and vaa.company LIKE '%{$this->data['filter']['ck']}%'";
            }

            $this->data['filter']['total'] = $total = $this->student_match_model->get_MANAGE_ListCount($attrs);

            $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

            $attrs['rows'] = $rows;
            $attrs['offset'] = $offset;

            $this->data['list'] = $this->student_match_model->get_MANAGE_List($attrs);
            $mode = 'MANAGE';
        }

        foreach ($this->data['list'] as & $row) {
            $row['url'] = base_url("student/student_match/detail/{$row['seq_no']}");
        }

        $this->data['page_data']['mode'] = $mode;

        $this->load->library('pagination');
        $config['base_url'] = base_url("student/student_match?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_refresh'] = base_url("student/student_match/");
        $this->layout->view('student/student_match/list',$this->data);
    }
    public function detail($seq_no)
    {

        $require_data = $this->student_match_model->get($seq_no);

        if(!isset($require_data)){
            $this->setAlert(3, '操作錯誤');
            redirect(base_url('management/print_student_list/'));
        }
        $msg = '';
        $this->data['require_data'] = $require_data;
        $DEF_MANAGE_ID = '1';// 系統－總管理者
        $DEF_HR_ID = '6';// 局處－人事
        $DEF_WORKER_ID = '8';// 教務組－承辦人
        $currentUsername = $this->flags->user['username'];
        if($post = $this->input->post()){
            // jd($post,1);
        	if(isset($this->data['filter']['insert'])){
        		$yearNew = 0;
				$termNew = 0;
				$s = explode("|", $post['change_term']);
				if(count($s)==2) {
					$yearNew = $s[0];
					$termNew = $s[1];
				}
				if (empty($post['id'])) {
					$msg = '請選擇人員';
				} else if (empty($post['contact'])) {
					$msg = '請輸入聯絡資訊';
				} //else if ($post['term']===$post['change_term']) {
				else if ($require_data['term']===$termNew && $require_data['year']===$yearNew) {
					$msg = '請選擇不同的擬換期別';
				} else {
					$id = $post['id'];
					$year = $require_data['year'];
					$term = $require_data['term'];
					//$change_term = $post['change_term'];
					$change_term = $termNew;
					$change_year = $yearNew;
					$classNo = $require_data['class_no'];
					$contact = $post['contact'];
					$fields = array(
						'year' => $year,
						'class_no' => $classNo,
						'term' => $term,
						'change_term' => $change_term,
						'change_year' => $change_year,
						'id' => $id,
						'contact' => $contact,
						'cre_user' => $currentUsername,
					);
                    $conditions = array(
                        'year' => $year,
                        'class_no' => $classNo,
                        'term' => $term,
                        'change_term' => $change_term,
                        'id' => $id,
                    );
                    $match_data = $this->student_match_model->get_match($conditions);
                    // jd($fields,1);
                    if($match_data){
                        $msg = '擬換班期重複';
                    }else{
                        $result = $this->student_match_model->student_match_insert($fields);
                        $msg = '新增完成';
                    }

				}
        	}

        	if(isset($this->data['filter']['update'])){

        		$id = $post['old_id'];
				$term = $post['old_term'];
				$change_term = $post['old_change_term'];
				$year = $post['old_year'];
				$classNo = $post['old_class_no'];
				$yearNew = 0;
				$termNew = 0;
				$s = explode("|", $post['change_term']);
				if(count($s)==2) {
					$yearNew = $s[0];
					$termNew = $s[1];
				}
				//驗證權限
				$conditions = array(
					'year' => $year,
					'term' => $term,
					'change_term' => $termNew,
					'id' => $id,
					'class_no' => $classNo,
					'cre_user' => $currentUsername,
					'change_year' => $yearNew,
				);
				$student_match_count = $this->student_match_model->student_match_count($conditions);

				if ($student_match_count!=='1' && !in_array($DEF_MANAGE_ID, $this->flags->user['group_id']) && !in_array($DEF_WORKER_ID, $this->flags->user['group_id'])) {
					die('權限不足');
				} else if (empty($post['new_contact'])) {
					$msg = '請輸入聯絡資訊';
				} //else if ($post['old_term']===$post['change_term']) {
				else if ($require_data['term']===$termNew && $require_data['year']===$yearNew) {
					$msg = '請選擇不同的擬換期別';
				} else {
					$new_change_term = $termNew;
					$conditions = array(
						'year' => $year,
						'term' => $term,
						'change_term' => $change_term,
						'id' => $id,
						'class_no' => $classNo,
					);

					$fields = array(
						'contact' => $post['new_contact'],
						'change_term' => $new_change_term,
						'change_year' => $yearNew,
					);

					$result = $this->student_match_model->student_match_update($conditions, $fields);

					if ($result) {
						$msg='修改完成';
					}
				}
        	}

        }

        if(isset($this->data['filter']['delete'])){

    		$array = explode('@', $_REQUEST['delete']);
			if (count($array)!==5) {
				die('參數錯誤');
			}
			$id = $array[0];
			$term = $array[1];
			$change_term = $array[2];
			$year = $array[3];
			$classNo = $array[4];
			//驗證權限

			$conditions = array(
				'year' => $year,
				'term' => $term,
				'change_term' => $change_term,
				'id' => $id,
				'class_no' => $classNo,
				'cre_user' => $currentUsername,
			);
			$student_match_count = $this->student_match_model->student_match_count($conditions);

			if ($student_match_count!=='1' && !in_array($DEF_MANAGE_ID, $this->flags->user['group_id']) && !in_array($DEF_WORKER_ID, $this->flags->user['group_id'])) {
				die('權限不足');
			} else {
				$conditions = array(
					'year' => $year,
					'term' => $term,
					'change_term' => $change_term,
					'id' => $id,
					'class_no' => $classNo,
				);

				$result = $this->student_match_model->student_match_delete($conditions);
				if ($result) {
					$msg='刪除完成';
				}
			}

    	}

		$where_special = "r.class_no = '{$require_data['class_no']}' and CONCAT( IFNULL(sd_edate,'2012/01/01'), ' ' , IFNULL(sd_edate_h_m,'23:59') ) > now() ";
		$this->data['changeTermInfos'] = $this->student_match_model->get_changeTermInfos($where_special);
		$this->data['changeTermInfosCount'] = count($this->data['changeTermInfos']);

		if (in_array($DEF_MANAGE_ID, $this->flags->user['group_id'])) {
			$where_special = '';
            $where_special = "year={$require_data['year']} and class_no='{$require_data['class_no']}' and term={$require_data['term']} and yn_sel in (3, 8)";
            $this->data['userInfos'] = $this->student_match_model->get_change_user($where_special);

            $mode = 'MANAGE';
        }else if(in_array($DEF_HR_ID, $this->flags->user['group_id'])){
            $beaurauid = $this->flags->user['bureau_id'];
            $where_special = '';
            $where_special = "year={$require_data['year']} and class_no='{$require_data['class_no']}' and term={$require_data['term']} and beaurau_id='{$beaurauid}' and yn_sel in (3, 8)";
            $this->data['userInfos'] = $this->student_match_model->get_change_user($where_special);

            $mode = 'HR';
        }else if(in_array($DEF_WORKER_ID, $this->flags->user['group_id'])){
            $personal_id = $this->flags->user['idno'];
            $where_special = '';
            $where_special = "year={$require_data['year']} and class_no='{$require_data['class_no']}' and term={$require_data['term']} and beaurau_id='{$beaurauid}' and yn_sel in (3, 8)";
            $this->data['userInfos'] = $this->student_match_model->get_change_user($where_special);

            $mode = 'WORKER';
        }else{
            $currentId = $this->flags->user['idno'];
            $this->data['userInfos'] = $this->student_match_model->get_user($currentId);

            $mode = 'STUDENT';
        }

        $page = $this->data['filter']['detail_page'];
        $rows = $this->data['filter']['rows'];

        $attrs = array();
        $attrs['class_no'] = $require_data['class_no'];

        $this->data['filter']['total'] = $total = $this->student_match_model->get_change_ListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;


        $this->data['list'] = $this->student_match_model->get_change_List($attrs);

        $this->load->library('pagination');
        $config['base_url'] = base_url("management/student_match/detail?". $this->getQueryString(array(), array('detail_page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['page_data']['mode'] = $mode;
        $this->data['page_data']['msg'] = $msg;

        $this->data['selfFilename'] = base_url("student/student_match/detail/{$require_data['seq_no']}");
        $this->layout->view('student/student_match/detail',$this->data);
    }
}
