<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_match_model extends MY_Model
{
    public $table = 'require';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function get_MANAGE_ListCount($attrs=array())
    {

        $data = $this->get_MANAGE_List($attrs);
        return count($data);
    }

    public function get_MANAGE_List($attrs=array())
    {
    	$params = array(
            'select' => 'require.seq_no, require.class_name, require.class_no, require.term, require.year, require.start_date1, require.end_date1, vaa.name as worker_name, nvl(vaa.co_empdb_poftel,vaa.office_tel) as office_tel',
            'order_by' => '',
        );

        $params['join'] = array(
                array(
                    'table' => "BS_user vaa",
                    'condition'=>'require.worker=vaa.idno',
                    'join_type'=>'left',
                ),
                array(
                    'table' => "stud_modify s",
                    'condition'=>"require.year = s.year and require.class_no =s.class_no and require.term = s.term and s.sd_wantchg = '1'",
                    'join_type'=>'',
                ),

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
                array('field' => 'require.class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_no);
        }
        if (isset($attrs['class_name'])) {
            $like_class_name = array(
                array('field' => 'require.class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }
        $this->db->distinct();
        $data = $this->getData($params);

        return $data;
    }

    public function get_HR_ListCount($attrs=array())
    {

        $data = $this->get_HR_List($attrs);
        return count($data);
    }

    public function get_HR_List($attrs=array())
    {
        $params = array(
            'select' => 'require.seq_no, require.class_name, require.class_no, require.term, require.year, require.start_date1, require.end_date1, vaa.name as worker_name, nvl(vaa.co_empdb_poftel,vaa.office_tel) as office_tel',
            'order_by' => '',
        );

        $params['join'] = array(
                array(
                    'table' => "online_app oa",
                    'condition'=>'require.year=oa.year and require.term=oa.term and require.class_no=oa.class_no',
                    'join_type'=>'left',
                ),
                array(
                    'table' => "BS_user vaa",
                    'condition'=>'require.worker=vaa.idno',
                    'join_type'=>'left',
                ),
                array(
                    'table' => "stud_modify s",
                    'condition'=>"require.year = s.year and require.class_no =s.class_no and require.term = s.term and s.sd_wantchg = '1'",
                    'join_type'=>'',
                ),

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
                array('field' => 'require.class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_no);
        }
        if (isset($attrs['class_name'])) {
            $like_class_name = array(
                array('field' => 'require.class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }
        $this->db->distinct();
        $data = $this->getData($params);

        return $data;
    }

    public function get_WORKER_ListCount($attrs=array())
    {

        $data = $this->get_WORKER_List($attrs);
        return count($data);
    }

    public function get_WORKER_List($attrs=array())
    {
        $params = array(
            'select' => 'require.seq_no, require.class_name, require.class_no, require.term, require.year, require.start_date1, require.end_date1, vaa.name as worker_name, nvl(vaa.co_empdb_poftel,vaa.office_tel) as office_tel',
            'order_by' => '',
        );

        $params['join'] = array(
                array(
                    'table' => "online_app oa",
                    'condition'=>'require.year=oa.year and require.term=oa.term and require.class_no=oa.class_no',
                    'join_type'=>'left',
                ),
                array(
                    'table' => "BS_user vaa",
                    'condition'=>'require.worker=vaa.idno',
                    'join_type'=>'left',
                ),
                array(
                    'table' => "stud_modify s",
                    'condition'=>"require.year = s.year and require.class_no =s.class_no and require.term = s.term and s.sd_wantchg = '1'",
                    'join_type'=>'',
                ),

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
                array('field' => 'require.class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_no);
        }
        if (isset($attrs['class_name'])) {
            $like_class_name = array(
                array('field' => 'require.class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }
        $this->db->distinct();
        $data = $this->getData($params);

        return $data;
    }

    public function get_STUDENT_ListCount($attrs=array())
    {

        $data = $this->get_STUDENT_List($attrs);
        return count($data);
    }

    public function get_STUDENT_List($attrs=array())
    {
        $params = array(
            'select' => 'require.seq_no, require.class_name, require.class_no, require.term, require.year, require.start_date1, require.end_date1, vaa.name as worker_name, nvl(vaa.co_empdb_poftel,vaa.office_tel) as office_tel',
            'order_by' => '',
        );

        $params['join'] = array(
                array(
                    'table' => "online_app oa",
                    'condition'=>'require.year=oa.year and require.term=oa.term and require.class_no=oa.class_no',
                    'join_type'=>'left',
                ),
                array(
                    'table' => "BS_user vaa",
                    'condition'=>'require.worker=vaa.idno',
                    'join_type'=>'left',
                ),
                array(
                    'table' => "stud_modify s",
                    'condition'=>"require.year = s.year and require.class_no =s.class_no and require.term = s.term and s.sd_wantchg = '1'",
                    'join_type'=>'',
                ),

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
                array('field' => 'require.class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_no);
        }
        if (isset($attrs['class_name'])) {
            $like_class_name = array(
                array('field' => 'require.class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_class_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }
        $this->db->distinct();
        $data = $this->getData($params);

        return $data;
    }

    public function get_changeTermInfos($where_special)
    {

        $this->db->select("r.class_name, r.start_date1, r.end_date1, r.term, r.year");
        $this->db->from('require r');
        $this->db->join('stud_modify s', "r.year = s.year and r.class_no =s.class_no and r.term = s.term", 'left');
        $this->db->where($where_special);
        $query = $this->db->get();
        $data = $query->result_array();
        $changeTermInfos = array();
        foreach($data as $row){
            $changeTermInfos[$row['year'].'|'.$row['term']] = "{$row['year']}年度第{$row['term']}期 (".substr($row['start_date1'], 0, 10)."~".substr($row['end_date1'], 0, 10).")";
        }

        return $changeTermInfos;

    }

    public function get_change_user($where_special)
    {
        //var_dump($where_special);
        //die();
        $this->db->select("id");
        $this->db->from('online_app');
        $this->db->where($where_special);
        $this->db->distinct();
        $query = $this->db->get();
        $data = $query->result_array();

        $userInfos = array();
        foreach($data as $row){
            $this->db->select("idno as id, name, telephone as home_tel, office_tel, email");
            $this->db->from('BS_user');
            $this->db->where('idno', "{$row['id']}");
            $query = $this->db->get();
            $row_data = $query->row_array();
            // jd($this->db->last_query());

            if(!empty($row_data)){
                $row_data['contact'] = '辦公室電話:'.$row_data['office_tel']."\rEMAIL:".$row_data['email'];
                if (empty($row_data['office_tel']) && empty($row_data['email'])) {
                    $row_data['contact'] = '';
                }
                $userInfos[] = $row_data;
            }
        }

        return $userInfos;
    }

    public function get_match($conditions)
    {
        $this->db->from('student_match');
        $this->db->where($conditions);

        $query = $this->db->get();
        $data = $query->row_array();

        return $data;
    }

    public function get_user($idno)
    {
        $userInfos = array();
        $this->db->select("idno as id, name, telephone as home_tel, office_tel, email");
        $this->db->from('BS_user');
        $this->db->where('idno', "{$idno}");
        $query = $this->db->get();
        $data = $query->row_array();
        if(!empty($data)){
            $data['contact'] = '辦公室電話:'.$data['office_tel']."\rEMAIL:".$data['email'];
            if (empty($data['office_tel']) && empty($data['email'])) {
                $data['contact'] = '';
            }
            $userInfos[] = $data;
        }
        return $userInfos;
    }

    public function get_change_ListCount($attrs=array())
    {

        $data = $this->get_change_List($attrs);
        return count($data);
    }

    public function get_change_List($attrs=array())
    {
        $qyear=date('Y')-1911;
        $qyear1=date('Y', strtotime('+1 year'))-1911;
        $qyear2=date('Y', strtotime('-1 year'))-1911;

        $this->db->select("sm.year, sm.term, sm.change_term, sm.class_no, sm.id, sm.cre_date, sm.cre_user, sm.contact, sm.change_year, vaa.name, oa.st_no");
        $this->db->from('student_match sm');
        $this->db->join('online_app oa', "oa.id=sm.id and oa.year=sm.year and oa.term=sm.term and oa.class_no=sm.class_no", 'left');
        $this->db->join('BS_user vaa', "sm.id=vaa.idno", 'left');
        $this->db->join('require r', "oa.year=r.year and oa.term=r.term and oa.class_no=r.class_no", 'left');
        $this->db->where("r.end_date1 > now() and (sm.year='{$qyear}' or sm.year='{$qyear1}' or sm.year='{$qyear2}') and sm.class_no='{$attrs['class_no']}'");
        $this->db->order_by("year, term, CRE_DATE");

        if (isset($attrs['rows']) && $attrs['rows'] && isset($attrs['offset']) && $attrs['offset']) {
            $this->db->limit($attrs['rows'], $attrs['offset']);
        } elseif (isset($attrs['rows']) && $attrs['rows']) {
            $this->db->limit($attrs['rows']);
        }

        $query = $this->db->get();
        $data = $query->result_array();

        return $data;
    }

    public function student_match_insert($fields)
    {
        $this->db->set('cre_date', 'now()', false);
        return $this->db->insert('student_match', $fields);
    }

    public function student_match_update($conditions=NULL, $fields=array())
    {
        if ($conditions) {
            if (is_array($conditions)) {
                $this->db->where($conditions);
            }
        }
        $fields = array_map('addslashes', $fields);
        return $this->db->update('student_match', $fields);
        // return $this->db->affected_rows();
    }

    public function student_match_delete($conditions=NULL)
    {
        if ($conditions) {
            if ($conditions && is_array($conditions)) {
                $this->db->where($conditions);
            }
        }

        $result = $this->db->delete('student_match');

        return $this->_result($result);
    }

    public function student_match_count($conditions=array())
    {
        $this->db->from('student_match');
        $this->db->where($conditions);
        return $this->db->count_all_results();
    }

}