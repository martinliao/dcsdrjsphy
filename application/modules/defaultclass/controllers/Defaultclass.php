<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 表單預設值
 */
class Defaultclass extends MY_Controller //AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('planning/createclass_model');
        $this->load->model('data/second_category_model');
        $this->load->model('planning/setclass_model');
        $this->load->model('planning/booking_place_model');

        $this->load->model('defaultclass_model');
    }

    public function index($id = 0)
    {
        $this->data['page_name'] = 'edit';
        $this->data['user_bureau'] = $this->flags->user['bureau_id'];

        //$default_values  = $this->createclass_model->getFormDefault($this->createclass_model->get($id));
        // Save to DB
        //$tmp = json_encode($default_values);
        //$this->defaultclass_model->setDefault($tmp);
        // Get from DB
        $default_values  = (array)$this->defaultclass_model->getDefault();
        //$default_values  = $this->createclass_model->getFormDefault($this->createclass_model->get($id));
        $this->data['form'] = $default_values;
        //$this->data['form']['segmemo'] = $this->createclass_model->getSegmemo($this->data['form']['year'], $this->data['form']['class_no']);

        if (isset($this->data['form']['dev_type']) && !empty($this->data['form']['dev_type'])) {
            $this->data['form']['dev_type_name'] = $this->createclass_model->getDevTypeName($this->data['form']['dev_type']);
        }

        if (isset($this->data['form']['req_beaurau']) && !empty($this->data['form']['req_beaurau'])) {
            $this->data['form']['req_beaurau_name'] = $this->createclass_model->getDevTypeName($this->data['form']['req_beaurau']);
        }

        if (isset($this->data['form']['ecpa_class_id']) && !empty($this->data['form']['ecpa_class_id'])) {
            $this->data['form']['ecpa_class_name'] = $this->createclass_model->getEcpaClassName($this->data['form']['ecpa_class_id']);
        }

        if (isset($this->data['form']['room_code']) && !empty($this->data['form']['room_code'])) {
            $this->data['form']['room_name'] = $this->createclass_model->getRoomName($this->data['form']['room_code']);
            //var_dump($this->data['form']['room_name']);
        }

        if (isset($this->data['form']['room_remark']) && !empty($this->data['form']['room_remark'])) {
            $this->data['form']['room_name'] = '非公訓處上課';
        }

        $this->data['choices']['year'] = array($this->data['form']['year'] => $this->data['form']['year']);
        $this->data['choices']['type'] = $this->second_category_model->getSeriesCategory();
        $this->data['choices']['type'][''] = '請選擇';

        if (isset($this->data['form']['type']) && !empty($this->data['form']['type'])) {
            $this->data['beaurau_id'] = $this->createclass_model->getSecondCategory($this->data['form']['type']);
        }

        //if($this->data['form']['is_assess'] == '1' && $this->data['form']['is_mixed'] == '1'){ //2021-06-09 取消3B.edit *考核班期*影響*混成班級*的設定
        if ($this->data['form']['is_mixed'] == '1') {
            $this->data['form']['online_course'] = $this->createclass_model->getRequireOnline($this->data['form']['year'], $this->data['form']['class_no'], $this->data['form']['term']);
        }

        //$this->data['course_name'] = $this->createclass_model->getCourse($this->data['form']['year'], $this->data['form']['class_no'], $this->data['form']['term']);

        $this->data['choices']['ht_class_type'] = $this->createclass_model->getHourlyFee();
        $this->data['choices']['classify'] = $this->createclass_model->getClassProperty();
        $this->data['choices']['class_cate'] = $this->createclass_model->getStudyWayOne();
        $this->data['choices']['class_cate1'] = $this->createclass_model->getStudyWayTwo();
        $this->data['choices']['class_cate2'] = $this->createclass_model->getStudyWayThree();
        $this->data['choices']['isappsameclass'] = array('1' => 'YES', '2' => 'NO');
        $this->data['choices']['app_type'] = $this->createclass_model->getElectionWay();
        $this->data['choices']['class_status'] = array('0' => '', '1' => '草案', '2' => '確定計畫', '3' => '新增計畫');
        $this->data['choices']['reason'] = array('' => '自動偵測', '1' => '1', '2' => '2', '3' => '3', '4' => '4');
        $this->data['choices']['is_start'] = array('Y' => 'YES', 'N' => 'NO');
        $this->data['choices']['is_assess'] = array('1' => '是', '0' => '否');
        $this->data['choices']['is_mixed'] = array('1' => '是', '0' => '否');
        $this->data['choices']['map'] = array('' => '請選擇', '1' => 'A營造永續環境', '2' => 'B健全都市發展', '3' => 'C發展多元文化', '4' => 'D優化產業勞動', '5' => 'E強化社會支持', '6' => 'F打造優質教育', '7' => 'G精進健康安全', '8' => 'H精實良善治理');
        $this->data['choices']['env_class'] = array('Y' => '是', 'N' => '否');
        $this->data['choices']['policy_class'] = array('Y' => '是', 'N' => '否');
        $this->data['choices']['open_retirement'] = array('Y' => '是', 'N' => '否');

        if ($post = $this->input->post()) {
            $segmemo = $this->input->post('segmemo');
            $course_name = $this->input->post('course_name');
            $material = $this->input->post('material');

            $fmap_check = false;
            if ($post['fmap'] == 'Y') {
                for ($i = 1; $i <= 8; $i++) {
                    $key = 'map' . $i;
                    if (isset($post[$key]) && $post[$key] > 0) {
                        $fmap_check = true;
                        break;
                    }
                }
            }

            if (isset($post['online_course_name']) && !empty($post['online_course_name'])) {
                $online_course_name = $post['online_course_name'];
            }

            if (isset($post['hours']) && !empty($post['hours'])) {
                $hours = $post['hours'];
            }

            if (isset($post['elrid']) && !empty($post['elrid'])) {
                $elrid = $post['elrid'];
            }
            if ($this->_isVerify('edit', $this->data['user_bureau'], $fmap_check) == TRUE) {
                unset($post['room_name']);
                unset($post['course_name']);
                unset($post['material']);
                unset($post['dev_type_name']);
                unset($post['req_beaurau_name']);
                unset($post['ecpa_class_name']);
                unset($post['fmap']);
                //unset($post['online_course_name']);
                unset($post['hours']);
                unset($post['elrid']);

                //$rs = $this->createclass_model->updateRequire($id, $post);
                $tmp = json_encode($post);
                $rs = $this->defaultclass_model->setDefault($tmp);
                if ($rs) {
                    $this->setAlert(2, '預設值編輯成功');
                }
                //redirect(base_url("planning/createclass/?{$_SERVER['QUERY_STRING']}"));
                redirect(base_url("defaultclass"));
            }
        }

        if (isset($post['type']) && !empty($post['type'])) {
            $this->data['beaurau_id'] = $this->createclass_model->getSecondCategory($post['type']);
        }

        if (isset($post['beaurau_id']) && !empty($post['beaurau_id'])) {
            $this->data['form']['beaurau_id'] = $post['beaurau_id'];
        }

        //$this->data['link_save2'] = base_url("defaultclass/edit/{$id}/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_save2'] = base_url("defaultclass");

        $this->data['link_cancel'] = base_url("planning/createclass/?{$_SERVER['QUERY_STRING']}");
        $this->data['link_get_ecpa_name'] = base_url("planning/createclass/getEcpaClassName");
        $this->data['link_get_second_category'] = base_url("planning/createclass/getSecondCategory");
        $this->data['link_get_room'] = base_url("planning/createclass/getRoom");
        $this->data['link_update_require'] = base_url("planning/createclass/update_require_RoomCode_and_time"); //mark 2021-06-04

        $this->data['unlock_start_date1'] = 'false';
        $this->data['unlock_end_day1'] = 'false';
        if ($_SESSION['username'] == 'A226193585' or $_SESSION['username'] == '3009006' or $_SESSION['username'] == 'admin' or $_SESSION['username'] == 'F227164127') {   //mark 2021-06-04
            $this->data['unlock_start_date1'] = 'true';
            $this->data['unlock_end_day1'] = 'true';
        } //mark 2021-06-04 加入unlock條件username

        //$this->layout->view('planning/createclass/edit', $this->data);
        //$this->layout->setLayout('adminlte/main');
        //$this->layout->setLayout('common2/main');
        //$this->layout->view('edit', $this->data);
        //$this->render_page('default', $this->data);
        $this->layout->view('defaultclass/default', $this->data);
    }

    private function _isVerify($action = 'add', $user_bureau, $fmap_check)
    {
        $config = $this->defaultclass_model->getVerifyConfig();
        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');
        return ($this->form_validation->run() == FALSE) ? FALSE : TRUE;
    }

}
