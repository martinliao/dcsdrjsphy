<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Student_admit_model extends Common_model
{
    public function getStudentAdmitData($account,$class_no,$class_name,$action,$type
    ,$year,$beaurau_id,$block, $rows="", $offset="")
    {
        $accountInfo = $this->getAccountData($account);
        $check_id =0;
        if(sizeof($accountInfo)>0){
            $check_id =  $accountInfo[0]['ROLE_ID'];
            
        }
        
        // //class_no session or request 判斷 start
        // if(basename($_SERVER['HTTP_REFERER'])!=basename(__FILE__))
        // $class_no = $_REQUEST['class_no'];
        //     $_SESSION['query_class_no']=$_REQUEST['class_no'];
        // end
        $auth_count = strpos($account,"edap");

        //$year       = $_REQUEST['query_year'];
        // $class_name = $_REQUEST['query_class_name'];
        // $class_no   = $_REQUEST['query_class_no'];
        
        if ($type=="") $type ='3';
        
        if ($type=='3') {
            $type_condi = " yn_sel NOT IN ('2', '6', '7') ";
        } else {	
            $type_condi = " yn_sel IN ('2') ";
        }

        $where = '1=1';

        if ($class_no != ""){
                $where .= " AND UPPER(a.class_no) LIKE UPPER(".$this->db->escape("%".addslashes($class_no)."%").") ";
        }
        
        if ($class_name != ""){
            $where .= " and upper(a.class_name) like upper(".$this->db->escape("%".addslashes($class_name)."%").")";
        }
        
        if($year=='')
        {
            $year = date('Y')-1911;
        }
        else {
            $where .= " and YEAR = ".$this->db->escape(addslashes($year))."";
        }
        $sql = $this->actionSearch($type_condi,$beaurau_id,$where);
        
        if ($check_id==10 || $check_id==50 || $check_id == 'WW'){
            if ($block!="")
            {
                if ($block!=""){
                    $where .= "AND b.NAME LIKE ".$this->db->escape(addslashes("%".$block."%"));
                }
                $sql = $this->isblock($type_condi,$where); 
            }
            else{
                $sql = $this->noblock($type_condi,$where); 
            }
        }

        $limit = "";
        if($rows != "" && $offset != "") {
          $limit = " limit " . intVal($rows) . " offset " . intVal($offset);
        }
        else if($rows != "") {
          $limit = " limit " . intVal($rows);
        }

        $sql = $sql . $limit;

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }


    public function getAccountData($account){
       // $sql = "select A.ROLE_ID
       // from BS_user V,account_role A,role_right R
       // where V.USERNAME='{$account}' AND R.FUN_ID='Students_transaction_bureaus' AND V.USERNAME=A.ID AND A.ROLE_ID=R.ROLE_ID";

        $sql = "select A.ROLE_ID
        from BS_user V,account_role A
        where V.USERNAME=".$this->db->escape(addslashes($account))." AND V.USERNAME=A.USERNAME ";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);
    }

    public function getBeaurauId($account){
        $sql="select bureau_id as beaurau_id from BS_user where username = ".$this->db->escape(addslashes($account))." ";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query);

    }
    
    public function actionSearch($type_condi,$beaurau_id,$where){
        $sql = "select
                * 
            from
                (
                    select 
                        a.*, b.name as description, v.name as first_name, c.ADD_VAL1, 
                        (
                            select count(*)  
                            from online_app o 
                            left join BS_user v on o.id=v.idno  
                            where o.year=a.year and o.class_no=a.class_no and o.term=a.term and ".$type_condi." and v.bureau_id=".$this->db->escape(addslashes($beaurau_id))."
                        ) as apply_count,
                        (
                            select count(*)
                            from online_app 
                            where year=a.year and class_no=a.class_no and term=a.term and ".$this->db->escape(addslashes($type_condi))."
                        ) as tapply_count,
                        (
                            select count(*)
                            from mail_log
                            where year=a.year and class_no=a.class_no and term=a.term and MAIL_TYPE='3'
                        )  as mail_count 
                    from `require` a 
                    LEFT  JOIN second_category b ON a.beaurau_id=b.item_id and b.parent_id=a.type
                    left join BS_user v on a.worker = v.idno 
                    left join code_table c on c.TYPE_ID='26' and v.idno =  c.ITEM_ID 
                    where  {$where} order by a.year, a.class_no, a.term, a.start_date1
                ) as zz
            where 
                mail_count >0 or (CO_OPEN_MEMBER_SHEET = 'Y'
                and (date_format( NOW(), 'yyyy-mm-dd') between date_format(CO_SHEET_OPEN_SDATE, 'yyyy-mm-dd') and date_format(CO_SHEET_OPEN_EDATE, 'yyyy-mm-dd'))) ";
        
        
        return $sql;
    }

    public function isblock($type_condi,$where){
        $sql = "select
					* 
		    	from
			    	(
			    		select 
			    			a.*, b.name as description, v.name as first_name, c.ADD_VAL1, 
			    			(
			    				select count(*)  
			    				from online_app o 
			    				left join BS_user v on o.id=v.idno  
			    				where o.year=a.year and o.class_no=a.class_no and o.term=a.term and ".$type_condi."
			    			) as apply_count,
			    			(
			    				select count(*)
			    				from online_app 
			    				where year=a.year and class_no=a.class_no and term=a.term and ".$type_condi."
			    			) as tapply_count,
			    			(
			    				select count(*)
			    				from mail_log
			    				where year=a.year and class_no=a.class_no and term=a.term and MAIL_TYPE='3'
			    			)  as mail_count 
			    		from `require` a 
                        LEFT  JOIN second_category b ON a.beaurau_id=b.item_id and b.parent_id=a.type
                        left join BS_user v on a.worker = v.idno 
                        left join code_table c on c.TYPE_ID='26' and v.idno =  c.ITEM_ID 
			    		where  {$where} order by a.year, a.class_no, a.term, a.start_date1
			    	) as zz
		        where 
		        	mail_count >0 or (CO_OPEN_MEMBER_SHEET = 'Y'
                    and (date_format( NOW(), 'yyyy-mm-dd') between date_format(CO_SHEET_OPEN_SDATE, 'yyyy-mm-dd') and date_format(CO_SHEET_OPEN_EDATE, 'yyyy-mm-dd'))) ";
        return $sql;
    }

    public function noblock($type_condi,$where){
        $sql = $sql = "select
                * 
            from
                (
                    select 
                        a.*, b.name as description, v.name as first_name, c.ADD_VAL1, 
                        (
                            select count(*)  
                            from online_app o 
                            left join BS_user v on o.id=v.idno  
                            where o.year=a.year and o.class_no=a.class_no and o.term=a.term and ".$this->db->escape(addslashes($type_condi))."
                        ) as apply_count,
                        (
                            select count(*)
                            from online_app 
                            where year=a.year and class_no=a.class_no and term=a.term and ".$this->db->escape(addslashes($type_condi))."
                        ) as tapply_count,
                        (
                            select count(*)
                            from mail_log
                            where year=a.year and class_no=a.class_no and term=a.term and MAIL_TYPE='3'
                        )  as mail_count 
                    from `require` a 
                    LEFT  JOIN second_category b ON a.beaurau_id=b.item_id and b.parent_id=a.type
                    left join BS_user v on a.worker = v.idno 
                    left join code_table c on c.TYPE_ID='26' and v.idno =  c.ITEM_ID 
                    where  {$where} order by a.year, a.class_no, a.term, a.start_date1
                ) as zz
            where 
                mail_count >0 or (CO_OPEN_MEMBER_SHEET = 'Y'
                and (date_format( NOW(), 'yyyy-mm-dd') between date_format(CO_SHEET_OPEN_SDATE, 'yyyy-mm-dd') and date_format(CO_SHEET_OPEN_EDATE, 'yyyy-mm-dd'))) ";
        return $sql;
    }

    // //CSV套表~~~~
    // public function CSV($check_id,$yearlist,$class_no,$class_name,$type){
    //     $data["check_id"] = $check_id;
    //     $data['Get_Year_List'] = $yearlist;//Set_Query_Year_List(); // define in init.inc.php
    //     //$data["query_year"] = $_REQUEST['query_year'];
    //     $data["query_class_no"] = $class_no;
    //     $data["query_class_name"] = $class_name;
    //     $data["query_type"] = $type;
    //     $data["search_start_date"] = $startdate;
    //     $data["search_end_date"] = $pdate_e;
    //     $data['block'] = $block;
    //     $data['year']=$year;
    //     //指定要嵌套的資料
    //     //$smarty->assign('year_select_list', Set_Query_Year_List()); // define in init.inc.php
    //     $smarty->assign('data', $data);
    //     //$smarty->assign('year_select_list', Set_Query_Year_List());
    //     $smarty->assign('rowsCount', count($data['rows']));
    //     $smarty->assign('currentDateTime', date('Y/m/d H:i'));
    //     echo $smarty->fetch('search10.tpl');
    // }
   
}
