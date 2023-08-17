<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student_learn_record_model extends MY_Model
{
    public $table = 'online_app';

    public function __construct()
    {
        parent::__construct();

        $this->init($this->table, $this->pk);

    }

    public function getListCount($attrs=array(),$bureau_id,$username)
    {
        $params = array(
            'conditions' => $attrs['conditions'],
        );

        /*if (isset($attrs['query_class_name'])) {
            $params['query_class_name'] = $attrs['query_class_name'];
        }*/
        if(isset($attrs['query_class_name'])){
            $params['query_class_name']=$attrs['query_class_name'];
        }
        if(isset($attrs['query_student_name'])){
            $params['query_student_name']=$attrs['query_student_name'];
        }
        if(isset($attrs['query_bureau_name'])){
            $params['query_bureau_name']=$attrs['query_bureau_name'];
        }
        $data = $this->getList($params,$bureau_id,$username);
        return count($data);
    }

    public function getList($attrs=array(),$bureau_id,$username)
    {
        if($bureau_id == '379680000A' && $username!='edap'){

            $params = array(
                'select' => 'online_app.year,NVL(out_gov.ou_gov,BS_user.company) as company,online_app.id,online_app.class_no,online_app.term,
                            BS_user.name,require.class_name,require.range,require.start_date1,require.end_date1,require.seq_no,GROUP_CONCAT(certificate_user_list.id) as certificate_id',
                'order_by' => 'class_no',
            );

            $params['join'] = array(array('table' => 'BS_user',
                                'condition' => 'BS_user.idno = online_app.id',
                                'join_type' => 'left'),
                                array('table' => 'require',
                                'condition' => 'require.year = online_app.year and require.class_no=online_app.class_no and require.term=online_app.term',
                                'join_type' => 'left'),
                                array('table' => 'out_gov',
                                'condition' => 'BS_user.idno = out_gov.id',
                                'join_type' => 'left'),
                                array(
                                    'table' => 'certificate_user_list',
                                    'condition' => 'certificate_user_list.seq_no = require.seq_no AND online_app.id = certificate_user_list.idno',
                                    'join_type' => 'left'
                                )                                
                        );
            $params['order_by']='online_app.id asc,require.start_date1';
            $yn_sel=[2,4,5,6,7];
            $params['where_not_in']=array('field'=>'online_app.yn_sel','value'=>$yn_sel);

            if(!isset($attrs['query_class_name'])){
                $attrs['query_class_name']='';
            }
            if(!isset($attrs['query_student_name'])){
                $attrs['query_student_name']='';
            }
            if(!isset($attrs['query_bureau_name'])){
                $attrs['query_bureau_name']='';
            }

            $params['and_like']=array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                    array('field' => 'BS_user.name', 'value'=>$attrs['query_student_name'], 'position'=>'both'),
                    array('field' => 'BS_user.bureau_name', 'value'=>$attrs['query_bureau_name'], 'position'=>'both'),
                ),
            );
            if (isset($attrs['query_student_name'])) {
                $params['or_like'] = array(
                    'many' => TRUE,
                    'data' => array(
                        array('field' => 'BS_user.name', 'value'=>$attrs['query_student_name'], 'position'=>'both'),
                    ),
                );
            }

            if (isset($attrs['conditions'])) {
                $params['conditions'] = $attrs['conditions'];
            }
            if (isset($attrs['rows'])) {
                $params['rows'] = $attrs['rows'];
            }
            if (isset($attrs['offset'])) {
                $params['offset'] = $attrs['offset'];
            }
            
            $params['group_by'] = "require.seq_no, online_app.id";
                     
            $data = $this->getData($params);
        }else{
            $params = array(
                'select' => 'online_app.year,NVL(out_gov.ou_gov,BS_user.company) as company,online_app.id,online_app.class_no,online_app.term,
                             BS_user.name,require.class_name,require.range,require.start_date1,require.end_date1,require.seq_no, GROUP_CONCAT(certificate_user_list.id) as certificate_id',
                'order_by' => 'class_no',
            );
            $params['join'] = array(array('table' => 'BS_user',
                                'condition' => 'BS_user.idno = online_app.id',
                                'join_type' => 'left'),
                                array('table' => 'require',
                                'condition' => 'require.year = online_app.year and require.class_no=online_app.class_no and require.term=online_app.term',
                                'join_type' => 'left'),
                                array('table' => 'out_gov',
                                'condition' => 'BS_user.idno = out_gov.id',
                                'join_type' => 'left'),
                                array(
                                    'table' => 'certificate_user_list',
                                    'condition' => 'certificate_user_list.seq_no = require.seq_no AND online_app.id = certificate_user_list.idno',
                                    'join_type' => 'left'
                                )                                   
                        );
            $params['order_by']='online_app.id asc,require.start_date1';

            $yn_sel=[2,4,5,6,7];
            $params['where_not_in']=array('field'=>'online_app.yn_sel','value'=>$yn_sel);

            $params['where_in']=array('field'=>'BS_user.bureau_id','value'=>$bureau_id);

            if(!isset($attrs['query_class_name'])){
                $attrs['query_class_name']=null;
            }
            if(!isset($attrs['query_student_name'])){
                $attrs['query_student_name']=null;
            }
            if(!isset($attrs['query_bureau_name'])){
                $attrs['query_bureau_name']=null;
            }

            $params['and_like']=array(
                'many' => TRUE,
                'data' => array(
                    array('field' => 'class_name', 'value'=>$attrs['query_class_name'], 'position'=>'both'),
                    array('field' => 'BS_user.name', 'value'=>$attrs['query_student_name'], 'position'=>'both'),
                    array('field' => 'BS_user.bureau_name', 'value'=>$attrs['query_bureau_name'], 'position'=>'both'),
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
            $params['group_by'] = "require.seq_no, online_app.id";
            $data = $this->getData($params);
        }

        $certs = [];
        foreach ($data as $cert){
            if (!empty($cert['certificate_id'])){
                $cert['certificate_id'] = explode(",", $cert['certificate_id']);
                foreach($cert['certificate_id'] as $certificate_id){
                    array_push($certs, $certificate_id);
                }
                
            }
        }

        if (count($certs) > 0){
            $tmp_certs = [];
            $certs = $this->db->select("certificate_user_list.seq_no, certificate_user_list.id, certificate_user_list.idno, certificate_list.cer_name")
            ->join('certificate_list', 'certificate_list.id = certificate_user_list.cer_list_id')
            ->where_in('certificate_user_list.id', $certs)
            ->get('certificate_user_list')->result();
            foreach ($certs as $cert){
                if (!empty($cert->seq_no) && !empty($cert->idno)){
                    if (empty($tmp_certs[$cert->seq_no][$cert->idno])){
                        $tmp_certs[$cert->seq_no][$cert->idno] = [];
                    }
                    
                    array_push($tmp_certs[$cert->seq_no][$cert->idno], $cert);
                }
            }
            $certs = $tmp_certs;
        }

        foreach ($data as $key => $row){
            if (isset($certs[$data[$key]['seq_no']][$data[$key]['id']])){
                $data[$key]['certs'] = $certs[$data[$key]['seq_no']][$data[$key]['id']];
            }
        }

        return $data;
    }
    
}