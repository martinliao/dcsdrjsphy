<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class classroom_model extends MY_Model
{
    public $table = 'venue_information';
    public $pk = 'ROOM_ID';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);
    }
    public function getList($attrs=array()){
        $sql = " A.*, C.room_name as NAME, (CASE WHEN B.ROOM_ID IS NOT NULL THEN 'Y' END) IS_SEAT FROM 
                   (SELECT DISTINCT ROOM_ID FROM room_use WHERE YEAR = '".$attrs['year']."' AND CLASS_ID = '".$attrs['class_no']."' AND TERM = '".$attrs['term']."') A 
                   LEFT JOIN (SELECT DISTINCT ROOM_ID FROM classroom_seat) B ON A.ROOM_ID = B.ROOM_ID 
                   LEFT JOIN venue_information C ON A.ROOM_ID = C.ROOM_ID ORDER BY A.ROOM_ID";
        $this->db->select($sql,FALSE);
        $query = $this->db->get();
        $data = $query->result_array(); 
        return $data;
    }
    public function getRoomseat($attrs=array(),$select=NULL){
        if(!is_null($select)){
            $this->db->select($select);
        }else{
            $this->db->select("*");
        }
        if(count($attrs)==0) return false;
        $this->db->from("classroom_seat");
        foreach ($attrs as $key => $value) {
            if($key == 'special'){
                $this->db->where($value);
            }elseif($value!==''){
                $this->db->where($key, $value);
            }
        }
        
        $query = $this->db->get();
        $data = $query->row_array(); 
        return $data;
    }
    public function loadingseat($attrs){

        $sql = " z1.*, z2.ST_NO, z2.`name` FROM classroom_seat as z1
                        LEFT JOIN
                        (
                                SELECT b1.X, b1.Y, b2.st_no, b2.`name` FROM     
                                (
                                    select  @i := @i + 1 as KEY1 ,a1.* from
                                    (
                                    SELECT * FROM classroom_seat WHERE room_id = '".$attrs['room_id']."' AND IS_SET = 'Y' 
                                    ) as a1,(select @i := 0) temp ORDER BY ".$attrs['seatOrder']."
                                ) as b1 
                                LEFT JOIN
                                (
                                            SELECT a.ID, a.st_no, b.`name` FROM online_app as a
                                            LEFT JOIN `BS_user` as b ON a.ID = b.idno
                                            WHERE a.YEAR = '".$attrs['year']."' AND a.CLASS_NO = '".$attrs['class_no']."' AND a.TERM = '".$attrs['term']."' AND a.YN_SEL in ('1','3','4','5','8') ORDER BY a.ST_NO
                                )as b2 on b1.KEY1 = b2.st_no
                        ) as z2 on z1.X = z2.X AND z1.Y = z2.Y 
                        where z1.room_id = '".$attrs['room_id']."' ORDER BY z1.X, z1.Y" ;
        $this->db->select($sql,FALSE);
        $query = $this->db->get();
        $data = $query->result_array(); 
        return $data;
    }
}