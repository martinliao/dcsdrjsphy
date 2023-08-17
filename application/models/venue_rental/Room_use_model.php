<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Room_use_model extends MY_Model
{
    public $table = 'room_use';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function get_used($fields=array())
    {
    	$room_id = $fields['room_id'];
    	$start_date = $fields['start_date'];
    	$end_date = $fields['end_date'];
    	$item_id = $fields['use_period'];
    	$room_type = $fields['cat_id'];

    	$this->db->select("start_time, end_time");
        $this->db->from("reservation_time");
        $this->db->where("item_id", $item_id);
        $query = $this->db->get();
        $data = $query->row_array();
        $st_time = $data['start_time'];
        $ed_time = $data['end_time'];

        $room_used = '';
        if($room_type != '04'){
        	$sql = "SELECT distinct R.YEAR,R.TERM,USE_DATE,RE.CLASS_NAME FROM room_use R
	  				LEFT JOIN `require` RE ON R.YEAR = RE.YEAR AND R.CLASS_ID=RE.CLASS_NO AND R.TERM=RE.TERM
	  				LEFT JOIN reservation_time C ON R.USE_PERIOD = C.ITEM_ID
	  				WHERE R.APPI_ID IS NOT NULL
	  				AND  (C.start_time  BETWEEN  ".$this->db->escape(addslashes($st_time))." AND  ".$this->db->escape(addslashes($ed_time))." OR C.end_time  BETWEEN  ".$this->db->escape(addslashes($st_time))." AND  ".$this->db->escape(addslashes($ed_time)).")
	  				AND USE_DATE BETWEEN ".$this->db->escape(addslashes($start_date))." AND ".$this->db->escape(addslashes($end_date))." AND R.ROOM_ID = ".$this->db->escape(addslashes($room_id))."
					AND (RE.is_cancel not in ('1') or IFNULL(RE.is_cancel, '0') = '0');";
			$query = $this->db->query($sql);
	        $used1 = $query->result_array();

	        if($used1){
	        	foreach($used1 as $row){
	        		$room_used .= $row["YEAR"]."年度 ".$row["CLASS_NAME"] . " 第".$row["TERM"]."期 ".substr($row["USE_DATE"], 0, 10).'<br>';
	        	}
	        	$room_used .='衝堂';
	        	return $room_used;
	        }

	        $st_time = substr($st_time, 0, 2).substr($st_time, 3, 2);
	        $ed_time = substr($ed_time, 0, 2).substr($ed_time, 3, 2);

			$sql = "SELECT  distinct R.YEAR,R.TERM,USE_DATE,RE.CLASS_NAME FROM room_use R
	  				LEFT JOIN `require` RE ON R.YEAR = RE.YEAR AND R.CLASS_ID=RE.CLASS_NO AND R.TERM=RE.TERM
	  				LEFT JOIN periodtime P ON R.YEAR = P.YEAR AND R.CLASS_ID=P.CLASS_NO AND R.TERM=P.TERM
						AND R.USE_PERIOD = P.ID AND R.USE_DATE = P.COURSE_DATE AND R.USE_ID = P.COURSE_CODE
	  				WHERE R.APPI_ID IS  NULL
	  				AND  (P.FROM_TIME  BETWEEN  ".$this->db->escape(addslashes($st_time))." AND  ".$this->db->escape(addslashes($ed_time))." OR P.TO_TIME BETWEEN  ".$this->db->escape(addslashes($st_time))." AND  ".$this->db->escape(addslashes($ed_time)).")
	  				AND USE_DATE BETWEEN ".$this->db->escape(addslashes($start_date))." AND ".$this->db->escape(addslashes($end_date))." AND R.ROOM_ID = ".$this->db->escape(addslashes($room_id))."
					AND (RE.is_cancel not in ('1') or IFNULL(RE.is_cancel, '0') = '0');";
			$query = $this->db->query($sql);
	        $used2 = $query->result_array();

	        if($used2){
	        	foreach($used2 as $row){
	        		$room_used .= $row["YEAR"]."年度 ".$row["CLASS_NAME"] . " 第".$row["TERM"]."期 ".substr($row["USE_DATE"], 0, 10).'<br>';
	        	}
	        	$room_used .='衝堂';
	        	return $room_used;
	        }

			$sql = "SELECT distinct R.YEAR,R.TERM,BOOKING_DATE,RE.CLASS_NAME FROM booking_place R
					LEFT JOIN `require` RE ON R.YEAR = RE.YEAR AND R.CLASS_NO=RE.CLASS_NO AND R.TERM=RE.TERM
					LEFT JOIN reservation_time C ON R.BOOKING_PERIOD = C.ITEM_ID
					WHERE
					R.BOOKING_DATE BETWEEN ".$this->db->escape(addslashes($start_date))." AND ".$this->db->escape(addslashes($end_date))." AND R.ROOM_ID = ".$this->db->escape(addslashes($room_id))."
					AND (RE.is_cancel not in ('1') or IFNULL(RE.is_cancel, '0') = '0');";
			$query = $this->db->query($sql);
	        $used3 = $query->result_array();

	        if($used3){
	        	foreach($used3 as $row){
	        		$room_used .= $row["YEAR"]."年度 ".$row["CLASS_NAME"] . " 第".$row["TERM"]."期 ".$row["BOOKING_DATE"].'<br>';
	        	}
	        	$room_used .='衝堂';
	        	return $room_used;
	        }
        }

		return $room_used;
    }

    public function get_groupnum($appi_id=NULL){
    	$params = array(
            'select' => 'max(groupnum) as groupnum_max',
        );
        $params['conditions'] = array(
        	'appi_id' => $appi_id,
        );
        $data = $this->getData($params);
        $data['0']['groupnum_max'];
        if($data['0']['groupnum_max']){
        	$groupnum = $data['0']['groupnum_max'] +1;
        }else{
        	$groupnum = '1';
        }
        return $groupnum;
    }

    public function get_expense($appi_id=NULL){
        $params = array(
            'select' => 'sum(expense) as all_expense',
        );
        $params['conditions'] = array(
            'appi_id' => $appi_id,
        );
        $data = $this->getData($params);
        return $data['0']['all_expense'];
    }

    public function get_room_countby($room_id=NULL){
    	$this->db->select("room_countby");
        $this->db->from("venue_information");
        $this->db->where("room_id", $room_id);
        $query = $this->db->get();
        $data = $query->row_array();
        // jd($data,1);
        return $data['room_countby'];
    }

    public function get_room_use_list($appi_id=NULL)
    {

        $this->db->select("appi_id, room_id, use_period, cat_id, min(use_date) as start_date, max(use_date) as end_date, unit, num, discount, groupnum, groupnote, SUM(expense) AS expense");
        $this->db->from("room_use");
        $this->db->where("appi_id", $appi_id);
        $this->db->group_by("appi_id, cat_id, room_id, use_period, unit, num, discount, groupnum, groupnote");
        $this->db->order_by('cat_id, room_id, start_date, use_period');
        $query = $this->db->get();
        $data = $query->result_array();

        foreach($data as & $row){
            $this->db->select("room_name");
            $this->db->from("venue_information");
            $this->db->where("room_id", $row['room_id']);
            $query = $this->db->get();
            $room_data = $query->row_array();
            $row['room_name'] = $room_data['room_name'];

            if(empty($row['unit'])){
                $this->db->select("room_countby");
                $this->db->from("venue_information");
                $this->db->where("room_id", $row['room_id']);
                $query = $this->db->get();
                $room_data = $query->row_array();
                $row['unit'] = $room_data['room_countby'];
            }

            $this->db->select("*");
            $this->db->from("venue_time");
            $this->db->where("room_id", $row['room_id']);
            $this->db->where("price_t", $row['use_period']);
            $query = $this->db->get();
            $time_data = $query->row_array();
            $row['price_a'] = $time_data['price_a'];
            $row['price_b'] = $time_data['price_b'];
            $row['price_c'] = $time_data['price_c'];
            $days = ((strtotime($row['end_date'])-strtotime($row['start_date'])) / 86400) + 1;
            $weekend = '0';
            for($i=0; $i<$days; $i++){
                $use_date = date("Y-m-d",strtotime("+{$i} day",strtotime($row['start_date'])));
                $use_day = date("N",strtotime($use_date));

                if($use_day == '6' || $use_day == '7'){
                    $weekend++;
                }
            }
            $row['weekend'] = $weekend;

        }
        // jd($data);
        return $data;
    }

    

    public function get_room_name($appi_id=NULL)
    {
        $room_name = '';
        $this->db->select("room_id");
        $this->db->from("room_use");
        $this->db->where("appi_id", $appi_id);
        $this->db->group_by("room_id");
        $query = $this->db->get();
        $data = $query->result_array();

        foreach($data as & $row){
            $this->db->select("room_name");
            $this->db->from("venue_information");
            $this->db->where("room_id", $row['room_id']);
            $query = $this->db->get();
            $room_data = $query->row_array();
            if(!empty($room_data['room_name'])){
                $room_name .= $room_data['room_name'].'<br>';
            }

        }

        return $room_name;
    }


    public function get_room_teacher($conditions=array()){

        $sql = "select distinct year, class_id as class_no,term,use_id as course_code,teacher_id,'N' as isevaluate,".$this->db->escape(addslashes($this->flags->user['username']))." as cre_user,NOW() as cre_date,".$this->db->escape(addslashes($this->flags->user['username']))." as upd_user,NOW() as upd_date ,null as assess_date,use_date,'N' as co_sync2epa,'' as inside,null as assess_date_end
                from room_use where isteacher='Y' and year=".$this->db->escape(addslashes($conditions['year']))." and term=".$this->db->escape(addslashes($conditions['term']))." and class_id=".$this->db->escape(addslashes($conditions['class_no']))." ;";
        $query = $this->db->query($sql);
        $data = $query->result_array();
        // jd($data,1);
        return $data;
    }

    public function get_ckeckin($conditions=array()){

        $data = array();
        if($conditions['start_date'] == '' && $conditions['end_date'] == ''){
            return $data;
        }else{
            $where = "A.APPI_ID IS NOT NULL";
            if ($conditions['start_date'] != "" && $conditions['end_date'] == ""){
              $where .= " and A.USE_DATE >= ".$this->db->escape(addslashes($conditions['start_date']))."";
            }
            if ($conditions['start_date'] == "" && $conditions['end_date'] != ""){
              $where .= " and A.USE_DATE <= ".$this->db->escape(addslashes($conditions['end_date']))."";
            }
            if ($conditions['start_date'] != "" && $conditions['end_date'] != ""){
              $where .= " and A.USE_DATE between ".$this->db->escape(addslashes($conditions['start_date']))." and ".$this->db->escape(addslashes($conditions['end_date']))."";
            }

            $select_limit = "";

            if(!empty($conditions['offset']) && !empty($conditions['rows'])){
                $select_limit .= " LIMIT " .intVal($conditions['offset']). ", " . intVal($conditions['rows']);
            }

            $sql = "SELECT a.*, d.APP_NAME, b.room_name, b.room_cap, (IFNULL(c.price_a,0) + IFNULL(c.price_b,0)) AS UNITAMT, IFNULL(a.TOTCNT,0) * (IFNULL(c.price_a,0) + IFNULL(c.price_b,0)) AS TOTAMT
                    FROM
                    (
                      SELECT D.APP_ID, A.ROOM_ID, SUM(A.NUM) AS TOTCNT
                      FROM room_use A
                      JOIN venue_information B ON A.ROOM_ID = B.ROOM_ID AND B.room_type = '02'
                      LEFT JOIN appinfo C ON A.APPI_ID = C.APPI_ID
                      LEFT JOIN applicant D ON C.APP_ID = D.APP_ID
                      WHERE {$where}
                      GROUP BY D.APP_ID, A.ROOM_ID
                    ) a
                    LEFT JOIN venue_information b ON a.ROOM_ID = b.ROOM_ID
                    LEFT JOIN venue_time c ON a.ROOM_ID = c.ROOM_ID AND c.price_t = '10'
                    LEFT JOIN applicant d ON a.APP_ID = d.APP_ID
                    ORDER BY APP_NAME, ROOM_ID" . $select_limit;
            $query = $this->db->query($sql);
            $data = $query->result_array();

            return $data;
        }
    }

    public function get_ckeckin_count($conditions=array()){

        $data = array();
        if($conditions['start_date'] == '' && $conditions['end_date'] == ''){
            return '0';
        }else{
            $where = "A.APPI_ID IS NOT NULL";
            if ($conditions['start_date'] != "" && $conditions['end_date'] == ""){
              $where .= " and A.USE_DATE >= ".$this->db->escape(addslashes($conditions['start_date']))."";
            }
            if ($conditions['start_date'] == "" && $conditions['end_date'] != ""){
              $where .= " and A.USE_DATE <= ".$this->db->escape(addslashes($conditions['end_date']))."";
            }
            if ($conditions['start_date'] != "" && $conditions['end_date'] != ""){
              $where .= " and A.USE_DATE between ".$this->db->escape(addslashes($conditions['start_date']))." and ".$this->db->escape(addslashes($conditions['end_date']))."";
            }

            $sql = "SELECT a.*, d.APP_NAME, b.room_name, b.room_cap, (IFNULL(c.price_a,0) + IFNULL(c.price_b,0)) AS UNITAMT, IFNULL(a.TOTCNT,0) * (IFNULL(c.price_a,0) + IFNULL(c.price_b,0)) AS TOTAMT
                    FROM
                    (
                      SELECT D.APP_ID, A.ROOM_ID, SUM(A.NUM) AS TOTCNT
                      FROM room_use A
                      JOIN venue_information B ON A.ROOM_ID = B.ROOM_ID AND B.room_type = '02'
                      LEFT JOIN appinfo C ON A.APPI_ID = C.APPI_ID
                      LEFT JOIN applicant D ON C.APP_ID = D.APP_ID
                      WHERE {$where}
                      GROUP BY D.APP_ID, A.ROOM_ID
                    ) a
                    LEFT JOIN venue_information b ON a.ROOM_ID = b.ROOM_ID
                    LEFT JOIN venue_time c ON a.ROOM_ID = c.ROOM_ID AND c.price_t = '10'
                    LEFT JOIN applicant d ON a.APP_ID = d.APP_ID
                    ORDER BY APP_NAME, ROOM_ID";
            $query = $this->db->query($sql);
            $data = $query->result_array();

            return count($data);
        }
    }


}