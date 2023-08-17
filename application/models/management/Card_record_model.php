<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
define("DELAY_TIME", 20);
class Card_record_model extends MY_Model
{
    public $table = 'require';
    //public $pk = 'seq_no';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }

    /*遲到起訖算時間*/
    public function periodInfoCompute ($periods,$year,$classNo,$term,$date)
	{
		$noon_time = "1300";
		$periodInfo = array(
			'start1' => 0,	       //第一段上課開始
			'end1' => 0,		   //第一段上課結束
			'start2' => 0,	       //第二段上課開始
			'end2' => 0,		   //第二段上課結束
			'checkin_time' => 0,   //上課開始
			'checkout_time' => 0,  //上課結束
            'hrs1' => 0,
            'hrs2' => 0,
        );
        
        //var_dump($periods);
		if (count($periods)>0) {
			//$periodInfo['checkin_time'] = substr($periods[0]['CHECKIN_TIME'],0 ,2) * 3600 + substr($periods[0]['CHECKIN_TIME'],2 ,2) * 60;
			//$periodInfo['checkout_time'] = substr($periods[0]['CHECKOUT_TIME'],0 ,2) * 3600 + substr($periods[0]['CHECKOUT_TIME'],2 ,2) * 60;
            $periodInfo['checkin_time'] = substr($periods[0]['from_time'],0,2)*3600 + substr($periods[0]['from_time'],2,2)*60+20*60;
            $periodInfo['checkout_time'] =  substr($periods[count($periods)-1]['to_time'],0,2)*3600 + substr($periods[count($periods)-1]['to_time'],2,2)*60;


			// 第一段上課開始 （轉秒）
			$periodInfo['start1'] = substr($periods[0]['from_time'],0 ,2) * 3600 + substr($periods[0]['from_time'],2 ,2)*60;
			
			if (count($periods)>=2 && $periods[0]['from_time']<$noon_time && $periods[count($periods)-1]['to_time']>=$noon_time)  //上下午有課
			{
				//echo "here"."<br>\r\n";
				foreach ($periods as $period) 
				{
					if ($period['from_time'] >= $noon_time)
					{
						//第二段上課開始 
						//echo ",sec2 start:".$period['FROM_TIME'];
						$periodInfo['start2'] = substr($period['from_time'],0 ,2) * 3600 + substr($period['from_time'],2 ,2)*60;
						//第一段上課結束
						$periodInfo['end1'] = substr($last_period['to_time'],0 ,2) * 3600 + substr($last_period['to_time'],2 ,2)*60;
						//echo ",sec1 end:".$last_period['TO_TIME']."<br>\r\n";
						break;
					}
					$last_period = $period;
				}
				
				// 第二段上課結束 （轉秒）
				$periodInfo['end2'] = substr($periods[count($periods)-1]['to_time'],0 ,2) * 3600 + substr($periods[count($periods)-1]['to_time'],2 ,2)*60;
			} 
			else 
			{	//第一段上課結束
				$periodInfo['end1'] = substr($periods[count($periods)-1]['to_time'],0 ,2) * 3600 + substr($periods[count($periods)-1]['to_time'],2 ,2)*60;
			}
           
			foreach ($periods as $period) 
			{
				if ($period['from_time'] >= $noon_time)
				{
					$periodInfo['hrs2'] += $period['HRS'];
				} else {
					$periodInfo['hrs1'] += $period['HRS'];
				}
			}
			
		}
		$periodInfo['detail'] = $periods;
        //var_dump($periodInfo);
		return $periodInfo;
    }
    public function getCardLog($attrs=array())
    {
        $this->db->select('count(1) as cnt');
        $this->db->where('year',$attrs['year']);
        $this->db->where('class_no',$attrs['class_no']);
        $this->db->where('term',$attrs['term']);
        $this->db->where('gid',$attrs['gid']);
        $this->db->where('pass_time',$attrs['pass_time']);
        $cnt=$this->db->get('card_log');
        $cnt=$cnt->result_array();
        return $cnt[0]['cnt'];
    }	
    //學生簽到簽退時間
    public function getDoorLogs($id,$use_date,$year,$term,$class_no)
    {   
        $this->db->select('*,pass_time,type');
        
        $this->db->where('gid',$id);
        $this->db->where('use_date',$use_date);
        $this->db->where('year',$year);
        $this->db->where('term',$term);
        $this->db->where('class_no',$class_no);
        $query=$this->db->get('card_log');
        $query=$query->result_array();


        /*var_dump($query);
        var_dump($use_date);
        var_dump($id);
        var_dump($class_no);
        var_dump($term);
        var_dump($year);
        die();*/




        $oriDoorlogs = array();
        $doorlogs = array();
        //var_dump($doorlogs);
        //var_dump(strlen($query[0]['pass_time']));

        for ($i=0;$i<count($query);$i++) {
            if (!empty($query[$i])) {
                if ($query[$i]['type']=='') {
                    $oriDoorlogs[] = substr($query[$i]['pass_time'], 0, 2).":".substr($query[$i]['pass_time'], 2, 2).":".substr($query[$i]['pass_time'], 4, 2)."(<font color='blue'>卡</font>)";
                }else if($query[$i]['type']=='補登'){
                    $oriDoorlogs[] = substr($query[$i]['pass_time'], 0, 2).":".substr($query[$i]['pass_time'], 2, 2).":".substr($query[$i]['pass_time'], 4, 2)."(<font color='red'>補</font>)";
                }else if($query[$i]['type']=='台北通'){
                    $oriDoorlogs[] = substr($query[$i]['pass_time'], 0, 2).":".substr($query[$i]['pass_time'], 2, 2).":".substr($query[$i]['pass_time'], 4, 2)."(<font color='blue'>台北通</font>)";
                }else if($query[$i]['type']=='NFC'){
                    $oriDoorlogs[] = substr($query[$i]['pass_time'], 0, 2).":".substr($query[$i]['pass_time'], 2, 2).":".substr($query[$i]['pass_time'], 4, 2)."(<font color='blue'>NFC</font>)";
                }else {
                    $oriDoorlogs[] = substr($query[$i]['pass_time'], 0, 2).":".substr($query[$i]['pass_time'], 2, 2).":".substr($query[$i]['pass_time'], 4, 2)."(<font color='blue'>平</font>)";
                }
                $doorlogs[] = intval(substr($query[$i]['pass_time'], 0, 2))*3600 + intval(substr($query[$i]['pass_time'], 2, 2))*60 + intval(substr($query[$i]['pass_time'], 4, 2));
                //$query[$i]['oriDoorlogs']= $oriDoorlogs;
                //$query[$i]['doorlogs']= $doorlogs;
            }else{
                //$query['oriDoorlogs']= null;
                //$query['doorlogs']= null;
                break;
            }
        }
        $passtime = array('oriDoorlogs'=>$oriDoorlogs,
                          'doorlogs'=>$doorlogs);
        /*var_dump($query);
        var_dump($use_date);
        var_dump($id);
        var_dump($class_no);
        var_dump($term);
        var_dump($year);
        die();*/
        //var_dump($passtime)
        return $passtime;
        
    }
    //計算簽到簽退時間
    public function evaluateLogTime($oriDoorlogs,$doorlogs,$checkin_time,$checkout_time,$start1,$end1,$end2,$hrs1,$hrs2)
    {
        if (count($doorlogs)==2) {
			$arr['DOORLOGS_STR'] = implode('<br />', $oriDoorlogs);
		} else {
			$arr['DOORLOGS_STR'] = implode('<br />', $oriDoorlogs);
        }
        //var_dump($doorlogs);
		// 重新算 Login/Logout Time '<font color="#ff0000">' '</font>'
		//echo "count doorlogs:".count($doorlogs);
		//echo "start1:".$periodInfos[$index]['start1'];
		if (count($doorlogs)>0) {
			// 去除秒
			$firsttime = floor($doorlogs[0]/60)*60;
			
			if(count($doorlogs)<2 || ($checkin_time>0 && $checkin_time<$firsttime) || 
			   ($checkin_time==0 && ($start1+DELAY_TIME*60)<$firsttime) )
				$arr['LOGIN_TIME'] = sprintf('%02d:%02d:%02d', floor($doorlogs[0]/3600), (floor(($doorlogs[0]%3600)/60)), ($doorlogs[0]%60));
			else
				$arr['LOGIN_TIME'] = sprintf('%02d:%02d:%02d', floor($doorlogs[0]/3600), (floor(($doorlogs[0]%3600)/60)), ($doorlogs[0]%60));
		} else {
			$arr['LOGIN_TIME'] = '';
		}
		if (count($doorlogs)>1) {
			$tmp = $doorlogs[count($doorlogs)-1];
			$log_out = sprintf('%02d:%02d:%02d', floor($tmp/3600), (floor(($tmp%3600)/60)), ($tmp%60));
			$checkout_time = sprintf('%02d:%02d:%02d', floor($checkout_time/3600), (floor(($checkout_time%3600)/60)), ($checkout_time%60));
			
			if ($end2>0)	{//上下午都有課
				$end_time = $end2;
			} else { //只有上午或下午有課
				$end_time = $end1;
			}
			if ($end_time>0 && $checkout_time>0 && $tmp<$checkout_time) {
				$arr['LOGOUT_TIME'] = sprintf('%02d:%02d:%02d', floor($tmp/3600), (floor(($tmp%3600)/60)), ($tmp%60));
			}	
			else if ($end_time>0 && $checkout_time ==0 && $tmp<$end_time) {
				$arr['LOGOUT_TIME'] = sprintf('%02d:%02d:%02d', floor($tmp/3600), (floor(($tmp%3600)/60)), ($tmp%60));
			}	
			else {	
				$arr['LOGOUT_TIME'] = sprintf('%02d:%02d:%02d', floor($tmp/3600), (floor(($tmp%3600)/60)), ($tmp%60));
			}	
		} else {
			$arr['LOGOUT_TIME'] = '';
		}
		//$arr['SUM_HOURS'] = $periodInfos[$index]['hours'];
        $arr['SUM_HOURS'] = $hrs1+$hrs2;;
        //var_dump($hrs1);
        //var_dump($hrs2);
		//$arr['UNSTUDY_HOURS'] = $unstudyHours;
        $arr['DOORLOGS'] = $doorlogs;
        $condtion = array('DOORLOGS_STR'=> $arr['DOORLOGS_STR'],
                          'LOGIN_TIME' => $arr['LOGIN_TIME'],
                          'LOGOUT_TIME' =>$arr['LOGOUT_TIME'],
                          'SUM_HOURS' => $arr['SUM_HOURS'],
                          'DOORLOGS' =>$arr['DOORLOGS']);
        return $condtion;
    }
    //計算未到時數
    public function getUnstudyHour($id,$year,$class_no,$term,$use_date,$login,$logout,$checkin,$checkout,$sumhours)
    {
        
        $this->db->select('*');
        $this->db->where('gid',$id);
        $this->db->where('use_date',$use_date);
        $this->db->where('class_no',$class_no);
        $this->db->where('term',$term);
        $this->db->where('year',$year);
        $query=$this->db->get('card_log');
        $query=count($query->result_array());

        $late_time=substr($checkin,0,2)*3600+substr($checkin,2,2)*60;
        $early_out_time=substr($checkout,0,2)*3600+substr($checkout,2,2)*60;

        if($login!=null && $logout!=null){
            $final_login=substr($login,0,2)*3600+substr($login,3,2)*60;
            $final_logout=substr($logout,0,2)*3600+substr($logout,3,2)*60;
        }
        
        $sum_late=0;

        if($query>=2){
            if($final_login>$late_time && $final_logout<=$early_out_time){
                $temp=$final_login-$late_time+$early_out_time-$final_logout;
                $temp=ceil($temp/3600);
                $sum_late+=$temp;
            }

            if($final_logout<$early_out_time && $final_login<=$late_time){
                $temp2=$early_out_time-$final_logout;
                $temp2=ceil($temp2/3600);
                $sum_late+=$temp2;
                //var_dump($sum_late);
            }

            if($final_login>$late_time && $final_logout>$early_out_time){
                $temp=$final_login-$late_time;
                $temp=ceil($temp/3600);
                $sum_late+=$temp;
            }
            
        }
        if($sum_late>$sumhours){
            $sum_late=$sumhours;
        }

        
        $this->db->select('ru.*,p.*');
        $this->db->where('ru.use_date',$use_date);
        $this->db->where('ru.year',$year);
        $this->db->where('ru.class_id',$class_no);
        $this->db->where('ru.term',$term);
        $course_code=['O00001','O00002','O00003','O00004','O00005'];
        $this->db->where_not_in('p.course_code',$course_code);
        $this->db->group_by('ru.year,ru.class_id,ru.term,p.name,p.from_time,p.to_time');
        $this->db->join('periodtime as p','p.id=ru.use_period and p.year=ru.year and p.class_no=ru.class_id and p.term=ru.term and p.course_date=ru.use_date and p.room_id=ru.room_id');
        $class=$this->db->get('room_use as ru');
        $class=$class->result_array();
        //var_dump($class);
        //die();
        $test103=0;
        if($query>=2){
        for($i=0;$i<count($class);$i++){
            $from_time=substr($class[$i]['from_time'],0,2)*3600+ substr($class[$i]['from_time'],2,2)*60+20*60;
            $to_time=substr($class[$i]['to_time'],0,2)*3600+ substr($class[$i]['to_time'],2,2)*60;

            if($from_time<$final_login){
                $test100=$final_login-$from_time;
                $test100=ceil($test100/3600);
                //var_dump($test100);
                //echo $i;
                if($test100>$class[$i]['hrs']){
                    $test103+=$class[$i]['hrs'];
                }else{
                    $test103+=$test100;
                }
                    
            }

            if($to_time>$final_logout){
                $test101=$to_time-$final_logout;
                $test101=ceil($test101/3600);
                //var_dump($test101);
                //echo $i;
                if($test101>$class[$i]['hrs']){
                    $test103+=$class[$i]['hrs'];
                    //var_dump($test103);
                }else{
                     $test103+=$test101;
                }
               
                //echo 'hello';
            }

            }
            if($test103>$sumhours){
                $test103=$sumhours;
            }
        }else{
            $test103=$sumhours;
        }
        

        return $test103;
    }
    /*找出學生重複修課*/
    public function getCsum($id,$use_date)
    {
        $this->db->select('online_app.id,room_use.use_date,online_app.yn_sel,online_app.class_no,online_app.term')
                ->join('room_use','room_use.year = online_app.year AND room_use.class_id = online_app.class_no AND room_use.term = online_app.term
                       ','inner');
        $this->db->distinct('year,class_no,term');
        $array=[1,3,4,8];
        $this->db->where_in('yn_sel',$array);
        $this->db->where('online_app.id',$id);
        $this->db->where('room_use.use_date',$use_date);  
        $query=$this->db->get('online_app');
        $query=$query->result_array();
        //var_dump($query);
        $Csum = count($query);
        return $Csum;
        
    }
    /*找出學生重複修課*/
    public function getRemark($csum,$yn_sel)
    {
        if($csum != "1") 
		{
			if($yn_sel == "4") 
			{
				$arr['remark'] = "重複修課, 已退訓";
			}	
			else
			{
				$arr['remark'] = "重複修課";
			}	
		} 
		else 
		{
			if($yn_sel == "4") 
			{
				$arr['remark'] = "已退訓";
            }
            else $arr['remark']='';	
        }
        return $arr['remark'];
    }
    //課堂資訊
    public function getPeroidInfo($year,$class_no,$term,$date)
    {
        $this->db->select('periodtime.name,periodtime.from_time,periodtime.to_time,room_use.hrs as HRS')
            ->join('room_use','periodtime.room_id=room_use.room_id AND periodtime.year=room_use.year AND periodtime.class_no=room_use.class_id
                    AND periodtime.id=room_use.use_period AND periodtime.course_date = room_use.use_date','left');
            /*->join('card_log_settime','periodtime.year = card_log_settime.year AND periodtime.class_no = card_log_settime.class_no AND periodtime.term = card_log_settime.term ,card_log_settime.checkin_time as CHECKIN_TIME,card_log_settime.checkout_time as CHECKOUT_TIME  
                    ','left');*/
        $this->db->distinct('periodtime.name,periodtime.from_time,periodtime.to_time,room_use.hrs as HRS');

        //$this->db->where('card_log_settime.set_date',$date);
        $this->db->where('periodtime.year',$year);
        $this->db->where('periodtime.class_no',$class_no);
        $this->db->where('periodtime.term',$term);
        $this->db->where('periodtime.course_date',$date);
        $code=['O00001','O00003','O00004','O00005','004643','006430','017826','017835','021360'];
        $this->db->where_not_in('periodtime.course_code',$code);
        $this->db->order_by('periodtime.from_time',"asc");
        $query=$this->db->get('periodtime');
        $query=$query->result_array();
        //var_dump($query);     
        return $this->periodInfoCompute($query,$year,$class_no,$term,$date);
    }
    
    /*取得課程詳細內容*/ 
    public function test($attrs=array())
    {
        $params = array(
            'select' => 'require.class_no,require.year,require.class_name,require.term,require.room_code,require.contactor,
                         require.seq_no,require.start_date1,online_app.st_no as st_no,require.worker,
                         require.start_date1,require.end_date1,online_app.id as id,online_app.yn_sel,
                         bureau.name as bureau_name,online_app.group_no as group_no, BS_user.name as user_name,
                         view_code_table.description as title,room_use.use_date as use_date,BS_user.out_gov_name as ou_gov,
                        '
                         ,
            'order_by' => 'st_no,use_date',
        );

        $params['join'] = array(
            array('table' => 'online_app',
                    'condition' => 'require.year=online_app.year AND require.term = online_app.term AND require.class_no = online_app.class_no',
                    'join_type' => 'inner'),
                    array('table' => 'room_use',
                    'condition' => 'room_use.year = online_app.year AND room_use.term = online_app.term AND room_use.class_id = online_app.class_no ',
                    'join_type' => 'left'),
                    array('table' => 'bureau',
                    'condition' => 'bureau.bureau_id=online_app.beaurau_id',
                    'join_type' => 'left'),
                    array('table' => 'BS_user',
                    'condition' => 'online_app.id = BS_user.idno',
                    'join_type' => 'left'),
                    array('table' => 'view_code_table',
                    'condition' => 'view_code_table.item_id = BS_user.job_title AND view_code_table.type_id=02',
                    'join_type' => 'left'),
                    array('table' => 'out_gov',
                    'condition' => 'out_gov.id = BS_user.idno',
                    'join_type' => 'left outer'),
                    //out_gov.ou_gov as ou_gov,
                    
        );
        $params['distinct']='room_use.use_date,room_use.year,room_use.term,room_use.class_id';

        $yn_sel=[1,3,4,8];
        $params['where_in'] = array('field'=>'yn_sel','value'=>$yn_sel);

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }

        $data = $this->getData($params);
        //var_dump($data);
        for ($i=0;$i<count($data);$i++) {
            $query=$this->getPeroidInfo($data[$i]['year'], $data[$i]['class_no'], $data[$i]['term'], $data[$i]['use_date']);

            if (!empty($query)) {
                $data[$i]['start1']=$query['start1'];
                $data[$i]['end1']=$query['end1'];
                $data[$i]['start2']=$query['start2'];
                $data[$i]['end2']=$query['end2'];
                $data[$i]['checkout_time']=sprintf('%02d%02d', floor($query['checkout_time']/3600), floor(($query['checkout_time']%3600)/60));
                $data[$i]['checkin_time']=sprintf('%02d%02d', floor($query['checkin_time']/3600), floor(($query['checkin_time']%3600)/60));
                $data[$i]['hrs1']=$query['hrs1'];
                $data[$i]['hrs2']=$query['hrs2'];
                $data[$i]['detail']=$query['detail'];
            } else {
                $data[$i]['start1']=null;
                $data[$i]['end1']=null;
                $data[$i]['start2']=null;
                $data[$i]['end2']=null;
                $data[$i]['checkout_time']=null;
                $data[$i]['checkin_time']=null;
                $data[$i]['hrs1']=null;
                $data[$i]['hrs2']=null;
                $data[$i]['detail']=null;
            }
        }
            
        for ($k=0;$k<count($data);$k++) {
            $query2=$this->getDoorLogs($data[$k]['id'], $data[$k]['use_date'], $data[$k]['year'], $data[$k]['term'], $data[$k]['class_no']);
            
            if (!empty($query2)) {
                $data[$k]['oriDoorlogs_temp']=$query2['oriDoorlogs'];
                $data[$k]['doorlogs_temp']=$query2['doorlogs'];
                //var_dump($data[$k]['oriDoorlogs_temp']);
                //var_dump($data[$k]['doorlogs_temp']);
            } else {
                $data[$k]['oriDoorlogs_temp']=null;
                $data[$k]['doorlogs_temp']=null;
            }
        }
        for ($i=0;$i<count($data);$i++) {
            $query3=$this->evaluateLogTime($data[$i]['oriDoorlogs_temp'], $data[$i]['doorlogs_temp'], $data[$i]['checkin_time'],$data[$i]['checkout_time'],$data[$i]['start1'] ,$data[$i]['end1'], $data[$i]['end2'], $data[$i]['hrs1'], $data[$i]['hrs2']);
            if (!empty($query3)) {
                $data[$i]['DOORLOGS_STR']=$query3['DOORLOGS_STR'];
                $data[$i]['LOGIN_TIME']=$query3['LOGIN_TIME'];
                $data[$i]['LOGOUT_TIME']=$query3['LOGOUT_TIME'];
                $data[$i]['SUM_HOURS']=$query3['SUM_HOURS'];
                $data[$i]['DOORLOGS']=$query3['DOORLOGS'];
            } else {
                $data[$i]['DOORLOGS_STR']=null;
                $data[$i]['LOGIN_TIME']=null;
                $data[$i]['LOGOUT_TIME']=null;
                $data[$i]['SUM_HOURS']=null;
                $data[$i]['DOORLOGS']=null;
            }
        }
        for ($i=0;$i<count($data);$i++) {
            $query4=$this->getUnstudyHour($data[$i]['id'],$data[$i]['year'],$data[$i]['class_no'],$data[$i]['term'],$data[$i]['use_date'],$data[$i]['LOGIN_TIME'],$data[$i]['LOGOUT_TIME'],$data[$i]['checkin_time'],$data[$i]['checkout_time'],$data[$i]['SUM_HOURS']);
            $data[$i]['unstudyhours']=$query4;
            $Csum=$this->getCsum($data[$i]['id'],$data[$i]['use_date']);
            //var_dump($Csum);
            $data[$i]['csum']=$Csum;
            $query6=$this->getRemark($data[$i]['csum'],$data[$i]['yn_sel']);
            $data[$i]['remark']=$query6;
            $query7=$this->getExpectAttendence($data[$i]['year'],$data[$i]['term'],$data[$i]['class_no']);
            $data[$i]['expect_num'] = $query7;
        }
        //var_dump($data);

        return $data;
    }
   
    /*取得日期當天所有課程*/
    public function getList($attrs=array())
    {
        
        // $this->db->cache_on();
        $params = array(
            'select' => 'require.class_no,require.year,require.class_name,require.term,periodtime.room_id as room_code,require.contactor,require.seq_no,require.start_date1,
                         require.start_date1,require.end_date1,card_record_people_num.enable as enable,periodtime.course_date as use_date,online_app.id as id,
                         card_record_people_num.disabled_people_num as dpn,card_record_people_num.hand_people_num as hpn,require.worker,BS_user.name as worker_name,card_record_people_num.use_date as crpn_use_date
                        '
                         ,
            'order_by' => 'periodtime.room_id',
        );

        $params['join'] = array(
            
            array('table' => 'periodtime',
                    'condition' => 'require.year=periodtime.year AND require.term = periodtime.term AND require.class_no = periodtime.class_no',
                    'join_type' => 'left'),
            array('table' => 'online_app',
                    'condition' => 'require.year=online_app.year AND require.term = online_app.term AND require.class_no = online_app.class_no',
                    'join_type' => 'left'),
                    
            array('table' => 'BS_user',
                    'condition' => "BS_user.idno = require.worker",
                    'join_type' => 'left'),
            array('table' => 'card_record_people_num',
                    'condition' => 'card_record_people_num.year=require.year AND card_record_people_num.term = require.term 
                                    AND card_record_people_num.class_no = require.class_no AND periodtime.course_date=card_record_people_num.use_date',
                    'join_type' => 'left'),
        );

        $params['group_by'] = 'require.year,require.class_no,require.term';

        $condition = [1,3,4,8];
        $params['where_in']= array('field'=>'yn_sel','value'=>$condition);
        /*if (isset($attrs['use_date'])) {
            $params['where_in']= array('field'=>'room_use.use_date','value'=>$attrs['use_date']);
        }*/

        if (isset($attrs['conditions'])) {
            $params['conditions'] = $attrs['conditions'];
        }
        
        $params["escape_query"]["statusSql"]="from_time =(SELECT min( from_time ) FROM periodtime AS r2 WHERE 
                                                                r2.class_no = periodtime.class_no and r2.year=periodtime.year and r2.term=periodtime.term and r2.course_date=periodtime.course_date )";
        

       
       
        $data = $this->getData($params);
        
       
        $data_list1 = array();
        $data_list2 = array();
        $data_total = array();
        for($i=0;$i<count($data);$i++)
        {
            $query2=$this->getExpectAttendence($data[$i]['year'],$data[$i]['term'],$data[$i]['class_no']);
            $query3=$this->getRealAttendence($data[$i]['year'],$data[$i]['term'],$data[$i]['class_no'],$data[$i]['use_date'],$query2);
            $phydisabled=$this->getPhyDisabled($query2);
            $data[$i]['expect_num'] = count($query2);
            $data[$i]['real_num'] = count($query3);
            $data[$i]['phydisabled'] = count($phydisabled);

            if(substr($data[$i]['room_code'], 0, 1) == 'B' || substr($data[$i]['room_code'], 0, 1) == 'C' || substr($data[$i]['room_code'], 0, 1) == 'E'){
                array_push($data_list1, $data[$i]);
            } else {
                array_push($data_list2, $data[$i]);
            }
        }
    
        $data_total = array_merge($data_list1,$data_list2);

        return $data_total;
    }
    /*取得每堂課程的應到人數*/ 
    public function getExpectAttendence($year,$term,$class_no)
    {
        if($year==null && $term==null && $class_no==null){
            $year='1000003';
            $term='1000003';
            $class_no='3e3234234';
        }
        $yn_sel=[1,3,4,8];
        $this->db->select('online_app.id');
        $this->db->where('year',$year);
        $this->db->where('term',$term);
        $this->db->where('class_no',$class_no);
        $this->db->where_in('yn_sel',$yn_sel);
        $this->db->group_by('online_app.id');
        $query=$this->db->get('online_app')->result_array();
        //$query=count($query);
        //var_dump($query);
        //var_dump(count($query));
        //die();
        return $query;
    }
    /*取得每堂課程的實際到達人數*/
    public function getRealAttendence($year,$term,$class_no,$use_date,$query2)
    {       
        $hello=array();
        foreach($query2 as $test){
            $hello[]=$test['id'];
        }
        $this->db->select('gid');
        $this->db->where('year',$year);
        $this->db->where('term',$term);
        $this->db->where('class_no',$class_no);           
        $this->db->where('use_date',$use_date);

        //$this->db->where_in('gid',$hello);

        $this->db->group_by('gid');

        $query=$this->db->get('card_log')->result_array();
          
           
        //$query=count($query);
        //echo"<pre>";
        /*var_dump($year);
        var_dump($term);
        var_dump($class_no);*/
        /*var_dump($hello);
        var_dump($year);
        var_dump($class_no);
        var_dump($use_date);*/
        //var_dump(count($query));
        //die();
        return $query;
    }
    //手動補登學生刷卡紀錄
    public function InsertByImport($data=array())
    {
        $bool=$this->db->insert('card_log',$data);
        if($bool){
            return true;
        }
        return false;
    }
    /*自動取得每堂課程的身障學生人數*/ 
    public function getPhyDisabled($data=array())
    {
        //var_dump($data[0]['id']);
        $condition=array();
        for($i=0;$i<count($data);$i++){
            $condition[$i]=$data[$i]['id'];
        }
        $this->db->select('*');
        //$this->db->where('gid',$data[0]['id']);
        $this->db->where_in('gid',$condition);
        $query=$this->db->get('phydisabled');
        $query=$query->result_array();
        return $query;
    }
    

  
}
