<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserInfo extends CI_Controller {



    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('SysModel');
        $this->load->helper('url');
        $this->load->helper('tool');
        $this->load->library('encrypt');
    }

    public function index()
	{


        $group_items=$this->SysModel->table_seleRow("group_id,group_name",'group_info');
        $value['op_pwd']='jcabc';
        $value['grouplist']=$group_items;
		$this->load->view('v_userinfo',$value);
	}



    //获取用户数据
	public function getUserData(){

        $items=array();
	    $where=array();
	    $like=array();

	    $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');

        if(!($limit)){
            $limit=15;
        }

        if(count($search_val)>=1){

            $search_val['user_name']==""?$like=array():$like['user_name']=$search_val['user_name'];
            $search_val['user_name']="";



            $where=array_filter($search_val);
        }
        $items=$this->SysModel->get_All_User($curr,$limit,$where,$like);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);




    }


    //新增
    public function addUser(){
        $result=array();
        $val=$this->input->post('user_val');
        $sval=array();
        if($val){
            $val['user_cs']=0;
            $val['user_statue']=1;
            $val['user_pwd']=$this->encrypt->encode($val['user_phone']);

            $user_items=$this->SysModel->table_seleRow("user_name",'userinfo',array('user_phone'=>$val['user_phone']));
            if(count($user_items)>0){
                $result['code']=true;
                $result['msg']='电话号码重复';
                header("HTTP/1.1 201 Created");
                header("Content-type: application/json");
                echo json_encode($result);
                return true;
            }





            $db_result=$this->SysModel->table_addRow('userinfo',$val);
            if($db_result>=0){
                $user_items=$this->SysModel->table_seleRow("user_id",'userinfo',array('user_phone'=>$val['user_phone']));
                //生成二维码
                $dir='./uploads/';
                if(is_dir($dir) or mkdir($dir)){

                }
                $imgfile=time().'.png';
                $pdir=$dir.$imgfile;
                    $url="http://oa.fjspacecloud.com/Inquiry/?id=".$user_items[0]['user_id'];
                buildQr(''.$url,$pdir);
                $sval['user_QR']='http://oa.fjspacecloud.com/JKInquiry/uploads/'.$imgfile;
                $db_result=$this->SysModel->table_updateRow('userinfo',$sval,array('user_phone'=>$val['user_phone']));
                $add_num=1;
                $g['group_regnum']='group_regnum'+$add_num;
                $group_items=$this->SysModel->table_updateRow('group_info',$g,array('group_id'=>$val['user_group']));


                $result['code']=true;
                $result['msg']='用户添加成功';

            }
            else{
                $result['code']=false;
                $result['msg']='用户添加失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='用户添加失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //修改
    public function updateUser(){

        $result=array();
        $val=$this->input->post('user_val');
        $u_id=$this->input->post('user_id');
        if($val && $u_id){

            $val['user_pwd']=$this->encrypt->encode($val['user_phone']);
            $where_Data=array('user_id'=>$u_id);
            $db_result=$this->SysModel->table_updateRow('userinfo',$val,$where_Data);


            if($db_result>=0){
                $result['code']=true;
                $result['msg']='用户信息修改成功';

            }
            else{
                $result['code']=false;
                $result['msg']='用户信息修改失败';
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
            echo 1;
        }
        else{
            echo 0;
        }

    }




}
