<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Require_grade_model extends MY_Model
{
    public $table = 'require_grade';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    public function getListCount($attrs=array())
    {
        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => "*",
            'order_by' => 'grade_type',
        );

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
        }

        if (isset($attrs['rows'])) {
            $params['rows'] = $attrs['rows'];
        }
        if (isset($attrs['offset'])) {
            $params['offset'] = $attrs['offset'];
        }
        if (isset($attrs['sort'])) {
            $params['order_by'] = $attrs['sort'];
        }

        $data = $this->getData($params);

        return $data;
    }

}