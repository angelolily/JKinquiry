<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scheme extends CI_Controller {



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

        $value['op_pwd']='jcabc';
		$this->load->view('v_shemeinfo',$value);
	}



    //获取方案数据
	public function getSchemeData(){

        $items=array();
	    $where=array();


	    $curr=$this->input->GET('page');
        $limit=$this->input->GET('limit');
        $search_val=$this->input->GET('val');

        if(!($limit)){
            $limit=15;
        }

        if(count($search_val)>=1){
            $where=array_filter($search_val, function ($val) {
                if ($val == "0" || $val != false || $val == 0) {
                    return true;
                } else {
                    false;
                }
            });

        }
        $items=$this->SysModel->get_All_Scheme($curr,$limit,$where);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);




    }

    //新增
    public function addScheme(){
        $result=array();
        $val=$this->input->post('scheme_val');

        if($val){
            $val['scheme_statue']=1;
            $val['scheme_date']=date('Y-m-d');
            $val['scheme_person']='林建伟';
            $db_result=$this->SysModel->table_addRow('scheme_info',$val);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='方案添加成功';

            }
            else{
                $result['code']=false;
                $result['msg']='方案添加失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='方案添加失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //修改
    public function updateScheme(){

        $result=array();
        $val=$this->input->post('scheme_val');
        $s_id=$this->input->post('scheme_id');
        if($val && $s_id){
            $where_Data=array('scheme_id'=>$s_id);
            $db_result=$this->SysModel->table_updateRow('scheme_info',$val,$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='方案信息修改成功';

            }
            else{
                $result['code']=false;
                $result['msg']='方案信息修改失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='方案信息修改失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //同步删除业务组绑定方案

    public function release_group_scheme($s_id){


        $group_items=$this->SysModel->table_seleRow("group_id,group_scheme",'group_info',array(),array('group_scheme'=>$s_id));

        if(count($group_items)>0){

            foreach ($group_items as $grow){
                if(strlen($grow['group_scheme'])>1){
                    $arr_row=explode(",",$grow['group_scheme']);
                    if(count($arr_row)>0){
                        $tmpstr="";
                        foreach ($arr_row as $srow){
                            if($srow!=$s_id){
                                $tmpstr.=$srow.',';
                            }
                        }
                        $gval['group_scheme']=rtrim($tmpstr,',');
                    }
                }
                else{
                    $gval['group_scheme']='';
                }


                $wheregrou=array('group_id'=>$grow['group_id']);
                $this->SysModel->table_updateRow('group_info',$gval,$wheregrou);

            }



        }


    }

    //删除
    public function delScheme(){

        $result=array();
        $s_id=$this->input->post('scheme_id');
        if($s_id){
            $where_Data=array('scheme_id'=>$s_id);

            //删除方案前，要将业务组的方案信息同步清空

            $this->release_group_scheme($s_id);

            $db_result=$this->SysModel->table_del('scheme_info',$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='方案信息删除成功';

            }
            else{
                $result['code']=false;
                $result['msg']='方案信息删除失败';
            }

        }
        else{
            $result['code']=false;
            $result['msg']='方案信息删除失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }




}
