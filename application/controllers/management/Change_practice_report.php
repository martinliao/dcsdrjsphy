<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Change_practice_report extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['require_model']);

        if(!isset($this->data['filter']['query_year'])){
            $this->data['filter']['query_year']=date('Y')-1911;
        }
        if(!isset($this->data['filter']['class_no'])){
            $this->data['filter']['class_no']='';
        }

        if(!isset($this->data['filter']['class_name'])){
            $this->data['filter']['class_name']='';
        }
    }

    public function index()
    {
        //$condition = $this->getFilterData(['year', 'class_no', 'class_name']);
        $condition = $this->getFilterData(['query_year', 'class_no', 'class_name']);
        $condition=['year'=>$condition['query_year'],'class_no'=>$condition['class_no'],'class_name'=>$condition['class_name']];
        $this->data['requires'] = $this->require_model->getList($condition);
        $this->data['link_refresh'] = base_url("management/change_practice_report/");
        $this->layout->view('management/change_practice_report/list',$this->data);
    }
    public function detail()
    {
        $condition = $this->getFilterData(['seq_no']);
        $practices = $this->require_model->getPractice($condition);
        $bc_names = [];
        $requires = [];
        foreach($practices as $practice){
            if (!empty($practice->bc_name) && !in_array($practice->bc_name, $bc_names)){
                $bc_names[] = $practice->bc_name;
            }

            $require = [
                "year" => $practice->year,
                "class_no" => $practice->class_no,
                "term" => $practice->term,
                "class_name" => $practice->class_name,
            ];

            if (!in_array($require, $requires)){
                $requires[] = $require;
            }
        }
        // $bc_names = bc_names;
        $this->data['bc_names'] = $bc_names;
        $this->data['requires'] = $requires;
        $this->data['link_refresh'] = base_url("management/change_practice_report/");
        $this->layout->view('management/change_practice_report/detail',$this->data);
    }


}
