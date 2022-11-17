<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Statue extends CI_Controller {

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


        $value['op_pwd']='jcabc';
		$this->load->view('v_statueinfo',$value);
	}



    //获取状态数据
	public function getStatueData(){

        $items=array();
	    $where=array();

	    $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');

        if(!($limit)){
            $limit=15;
        }


        if(count($search_val)>=1){
            $where['statue_name']=$search_val['statue_name'];
        }
        $items=$this->SysModel->get_All_Statue($curr,$limit,$where);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);




    }


    //新增
    public function addStatue(){
        $result=array();
        $val=$this->input->post('statue_val');
        //$val=$_POST['statue_val'];

        if($val){
            $db_result=$this->SysModel->table_addRow('statue_info',$val);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='状态添加成功';

            }
            else{
                $result['code']=false;
                $result['msg']='状态组添加失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='状态添加失败,无法获取数据';
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




}
