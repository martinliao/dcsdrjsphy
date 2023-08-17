<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_list_model extends MY_Model
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
            'select' => "seq_no, year, class_no, class_name, term, range_real",
            'order_by' => 'year desc, class_no, term',
        );

        $date_like = array();
        if (isset($attrs['class_no'])) {
            $like_idno = array(
                array('field' => 'class_no', 'value'=>$attrs['class_no'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
        }
        if (isset($attrs['class_name'])) {
            $like_name = array(
                array('field' => 'class_name', 'value'=>$attrs['class_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
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
        // jd($this->db->last_query());

        return $data;
    }

    public function get_cid_list($eYear, $eClass_no, $eTerm)
    {
        $this->db->select("*");
        $this->db->from('require_online');
        $this->db->where("year",$eYear);
        $this->db->where("term",$eTerm);
        $this->db->where("class_no",$eClass_no);

        $query = $this->db->get();
        $cid_list = $query->result_array();
        return $cid_list;
    }

    public function getScoreInfoByPkey($eYear, $eClass_no, $eTerm, $scoredOnlyAndSort=false, $idList=null)
    {
        //查詢分數類別
        $this->db->select("b.name, a.*");
        $this->db->from('require_grade a');
        $this->db->join('grade_category b', "a.grade_type = b.item_id", 'left');
        $this->db->where("a.year",$eYear);
        $this->db->where("a.term",$eTerm);
        $this->db->where("a.class_no",$eClass_no);
        $this->db->order_by("a.grade_type");

        $query = $this->db->get();
        $grade = $query->result_array();
        $gradesInfo = array();
        //產生分數分類資訊
        foreach($grade as $grade_row){
            $gradesInfo[] = array(
                'grade_type' => $grade_row['grade_type'],
                'proportion' => $grade_row['proportion'],
                'type_name' => $grade_row['name'],
            );
        }
        //查詢分數
        $sql =  "SELECT b.out_gov_name,bc.name AS beaurau_name,\n";
        $sql .= "c1.name as title_name,\n";
        $sql .= "b.name, b.en_name, b.email, b.office_email,\n";
        $sql .= "a.id, a.st_no, a.group_no, IFNULL(a.modi_num, 0) as modi_num, a.score, a.p_score, a.notpass_desc,\n";
        $sql .= "r.class_name, r.year, r.term, r.class_no, r.start_date1, r.end_date1,\n";
        $sql .= "vaa_boss.name as boss_name, vaa_boss.office_tel as boss_tel ";

        $i = 1;
        foreach($grade as $grade_row){
            $sql .= ",a{$i}.score as s{$i}, a{$i}.proportion as p{$i} \n";
            $i = $i + 1;
        }

        $sql .= "from online_app a \n";
        $sql .= "left join BS_user b on a.id = b.idno \n";
        $sql .= "left join bureau bc on bc.bureau_id=b.bureau_id \n";
        $sql .= "left join job_title c1 on b.job_title = c1.item_id \n";
        $sql .= "left join `require` r on r.year = a.year and r.class_no = a.class_no and r.term = a.term \n";
        $sql .= "left join BS_user vaa_boss on r.worker = vaa_boss.idno \n";
        $sql .= "left outer join out_gov og on b.idno = og.id\n";

        $grades = array();
        $i = 1;
        foreach($grade as $grade_row){
            $sql .= "left join online_app_score a{$i} on a.year = a{$i}.year and a.class_no = a{$i}.class_no and a.term = a{$i}.term and a.id = a{$i}.id and a{$i}.grade_type = ".$this->db->escape(addslashes($grade_row['grade_type']))." \n";
            $grades['p' . $i] = 's' . $i;
            $i = $i + 1;
        }

        $sql .= "where a.yn_sel not in ('2','6','7') and a.year = ".$this->db->escape(addslashes($eYear))." and a.class_no = ".$this->db->escape(addslashes($eClass_no))." and a.term = ".$this->db->escape(addslashes($eTerm))." \n";

        if (!empty($idList) && is_array($idList)) {
            for($idi=0; $idi<count($idList); $idi++){
                $idList[$idi] = $this->db->escape(addslashes($idList[$idi]));
            }            
            $sql .= sprintf(' AND a.id IN (%s)', implode(',', $idList));
        }

        if (!$scoredOnlyAndSort) {
            $sql .= "order by a.group_no,a.st_no \n";
        } else {
            $sql .= " AND a.modi_num is not null order by a.score desc \n";
        }

        $query = $this->db->query($sql);
        $scoreInfo = $query->result_array();


        // 建立列表
        $return = array();
        foreach($scoreInfo as & $row){
            $mainScore = $this->caleScore($row, $grades, $gradesInfo);
            $finalScore = $mainScore + $row['modi_num'];
            $row['main_score'] = $mainScore;
            $row['p_score'] = $this->calePScore($finalScore);
            $row['final_score'] = $finalScore;
            $row['gradesInfo'] = $gradesInfo;

            if(!empty($row['out_gov_name'])){
                $row['beaurau_name'] = $row['out_gov_name'];
            }

            array_push($return, $row);
        }

        return $return;

    }

    public function get_regist($conditions)
    {
        $this->db->select("id");
        $this->db->from('online_app');
        $this->db->where("year",$conditions['year']);
        $this->db->where("term",$conditions['term']);
        $this->db->where("class_no",$conditions['class_no']);
        $this->db->where("st_no",$conditions['st_no']);

        $query = $this->db->get();
        $data = $query->row_array();
        return $data;
    }

    public function query_online_app($classNo, $year, $term, $idno)
    {
        $this->db->select("online_ready, notpass_desc, is_assess, yn_sel");
        $this->db->from('online_app');
        $this->db->where("year",$year);
        $this->db->where("term",$term);
        $this->db->where("class_no",$classNo);
        $this->db->where("id",$idno);

        $query = $this->db->get();
        $data = $query->row_array();
        return $data;
    }

    public function count_online_app_score($classNo, $year, $term, $idno, $grade_type)
    {
        $this->db->from('online_app_score');
        $this->db->where("year",$year);
        $this->db->where("term",$term);
        $this->db->where("class_no",$classNo);
        $this->db->where("id",$idno);
        $this->db->where("grade_type",$grade_type);
        $cnt = $this->db->count_all_results();

        return $cnt;
    }

    /**
     * 計算總分
     *
     * @param array $data
     *   需要  SCORE 欄位
     * @param array $gradesInfo 分數類別
     */

    public function caleScore(&$data, $grades, $gradesInfo)
    {
        if (!is_array($gradesInfo) || count($gradesInfo)===0) {
            return $data['score'];
        } else if (is_array($gradesInfo)) {
            $score = 0;

            foreach ($grades as $skey) {
                if (isset($gradesInfo[substr($skey, 1)-1]['proportion']) && isset($data[$skey])
                    && is_numeric($gradesInfo[substr($skey, 1)-1]['proportion']) && is_numeric($data[$skey])) {
                    $score += $data[$skey] * $gradesInfo[substr($skey, 1)-1]['proportion'] / 100;
                }
            }

        }
        return $score;
    }

    /**
     * 90分以上為「優」、80~89為「甲」、70~79為「乙」，69以下為「丙」
     *
     * @param integer $grade 分數
     * @return 取得等第
     */

    public function calePScore($grade)
    {
        if ($grade >= 100) {
            return '特優';
        } else if ($grade >= 90) {
            return '優';
        } elseif($grade >= 80) {
            return '甲';
        } elseif($grade >= 70) {
            return '乙';
        } elseif($grade >= 60) {
            return '丙';
        } elseif($grade >= 50) {
            return '丁';
        } elseif($grade >= 40) {
            return '戊';
        } elseif($grade >= 30) {
            return '己';
        } elseif($grade >= 20) {
            return '庚';
        } elseif($grade >= 10) {
            return '辛';
        } else {
            return '壬';
        }
    }

    public function get_cer_list_type($list_id)
    {
        $this->db->from('certificate_list');
        $this->db->where("id",$list_id);
        $query = $this->db->get();
        $data = $query->row(1);

        return $data->type_id;       
    }

    public function deleteOtherCertificate($id)
    {
        if (!empty($id)){
            $otherCertificate = $this->db->where('id', $id)->get('certificate_other')->row();
            $tmp_pathinfo = pathinfo($otherCertificate->cer_name);
            $path = $otherCertificate->certificatefile_list_id.'_'.$otherCertificate->id.'.'.$tmp_pathinfo['extension'];
            $path = DIR_UPLOAD_CERT_OTHER.basename($path);
            $this->db->where('id', $id)->delete('certificate_other');
            if (file_exists($path)){
                unlink($path);
            } 
        }
    }


}