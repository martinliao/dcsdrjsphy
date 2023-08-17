<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

require_once APPPATH . "models/Common_model.php";
class Tax_set_model extends Common_model
{
    public function getTaxSetData()
    {

        $sql = "select TAX,TAX_RATE,H_TAX,H_TAX_RATE from co_tax";

        $query = $this->db->query($sql);

        return $this->QueryToArray($query)[0];

    }

    public function insertTaxSetData($tax,$tax_rate,$h_tax,$h_tax_rate)
    {
        $js = '';
        $cnt = $this->db->query("select count(*) from co_tax")->num_rows();
        if($cnt>0){
            $sql = "update co_tax set TAX = ".$this->db->escape(addslashes($tax)).", TAX_RATE = ".$this->db->escape(addslashes($tax_rate)).",H_TAX=".$this->db->escape(addslashes($h_tax)).",H_TAX_RATE = ".$this->db->escape(addslashes($h_tax_rate))."";
            $this->db->query($sql);
        }
        else{
            $sql = "insert into co_tax (TAX,TAX_RATE,H_TAX,H_TAX_RATE) values (".$this->db->escape(addslashes($tax)).", ".$this->db->escape(addslashes($tax_rate)).",".$this->db->escape(addslashes($h_tax)).",".$this->db->escape(addslashes($h_tax_rate)).")";
            $this->db->query($sql);
        }
        if($this->db->Affected_Rows()){
            $js = "更新完成";
        }
        else{
            $js = "更新失敗或不需要更新";
        }
        return $js;    

    }

}
