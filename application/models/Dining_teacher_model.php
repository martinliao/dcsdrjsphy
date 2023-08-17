<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Dining_teacher_model extends MY_Model
{
    public $table = 'dining_teacher';
    public $pk = '';

    public function __construct()
    {
        parent::__construct();

    }

    public function spAddDiningTeacher($class_info, $use_date, $user){
        
        // 移除原有紀錄
        $delete_condition = $class_info;
        $delete_condition["date_format(use_date, '%Y-%m-%d')="] = $use_date;
        $this->delete($delete_condition);
        $insert_data = [];
        $insert_data = array_merge($insert_data, $this->getInsertData($class_info, $use_date, $user));
        $insert_data = array_merge($insert_data, $this->getInsertData2($class_info, $use_date, $user));
        $insert_data = array_merge($insert_data, $this->getInsertData3($class_info, $use_date, $user));

        foreach ($insert_data as $data){
            $this->insert($data);
        }

        return "ok";
    }


    public function spAddDiningStudent($class_info, $use_date, $user){

    }
    
    public function getInsertData($class_info, $use_date, $user){
        // 新增
        $sql = "SELECT DISTINCT
                    year,
                    class_id class_no,
                    term,
                    class_name,
                    use_date,
                    'A' dining_type,
                    teacher_id id,
                    name,
                    (CASE WHEN isteacher = 'Y' THEN '1' else '2' end) as type,
                    '{$user}' cre_user,
                    sysdate() cre_date,
                    '{$user}' upd_user,
                    sysdate() upd_date
                FROM
                    (
                    SELECT
                        A.* ,
                        (CASE WHEN class_cate = '2' THEN 'Y' end) as M ,
                        (CASE WHEN '1130' BETWEEN from_time and to_time THEN 'Y' end) as L ,
                        (CASE WHEN '1800' BETWEEN from_time and to_time THEN 'Y' end) as D ,
                        B.Breakfast_Type as M1,
                        (A.no_persons * IFNULL(B.Breakfast_Money, C1.add_val1)) as M2,
                        (round(A.no_persons / 10) * B.Breakfast_Money) as M3 ,
                        B.Lunch_type as L1, 
                        (A.no_persons * IFNULL(B.lunch_money, C2.add_val1)) as L2,
                        (round(A.no_persons / 10) * B.lunch_money) as L3 ,
                        B.Dinner_Type as D1,
                        (A.no_persons * IFNULL(B.dinner_money, C3.add_val1)) as D2,
                        (round(A.no_persons / 10) * B.dinner_money) as D3
                    FROM
                        (
                        SELECT
                            year,
                            class_id,
                            term,
                            class_name,
                            worker,
                            no_persons,
                            class_cate,
                            use_date,
                            teacher_id,
                            isteacher,
                            name,
                            min(from_time) as from_time,
                            max(to_time) as to_time
                        from
                            (
                            SELECT
                                a.year,
                                a.class_id,
                                a.term,
                                a.use_date,
                                a.room_id,
                                a.teacher_id,
                                a.isteacher,
                                c.class_name,
                                c.worker,
                                c.no_persons,
                                c.class_cate,
                                IFNULL(d1.name, d2.name) as name,
                                IFNULL(b1.FROM_TIME, b2.FROM_TIME) as FROM_TIME,
                                IFNULL(b1.TO_TIME, b2.TO_TIME) as TO_TIME
                            FROM
                                (
                                SELECT
                                    distinct 
                                    year,
                                    class_id,
                                    term,
                                    use_date,
                                    use_period,
                                    room_id,
                                    use_id,
                                    teacher_id,
                                    isteacher
                                FROM
                                    room_use
                                WHERE
                                    year = '{$class_info['year']}'
                                    and class_id = '{$class_info['class_no']}'
                                    and term = '{$class_info['term']}'
                                    and str_to_date(use_date, '%Y-%m-%d') = '{$use_date}'
                                    and teacher_id is not null ) a
                            left join periodtime b1 on
                                a.use_period = b1.id
                                and a.year = b1.year
                                and a.class_id = b1.class_no
                                and a.term = b1.term
                                and a.room_id = b1.room_id
                                and a.use_id = b1.course_code
                                and a.use_date = b1.course_date
                            left join periodtime b2 on
                                a.use_period = b2.id
                                and b2.year is null
                                and b2.class_no is null
                                and b2.term is null
                                and b2.room_id is null
                                and b2.course_code is null
                            left join `require` c on
                                a.year = c.year
                                and a.class_id = c.class_no
                                and a.term = c.term
                            left join teacher d1 on 
                                d1.teacher_type = '1'
                                and d1.idno = a.teacher_id
                                and a.isteacher = 'Y'
                            left join teacher d2 on
                                d2.teacher_type = '2'
                                and d2.idno = a.teacher_id
                                and a.isteacher = 'N' ) b
                        group by 
                            year,
                            class_id,
                            term,
                            class_name,
                            worker,
                            no_persons,
                            use_date,
                            class_cate,
                            teacher_id,
                            isteacher,
                            name )A
                    left join dining B on
                        A.year = B.year
                        and A.class_id = B.class_no
                        and A.term = B.term
                    left join dining_code C1 on
                        C1.item_id = 'A'
                    left join dining_code C2 on
                        C2.item_id = 'B'
                    left join dining_code C3 on
                        C3.item_id = 'C' ) d
                WHERE
                    M is not null
        ";

        $query = $this->db->query($sql);       
        return $query->result();        
    }
    /*
        沒人知道這是幹麻的所以 function name 只能暫時這樣命名
    */
    public function getInsertData2($class_info, $use_date, $user){
        $sql = "SELECT DISTINCT
                    year,
                    class_id class_no,
                    term,
                    class_name,
                    use_date,
                    'B' dining_type,
                    teacher_id id,
                    name ,
                    (CASE WHEN isteacher = 'Y' then '1' else '2' end) as type ,
                    '{$user}' cre_user,
                    sysdate() cre_date,
                    '{$user}' upd_user,
                    sysdate() upd_date
                from
                (
                    SELECT
                        A.* ,
                        (CASE WHEN class_cate = '2' then 'Y' end) as M ,
                        (CASE WHEN '1130' BETWEEN from_time AND to_time then 'Y' end) as L ,
                        (CASE WHEN '1800' BETWEEN from_time AND to_time then 'Y' end) as D ,
                        B.Breakfast_Type as M1,
                        (A.no_persons * IFNULL(B.Breakfast_Money, C1.add_val1)) as M2,
                        (round(A.no_persons / 10) * B.Breakfast_Money) as M3 ,
                        B.Lunch_type as L1,
                        (A.no_persons * IFNULL(B.lunch_money, C2.add_val1)) as L2,
                        (round(A.no_persons / 10) * B.lunch_money) as L3 , B.Dinner_Type as D1,
                        (A.no_persons * IFNULL(B.dinner_money,
                        C3.add_val1)) as D2,
                        (round(A.no_persons / 10) * B.dinner_money) as D3
                    from
                        (
                        SELECT
                            year,
                            class_id,
                            term,
                            class_name,
                            worker,
                            no_persons,
                            class_cate,
                            use_date,
                            teacher_id,
                            isteacher,
                            name,
                            min(from_time) as from_time,
                            max(to_time) as to_time
                        from
                            (
                            SELECT
                                a.year,
                                a.class_id,
                                a.term,
                                a.use_date,
                                a.room_id,
                                a.use_period,
                                a.teacher_id,
                                a.isteacher,
                                c.class_name,
                                c.worker,
                                c.no_persons,
                                c.class_cate,
                                IFNULL(d1.name,
                                d2.name) as name,
                                IFNULL(b1.FROM_TIME,
                                b2.FROM_TIME) as FROM_TIME,
                                IFNULL(b1.TO_TIME,
                                b2.TO_TIME) as TO_TIME
                            from
                                (
                                SELECT
                                    distinct room_use.year,
                                    room_use.class_id,
                                    room_use.term,
                                    room_use.use_date,
                                    room_use.use_period,
                                    room_use.room_id,
                                    room_use.use_id,
                                    room_use.teacher_id,
                                    room_use.isteacher
                                from
                                    room_use
                                JOIN classroom ON
                                    room_use.ROOM_ID = classroom.ROOM_ID
                                where
                                    room_use.year = '{$class_info['year']}'
                                    AND room_use.class_id = '{$class_info['class_no']}'
                                    AND room_use.term = '{$class_info['term']}'
                                    AND str_to_date(use_date, '%Y-%m-%d') = '{$use_date}'
                                    AND room_use.teacher_id is not null
                                    AND classroom.BELONGTO = '68000' ) a
                            LEFT JOIN periodtime b1 on
                                a.use_period = b1.id
                                AND a.year = b1.year
                                AND a.class_id = b1.class_no
                                AND a.term = b1.term
                                AND a.room_id = b1.room_id
                                AND a.use_id = b1.course_code
                                AND a.use_date = b1.course_date
                            LEFT JOIN periodtime b2 on
                                a.use_period = b2.id
                                AND b2.year is null
                                AND b2.class_no is null
                                AND b2.term is null
                                AND b2.room_id is null
                                AND b2.course_code is null
                            LEFT JOIN `require` c on
                                a.year = c.year
                                AND a.class_id = c.class_no
                                AND a.term = c.term
                            LEFT JOIN teacher d1 on
                                d1.teacher_type = '1'
                                AND d1.idno = a.teacher_id
                                AND a.isteacher = 'Y'
                            LEFT JOIN teacher d2 on
                                d2.teacher_type = '2'
                                AND d2.idno = a.teacher_id
                                AND a.isteacher = 'N' ) b
                        group by
                            year,
                            class_id,
                            term,
                            class_name,
                            use_period,
                            worker,
                            no_persons,
                            use_date,
                            class_cate,
                            teacher_id,
                            isteacher,
                            name )A
                    LEFT JOIN dining B on
                        A.year = B.year
                        AND A.class_id = B.class_no
                        AND A.term = B.term
                    LEFT JOIN dining_code C1 on
                        C1.item_id = 'A'
                    LEFT JOIN dining_code C2 on
                        C2.item_id = 'B'
                    LEFT JOIN dining_code C3 on
                        C3.item_id = 'C' )c
                where
                L is not null";
        $query = $this->db->query($sql);       
        // dd($sql);
        return $query->result(); 
    }

    public function getInsertData3($class_info, $use_date, $user){
        $sql = "SELECT DISTINCT
                    year,
                    class_id class_no,
                    term,
                    class_name,
                    use_date,
                    'C' dining_type,
                    teacher_id id,
                    name ,
                    (CASE WHEN isteacher = 'Y' then '1' else '2' end) as type ,
                    '{$user}' cre_user,
                    sysdate() cre_date,
                    '{$user}' upd_user,
                    sysdate() upd_date
                from
                (
                    SELECT
                        A.* ,
                        (CASE WHEN class_cate = '2' then 'Y' end) as M ,
                        (CASE WHEN '1130' BETWEEN from_time AND to_time then 'Y' end) as L ,
                        (CASE WHEN '1800' BETWEEN from_time AND to_time then 'Y' end) as D ,
                        B.Breakfast_Type as M1,
                        (A.no_persons * IFNULL(B.Breakfast_Money, C1.add_val1)) as M2,
                        (round(A.no_persons / 10) * B.Breakfast_Money) as M3 ,
                        B.Lunch_type as L1,
                        (A.no_persons * IFNULL(B.lunch_money, C2.add_val1)) as L2,
                        (round(A.no_persons / 10) * B.lunch_money) as L3 , B.Dinner_Type as D1,
                        (A.no_persons * IFNULL(B.dinner_money,
                        C3.add_val1)) as D2,
                        (round(A.no_persons / 10) * B.dinner_money) as D3
                    from
                        (
                        SELECT
                            year,
                            class_id,
                            term,
                            class_name,
                            worker,
                            no_persons,
                            class_cate,
                            use_date,
                            teacher_id,
                            isteacher,
                            name,
                            min(from_time) as from_time,
                            max(to_time) as to_time
                        from
                            (
                            SELECT
                                a.year,
                                a.class_id,
                                a.term,
                                a.use_date,
                                a.room_id,
                                a.teacher_id,
                                a.isteacher,
                                c.class_name,
                                c.worker,
                                c.no_persons,
                                c.class_cate,
                                IFNULL(d1.name,
                                d2.name) as name,
                                IFNULL(b1.FROM_TIME,
                                b2.FROM_TIME) as FROM_TIME,
                                IFNULL(b1.TO_TIME,
                                b2.TO_TIME) as TO_TIME
                            from
                                (
                                SELECT
                                    distinct room_use.year,
                                    room_use.class_id,
                                    room_use.term,
                                    room_use.use_date,
                                    room_use.use_period,
                                    room_use.room_id,
                                    room_use.use_id,
                                    room_use.teacher_id,
                                    room_use.isteacher
                                from
                                    room_use
                                JOIN classroom ON
                                    room_use.ROOM_ID = classroom.ROOM_ID
                                where
                                    room_use.year = '{$class_info['year']}'
                                    AND room_use.class_id = '{$class_info['class_no']}'
                                    AND room_use.term = '{$class_info['term']}'
                                    AND str_to_date(use_date, '%Y-%m-%d') = '{$use_date}'
                                    AND room_use.teacher_id is not null
                                    AND classroom.BELONGTO = '68000' ) a
                            LEFT JOIN periodtime b1 on
                                a.use_period = b1.id
                                AND a.year = b1.year
                                AND a.class_id = b1.class_no
                                AND a.term = b1.term
                                AND a.room_id = b1.room_id
                                AND a.use_id = b1.course_code
                                AND a.use_date = b1.course_date
                            LEFT JOIN periodtime b2 on
                                a.use_period = b2.id
                                AND b2.year is null
                                AND b2.class_no is null
                                AND b2.term is null
                                AND b2.room_id is null
                                AND b2.course_code is null
                            LEFT JOIN `require` c on
                                a.year = c.year
                                AND a.class_id = c.class_no
                                AND a.term = c.term
                            LEFT JOIN teacher d1 on
                                d1.teacher_type = '1'
                                AND d1.idno = a.teacher_id
                                AND a.isteacher = 'Y'
                            LEFT JOIN teacher d2 on
                                d2.teacher_type = '2'
                                AND d2.idno = a.teacher_id
                                AND a.isteacher = 'N' ) b
                        group by
                            year,
                            class_id,
                            term,
                            class_name,
                            worker,
                            no_persons,
                            use_date,
                            class_cate,
                            teacher_id,
                            isteacher,
                            name )A
                    LEFT JOIN dining B on
                        A.year = B.year
                        AND A.class_id = B.class_no
                        AND A.term = B.term
                    LEFT JOIN dining_code C1 on
                        C1.item_id = 'A'
                    LEFT JOIN dining_code C2 on
                        C2.item_id = 'B'
                    LEFT JOIN dining_code C3 on
                        C3.item_id = 'C' )c
                    where
                    D is not null";
        $query = $this->db->query($sql);       
        // dd($sql);
        return $query->result(); 
    }    
}
