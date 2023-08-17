<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_file_model extends MY_Model
{
    public $table = 'upload_file';
    public $pk = '';

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
            'select' => "upload_file.*,upload_file.cre_date as cre_time_stamp, r.worker,v.name",
            'order_by' => 'upload_file.cre_date desc',
        );

        $params['join'] = array(
                    array(
                        'table' => "require r",
                        'condition'=>'upload_file.year=r.year and upload_file.class_no=r.class_no and upload_file.term = r.term',
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => "BS_user v",
                        'condition'=>'v.username = upload_file.cre_user',
                        'join_type'=>'left',
                    ),

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