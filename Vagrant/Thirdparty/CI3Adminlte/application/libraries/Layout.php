<?php

/*
  $autoload['libraries'] = array('layout');
  From: ci3-adminlte: https://github.com/i4mnoon3/ci3-adminlte
 */

class Layout
{

  function __construct($layout = 'layout2', $theme = 'AdminLTE')
  {
    $this->layout = $layout;
    $this->theme = $theme;
    $this->obj = &get_instance();
    $this->data = array();
  }

  function set_data($key, $value)
  {
    $this->data[$key] = $value;
  }

  function view($view, $data = array())
  {
    $data = array_merge($this->data, $data);
    $data['content'] = $this->obj->load->view($this->theme . '/' . $view, $data, TRUE);
    $this->obj->load->view($this->theme . '/' . $this->layout, $data);
  }

  function set_layout($layout)
  {
    $this->layout = $layout;
  }

  function get_theme()
  {
    return $this->theme;
  }
}
