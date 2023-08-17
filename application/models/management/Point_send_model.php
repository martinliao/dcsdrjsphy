<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Point_send_model extends MY_Model
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

    public function getList($attrs=array())
    {
    	$params = array(
            'select' => '',
            'order_by' => 'year,class_no, term',
        );

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
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
        if (isset($attrs['where_special'])) {
            $params['where_special'] = $attrs['where_special'];
        }
        $date_like = array();
        if (isset($attrs['class_no'])) {
            $like_class_no = array(
                array('field' => 'class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_no);
        }
        if (isset($attrs['class_name'])) {
            $like_class_name = array(
                array('field' => 'class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        $data = $this->getData($params);

        return $data;
    }

    public function getExport($year, $term, $class_no)
    {
    	$this->db->select("r.year, o.id, r.term, r.class_no, r.ecpa_class_id, r.class_name, r.start_date1, r.end_date1, o.score, r.range_internet, r.range_real, v.bureau_id, v.name, r.ecpa_class_id");
        $this->db->from('online_app o');
        $this->db->join('require r', "o.year =r.year and o.term =r.term and o.class_no =r.class_no", 'left');
        $this->db->join('BS_user v', "o.id=v.idno", 'left');
        $this->db->where("o.year",$year);
        $this->db->where("o.term",$term);
        $this->db->where("o.class_no",$class_no);
        $this->db->where("r.isend",'Y');
        $this->db->where("(o.yn_sel = 1 or o.co_csv_pass = 1)");
        // custom by chiahua 多加上是結訓或是有手動設定可下載的學員才會出現在CSV
        $query = $this->db->get();
        $regist_list = $query->result_array();
        return $regist_list;
    }

    public function getPeriodtime($year, $term, $class_no)
    {
        $this->db->from('periodtime');
        $this->db->where("year",$year);
        $this->db->where("term",$term);
        $this->db->where("class_no",$class_no);
        $this->db->where("course_code != 'O00003' and course_date is not null");
        $this->db->order_by('course_date,from_time');
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }

    public function getCut_hour($year, $term, $class_no)
    {
        $this->db->from('cut_hour');
        $this->db->where("year",$year);
        $this->db->where("term",$term);
        $this->db->where("class_no",$class_no);

        $query = $this->db->get();
        $data = $query->row_array();
        return $data;
    }

    public function getExportList($start_date, $end_date)
    {
        $this->db->select('class_no,year,term');
        $this->db->from('require');
        $this->db->where("end_date1 >=",$start_date);
        $this->db->where("end_date1 <",$end_date);
        $this->db->where("isend",'Y');
        $this->db->order_by('year,term,class_no');
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }

    public function getAllExport($start_date, $end_date)
    {
        $this->db->select("r.year, o.id, r.term, r.class_no, r.ecpa_class_id, r.class_name, r.start_date1, r.end_date1, o.score, r.range_internet, r.range_real, v.bureau_id, v.name, r.ecpa_class_id");
        $this->db->from('online_app o');
        $this->db->join('require r', "o.year =r.year and o.term =r.term and o.class_no =r.class_no", 'left');
        $this->db->join('BS_user v', "o.id=v.idno", 'left');
        $this->db->where("r.end_date1 >=",$start_date);
        $this->db->where("r.end_date1 <",$end_date);
        $this->db->where("r.isend",'Y');
        $this->db->where("(o.yn_sel = 1 or o.co_csv_pass = 1)");
        // custom by chiahua 多加上是結訓或是有手動設定可下載的學員才會出現在CSV
        $query = $this->db->get();
        $regist_list = $query->result_array();
        return $regist_list;
    }

    public function getCourseList($ptYear, $timeFlag)
    {
        $this->db->select("CONCAT('C{$ptYear}_',rr.CLASS_NO,(CASE WHEN rr.TERM<10 THEN CONCAT('_0',CONVERT(rr.TERM, char)) ELSE CONCAT('_',CONVERT(rr.TERM, char)) END) ) IMPORTID, rr.CLASS_NAME, rr.RANGE_REAL, date_format(rr.START_DATE1, '%Y/%m/%d') STARTDATE, date_format(rr.END_DATE1, '%Y/%m/%d') ENDDATE, vaa.NAME, vaa.CO_EMPDB_EMAIL, vaa.CO_EMPDB_POFTEL, rr.TERM, rr.NO_PERSONS, date_format(rr.APPLY_S_DATE, '%Y/%m/%d') APLYSDATE, date_format(rr.APPLY_E_DATE, '%Y/%m/%d') APLYEDATE, rr.ENV_R1, rr.ENV_R2, rr.ENV_R3, rr.ENV_R4");
        $this->db->from('require rr');
        $this->db->join('online_app oap', "rr.CLASS_NO=oap.CLASS_NO AND rr.YEAR=oap.YEAR AND rr.TERM=oap.TERM");
        $this->db->join('BS_user vaa', "rr.WORKER=vaa.idno");
        $this->db->where("date_format(rr.END_DATE1, '%Y%m') =",$timeFlag);
        $this->db->where("oap.YN_SEL", "1");
        $this->db->group_by("rr.CLASS_NO, rr.CLASS_NAME, rr.RANGE_REAL, rr.START_DATE1, rr.END_DATE1, vaa.name, vaa.CO_EMPDB_EMAIL, vaa.CO_EMPDB_POFTEL, rr.TERM, rr.NO_PERSONS, rr.APPLY_S_DATE, rr.APPLY_E_DATE, rr.ENV_R1, rr.ENV_R2, rr.ENV_R3, rr.ENV_R4");
        $this->db->order_by("rr.CLASS_NO, rr.TERM, rr.RANGE_REAL");
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }

    public function getTeacherList($ptYear, $timeFlag)
    {
        $this->db->select("CONCAT('C{$ptYear}_',rr.CLASS_NO,(CASE WHEN rr.TERM<10 THEN CONCAT('_0',CONVERT(rr.TERM, char)) ELSE CONCAT('_',CONVERT(rr.TERM, char)) END) ) IMPORTID, oap.ID, rr.RANGE_REAL");
        $this->db->from('require rr');
        $this->db->join('online_app oap', "rr.CLASS_NO=oap.CLASS_NO AND rr.YEAR=oap.YEAR AND rr.TERM=oap.TERM");
        $this->db->join('BS_user vaa', "rr.WORKER=vaa.idno");
        $this->db->where("date_format(rr.END_DATE1, '%Y%m') =",$timeFlag);
        $this->db->where("oap.YN_SEL", "1");
        $this->db->group_by("rr.CLASS_NO, rr.RANGE_REAL, oap.ID, rr.TERM");
        $this->db->order_by("rr.CLASS_NO, rr.TERM, oap.ID, rr.RANGE_REAL");
        $query = $this->db->get();
        $data = $query->result_array();
        return $data;
    }

}