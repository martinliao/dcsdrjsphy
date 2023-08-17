<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ExportStudentList_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getSignStudentInfo($year,$classno,$term){
        $sql = sprintf("SELECT
							online_app.year,
							online_app.term,
							online_app.st_no,
							online_app.id,
							online_app.yn_sel,
							`require`.class_name,
							bureau.`name` AS bureau_name,
							BS_user.`name` AS student_name,
							BS_user.birthday,
							BS_user.office_email,
							BS_user.co_empdb_poftel,
							BS_user.office_tel,
							BS_user.gender,
							BS_user.out_gov_name,
							BS_user.retirement,
							job_title.`name` AS job_title_name,
							supervisor_code.`name` AS supervisor_name,
							job_level.`name` AS job_level_name,
							education.`name` AS education_name
						FROM
							online_app
							JOIN `require` ON online_app.`year` = `require`.`year` 
							AND online_app.class_no = `require`.class_no 
							AND online_app.term = `require`.term
							LEFT JOIN bureau ON online_app.beaurau_id = bureau.bureau_id
							JOIN BS_user ON online_app.id = BS_user.idno
							LEFT JOIN job_title ON BS_user.job_title = job_title.item_id
							LEFT JOIN supervisor_code ON BS_user.supervisor_id = supervisor_code.item_id
							LEFT JOIN job_level ON BS_user.job_level_id = job_level.item_id
							LEFT JOIN education ON BS_user.education = education.item_id 
						WHERE
							online_app.`year` = %s 
							AND online_app.class_no = %s 
							AND online_app.term = %s 
							AND online_app.yn_sel NOT IN (2,6,7)
						ORDER BY
							online_app.st_no",$this->db->escape(addslashes($year)),$this->db->escape(addslashes($classno)),$this->db->escape(addslashes($term)));	

        $query=$this->db->query($sql);
        $data=$query->result_array();

        return $data;
    }


}