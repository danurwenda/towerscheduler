<?php

defined('BASEPATH') OR
        exit('No direct script access allowed');
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Users_model
 *
 * @author Administrator
 */
class Users_model extends CI_Model {

    public $table = 'users';
    public $primary_key = 'user_id';

    public function __construct() {
        parent::__construct();
    }

    public function get_login_info($u) {
        $this->db->where('username', $u)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function get_user($id) {
        $this->db->where($this->primary_key, $id)->limit(1);
        $q = $this->db->get($this->table);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }

    public function auth($plain_u, $plain_p) {
        // Get the password from the database and compare it to a variable (for example post)
        $hashedPasswordFromDB = $this->db->get_where('users',[
            'username'=>$plain_u
        ]);
        if($hashedPasswordFromDB->num_rows()>0){
            //ada
            //bandingkan
            return password_verify($plain_p, $hashedPasswordFromDB->row()->password);
        }else{
            //ga ada
            return false;
        }
    }
    
    public function add_user($plain_u,$plain_p){
        $this->db->insert('users',[
            'username'=>$plain_u,
            'password'=>  password_hash($plain_p, PASSWORD_BCRYPT)
        ]);
    }
    
    public function change_password($plain_u,$new_p){
        $this->db->where('username',$plain_u)->update('users',[
            'password'=>  password_hash($new_p, PASSWORD_BCRYPT)            
        ]);
    }

}
