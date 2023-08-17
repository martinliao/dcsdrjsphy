<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Template_list extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }

        $this->load->model('data/template_list_model');
        $this->data['item_id'] = $this->template_list_model->item_id;

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['item_id'])) {
            $this->data['filter']['item_id'] = '01';
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'index';

        $data = array();

        $conditions = array();
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        if ($this->data['filter']['item_id'] !== '' ) {
            $conditions['item_id'] = $this->data['filter']['item_id'];
        }

        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['filter']['total'] = $total = $this->template_list_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        if ($this->data['filter']['sort'] !== '' ) {
            $attrs['sort'] = $this->data['filter']['sort'];
        }
        $this->data['list'] = $this->template_list_model->getList($attrs);
        $conditions = array('item_id' => $this->data['filter']['item_id']);
        // $lest_tmp_seq = $this->template_list_model->getCount($conditions);

        $tmp_seq_max = $this->template_list_model->getMaxSeq($this->data['filter']['item_id']);
        $tmp_seq_min = $this->template_list_model->getMinSeq($this->data['filter']['item_id']);
        
       
        foreach ($this->data['list'] as & $row) {
            $row['link_edit'] = base_url("data/template_list/edit/{$row['id']}/?".$this->getQueryString());
            if($tmp_seq_min > 0 && $row['tmp_seq'] != $tmp_seq_min){
                $row['link_up'] = base_url("data/template_list/tmp_seq_up/{$row['id']}/?".$this->getQueryString());
                
            }
            if($tmp_seq_max > 0 && $row['tmp_seq'] != $tmp_seq_max){
                $row['link_dn'] = base_url("data/template_list/tmp_seq_dn/{$row['id']}/?".$this->getQueryString());
            }
            // $row['link_view'] = base_url("data/template_list/view/{$row['tmp_seq']}/?{$_SERVER['QUERY_STRING']}");
        }
        $this->load->library('pagination');
        $config['base_url'] = base_url("data/template_list?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);

        $this->data['link_add'] = base_url("data/template_list/add?".$this->getQueryString());
        $this->data['link_delete'] = base_url("data/template_list/delete?".$this->getQueryString());
        $this->data['link_enable'] = base_url("data/template_list/enable?".$this->getQueryString());
        $this->data['link_refresh'] = base_url("data/template_list?".$this->getQueryString());

        $this->layout->view('data/template_list/list', $this->data);
    }

    public function add()
    {
        $this->data['page_name'] = 'add';
        // jd($this->data['filter']['item_id']);
        if ($post = $this->input->post()) {
            if ($this->_isVerify('add') == TRUE) {
                $post['cre_user'] = $this->flags->user['username'];
                $conditions = array(
                    'item_id' => addslashes($post['item_id']),
                );
                $tmp_seq = $this->template_list_model->getCount($conditions);
                $post['tmp_seq'] = $tmp_seq + 1;
                $saved_id = $this->template_list_model->insert($post,'cre_date');
                if ($saved_id) {
                    $this->setAlert(1, '資料新增成功');
                }

                redirect(base_url("data/template_list/?".$this->getQueryString()));
            }
        }

        $this->data['form'] =  $this->template_list_model->getFormDefault();
        $this->data['form']['item_id'] = $this->data['filter']['item_id'];

        $this->data['link_save'] = base_url("data/template_list/add?".$this->getQueryString());
        $this->data['send_email_plus'] = base_url("data/template_list/send_email/0/?".$this->getQueryString());
        $this->data['link_cancel'] = base_url("data/template_list?".$this->getQueryString());
        $this->layout->view('data/template_list/add', $this->data);
    }

    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';

        if ($post = $this->input->post()) {
            $old_data = $this->template_list_model->getInfo($id);
            if ($this->_isVerify('edit', $old_data) == TRUE) {

                $rs = $this->template_list_model->update($id, $post);
                if ($rs) {
                    $this->setAlert(1, '資料編輯成功');
                    redirect(base_url("data/template_list/?".$this->getQueryString()));
                }

            }
        }

        $this->data['form'] = $this->template_list_model->getFormDefault($this->template_list_model->getInfo($id));
        $this->data['link_save'] = base_url("data/template_list/edit/{$id}/?".$this->getQueryString());
        $this->data['send_email_plus'] = base_url("data/template_list/send_email/{$id}/?".$this->getQueryString());
        $this->data['link_cancel'] = base_url("data/template_list?".$this->getQueryString());


        $this->layout->view('data/template_list/edit', $this->data);
    }

    public function send_email($who)
    {
        $this->load->helper("progress");
        $progress_helper = new progress_helper();

        // $params = ['year', 'class_no', 'term'];
        // $params = $this->getFilterData($params);
        

        $post = $this->input->post();
        print_r(xss_clean($post['content']));exit();  // 感覺會再調整...
        // $class_info = $this->progress_model->getRequire($params);
        
        // if (!empty($class_info)) {

            $s_name = [
                '0' => '研習人員名冊',
                '8' => '取消開班人員名冊',
                '9' => '未錄取人員名冊',
            ];
            $mail_data = [
                'mail_content' => $post['mail_content'],
                'course_content' => $post['course_content'],
                // 'class_info' => $class_info,
            ];
            switch ($who) {
                case '1':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '2':
                    if (empty($post['signatures'])) {
                        $post['signatures'] = [];
                    }
                    $from = $class_info->worker_email;
                    $mail_data['signatures'] = $this->progress_model->getSignatureLinks($params, addslashes($post['signatures']), $email_list);
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                case '3':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '8':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '9':
                    $mail_data['course_content'] = "";
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
                case '10':
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    break;
                default:
                    $mail_data['online_schedule'] = $this->progress_model->getOnlineSchedule($params);
                    $mail_data['phy_schedule'] = $this->progress_model->getPhySchedule($params);
                    $mail_data['s_name'] = (!empty($s_name[$who])) ? $s_name[$who] : $s_name['0'];
                    $mail_data['user_list'] = $this->progress_model->getCourseUserList($who, $params);
                    break;
            }
            $replace_data = $this->progress_model->getReplaceData($params);

            $mail_data['course_content'] = replaceEmailContent($mail_data['course_content'], $replace_data);
            $mail_data['mail_content'] = replaceEmailContent($mail_data['mail_content'], $replace_data);

            $email_content = arrangeEmailContent($mail_data); // progress helper

            

        // } else {
        //     $this->setAlert(2, "找不到該班期資訊");
        //     redirect(base_url("create_class/progress"));
        // }

        echo xss_clean($email_content);
        
    }
    public function tmp_seq_up($id=NULL)
    {
        $up_data = $this->template_list_model->getInfo($id);
        $conditions = array(
            'item_id' => $up_data['item_id'],
            'tmp_seq' => $up_data['tmp_seq'],
        );
        $dn_data = $this->template_list_model->getPreSeqData($conditions);
        //print_r($dn_data);
        //die();
        $up_fields = array(
            'tmp_seq' => $dn_data[0]['pre_seq'],
        );
        $this->template_list_model->update($id, $up_fields);
        $dn_fields = array(
            'tmp_seq' => $up_data['tmp_seq'],
        );
        $this->template_list_model->update($dn_data[0]['id'], $dn_fields);
        // jd($dn_data,1);
        $this->setAlert(1, '順序切換成功');
        redirect(base_url("data/template_list/?".$this->getQueryString()));
    }

    public function tmp_seq_dn($id=NULL)
    {
        $dn_data = $this->template_list_model->getInfo($id);
        $conditions = array(
            'item_id' => $dn_data['item_id'],
            'tmp_seq' => $dn_data['tmp_seq'] ,
        );
        $up_data = $this->template_list_model->getNextSeqData($conditions);
        
        $up_fields = array(
            'tmp_seq' => $up_data[0]['next_seq'],
        );
        $this->template_list_model->update($id, $up_fields);
        $dn_fields = array(
            'tmp_seq' => $dn_data['tmp_seq'],
        );
        $this->template_list_model->update($up_data[0]['id'], $dn_fields);
        // jd($up_data,1);
        $this->setAlert(1, '順序切換成功');
        redirect(base_url("data/template_list/?".$this->getQueryString()));
        $this->setAlert(1, '順序切換成功');
        redirect(base_url("data/template_list/?".$this->getQueryString()));
    }

    public function delete()
    {

        if ($post = $this->input->post()) {
            $del_num = 0;
            foreach ($post['rowid'] as $id) {
                $rs = $this->template_list_model->delete(intval($id));
                if ($rs['status']) {
                    $del_num ++;
                }
            }

            $error_num = count($post['rowid']) - $del_num;
            if ($error_num == 0) {
                $this->setAlert(1, "共刪除 {$del_num} 筆資料");
            } else {
                $this->setAlert("共刪除 {$del_num} 筆資料, {$error_num} 筆未刪除");
            }
        }

        redirect(base_url("data/template_list/?{$_SERVER['QUERY_STRING']}"));
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->template_list_model->getVerifyConfig();
        if ($action == 'edit') {
        }

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }

    public function getTemplate($id){
        $example = $this->template_list_model->get($id);
        echo json_encode($example);
    }
}
