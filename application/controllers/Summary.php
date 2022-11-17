<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/11
 * Time: 16:07
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Summary extends CI_Controller {


    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('SysModel');
        $this->load->helper('url');
        $this->load->library('encrypt');
    }


    public function index(){

        $this->load->view('v_summary');

    }
    //显示时间线
    public function ordertime(){

        $oid=$this->uri->segment(3);
        $order_his_info = $this->SysModel->table_seleRow_limit("history_Data,history_statue", 'order_history', array('order_id' => $oid),array(),20,'history_Data','DESC');
        $datas['orderlist']=$order_his_info;
        $this->load->view('v_orderTimeline',$datas);

    }



}