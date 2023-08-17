<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This controller contains the general home site pages.
 *
 */

//class General extends MY_Controller
//class General extends FrontendController
class General extends BackendController
{
	public function __construct()
    {
        parent::__construct();
        /* Breadcrumbs :: Common */
		$this->breadcrumbs->unshift(1, lang('menu_general'), 'general');
    }

	/**
	 * Site Default Landing Page.
	 *
	 * @access public
	 * @return void
	 */
	public function index()
	{
		// Load via MY_Controller
		$this->set_page_title('Your Custom Page Title');
		$this->set_meta_description('Your Custom Meta Description.');
		$this->load_css(array(
			// relative path to your page specific css eg: /css/example.css
			'css/test.css'
		));
		$this->load_js(array(
			// relative path to your page specific javascript eg: /js/example.js
			// Remember we are going to handle the JS with require.js
		));

		// Do something here...
		$foo = 'bar';
		$data = array(
			'foo' => $foo
		);
		// relative path to your views file.php eg: index.php or custom/index.php pass your data to the view
		//$this->load->view('/general/index', $data);
//debugBreak();
		// 試著替換成 render_page(From CI_LTE, aka ci_lte)
		/* Title Page */
		$this->page_title->push(lang('menu_general'));
        $this->data['pagetitle'] = $this->page_title->show();
		/* Breadcrumbs */
		$this->breadcrumbs->unshift(2, lang('menu_general_testpage'), 'general'); // 測試
		$this->data['breadcrumb'] = $this->breadcrumbs->show();
		$this->render_page('general/index', $data);
	}

	/**
	 *
	 * Example of using another layout and view template for your view if needed
	 *
	 * @access public
	 * @return void
	 *
	*/
	public function highlight(){
		$this->set_page_title('Example of another page');
		$this->set_meta_description('Example of another page Meta Description.');

		// Set another layout
		$this->layout = 'highlight';

		// Do something here...
		// $foo = 'bar';

		// Assign your data to an array
		$data = array(
			//'baz' => $foo
		);

		// Load another view and pass the data
		$this->load->view('/example/index', $data);
	}
}