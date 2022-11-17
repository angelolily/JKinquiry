<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {
    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('SysModel');

    }

    public function index()
    {
        $user_name = $this->session->userdata('user_name');
        $user_type = $this->session->userdata('user_type');
        if($user_name){
            $data['username']=$user_name;

            if($user_type==3){
                $this->load->view('v_main',$data);
            }
            else{
                $this->load->view('v_main1',$data);
            }



        }

    }

}