<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends CI_Controller {



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
            }
            else if($user_type==3){
                $user_info=$this->SysModel->table_seleRow("user_name,user_id,user_group",'userinfo');
            }

        }
        else{

            $this->load->view('v_login');

        }
        $value['op_pwd']='jcabc';
        $value['userlist']=$user_info;
        $value['statuelist']=$this->SysModel->table_seleRow("*",'statue_info');
        $value['schemelist']=$this->SysModel->table_seleRow("scheme_id,scheme_name",'scheme_info',array('scheme_statue'=>1));
		$this->load->view('v_orderinfo',$value);
	}



    //获取客户数据
	public function getOrderData(){

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

            $where['order_manager']=$user_id;

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

            $search_val['order_clienname']==""?:$like['order_clienname']=$search_val['order_clienname'];
            $search_val['local_address']==""?:$like['local_address']=$search_val['local_address'];
            $search_val['order_clienname']="";
            $search_val['local_address']="";

            if($search_val['loan_amount_min'] && $search_val['loan_amount_max']){
                $search_val['loan_amount>']=$search_val['loan_amount_min'];
                $search_val['loan_amount<']=$search_val['loan_amount_max'];
                $search_val['loan_amount_min']="";
                $search_val['loan_amount_max']="";
            }

            $where=array_filter($search_val);


        }
        $items=$this->SysModel->get_All_Order($curr,$limit,$where,$like,$wherein);
        $items['msg']='';
        $items['code']=0;

        echo json_encode($items,true);




    }


    //新增
    public function addOrder(){
        $result=array();
        $val=$this->input->post('user_val');
        $user_id=$this->session->userdata('user_id');
        $db_hisresult=0;
        if($val){

            $clieninfo=$this->SysModel->table_seleRow("clien_id",'clien_info',array('clien_phone'=>$val['order_clien_phone']));
            if(count($clieninfo)<=0){

                $clien=array();
                $clien['clien_name']=$val['order_clienname'];
                $clien['clien_phone']=$val['order_clien_phone'];
                $clien['clien_local']=$val['local_address'];
                $clien['clien_photo']='https://wx1.sbimg.cn/2020/07/08/C65YI.th.jpg';//默认微信头像
                $db_clienresult=$this->SysModel->table_addRow('clien_info',$clien);


            }

            $val['order_date']=date('Y-m-d H:i');
            $db_result=$this->SysModel->table_addRow('order_info',$val);
            if($db_result>=0){

                $order_id=$this->SysModel->table_seleRow("order_id",'order_info',array('order_clien_phone'=>$val['order_clien_phone'],'local_address'=>$val['local_address']));
                $statue=$this->SysModel->table_seleRow("statue_name",'statue_info',array('statue_id'=>$val['order_statue']));
                if(count($order_id)>0){
                    if(count($statue)>0){
                        $his_ord['order_id']=$order_id[0]['order_id'];
                        $his_ord['history_manager']=$user_id;
                        $his_ord['history_Data']=date('Y-m-d H:i');
                        $his_ord['history_statue']=$statue[0]['statue_name'];
                        $db_hisresult=$this->SysModel->table_addRow('order_history',$his_ord);
                    }
                    else{
                        $result['code']=false;
                        $result['msg']='订单状态未选择';
                    }

                }
                if($db_hisresult>=0){
                    $result['code']=true;
                    $result['msg']='订单添加成功';

                }
                else{
                    $result['code']=false;
                    $result['msg']='订单添加失败';
                }

            }
            else{
                $result['code']=false;
                $result['msg']='订单添加失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='订单添加失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);


    }

    //修改
    public function updateOrder(){

        $result=array();
        $val=$this->input->post('user_val');
        $u_id=$this->input->post('order_id');
        $user_id=$this->session->userdata('user_id');
        if($val && $u_id){



            //同步更新Clien
            $clien=array();
            $whereclien=array('clien_phone'=>$val['order_clien_phone']);
            $clien['clien_name']=$val['order_clienname'];
            $clien['clien_phone']=$val['order_clien_phone'];
            $clien['clien_local']=$val['local_address'];
            $db_result=$this->SysModel->table_updateRow('clien_info',$clien,$whereclien);

            $order_statue=$this->SysModel->table_seleRow("order_statue",'order_info',array('order_id'=>$u_id));

            if($val['order_statue']!=$order_statue[0]['order_statue']){
                $statue=$this->SysModel->table_seleRow("statue_name",'statue_info',array('statue_id'=>$val['order_statue']));
                if(count($statue)>0){

                    $his_ord['history_statue']=$statue[0]['statue_name'];
                }
                $his_ord['order_id']=$u_id;
                $his_ord['history_manager']=$user_id;
                $his_ord['history_Data']=date('Y-m-d H:i');

                $db_hisresult=$this->SysModel->table_addRow('order_history',$his_ord);
            }



            $where_Data=array('order_id'=>$u_id);

            $db_result=$this->SysModel->table_updateRow('order_info',$val,$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='订单信息修改成功';

            }
            else{
                $result['code']=false;
                $result['msg']='订单信息修改失败';
            }
        }
        else{
            $result['code']=false;
            $result['msg']='订单信息修改失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }

    //删除
    public function delOrder(){

        $result=array();
        $o_id=$this->input->post('order_id');
        if($o_id){
            $where_Data=array('order_id'=>$o_id);
            $db_result=$this->SysModel->table_del('order_info',$where_Data);
            if($db_result>=0){
                $result['code']=true;
                $result['msg']='订单信息删除成功';

            }
            else{
                $result['code']=false;
                $result['msg']='订单信息删除失败';
            }

        }
        else{
            $result['code']=false;
            $result['msg']='订单信息删除失败,无法获取数据';
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($result);





    }








}
