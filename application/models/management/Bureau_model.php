<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Bureau_model extends MY_Model
{
    public $table = 'bureau';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getList($attrs=array())
    {

        $params = array(
            'select' => "bureau_id, name, persons, persons_2",
            'order_by' => 'bureau_id',
        );

        $params['join'] = array(
                    array(
                        'table' => "(SELECT persons, persons_2, beaurau from beaurau_persons where class_no='{$attrs['class_no']}' and year='{$attrs['year']}' and term='{$attrs['term']}') as beaurau_persons",
                        'condition'=>'beaurau_persons.beaurau = bureau.bureau_id',
                        'join_type'=>'left',
                    ),

                );
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
        }

        $data = $this->getData($params);

        foreach($data as & $row){
            $attrs2 = array(
                'year' => $attrs['year'],
                'class_no' => $attrs['class_no'],
                'term' => $attrs['term'],
                'bureau_id' => $row['bureau_id'],
            );
            $data2 = $this->getList2($attrs2);
            $row['lv4'] = $data2;
        }
        return $data;
    }

    public function getList2($attrs2=array())
    {
        $params = array(
            'select' => "bureau_id, name, persons, persons_2",
            'order_by' => 'bureau_id',
        );

        $params['join'] = array(
                    array(
                        'table' => "(SELECT persons, persons_2, beaurau from beaurau_persons where class_no='{$attrs2['class_no']}' and year='{$attrs2['year']}' and term='{$attrs2['term']}') as beaurau_persons",
                        'condition'=>'beaurau_persons.beaurau = bureau.bureau_id',
                        'join_type'=>'left',
                    ),

                );

        $params['where_special'] = "bureau_level='4' and (del_flag is null or del_flag !='C') and parent_id='{$attrs2['bureau_id']}'";

        $data = $this->getData($params);

        return $data;
    }


}