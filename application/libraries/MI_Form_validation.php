<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MI_Form_validation extends CI_Form_validation
{
    public $CI;

    public function is_unique($str, $field)
    {
        sscanf($field, '%[^.].%[^.]', $table, $field);
        //return isset($this->CI->db)
        return is_object($this->CI->db)
            ? ($this->CI->db->limit(1)->get_where($table, array($field => $str))->num_rows() === 0)
            : FALSE;
    }

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->set_message('valid_date', '%s 日期格式错误');
        $this->set_message('valid_datetime', '%s 日期时间格式错误');
        $this->set_message('valid_gather_zero', '%s 請設定是否為考核班期');
        $this->set_message('valid_famp', '重大政策最少設定一個');
        $this->set_message('validate_bank', '帳號請填入數字，不可有[-]等其他字元');
        $this->set_message('validate_zipcode', '郵遞區碼請輸入3、5或6碼');
        //$this->set_message('valid_exist','此場地代碼已經被刪除');
    }

    /**
     * Validate Date
     *
     * @param   string
     * @return  bool
     */
    public function valid_date($date)
    {
        if (preg_match("/^\d{4}-\d{2}-\d{2}$/s", $date)) {
            $y = substr($date, 0, 4);
            $m = substr($date, 5, 2);
            $d = substr($date, 8, 2);

            if (checkdate($m, $d, $y)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Validate Date
     *
     * @param   string
     * @return  bool
     */
    public function valid_datetime($date)
    {
        if (preg_match("/^\d{4}-\d{2}-\d{2}\ \d{2}:\d{2}$/s", $date)) {
            $y = substr($date, 0, 4);
            $m = substr($date, 5, 2);
            $d = substr($date, 8, 2);

            if (checkdate($m, $d, $y)) {
                return TRUE;
            }
        }

        return FALSE;
    }

    public function valid_gather_zero($value)
    {
        if ($value >= 0) {
            return TRUE;
        }

        return FALSE;
    }

    public function valid_famp($value)
    {
        if ($value != 'Y') {
            return TRUE;
        }

        return FALSE;
    }

    /*public function valid_exist($value)
    {
        if(strlen($value)<=3){
            return false;
        }
    }*/



    public function validate_bank($value)
    {
        if ($value == '未提供帳號' || is_numeric($value)) {
            return true;
        }


        return false;
    }

    public function validate_zipcode($zipcode)
    {
        $codeLen = [3, 5, 6];
        if (in_array(strlen($zipcode), $codeLen)) {
            return true;
        }
        return false;
    }
}
