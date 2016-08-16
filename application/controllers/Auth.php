<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->library('access');
    }

    function add_user($u, $p) {
        $this->load->model('users_model');
        $this->users_model->add_user($u, $p);
    }

    function edit_password($u, $p) {
        $this->load->model('users_model');
        $this->users_model->change_password($u, $p);
    }

    function index() {
        $this->access->logout();
        $this->login();
    }

    function login() {
        $this->load->library('form_validation');

        $this->form_validation->set_rules('username', 'Username', 'trim|required|strip_tags');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('token', 'token', 'callback_check_login');
        if ($this->form_validation->run() == false) {
            $data['title'] = 'Transmission Lines Analyzer';
            $this->load->view('login', $data);
//            redirect('converter/upload');
        } else {
            redirect('converter/upload');
        }
    }

    function logout() {
        $this->access->logout();
        redirect('auth/login');
    }

    function check_login() {
        $username = $this->input->post('username', true);
        $password = $this->input->post('password', true);

        $login = $this->access->login($username, $password);
        if ($login) {
            return true;
        } else {
            $this->form_validation->set_message('check_login', 'Wrong username or password');
            return false;
        }
    }

}
