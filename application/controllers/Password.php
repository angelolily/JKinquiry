<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password extends CI_Controller {



    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('SysModel');
        $this->load->helper('url');
        $this->load->library('encrypt');
    }

    public function index()
	{


		$this->load->view('v_setPassword');
	}




    //修改
    public function updatePwd(){

        $result=array();
        $user_id=$this->session->userdata('user_id');
        $val=$this->input->post('user_val');
        if($val && $user_id){

            $user_data=$this->SysModel->table_seleRow('user_pwd','userinfo',array('user_id'=>$user_id));
            if(count($user_data)>0){
                $pwd=$this->encrypt->decode($user_data[0]['user_pwd']);
                if($val['oldPassword']==$pwd){

                    $userpwd['user_pwd']=$this->encrypt->encode($val['password1']);
                    $where_Data=array('user_id'=>$user_id);
                    $db_result=$this->SysModel->table_updateRow('userinfo',$userpwd,$where_Data);
                    if($db_result>=0){
                        $result['code']=true;
                        $result['msg']='密码修改成功，下次登陆时生效';

                    }
                    else{
                        $result['code']=true;
                        $result['msg']='密码更改失败';

                    }

                }
                else{
                    $result['code']=false;
                    $result['msg']='旧密码错误';
                }

            }
            else{
                $result['code']=false;
                $result['msg']='用户不存在';
            }

        }
        else{
            $result['code']=false;
            $result['msg']='用户信息修改失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }

    //删除
    public function delUser(){

        $result=array();
        $u_id=$this->input->post('user_id');
        if($u_id){
            $where_Data=array('user_id'=>$u_id);
            $db_result=$this->SysModel->table_del('userinfo',$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='用户信息删除成功';

            }
            else{
                $result['code']=false;
                $result['msg']='用户信息删除失败';
            }

        }
        else{
            $result['code']=false;
            $result['msg']='用户信息删除失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }

    //重置密码
    public function restPwd(){

        $result=array();
        $u_phone=$this->input->post('user_phone');
        if($u_phone){
            $val=array('user_pwd'=>$this->encrypt->encode($u_phone));
            $where_Data=array('user_phone'=>$u_phone);
            $db_result=$this->SysModel->table_updateRow('userinfo',$val,$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='密码重置成功';

            }
            else{
                $result['code']=false;
                $result['msg']='密码重置失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='密码重置失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }

    //判断手机号是否重复
    public function isphonerepeat(){

        $user_phone=$this->input->post('user_phone');

        $user_items=$this->SysModel->table_seleRow("user_name",'userinfo',array('user_phone'=>$user_phone));
        if(count($user_items)>0){
            echo "电话号码重复";
        }
        else{
            echo "";
        }

    }




}
