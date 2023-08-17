<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 1. 禾多阝余 多餘 code(含註解code)
 * 2. Debug, 移除, jd(...
 * 3. 明確定義有用到的變數.
 * 4. Controller 應該要愈少(短)愈好, 因為它是每一個功能都會叫用的.
 * 5. 把 功能 與 登錄 controll 分離; 這裡只留 - 登入後才能使用的功能, 去掉 登入時的處理(eg. ReCaptcha...).
 */
class MY_Controller extends MI_Controller
{
    public $data = array();

    protected $site, $session_id;

    protected $flags = array();

    public function __construct()
    {
        parent::__construct();
        $this->session_id = $this->session->session_id;
        $this->site = 'admin';
        $this->flags = new stdClass;
        $this->data = array(
            'isMobile' => $this->isMobile(),
            'is_edap' => false
        );

        $this->load->library(array(
            'layout',
        ));

        $this->load->helper(array(
            'captcha',
            'common',
        ));

        $this->load->model(array(
            'system/setting_model',
            'system/menu_model',
            'system/user_model',
            'system/user_group_model',
            'system/user_group_auth_model',
            'system/personal_authority_model',
            'system/account_role_model'
            //'notice_model',
            //'comment_model',
        ));

        // 設定登入
        $this->initFlags();
        $this->data['flags'] = (array)$this->flags;
        // 選單
        $this->initMenu();

        // 目前位置
        if ($this->flags->is_login === TRUE) {
            $this->setMenu();
            $this->setMenuLocation();
        }

        // 網站參數
        $this->data['_SETTING'] = $this->initSetting();

        // 傳送 josn 給 JavaScrip 使用
        $this->data['_JSON'] = $this->initJson();

        // 提示訊息
        $this->initAlert();
        $this->setAlert();
        $this->setJson('_ALERT', $this->data['_ALERT']);
        // $this->data['_MESSAGE'] = $this->initMessage();
        // $this->setJson('_MESSAGE', $this->data['_MESSAGE']);

        // CSRF Token
        $this->data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        $this->initJson();
        $this->setJson('_ALERT', $this->data['_ALERT']);

        // META 參數設定
        $this->data['page_title'] = $this->data['_SETTING']['admin_name'];
        $this->data['page_description'] = '';
        $this->data['page_keywords'] = '';

        // 靜態頁面URL設定
        $this->data['static_css'] = HTTP_CSS;
        $this->data['static_js'] = HTTP_JS;
        $this->data['static_img'] = HTTP_IMG;
        //$this->data['static_fonts'] = HTTP_FONTS;
        $this->data['static_plugin'] = HTTP_PLUGIN;

        $this->data['filter'] = array(
            // 'q' => '',
            // 'page' => 1,
            // 'sort' => NULL,
        );
        if ($this->input->get()) {
            $this->data['filter'] = $this->input->get();
            foreach($this->data['filter'] as $filter_key => $filter_row){
                if (is_array($filter_row)){
                    foreach($this->data['filter'][$filter_key] as $filter_array_key => $filter_array_row){
                        $this->data['filter'][$filter_key][$filter_array_key] = htmlentities($this->security->xss_clean($filter_array_row));
                    }
                }else{
                    $this->data['filter'][$filter_key] = htmlentities($this->security->xss_clean($filter_row));
                }
            }
        }

        // 資料筆數
        $this->data['choices']['rows'] = array(
            10 => 10,
            20 => 20,
            30 => 30,
            50 => 50,
            100 => 100,
        );

        // 季別
        $this->data['choices']['query_season'] = array(
            '' =>'請選擇',
            1 => '第一季',
            2 => '第二季',
            3 => '第三季',
            4 => '第四季',
        );

        // 月份
        $this->data['choices']['query_month'] = array(
            '' =>'請選擇',
            1 => '1月',
            2 => '2月',
            3 => '3月',
            4 => '4月',
            5 => '5月',
            6 => '6月',
            7 => '7月',
            8 => '8月',
            9 => '9月',
            10 => '10月',
            11 => '11月',
            12 => '12月',
        );

         // 年度查詢
        $current_year = date('Y')-1909;
        for($i=$current_year;$i>=90;$i--){
             $this->data['choices']['query_year'][$i] = $i;
        }
        
        if (empty($this->data['filter']['rows'])) {
            $this->data['filter']['rows'] = 10;
            if ($this->input->cookie('rows')) {
                $this->data['filter']['rows'] = $this->input->cookie('rows');
            }
        } else {
            setcookie('rows',$this->data['filter']['rows'] , time()+86400);  // exist by 7 days
        }

        // switch profiler;
        if (ENVIRONMENT != 'testing') {

        }

        // ajax
        if ($this->input->is_ajax_request()) {
            $this->output->enable_profiler(FALSE);
        }

        // Command-line interface
        if (is_cli()) {
            $this->output->enable_profiler(FALSE);
        }

        $this->data['history_back'] = (empty($this->input->post('history_back'))) ? (int)$this->getFilterData('history_back', -1) : $this->input->post('history_back');

        $this->data['is_edap'] = false;
    }


    /********************************
     * Flags data setting.
     ********************************/
    private function initFlags()
    {
        $this->flags = new stdClass;
        $this->flags->is_login = FALSE;
        $this->flags->user = NULL;
        $data = $this->session->userdata($this->site.$this->session_id);

        if (isset($data['switch_ac'])) {
            $conditions = array(
                'id' => $data['switch_ac'],
            );
            $user = $this->user_model->get($conditions);

            if ($user) {
                $this->flags->is_login = TRUE;

                $this->flags->user = $user;
                $this->flags->user['switch'] = TRUE;
                $user_group_id = $this->account_role_model->getByUsername($user['username']);
                $this->flags->user['group_id'] = $user_group_id;
                foreach($user_group_id as $key => $group_id){
                    if($key == '0'){
                        $this->flags->permission = $this->user_group_auth_model->getByGroupID($group_id);
                    }else{
                        $group_permission = $this->user_group_auth_model->getByGroupID($group_id);
                        if(!empty($group_permission)){
                            $this->flags->permission = array_unique(array_merge($this->flags->permission, $group_permission));
                        }
                    }
                }
                // $this->flags->permission = $this->user_group_auth_model->getByGroupID($user['user_group_id']);
                //個人權限
                $user_permission = $this->personal_authority_model->getByUserID($data['switch_ac']);
                if(!empty($user_permission)){
                    $this->flags->permission = array_unique(array_merge($this->flags->permission, $user_permission));
                }

            }
        }

        if(isset($data['member_userid']) && empty($data['switch_ac'])){
            $conditions = array(
                'id' => $data['member_userid'],
            );

            $user = $this->user_model->get($conditions);

            if ($user) {
                $this->flags->is_login = TRUE;

                $this->flags->user = $user;
                $this->flags->user['switch'] = FALSE;
                $user_group_id = $this->account_role_model->getByUsername($user['username']);
                $this->flags->user['group_id'] = $user_group_id;
                foreach($user_group_id as $key => $group_id){
                    if($key == '0'){
                        $this->flags->permission = $this->user_group_auth_model->getByGroupID($group_id);
                    }else{
                        $group_permission = $this->user_group_auth_model->getByGroupID($group_id);
                        if(!empty($group_permission)){
                            $this->flags->permission = array_unique(array_merge($this->flags->permission, $group_permission));
                        }
                    }
                }
                // $this->flags->permission = $this->user_group_auth_model->getByGroupID($user['user_group_id']);
                //個人權限
                $user_permission = $this->personal_authority_model->getByUserID($data['member_userid']);
                if(!empty($user_permission)){
                    $this->flags->permission = array_unique(array_merge($this->flags->permission, $user_permission));
                }

            }
        }
        // session_star();
        $_SESSION['username']=$this->flags->user['username'];

    }

    public function setFlags($user)
    {
        //$this->session->set_userdata('member_userid',$user['id']);
        $data = array(
            'member_userid' => $user['id'],    
        );
        $this->session->set_userdata($this->site.$this->session_id, $data);
    }

    public function getToken($user)
    {
        return sha1($user['id'] . $this->session_id . time());
    }

    /********************************
     * Alert function.
     * param kind [int]
     * 0 => gary,
     * 1 => Green,
     * 2 => Blue,
     * 3 => Yellow,
     * 4 => Red
     ********************************/
    private function initAlert()
    {
        $this->data['_ALERT'] = array();

        $alert = $this->session->flashdata('_ALERT');
        if ($alert) {
            $this->data['_ALERT']['kind'] = $alert['kind'];
            $this->data['_ALERT']['message'] = $alert['message'];
            $this->data['_ALERT']['sec'] = $alert['sec'];
            $this->data['_ALERT']['layout'] = $alert['layout'];
        }
    }

    protected function setAlert($kind=0, $message=NULL, $sec=5 ,$layout='center')
    {
        $data = array(
            'kind' => $kind,
            'message' => $message,
            'sec' => $sec,
            'layout' => $layout,
        );
        $this->session->set_flashdata('_ALERT', $data);
    }


    /********************************
     * Message function.
     * param kind [int] alert, confirm, message
     ********************************/
    private function initMessage()
    {
        $this->data['_MESSAGE'] = array(
            'type' => NULL,
            'message' => NULL,
            'sec' => NULL,
        );

        $message = $this->session->flashdata('_MESSAGE');
        if ($message) {
            $this->data['_MESSAGE']['type'] = $message['type'];
            $this->data['_MESSAGE']['message'] = $message['message'];
            $this->data['_MESSAGE']['sec'] = $message['sec'];
        }

        return $message;
    }

    protected function setMessage($type=0, $message=NULL, $sec=5)
    {
        $data = array(
            'type' => $type,
            'message' => $message,
            'sec' => $sec,
        );
        $this->session->set_flashdata('_MESSAGE', $data);
    }

    /********************************
     * Menu functions.
     ********************************/
    private function initMenu()
    {
        $this->data['_MENU'] = array();
        $this->data['menu_catalog'] = array();
        $this->data['menu_function'] = array();
        $this->data['menu_current'] = array();
        return $this;
    }

    private function setMenu()
    {
        $this->data['_MENU'] = $this->menu_model->getSidebarByPort($this->site);
        return $this;
    }

    private function setMenuLocation()
    {
        $this->data['_LOCATION'] = $this->menu_model->getLocation($this->site);
        return $this;
    }

    /********************************
     * Notice functions.
     ********************************/
    private function initNotice()
    {
        $notice_kind = $this->notice_model->kind;
        $notice = $this->notice_model->getNotReply();
        foreach ($notice as & $row) {
            $row['ago'] = getAgo($row['date_added']);
            $row['kind_name'] = $notice_kind[$row['kind']];
            $row['link'] = base_url("notice/edit/{$row['id']}");
            // if ($row['kind'] == 1) {
                // $row['link'] = base_url("notice/contact/edit/{$row['id']}");
            // } elseif ($row['kind'] == 2) {
                // $row['link'] = base_url("notice/complete/edit/{$row['id']}");
            // } elseif ($row['kind'] == 3) {
                // $row['link'] = base_url("notice/returns/edit/{$row['id']}");
            // }
        }

        return $notice;
    }

    /********************************
     * Comment functions.
     ********************************/
    private function initComment()
    {
        $comment_kind = $this->comment_model->kind;
        $comment = $this->comment_model->getNotReply();
        foreach ($comment as & $row) {
            $row['ago'] = getAgo($row['date_added']);
            $row['kind_name'] = $comment_kind[$row['kind']];
            if ($row['kind'] == 1) {
                $row['link'] = base_url("notice/comment/edit/{$row['id']}");
            }
        }

        return $comment;
    }


    /********************************
     * Setting functions.
     ********************************/
    private function initSetting()
    {
        $data = array();
        $settings = $this->setting_model->getChoices();
        if ($settings) {
            $data = $settings;
        }
        return $data;
    }

    /********************************
     * JSON functions.
     ********************************/
    private function initJson()
    {
        return array();
    }

    protected function setJson($key, $val)
    {
        $this->data['_JSON'][$key] = $val;

        return $this;
    }

    protected function getJson($key)
    {
        if (isset($this->data['_JSON'][$key]))
        {
            return $this->data['_JSON'][$key];
        }

        return NULL;
    }

    protected function delJson($key)
    {
        unset($this->data['_JSON'][$key]);

        return $this;
    }

    /********************************
     * Check Mobile functions.
     ********************************/
    public function isMobile()
    {
        $regex_match="/(nokia|iphone|android|motorola|^mot\-|softbank|foma|docomo|kddi|up\.browser|up\.link|";
        $regex_match.="htc|dopod|blazer|netfront|helio|hosin|huawei|novarra|CoolPad|webos|techfaith|palmsource|";
        $regex_match.="blackberry|alcatel|amoi|ktouch|nexian|samsung|^sam\-|s[cg]h|^lge|ericsson|philips|sagem|wellcom|bunjalloo|maui|";
        $regex_match.="symbian|smartphone|midp|wap|phone|windows ce|iemobile|^spice|^bird|^zte\-|longcos|pantech|gionee|^sie\-|portalmmm|";
        $regex_match.="jig\s browser|hiptop|^ucweb|^benq|haier|^lct|opera\s*mobi|opera\*mini|320x320|240x320|176x220";
        $regex_match.=")/i";
        return preg_match($regex_match, strtolower($_SERVER['HTTP_USER_AGENT']));
    }

    /********************************
     * Change Query String params
     ********************************/
    public function getQueryString($merge_params=array(), $skip_params=array())
    {
        $filter = $this->data['filter'];
        $filter = array_merge($filter, $merge_params);

        foreach ($skip_params as $param) {
            if (isset($filter[$param])) {
                unset($filter[$param]);
            }
        }

        return http_build_query($filter);
    }

    public function getSeriesCategory(){
        $data = array();
        $this->db->select('item_id,name');
        $this->db->from('series_category');
        $query = $this->db->get();
        $bureau = $query->result_array();

        foreach ($bureau as $key) {
            $data[$key['item_id']] = $key['name'];
        }

        return $data;
    }

    public function getSecondCategory(){
        $type = $this->input->post('type');

        $this->db->select('item_id,name');
        $this->db->where('parent_id',$type);
        $query = $this->db->get('second_category');
        $result = $query->result_array();

        print_r(json_encode($result));
    }
    public function getSecond(){
        $type = $this->input->post('type');

        $this->db->select('cate_id,name');
        $this->db->where('type',$type);
        $this->db->where('is_start',0);
        $query = $this->db->get('sub_category');
        $result = $query->result_array();

        print_r(json_encode($result));
    }
    /*
        取得過濾後的 get 參數
        取得時同時判斷有無存在或者為空
        若無則回傳 $default
        若 $check = true 當沒有某參數則直接回傳 false
    */
    public function getFilterData($filter_key, $default = null, $check = false){
        if (is_array($filter_key)){
            $data = [];
            foreach ($filter_key as $key) {
                if (!empty($this->data['filter'][$key])){
                    $data[$key] = $this->data['filter'][$key];
                }else{
                    if ($check){
                        return false;
                    }
                    $this->data['filter'][$key] = $default;
                    $data[$key] = $this->data['filter'][$key];
                }
            }
            return $data;
        }else{
            $this->data['filter'][$filter_key] = (!empty($this->data['filter'][$filter_key])) ? $this->data['filter'][$filter_key] : $default;
            return (!empty($this->data['filter'][$filter_key])) ? $this->data['filter'][$filter_key] : $default;
        }
    }
}
