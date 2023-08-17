<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Lecture_notes_assignments_model extends MY_Model
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
        if (isset($attrs['class_no']) && isset($attrs['conditions']['require.year'])){
            $this->db->select('count(1) cnt');
            $this->db->where('year', $attrs['conditions']['require.year']);
            $this->db->where('class_no', $attrs['class_no']);
            $query = $this->db->get('require');
            $terms_count = $query->result_array();

            if($terms_count[0]['cnt'] > 1){
                $order_by = 'ct.teacher_id,c_name';
            } else {
                $order_by = 'require.class_no,periodtime.course_date,periodtime.from_time';
            }
        }

        $params = array(
            'select' => "require.year,require.class_no,require.class_name,c.course_code,cd.name as c_name,ct.teacher_id ,t.name",
            'order_by' => $order_by,
            'group_by' => 'require.year,require.class_no,c.course_code,c_name,ct.teacher_id,t.name',
        );

        $params['join'] = array(
                    array(
                        'table' => "room_use ct",
                        'condition'=>'require.year = ct.year and require.class_no = ct.class_id and require.term = ct.term',
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => "periodtime",
                        'condition'=>'ct.year = periodtime.year and ct.class_id = periodtime.class_no and ct.term = periodtime.term and ct.use_id = periodtime.course_code and ct.use_date = periodtime.course_date',
                    ),
                    array(
                        'table' => "course c",
                        'condition'=>'ct.year = c.year and ct.class_id = c.class_no and ct.term = c.term and ct.use_id = c.course_code',
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => "course_code cd",
                        'condition'=>'c.course_code = cd.item_id ',
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => "teacher t",
                        'condition'=>"ct.teacher_id = t.idno and t.teacher='Y'",
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => "BS_user v",
                        'condition'=>'v.username = require.worker',
                        'join_type'=>'left',
                    ),

                );

        $date_like = array();
        if (isset($attrs['class_no'])) {
            // $like_idno = array(
            //     array('field' => 'require.class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            // );
            // $date_like = array_merge($date_like, $like_idno);
            $attrs['conditions']['require.class_no'] = $attrs['class_no'];
        }
        if (isset($attrs['class_name'])) {
            $like_name = array(
                array('field' => 'require.class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($attrs['query_teacher'])) {
            $like_teacher = array(
                array('field' => 't.name', 'value'=>$attrs['query_teacher'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_teacher);
        }
        if (isset($attrs['query_course_name'])) {
            $like_course_name = array(
                array('field' => 'cd.name', 'value'=>$attrs['query_course_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_course_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        if (!empty($attrs['where_special'])) {
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

        $list_data = $this->getData($params);
       
        $data = array();

        foreach($list_data as & $row){
            $all_files = $this->getFileList($row['year'], $row['class_no'], $row['course_code'], $row['teacher_id']);
            
            if(!isset($all_files['title'])){
                $all_files['title'] = '';
            }
            if(!isset($all_files['file'])){
                $all_files['file'] = '';
            }
            if(!isset($all_files['file_path'])){
                $all_files['file_path'] = '';
            }
            if(!isset($all_files['cre_time_stamp'])){
                $all_files['cre_time_stamp'] = '';
            }
            if(!isset($all_files['is_authorize'])){
                $all_files['is_authorize'] = '';
            }
            if(!isset($all_files['set_to_terms'])){
                $all_files['set_to_terms'] = '';
            }
            if(!isset($all_files['handouts'])){
                $all_files['handouts'] = '';
            }

            $row['file_name'] = @implode('<br>', $all_files['title']);
            $row['file'] = @implode('<br>', $all_files['file']);
            $row['download_name'] = @implode('<br>', $all_files['file_path']);
            $row['cre_time_stamp'] = @implode('<br>', $all_files['cre_time_stamp']);
            $row['is_authorize'] = $all_files['is_authorize']==1?"是":"";
            // custom (b) by chiahua 加上講義名稱和實體檔案下載的欄位
            $row['set_to_terms'] = @implode('<br>', $all_files['set_to_terms']);
            $row['handouts'] = @implode('<br>', $all_files['handouts']);

            if($row['handouts'] == '1' && empty($row['download_name'])){
               $row['file'] = $row['download_name'] = '無講義';
            }

            $this->db->select("status");
            $this->db->from('handouts_status');
            $this->db->where("year",$row['year']);
            $this->db->where("class_no",$row['class_no']);
            $this->db->where("course_code",$row['course_code']);
            $this->db->where("teacher_id",$row['teacher_id']);
            $query = $this->db->get();
            $status_data = $query->result_array();

            if(!empty($status_data['0']['status'])){
                $row['status'] = $status_data['0']['status'];
            }else{
                $row['status'] = '0';
            }


            if($row['status'] == '1' && empty($row['download_name'])){
                $row['file'] = $row['download_name'] = '無講義';
            }

            $data[] = $row;
        }



        return $data;
    }
    public function updateHandoutsStatus($attrs=array())
    {
        $this->db->where('year',$attrs['year']);
        $this->db->where('class_no',$attrs['class_no']);
        $this->db->where('course_code',$attrs['course_code']);
        $this->db->where('teacher_id',$attrs['teacher_id']);
        $update=['status'=>0];

        $this->db->update('handouts_status',$update);
        //die();
    }

    public function getSub($sub_conditions)
    {
        $this->db->select("F.*");
        $this->db->from('upload_file F');
        $this->db->join('room_use C', "F.id = C.teacher_id and F.course_code = C.use_id", 'left');
        $this->db->where("F.year",$sub_conditions['year']);
        $this->db->like("F.title", $sub_conditions['title'], "both");

        $query = $this->db->get();
        $sub_data = $query->result_array();
        $sub_qry = array();
        foreach($sub_data as $row){
            $tmp_id = (trim($row['id']) == '' ? "and ct.teacher_id is null " : "and ct.teacher_id = '{$row['id']}'");
            $tmp_course_code = (trim($row['course_code']) == '' ? "and c.course_code is null " : "and c.course_code = '{$row['course_code']}'");
            $sub_qry[] = " (require.year = '{$row['year']}' and require.class_no = '{$row['class_no']}' {$tmp_id} {$tmp_course_code})";
        }
        if(count($sub_qry) == 0){ // 有輸入檔名查詢但是找不到資料時，就預設也查不到課程資料的條件
            $query_sub_file_title = " ( require.year='-1' and require.class_no = '-1' and c.course_code = '-1')";
        }else{
            $query_sub_file_title = " (" . implode(' or ', $sub_qry) . ") ";
        }
        return $query_sub_file_title;
    }

    public function getFileList($year, $class_no, $course_code, $teacher_id)
    {
        $cnt = 0;
        $file_list = array();
        $get_teacher_id = (trim($teacher_id) == '' ? " and u.id is null" : " and u.id=".$this->db->escape(addslashes($teacher_id))."");
        $get_course_id = (trim($course_code) == '' ? " and u.course_code is null" : " and u.course_code=".$this->db->escape(addslashes($course_code))."");
        $sql = "select u.cre_date as cre_time_stamp , u.* from upload_file u left join `require` r on u.year=r.year and u.class_no=r.class_no and u.term = r.term where u.year=".$this->db->escape(addslashes($year))." and  u.class_no=".$this->db->escape(addslashes($class_no))." {$get_course_id} {$get_teacher_id} order by u.cre_date DESC";

        $query = $this->db->query($sql);
        $data = $query->result_array();
        foreach($data as $row){
            $file_list['title'][$cnt] = $row['title'];
            // $file_list['file_path'][$cnt] = '<a href="/upload_file.php?path='.urlencode($row['file_path']).'&file_name='.urlencode(preg_replace('/^.+[\\\\\\/]/', '', $row['file_path'])).'">'.preg_replace('/^.+[\\\\\\/]/', '', $row['file_path']).'</a>';
            $file_list['file_path'][$cnt] = '<a style="cursor: pointer;" onclick="go_download(\''.$row['file_path'].'\')">'.preg_replace('/^.+[\\\\\\/]/', '', $row['file_path']).'</a>';
            $file_list['file'][$cnt] = $row['file_path'];
            $file_list['cre_time_stamp'][$cnt] = $row['cre_time_stamp'];
            $file_list['is_authorize'] = $row['is_authorize'];
            $file_list['set_to_terms'][$cnt] = $row['set_to_terms'];
            $file_list['handouts'][$cnt] = $row['is_handouts'];
            $cnt++;
        }

        return $file_list;
    }

    public function get_course_name($conditions=array())
    {
        $this->db->select("name");
        $this->db->from('course_code');
        $this->db->where($conditions);
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['name'];
    }

    public function get_teacher_name($conditions=array())
    {
        $this->db->select("name");
        $this->db->from('teacher');
        $this->db->where($conditions);
        $query = $this->db->get();
        $data = $query->row_array();
        return $data['name'];
    }

    public function get_term($conditions=array())
    {
        $choices = array();
        $params['conditions'] = $conditions;
        $data = $this->getData($params);
        foreach($data as $row){
            $choices[$row['term']] = $row['term'];
        }
        return $choices;
    }

}