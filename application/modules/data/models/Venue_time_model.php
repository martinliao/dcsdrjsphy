<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Venue_time_model extends MY_Model
{
    public $table = 'venue_time';
    public $pk = 'room_id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'room_id' => '',
                    'price_t' => '',
                    'price_a' => '',
                    'price_b' => '',
                    'price_c' => '',
                ),$info);

        return $data;
    }

    public function getByRoomID($id)
    {
        $params = array(
            'conditions' => array('room_id'=>$id),
            'order_by' => 'price_t'
        );
        $data = $this->getData($params);
        //jd($data);

        return $data;
    }

}