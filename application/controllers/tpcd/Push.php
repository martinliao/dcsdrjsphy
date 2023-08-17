<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Push extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();

		if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
		
		$this->load->model('tpcd/tpcd_model');
		$this->load->model('tpcd/course_push_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['q'])) {
            $this->data['filter']['q'] = '';
        }
	}

	public function index()
	{
		$this->data['list'] = $this->course_push_model->getSetupData();
		$this->data['link_log'] = base_url("tpcd/push/log");
		$this->layout->view('tpcd/push', $this->data);
	}

	public function send()
	{
		$post_data = $this->input->post();

		$setup = $this->course_push_model->getSetupData();

		if(!empty($setup) && $setup[0]['limit'] > 0){
			$check_limit = $this->tpcd_model->checkLimit($setup[0]['limit'], $this->flags->user['idno']);

			if(!$check_limit){
				$url = base_url("tpcd/push");
				echo '<script>';
				echo 'alert("推播次數已達本月上限");';
				echo 'location.href ="'.$url.'";';
				echo '</script>';

				exit;
			}
		}
		
		if(!empty($post_data)){
			if($post_data['type'] == '1'){
				$send_list = $this->tpcd_model->getLocalUserList();

				if(count($send_list) > 0 && ((!empty($post_data['notification_title']) && !empty($post_data['notification_context'])) || (!empty($post_data['message_title']) && !empty($post_data['message_content'])))){
					$data = array();
					$k = 1;
					for($i=0;$i<count($send_list);$i++){
						$post_data['message_content'] = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', trim($post_data['message_content']));

						$data[$k][] = 'df55a063-1f19-4461-9844-e12eea2943421880b57f-65e3-40c5-a04b-73e6aeec6ac0';
						$data[$k][] = strtoupper($send_list[$i]['idno']);
						$data[$k][] = trim($post_data['notification_title']);
						$data[$k][] = trim($post_data['notification_context']);
						$data[$k][] = trim($post_data['message_title']);
						$data[$k][] = $post_data['message_content'];
						$k++;
						if(count($data) == 5000){
							$filename = '/var/www/html/base/admin/tpcd/'.$this->flags->user['username'].time().'.csv';
							$check = $this->creatCsvFile($data, $filename);

							if($check){
								$scheduleTime = date('Y/m/d H:i:s');
								$messageSchedule_id = $this->uploadApi($scheduleTime, $filename);
								$this->removeFile($filename);

								if($messageSchedule_id > 0){
									$this->insertLog($post_data, $messageSchedule_id, $scheduleTime, $this->flags->user['idno'], $this->flags->user['name']);
								}
							}

							$data = array();
							$k = 1;
						}
					}

					if(count($data) > 0){
						$filename = '/var/www/html/base/admin/tpcd/'.$this->flags->user['username'].time().'.csv';
						$check = $this->creatCsvFile($data, $filename);

						if($check){
							$scheduleTime = date('Y/m/d H:i:s');
							$messageSchedule_id = $this->uploadApi($scheduleTime, $filename);
							$this->removeFile($filename);
							
							if($messageSchedule_id > 0){
								$this->insertLog($post_data, $messageSchedule_id, $scheduleTime, $this->flags->user['idno'], $this->flags->user['name']);
							}
						}
					}
				}
			} else if($post_data['type'] == '2' && !empty($post_data['send_list'])){
				$send_list = explode(',',$post_data['send_list']);
			
				if(count($send_list) > 0 && ((!empty($post_data['notification_title']) && !empty($post_data['notification_context'])) || (!empty($post_data['message_title']) && !empty($post_data['message_content'])))){
					$data = array();
					$k = 1;
					for($i=0;$i<count($send_list);$i++){
						$post_data['message_content'] = str_replace(array("\r", "\n", "\r\n", "\n\r"), '', trim($post_data['message_content']));
						
						$data[$k][] = 'df55a063-1f19-4461-9844-e12eea2943421880b57f-65e3-40c5-a04b-73e6aeec6ac0';
						$data[$k][] = strtoupper($send_list[$i]);
						$data[$k][] = trim($post_data['notification_title']);
						$data[$k][] = trim($post_data['notification_context']);
						$data[$k][] = trim($post_data['message_title']);
						$data[$k][] = $post_data['message_content'];
						
						$k++;
						if(count($data) == 5000){
							$filename = '/var/www/html/base/admin/tpcd/'.$this->flags->user['username'].time().'.csv';
							$check = $this->creatCsvFile($data, $filename);

							if($check){
								$scheduleTime = date('Y/m/d H:i:s');
								$messageSchedule_id = $this->uploadApi($scheduleTime, $filename);
								$this->removeFile($filename);

								if($messageSchedule_id > 0){
									$this->insertLog($post_data, $messageSchedule_id, $scheduleTime, $this->flags->user['idno'], $this->flags->user['name']);
								}
							}

							$data = array();
							$k = 1;
						}
					}

					if(count($data) > 0){
						$filename = '/var/www/html/base/admin/tpcd/'.$this->flags->user['username'].time().'.csv';
						$check = $this->creatCsvFile($data, $filename);

						if($check){
							$scheduleTime = date('Y/m/d H:i:s');
							$messageSchedule_id = $this->uploadApi($scheduleTime, $filename);
							$this->removeFile($filename);
							
							if($messageSchedule_id > 0){
								$this->insertLog($post_data, $messageSchedule_id, $scheduleTime, $this->flags->user['idno'], $this->flags->user['name']);
							}
						}
					}
				}
			}

			redirect(base_url("tpcd/push"));
		}
	}

	public function log()
	{
		$conditions = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $attrs = array(
            'conditions' => $conditions,
        );
        if ($this->data['filter']['q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['q'];
        }

        $this->data['filter']['total'] = $total = $this->tpcd_model->getListCount($attrs);
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

        $this->data['list'] = $this->tpcd_model->getList($attrs);

		$this->load->library('pagination');
        $config['base_url'] = base_url("tpcd/push/log?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_index'] = base_url("tpcd/push");
		$this->data['link_show'] = base_url("tpcd/push/show");

        $this->layout->view('tpcd/log', $this->data);
	}

	public function show($id)
	{
		$content = $this->tpcd_model->getContent($id);

		if(!empty($content)){
			$this->data['content'] = htmlspecialchars_decode($content[0]['message_content']);
		} else {
			$this->data['content'] = '';
		}
		
		$this->layout->view('tpcd/content', $this->data);
	}

	private function creatCsvFile($detail, $filename)
	{
		$this->removeFile($filename);

		$data = array( 
			array('serverCode', 'id_no', 'notification_title', 'notification_context','message_title','message_content'), 
		); 

		$final_data = array_merge($data,$detail);
			
		$myfile = fopen($filename, "w"); 
		fprintf($myfile, chr(0xEF).chr(0xBB).chr(0xBF));
		
		foreach ($final_data as $line) 
		{ 
			fputcsv($myfile, $line); 
		} 
			
		fclose($myfile); 

		if(file_exists($filename)){
			return true;
		}

		return false;
	}

	private function removeFile($filename)
	{
		if(file_exists($filename)){
			unlink($filename);
		}
	}

	private function uploadApi($scheduleTime, $filename)
	{
		$url = "https://idmanage.gov.taipei:5443/tpcard/rest/messageBatch/v1.0.0/upload";

		$data = array();

		if (function_exists('curl_file_create')) {
			$cFile = curl_file_create($filename,'text/csv','detail.csv');
		} else { 
			$cFile = '@' . realpath($filename);
		}

		$data['serverToken'] = 'df55a063-1f19-4461-9844-e12eea2943421880b57f-65e3-40c5-a04b-73e6aeec6ac0';
		$data['scheduleTime'] = $scheduleTime;
		$data['file'] = $cFile;

		$soap_do = curl_init();
		$options = array(
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_SSL_VERIFYHOST => FALSE,
						CURLOPT_SSL_VERIFYPEER => FALSE,
						CURLOPT_VERBOSE => true,
						CURLOPT_URL => $url,
					);

		curl_setopt($soap_do, CURLOPT_POSTFIELDS, $data);
		curl_setopt($soap_do, CURLOPT_HTTPHEADER, array(
			'cache-control: no-cache',
			'content-type: multipart/form-data',)
		);

		curl_setopt_array($soap_do, $options);
		$result  = curl_exec($soap_do);

		if ($result === FALSE) {	
			$err = 'Curl error: ' . curl_error($soap_do);
			die($err);
		}

		$response = json_decode($result, true);

		curl_close($soap_do);

		if(!empty($response) && $response['status'] = 1){
			return $response['messageSchedule_id'];
		}

		return 0;
	}

	private function insertLog($data, $messageSchedule_id, $scheduleTime, $idno, $name)
	{
		$result = $this->tpcd_model->insertLog($data, $messageSchedule_id, $scheduleTime, $idno, $name);

		if($result){
			return true;
		}

		return false;
	}
}