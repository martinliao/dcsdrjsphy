<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setclass extends MY_Controller
{
    public function __construct()
	{
		parent::__construct();
		$this->load->model('planning/set_startdate_model');
		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}

		$this->load->model('planning/setclass_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
		}
		if (!isset($this->data['filter']['query_class_no'])) {
            $this->data['filter']['query_class_no'] = '';
        }
        if (!isset($this->data['filter']['query_class_name'])) {
            $this->data['filter']['query_class_name'] = '';
		}
		if (!isset($this->data['filter']['query_type'])) {
            $this->data['filter']['query_type'] = '';
        }
        if (!isset($this->data['filter']['query_second'])) {
            $this->data['filter']['query_second'] = '';
        }
	}

	public function index()
	{
		$this->data['page_name'] = 'list';
		$this->data['user_bureau'] = $this->flags->user['bureau_id'];
		$this->data['choices']['query_type'] = $this->getSeriesCategory();
        $this->data['choices']['query_type'][''] = '請選擇系列別';
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();
		if ($this->data['filter']['query_class_no'] !== '' ) {
            $conditions['class_no'] = $this->data['filter']['query_class_no'];
		}
		if ($this->data['filter']['query_type'] !== '' ) {
            $conditions['type'] = $this->data['filter']['query_type'];
            $this->data['choices']['query_second'] = $this->set_startdate_model->getSecondCategory($this->data['filter']['query_type']);
        }

        if ($this->data['filter']['query_second'] !== '' ) {
            $conditions['beaurau_id'] = $this->data['filter']['query_second'];
        }
		$attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
		if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
        $this->data['filter']['total'] = $total = $this->setclass_model->getListCount($attrs,$this->data['user_bureau']);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }
        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }
		if ($this->data['filter']['query_class_name'] !== '' ) {
            $attrs['query_class_name'] = $this->data['filter']['query_class_name'];
        }
		$this->data['list'] = $this->setclass_model->getList($attrs,$this->data['user_bureau']);
		$this->data['current_year'] = date('Y')-1911;
		$this->data['next_year'] = date('Y')-1910;
		foreach ($this->data['list'] as & $row) {
			$check = $this->setclass_model->checkExist($row['class_no']);
			if(!empty($check)){
				for($i=0;$i<count($check);$i++){
					if($check[$i]['year'] == $this->data['current_year']){
						$row['current_exist'] = true;
					} else {
						$row['link_current'] = base_url("planning/createclass/add/?id={$row['seq_no']}&year={$this->data['current_year']}");
					}
					if($check[$i]['year'] == $this->data['next_year']){
						$row['next_exist'] = true;
					} else {
						$row['link_next'] = base_url("planning/createclass/add/?id={$row['seq_no']}&year={$this->data['next_year']}");
					}
				}
			} else {
				$row['link_current'] = base_url("planning/createclass/add/?id={$row['seq_no']}&year={$this->data['current_year']}");
				$row['link_next'] = base_url("planning/createclass/add/?id={$row['seq_no']}&year={$this->data['next_year']}");
			}
		}
		$this->load->library('pagination');
        $config['base_url'] = base_url("planning/setclass?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
		$this->data['link_get_second_category'] = base_url("planning/set_startdate/getSecondCategory");
		$this->data['link_import'] = base_url("planning/setclass/add/");
		$this->data['link_refresh'] = base_url("planning/setclass/");

		$this->layout->view('planning/setclass/list', $this->data);
	}

	public function add()
	{	
		$this->data['user_bureau'] = $this->flags->user['bureau_id'];
		$from=$this->input->get('from');

		if(isset($_FILES['myfile']['name'])){
            $current_year = date('Y')-1911;

			if(basename($_FILES['myfile']['name']) == 'require_dev_class_samp.csv' && $this->data['user_bureau'] == '379680000A'){
				$uploaddir = DIR_UPLOAD_FILES;
				$uploadfile = $uploaddir.basename($_FILES['myfile']['name']);
				$uploadfile = iconv("utf-8", "big5", $uploadfile);

				if (move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile)) {    
					$fp = fopen ($uploadfile,"r") or die("無法開啟");
					$data = $this->setclass_model->getFormDefault();
					$row = 0;
					$success = 0;
					$fail = 0;
					while(!feof($fp)){
						$content = fgets($fp);
						$content = mb_convert_encoding($content, 'UTF-8', 'BIG5');
						$fields = explode(",",$content);
				
						if($row == '1' && count($fields) == 48 && !empty($fields[0]) && !empty($fields[2]) && !empty($fields[3])){
                            if(intval($fields[0]) < $current_year){
                                $fail++;
                            }else {
							    $data['year'] =  intval($fields[0]);
							    $data['class_no'] = $this->setclass_model->createClassNo(trim($fields[3]));
							    $data['term'] = intval($fields[1]);
							    $data['class_name'] = trim($fields[2]);
							    $data['type'] = trim($fields[3]);
							    $data['ht_class_type'] = trim($fields[5]);
							    $data['no_persons'] = intval($fields[6]);
							    $data['classify'] = trim($fields[7]);
							    $data['class_cate'] = trim($fields[8]);
							    $data['range'] = intval($fields[9]);
							    $data['yn_continues'] = trim($fields[10]);
							    $data['isappsameclass'] = trim($fields[11]);
							    $data['req_beaurau'] = trim($fields[47]);
							    $data['contactor'] = trim($fields[13]);
							    $data['tel'] = trim($fields[14]);
							    $data['room_code'] = trim($fields[15]);
							    $data['app_type'] = trim($fields[16]);
							    $data['start_date1'] = $fields[17];
							    $data['end_date1'] = $fields[18];
							    $data['start_date2'] = $fields[19];
							    $data['end_date2'] = $fields[20];
							    $data['start_date3'] = $fields[21];
							    $data['end_date3'] = $fields[22];
							    $data['way1'] = trim($fields[23]);
							    $data['way2'] = trim($fields[24]);	
							    $data['way3'] = trim($fields[25]);
							    $data['way4'] = trim($fields[26]);
							    $data['way5'] = trim($fields[27]);
							    $data['way6'] = trim($fields[28]);
							    $data['way7'] = trim($fields[29]);
							    $data['way8'] = trim($fields[30]);
							    $data['way9'] = trim($fields[31]);
							    $data['way10'] = trim($fields[32]);
							    $data['way11'] = trim($fields[33]);
							    $data['way12'] = trim($fields[34]);
							    $data['way13'] = trim($fields[35]);
							    $data['way14'] = trim($fields[36]);
							    $data['way15'] = trim($fields[37]);
							    $data['way16'] = trim($fields[38]);
							    $data['way17'] = trim($fields[39]); 
							    $data['obj'] = trim($fields[40]);
							    $data['content'] = trim($fields[41]);
							    $data['respondant'] = trim($fields[42]);
							    $data['class_name_shot'] = trim($fields[43]);
							    $data['min_no_persons'] = intval($fields[44]);
							    $data['class_status'] = '1';
							    $data['dev_type'] = trim($fields[12]);
							    $data['beaurau_id'] = trim($fields[4]);
							    $data['contactor_email'] = trim($fields[46]);

							    if(intval(date('Y', strtotime($data['start_date1']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['end_date1']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['start_date2']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['end_date2']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['start_date3']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['end_date3']))) != ($data['year']+1911)){

                                    $fail++;
                                }
                                else {
                                    $check_repeat = $this->setclass_model->checkRepeat($data['year'],$data['class_no'],$data['term']);

							        if($check_repeat){
								        $fail++;
							        } else {
								        $saved_id = $this->setclass_model->_insert($data);
								        if ($saved_id) {
									        $success++;
								        } else {
									        $fail++;
								        }
							        }
                                }
                            }
						}
						$row = 1;
					}

					if(!empty($from)){
						$this->setAlert(1, '資料匯入完成<br>'.'成功:'.$success.'筆<br>'.'失敗'.$fail.'筆');
						redirect(base_url('planning/createclass'));
					}

					$this->setAlert(1, '資料匯入完成<br>'.'成功:'.$success.'筆<br>'.'失敗'.$fail.'筆');
					redirect(base_url('planning/setclass'));
				}
			} else if(basename($_FILES['myfile']['name']) == 'require_class_samp.csv' && $this->data['user_bureau'] != '379680000A'){
				$uploaddir = DIR_UPLOAD_FILES;
				$uploadfile = $uploaddir.basename($_FILES['myfile']['name']);
				$uploadfile = iconv("utf-8", "big5", $uploadfile);

				if (move_uploaded_file($_FILES['myfile']['tmp_name'], $uploadfile)) {    
					$fp = fopen ($uploadfile,"r") or die("無法開啟");
					$data = $this->setclass_model->getOrganFormDefault();
					$row = 0;
					$success = 0;
					$fail = 0;
					while(!feof($fp)){
						$content = fgets($fp);
						$content = mb_convert_encoding($content, 'UTF-8', 'BIG5');
						$fields = explode(",",$content);

						if($row == '1' && count($fields) == 45 && !empty($fields[0]) && !empty($fields[2])){
                            if(intval($fields[0]) != ($current_year+1)){
                                $fail++;
                            }else {
							    $data['year'] =  intval($fields[0]);
							    $data['class_no'] = $this->setclass_model->createClassNo('A');
							    $data['term'] = intval($fields[1]);
							    $data['class_name'] = trim($fields[2]);
							    $data['ht_class_type'] = trim($fields[3]);
							    $data['no_persons'] = intval($fields[4]);
							    $data['classify'] = trim($fields[5]);
							    $data['class_cate'] = trim($fields[6]);
							    $data['range'] = intval($fields[7]);
							    $data['yn_continues'] = trim($fields[8]);
							    $data['isappsameclass'] = trim($fields[9]);
							    $data['req_beaurau'] = trim($fields[44]);
							    $data['contactor'] = trim($fields[10]);
							    $data['tel'] = trim($fields[11]);
							    $data['room_code'] = trim($fields[12]);
							    $data['app_type'] = trim($fields[13]);
							    $data['start_date1'] = $fields[14];
							    $data['end_date1'] = $fields[15];
							    $data['start_date2'] = $fields[16];
							    $data['end_date2'] = $fields[17];
							    $data['start_date3'] = $fields[18];
							    $data['end_date3'] = $fields[19];
							    $data['way1'] = trim($fields[20]);
							    $data['way2'] = trim($fields[21]);	
							    $data['way3'] = trim($fields[22]);
							    $data['way4'] = trim($fields[23]);
							    $data['way5'] = trim($fields[24]);
							    $data['way6'] = trim($fields[25]);
							    $data['way7'] = trim($fields[26]);
							    $data['way8'] = trim($fields[27]);
							    $data['way9'] = trim($fields[28]);
							    $data['way10'] = trim($fields[29]);
							    $data['way11'] = trim($fields[30]);
							    $data['way12'] = trim($fields[31]);
							    $data['way13'] = trim($fields[32]);
							    $data['way14'] = trim($fields[33]);
							    $data['way15'] = trim($fields[34]);
							    $data['way16'] = trim($fields[35]);
							    $data['way17'] = trim($fields[36]); 
							    $data['obj'] = trim($fields[37]);
							    $data['content'] = trim($fields[38]);
							    $data['respondant'] = trim($fields[39]);
							    $data['class_name_shot'] = trim($fields[40]);
							    $data['min_no_persons'] = intval($fields[41]);
							    $data['class_status'] = '1';
							    // $data['beaurau_id'] = $this->setclass_model->getUserSuperBureauId($this->data['user_bureau']);
							    $data['beaurau_id'] = $this->data['user_bureau'];
							    $data['contactor_email'] = trim($fields[43]);
                                
                                if(intval(date('Y', strtotime($data['start_date1']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['end_date1']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['start_date2']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['end_date2']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['start_date3']))) != ($data['year']+1911)
                                    || intval(date('Y', strtotime($data['end_date3']))) != ($data['year']+1911)){

                                    $fail++;
                                }
                                else {
							        $check_repeat = $this->setclass_model->checkRepeat($data['year'],$data['class_no'],$data['term']);

							        if($check_repeat){
								        $fail++;
							        } else {
								        $saved_id = $this->setclass_model->_insert($data);
								        if ($saved_id) {
									        $success++;
								        } else {
									        $fail++;
								        }
							        }
                                }
                            }
						}
						$row = 1;
					}
					if(!empty($from)){
						$this->setAlert(1, '資料匯入完成<br>'.'成功:'.$success.'筆<br>'.'失敗'.$fail.'筆');
						redirect(base_url('planning/createclass'));
					}

					$this->setAlert(1, '資料匯入完成<br>'.'成功:'.$success.'筆<br>'.'失敗'.$fail.'筆');
					redirect(base_url('planning/setclass'));
				}
			}
		}

		$this->data['link_cancel'] = base_url("planning/setclass/?{$_SERVER['QUERY_STRING']}");
		$this->layout->view('planning/setclass/import',  $this->data);
	}

}
