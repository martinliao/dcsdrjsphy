<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tv_wall_model extends MY_Model
{
    public $table = 'tv_wall_5c';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'appi_id' => '',
                    'tv_wall' => '',
                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'appi_id' => array(
                'field' => 'appi_id',
                'label' => 'appi_id',
                'rules' => 'trim',
            ),
            'tv_wall' => array(
                'field' => 'tv_wall',
                'label' => '是否公告至電視牆',
                'rules' => 'trim',
            ),
        );

        return $config;
    }

}