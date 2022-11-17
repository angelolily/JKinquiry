<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/23
 * Time: 20:41
 */
class SysModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database('default');


    }



    //获取全部业务组数据
    public function get_All_Goup($pages,$rows,$wheredata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('group_id');
        if(count($wheredata)>0){
            $this->db->like($wheredata);//判断需不需要查询
        }
        $this->db->from('group_info');
        $total=$this->db->count_all_results();//查询总条数


        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->like($wheredata);//判断需不需要查询
        }
        $this->db->limit($rows,$offset);
        $query = $this->db->get('group_info');

        $ss=$this->db->last_query();

        $row_arr=$query->result_array();

        foreach ($row_arr as $row)
        {
//            参数修改
            if(!(is_null($row['group_scheme']))){
                $group_schemeText="";
                $schemarray=$this->table_seleRow("scheme_id,scheme_name",'scheme_info');
                $gscheme=explode(',',$row['group_scheme']);
                foreach ($gscheme as $grow){
                    foreach ($schemarray as $srow){
                        if($srow['scheme_id']==$grow){
                            $group_schemeText=$group_schemeText.$srow['scheme_name'].',';
                        }

                    }


                }



                $row['group_schemText']=$group_schemeText;
            }

            array_push($data, $row);
        }

        $result['count']=$total;//获取总行数
        $result["data"] = $data;

        return $result;

    }

    //获取全部状态数据
    public function get_All_Statue($pages,$rows,$wheredata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('statue_id');
        if(count($wheredata)>0){
            $this->db->like($wheredata);//判断需不需要查询
        }
        $this->db->from('statue_info');
        $total=$this->db->count_all_results();//查询总条数


        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->like($wheredata);//判断需不需要查询
        }
        $this->db->limit($rows,$offset);
        $query = $this->db->get('statue_info');

        $ss=$this->db->last_query();

        $row_arr=$query->result_array();

        foreach ($row_arr as $row)
        {

            array_push($data, $row);
        }

        $result['count']=$total;//获取总行数
        $result["data"] = $data;

        return $result;

    }



    //获取全部微信菜单
    public function get_All_Wmenu($pages,$rows,$wheredata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('menu_id');
        if(count($wheredata)>0){
            $this->db->like($wheredata);//判断需不需要查询
        }
        $this->db->from('wechart_menu');
        $total=$this->db->count_all_results();//查询总条数


        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->like($wheredata);//判断需不需要查询
        }
        $this->db->limit($rows,$offset);
        $query = $this->db->get('wechart_menu');

        $ss=$this->db->last_query();

        $row_arr=$query->result_array();

        foreach ($row_arr as $row)
        {

            array_push($data, $row);
        }

        $result['count']=$total;//获取总行数
        $result["data"] = $data;

        return $result;

    }

    //获取全部方案数据
    public function get_All_Scheme($pages,$rows,$wheredata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('scheme_id');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需要查询
        }
        $this->db->from('scheme_info');
        $total=$this->db->count_all_results();//查询总条数


        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->like($wheredata);//判断需不需要查询
        }
        $this->db->limit($rows,$offset);
        $query = $this->db->get('scheme_info');

        $ss=$this->db->last_query();

        $row_arr=$query->result_array();

        foreach ($row_arr as $row)
        {
//            参数修改
//            if(!(is_null($row['group_tatue']))){
//                $row['group_tatue']=$this->serarchName($row['c_skemp']);
//            }

            array_push($data, $row);
        }

        $result['count']=$total;//获取总行数
        $result["data"] = $data;

        return $result;

    }

    //获取全部用户数据
    public function get_All_User($pages,$rows,$wheredata,$likedata){

        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('user_id');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要查询
        }
        $this->db->from('userinfo');
        $total=$this->db->count_all_results();//查询总条数


        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要查询
        }
        $this->db->limit($rows,$offset);
        $query = $this->db->get('userinfo');

        $ss=$this->db->last_query();

        $row_arr=$query->result_array();

        foreach ($row_arr as $row)
        {
            //参数修改
            if(!(is_null($row['user_group']))){

                $g_name=$this->table_seleRow('group_name','group_info',array('group_id'=>$row['user_group']));

                if(count($g_name)>0){

                    $row['user_group_name']= $g_name[0]['group_name'];
                }


            }

            array_push($data, $row);
        }

        $result['count']=$total;//获取总行数
        $result["data"] = $data;

        return $result;

    }

    //获取客户数据
    public function get_All_Clien($pages,$rows,$wheredata,$likedata,$wherein){
        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('clien_id');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需like要查询
        }
        if(count($wherein)>0){
            $this->db->where_in('clien_userid',$wherein);//判断需不需要wherein查询
        }
        $ss=$this->db->last_query();
        $this->db->from('clien_info');
        $total=$this->db->count_all_results();//查询总条数

        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需like要查询
        }
        if(count($wherein)>0){
            $this->db->where_in('clien_userid',$wherein);//判断需不需要wherein查询
        }
        $this->db->limit($rows,$offset);
        $this->db->order_by('clien_id','desc');
        $query = $this->db->get('clien_info');
        $ss=$this->db->last_query();
        $row_arr=$query->result_array();


        foreach ($row_arr as $row)
        {
            //参数修改
            if(!(is_null($row['clien_userid']))){

                $clien_name=$this->table_seleRow('user_name','userinfo',array('user_id'=>$row['clien_userid']));

                if(count($clien_name)>0){

                    $row['clien_userid_name']= $clien_name[0]['user_name'];
                }
                else{
                    $row['clien_userid_name']='无';
                }

            }

            array_push($data, $row);
        }





        $result['count']=$total;//获取总行数
        $result["data"] =$data;

        return $result;

    }

    //获取订单数据
    public function get_All_Order($pages,$rows,$wheredata,$likedata,$wherein){
        $offset=($pages-1)*$rows;//计算偏移量
        $data = array();


        $this->db->select('order_id');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需like要查询
        }
        if(count($wherein)>0){
            $this->db->where_in('order_manager',$wherein);//判断需不需要wherein查询
        }
        $ss=$this->db->last_query();
        $this->db->from('order_info');
        $total=$this->db->count_all_results();//查询总条数

        $this->db->select('*');
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需like要查询
        }
        if(count($wherein)>0){
            $this->db->where_in('order_manager',$wherein);//判断需不需要wherein查询
        }
        $this->db->limit($rows,$offset);
        $this->db->order_by('order_id','desc');
        $query = $this->db->get('order_info');
        $ss=$this->db->last_query();
        $row_arr=$query->result_array();


        foreach ($row_arr as $row)
        {
            //参数修改
            if(!(is_null($row['order_manager']))){

                $order_manager=$this->table_seleRow('user_name','userinfo',array('user_id'=>$row['order_manager']));

                if(count($order_manager)>0){

                    $row['order_userid_name']= $order_manager[0]['user_name'];
                }
                else{
                    $row['order_userid_name']='无';
                }

            }

            //参数修改
            if(!(is_null($row['order_statue']))){

                $order_manager=$this->table_seleRow('statue_name','statue_info',array('statue_id'=>$row['order_statue']));

                if(count($order_manager)>0){

                    $row['order_statue_name']= $order_manager[0]['statue_name'];
                }
                else{
                    $row['order_statue_name']='无';
                }

            }

            array_push($data, $row);
        }





        $result['count']=$total;//获取总行数
        $result["data"] =$data;

        return $result;

    }
    //limit orderc查询

    public function table_seleRow_limit($field,$taname,$wheredata=array(),$likedata=array(),$limit=1,$order=null,$order_type=null){


        $this->db->select($field);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要like查询
        }
        $this->db->limit($limit);
        if(!(is_null($order))){

            $this->db->order_by($order,$order_type);

        }

        $query = $this->db->get($taname);
        $ss=$this->db->last_query();

        $rows_arr=$query->result_array();

        return $rows_arr;


    }

    //插入记录
    public function table_addRow($taname,$values){

        $this->db->insert($taname,$values);
        $result = $this->db->affected_rows();

        return $result;

    }

    //查询记录
    public function table_seleRow($field,$taname,$wheredata=array(),$likedata=array()){

        $this->db->select($field);
        if(count($wheredata)>0){
            $this->db->where($wheredata);//判断需不需where要查询
        }
        if(count($likedata)>0){
            $this->db->like($likedata);//判断需不需要like查询
        }
        $query = $this->db->get($taname);

        $ss=$this->db->last_query();

        $rows_arr=$query->result_array();

        return $rows_arr;

    }

    //修改记录
    public function table_updateRow($taname,$values,$wheredata){

        $this->db->where($wheredata);
        $this->db->update($taname,$values);
        $result = $this->db->affected_rows();

        return $result;

    }

    //删除记录
    public function table_del($taname,$wheredata){
        $this->db->where($wheredata);
        $this->db->delete($taname);
        $result = $this->db->affected_rows();

        return $result;
    }






}