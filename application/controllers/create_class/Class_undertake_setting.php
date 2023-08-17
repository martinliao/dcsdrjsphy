<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Class_undertake_setting extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("create_class/class_undertake_setting_model");
        $this->load->model("bureau_model");
        
        $this->load->library('pagination');
        $date=date('m');
        $y=date('Y');
        if($date>=1&&$date<=3){
            $sDate=$y.'-01-01';
            $eDate=$y.'-03-31';
        }
        if($date>=4&&$date<=6){
            $sDate=$y.'-04-01';
            $eDate=$y.'-06-30';
        }
        if($date>=7&&$date<=9){
            $sDate=$y.'-07-01';
            $eDate=$y.'-09-30';
        }
        if($date>=10&&$date<=12){
            $sDate=$y.'-10-01';
            $eDate=$y.'-12-31';
        }
        if (!isset($this->data['filter']['start_date1'])) {
            $this->data['filter']['start_date1'] = $sDate;
        };
        if (!isset($this->data['filter']['end_date1'])) {
            $this->data['filter']['end_date1'] = $eDate;
        }
        if (!isset($this->data['filter']['del_flag'])) {
            $this->data['filter']['del_flag'] = "";
        }
        if (!isset($this->data['filter']['reason'])) {
            $this->data['filter']['reason']="";
        }
       
    }

    public function index()
    {

        $this->data['filter']['start_date1'] = (empty($this->data['filter']['start_date1'])) ? null : $this->data['filter']['start_date1'];
        $this->data['filter']['end_date1'] = (empty($this->data['filter']['end_date1'])) ? null : $this->data['filter']['end_date1'];
        $this->data['filter']['class_name'] = (empty($this->data['filter']['class_name'])) ? null : $this->data['filter']['class_name'];
        $this->data['filter']['class_no'] = (empty($this->data['filter']['class_no'])) ? null : $this->data['filter']['class_no'];

        $username=$this->flags->user['idno'];

        if($this->data['filter']['del_flag']==""){
            $this->data['filter']['worker']=$username;
        }
       
       
        

        $this->data['page_name'] = 'list';

        // 頁面資訊
        $this->data['filter']['sort'] = (!empty($this->data['filter']['sort'])) ? $this->data['filter']['sort']: null;
        $rows = $this->getFilterData("rows");
        $page = $this->getFilterData("page", 1);

        $this->data["list"] = $this->class_undertake_setting_model->getList($this->data['filter'], $page, $rows);
        // 分頁設定
        $config['base_url'] = base_url("create_class/class_undertake_setting?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $this->data["list"]["count"];
        $config['per_page'] = $rows;
        $this->pagination->initialize($config); 
        $this->data['link_refresh']=  base_url("create_class/class_undertake_setting");
        $this->layout->view('create_class/classundertakesetting/list', $this->data);
    }

    public function edit($id)
    {
        if ($post = $this->input->post()){
            $old_data = $this->class_undertake_setting_model->get($id);
            // if ($this->_isVerify('edit', $old_data) == TRUE) {
                if(empty($post['apply_s_date'])){
                    $post['apply_s_date'] = null;
                    // unset($post['apply_s_date']);
                }

                if(empty($post['apply_e_date'])){
                    $post['apply_e_date'] = null;
                    // unset($post['apply_e_date']);
                }

                if(empty($post['apply_s_date2'])){
                    $post['apply_s_date2'] = null;
                    // unset($post['apply_s_date2']);
                }

                if(empty($post['apply_e_date2'])){
                    $post['apply_e_date2'] = null;
                    // unset($post['apply_e_date2']);
                }

                if(empty($post['sel_s_date'])){
                    $post['sel_s_date'] = null;
                    // unset($post['sel_s_date']);
                }

                if(empty($post['sel_e_date'])){
                    $post['sel_e_date'] = null;
                    // unset($post['sel_e_date']);
                }
                
                //20210708加入自費語言start
                if(empty($post['ref_s_date'])){
                    $post['ref_s_date'] = null;
                    // unset($post['ref_s_date']);
                }

                if(empty($post['ref_e_date'])){
                    $post['ref_e_date'] = null;
                    // unset($post['ref_e_date']);
                }

                if(empty($post['ext_s_date'])){
                    $post['ext_s_date'] = null;
                    // unset($post['ext_s_date']);
                }

                if(empty($post['ext_e_date'])){
                    $post['ext_e_date'] = null;
                    // unset($post['ext_e_date']);
                }

                if((empty($post['apply_s_date']) || empty($post['apply_e_date']))
                    && (!empty($post['apply_s_date2']) || !empty($post['apply_e_date2']))) {
                    echo "<script>alert('若「報名起日(西元)」尚未設定，則不可設定「報名起日2(西元)」')</script>";
                }else {

                    if( (!empty($post['ref_s_date'])) && (!empty($post['ref_e_date'])) )
                    {
                        if ((date('Y-m-d',strtotime($post['ref_s_date'])) > date('Y-m-d',strtotime($post['ref_e_date'])))){
                            die('退費開始日期不可大於退費結束日期，請回上一頁');
                        }      
                    
                    }
                    
                    if( (!empty($post['ext_s_date'])) && (!empty($post['ext_e_date'])) )
                    {
                        if ((date('Y-m-d',strtotime($post['ext_s_date'])) > date('Y-m-d',strtotime($post['ext_e_date'])))){
                            die('延期開始日期不可大於延期結束日期，請回上一頁');
                        }      
                    
                    }

                    $rs3 = $this->class_undertake_setting_model->getselect2($post);
                    if ($rs3) {
                        $this->setAlert(2, 'moodle資料編輯成功');

                        $rs2 = $this->class_undertake_setting_model->update_phy_require($post);
                        if ($rs2) {
                            $this->setAlert(2, 'moodle資料編輯成功');
                        }
                    }else{
                        $this->setAlert(2, '沒有moodle資料');
                    }

                    unset($post['phy_year']);
                    unset($post['phy_class_no']);
                    unset($post['phy_term']);
                    
                    //20210708加入自費語言end
                    $rs = $this->class_undertake_setting_model->update($id, $post);
                    if ($rs) {
                        $this->setAlert(2, '資料編輯成功');
                    }
                    redirect(base_url("create_class/class_undertake_setting/?{$_SERVER['QUERY_STRING']}"));
                }
            //}
        }
        $this->data["link_save"] = "test";
        $this->data["page_name"] = "";
        $this->data["link_cancel"] = base_url("create_class/class_undertake_setting/?{$_SERVER['QUERY_STRING']}");

        $params = [
            "select" => "r.*, b_limit.name as limit_name, b_req.name as req_name",
            "conditions" => [
                "seq_no" => $id
            ],
            "join" => [
                [
                    "table" => "bureau b_limit",
                    "condition" => "r.limit_beaurau = b_limit.bureau_id",
                    "join_type" => 'left'
                ],
                [
                    "table" => "bureau b_req",
                    "condition" => "r.req_beaurau = b_req.bureau_id",
                    "join_type" => 'left'
                ]                
            ]
        ];
        $this->data["edit_data"] = $this->class_undertake_setting_model->getData($params, "object")[0];

        $this->layout->view('create_class/classundertakesetting/edit', $this->data);
    }

    private function _isVerify($action='add', $old_data=array())
    {
        $config = $this->class_undertake_setting_model->getVerifyConfig();

        $this->form_validation->set_rules($config);
        $this->form_validation->set_error_delimiters('<p class="text-danger">', '</p>');

        return ($this->form_validation->run() == FALSE)? FALSE : TRUE;
    }
    /*
        單位查詢
        type
        1 = 單位人事
        2 = 承辦單位
    */
    public function personnelQuery($type)
    {
        $rows = $this->getFilterData("rows", 10);
        $page = $this->getFilterData("page", 1);
        $condition['keyword'] = $this->getFilterData("keyword");
        $condition['agency'] = $this->getFilterData("agency");

        $this->data['query_id'] = $type;
        $this->data['list'] = $this->bureau_model->getList($condition, $page, $rows);
        $config['base_url'] = base_url("create_class/class_undertake_setting/personnelQuery/".$type."?". $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $this->data["list"]["count"];
        $config['per_page'] = $rows;
        $this->pagination->initialize($config); 

        $this->load->view("create_class/classundertakesetting/personnelQuery", $this->data);
    }
}
