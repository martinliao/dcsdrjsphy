<?php
class Layout {

	private $CI;
	private $oldStyleLayout = 'common/layout_main';
	private $theme;
	private $layout;

	//public function __construct() {
	public function __construct($_layout = 'layout2', $theme = 'common') {
		$this->CI =& get_instance();
		if (is_array($_layout)) {
            $this->theme = $_layout['theme'];
            $this->layout = $_layout['layout'];
            # $this->theme = $this->config->item('theme');
			$this->setLayout($_layout['theme'] . '/' . $_layout['layout']);
        } else {
            $this->theme = $theme;
            //$this->layout = $_layout;
			$this->setLayout($this->oldStyleLayout);
        }
	}

	public function setLayout($layout) {
		$this->layout =  $layout;
		return $this;
	}

	public function view($view, $data = array(), $return = false)
	{
		$data['base_url'] = base_url('/');
		/*$data['site'] = $this->CI->site;
		$data['_MENU'] = array();
		if (isset($this->CI->data['_MENU']))
			$data['_MENU'] = $this->CI->data['_MENU'];

		$data['_CONF'] = array();
		if (isset($this->CI->data['_SETTING']))
			$data['_SETTING'] = $this->CI->data['_SETTING'];

		$data['_JSON'] = array();
		if (isset($this->CI->data['_JSON']))
			$data['_JSON'] = $this->CI->data['_JSON'];
		unset($this->CI->data);


		// $data['page_title'] = $data['_CONF'][$this->CI->site]['title'];
		// $data['page_description'] = $data['_CONF'][$this->CI->site]['description'];
		// $data['page_keywords'] = $data['_CONF'][$this->CI->site]['keywords'];/** */
		if ( $this->layout == $this->oldStyleLayout ) {
			$data['__header'] = $this->CI->load->view("common/header", $data, true);
			$data['__footer'] = $this->CI->load->view("common/footer", $data, true);
		}

		if (is_array($view)) {
			$data['__content'] = '';
			foreach ($view as $v) {
				$data['__content'] .= $this->CI->load->view($v, $data, true);
			}
		} elseif(!empty($view)) {
			$data['__content'] = $this->CI->load->view($view, $data, true);
		} else{
			$data['__content'] = '';
		}

		if ($return) {
			$output = $this->CI->load->view($this->layout, $data, true);
			return $output;
		} else {
			$this->CI->load->view($this->layout, $data, false);
		}
	}
}
