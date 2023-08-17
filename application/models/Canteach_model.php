<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Canteach_model extends MY_Model
{	
    public $table = 'canteach_model';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();
    }	

    public function getDownloadList($condition){

        $now= new DateTime(date('Y-m-d'));
        //$now = new DateTime("2019-06-01");
        $ynsel=[1,3,8];
        $search_file_sql = $this->db->select("*")
                                    ->distinct()
                                    ->from("upload_file")
                                    ->where("title LIKE", "%{$condition['queryFile']}%")
                                    ->or_where("SUBSTRING_INDEX(file_path, '/', -1) LIKE", "%{$condition['queryFile']}%")
                                    ->get_compiled_select();
        //var_dump($now);
        $this->db->select("de.year,de.class_no,de.term,de.course_code")
                 ->distinct()
                 ->from("online_app oa")
                 ->where('oa.id',$condition['idno'])
                 ->where_in('oa.yn_sel',$ynsel)
                 ->join("(SELECT distinct year, class_no, term, set_to_terms, open_to_all, course_code
                         FROM upload_file
                         WHERE '{$now->format("Y-m-d")}' BETWEEN start_date AND end_date AND is_open='Y') de",
                         "oa.year = de.year AND  
                          oa.class_no = de.class_no AND
                        (`oa`.`term` = `de`.`term` OR ( find_in_set( `oa`.`term`,de.set_to_terms) > 0 ) OR de.open_to_all = 'Y')");
        //$this->db->group_by('year,class_no,term,course_code');
        
        if (isset($condition['query_year'])) {
            $this->db->where("oa.year", $condition['query_year']);
        }
        // if (isset($condition['username'])) $this->db->where("user.username", $condition['username']);

        $re = $this->db->get_compiled_select();
        
        $this->db->start_cache();
        $this->db->select("r.year,r.class_no,r.term,r.class_name,c.course_code,cd.description,ca.id,t.name, p.course_date, p.from_time")
                 ->from("({$re}) re")
                 ->join("require r","re.year = r.year and re.class_no =r.class_no and re.term = r.term", "left")
                 ->join("upload_file u", "re.year = u.year and re.class_no =u.class_no and re.term = u.term and re.course_code = u.course_code and '{$now->format("Y-m-d")}' between u.start_date and u.end_date and u.is_open='Y'", "left")
                 ->join("course c", "r.year=c.year and r.class_no=c.class_no  and u.course_code = c.course_code", "left")
                 ->join("code_table cd", "c.course_code = cd.item_id and cd.type_id = '17'", "left")
                 ->join("canteach ca", "c.course_code = ca.course_code and ca.id=u.id", "left")
                 ->join("teacher t", "ca.id = t.idno and t.teacher='Y'", "left")
                 ->join("periodtime p", "c.course_code=p.course_code and p.year=r.year and p.class_no=r.class_no and p.term=r.term", "left")
                 ->join("room_use ru", "c.course_code=ru.use_id and ru.teacher_id = u.id and ru.year=re.year and ru.class_id=re.class_no and ru.term=re.term")
                 ->where("r.year is not null ")
                 //->where("r.year is not null and p.course_date is not null ")
               //  ->group_by("r.year,r.class_no,r.term,r.class_name,c.course_code,cd.description,ca.id ,t.name, p.course_date, p.from_time")
                 ->group_by("r.year,r.class_no,r.term,c.course_code,ca.id")
                 ->order_by("r.class_no, p.course_date, p.from_time");

        

        if (isset($condition['queryFile'])){
            $this->db->join("({$search_file_sql}) file", "file.year = r.year AND file.class_no = r.class_no AND file.term = r.term");
        }          

        if (isset($condition['query_year'])) $this->db->where("r.year", $condition['query_year']);
        if (isset($condition['term'])) $this->db->where("r.term", $condition['term']);
        if (isset($condition['class_no'])) $this->db->where("upper(r.class_no) LIKE", "%".strtoupper($condition['class_no'])."%");
        if (isset($condition['class_name'])) $this->db->where("upper(r.class_name) LIKE", "%".strtoupper($condition['class_name'])."%");
        if (isset($condition['course_name'])) $this->db->where("cd.description LIKE", "%{$condition['course_name']}%");
        if (isset($condition['teacher'])) $this->db->where("t.name LIKE", "%{$condition['teacher']}%");
        if (isset($condition['b_name'])) $this->db->where("cd.description", "%{$condition['b_name']}%");
        $this->db->where("c.course_code!=",null);
        // if (isset($condition['course_name'])) $this->db->where("ca.id", $condition['id']);

        $this->db->stop_cache();
        //$this->paginate();
        $query = $this->db->get();

        $result = $query->result();

        foreach($result as $row){
            $row->files = $this->getFileList($row);
            //$row->tname = $this->getTeacherName($row);
        }

        $this->db->flush_cache();
        
        return $result;
    }

    public function getFileList($row){
        $now = new DateTime();
        $file_list = array();
        $get_teacher_id = (trim($row->id) == '' ? " and u.id is null" : " and u.id='{$row->id}'");
        $get_course_id = (trim($row->course_code) == '' ? " and u.course_code is null" : " and u.course_code='{$row->course_code}'");
        $sql = "SELECT 
                    u.cre_date cre_time_stamp,
                    u.* 
                FROM upload_file u 
                -- LEFT JOIN `require` r on u.year=r.year AND u.class_no=r.class_no AND u.term = r.term 
                WHERE '{$now->format("Y-m-d")}' between u.start_date and u.end_date AND
                       u.is_open='Y' AND 
                       (u.open_to_all='Y' OR (u.open_to_all='N' AND INSTR(u.set_to_terms,'{$row->term}') > 0) OR
                       (u.open_to_all='N' AND u.term='{$row->term}')) AND 
                       u.year='{$row->year}' AND 
                       u.class_no='{$row->class_no}' {$get_course_id} {$get_teacher_id} 
                       ORDER BY u.cre_date DESC";

        
        //echo $sql."<br>";
        $query = $this->db->query($sql);
        $rows = $query->result();

        $files = [];
        foreach ($rows as $row){
            $file = new stdClass();
            $file->title = $row->title."<br>";
            // $file->file_path = '<a href="/upload_file.php?path='.urlencode($row->file_path).'&file_name='.urlencode(basename($row->file_path)).'">'.basename($row->file_path).'</a>';
            //$file->file_path = '<a style="cursor: pointer;" onclick="go_download('."'".$row->file_path."'".')">'.basename($row->file_path) ."</a><br>";
            $t=explode("/",$row->file_path );
            
            $file->file_path = '<a style="cursor: pointer;" onclick="go_download('."'".$row->file_path."'".')">'.$t[2] ."</a><br>";

            $file->cre_time_stamp = $row->cre_time_stamp."<br>";
            //$t=explode("/",$row->file_path );
            //var_dump($t);
            $files[] = $file;
        }
        //var_dump($get_teacher_id);
        //var_dump($get_course_id);
        return $files;        
    }

    

}
