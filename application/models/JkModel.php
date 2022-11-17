<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/23
 * Time: 20:41
 */
class JkModel extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->db =$this->load->database('jkodb', true);


    }



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