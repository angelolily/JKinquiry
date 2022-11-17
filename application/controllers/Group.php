<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Group extends CI_Controller {

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
        $this->load->helper('url');
    }

    public function index()
	{

        $scheme_items=$this->SysModel->table_seleRow("scheme_id,scheme_name",'scheme_info',array('scheme_statue'=>1));
        $value['op_pwd']='jcabc';
        $value['schemelist']=$scheme_items;
		$this->load->view('v_group',$value);
	}



    //获取机构数据
	public function getGroupData(){

        $items=array();
	    $where=array();

	    $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');

        if(!($limit)){
            $limit=15;
        }


        if(count($search_val)>=1){
            $where['group_name']=$search_val['group_name'];
        }
        $items=$this->SysModel->get_All_Goup($curr,$limit,$where);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);




    }


    //新增
    public function addGroup(){
        $result=array();
        $val=$this->input->post('group_val');
        if($val){
            $val['group_id']=uniqid();
            $val['group_regnum']=0;
            $db_result=$this->SysModel->table_addRow('group_info',$val);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='业务组添加成功';

            }
            else{
                $result['code']=false;
                $result['msg']='业务组添加失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='业务组添加失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //修改
    public function updateGroup(){

        $result=array();
        $val=$this->input->post('group_val');
        $g_id=$this->input->post('group_id');
        if($val && $g_id){
            $where_Data=array('group_id'=>$g_id);
            $db_result=$this->SysModel->table_updateRow('group_info',$val,$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='业务组信息修改成功';

            }
            else{
                $result['code']=false;
                $result['msg']='业务组信息修改失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='业务组信息修改失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }

    //删除
    public function delGroup(){

        $result=array();
        $g_id=$this->input->post('group_id');
        if($g_id){
            $where_Data=array('group_id'=>$g_id);
            $db_result=$this->SysModel->table_del('group_info',$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='业务组信息删除成功';

            }
            else{
                $result['code']=false;
                $result['msg']='业务组信息删除失败';
            }

        }
        else{
            $result['code']=false;
            $result['msg']='业务组信息删除失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }




}
