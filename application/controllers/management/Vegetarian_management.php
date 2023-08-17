<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vegetarian_management extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('management/Vegetarian_management_model');
    }

    public function index()
    {
        $default_date = date('Y-m-d');
        $start_date = isset($_GET['start_date'])?addslashes($_GET['start_date']):$default_date;
        $year = isset($_GET['year'])?intval($_GET['year']):"";
        $term = isset($_GET['term'])?intval($_GET['term']):"";
        $classno = isset($_GET['classno'])?addslashes($_GET['classno']):"";
        $id = isset($_GET['id'])?addslashes($_GET['id']):"";
        $act = isset($_GET['act'])?addslashes($_GET['act']):"";
        $remark = isset($_GET['remark'])?addslashes($_GET['remark']):"";

        $this->load->library('pagination');
        $config['total_rows'] = 200;
        $config['per_page'] = 20;
        $this->pagination->initialize($config);
        $this->data['sess_start_date'] = $start_date;
        $this->data['link_refresh'] = base_url("management/vegetarian_management/");
        $this->data['link_reset'] = base_url("/management/vegetarian_management/resetGetMeal");
        $this->data['result']=0;
        $this->data['datas'] = array();
        $this->data['data2'] = array();

        if($start_date != ""){
            $this->data['datas'] = $this->Vegetarian_management_model->getVegetarianSearch($start_date);

       
            $this->data['data2'] = $this->Vegetarian_management_model->getVegetarianSearch2($start_date);
            $this->data['data3'] = $this->Vegetarian_management_model->getVegetarianSearch3($start_date);
            
            $hand_people =0;
            $this->data['diningTotal'] = 0;
            $this->data['vegetarianTotal'] = 0;
            foreach($this->data['datas'] as $itema){
                $this->data['diningTotal'] += $itema['DINING_COUNT'];
                $this->data['vegetarianTotal'] += $itema['totalCount'];
                if($itema['ARRIVAL_TIME']=='便當'){               
                    $itnum = 0;
                    foreach($this->data['data2'] as $itemb){
                        if(($itemb['year']==$itema['YEAR'])&&($itemb['class_no']==$itema['CLASS_NO'])&&($itemb['term']==$itema['TERM'])){                      
                            $this->data['data2'][$itnum]['get_time'] = '已領取';                          
                        }       
                        $itnum++;                 
                    }                    
                }
               
                $info_to_time = array();
                $info_to_time = $this->Vegetarian_management_model->getLoopSql($itema['YEAR'], $itema['CLASS_NO'], $itema['TERM'], $itema['COURSE_DATE']);
                
                if(isset($info_to_time[0]['to_time']) && str_replace(':','',$info_to_time[0]['to_time']) != $itema['TO_TIME']){
                    $this->data['datas'][$hand_people]['TO_TIME'] = $itema['TO_TIME'].'<br>'.str_replace(':','',$info_to_time[0]['to_time']);
                }
                
                $this->db->select('hand_people_num as hpn ,disabled_people_num as dpn');
                $this->db->where('class_no', $itema['CLASS_NO']);
                $this->db->where('year', $itema['YEAR']);
                $this->db->where('term', $itema['TERM']);
                $this->db->where('use_date', $start_date);
                $searchA = $this->db->get('card_record_people_num')->result_array();
                if (empty($searchA)) {
                    $searchA[0]['hpn'] = 0;
                }
                $this->data['diningTotal'] += $searchA[0]['hpn'];
                $this->data['datas'][$hand_people]['hand_people_num'] = $searchA[0]['hpn'];
                $hand_people++;
                 
                 
            }
           
    
        }

        $this->data['link_teachVegtFun'] = base_url("management/vegetarian_management/insertTeacherVegetarian");
        $this->data['link_arrivalFun'] = base_url("management/vegetarian_management/insertVegetarianSearch");
        $this->data['link_arrivalFunBin'] = base_url("management/vegetarian_management/insertVegetarianBinSearch");
        $this->data['link_getFun'] = base_url("management/vegetarian_management/updateVegetarianSearch");
        // if($act=="insert"){
        //     $this->data['result'] = $this->Vegetarian_management_model->insertVegetarianSearch($year,$classno,$term,$start_date);
        // }
        // if($act=="insertBin"){
        //     $this->data['result'] = $this->Vegetarian_management_model->insertVegetarianBinSearch($year,$classno,$term,$start_date);
        // }
        // if($act=="update"){
        //     $this->data['result'] = $this->Vegetarian_management_model->updateVegetarianSearch($id);
        // }
        if($act=="updateremark"){
            $this->data['result'] = $this->Vegetarian_management_model->updateRemark($year,$classno,$term,$start_date,$remark);
        }
        $this->data['link_reset'] = base_url("management/vegetarian_management/resetVegetarianSearch");
        
        $this->layout->view('management/vegetarian_management/list',$this->data);
    }

    public function insertTeacherVegetarian(){
        $year = intval($this->input->post('year'));
        $classno = addslashes($this->input->post('classno'));
        $term = intval($this->input->post('term'));
        $start_date = addslashes($this->input->post('start_date'));
        $teach_vegt_count = addslashes($this->input->post('teach_vegt_count'));

        $result = $this->Vegetarian_management_model->insertTeacherVegetarian($year,$classno,$term,$start_date,$teach_vegt_count);

        return $result;
    }

    public function insertVegetarianSearch(){
        $year = intval($this->input->post('year'));
        $classno = addslashes($this->input->post('classno'));
        $term = intval($this->input->post('term'));
        $start_date = addslashes($this->input->post('start_date'));

        $result = $this->Vegetarian_management_model->insertVegetarianSearch($year,$classno,$term,$start_date);

        return $result;
    }

    public function insertVegetarianBinSearch(){
        $year = intval($this->input->post('year'));
        $classno = addslashes($this->input->post('classno'));
        $term = intval($this->input->post('term'));
        $start_date = addslashes($this->input->post('start_date'));

        $result = $this->Vegetarian_management_model->insertVegetarianBinSearch($year,$classno,$term,$start_date);

        return $result;
    }

    public function updateVegetarianSearch(){
        $id = addslashes($this->input->post('id'));

        $result = $this->Vegetarian_management_model->updateVegetarianSearch($id);

        return $result;
    }
    /*
        移除抵達紀錄
    */
    public function resetVegetarianSearch(){
        $queryData = $this->input->post(['year', 'classno', 'term', 'course_date']);
        $sql = "DELETE FROM arrival WHERE year = ".$this->db->escape(addslashes($queryData['year']))." AND class_no = ".$this->db->escape(addslashes($queryData['classno']))." AND term = ".$this->db->escape(addslashes($queryData['term']))." AND course_date = ".$this->db->escape(addslashes($queryData['course_date']))."";
        $query = $this->db->query($sql);

        if ($query === true){
            echo json_encode(['message' => 'Success']);
        }else{
            echo json_encode(['message' => 'Fail']);
        }            

    }

    public function resetGetMeal(){
        $id = $this->input->post('id');

        if (!empty($id)){
            $sql = "UPDATE vegetarian SET isget = 0,get_time = null WHERE id = ".$this->db->escape(addslashes($id));
            $query = $this->db->query($sql);

            if ($query === true){
                echo json_encode(['message' => 'Success']);
            }else{
                echo json_encode(['message' => 'Fail']);
            }             
        }
    }
}
