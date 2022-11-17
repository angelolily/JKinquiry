<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ClienInfo extends CI_Controller {



    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('SysModel');
        $this->load->helper('url');
        $this->load->library('encrypt');
    }
    //关联数组删除key
    private function bykey_reitem($arr, $key){
        if(!array_key_exists($key, $arr)){
            return $arr;
        }
        $keys = array_keys($arr);
        $index = array_search($key, $keys);
        if($index !== FALSE){
            array_splice($arr, $index, 1);
        }
        return $arr;

    }
    public function index()
	{
        $user_type=$this->session->userdata('user_type');
        $user_group=$this->session->userdata('user_group');
        $user_id=$this->session->userdata('user_id');
        $user_name=$this->session->userdata('user_name');
        $user_info=array();
        if($user_group && $user_type){

            if($user_type==1){

                $user_info=array(array('user_id'=>$user_id,'user_name'=>$user_name));
            }
            else if($user_type==2){

                $user_info=$this->SysModel->table_seleRow("user_name,user_id,user_group",'userinfo',"user_group='{$user_group}'");
                if(count($user_info)>0){

                    $user_tmp['user_id']='无';
                    $user_tmp['user_name']='未绑定';
                    $user_tmp['user_group']='';
                    array_unshift($user_info,$user_tmp);

                }

            }
            else if($user_type==3){
                $user_info=$this->SysModel->table_seleRow("user_name,user_id,user_group",'userinfo');
                if(count($user_info)>0){

                    $user_tmp['user_id']='无';
                    $user_tmp['user_name']='未绑定';
                    $user_tmp['user_group']='全部';
                    array_unshift($user_info,$user_tmp);

                }
            }

        }
        else{

            $this->load->view('v_login');

        }

        $value['op_pwd']='jcabc';
        $value['userlist']=$user_info;
		$this->load->view('v_clieninfo',$value);
	}



    //获取客户数据
	public function getClienData(){

        $items=array();
	    $where=array();
	    $like=array();
        $wherein=array();

	    $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');
        $user_type=$this->session->userdata('user_type');
        $user_group=$this->session->userdata('user_group');
        $user_id=$this->session->userdata('user_id');



        if($user_type==1){

            $where['clien_userid']=$user_id;

        }
        if($user_type==2){
            $user_group_info = $this->SysModel->table_seleRow("user_id", 'userinfo', array('user_group' => $user_group));
            foreach ($user_group_info as $ug_row){
                array_push($wherein,$ug_row['user_id']);

            }
        }

        if(!($limit)){
            $limit=15;
        }

        if(count($search_val)>=1){

            $search_val['clien_name']==""?"":$like['clien_name']=$search_val['clien_name'];
            $search_val['clien_name']="";
            $search_val['clien_local']==""?"":$like['clien_local']=$search_val['clien_local'];
            $search_val['clien_local']="";

            if($search_val['clien_userid']!=''){

                //有明确查询业务经理，不用在wherein
                $wherein=array();

                if($search_val['clien_userid']=='null'){
                    $search_val['clien_userid is']='null';
                    $search_val['clien_userid']="";
                }




            }
            else{
                if($user_type==1){

                    $search_val['clien_userid']=$user_id;

                }
            }





            $where=array_filter($search_val);




            if(array_key_exists('clien_userid',$where)){
                if($where['clien_userid']=='无'){
                    $where['clien_userid']="";
                }
            }


        }
        $items=$this->SysModel->get_All_Clien($curr,$limit,$where,$like,$wherein);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);




    }


    //新增
    public function addUser(){
        $result=array();
        $val=$this->input->post('user_val');

        if($val){
            $val['clien_sex']=$val['clien_sex']=='女'?'2':'1';
            $val['clien_photo']='https://wx1.sbimg.cn/2020/07/08/C65YI.th.jpg';
            $db_result=$this->SysModel->table_addRow('clien_info',$val);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='客户添加成功';

            }
            else{
                $result['code']=false;
                $result['msg']='客户添加失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='客户添加失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //修改
    public function updateUser(){

        $result=array();
        $val=$this->input->post('user_val');
        $u_id=$this->input->post('clien_id');
        if($val && $u_id){
            $where_Data=array('clien_id'=>$u_id);
            $val['clien_sex']=$val['clien_sex']=='女'?'2':'1';
            $val['clien_photo']='https://wx1.sbimg.cn/2020/07/08/C65YI.th.jpg';//默认微信头像
            $db_result=$this->SysModel->table_updateRow('clien_info',$val,$where_Data);
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
        $enable=$this->input->post('enable');
        $user_phone=$this->input->post('clien_phone');

        if($enable==0){
            $user_items=$this->SysModel->table_seleRow("clien_name",'clien_info',array('clien_phone'=>$user_phone));
            if(count($user_items)>0){
                echo "电话号码重复,已被{$user_items[0]['clien_name']}注册";
            }
            else{
                echo "";
            }
        }
        else{
            echo "";
        }


    }




}
