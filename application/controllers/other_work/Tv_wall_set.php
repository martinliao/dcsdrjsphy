<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tv_wall_set extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('other_work/tv_wall_set_model');
    }

    public function index()
    {
        $mode=$this->input->post('mode');
        $item_id=$this->input->post('item_id');

        // if($mode=='savepar'){
        //     $frequency = $this->input->post('frequency');
        //     $this->db->set('frequency',$frequency);
        //     $this->db->update('rotation_play_setup');

        //     $this->setAlert('1','輪播頻率修改成功');
        //     redirect(base_url('other_work/tv_wall_set/'),'refresh');
        // }
        // if($mode=='saveMarquee'){
        //     $marquee = $this->input->post('marquee');
        //     $this->db->set('marquee',$marquee);
        //     $this->db->update('rotation_play_setup');

        //     $this->setAlert('1','跑馬燈文字修改成功');
        //     redirect(base_url('other_work/tv_wall_set/'),'refresh');
        // }
        if($mode=='savetime'){
            $s_key="start_time_".$item_id;
            $e_key="end_time_".$item_id;
            $s_key2="start_date_".$item_id;
            $e_key2="end_date_".$item_id;
            $data=['start_time'=>addslashes($this->input->post($s_key)),
                  'end_time'=>addslashes($this->input->post($e_key)),
                  'start_date'=>addslashes($this->input->post($s_key2)),
                  'end_date'=>addslashes($this->input->post($e_key2))];
            $this->db->where('id',$item_id);
            $data = array_map('addslashes', $data);
            $this->db->update('rotation_play',$data);
            $this->setAlert('1','資料修改成功');
            
            redirect(base_url('other_work/tv_wall_set/'),'refresh');
        }
        if($mode=='del'){
            $this->db->where('id',$item_id);
            $this->db->delete('rotation_play');
            $this->setAlert('1','圖片刪除成功');
    
            redirect(base_url('other_work/tv_wall_set/'),'refresh');
        }

        if($mode=='down'){
            //die();
            //var_dump($this->input->post());
            $this->db->select('min(order_id) as min_oid');
            $this->db->where('order_id >',$this->input->post('order_id'));
            $query=$this->db->get('rotation_play');
            $result=$query->result_array();
            
            $old_order_id=$this->input->post('order_id');
            $this->db->select('id');
            $this->db->where('order_id',$result[0]['min_oid']);
            $id=$this->db->get('rotation_play');
            $final_id=$id->result_array();

            $this->db->trans_start();
            $data=['order_id'=>$result[0]['min_oid']];
            $this->db->where('order_id',$old_order_id);
            $a1=$this->db->update('rotation_play',$data);
            $this->db->trans_complete();        

            
            $this->db->trans_start();
            $tmp=['order_id'=>$old_order_id];
            $this->db->where('id',$final_id[0]['id']);
            $a=$this->db->update('rotation_play',$tmp);
            $this->db->trans_complete();        

            //var_dump($a);
            //var_dump($a1);
            //die();

            redirect(base_url('other_work/tv_wall_set/'),'refresh');
        }
        if($mode=='up'){
            $this->db->select('max(order_id) as max_oid');
            $this->db->where('order_id <',$this->input->post('order_id'));
            $query=$this->db->get('rotation_play');
            $result=$query->result_array();
            
            $this->db->select('id');
            $this->db->where('order_id',$result[0]['max_oid']);
            $id=$this->db->get('rotation_play');
            $final_id=$id->result_array();

            $data=['order_id'=>$result[0]['max_oid']];
            $this->db->where('order_id ',$this->input->post('order_id'));
            $this->db->update('rotation_play',$data);
            //
            $tmp=['order_id'=>$this->input->post('order_id')];
            $this->db->where('id',$final_id[0]['id']);
            $a=$this->db->update('rotation_play',$tmp);
        }

        $this->data['list']=$this->tv_wall_set_model->getPhoto();
        $this->data['setup_list']=$this->tv_wall_set_model->getRotationPlaySetup();

        $this->data['link_refresh'] = base_url("other_work/tv_wall_set/");
        $this->layout->view('other_work/tv_wall_set/list',$this->data);
    }
    

    public function uploadPhoto()
    {
        $now = date("Y-m-d");
        $config['upload_path'] = './files/upload_tv_wall/';
        $config['allowed_types'] = 'gif|jpg|png|jpeg';
        $config['max_size']  = '102400';
      
        $this->load->library('upload', $config);
        $this->db->select('max(order_id) as max_order');
        $query=$this->db->get('rotation_play');
        $result=$query->result_array();
        //$order=array();
        if(!empty($result)){
            $result[0]['max_order'] +=1;
        }else{
            $result[0]['max_order'] = 1;
        }
       
        if($this->upload->do_upload('photo')) {       
            $upload_data = $this->upload->data(); 
            $data=['file_path'=>'files/upload_tv_wall/'.$upload_data['file_name'],
                   'cre_date'=>$now,
                   'status'=>'1',
                   'order_id'=>$result[0]['max_order']];
            $this->db->insert('rotation_play',$data);
            $this->setAlert('1','圖片上傳成功');
            redirect(base_url('other_work/tv_wall_set'),'refresh');
        } else {
            $this->setAlert('1','圖片大小不能超過1MB');
            redirect(base_url('other_work/tv_wall_set'),'refresh');                                    
        }  

    }

    public function saveSet()
    {   
        $mode = $this->input->post('mode2');
        $frequency=$this->input->post('frequency');
        $marquee=$this->input->post('marquee');
        if($mode == 'savepar'){
            $data=['frequency'=>intval($this->input->post('frequency'))];
            $this->db->update('rotation_play_setup',$data);
            $this->setAlert('1','輪播頻率修改成功');
            redirect(base_url('other_work/tv_wall_set/'),'refresh');
        }else if($mode == 'saveMarquee'){
            $data=['marquee'=>$this->input->post('marquee')];
            $this->db->update('rotation_play_setup',$data);
            $this->setAlert('1','跑馬燈文字修改成功');
            redirect(base_url('other_work/tv_wall_set/'),'refresh');
        } else {
            $this->setAlert('2','修改失敗');
            redirect(base_url('other_work/tv_wall_set/'),'refresh');
        }
    }
    

}
