<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Setting_class_model extends MY_Model
{
    public $table = '`require`';
    public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getList($conditions=array())
    {

		$sql = "SELECT * FROM (
			SELECT
				BC.NAME AS TYPE_NAME_A,B1.NAME AS TYPE_NAME_B,R.*,
				CONCAT(MONTH(R.START_DATE1) , '/' , DAYOFMONTH(R.START_DATE1) , '-' , MONTH(R.END_DATE1) , '/' , DAYOFMONTH(R.END_DATE1)) AS S_DATE,
				R.TYPE AS CLASS_TYPE, C.name AS CLASS_TYPE_NAME , qm.question_id, v.name as worker_name, fcc.ID NID,
				(select count(*) from courseteacher where R.term=term and R.year=year and R.class_no=class_no  AND ISEVALUATE='Y') as teacher_count,
				(select count(*) from mail_log where R.term=mail_log.term and R.year=mail_log.year and R.class_no=mail_log.class_no and mail_log.mail_type='3') as mail_mag_count
			FROM `require` R
			LEFT JOIN second_category BC ON BC.item_id=R.BEAURAU_ID and R.Type = BC.parent_id
			LEFT JOIN bureau B1 ON R.DEV_TYPE = B1.BUREAU_ID
			LEFT JOIN series_category C ON R.TYPE = C.ITEM_ID
			LEFT JOIN BS_user v ON v.idno = R.worker
			LEFT JOIN question_management qm on qm.year=R.year and qm.class_no=R.class_no and qm.term=R.term
			LEFT OUTER JOIN feedback_course_collocation fcc on R.year = fcc.CLASS_YEAR and R.class_no = fcc.CLASS_ID and R.term = fcc.CLASS_TERM
			WHERE  R.WORKER IS NOT NULL AND R.IS_CANCEL = '0' and R.TYPE <> 'O' AND START_DATE1>='{$conditions['start_date']}' AND START_DATE1<='{$conditions['end_date']}' order by R.TYPE ,R.CLASS_NAME,R.TERM
		)a WHERE mail_mag_count>0 ";

		$query = $this->db->query($sql);
        $data = $query->result_array();

        return $data;
    }




}