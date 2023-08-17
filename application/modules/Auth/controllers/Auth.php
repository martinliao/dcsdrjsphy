<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Memo: 改造成 DCSD Loing(登錄?), 替換Welcome. 22Apr2023
 * 修改: 
 *  1. 不繼承 MY_Controller, 因為太多處理及資訊不是 Login 需要的(增加負擔).
 *  2. 改用 SmartACL, 使用專業的ACL, 不自己去處理 membership, lock/unlock, 帳號啟用, attempt 次數等, 資安條件另外管理
 *  3. view 的部份收歛成1個, 且不使用 Layout(library, 增加Login負擔)
 */
class Auth extends MI_Controller
{
    protected $flag_site = 'admin';

    public function __construct()
    {
        parent::__construct();
        $this->load->library('smarty_acl');
        $this->load->helper('url');
        $this->load->library('form_validation');

        $this->load->model('system/user_blacklist_model');
        $this->load->model('system/login_history_model');

        $this->load->helper(array(
            'captcha',
            'common',
        ));

        $this->load->model(array(
            'system/user_model',
        ));
    }

    /**
     * Login page
     */
    public function login()
    {
        $username = $password = NULL;
        if ($this->smarty_acl->logged_in(FALSE)) {
            //return redirect('/account');
            return redirect(base_url('dcsdindex'));
        }

        $tmp = $this->input->cookie('username');
        $this->data['username'] = $this->input->cookie('username');
        $error = '';
        //Rules
        $this->form_validation->set_rules('username', 'Username', 'trim|required|alpha_dash');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        
        // Validate
        if ($this->form_validation->run() === TRUE) {
            $post = $this->input->post();
            if((strcmp(ENVIRONMENT, 'production') == 0) && empty($post['g-recaptcha-response'])){
                $error = '請勾選我不是機器人';
            }else{
                //$username = str_replace(array("\r", "\n", "\r\n", "\n\r" , "%0a", "%0d"), "", $post['username']);
                $username = $this->input->post('username', true);
                $password = $this->input->post('password', true);
                /*// $ip = getClentIP();
                $ip = $this->input->ip_address();
                $lock_time = $this->user_blacklist_model->getLockTimeByIP($ip);
                if ($lock_time) {
                    $lock_time = date('Y-m-d H:i:s', strtotime($lock_time));
                    $error = "該IP已遭鎖定！<br>於「{$lock_time}」解鎖";
                }/** */
                $user = null;
                $login = $this->smarty_acl->login($username, $password, $this->input->post('remember', true), FALSE);
                if ($login) {
                    $user = $this->user_model->getuserByAccount('admin'); // ToDo: 暫時用 admin 的帳號. 22Apr2023
                } else {
                    // 以下是 Welcome作的
                    $user = $this->user_model->getuserByAccount($username);
                    $rs = $this->user_model->login($username, $password);
                    if ($rs['status']) {
                        $login = $this->smarty_acl->login('martin', 'jack5899', false, FALSE);
                    }
                }
                if ($login) {
                    $this->session->set_flashdata('success_msg', 'User logged in successfully!');
                    // cancel logs for login error
                    $this->user_blacklist_model->cancelAccount($username);
                    $this->user_blacklist_model->cancelIP($ip);
                    $this->setOldFlags($user);
                    return redirect(base_url('dcsdindex'));
                }
                $this->session->set_flashdata('error_msg', $this->smarty_acl->errors());
                return redirect(current_url());
            }
            $this->session->set_flashdata('error_msg', $error);
        }
        //Load view
        $this->data['grecaptcha_sitekey'] = '6LeWFsEUAAAAAPkas5uVitqs1e2yKDxivqDD8sii';
        // 舊版 DCSD-Phy: common/layout_base + login
        //$this->load->view('login');
        // CSRF Token
        $this->data['csrf'] = array(
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        );
        $this->load->view('old-login', $this->data);
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->smarty_acl->logout(FALSE);
        return redirect('/login');
    }

    /**
     * Register
     */
    public function register()
    {
        if ($this->smarty_acl->logged_in(FALSE)) {
            return redirect('admin');
        }
        //Rules
        $this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[admins.username]|alpha_dash');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[admins.email]');
        $this->form_validation->set_rules('name', 'Name', 'trim|required|alpha_numeric_spaces');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[' . $this->config->item('min_password_length', 'smarty_acl') . ']');

        //Validate
        if ($this->form_validation->run() === TRUE) {
            //Register user
            $register = $this->smarty_acl->register_user(
                $this->input->post('username', true),
                $this->input->post('password', true),
                $this->input->post('email', true),
                [
                    'name' => $this->input->post('name', true)
                ]
            );
            //User registered
            if ($register) {
                $this->session->set_flashdata('success_msg', 'User created successfully!');
                return redirect(current_url());
            }

            $this->session->set_flashdata('error_msg', $this->smarty_acl->errors());
            return redirect(current_url());
        }
        //Load view
        $this->load->view('register');
    }

    /**
     * Activate account
     * @param integer $user_id
     * @param string $code
     */
    public function activate($user_id, $code)
    {
        if (!$user_id || !$code) {
            $this->session->set_flashdata('error_msg', 'Empty or invalid activation link!');
            return redirect('login');
        }
        //Activate user
        $activate = $this->smarty_acl->activate_user($user_id, $code);
        //Activation success
        if ($activate) {
            $this->session->set_flashdata('success_msg', 'Email confirmed successfully!');
            return redirect('login');
        }
        //Activation error
        $this->session->set_flashdata('error_msg', $this->smarty_acl->errors());
        return redirect('login');
    }

    /**
     * Resend Activation Link
     */
    public function resend_activation()
    {
        if ($this->smarty_acl->logged_in(FALSE)) {
            return redirect('/');
        }
        //Rules
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        //Validate
        if ($this->form_validation->run() === TRUE) {
            $activation = $this->smarty_acl->resend_activation($this->input->post('email', true), FALSE);
            if ($activation) {
                $this->session->set_flashdata('success_msg', 'A fresh verification link has been sent to your email address!');
                return redirect(current_url());
            }

            $this->session->set_flashdata('error_msg', $this->smarty_acl->errors());
            return redirect(current_url());
        }
        //Load view
        $this->load->view('passwords/activation');
    }

    /**
     * Forgot Password
     */
    public function forgot_password()
    {
        if ($this->smarty_acl->logged_in(FALSE)) {
            return redirect('/');
        }
        //Rules
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        //Validate
        if ($this->form_validation->run() === TRUE) {
            $forgotten = $this->smarty_acl->forgotten_password($this->input->post('email', true), FALSE);
            if ($forgotten) {
                $this->session->set_flashdata('success_msg', 'Password Reset Email Sent!');
                return redirect(current_url());
            }

            $this->session->set_flashdata('error_msg', $this->smarty_acl->errors());
            return redirect(current_url());
        }
        //Load view
        $this->load->view('passwords/forgot');
    }

    /**
     * Reset Password
     * @param string $code
     */
    public function reset_password($code)
    {
        if ($this->smarty_acl->logged_in(FALSE)) {
            return redirect('/');
        }
        //Validate code
        $user = $this->smarty_acl->forgotten_password_check($code, FALSE);
        if (!$user) {
            $this->session->set_flashdata('error_msg', $this->smarty_acl->errors());
            return redirect('forgot_password');
        }
        //Rules
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|is_unique[admins.email]');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|matches[password_confirmation]|min_length[' . $this->config->item('min_password_length', 'smarty_acl') . ']');
        $this->form_validation->set_rules('password_confirmation', 'Password Confirmation', 'trim|required');

        //Validate
        if ($this->form_validation->run() === TRUE) {
            $reset = $this->smarty_acl->reset_password(
                $user,
                $this->input->post('email', true),
                $this->input->post('password', true),
                FALSE
            );
            if ($reset) {
                $this->session->set_flashdata('success_msg', 'Password Updated successfully!');
                return redirect('login');
            }

            $this->session->set_flashdata('error_msg', $this->smarty_acl->errors());
            return redirect(current_url());
        }
        //Load view
        $this->load->view('passwords/reset', ['code' => $code]);
    }

    /** Copy from (Fet)MY_Controller */
    private function setOldFlags($user)
    {
        $data = array(
            'member_userid' => $user['id'],
        );
        $_sessId = $this->session->session_id;
        $this->session->set_userdata('session_id', $_sessId);
        // $this->site = 'admin'
        $this->session->set_userdata($this->flag_site.$_sessId, $data);
    }
}
