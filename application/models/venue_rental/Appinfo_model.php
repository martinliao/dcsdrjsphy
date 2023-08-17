<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Appinfo_model extends MY_Model
{
    public $table = 'appinfo';
    public $pk = 'id';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getFormDefault($info=array())
    {
        $data = array_merge(array(
                    'app_id' => '',
                    'app_reason' => '',
                    'memo' => '',
                    'other_expense' => '',
                    'total_expense' => '',
                    'people' => '0',
                    'days' => '0',
                    'billno' => '',
                    'app_name' => '',
                    'is_public' => '',
                    'contact_name' => '',
                    'tel' => '',
                    'fax' => '',
                    'zone' => '',
                    'addr' => '',
                    'email' => '',
                    'tv_wall' => 'N',

                ),$info);

        return $data;
    }

    public function getVerifyConfig()
    {
        $config = array(
            'app_id' => array(
                'field' => 'app_id',
                'label' => '申請單位',
                'rules' => 'trim|required',
            ),
            'app_reason' => array(
                'field' => 'app_reason',
                'label' => '活動名稱暨內容說明',
                'rules' => 'trim|required|max_length[100]',
            ),
            'memo' => array(
                'field' => 'memo',
                'label' => '其它代辦事項',
                'rules' => 'trim|max_length[100]',
            ),
            'other_expense' => array(
                'field' => 'other_expense',
                'label' => '代辦事項費用',
                'rules' => 'trim|integer|max_length[10]',
            ),
            'total_expense' => array(
                'field' => 'total_expense',
                'label' => '金額總計',
                'rules' => 'trim|integer|max_length[10]',
            ),
            'people' => array(
                'field' => 'people',
                'label' => '人數',
                'rules' => 'trim|integer|max_length[15]',
            ),
            'days' => array(
                'field' => 'days',
                'label' => '天數',
                'rules' => 'trim|integer|max_length[15]',
            ),
            'billno' => array(
                'field' => 'billno',
                'label' => '備註',
                'rules' => 'trim|max_length[15]',
            ),
        );

        return $config;
    }

    public function _insert($fields=array())
    {
        return $this->insert($fields);
    }

    public function _update($pk, $fields=array())
    {
        return parent::update($pk, $fields);
    }

    public function getListCount($attrs=array())
    {

        $data = $this->getList($attrs);
        return count($data);
    }

    public function getList($attrs=array())
    {
        $params = array(
            'select' => 'appinfo.*, room_use.start_date, room_use.end_date, applicant.tel, applicant.app_name , applicant.contact_name ',
            'order_by' => 'cre_date desc',
        );
        $params['join'] = array(
                    array(
                        'table' => '(SELECT app_id, tel, contact_name, app_name from applicant ) as applicant',
                        'condition'=>'applicant.app_id = appinfo.app_id',
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => '(SELECT appi_id, min(use_date) as start_date, max(use_date) as end_date from room_use GROUP BY appi_id) as room_use',
                        'condition'=>'room_use.appi_id = appinfo.appi_id',
                        'join_type'=>'left',
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

        $date_like = array();
        if (isset($attrs['appi_id'])) {
            $like_idno = array(
                array('field' => 'appinfo.appi_id', 'value'=>$attrs['appi_id'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_idno);
        }
        if (isset($attrs['app_name'])) {
            $like_name = array(
                array('field' => 'applicant.app_name', 'value'=>$attrs['app_name'], 'position'=>'both')
            );
            $date_like = array_merge($date_like, $like_name);
        }
        if (isset($date_like)) {
            $params['like'] = array(
                'many' => TRUE,
                'data' => $date_like,
            );
        }

        $data = $this->getData($params);

        foreach ($data as & $row) {
            if(!empty($row['start_date']) && !empty($row['end_date'])){
                $start_date = new DateTime($row['start_date']);
                $row['start_date'] = $start_date->format('Y-m-d');
                $end_date = new DateTime($row['end_date']);
                $row['end_date'] = $end_date->format('Y-m-d');
                $row['room_date'] = $row['start_date'].' ~ '.$row['end_date'];
            }else{
                $row['room_date'] = '';
            }
            $cre_date = new DateTime($row['cre_date']);
            $row['cre_date'] = $cre_date->format('Y-m-d');
        }
        // jd($this->db->last_query());
        return $data;
    }

    public function getChoices()
    {
        $choices = array();
        $attrs['conditions'] = array(
            'enable' => '1',
        );
        $data = $this->getList($attrs);
        foreach ($data as $row) {
            $choices[$row['item_id']] = $row['name'];
        }
        return $choices;
    }

    public function get_print_room($conditions=array())
    {

        $where = "1";
        if ($conditions['appi_id'] != ""){
          $where .= " and A.APPI_ID = '{$conditions['appi_id']}'";
        }
        if ($conditions['app_name'] != ""){
          $where .= " and B.APP_NAME LIKE '%{$conditions['app_name']}%'";
        }
        /*if ($conditions['start_date'] != "" && $conditions['end_date'] == ""){
          $where .= " and A.APP_DATE >= '{$conditions['start_date']}'";
        }
        if ($conditions['start_date'] == "" && $conditions['end_date'] != ""){
          $where .= " and A.APP_DATE <= '{$conditions['end_date']}'";
        }
        if ($conditions['start_date'] != "" && $conditions['end_date'] != ""){
          $where .= " and A.APP_DATE between '{$conditions['start_date']}' and '{$conditions['end_date']}'";
        }*/
        if ($conditions['start_date'] != "" && $conditions['end_date'] == ""){
          $where .= " and A.cre_date >= '{$conditions['start_date']}'";
        }
        if ($conditions['start_date'] == "" && $conditions['end_date'] != ""){
          $where .= " and A.cre_date <= '{$conditions['end_date']}'";
        }
        if ($conditions['start_date'] != "" && $conditions['end_date'] != ""){
          $where .= " and A.cre_date between '{$conditions['start_date']}' and '{$conditions['end_date']}'";
        }

        $sql = "SELECT A.APP_DATE, DATE_FORMAT(IFNULL(C.APP_SDATE,A.APP_DATE),'%Y-%m-%d') AS APP_SDATE, DATE_FORMAT(IFNULL(C.APP_EDATE,A.APP_DATE),'%Y-%m-%d') AS APP_EDATE,
        B.APP_NAME, A.APP_REASON, B.CONTACT_NAME, B.TEL,
        D.price_a, D.price_b, D.price_c, D.EXPENSE, A.OTHER_EXPENSE, A.TOTAL_EXPENSE, (A.PEOPLE*A.DAYS) AS PDS, A.BILLNO FROM appinfo A
        LEFT JOIN applicant B ON A.APP_ID = B.APP_ID
        LEFT JOIN (SELECT APPI_ID, MIN(USE_DATE) AS APP_SDATE, MAX(USE_DATE) AS APP_EDATE FROM room_use WHERE APPI_ID IS NOT NULL GROUP BY APPI_ID) C ON A.APPI_ID = C.APPI_ID
        LEFT JOIN
        (
          SELECT APPI_ID, SUM(price_a) AS price_a, SUM(price_b) AS price_b, SUM(price_c) AS price_c, SUM(EXPENSE) AS EXPENSE FROM
          (
            SELECT APPI_ID, USE_DATE, ROOM_ID, CAT_ID
            ,((IFNULL(price_a,0) * DISCOUNT1 * DISCOUNT2) + (IFNULL(price_a,0) * ADDCOUNT)) * NUM AS price_a
            ,((IFNULL(price_b,0) * DISCOUNT1 * DISCOUNT2) + (IFNULL(price_b,0) * ADDCOUNT)) * NUM AS price_b
            ,IFNULL(price_c,0) * NUM AS price_c, EXPENSE
            FROM
            (
              SELECT a.APPI_ID, a.USE_DATE, a.CAT_ID, a.ROOM_ID, a.NUM, b.price_a, b.price_b, b.price_c, c2.IS_PUBLIC
              ,IFNULL(a.DISCOUNT,1) AS DISCOUNT1
              ,CASE WHEN a.CAT_ID NOT IN ('02','04') AND c2.IS_PUBLIC = 'Y' THEN 0.8 ELSE 1 END AS DISCOUNT2
              ,CASE WHEN a.CAT_ID NOT IN ('02','04') AND DAYOFWEEK(a.USE_DATE) IN ('1','7') THEN 0.2 ELSE 0 END AS ADDCOUNT
              ,a.EXPENSE
              FROM room_use a
              LEFT JOIN venue_time b ON a.ROOM_ID = b.ROOM_ID AND a.USE_PERIOD = b.price_t
              LEFT JOIN appinfo c1 ON a.APPI_ID = c1.APPI_ID
              LEFT JOIN applicant c2 ON c1.APP_ID = c2.APP_ID
              WHERE a.APPI_ID IS NOT NULL
            ) e
          ) f GROUP BY APPI_ID
        ) D ON A.APPI_ID = D.APPI_ID
        WHERE {$where} ORDER BY A.APP_DATE";

        $query = $this->db->query($sql);
        $data = $query->result_array();

        
        return $data;
    }

    public function get_print_accounting($conditions=array())
    {
        $params = array(
            'select' => '*',
            'order_by' => '',
        );

        $params['join'] = array(
                    array(
                        'table' => '(SELECT app_id, APP_NAME, CONTACT_NAME, TEL, FAX, EMAIL, ADDR, ZONE from applicant ) as applicant',
                        'condition'=>'applicant.app_id = appinfo.app_id',
                        'join_type'=>'left',
                    ),
                    array(
                        'table' => '(SELECT appi_id as appiid, min(use_date) as APP_SDATE, max(use_date) as APP_EDATE from room_use) as room_use',
                        'condition'=>'room_use.appiid = appinfo.appi_id',
                        'join_type'=>'left',
                    ),

                );
        if (isset($conditions)) {
            $params['conditions'] = $conditions;
        }

        $data = $this->getData($params);
        // jd($data,1);
        foreach ($data as & $row) {
            if(!empty($row['APP_SDATE']) && !empty($row['APP_EDATE'])){
                $row['room_date'] = $row['APP_SDATE'].' ~ '.$row['APP_EDATE'];
            }else{
                $row['room_date'] = '';
            }
            $sale_time_end = new DateTime($row['cre_date']);
            $row['cre_date'] = $sale_time_end->format('Y-m-d');
        }

        return $data['0'];
    }

    public function get_room($appi_id=NULL)
    {
        $sql = "SELECT A.*, B2.IS_PUBLIC, C.ROOM_COUNTBY, C.ROOM_NAME AS ROOM_NAME, D.NAME AS CAT_NAME, E.remark AS USE_NAME, F.price_a*NVL(A.DISCOUNT,1) as price_a, F.price_b*NVL(A.DISCOUNT,1) as price_b, F.price_c*NVL(A.DISCOUNT,1) as price_c FROM
        (
        SELECT MAX(USE_DATE)-MIN(USE_DATE)+1 as DIFF_DAY,MIN(USE_DATE) AS APP_DATE_S, MAX(USE_DATE) AS APP_DATE_E, APPI_ID, CAT_ID, ROOM_ID, USE_PERIOD, UNIT, NUM,
        SUM(EXPENSE) AS EXPENSE, DISCOUNT, GROUPNUM, GROUPNOTE, SUM(WEEKEND) AS WEEKEND
        FROM (SELECT a.*, CASE WHEN DAYOFWEEK(a.USE_DATE) IN ('1','7') THEN 1 ELSE 0 END AS WEEKEND FROM room_use a) a
        WHERE APPI_ID = '{$appi_id}' GROUP BY
        APPI_ID, CAT_ID, ROOM_ID, USE_PERIOD, UNIT, NUM, DISCOUNT, GROUPNUM, GROUPNOTE
        ) A
        LEFT JOIN appinfo B1 ON A.APPI_ID = B1.APPI_ID
        LEFT JOIN applicant B2 ON B1.APP_ID = B2.APP_ID
        LEFT JOIN venue_information C ON A.ROOM_ID = C.ROOM_ID
        LEFT JOIN place_category D ON C.room_type = D.ITEM_ID
        LEFT JOIN reservation_time E ON A.USE_PERIOD = E.ITEM_ID
        LEFT JOIN venue_time F ON A.ROOM_ID = F.ROOM_ID AND A.USE_PERIOD = F.price_t
        ORDER BY CAT_ID, ROOM_ID, APP_DATE_S, USE_PERIOD";

        $query = $this->db->query($sql);
        $data = $query->result_array();

       

        foreach($data as & $row){
            $row['APP_DATE_S'] = substr($row['APP_DATE_S'], 0, 10);
            $row['APP_DATE_E'] = substr($row['APP_DATE_E'], 0, 10);
            $conditions = array(
                'appi_id' => $row['appi_id'],
                'room_id' => $row['room_id'],
                'use_date >=' => $row['APP_DATE_S'],
                'use_date <=' => $row['APP_DATE_E'],
                'num' => $row['num'],
                'use_period' => $row['use_period'],
            );
            $this->db->from('room_use');
            $this->db->where($conditions);
            $row['date_num'] = $this->db->count_all_results();
            
            $not_discount = 'N';
            if($row['cat_id'] == '02' || $row['cat_id'] == '04'){
                $not_discount = 'Y';
            }
            $discount1 = '1';
            $discount2 = '1';
            $discount3 = '0';
            if($row['discount'] > 0){
                $discount1 = $row['discount'];
            }
            if($not_discount == 'N'){
                if($row['IS_PUBLIC'] == 'Y'){
                    $discount2 = '0.8';
                }
                if(substr($appi_id, 0,8) > 20200811){
                    $row['price_a_sum'] = $row['price_a']*$discount2*($row['date_num']-$row['WEEKEND'])*$row['num'] + ( $row['price_a'] * 1.2 )*$row['num']*$row['WEEKEND'];
                    $row['price_b_sum'] = $row['price_b']*$discount2*($row['date_num']-$row['WEEKEND'])*$row['num'] + ( $row['price_b'] * 1.2 )*$row['num']*$row['WEEKEND'];
                } else{
                    if($row['WEEKEND']!=0){
                        $discount3 = '0.2';
                    }
                    //$row['price_a_sum'] = (((( $row['price_a'] * $discount1* $discount2 ) ) * ( $row['date_num'] - $row['WEEKEND'] )) * $row['num'] ) + (((( $row['price_a'] * $discount1* $discount2 ) + ( $row['price_a'] * $discount3)) * $row['WEEKEND'] ) * $row['num']);//2019-12-19
                    $row['price_a_sum'] = $row['price_a']*$discount2*$row['date_num']*$row['num'] + ( $row['price_a']/$discount1 * $discount3 )*$row['num']*$row['WEEKEND'];
                    //$row['price_b_sum'] = (((( $row['price_b'] * $discount1* $discount2 ) ) * ( $row['date_num'] - $row['WEEKEND'] )) * $row['num'] ) + (((( $row['price_b'] * $discount1* $discount2 ) + ( $row['price_b'] * $discount3)) * $row['WEEKEND'] ) * $row['num']);//2019-12-19
                    $row['price_b_sum'] = $row['price_b']*$discount2*$row['date_num']*$row['num'] + ( $row['price_b']/$discount1 * $discount3 )*$row['num']*$row['WEEKEND'];
                    //die(); 
                }
                
            }else{
                //$row['price_a_sum'] = (( $row['price_a'] * $discount1* $discount2 ) ) * $row['date_num'] * $row['num'];//2019-12-19
                $row['price_a_sum'] = (( $row['price_a']* $discount2 ) ) * $row['date_num'] * $row['num'];
                //$row['price_b_sum'] = (( $row['price_b'] * $discount1* $discount2 ) ) * $row['date_num'] * $row['num'];//2019-12-19
                $row['price_b_sum'] = (( $row['price_b']* $discount2 ) ) * $row['date_num'] * $row['num'];
            }
            
            
            $row['price_c_sum'] = $row['EXPENSE'] - $row['price_a_sum'] - $row['price_b_sum'];
        }
        // jd($data,1);
        
        return $data;


    }


}