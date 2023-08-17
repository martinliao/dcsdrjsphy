<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Certificate_user_list_model extends MY_Model
{
    public $table = 'certificate_user_list';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function get_list_by_seq_no($conditions=array())
    {
        $this->db->select("*");
        $this->db->from($this->table);
        $this->db->where("seq_no",$conditions['seq_no']);
        $this->db->where("cer_list_id",$conditions['cer_list_id']);
        $query = $this->db->get();
        $list = $query->result_array();
        return $list;
    }

    public function soft_delete_by_idno($conditions=array())
    {
        $data = array(
            'del' => '1',
        );

    $this->db->where('idno', $conditions['idno']);
    $this->db->where('seq_no', $conditions['seq_no']);
    $this->db->where('cer_list_id', $conditions['cer_list_id']);
    $this->db->update($this->table, $data);
    }

    public function is_check($conditions=array())
    {
        $this->db->select("idno");
        $this->db->from($this->table);
        //$this->db->where('idno', $conditions['idno']);
        $this->db->where("seq_no",$conditions['seq_no']);
        $this->db->where("cer_list_id",$conditions['cer_list_id']);
        $this->db->where("del",'0');
        $query = $this->db->get();
        $list = $query->result_array();
        //$idno = array();
        foreach ($list as $value) {
            //array_push($idno,$value['idno']);
            $checked[$value['idno']] = 'checked';
        }
        return $checked;
    }

    public function get_rank_data($conditions=array())
    {
        $this->db->select("idno,rank");
        $this->db->from($this->table);
        $this->db->where("seq_no",$conditions['seq_no']);
        $this->db->where("cer_list_id",$conditions['cer_list_id']);
        $query = $this->db->get();
        $list = $query->result_array();
        foreach ($list as $value) {
            $rank_data[$value['idno']] = $value['rank'];
        }
        return $rank_data;
    }

    public function delete_cer_user_data($conditions=array())
    {
        $this->db->where('seq_no', $conditions['seq_no']);
        $this->db->where('cer_list_id', $conditions['cer_list_id']);
        $this->db->delete($this->table);
    }

    public function get_user_cer_by_idno($idno)
    {
        $this->db->distinct();
        $this->db->select("seq_no");
        $this->db->from($this->table);
        $this->db->where("idno",$idno);
        $this->db->where("del",'0');
        $query = $this->db->get();
        $courses = $query->result_array();

        $this->db->select("certificate_user_list.*,certificate_list.cer_name,certificate_type.category");
        $this->db->from($this->table);
        $this->db->join('certificate_list', 'certificate_list.id = certificate_user_list.cer_list_id', 'left');
        $this->db->join('certificate_type', 'certificate_type.id = certificate_list.type_id', 'left');
        $this->db->where("idno",$idno);
        $this->db->where("del",'0');
        $query = $this->db->get();
        $list = $query->result_array();
        $cer_list = array();
        foreach ($courses as $seq_no) {
            //$tt = $seq_no['seq_no'];
            foreach ($list as $key => $data) {
                if($data['seq_no'] == $seq_no['seq_no']){
                   //array_push($cer_list[$seq_no['seq_no']],'2');
                   $cer_list[$seq_no['seq_no']][$key]= $data;
                   unset($list[$key]);
                }
                //$tt = $data['cer_list_id'];
            }
        }
        return $cer_list;
    }

    public function getUserOtherCert($idno)
    {
        $cer_list = [];
        if (!empty($idno)){
            $certificate_others = $this->db->where('certificate_other.idno', $idno)
            ->join('certificate_other', 'certificate_list.id = certificate_other.certificatefile_list_id')          
            ->get('certificate_list')->result_array();
            foreach ($certificate_others as $certificate_other) {
                if (!isset($cer_list[$certificate_other['seq_no']])){
                    $cer_list[$certificate_other['seq_no']] = array();
                }
                $tmp_pathinfo = pathinfo($certificate_other['cer_name']);
                $certificate_other['link'] = base_url('files/upload_cert_other/'.$certificate_other['certificatefile_list_id'].'_'.$certificate_other['id'].".".$tmp_pathinfo['extension']);
                $cer_list[$certificate_other['seq_no']][] = $certificate_other;
            }                  
        }  
        return $cer_list;               
    }

    public function get_pdf_data_by_user_list_id($cer_user_list_id)
    {
        $cer_user_list_data = $this->get($cer_user_list_id);
        $seq_no = $cer_user_list_data["seq_no"];
        $cer_list_id = $cer_user_list_data["cer_list_id"];
        $idno = $cer_user_list_data["idno"];                
        $pdf_data['rank'] = $cer_user_list_data["rank"];                //pdf
        //取得certificate_list資料
        $this->db->select("*");
        $this->db->from('certificate_list');
        $this->db->where("id",$cer_list_id);
        $query = $this->db->get();
        $list = $query->result_array();

        $pdf_data['cer_date'] = $list[0]["cer_date"];                   //發證日期參數 未轉換

        $type_id = $list[0]['type_id'];
        $pdf_data['cer_number'] = $list[0]['cer_number'];               //pdf
        $pdf_data['content_text'] = $list[0]['cer_text'];               //pdf
        $pdf_data['unit'] = $list[0]['cer_unit'];                       //pdf
        $pdf_data['cer_name'] = $list[0]['cer_name'];                       //pdf
        $pdf_data['qr_top_text'] = $list[0]['qr_top_text']; 
        $pdf_data['qr_bottom_text'] = $list[0]['qr_bottom_text']; 
        //取得檔案名稱
        $this->db->select("*");
        $this->db->from('certificate_type');
        $this->db->where("id",$type_id);
        $query = $this->db->get();
        $type = $query->result_array();

        $temp_ids['bg_file'] = $type[0]['bg_file_id']; 
        $temp_ids['signature_file'] = $type[0]['signature_file_id']; 
        $temp_ids['seal_file'] = $type[0]['seal_file_id']; 
        $pdf_data['special_type'] = $type[0]['special_type'];   //2021-11-22 新增 是否為特殊格式																										 
        foreach ($temp_ids as $name => $temp_id) {
            $this->db->select("*");
            $this->db->from('certificate_image');
            $this->db->where("id",$temp_id);
            $query = $this->db->get();
            $image = $query->result_array();
            $pdf_data[$name] = $image[0]['file_name'];                  //pdf
        }

        //取得require資料
        $this->db->select("*");
        $this->db->from('require');
        $this->db->where("seq_no",$seq_no);
        $query = $this->db->get();
        $require = $query->result_array();

        $pdf_data['course_name'] = $require[0]['class_name'];           //pdf
        $pdf_data['course_year'] = $require[0]['year'];                 //pdf
        $pdf_data['term'] = $require[0]['term'];                        //pdf
        $pdf_data['total_time'] = $require[0]['range'];            //pdf
        $pdf_data['temp_start_date'] = $require[0]['start_date1'];      //pdf
        $pdf_data['temp_end_date'] = $require[0]['end_date1'];          //pdf
        
        //2021-12-27 如果classenddate沒資料就用end_date1
        if(empty($require[0]['classenddate'])){
            $fix_classenddate = $require[0]['end_date1'];
        }else{
            $fix_classenddate = $require[0]['classenddate'];
        }
        $pdf_data['temp_real_end_date'] = $fix_classenddate;  //pdf
        //$pdf_data['temp_real_end_date'] = $require[0]['classenddate'];  //pdf
                //取得使用者資料
        $this->db->select("BS_user.name AS real_name,bureau.name AS beaurau_name,job_title.name AS title_name, BS_user.en_name");
        $this->db->from('BS_user');
        $this->db->where("idno",$idno);
        $this->db->join('bureau', 'bureau.bureau_id = BS_user.bureau_id', 'left');
        $this->db->join('job_title', 'job_title.item_id = BS_user.job_title', 'left');
        $query = $this->db->get();
        $user = $query->result_array();

        $pdf_data['user_name'] = $user[0]['real_name'];                 //pdf
        $pdf_data['unit_name'] = $user[0]['beaurau_name'];                 //pdf
        $pdf_data['job_title'] = $user[0]['title_name'];                 //pdf
        $pdf_data['en_name'] = $user[0]['en_name'];

        $pdf_data['id'] = $cer_user_list_data['id'];
        $pdf_data['idno'] = $cer_user_list_data['idno'];
          

        return $pdf_data;
    }


    public function get_file_name_by_type_id($type_id)
    {
        //取得檔案名稱
        $this->db->select("*");
        $this->db->from('certificate_type');
        $this->db->where("id",$type_id);
        $query = $this->db->get();
        $type = $query->result_array();
        $file_name['special_type'] = $type[0]['special_type']; //2021-11-22 新增 是否為特殊格式

        $temp_ids['bg_file'] = $type[0]['bg_file_id']; 
        $temp_ids['signature_file'] = $type[0]['signature_file_id']; 
        $temp_ids['seal_file'] = $type[0]['seal_file_id']; 
        foreach ($temp_ids as $name => $temp_id) {
            $this->db->select("*");
            $this->db->from('certificate_image');
            $this->db->where("id",$temp_id);
            $query = $this->db->get();
            $image = $query->result_array();
            $file_name[$name] = $image[0]['file_name'];                  //pdf
        }
        return $file_name;
    }
    
    public function get_course_data_by_seq_no($seq_no)
    {    
        //取得require資料
        $this->db->select("*");
        $this->db->from('require');
        $this->db->where("seq_no",$seq_no);
        $query = $this->db->get();
        $require = $query->result_array();

        $course_data['course_name'] = $require[0]['class_name'];           //pdf
        $course_data['course_year'] = $require[0]['year'];                 //pdf
        $course_data['term'] = $require[0]['term'];                        //pdf
        $course_data['total_time'] = $require[0]['range'];            //pdf
        $course_data['temp_start_date'] = $require[0]['start_date1'];      //pdf
        $course_data['temp_end_date'] = $require[0]['end_date1'];          //pdf
        
        //2021-12-27 如果classenddate沒資料就用end_date1
        if(empty($require[0]['classenddate'])){
            $fix_classenddate = $require[0]['end_date1'];
        }else{
            $fix_classenddate = $require[0]['classenddate'];
        }
        $course_data['temp_real_end_date'] = $fix_classenddate;  //pdf
        //$course_data['temp_real_end_date'] = $require[0]['classenddate'];  //pdf
        return $course_data;
    }

    public function get_user_data_by_idno($idno)
    {    
        //取得使用者資料
        $this->db->select("BS_user.name AS real_name,bureau.name AS beaurau_name,job_title.name AS title_name, BS_user.en_name");
        $this->db->from('BS_user');
        $this->db->where("idno",$idno);
        $this->db->join('bureau', 'bureau.bureau_id = BS_user.bureau_id', 'left');
        $this->db->join('job_title', 'job_title.item_id = BS_user.job_title', 'left');
        $query = $this->db->get();
        $user = $query->result_array();

        $user_data['user_name'] = $user[0]['real_name'];                  //pdf
        $user_data['en_name'] = $user[0]['en_name'];  
        $user_data['unit_name'] = $user[0]['beaurau_name'];               //pdf
        $user_data['job_title'] = $user[0]['title_name'];                 //pdf
        
        return $user_data;
    }

    /*
    取得結業證書課表
    */
    public function getPhyScheduleForCertificate($certificate_id)
    {
        $sql = "SELECT ru.use_date, cc.name, sum(ru.hrs) hrs, r.class_name, r.`year`, r.class_no, r.term
        FROM room_use ru
        JOIN course_code cc ON cc.item_id = ru.use_id
        JOIN `require` r ON r.`year` = ru.`year` AND r.term = ru.term AND r.class_no = ru.class_id
        JOIN certificate_user_list cul ON cul.seq_no = r.seq_no 
        LEFT JOIN periodtime pt ON pt.`year` = ru.`year` AND 
            pt.class_no = ru.class_id AND 
            pt.term = ru.term AND 
            pt.id = ru.use_period AND
            pt.course_date = ru.use_date AND
            pt.course_code = ru.use_id AND
            pt.room_id = ru.room_id        
        WHERE cul.id = ? AND cc.item_id NOT IN ('O00001', 'O00002', 'O00003', 'O00004', 'O00005') AND cc.name NOT LIKE '%報到%' AND cc.name NOT LIKE '%班務%' AND ru.isteacher = 'Y'
        GROUP BY SUBSTR(cc.name, 1, 4)
        ORDER BY ru.use_date, pt.from_time ";
        $query = $this->db->query($sql, [$certificate_id]);
        return $query->result();
    }

    public function getOnlineScheduleForCertificate($certificate_id)
    {
        $sql = "SELECT x.class_name, x.hours
        FROM certificate_user_list cul
        JOIN `require` r ON r.seq_no = cul.seq_no
        JOIN require_online x ON x.`year` = r.`year` AND x.class_no = r.class_no AND x.term = r.term 
        WHERE cul.id = ?";
        $query = $this->db->query($sql, [$certificate_id]);
        return $query->result();        
    }

    public function getCourseByCrt($certificate_id)
    {
        $sql = "SELECT r.*
        FROM certificate_user_list cul
        JOIN `require` r ON r.seq_no = cul.seq_no
        WHERE cul.id = ?";
        $query = $this->db->query($sql, [$certificate_id]);
        return $query->row();        
    }

    public function getCertificate($certificate_id)
    {
        $sql = "SELECT *, (SELECT value FROM certificate_setting WHERE setting_name = 'qRcodeTimeisOneYear' LIMIT 1) as qRcodeTimeisOneYearSetting FROM certificate_user_list WHERE id = ?";
        $query = $this->db->query($sql, [$certificate_id]);
        return $query->row();        
    }
    
    public function updateCertificateQrocdeInfo($certificate_id, $qrcode_is_one_year, $qrcode_manufactured_date)
    {
        $sql = "UPDATE certificate_user_list SET qrcode_is_one_year = ?, qrcode_manufactured_date = ? WHERE id = ?";
        $this->db->query($sql, [$qrcode_is_one_year, $qrcode_manufactured_date, $certificate_id]);
    }
}