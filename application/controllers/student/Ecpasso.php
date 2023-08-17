<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ecpasso extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
			redirect(base_url('welcome'));
		}
    }

    public function index()
    {
        $idno = $this->flags->user['idno'];

        $this->load->helper('other_des');
        $old_des = new OTHER_DES();
        $encode_idno = $old_des->encrypt($idno,'EDS45CSAS');
        $encode_idno = $old_des->base64url_encode($encode_idno);
        $encode_time = time();
        $url = "http://elearning.taipei/sso/to.php?sitelink=phyEcpa&a=$encode_idno&t=$encode_time";
        redirect($url);

    }

}
