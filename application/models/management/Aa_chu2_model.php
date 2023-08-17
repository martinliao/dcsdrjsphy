<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Aa_chu2_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getListCount($attrs=array())
    {

        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($query_condition)
    {
        $sql="with s1 as
                (select a.TYPE,a.CLASS_NO, CONCAT(a.CLASS_NAME,'(第',a.TERM,'期)') as CLASS_NAME,a.TERM,a.REASON,a.BEAURAU_ID,(select count(*) from online_app where year=a.year and class_no=a.class_no and term=a.term and yn_sel in ('3','8','1')) as seled_no_persons ,a.no_persons,b.name as description from `require` a
                left join second_category b on  a.beaurau_id=b.item_id and a.type=b.parent_id
                where  {$query_condition} and a.type in ('A') order by a.beaurau_id,a.term)
                ,s2 as
                (select  a.TYPE,a.CLASS_NO,CONCAT(a.CLASS_NAME,'(第',a.TERM,'期)') as CLASS_NAME,a.TERM,a.REASON,a.beaurau_id as BEAURAU_ID ,(select count(*) from online_app where year=a.year and class_no=a.class_no and term=a.term and yn_sel in ('3','8','1')) as seled_no_persons ,a.no_persons,b.name as description from `require` a
                left join second_category b on  a.beaurau_id=b.item_id and a.type=b.parent_id
                where  {$query_condition} and a.type in ('B')  order by a.beaurau_id,a.term) select * from s1 union all select * from s2 ";
        $query = $this->db->query($sql);
        // jd($sql);
        $data = $query->result_array();

        return $data;
    }

    public function get_all_count($sql)
    {
        $sql="select sum(seled_no_persons) as A1 ,sum(no_persons) as A2 from (" . $sql .") as get_all_count";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        return $data['0'];
    }

    public function get_beaurau_count($test_arr, $query_condition)
    {
        $beaurau_count = array();
        foreach ($test_arr as $val) {
            if ($val=="A") {
                $sql="select a.BEAURAU_ID,(select count(*) from online_app where year=a.year and class_no=a.class_no and term=a.term and yn_sel in ('3','8','1')) as seled_no_persons ,a.no_persons from `require` a
                where  $query_condition and a.type in ('A') order by a.beaurau_id ";
            } elseif ($val=="B") {
                $sql="select a.beaurau_id as BEAURAU_ID,(select count(*) from online_app where year=a.year and class_no=a.class_no and term=a.term and yn_sel in ('3','8','1')) as A1 ,a.no_persons as A2 from `require` a
                where  $query_condition and a.type in ('B') order by a.beaurau_id";
            }

            $query = $this->db->query($sql);
            $data = $query->result_array();
            foreach($data as $row) {
                $beaurau_count[$val.$row['BEAURAU_ID']] = $row;
            }
        }

        return $beaurau_count;
    }
}