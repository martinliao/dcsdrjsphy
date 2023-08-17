<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin Controller
 *
 * @package    ClassMgt(Click-AP)-core
 * @author     Martin <martin@click-ap.com>
 * @copyright  2023 Click-AP {@link https://www.click-ap.com}
 * @license    https://opensource.org/licenses/MIT  MIT License
 * @link       <URI> (description)
 * @since      Version 3.2.0
 *
 */

class AdminController extends MI_Controller
{
    /**
     * An array of variables to be passed through to the view, layout, ....
     */
    protected $data = array();

    protected $theme = 'common3/admin'; // 'common2/admin' , 'reactadmin/admin' , 'common2/admin2';

    protected $role_id;

    protected $site = 'admin';

    protected $session_id;

    protected $user;

    protected $amdjscode = array('');

    protected $M_cfg;

    /**
     * [__construct description]
     *
     * @method __construct
     */
    public function __construct()
    {
        // To inherit directly the attributes of the parent class.
        parent::__construct();

        // CI profiler
        $this->output->enable_profiler(false);

        $this->load->library('smarty_acl');
        $this->load->helper('url');
        $this->load->helper('form');
        $this->load->helper('weblib_29');
        //$this->load->library('form_validation');

        $this->load->model(array(
            'system/setting_model',
            'system/user_model',
            'system/menu_model',
            'system/account_role_model',
            'system/user_group_auth_model',
            'system/personal_authority_model',
        ));
        // 網站參數
        $this->data['_SETTING'] = $this->initSetting();
        // 傳送 josn 給 JavaScrip 使用
        $this->data['_JSON'] = array();
        // 提示訊息
        $this->initAlert();

        $this->session_id = $this->session->session_id;
        $_session = $this->session->userdata($this->site.$this->session_id);
        $user = $this->user_model->get(array('id' => $_session['member_userid']));
        if ($user) {
            $this->user = $user;
            $this->data['_USER'] = $user;
            $_usrnick= empty($user['co_usrnick']) ? $user['name'] : $user['co_usrnick'];
            $this->data['_USER']['usrnick'] = $_usrnick;
        }
        // 選單
        $this->initMenu();
        // 目前位置
        $this->setMenu($user, $_session['member_userid']);
        $this->setMenuLocation();

        $this->getFilter();

        // 年度查詢
        $current_year = date('Y') - 1909;
        for($i=$current_year;$i>=90;$i--){
             $this->data['choices']['query_year'][$i] = $i;
        }
    }

    /**
     * [render_page description]
     *
     * @method render_page
     *
     * @param  [type]      $view [description]
     * @param  [type]      $data [description]
     *
     * @return [type]            [description]
     */
    protected function render_page($view, $data)
    {
        $_theme = $this->theme;
        // CSRF Token
        $this->data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );

        $data['_JSON']['_ALERT'] = $data['_ALERT'];
        $data = array_merge($this->data, $data);

        $_css = $this->load->view("{$_theme}/css.inc.php", $data, TRUE);
        $_standard_head_html = $this->get_head_code();
        $this->load->view("{$_theme}/header.inc.php", array('css' => $_css, 'standard_head_html' => $_standard_head_html));
        
        $data['navbar'] = $this->load->view("{$_theme}/navbar.inc.php", $data, TRUE);
        $data['sidebar'] = $this->load->view("{$_theme}/sidebar.inc.php", $data, TRUE);

        //$data['__content'] = $this->load->view($view, $data, true);
        // 套用舊的style ==> wrapper
        $_content = $this->load->view($view, $data, true);
        $data['__content'] = $this->load->view("{$_theme}/wrapper.inc.php", array(
            '__wrapper' => $_content,
            '_LOCATION' => $data['_LOCATION']
        ), TRUE);

        $this->load->view("{$_theme}/index", $data); // navbar, sidebar, view

        $data['js'] = $this->load->view("{$_theme}/js.inc.php", $data, TRUE);
        $data['standard_footer_html'] = $this->get_amd_footercode();
        $this->load->view("{$_theme}/footer.inc.php", $data); // footer, js
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

    private function setMenu($_user, $_userid)
    {
        $_permission = [];
        //$this->data['_MENU'] = $this->menu_model->getSidebarByPort($this->site);
        $userGroupId = $this->account_role_model->getByUsername($_user['username']);
        foreach($userGroupId as $key => $groupId){
            if($key == '0'){
                $_permission = $this->user_group_auth_model->getByGroupID($groupId);
            }else{
                $groupPermission = $this->user_group_auth_model->getByGroupID($groupId);
                if(!empty($groupPermission)){
                    $_permission = array_unique(array_merge($_permission, $groupPermission));
                }
            }
        }
        // 個人權限
        $userPermission = $this->personal_authority_model->getByUserID($_userid);
        if(!empty($userPermission)){
            $_permission = array_unique(array_merge($_permission, $userPermission));
        }
        $_allMenus = $this->menu_model->getSidebarByPort($this->site);
        $_accessMenus = [];
        foreach($_allMenus as $menu) {
            $_topMenu = [];
            if (in_array($menu['id'], $_permission) || $menu['auth'] == 0) { 
                $_topMenu = $menu;
                $_subMenus = [];
                if (count($menu['sub']) > 0) {
                    foreach($menu['sub'] as $subMenu) {
                        if (in_array($subMenu['id'], $_permission) || $subMenu['auth'] == 0) { 
                            array_push($_subMenus, $subMenu);
                        }
                    }
                    $_topMenu['sub'] = $_subMenus;
                }
                array_push($_accessMenus, $menu);
            }
        }
        $this->data['_MENU'] = $_accessMenus;
        return $_accessMenus;
    }

    private function setMenuLocation()
    {
        $this->data['_LOCATION'] = $this->menu_model->getLocation($this->site);
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

	/**
	 * Load css styles.
	 *
	 * @access protected
	 * @param  array $css
	 */
	protected function load_css(array $css)
	{
		// If globals exist - combine the globals with local
		if ($og_css = $this->load->get_var('site_css')) {
			// merge
			$css = array_merge($og_css, $css);
			// get rid of duplicates
			$css = array_unique($css);
		}

		$this->load->vars('site_css', $css);
	}

    /**
	 * Load javascript files.
	 *
	 * @access protected
	 * @param  array $js
	 */
	protected function load_js(array $js)
	{
		// If globals exist - combine the globals with local
		if ($og_js = $this->load->get_var('site_js')) {
			// merge
			$js = array_merge($og_js, $js);
			// get rid of duplicates
			$js = array_unique($js);
		}

		$this->load->vars('site_js', $js);
	}

    function get_head_code()
	{
        // From Moolde: outputrequirementslib.php
		$this->load->helper('moodlelib'); // common
		$this->load->helper('weblib_29');
		//$this->load->library('js_writer');
		$tmp = $this->init_requirements_data();
		$output = '';
		// Set up the M namespace.
        $js = "var M = {}; M.yui = {};\n";
		$js .= "M.pageloadstarttime = new Date();\n";
		$js .= Js_writer::set_variable('M.cfg', $this->M_cfg, false);
		//$js .= $this->YUI_config->get_config_functions();
        //$js .= js_writer::set_variable('YUI_config', $this->YUI_config, false) . "\n";
        //$js .= "M.yui.loader = {modules: {}};\n"; // Backwards compatibility only, not used any more.
        //$js = $this->YUI_config->update_header_js($js);
		$output .= html_writer::script($js);
        return $output;
    }


    /**
     * Returns js code to load amd module loader, then insert inline script tags
     * that contain require() calls using RequireJS.
     * @return string
     */
    protected function get_amd_footercode() {
        $output = '';
        $jsloader = site_url('Javascript/');
        $requirejsloader= site_url('Getamd/');
        $requirejsconfig = file_get_contents(APPPATH.'lib/requirejs/moodle-config.js');

        $requirejsconfig = str_replace('[BASEURL]', $requirejsloader, $requirejsconfig);
        $requirejsconfig = str_replace('[JSURL]', $jsloader, $requirejsconfig);
        $requirejsconfig = str_replace('[JSEXT]', '', $requirejsconfig);
        // 加了 3.9.12 的版本
        $requirejsconfig = str_replace('[JSMIN]', '', $requirejsconfig);

        $output .= html_writer::script($requirejsconfig);
        $output .= html_writer::script('', base_url('assets/libs/requirejs/require.js'));  // Ignore js_fix_url()

        // First include must be to a module with no dependencies, this prevents multiple requests.
        $prefix = "require(['core/first'], function() {\n";
        $suffix = "\n});";
        $output .= html_writer::script($prefix . implode(";\n", $this->amdjscode) . $suffix);
        return $output;
    }

    protected function sesskey() {
        //Get session name
        //$session_name = $admin ? $this->session_names['admin'] : $this->session_names['user'];
        $session_name = $this->session_names['admin'];
        $session = $this->session->userdata($session_name);
//debugBreak();
        //Check session exists
		if (empty($session['sesskey'])) {
			// note: do not use $USER because it may not be initialised yet
			if (!isset($session)) {
				return FALSE;
			}
			$session['sesskey'] = random_string(10);
		}
        return $session['sesskey'];
    }

    function get_config_for_javascript() {
        global $CFG;

        if (empty($this->M_cfg)) {
            // JavaScript should always work with $CFG->httpswwwroot rather than $CFG->wwwroot.
            // Otherwise, in some situations, users will get warnings about insecure content
            // on secure pages from their web browser.

            $this->M_cfg = array(
                'wwwroot'             => base_url(), // Yes, really. See above.
                'sesskey'             => $this->sesskey(),
                'themerev'            => -1,
                'slasharguments'      => (int)(!empty($CFG->slasharguments)),
                'theme'               => 'default',
                'jsrev'               => '-1', //$this->get_jsrev(),
                'admin'               => 'admin',
                'csrfname'            => $this->security->get_csrf_token_name(),
                'csrfhash'            => $this->security->get_csrf_hash()
            );
            //if ($CFG->debugdeveloper) {
            if (ENVIRONMENT !== 'production') {
                $this->M_cfg['developerdebug'] = true;
            }
        }
        return $this->M_cfg;
    }

    function init_requirements_data() {
        // Init the js config.
        $this->get_config_for_javascript();
        // ToDo: core-block
        //$this->yui_module('moodle-core-blocks', 'M.core_blocks.init_dragdrop', [], null, true);
    }

    function getFilter() {
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
    }

    function getDataTag($array) {
		return str_replace("=", '="', http_build_query($array, null, '" ', PHP_QUERY_RFC3986)).'"';
		/*$btn_book = <<<EOL
			<button type="button" class="btn btn-warning btn-xs edit"
				data-room_id="' . $d->room_id . '"
				data-room_sname="' . $d->room_sname . '"
				data-room_cap="' . $d->room_cap . '"
			>
				<i class="fas fa-fw fa-pen"></i> 訂!!
			</button>
EOL;/** */
	}


}
