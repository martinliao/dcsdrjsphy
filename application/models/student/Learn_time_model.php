<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Learn_time_model extends MY_Model
{
    public $table = 'lux_study_record_log';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }


    public function getRecord($attrs=array())
    {
        //var_dump($attrs);
        $this->db->select('a.*, b.class_name, b.is_mixed, b.is_assess,b.type,bureau.name as req_beaurau');
        $this->db->join('require as b','a.class_no = b.class_no and a.year=b.year and a.term=b.term');
        $this->db->join('bureau','bureau.bureau_id=b.req_beaurau','left');
        $this->db->order_by('a.month,a.create_date');
        $this->db->where('a.stu_id',$attrs['idno']);
        $this->db->where('a.year',$attrs['year']);
        $query=$this->db->get('lux_study_record_log as a');
        $result=$query->result_array();

        return $result;

        
        /*$this->db->select('online_app.id,online_app.st_no,r.year,r.class_name,r.term,r.start_date1,r.class_no as class_id,r.room_code,t.description as title,t.description as name,bu.name as pname,nvl(og.ou_gov,b1.name) as bname,b2.name as unit_name,r.is_assess,r.is_mixed,r.range,r.type, r.range_internet ,r.range_real ');
        $this->db->join('require as r','online_app.year=r.year and online_app.class_no=r.class_no and online_app.term=r.term','left')
                           ->join('BS_user as bu','online_app.id=bu.idno','left')
                           ->join('view_code_table as t','t.item_id = bu.job_title and t.type_id="02"','left')
                           ->join('bureau as b1','bu.bureau_id=b1.bureau_id','left')
                           ->join('bureau as b2','online_app.beaurau_id=b2.bureau_id','left')
                           ->join('room_use as ru','ru.year=online_app.year and ru.term=online_app.term and ru.class_id=online_app.class_no and ru.use_period="01"','left')
                           ->join('out_gov as og','bu.idno=og.id','left');
        $yn_sel=[1,3,8];
        $this->db->where_in('online_app.yn_sel',$yn_sel);
        $this->db->where('online_app.id',$attrs['idno']);
        $this->db->where('r.year',$attrs['year']);
        $this->db->where('r.is_cancel !=',1);
        $this->db->where('r.is_cancel is NOT NULL', NULL, FALSE);
        $this->db->group_by('`ru`.`year`,`ru`.`class_id`,`ru`.`term`');
        $this->db->order_by('r.year desc,r.start_date1 desc');
        $query=$this->db->get('online_app');
        $result=$query->result_array();

        
            for($i=0;$i<count($result);$i++){
                if($result[$i]['is_assess']==1){
                    $result[$i]['h1']=$result[$i]['range_real'];
                }else{
                    $result[$i]['h2']=$result[$i]['range_real'];
                }
                $result[$i]['h3']=$result[$i]['range_internet'];
            }
        }
        
        return $result;*/
    }
 
}