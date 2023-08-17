<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Human_personnel extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('data/job_title_model');
    }

    public function index()
    {
        $this->data['page_name'] = 'list';

        $this->data['user_data'] = $this->user_model->get($this->flags->user["id"]);
        $data = $this->user_model->_get($this->flags->user["id"]);
        $this->data['user_data']['job_title_name'] = $data['job_title_name'];
        $this->data['user_data']['edit'] = base_url("data/human_personnel/edit");

        $this->layout->view('data/human_personnel/list', $this->data);
    }


    public function edit($id=NULL)
    {
        $this->data['page_name'] = 'edit';
        $user_data = $this->user_model->get($this->flags->user["id"]);
        $data = $this->user_model->_get($this->flags->user["id"]);
        $this->data['user_data']['job_title_name'] = $data['job_title_name'];

        $s_pos = strpos($user_data['office_tel'],'[');
        $e_pos = strpos($user_data['office_tel'],']');
        $len = $e_pos - $s_pos ;
        if ($s_pos==false || $e_pos==false || $len<1) {
            $office_tel = $user_data['office_tel'];
            $office_tel_ext = '';
        } else {
            $office_tel = substr($user_data['office_tel'],0, $s_pos);
            $office_tel_ext = substr($user_data['office_tel'],$s_pos+1, $len-1);
        }

        $this->data['user_data'] = array(
            'name' => $user_data['co_usrnick'],
            'gname' => $user_data["name"],
            'gender' => $user_data["gender"],
            'job'  => $data['job_title_name'],
            'title'  => $user_data["job_title"],
            'office_tel' => $office_tel,
            'office_tel_ext' => $office_tel_ext,
            'office_fax' => $user_data["office_fax"],
            'email' => $user_data["email"],
            'email2' => $user_data["email2"],

        );

        if ($post = $this->input->post()) {
            if($post['mode']=='save'){
                $office_tel = $post['office_tel'];
                $office_tel_ext = $post['office_tel_ext'];
                if (trim($office_tel_ext)=='')
                    $tel = $office_tel;
                else
                    $tel = $office_tel.'['.$office_tel_ext.']';

                $fields = array(
                    'co_usrnick' => $post['name'],
                    'name' => $post['gname'],
                    'gender' => $post['gender'],
                    'email' => $post['email'],
                    'email2' => $post['email2'],
                    'office_tel' => $tel,
                    'co_empdb_poftel' => $post['office_tel'],
                    'office_fax' => $post['office_fax'],
                    'job_title' => $post['title'],
                );
                // jd($post);
                // jd($fields,1);
                $rs = $this->user_model->update($this->flags->user["id"], $fields);
                if($rs){
                    echo "<script>\n";
                    echo "alert('修改成功!')\n";
                    echo "window.opener.location.reload();";
                    echo "window.close();";
                    echo "</script>\n";
                }
            }

        }

        $this->load->view('data/human_personnel/edit', $this->data);
    }

    public function co_title()
    {
        if (!isset($this->data['filter']['bureau_page'])) {
            $this->data['filter']['bureau_page'] = '1';
        }
        if (!isset($this->data['filter']['bureau_q'])) {
            $this->data['filter']['bureau_q'] = '';
        }
        if (!isset($this->data['filter']['key1'])) {
            $this->data['filter']['key1'] = 'N';
        }

        $page = $this->data['filter']['bureau_page'];
        $rows = $this->data['filter']['rows'];

        $conditions = array();

        $attrs = array();
        $attrs['conditions'] = $conditions;

        if ($this->data['filter']['bureau_q'] !== '' ) {
            $attrs['q'] = $this->data['filter']['bureau_q'];
        }
        // jd($attrs);
        $total_query_records = $this->job_title_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page -1) * $rows;
        $this->data['total_page'] = ceil($total_query_records / $rows);

        $attrs['rows'] = $rows;
        $attrs['offset'] = $offset;

        $this->data['title_list'] = $this->job_title_model->getList($attrs);
        // jd($this->data['bureau_list'],1);
        $this->load->view('data/human_personnel/co_title', $this->data);
    }

}
