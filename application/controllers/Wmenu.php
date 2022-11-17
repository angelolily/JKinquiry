<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wmenu extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('SysModel');
        $this->load->model('Wechat_Tool_Model');
        $this->load->helper('url');
    }

    public function index()
	{


        $value['op_pwd']='jkabc';
		$this->load->view('v_wechart_menu',$value);
	}



    //获取状态数据
	public function getMenuData(){

        $items=array();
	    $where=array();

        //$menulist=$this->Wechat_Tool_Model->getWMenulist();


        $menulist['menu']["button"][0]["name"]="公司介绍";
        $menulist['menu']["button"][0]["sub_button"][0]["type"]="view";
        $menulist['menu']["button"][0]["sub_button"][0]["name"]="关于我们";
        $menulist['menu']["button"][0]["sub_button"][0]["url"]="https://mp.weixin.qq.com/s?__biz=MzU0NzQyMTE4MA==&mid=2247483879&idx=1&sn=8f54be4da4de525f1ab0024137a6c5a2&chksm=fb4fef74cc386662256565dea8399d3db6f2886385aefe2209641643e3673ad4ecc0931339fe&token=1628501985&lang=zh_CN#rd";

        $menulist['menu']["button"][0]["sub_button"][1]["type"]="view";
        $menulist['menu']["button"][0]["sub_button"][1]["name"]="公司风采";
        $menulist['menu']["button"][0]["sub_button"][1]["url"]="http://oa.fjspacecloud.com/jk/companyDynamic.html";

        $menulist['menu']["button"][1]["name"]="房产询价";
        $menulist['menu']["button"][1]["sub_button"][0]["type"]="view";
        $menulist['menu']["button"][1]["sub_button"][0]["name"]="快速询价";
        $menulist['menu']["button"][1]["sub_button"][0]["url"]="https://price.fjspacecloud.com";

        $menulist['menu']["button"][1]["sub_button"][1]["type"]="miniprogram";
        $menulist['menu']["button"][1]["sub_button"][1]["name"]="综合询价";
        $menulist['menu']["button"][1]["sub_button"][1]["url"]="https://price.fjspacecloud.com";
        $menulist['menu']["button"][1]["sub_button"][1]["appid"]="wx289d944cdf967972";
        $menulist['menu']["button"][1]["sub_button"][1]["pagepath"]="pages/index/index";


        $menulist['menu']["button"][2]["name"]="智能工具";
        $menulist['menu']["button"][2]["sub_button"][0]["type"]="view";
        $menulist['menu']["button"][2]["sub_button"][0]["name"]="水厂查勘";
        $menulist['menu']["button"][2]["sub_button"][0]["url"]="http://waterfactory.fjspacecloud.com/";

        $menulist['menu']["button"][2]["sub_button"][1]["type"]="miniprogram";
        $menulist['menu']["button"][2]["sub_button"][1]["name"]="房产查勘";
        $menulist['menu']["button"][2]["sub_button"][1]["url"]="http://oa.fjspacecloud.com/jk/companyDynamic.html";
        $menulist['menu']["button"][2]["sub_button"][1]["appid"]="wx0c02dc5b14ab2ab5";
        $menulist['menu']["button"][2]["sub_button"][1]["pagepath"]="pages/start/start";

        $menulist['menu']["button"][2]["sub_button"][2]["type"]="view";
        $menulist['menu']["button"][2]["sub_button"][2]["name"]="银行评估管理";
        $menulist['menu']["button"][2]["sub_button"][2]["url"]="http://ccbbank.fjspacecloud.com/";

        $this->Wechat_Tool_Model->createTempMenu($menulist['menu']["button"]);

	    $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');

        if(!($limit)){
            $limit=15;
        }


        if(count($search_val)>=1){
            $where['menu_name']=$search_val['menu_name'];
        }
//        $items=$this->SysModel->get_All_Wmenu($curr,$limit,$where);
//        $items['msg']='';
//        $items['code']=0;

        echo json_encode($items,true);




    }

    
    //处理微信菜单
    private function build_menu_json($menu_type=array()){

        $button=array();
        if(count($menu_type)>0){

            switch ($menu_type['menu_type']){

                case 'view':
                    $button['type']='view';
                    $button['name']=$menu_type['menu_name'];
                    $button['url']=$menu_type['menu_url'];
                    break;
                case 'miniprogram':
                    $button['type']='miniprogram';
                    $button['name']=$menu_type['menu_name'];
                    $button['url']=$menu_type['menu_url'];
                    $button['appid']=$menu_type['menu_appid'];
                    $button['pagepath']=$menu_type['menu_pagepath'];
                    break;
                case 'view_limited':
                    $button['type']='view_limited';
                    $button['name']=$menu_type['menu_name'];
                    $button['media_id']=$menu_type['menu_media_id'];
                    break;
                case 'media_id':
                    $button['type']='media_id';
                    $button['name']=$menu_type['menu_name'];
                    $button['media_id']=$menu_type['menu_media_id'];
                    break;

            } 
            
        }
        return $button;
        

    }
    

    //新增
    public function addMenu(){
        $result=array();
        $val=$this->input->post('menu_val');
        //$val=$_POST['statue_val'];

        if($val){
            $db_result=$this->SysModel->table_addRow('wechart_menu',$val);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='菜单添加成功';

            }
            else{
                $result['code']=false;
                $result['msg']='菜单添加失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='菜单添加失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //修改
    public function updateStatue(){

        $result=array();
        $val=$this->input->post('statue_val');
        $s_id=$this->input->post('statue_id');
        if($val && $s_id){
            $where_Data=array('statue_id'=>$s_id);
            $db_result=$this->SysModel->table_updateRow('statue_info',$val,$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='状态信息修改成功';

            }
            else{
                $result['code']=false;
                $result['msg']='状态信息修改失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='状态信息修改失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }

    //删除
    public function delStatue(){

        $result=array();
        $statue_id=$this->input->post('statue_id');
        if($statue_id){
            $where_Data=array('statue_id'=>$statue_id);
            $db_result=$this->SysModel->table_del('statue_info',$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='状态信息删除成功';

            }
            else{
                $result['code']=false;
                $result['msg']='状态信息删除失败';
            }

        }
        else{
            $result['code']=false;
            $result['msg']='状态删除失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }

    //更新微信菜单
    public function add_push_menu(){


        $menu=array('button'=>[]);
        //先获取三个一级菜单,只获取前3个
        $menu_list1=$this->SysModel->table_seleRow_limit("*","wechart_menu",array('menu_type'=>1),array(),3);

        if(count($menu_list1)[0]>0){

            foreach ($menu_list1 as $row1){
                $menu_tmp=array();
                //获取这个一级下的所有二级子菜单
                $menu_list2=$this->SysModel->table_seleRow("*","wechart_menu",array('menu_father'=>$row1[0]));
                if(count($menu_list2)>0){
                    foreach ($menu_list2 as $row2){
                        $menu_tmp['name']=$row2[0]['name'];
                        $menu_tmp['sub_button']=$this->build_menu_json($row2);
                    }
                }
                else{
                    $menu_tmp=$this->build_menu_json($row1);

                }

                array_push($menu['button'],$menu_tmp);

            }
        }
        $w_menu_json=json_encode($menu);
        $menu_result=$this->Wechat_Tool_Model->setMenu($w_menu_json);


        echo $menu_result;





    }

    public function getMediaId(){
        $medias= $this->Wechat_Tool_Model->getMediaList("news",0,10);
        $w_media_json=json_encode($medias);
        echo $w_media_json;
    }

    //微信测试
    public function wechatTest()
    {
        //获取素材

//        $agentinfo = file_get_contents('php://input');
//        $agentinfo=json_decode($agentinfo,true);
//
//        $menu_result=$this->Wechat_Tool_Model->setMenu($agentinfo);

    }




}
