<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This controller contains the general home site pages.
 *
 */
class Javascript extends JavascriptController
{

	protected $data = array();

	public function __construct()
	{
		parent::__construct();
		//$filterQueryString = filterQueryString($_SERVER['QUERY_STRING']);
		//$slashargument = min_get_slash_argument();
		#$this->load->helper('configonlylib');
		#$this->load->helper('jslib');
		#$this->load->library(['core/minify']);
		//$uri = current_url(true);
	}


	/**
	 * Site Default Landing Page.
	 *
	 * @access public
	 * @return void
	 */
	public function index()
	{		
		$this->load->view('/general/index', $this->data);
	}

	public function lib($path, $scriptfile)
	{
		return $this->get(-1, 'lib/'.$path, $scriptfile);
	}

	public function jquery($path, $scriptfile)
	{
		return $this->get(-1, 'lib/jquery/'.$path, $scriptfile);
	}

	public function get($id = null, $path, $scriptfile) {
		$uri = current_url(true);
		# $path = $this->request->getPath(); # not working
		#$product_id = $this->uri->segment(3, 0);
//debugBreak();
		$param_offset=0;
		$segment= $params = $this->uri->segment_array();
		$rsegment= $params = $this->uri->rsegment_array();
		$params = array_slice($this->uri->rsegment_array(), $param_offset);
		//var_dump($params);
		$slashargument= $id.'/'.$path.'/'.$scriptfile;
		$slashargument = ltrim($slashargument, '/');
    	if (substr_count($slashargument, '/') < 1) {
			header('HTTP/1.0 404 not found');
			die('Slash argument must contain both a revision and a file path');
		}
		// image must be last because it may contain "/"
		list($rev, $file) = explode('/', $slashargument, 2);
		$rev  = min_clean_param($rev, 'INT');
		$file = '/'.min_clean_param($file, 'SAFEPATH');
		$jsfiles = array();
		$files = explode(',', $file);
		foreach ($files as $fsfile) {
			$tmp= APPPATH.$fsfile;
			//$jsfile = realpath(ASSETSPATH.$fsfile);
			$jsfile = realpath(APPPATH.$fsfile);
			if ($jsfile === false) {
				// does not exist
				continue;
			}
			if ((substr($jsfile, -3) !== '.js') && (substr($jsfile, -4) != '.css')) {
				// hackers - not a JS file
				continue;
			}
			$jsfiles[] = $jsfile;
		}
		if (!$jsfiles) {
			// bad luck - no valid files
			header('HTTP/1.0 404 not found');
			die('No valid javascript files found');
		}
		$etag = sha1($rev.implode(',', $jsfiles));
		// ToDo: cache 
		$content = '';
		foreach ($jsfiles as $jsfile) {
			$content .= file_get_contents($jsfile)."\n";
		}
		js_send_uncached($content, $etag);
	}
}