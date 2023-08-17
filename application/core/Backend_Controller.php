<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CodeIgniter-HMVC
 *
 * @package    CodeIgniter-HMVC
 * @author     N3Cr0N (N3Cr0N@list.ru)
 * @copyright  2019 N3Cr0N
 * @license    https://opensource.org/licenses/MIT  MIT License
 * @link       <URI> (description)
 * @version    GIT: $Id$
 * @since      Version 0.0.1
 * @filesource
 *
 */

class BackendController extends MI_Controller
{
    //
    public $CI;

    /**
     * An array of variables to be passed through to the view, layout, ....
     */
    protected $data = array();

    /**
     * [__construct description]
     *
     * @method __construct
     */
    public function __construct()
    {
        parent::__construct();
        $this->output->enable_profiler(false);
        $CI =& get_instance();
        $this->load->library('smarty_acl');
        //$this->load->helper('url');
        //$this->load->helper('form');
        //$this->load->library('theme');
        //$this->load->library('form_validation');
        $this->logged_in();

        $this->smarty_acl->authorized();

        // 試著替換成 render_page(From CI_LTE, aka ci_lte)
        /* Load */
        //$config = $this->load->config('config', true);
        //$this->load->config('admin/config');
        $config = $CI->load->config('config', true);
        $this->load->library(['adminlte/breadcrumbs', 'adminlte/page_title']);
        $this->load->helper('menu');
        // 語言包
        $this->lang->load(['admin/main_header', 'admin/main_sidebar', 'admin/footer', 'admin/actions']);
        $this->breadcrumbs->unshift(0, $this->lang->line('menu_administration'), 'admin');

        /* Data */
        $this->load->model('common/prefs_model');
        $this->data['title'] = $this->config->item('title');
        $this->data['title_lg'] = $this->config->item('title_lg');
        $this->data['title_mini'] = $this->config->item('title_mini');
        $this->data['admin_prefs'] = $this->prefs_model->admin_prefs();
        //$this->data['user_login'] = $this->prefs_model->user_info_login($this->ion_auth->user()->row()->id);

        $this->data['charset'] = $this->config->item('charset');
        $this->data['avatar_dir'] = $this->config->item('avatar_dir');
        $this->data['plugins_dir'] = $this->config->item('plugins_dir');
		$this->data['frameworks_dir'] = $this->config->item('frameworks_dir');
		//$this->data['lang'] = element($this->config->item('language'), $this->config->item('language_abbr'));
    }

    protected function logged_in()
    {
        if (!$this->smarty_acl->logged_in(TRUE)) {
            return redirect('admin/login');
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
        $this->data = array_merge($this->data, $data);
        $this->load->view('templates/header', $this->data);
        $this->load->view('templates/main_header', $this->data);
        $this->load->view('templates/main_sidebar', $this->data);
        $this->load->view($view, $data);
        $this->load->view('templates/footer', $this->data);
        $this->load->view('templates/control_sidebar', $this->data);
    }

    /**
     * 參考自 CI_LTE 的 Template(library)
     */
    protected function render_page2($view, $data) {
        $_template = array();
        $_template['header']          = $this->load->view('_templates/header', $data, TRUE);
        $_template['main_header']     = $this->load->view('_templates/main_header', $data, TRUE);
        $_template['main_sidebar']    = $this->load->view('_templates/main_sidebar', $data, TRUE);
        $_template['content']         = $this->load->view($view, $data, TRUE);
        $_template['control_sidebar'] = $this->load->view('_templates/control_sidebar', $data, TRUE);
        $_template['footer']          = $this->load->view('_templates/footer', $data, TRUE);

        return $this->load->view('_templates/template', $_template);
    }
}
