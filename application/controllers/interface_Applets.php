<?php
defined('BASEPATH') OR exit('No direct script access allowed');
header('Content-type: text/html; charset=utf-8');
class interface_Applets extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('SysModel');
        $this->load->model('JkModel');
        $this->load->helper('url');
        $this->load->library('encrypt');
        $this->load->library('encryption');
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header('Access-Control-Allow-Headers:Origin,Content-Type,Accept,token,X-Requested-With,device');
    }

    private function create_guid() {
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);// "}"
        return $uuid;
    }

    private static $userId ='5f1e450705681';    //配置用户keyid
    //验证用户信息
    private function VerifyUser($userinfo){

        $assdata['data']='';
        $assdata[ "errorCode"]="";
        $assdata[ "ErrorMessage"]="";
        $assdata[ "Success"]=true;
        if (count($userinfo) > 0) {

            //判断用户次数
            if ($userinfo[0]['c_userquerynum'] <= 0) {
                $assdata['data']='';
                $assdata[ "errorCode"]="userquernum-error";
                $assdata[ "ErrorMessage"]="用户查询次数已经用完,无法查询";
                $assdata[ "Success"]=false;
            }

            //判断用户是否冻结
            if ($userinfo[0]['c_userstatue'] == 0) {
                $assdata['data']='';
                $assdata[ "errorCode"]="userfreeze-error";
                $assdata[ "ErrorMessage"]="用户已被冻结,无法查询";
                $assdata[ "Success"]=false;
            }
            //判断用户所在渠道是否冻结
            if ($userinfo[0]['c_banstatue'] == 0) {
                $assdata['data']='';
                $assdata[ "errorCode"]="channelfreeze-error";
                $assdata[ "ErrorMessage"]="渠道已被冻结,无法查询";
                $assdata[ "Success"]=false;
            }
            //判断用户所在渠道类型
            if ($userinfo[0]['c_jgType'] != "type_1") {
                $assdata['data']='';
                $assdata[ "errorCode"]="channeltype-error";
                $assdata[ "ErrorMessage"]="渠道类型错误,无法查询";
                $assdata[ "Success"]=false;
            }
        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="queryuserinfo-error";
            $assdata[ "ErrorMessage"]="用户信息不存在";
            $assdata[ "Success"]=false;
        }



        return $assdata;



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
    //城市转换 $serachtype:1 传入城市id，获取城市名称
    private function transfcity($city,$serachtype=1){
        $cityidnamd=array('350100'=>'福州市','350200'=>'厦门市','350300'=>'莆田市','350400'=>'三明市','350500'=>'泉州市','350600'=>'漳州市','350700'=>'南平市','350800'=>'龙岩市','350900'=>'宁德市');
        if($serachtype==1){
            return $cityidnamd[$city];

        }

    }
    //post请求操作
    public function postcurl($url,$fileddata){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileddata);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $tmpInfo = curl_exec($ch); // 执行操作
        if (curl_errno($ch)) {
            echo 'Errno'.curl_error($ch);//捕抓异常
        }
        curl_close($ch); // 关闭CURL会话
        return $tmpInfo; // 返回数据，json格式*/

    }

    //获取房屋详细信息
    public function getInfoDetail(){
        $assdata=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("HousesInfoId", $info)){
            $info['appkey']=self::$userId;
            $so=json_encode($info);
            $info=$this->postcurl("http://eping.hdzxpg.cn/HDEP/interface_Hdprice/IF_getHDInfoDetail",$so);
            $result=json_decode($info,true);
            if(count($result)>0){
                $assdata['data']=$result['Data'];
                $assdata[ "errorCode"]="";
                $assdata[ "ErrorMessage"]="";
                $assdata[ "Success"]=true;
                $assdata[ "Status_Code"]=200;

            }

        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);


    }
    //获取方案信息
    public function getScheme()
    {
        $group_id = array();
        $result_data=array();
        $wherestr = "";
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("clien_openid", $info) && array_key_exists("clien_userid", $info) && array_key_exists("EvaluateAllPrice", $info)) {

            if ($info['clien_userid'] != "") {
                $group_id = $this->SysModel->table_seleRow("user_group", 'userinfo', array('user_id' => $info['clien_userid']));
                if (count($group_id) > 0) {
                    $scheme_id = $this->SysModel->table_seleRow("group_scheme", 'group_info', array('group_id' => $group_id[0]['user_group']));
                    if (count($scheme_id) > 0) {
                        $arr_row = explode(",", $scheme_id[0]['group_scheme']);
                        if (count($arr_row) > 0) {
                            $tmpstr = " scheme_id";
                            foreach ($arr_row as $srow) {
                                $wherestr .= $tmpstr . '=' . $srow . ' or';
                            }
                            $wherestr=substr($wherestr, 0,strlen($wherestr)-2);
                        }

                    }

                }
            }

            if($wherestr==""){
                $wherestr=array('scheme_statue'=>1);

            }

            $scheme_data=$this->SysModel->table_seleRow("scheme_name,loan_amount,loan_rate,loan_year,repayment_way,order_Repayment", 'scheme_info', $wherestr);;

            $tmp_scheme=array();
            foreach ($scheme_data as $row){

                $row['loan_amount']=intval($row['loan_amount']*$info['EvaluateAllPrice']);
                $row['order_Repayment']=$row['loan_amount']*10000*($row['loan_rate']/1000);
                $row['order_Repayment']=intval($row['order_Repayment']);

                array_push($result_data,$row);

            }
            $assdata['data']=$result_data;
            $assdata[ "errorCode"]="0";
            $assdata[ "ErrorMessage"]="";
            $assdata[ "Success"]=true;
            $assdata[ "Status_Code"]=200;
        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;

        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);


    }
    //get获取JSON
    private function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }
    //用户登陆
    public function login1(){
        $appid = "wx71b2317f2015c429";
        $secret = "c94eb5b0801ee5d5046a6ff84069a2ac";

//        $appid = "wx93cb0390799f1959";
//        $secret = "11e0966b745047b1b9debe3573d88dc1";
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        if(count($info)==3){
            if (array_key_exists("code", $info) && array_key_exists("clien_userid", $info) && array_key_exists("clien_openid", $info) ) {


                if($info['code']!=""){

                    //第一步:取全局access_token

                    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
                    $token = $this->getJson($url);

                    if(array_key_exists("errcode", $token)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$token['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=601;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$token['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }

                    //第二步:取得openid
                    $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code={$info['code']}&grant_type=authorization_code";
                    $oauth2 = $this->getJson($oauth2Url);

                    if(array_key_exists("errcode", $oauth2)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$oauth2['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=602;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$oauth2['errmsg']);
                        echo json_encode($assdata);

                        return true;

                    }


                    //第三步:根据全局access_token和openid查询用户信息
                    $access_token = $token["access_token"];
                    $openid = $oauth2['openid'];
                    $get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
                    $userinfo = $this->getJson($get_user_info_url);
                    if(array_key_exists("errcode", $userinfo)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$userinfo['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=603;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$userinfo['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }
                    if(count($userinfo)>0){
                        $info['clien_nickname']=array_key_exists('nickname',$userinfo)?$userinfo['nickname']:'';
                        $info['clien_sex']=array_key_exists('sex',$userinfo)?$userinfo['sex']:'';
                        $info['clien_openid']=$openid;
                        $info['clien_city']=array_key_exists('city',$userinfo)?$userinfo['city']:'';
                        $info['clien_photo']=array_key_exists('headimgurl',$userinfo)?$userinfo['headimgurl']:'';

                    }
                    else{
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]="获取用户信息失败";
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=303;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        echo json_encode($userinfo);
                        return true;
                    }

                }
                $clien_info = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                if(count($clien_info)>0){
                    if($clien_info[0]['clien_userid']!=""){
                        //原有业务经理，无需更新业务经理
                        $info=$this->bykey_reitem($info,'clien_userid');
                    }
                    $info=$this->bykey_reitem($info,'code');
                    $isok=$this->SysModel->table_updateRow('clien_info',$info,array('clien_openid' => $info['clien_openid']));
                    if($isok>=0){
                        $clineinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                        $userinfo=$this->SysModel->table_seleRow("user_name,user_phone", 'userinfo', array('user_id' => $clineinfo[0]['clien_userid']));
                        if(count($userinfo)>0 && count($clineinfo)>0){
                            $clineinfo[0]['manager_name']=$userinfo[0]['user_name'];
                            $clineinfo[0]['manager_phone']=$userinfo[0]['user_phone'];
                        }

                        $assdata['data']=$clineinfo[0];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="客户信息更新成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=200;
                    }
                    else{
                        $assdata['data']='';
                        $assdata["errorCode"]="addClien-error";
                        $assdata["ErrorMessage"]="客户信息更新失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=300;
                    }
                }
                else{
                    $info=$this->bykey_reitem($info,'code');
                    $isok=$this->SysModel->table_addRow('clien_info',$info);
                    if($isok>0){
                        $clineinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                        $userinfo=$this->SysModel->table_seleRow("user_name,user_phone", 'userinfo', array('user_id' => $clineinfo[0]['clien_userid']));
                        if(count($clien_info)>0 && count($userinfo)>0){
                            $clineinfo[0]['manager_name']=$userinfo[0]['user_name'];
                            $clineinfo[0]['manager_phone']=$userinfo[0]['user_phone'];
                        }

                        $assdata['data']=$clineinfo[0];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="客户新增成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=200;
                    }
                    else{
                        $assdata['data']='';
                        $assdata["errorCode"]="addClien-error";
                        $assdata["ErrorMessage"]="客户新增失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=301;
                    }

                }

            }
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);
    }
    public function getFuzzyMatching1(){
        $tmpHouse1=array();
        $tmpHouse2=array();
        $agentinfo = file_get_contents('php://input');
        if($agentinfo!=""){
            $info = json_decode($agentinfo, true);
            if (array_key_exists("cityCode", $info) && array_key_exists("LikeHousesName", $info) && array_key_exists("MaxSearchCount", $info)) {

                $LikeHousesName=$info['LikeHousesName'];
                $info['MaxSearchCount']=50;
                $so=json_encode($info);
                $info=$this->postcurl("http://eping.hdzxpg.cn/HDEP/interface_Hdprice/getHDFuzzyMatching",$so);
                $assdata=json_decode($info,true);
                if(count($assdata)>0){

                    $march="/^".$LikeHousesName.".*/";
                    foreach ($assdata['Data']['List'] as $row){

                        if(preg_match($march,$row['HousesShowName'])){

                            array_push($tmpHouse1,$row);


                        }
                        else{
                            array_push($tmpHouse2,$row);
                        }
                    }
                }

                foreach ($tmpHouse2 as $tmprow){

                    array_push($tmpHouse1,$tmprow);

                }


                $assdata['Data']['List']=$tmpHouse1;
                $assdata[ "Status_Code"]=200;
            }
            else{
                $assdata['data']='';
                $assdata[ "errorCode"]="parameter-error";
                $assdata[ "ErrorMessage"]="参数接收错误";
                $assdata[ "Success"]=false;
                $assdata[ "Status_Code"]=400;
            }
        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收失败";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //用户登陆
    public function login(){
        $appid = "wx71b2317f2015c429";
        $secret = "c94eb5b0801ee5d5046a6ff84069a2ac";

//        $appid = "wx93cb0390799f1959";
//        $secret = "11e0966b745047b1b9debe3573d88dc1";
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        if(count($info)==3){
            if (array_key_exists("code", $info) && array_key_exists("clien_userid", $info) && array_key_exists("clien_openid", $info) ) {


                if($info['code']!=""){

                    //第一步:取全局access_token

                    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
                    $token = $this->getJson($url);

                    if(array_key_exists("errcode", $token)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$token['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=601;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$token['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }

                    //第二步:取得openid
                    $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code={$info['code']}&grant_type=authorization_code";
                    $oauth2 = $this->getJson($oauth2Url);

                    if(array_key_exists("errcode", $oauth2)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$oauth2['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=602;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$oauth2['errmsg']);
                        echo json_encode($assdata);

                        return true;

                    }


                    //第三步:根据全局access_token和openid查询用户信息
                    $access_token = $token["access_token"];
                    $openid = $oauth2['openid'];
                    $get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
                    $userinfo = $this->getJson($get_user_info_url);
                    if(array_key_exists("errcode", $userinfo)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$userinfo['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=603;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$userinfo['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }
                    if(count($userinfo)>0){
                        $info['clien_nickname']=array_key_exists('nickname',$userinfo)?$userinfo['nickname']:'';
                        $info['clien_sex']=array_key_exists('sex',$userinfo)?$userinfo['sex']:'';
                        $info['clien_openid']=$openid;
                        $info['clien_city']=array_key_exists('city',$userinfo)?$userinfo['city']:'';
                        $info['clien_photo']=array_key_exists('headimgurl',$userinfo)?$userinfo['headimgurl']:'';

                    }
                    else{
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]="获取用户信息失败";
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=303;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        echo json_encode($userinfo);
                        return true;
                    }

                }
                $clien_info = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                if(count($clien_info)>0){
                    if($clien_info[0]['clien_userid']!=""){
                        //原有业务经理，无需更新业务经理
                        $info=$this->bykey_reitem($info,'clien_userid');
                    }
                    $info=$this->bykey_reitem($info,'code');
                    $isok=$this->SysModel->table_updateRow('clien_info',$info,array('clien_openid' => $info['clien_openid']));
                    if($isok>=0){
                        $clineinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                        $userinfo=$this->SysModel->table_seleRow("user_name,user_phone", 'userinfo', array('user_id' => $clineinfo[0]['clien_userid']));
                        if(count($userinfo)>0 && count($clineinfo)>0){
                            $clineinfo[0]['manager_name']=$userinfo[0]['user_name'];
                            $clineinfo[0]['manager_phone']=$userinfo[0]['user_phone'];
                        }

                        $assdata['data']=$clineinfo[0];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="客户信息更新成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=200;
                    }
                    else{
                        $assdata['data']='';
                        $assdata["errorCode"]="addClien-error";
                        $assdata["ErrorMessage"]="客户信息更新失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=300;
                    }
                }
                else{
                    $info=$this->bykey_reitem($info,'code');
                    $isok=$this->SysModel->table_addRow('clien_info',$info);
                    if($isok>0){
                        $clineinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                        $userinfo=$this->SysModel->table_seleRow("user_name,user_phone", 'userinfo', array('user_id' => $clineinfo[0]['clien_userid']));
                        if(count($clien_info)>0 && count($userinfo)>0){
                            $clineinfo[0]['manager_name']=$userinfo[0]['user_name'];
                            $clineinfo[0]['manager_phone']=$userinfo[0]['user_phone'];
                        }

                        $assdata['data']=$clineinfo[0];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="客户新增成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=200;
                    }
                    else{
                        $assdata['data']='';
                        $assdata["errorCode"]="addClien-error";
                        $assdata["ErrorMessage"]="客户新增失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=301;
                    }

                }

            }
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);
    }

    //删除实勘照片目录
    private function deldir($dir) {
        //先删除目录下的文件：
        if(is_dir($dir)){
            $dh=opendir($dir);
            while ($file=readdir($dh)) {
                if($file!="." && $file!="..") {
                    $fullpath=$dir."/".$file;
                    if(!is_dir($fullpath)) {
                        unlink($fullpath);
                    } else {
                        deldir($fullpath);
                    }
                }
            }
        }
        closedir($dh);
        //删除当前文件夹：
        if(rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    //1、模糊地址匹配
    public function getFuzzyMatching(){
        $tmpHouse1=array();
        $tmpHouse2=array();
        $housename=array();
        $assdata=array();
        $agentinfo = file_get_contents('php://input');
        if($agentinfo!=""){
            $info = json_decode($agentinfo, true);
            if (array_key_exists("cityCode", $info) && array_key_exists("LikeHousesName", $info) && array_key_exists("MaxSearchCount", $info)) {

                $LikeHousesName=$info['LikeHousesName'];
                $info['MaxSearchCount']=50;
                $so=json_encode($info);



                //先是使用，自建库
                $housename=$this->SysModel->table_seleRow('c_houseid as HousesInfoId,c_housename as HousesShowName','tbl_price',array(),array('c_housename'=>$LikeHousesName));

                if(count($housename)>0){
                    $assdata['Success']=true;
                    $assdata['ErrorCode']=0;
                    $assdata['ErrorMessage']=null;
                    $assdata['Data']['List']=$housename;

                }
                //然后使用第三方渠道
                else{
                    $info=$this->postcurl("http://eping.hdzxpg.cn/HDEP/interface_Hdprice/getHDFuzzyMatching",$so);
                    $assdata=json_decode($info,true);
                }


                if(count($assdata)>0){
                    $march="/^".$LikeHousesName.".*/";
                    foreach ($assdata['Data']['List'] as $row){

                        if(preg_match($march,$row['HousesShowName'])){

                            array_push($tmpHouse1,$row);


                        }
                        else{
                            array_push($tmpHouse2,$row);
                        }
                    }
                    foreach ($tmpHouse2 as $tmprow){

                        array_push($tmpHouse1,$tmprow);

                    }
                    $assdata['Data']['List']=$tmpHouse1;
                    $assdata[ "Status_Code"]=200;
                }
                else{
                    $assdata['data']='';
                    $assdata[ "errorCode"]="quere-data-zero";
                    $assdata[ "ErrorMessage"]="查找不到地址";
                    $assdata[ "Success"]=false;
                    $assdata[ "Status_Code"]=500;

                }
            }
            else{
                $assdata['data']='';
                $assdata[ "errorCode"]="parameter-error";
                $assdata[ "ErrorMessage"]="参数接收错误";
                $assdata[ "Success"]=false;
                $assdata[ "Status_Code"]=400;
            }
        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收失败";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }
    public function getEvaluate(){

        $assdata=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("CarportFloor", $info) && array_key_exists("c_useropenid", $info) && array_key_exists("address", $info) && array_key_exists("cityCode", $info) && array_key_exists("HousesInfoId", $info) && array_key_exists("LegalUsage", $info) && array_key_exists("PropertyRegFullTwoYear", $info) && array_key_exists("HasLift", $info) && array_key_exists("BuildingArea", $info)) {

            //先判断是否使用自建库
            if(strpos($info['HousesInfoId'],'-')){

                $housename=$this->SysModel->table_seleRow('c_avg_Pricee','tbl_price',array('c_houseid'=>$info['HousesInfoId']),array());

                if(count($housename)>0){
                    //判断是否有电梯
                    $area=$info['BuildingArea'];
                    $unit_price=$housename[0]['c_avg_Pricee'];//挂牌均价（单价）

                    //40000元的调整
                    if($unit_price>=40000){
                        $unit_price=round($unit_price*0.8);
                    }
                    else{
                        $unit_price=round($unit_price*0.9);
                    }

                    if($info['HasLift']==1){
                        switch ($area){
                            case $area<=60:$unit_price=round($unit_price*1.08);break;
                            case ($area>60 && $area<=90):$unit_price=round($unit_price*1.05);break;
                            case ($area>100 && $area<=150):$unit_price=round($unit_price*0.95);break;
                            case ($area>150 && $area<=200):$unit_price=round($unit_price*0.9);break;
                        }

                    }
                    else{
                        switch ($area){
                            case $area<=60:$unit_price=round($unit_price*1.1);break;
                            case ($area>60 && $area<=90):$unit_price=round($unit_price*1.05);break;
                            case ($area>100 && $area<=150):$unit_price=round($unit_price*0.95);break;
                            case ($area>150 && $area<=200):$unit_price=round($unit_price*0.9);break;
                        }

                    }

                    $assdata['Success']=true;
                    $assdata['ErrorCode']=0;
                    $assdata['ErrorMessage']=null;
                    $assdata["Status_Code"]=200;
                    $unit_price=$unit_price*0.9;
                    $total_price=round($unit_price*$area/10000);
                    //税后计算
                    if($info['LegalUsage']=='住宅'){
                        if($info['PropertyRegFullTwoYear']==1){
                            $sfxs=0.99;
                        }
                        else{
                            $sfxs=0.933;
                        }
                    }
                    else{
                        $sfxs=1;
                    }
                    $assdata['data']['EvaluateUnitPrice']=$unit_price;
                    $assdata['data']['EvaluateAllPrice']=$total_price;
                    $assdata['data']['EvaluateunitPriceAT']=round($unit_price*$sfxs);
                    $assdata['data']['EvaluateAllPriceAT']=round($unit_price*$sfxs*$area/10000);

                }

            }
            else{

                $info['appkey']=self::$userId;
                $res_tmp=$info;
                $so=json_encode($info);
                $info=$this->postcurl("http://eping.hdzxpg.cn/HDEP/interface_Hdprice/IF_getEvaluate",$so);
                $result=json_decode($info,true);
                if(count($result)>0){

                    //插入到价格库
                    $uid=$this->create_guid();
                    $ins_price=array('c_houseid'=>$uid,
                        'c_housename'=>$res_tmp['address'],
                        'c_house_type'=>$res_tmp['LegalUsage'],
                        'c_house_type'=>$res_tmp['LegalUsage'],
                        'c_avg_Pricee'=>$result['Data']['EvaluateUnitPrice'],
                        'c_city'=>$res_tmp['cityCode'],'c_sources'=>'HD',
                        'c_updatetime'=>date('Y-m-d H:i:s'));
                    $this->SysModel->table_addRow('tbl_price',$ins_price);

                    $assdata['data']=$result['Data'];
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="";
                    $assdata["Success"]=true;
                    $assdata["Status_Code"]=200;

                }

            }


            //插入到历史查询记录


            $his_price=['c_projname'=>$info['address'],
                'c_jzarea'=>$info['BuildingArea'],
                'c_Elevator'=>$info['HasLift'],
                'c_customManger'=>$info['c_useropenid'],
                'c_BuiltYear'=>$info['PropertyRegFullTwoYear'],
                'c_sqprice'=>$assdata['data']['EvaluateUnitPrice'],
                'c_amount'=>$assdata['data']['EvaluateAllPrice'],
                'c_shprice'=>$assdata['data']['EvaluateunitPriceAT'],
                'c_shamount'=>$assdata['data']['EvaluateAllPriceAT'],
                'querydate'=>date('Y-m-d H:i:s'),
                'c_priceid'=>time()
            ];



            $result=$this->SysModel->table_addRow('history_price',$his_price);




        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //1、模糊地址匹配 新
    public function getFuzzyMatchingNew(){
        $tmpHouse1=array();
        $tmpHouse2=array();
        $housename=array();
        $assdata=array();
        $agentinfo = file_get_contents('php://input');
        if($agentinfo!=""){
            $info = json_decode($agentinfo, true);
            if (array_key_exists("cityCode", $info) && array_key_exists("LikeHousesName", $info) && array_key_exists("MaxSearchCount", $info)) {

                $LikeHousesName=$info['LikeHousesName'];
                $info['MaxSearchCount']=50;
                $so=json_encode($info);

                //先是使用，自建库
                $housename=$this->SysModel->table_seleRow('c_houseid as HousesInfoId,c_housename as HousesShowName','tbl_price_internal',array(),array('c_housename'=>$LikeHousesName));

                if(count($housename)>0){
                    $assdata['Success']=true;
                    $assdata['ErrorCode']=0;
                    $assdata['ErrorMessage']=null;
                    $assdata['Data']['List']=$housename;

                }


                if(count($assdata)>0){
                    $march="/^".$LikeHousesName.".*/";
                    foreach ($assdata['Data']['List'] as $row){

                        if(preg_match($march,$row['HousesShowName'])){

                            array_push($tmpHouse1,$row);


                        }
                        else{
                            array_push($tmpHouse2,$row);
                        }
                    }
                    foreach ($tmpHouse2 as $tmprow){

                        array_push($tmpHouse1,$tmprow);

                    }
                    $assdata['Data']['List']=$tmpHouse1;
                    $assdata[ "Status_Code"]=200;
                }
                else{
                    $assdata['data']='';
                    $assdata[ "errorCode"]="quere-data-zero";
                    $assdata[ "ErrorMessage"]="查找不到地址";
                    $assdata[ "Success"]=false;
                    $assdata[ "Status_Code"]=500;

                }
            }
            else{
                $assdata['data']='';
                $assdata[ "errorCode"]="parameter-error";
                $assdata[ "ErrorMessage"]="参数接收错误";
                $assdata[ "Success"]=false;
                $assdata[ "Status_Code"]=400;
            }
        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收失败";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //2、用户登陆，新
    public function newlogin(){

       $appid = "wx71b2317f2015c429";
       $secret = "c94eb5b0801ee5d5046a6ff84069a2ac";
        // $appid = "wx93cb0390799f1959";
        // $secret = "11e0966b745047b1b9debe3573d88dc1";
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        if(count($info)==4){
            if (array_key_exists("code", $info)  && array_key_exists("clien_openid", $info) && array_key_exists("login_type", $info)) {


                if($info['code']!=""){

                    //第一步:取全局access_token

                    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
                    $token = $this->getJson($url);

                    if(array_key_exists("errcode", $token)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$token['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=201;
                        header("HTTP/1.1 200 Created");
                        header("Content-type: application/json");
                        log_message("error",$token['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }

                    //第二步:取得openid
                    $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code={$info['code']}&grant_type=authorization_code";
                    $oauth2 = $this->getJson($oauth2Url);

                    if(array_key_exists("errcode", $oauth2)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$oauth2['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=202;
                        header("HTTP/1.1 200 Created");
                        header("Content-type: application/json");
                        log_message("error",$oauth2['errmsg']);
                        echo json_encode($assdata);

                        return true;

                    }


                    //第三步:根据全局access_token和openid查询用户信息
                    $access_token = $token["access_token"];
                    $openid = $oauth2['openid'];
                    $get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
                    $userinfo = $this->getJson($get_user_info_url);
                    if(array_key_exists("errcode", $userinfo)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$userinfo['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=203;
                        header("HTTP/1.1 200 Created");
                        header("Content-type: application/json");
                        log_message("error",$userinfo['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }
                    if(count($userinfo)>0){

                        $info['clien_nickname']=array_key_exists('nickname',$userinfo)?$userinfo['nickname']:'';
                        $info['clien_sex']=array_key_exists('sex',$userinfo)?$userinfo['sex']:'';
                        $info['clien_openid']=$openid;
                        $info['clien_city']=array_key_exists('city',$userinfo)?$userinfo['city']:'';
                        $info['clien_photo']=array_key_exists('headimgurl',$userinfo)?$userinfo['headimgurl']:'';


                    }
                    else{
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]="获取用户信息失败";
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=203;
                        header("HTTP/1.1 200 Created");
                        header("Content-type: application/json");
                        echo json_encode($userinfo);
                        return true;
                    }

                }
                //查找银行客户经理
                $clien_info = $this->SysModel->table_seleRow("*", 'clien_user', array('clien_openid' => $info['clien_openid']));

                if(count($clien_info)>0){


                    if($info['login_type']==1)
                    {
                        if($clien_info[0]['clien_type']==1)
                        {

                        
                            $assdata['data']=$clien_info[0];
                            $assdata["errorCode"]="";
                            $assdata["ErrorMessage"]="登陆成功";
                            $assdata["Success"]=true;
                            $assdata["Status_Code"]=200;
                        }
                        else
                        {
                            $assdata['data']=[];
                            $assdata["errorCode"]="";
                            $assdata["ErrorMessage"]="登陆失败，用户类型不匹配";
                            $assdata["Success"]=true;
                            $assdata["Status_Code"]=206;
                        }
                    }
                    else
                    {
                            $assdata['data']=$clien_info[0];
                            $assdata["errorCode"]="";
                            $assdata["ErrorMessage"]="登陆成功";
                            $assdata["Success"]=true;
                            $assdata["Status_Code"]=200;

                    }


                }
                else{
                    $assdata['data']=$info;
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="登陆失败，请绑定";
                    $assdata["Success"]=true;
                    $assdata["Status_Code"]=205;

                }

            }
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;
        }
        header("HTTP/1.1 200 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //3、绑定银行用户，新
    public function bindUser(){

        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        if(count($info)==6){
            if (array_key_exists("clien_photo", $info) && array_key_exists("clien_sex", $info) && array_key_exists("clien_nickname", $info) && array_key_exists("clien_phone", $info) && array_key_exists("clien_openid", $info) && array_key_exists("clien_name", $info)) {

                //查找银行客户经理
                $clien_info = $this->SysModel->table_seleRow("clien_phone,clien_nickname,clien_name,clien_sex,clien_phone,clien_photo,clien_openid", 'clien_user', array('clien_phone' => $info['clien_phone']));

                if(count($clien_info)>0)
                {
                    if($clien_info[0]['clien_openid']!="")
                    {
                        $assdata['data']=[];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="该微信号已绑定，请联系管理员";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=301;
                        header("HTTP/1.1 200 Created");
                        header("Content-type: application/json");
                        echo json_encode($assdata,true);
                        return true;

                    }
                    if($clien_info[0]['clien_name']!=$info['clien_name'])
                    {
                        $assdata['data']=[];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="手机号与注册姓名不相符合，请联系管理员";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=302;
                        header("HTTP/1.1 200 Created");
                        header("Content-type: application/json");
                        echo json_encode($assdata,true);
                        return true;

                    }

                    //绑定当前微信到银行客户经理
                    $result=$this->SysModel->table_updateRow("clien_user",['clien_openid'=>$info['clien_openid'],'clien_photo'=>$info['clien_photo'],'clien_nickname'=>$info['clien_nickname'],'clien_sex'=>$info['clien_sex']],['clien_phone' => $info['clien_phone']]);
                    if($result>=0)
                    {
                        $clien_info['clien_phone']=$info['clien_phone'];
                        $clien_info['clien_name']=$info['clien_name'];
                        $clien_info['clien_openid']=$info['clien_openid'];
                        $clien_info['clien_nickname']=$info['clien_nickname'];
                        $clien_info['clien_photo']=$info['clien_photo'];
                        $assdata['data']=$clien_info;
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="绑定成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=300;

                    }
                    else
                    {
                        $assdata['data']=[];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="绑定失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=303;

                    }

                }
                else{

                    $assdata['data']=[];
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="该微信号已绑定";
                    $assdata["Success"]=false;
                    $assdata["Status_Code"]=304;


                }



            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="parameter-error";
                $assdata["ErrorMessage"]="参数接收错误";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=305;
            }

        }


        header("HTTP/1.1 200 Created");
        header("Content-type: application/json");
        echo json_encode($assdata,true);




    }
    
    //4、新增银行用户,新
    public function addClineUser()
    {

        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        if(count($info)>3){

            if (array_key_exists("SecurityAccess", $info) && array_key_exists("clien_phone", $info) && array_key_exists("clien_company", $info) && array_key_exists("clien_name", $info)) {

                if($info['SecurityAccess']=="77565")
                {
                    $clien_info = $this->SysModel->table_seleRow("clien_id", 'clien_user', array('clien_phone' => $info['clien_phone']));
                    if(count($clien_info)>0)
                    {
                        $assdata['data']=[];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="该手机号已注册，请联系管理员";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=401;

                    }
                    else
                    {
                        $result=$this->SysModel->table_addRow("clien_user",['clien_phone'=>$info['clien_phone'],'clien_company'=>$info['clien_company'],'clien_name'=>$info['clien_name']]);

                        if($result>=0)
                        {

                            $assdata['data']=[];
                            $assdata["errorCode"]="";
                            $assdata["ErrorMessage"]="注册成功";
                            $assdata["Success"]=false;
                            $assdata["Status_Code"]=400;
                        }
                        else
                        {
                            $assdata['data']=[];
                            $assdata["errorCode"]="";
                            $assdata["ErrorMessage"]="注册失败，请联系管理员";
                            $assdata["Success"]=false;
                            $assdata["Status_Code"]=402;
                        }

                    }
                }
                else
                {
                    $assdata['data']=[];
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="注册失败，安全码错误";
                    $assdata["Success"]=false;
                    $assdata["Status_Code"]=403;

                }



            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="parameter-error";
                $assdata["ErrorMessage"]="参数接收错误";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=403;
            }

        }



        header("HTTP/1.1 200 Created");
        header("Content-type: application/json");
        echo json_encode($assdata,true);



        
    }

    //5、查询历史记录 新
    public function get_history_list(){
        $where_data=array();
        $query_data="";
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("c_autoNum", $info) && array_key_exists("c_customManger", $info) && array_key_exists("c_projname", $info)) {


            if($info['c_autoNum']==0)
            {
                $where_data=$query_data."c_autoNum<10000 and c_customManger='{$info['c_customManger']}' and c_projname like '%{$info['c_projname']}%'";
            }
            else
            {
                $where_data=$query_data."c_autoNum<'{$info['c_autoNum']}' and c_customManger='{$info['c_customManger']}' and c_projname like '%{$info['c_projname']}%'";
            }


            $history_info=$this->SysModel->table_seleRow_limit('c_projstate,c_autoNum,c_inquiryDate,c_customManger,c_projname,c_jzarea,c_shprice,c_shamount,c_lookhouse,c_housetel,c_projtype,c_priceid,c_rate as c_rem','history_price',$where_data,array(),30,'c_autoNum','DESC');


            if(count($history_info)>0){
                $assdata['data']=$history_info;
                $assdata[ "errorCode"]="";
                $assdata[ "ErrorMessage"]="";
                $assdata[ "Success"]=true;
                $assdata["Status_Code"]=200;
            }
            else{
                $assdata['data']='';
                $assdata[ "errorCode"]="none-data";
                $assdata[ "ErrorMessage"]="查无数据";
                $assdata[ "Success"]=false;
                $assdata["Status_Code"]=303;
            }

        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata["Status_Code"]=400;

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //6、获取询价信息 新
    public function getEvaluateNew(){

        $assdata=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("CarportFloor", $info) && array_key_exists("c_useropenid", $info) && array_key_exists("address", $info) && array_key_exists("cityCode", $info) && array_key_exists("HousesInfoId", $info) && array_key_exists("LegalUsage", $info) && array_key_exists("PropertyRegFullTwoYear", $info) && array_key_exists("HasLift", $info) && array_key_exists("BuildingArea", $info)) {



            //先判断是否使用自建库
            if(strpos($info['HousesInfoId'],'-')){

                $housename=$this->SysModel->table_seleRow('c_avg_Pricee,c_elevator_Price','tbl_price_internal',array('c_houseid'=>$info['HousesInfoId']),array());

                if(count($housename)>0){

                    $area=$info['BuildingArea'];
                    $unit_price=$housename[0]['c_avg_Pricee'];//挂牌均价（单价）

                    if($info['HasLift']==1){

                        $unit_price=empty($housename[0]['c_elevator_Price'])?$unit_price:$housename[0]['c_elevator_Price'];
                    }


                    $assdata['Success']=true;
                    $assdata['ErrorCode']=0;
                    $assdata['ErrorMessage']=null;
                    $assdata["Status_Code"]=200;

                    $total_price=round($unit_price*$area/10000);
                    //税后计算
                    if($info['LegalUsage']=='住宅'){
                        if($info['PropertyRegFullTwoYear']==1){
                            $sfxs=0.99;
                        }
                        else{
                            $sfxs=0.933;
                        }
                    }
                    else{
                        $sfxs=1;
                    }
                    $assdata['data']['EvaluateUnitPrice']=$unit_price;
                    $assdata['data']['EvaluateAllPrice']=$total_price;
                    $assdata['data']['EvaluateunitPriceAT']=round($unit_price*$sfxs);
                    $assdata['data']['EvaluateAllPriceAT']=round($unit_price*$sfxs*$area/10000);

                }

            }


            //插入到历史查询记录


            $his_price=['c_projname'=>$info['address'],
                       'c_jzarea'=>$info['BuildingArea'],
                       'c_Elevator'=>$info['HasLift'],
                       'c_customManger'=>$info['c_useropenid'],
                       'c_BuiltYear'=>$info['PropertyRegFullTwoYear'],
                       'c_sqprice'=>$assdata['data']['EvaluateUnitPrice'],
                       'c_amount'=>$assdata['data']['EvaluateAllPrice'],
                       'c_shprice'=>$assdata['data']['EvaluateunitPriceAT'],
                       'c_shamount'=>$assdata['data']['EvaluateAllPriceAT'],
                       'c_inquiryDate'=>date('Y-m-d H:i:s'),
                       'c_priceid'=>time()
                       ];



            $result=$this->SysModel->table_addRow('history_price',$his_price);




        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //7、获取银行信息 新
    public function getBankeName()
    {
        $bankdate=[];
        $bank_info = $this->SysModel->table_seleRow("c_bankname", 'bank_info');

        foreach ($bank_info as $value)
        {
            array_push($bankdate,$value['c_bankname']);
        }


        if(count($bank_info)>0)
        {
            $assdata['data']=$bankdate;
            $assdata["errorCode"]="";
            $assdata["ErrorMessage"]="";
            $assdata["Success"]=true;
            $assdata["Status_Code"]=700;

        }
        else{
            $assdata['data']=[];
            $assdata["errorCode"]="";
            $assdata["ErrorMessage"]="";
            $assdata["Success"]=true;
            $assdata["Status_Code"]=701;
        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //8、房产证，身份证
    public function manyImageUpload(){
        $resultvalue=array();
        $c_priceid=$this->input->post('c_priceid');
        $dir='D:\\phpStudy\\PHPTutorial\\WWW\\JKOffice\\uploads\\Control\\'.$c_priceid;
        if(is_dir($dir) or mkdir($dir)){
            //载入所需文件上传类库
            //配置上传参数
            $config['upload_path'] =$dir;
            $config['allowed_types'] = 'jpg|png|bmp|pdf';
            $config['overwrite'] = false;
            $config['file_name']=time().'.jpg';
            $this->load->library('upload', $config);
            //循环处理上传文件
            foreach ($_FILES as $key=>$value) {
                if (!empty($value['name'])) {
                    $_FILES[$key]['name']=time().'.jpg';
                    $_FILES[$key]['type']='image/jpeg';
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload($key)) {
                        //上传成功

                        $assdata['data']="";
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="上传成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=800;

                    } else {
                        //上传失败
                        $assdata['data']="";
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]=$this->upload->display_errors();;
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=801;


                    }
                }

            }

        }

        header("HTTP/1.1 200 Created");
        header("Content-type: application/json");
        header("Connection:Keep-Alive");
        echo json_encode($assdata);

    }

    //9、删除房产证，身份证照片
    public function delAllImgFile(){
        $pricinfo=file_get_contents('php://input');
        $resultvalue=array();
        try {
            $info = json_decode($pricinfo, true);
            if (array_key_exists("c_delfilename", $info) && array_key_exists("c_priceid", $info) ) {
                $dir='D:\\phpStudy\\PHPTutorial\\WWW\\JKOffice\\uploads\\Control\\'.$info['c_priceid']."\\".$info['c_delfilename'];
                if(file_exists($dir))
                {
                    if(!unlink($dir)){
                        $assdata['data']="";
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="删除失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=901;
                    }
                    else{
                        $assdata['data']="";
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="删除成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=900;
                    }

                }
                else
                {
                    $assdata['data']="";
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="删除成功";
                    $assdata["Success"]=true;
                    $assdata["Status_Code"]=900;

                }

            }
        } catch(Exception $ex){
            $resultvalue['code']="8002";//其他未知错误
            $resultvalue['msg']="其他未知错误";

        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //10、获取房产证目录下所有图片
    public function getImageFilename(){

        $pricinfo=file_get_contents('php://input');
        $resultvalue=array();
        $assdata=[];

        try {
            $info = json_decode($pricinfo, true);
            if (array_key_exists("c_priceid", $info)) {
                $dir_original='D:\\phpStudy\\PHPTutorial\\WWW\\JKOffice\\uploads\\Control\\'.$info['c_priceid'];

                if(is_dir($dir_original) or mkdir($dir_original)){

                //2、循环的读取目录下的所有文件
                //其中$filename = readdir($handler)是每次循环的时候将读取的文件名赋值给$filename，为了不陷于死循环，所以还要让$filename !== false。一定要用!==，因为如果某个文件名如果叫’0′，或者某些被系统认为是代表false，用!=就会停止循环*/

                $handler = opendir($dir_original);
                if($handler){
                    $arrdirfiles=array();
                    $arrfiles=array();
                    while( ($filename = readdir($handler)) !== false ) {
                        //3、目录下都会有两个文件，名字为’.'和‘..’，不要对他们进行操作
                        if($filename != "." && $filename != ".."){
                            //4、进行处理
                            //这里简单的用echo来输出文件名
                            $dirfilename="https://oa.fjspacecloud.com/JKOffice/uploads"."/Control/".$info['c_priceid'].'/'.$filename;
                            array_push($arrdirfiles,$dirfilename);
                            array_push($arrfiles,$filename);

                        }
                    }
                    //5、关闭目录
                    closedir($handler);
                    if(count($arrdirfiles)>0)
                    {
                        $resultvalue[0]['path']=$arrdirfiles;
                        $resultvalue[0]['filename']=$arrfiles;
                    }
                   

                }
                else{
                    $resultvalue[0]['path']=array();
                    $resultvalue[0]['filename']=array();

                }


                if(count($resultvalue)>0){
                    $assdata['data']=$resultvalue;
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="获取成功";
                    $assdata["Success"]=true;
                    $assdata["Status_Code"]=1000;
                }
                else{
                    $assdata['data']=[];
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="该项目下没有任何图片";
                    $assdata["Success"]=false;
                    $assdata["Status_Code"]=10001;


                }
            }
        }
        } catch(Exception $ex){
            $assdata['data']=[];
            $assdata["errorCode"]="";
            $assdata["ErrorMessage"]="其他未知错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=10002;


        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }

    //11、正式委托评估保存
    public function saveDelegation(){

        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=[];
        if (array_key_exists("c_projstate", $info) && array_key_exists("c_shprice", $info) && array_key_exists("c_shamount", $info) && array_key_exists("c_projtype", $info) && array_key_exists("c_projname", $info) && array_key_exists("c_rem", $info) && array_key_exists("c_jzarea", $info) && array_key_exists("c_priceid", $info) && array_key_exists("c_lookhouse", $info) && array_key_exists("c_housetel", $info)) {


            //1、同步到调度表

            $addProjinfo=[];
            $addProjinfo['c_projname']=$info['c_projname'];
            $addProjinfo['c_projtype']=$info['c_projtype'];
            $addProjinfo['c_jzarea']=$info['c_jzarea'];
            $addProjinfo['c_lookhouse']=$info['c_lookhouse'];
            $addProjinfo['c_housetel']=$info['c_housetel'];
            $addProjinfo['c_control_id']="13850156038";
            $addProjinfo['c_rem']=$info['c_rem']."询价：税前单价：".$info['c_shprice']."税后总价：".$info['c_shamount'];


            if($info['c_projstate']=="")
            {
                $addProjinfo['c_projstate']=$info['c_projstate'];
                $addProjinfo['c_projid']=$info['c_priceid'];
                $isprojok=$this->JkModel->table_addRow('jko_projinfotb',$addProjinfo);
            }
            else
            {
                $isprojok=$this->JkModel->table_updateRow('jko_projinfotb',$addProjinfo,['c_projid'=>$info['c_priceid']]);

                }


            //2、更新询价历史记录表中的信息
            if($isprojok>=0)
            {
                $upHistoryPrice=[];
                $upHistoryPrice['c_lookhouse']=$info['c_lookhouse'];
                $upHistoryPrice['c_housetel']=$info['c_housetel'];
                $upHistoryPrice['c_rate']=$info['c_rem'];
                $upHistoryPrice['c_projstate']='委托中';
                $upHistoryPrice['c_projtype']=$info['c_projtype'];
                $ishisok=$this->SysModel->table_updateRow('history_price',$upHistoryPrice,array('c_priceid' => $info['c_priceid']));
                if($ishisok>0)
                {
                    $assdata['data']='';
                    $assdata[ "errorCode"]="";
                    $assdata[ "ErrorMessage"]="委托成功";
                    $assdata[ "Success"]=true;
                    $assdata[ "Status_Code"]=1100;
                }
            }
            else
            {
                $assdata['data']='';
                $assdata[ "errorCode"]="";
                $assdata[ "ErrorMessage"]="委托失败";
                $assdata[ "Success"]=false;
                $assdata[ "Status_Code"]=1101;

            }



        }
        else
        {
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=1102;
        }


        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }



















    //测试微信公众号用户登陆
    public function login2(){
        //$appid = "wx71b2317f2015c429";
        //$secret = "c94eb5b0801ee5d5046a6ff84069a2ac";

        $appid = "wx93cb0390799f1959";
        $secret = "11e0966b745047b1b9debe3573d88dc1";
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        if(count($info)==3){
            if (array_key_exists("code", $info) && array_key_exists("clien_userid", $info) && array_key_exists("clien_openid", $info) ) {


                if($info['code']!=""){

                    //第一步:取全局access_token

                    $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$secret";
                    $token = $this->getJson($url);

                    if(array_key_exists("errcode", $token)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$token['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=601;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$token['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }

                    //第二步:取得openid
                    $oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$secret&code={$info['code']}&grant_type=authorization_code";
                    $oauth2 = $this->getJson($oauth2Url);

                    if(array_key_exists("errcode", $oauth2)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$oauth2['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=602;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$oauth2['errmsg']);
                        echo json_encode($assdata);

                        return true;

                    }


                    //第三步:根据全局access_token和openid查询用户信息
                    $access_token = $token["access_token"];
                    $openid = $oauth2['openid'];
                    $get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
                    $userinfo = $this->getJson($get_user_info_url);
                    if(array_key_exists("errcode", $userinfo)){
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]=$userinfo['errmsg'];
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=603;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        log_message("error",$userinfo['errmsg']);
                        echo json_encode($assdata);
                        return true;

                    }
                    if(count($userinfo)>0){
                        $info['clien_nickname']=array_key_exists('nickname',$userinfo)?$userinfo['nickname']:'';
                        $info['clien_sex']=array_key_exists('sex',$userinfo)?$userinfo['sex']:'';
                        $info['clien_openid']=$openid;
                        $info['clien_city']=array_key_exists('city',$userinfo)?$userinfo['city']:'';
                        $info['clien_photo']=array_key_exists('headimgurl',$userinfo)?$userinfo['headimgurl']:'';

                    }
                    else{
                        $assdata['data']='';
                        $assdata[ "errorCode"]="user-error";
                        $assdata[ "ErrorMessage"]="获取用户信息失败";
                        $assdata[ "Success"]=false;
                        $assdata[ "Status_Code"]=303;
                        header("HTTP/1.1 201 Created");
                        header("Content-type: application/json");
                        echo json_encode($userinfo);
                        return true;
                    }

                }
                $clien_info = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                if(count($clien_info)>0){
                    if($clien_info[0]['clien_userid']!=""){
                        //原有业务经理，无需更新业务经理
                        $info=$this->bykey_reitem($info,'clien_userid');
                    }
                    $info=$this->bykey_reitem($info,'code');
                    $isok=$this->SysModel->table_updateRow('clien_info',$info,array('clien_openid' => $info['clien_openid']));
                    if($isok>=0){
                        $clineinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                        $userinfo=$this->SysModel->table_seleRow("user_name,user_phone", 'userinfo', array('user_id' => $clineinfo[0]['clien_userid']));
                        if(count($userinfo)>0 && count($clineinfo)>0){
                            $clineinfo[0]['manager_name']=$userinfo[0]['user_name'];
                            $clineinfo[0]['manager_phone']=$userinfo[0]['user_phone'];
                        }

                        $assdata['data']=$clineinfo[0];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="客户信息更新成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=200;
                    }
                    else{
                        $assdata['data']='';
                        $assdata["errorCode"]="addClien-error";
                        $assdata["ErrorMessage"]="客户信息更新失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=300;
                    }
                }
                else{
                    $info=$this->bykey_reitem($info,'code');
                    $isok=$this->SysModel->table_addRow('clien_info',$info);
                    if($isok>0){
                        $clineinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                        $userinfo=$this->SysModel->table_seleRow("user_name,user_phone", 'userinfo', array('user_id' => $clineinfo[0]['clien_userid']));
                        if(count($clien_info)>0 && count($userinfo)>0){
                            $clineinfo[0]['manager_name']=$userinfo[0]['user_name'];
                            $clineinfo[0]['manager_phone']=$userinfo[0]['user_phone'];
                        }

                        $assdata['data']=$clineinfo[0];
                        $assdata["errorCode"]="";
                        $assdata["ErrorMessage"]="客户新增成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=200;
                    }
                    else{
                        $assdata['data']='';
                        $assdata["errorCode"]="addClien-error";
                        $assdata["ErrorMessage"]="客户新增失败";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=301;
                    }

                }

            }
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);
    }



    public function business_delegate(){

        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $order=array();
        $assdata=array();
        if (array_key_exists("clien_local", $info) && array_key_exists("clien_area", $info) && array_key_exists("clien_openid", $info) && array_key_exists("clien_phone", $info) && array_key_exists("clien_name", $info) ) {

            $clien_info['clien_local']=$info['clien_local'];
            $clien_info['clien_area']=$info['clien_area'];
            $clien_info['clien_openid']=$info['clien_openid'];
            $clien_info['clien_phone']=$info['clien_phone'];
            $clien_info['clien_name']=$info['clien_name'];


            $isok=$this->SysModel->table_updateRow('clien_info',$clien_info,array('clien_openid' => $info['clien_openid']));

            if($isok>=0){
                $clien_info = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                $order['order_clienname']=$clien_info[0]['clien_name'];
                $order['order_clienopenid']=$clien_info[0]['clien_openid'];
                $order['order_clien_phone']=$clien_info[0]['clien_phone'];
                $order['order_manager']=$clien_info[0]['clien_userid'];
                $order['order_statue']="1";//初始订单
                $order['order_date']=date('Y-m-d H:i');//初始订单
                $order['order_EvaluateAllPriceAT']=$info['EvaluateAllPriceAT'];
                $order['order_EvaluateUnitPrice']=$info['EvaluateUnitPrice'];
                $order['local_address']=$info['clien_local'];
                $order['area']=$info['clien_area'];
                $order['HasLift']=$info['HasLift'];
                $order['LegalUsage']=$info['LegalUsage'];
                $order['PropertyRegFullTwoYear']=$info['PropertyRegFullTwoYear'];
                $isok=$this->SysModel->table_addRow('order_info',$order);

                if($isok>=1){
                    $assdata['data']=$info;
                    $assdata[ "errorCode"]="";
                    $assdata[ "ErrorMessage"]="订单新增成功";
                    $assdata[ "Success"]=true;
                    $assdata[ "Status_Code"]=200;

                }
                else{
                    $assdata['data']='';
                    $assdata[ "errorCode"]="add-error";
                    $assdata[ "ErrorMessage"]="订单新增失败";
                    $assdata[ "Success"]=false;
                    $assdata[ "Status_Code"]=301;

                }



            }
            else{
                $assdata['data']='';
                $assdata[ "errorCode"]="addClien-error";
                $assdata[ "ErrorMessage"]="订单新增失败";
                $assdata[ "Success"]=false;
                $assdata[ "Status_Code"]=301;
            }




        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=400;

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);



    }
    //获取方案明细
    public function getScheme_Details(){
        $assdata=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("order_id", $info) && array_key_exists("clien_phone", $info)) {


            $order_info = $this->SysModel->table_seleRow("*", 'order_info', array('order_id' => $info['order_id'],'order_clien_phone'=> $info['clien_phone']));
            if(count($order_info)>0){
                if(!(is_null($order_info[0]['order_statue']))){

                    $statue_info=$this->SysModel->table_seleRow('*','statue_info',array('statue_id'=>$order_info[0]['order_statue']));
                    if(count($statue_info)>0){

                        $order_info[0]['order_statue_name']=$statue_info[0]['statue_name'];

                    }
                    else{
                        $order_info[0]['order_statue_name']="无状态";
                    }

                }
                else{
                    $order_info[0]['order_statue_name']="无状态";
                }



                if(!(is_null($order_info[0]['order_manager']))){

                    $user_info=$this->SysModel->table_seleRow('user_name,user_phone,user_statue','userinfo',array('user_id'=>$order_info[0]['order_manager']));
                    if(count($user_info)>0){
                        $order_info[0]['order_manager']=$user_info[0]['user_name'];
                        $order_info[0]['order_manager_phone']=$user_info[0]['user_phone'];
                        $order_info[0]['order_manager_statue']=$user_info[0]['user_statue'];
                    }
                    else{
                        $order_info[0]['order_manager']="";
                        $order_info[0]['order_manager_phone']="";
                        $order_info[0]['order_manager_statue']="";
                    }

                }




                $assdata['data']=$order_info[0];
                $assdata["errorCode"]="";
                $assdata["ErrorMessage"]="查询成功";
                $assdata["Success"]=true;
                $assdata["Status_Code"]=200;


            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="";
                $assdata["ErrorMessage"]="无数据";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=303;

            }
        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata["Status_Code"]=400;

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);





    }
    //办理进度查询，获取所有方案信息，每次取30条，按order_id
    public function getProgress(){

        $assdata=array();
        $data=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("order_id", $info) && array_key_exists("clien_phone", $info)) {

            $where="order_id<{$info['order_id']} and order_clien_phone='{$info['clien_phone']}'";
            $order_info=$this->SysModel->table_seleRow_limit('local_address,order_manager,order_id,order_statue,order_clien_phone','order_info',$where,array(),30,'order_date','DESC');
            $statue_info=$this->SysModel->table_seleRow('*','statue_info');
            if(count($order_info)>0){
                foreach ($order_info as $row)
                {

                    if(!(is_null($row['order_manager']))){

                        $user_info=$this->SysModel->table_seleRow('user_name,user_phone','userinfo',array('user_id'=>$row['order_manager']));
                        if(count($user_info)>0){
                            $row['order_manager']=$user_info[0]['user_name'];
                            $row['order_manager_phone']=$user_info[0]['user_phone'];
                        }
                        else{
                            $row['order_manager']="";
                            $row['order_manager_phone']="";
                        }

                    }

                    if(!(is_null($row['order_statue']))){
                        foreach ($statue_info as $statue_row){
                            if($row['order_statue']==$statue_row['statue_id']){

                                $row['order_statue_name']=$statue_row['statue_name'];
                            }
                        }
                    }

                    if(!(array_key_exists('order_statue_name',$row))){

                        $row['order_statue_name']="无状态";


                    }


                    array_push($data, $row);
                }
            }
            $assdata['data']=$data;
            $assdata["errorCode"]="";
            $assdata["ErrorMessage"]="";
            $assdata["Success"]=true;
            $assdata["Status_Code"]=200;
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;

        }


        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);




    }
    //业务经理获取订单列表（首页）
    public function get_order_list(){
        $assdata=array();
        $where_data=array();
        $tmp_orderinfo=array();
        $query_data="";
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("order_id", $info) && array_key_exists("order_manager", $info) && array_key_exists("table_type", $info) && array_key_exists("query_data", $info)) {

            if($info['table_type']==1){
                $order_statue="='放款结算'";
            }
            else{
                $order_statue="!='放款结算'";
            }

            if($info['query_data']!=''){
               $query_data="concat(local_address,order_clienname,order_clien_phone) like '%{$info['query_data']}%' and ";

            }

            $where_data=$query_data."order_id<'{$info['order_id']}' and order_manager='{$info['order_manager']}' and order_statue{$order_statue}";

            $order_info=$this->SysModel->table_seleRow_limit('local_address,order_clienname,order_clien_phone,order_clienopenid,order_id,order_statue','order_info',$where_data,array(),30,'order_id','DESC');
            $statue_info=$this->SysModel->table_seleRow('*','statue_info');


            if(count($order_info)>0 && count($statue_info)>0){
                foreach($order_info as $row){
                    if(!(is_null($row['order_statue']))){
                        foreach ($statue_info as $statue_row){
                            if($row['order_statue']==$statue_row['statue_id']){

                                $row['order_statue_name']=$statue_row['statue_name'];
                            }
                        }
                    }
                    if(!(array_key_exists('order_statue_name',$row))){

                        $row['order_statue_name']="无状态";


                    }
                    array_push($tmp_orderinfo,$row);
                }
            }


            if(count($tmp_orderinfo)>0){
                $assdata['data']=$tmp_orderinfo;
                $assdata[ "errorCode"]="";
                $assdata[ "ErrorMessage"]="";
                $assdata[ "Success"]=true;
                $assdata["Status_Code"]=200;
            }
            else{
                $assdata['data']='';
                $assdata[ "errorCode"]="none-data";
                $assdata[ "ErrorMessage"]="查无数据";
                $assdata[ "Success"]=false;
                $assdata["Status_Code"]=303;
            }

        }
        else{
            $assdata['data']='';
            $assdata[ "errorCode"]="parameter-error";
            $assdata[ "ErrorMessage"]="参数接收错误";
            $assdata[ "Success"]=false;
            $assdata["Status_Code"]=400;

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);

    }
    //业务经理新增客户
    public function manager_add_custom(){
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        if (array_key_exists("order_manager", $info) && array_key_exists("clien_local", $info) && array_key_exists("clien_area", $info) && array_key_exists("EvaluateAllPriceAT", $info) && array_key_exists("clien_phone", $info) && array_key_exists("clien_name", $info) ) {
            $clien_info['clien_local']=$info['clien_local'];
            $clien_info['clien_area']=$info['clien_area'];
            $clien_info['clien_phone']=$info['clien_phone'];
            $clien_info['clien_name']=$info['clien_name'];
            $clien_info['clien_userid']=$info['order_manager'];
            //业务经理新增客户，判断客户是否在客户表，如果在就仅新增订单，否则更新新增客户，
            $clien_querydata = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_phone' => $info['clien_phone']));

            //客户已存在
            if(count($clien_querydata)>0){
                //客户原关联经理是否为空，或者原关联经理等于当前添加经理。
                if(($clien_querydata[0]['clien_userid']=='')||($clien_querydata[0]['clien_userid']==$info['order_manager'])){
                    //更新客户信息
                    $isok=$this->SysModel->table_updateRow('clien_info',$clien_info,array('clien_phone' => $info['clien_phone']));
                }
                else{
                    $assdata['data']='';
                    $assdata["errorCode"]="manmager-error";
                    $assdata["ErrorMessage"]="客户已关联其他业务经理！，无法重复添加";
                    $assdata["Success"]=false;
                    $assdata["Status_Code"]=301;
                    header("HTTP/1.1 201 Created");
                    header("Content-type: application/json");
                    echo json_encode($assdata);
                    return false;

                }

            }
            else{

                $isok=$this->SysModel->table_addRow('clien_info',$clien_info);
            }
            $clien_newinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_phone' => $info['clien_phone']));
            if(count($clien_info)>0){

                //添加订单信息
                $order['order_clienname']=$clien_newinfo[0]['clien_name'];
                $order['order_clienopenid']=$clien_newinfo[0]['clien_openid'];
                $order['order_clien_phone']=$clien_newinfo[0]['clien_phone'];
                $order['order_manager']=$clien_newinfo[0]['clien_userid'];
                $order['order_statue']=$info['order_statue'];//订单状态,默认初始状态
                $order['order_date']=date('Y-m-d H:i');//添加时间
                //$order['order_EvaluateAllPriceAT']=$info['EvaluateAllPriceAT'].'万元';
                $order['local_address']=$info['clien_local'];
                $order['area']=$info['clien_area'];
                //$order['HasLift']=$info['HasLift'];
                //$order['loan_amount']=$info['loan_amount'];
                $order['order_planid']=$info['order_planid'];
                //$order['loan_rate']=$info['loan_rate'];
                //$order['loan_year']=$info['loan_year'];
                //$order['order_Payment']=$info['order_Payment'];
                //$order['order_Monther_remind']=$info['order_Monther_remind'];
                //$order['order_Repayment']=$info['order_Repayment'];
                //$order['LegalUsage']=$info['LegalUsage'];
                $order['order_rate']=array_key_exists('order_rate',$info)?$info['order_rate']:'';
               // $order['PropertyRegFullTwoYear']=$info['PropertyRegFullTwoYear'];
                $isok=$this->SysModel->table_addRow('order_info',$order);
                if($isok>0){
                    $assdata['data']='';
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="客户信息添加成功";
                    $assdata["Success"]=true;
                    $assdata["Status_Code"]=200;

                }
                else{
                    $assdata['data']='';
                    $assdata["errorCode"]="custom-add-error";
                    $assdata["ErrorMessage"]="客户信息添加失败";
                    $assdata["Success"]=false;
                    $assdata["Status_Code"]=301;


                }
            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="custom-add-error";
                $assdata["ErrorMessage"]="客户信息添加失败";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=301;

            }

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);




    }
    //业务经理获取所有客户
    public function manager_get_custom(){
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        $assdata=array();
        $query_data="";
        if (array_key_exists("order_manager", $info) && array_key_exists("clien_id", $info) && array_key_exists("query_data", $info)) {
            if($info['query_data']!=''){
                $query_data="concat(clien_name,clien_phone) like '%{$info['query_data']}%' and ";
            }

            $wheredata=$query_data."clien_id<{$info['clien_id']} and clien_userid={$info['order_manager']}";

            $clien_data=$this->SysModel->table_seleRow_limit('clien_name,clien_nickname,clien_phone,clien_sex,clien_id,clien_openid','clien_info',$wheredata,array(),30,'clien_id','DESC');
            if(count($clien_data)>0){
                $assdata['data']=$clien_data;
                $assdata["errorCode"]="";
                $assdata["ErrorMessage"]="";
                $assdata["Success"]=true;
                $assdata["Status_Code"]=200;

            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="none-data";
                $assdata["ErrorMessage"]="查无数据";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=301;

            }


        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;

        }

        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);




    }
    //获取订单状态
    public function get_order_statue(){
        $statue_list=$this->SysModel->table_seleRow('statue_id,statue_name','statue_info');

        if(count($statue_list)>0){
            $assdata['data']=$statue_list;
            $assdata["errorCode"]="";
            $assdata["ErrorMessage"]="";
            $assdata["Success"]=true;
            $assdata["Status_Code"]=200;
        }
        else{
            $assdata['data']="";
            $assdata["errorCode"]="query-none-data";
            $assdata["ErrorMessage"]="查无数据";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=303;
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);
    }
    //获取方案
    public function get_scheme_list(){
        $scheme_list=$this->SysModel->table_seleRow('scheme_id,scheme_name','scheme_info');

        if(count($scheme_list)>0){
            $assdata['data']=$scheme_list;
            $assdata["errorCode"]="";
            $assdata["ErrorMessage"]="";
            $assdata["Success"]=true;
            $assdata["Status_Code"]=200;
        }
        else{
            $assdata['data']="";
            $assdata["errorCode"]="query-none-data";
            $assdata["ErrorMessage"]="查无数据";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=303;
        }
        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);
    }
    //业务经理登陆
    public function manager_login(){
        log_message('error','sdfsdf');
        $assdata=array();
        $where_data=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("user_phone", $info) && array_key_exists("user_pwd", $info)) {

            $user_data=$this->SysModel->table_seleRow('user_phone,user_id,user_name,user_pwd,user_statue,user_group,user_QR','userinfo',array('user_phone'=>$info['user_phone']));
            if(count($user_data)>0){
                $pwd=$this->encrypt->decode($user_data[0]['user_pwd']);
                if($info['user_pwd']==$pwd){
                    if($user_data[0]['user_statue']==1){
                        $user_data[0]= $this->bykey_reitem($user_data[0],'user_pwd');
                        $assdata['data']=$user_data;
                        $assdata["errorCode"]="parameter-error";
                        $assdata["ErrorMessage"]="登录成功";
                        $assdata["Success"]=true;
                        $assdata["Status_Code"]=200;
                        $this->session->set_userdata($user_data[0]);

                    }
                    else{
                        $assdata['data']="";
                        $assdata["errorCode"]="user-statue-error";
                        $assdata["ErrorMessage"]="用户状态异常，请联系管理员！！";
                        $assdata["Success"]=false;
                        $assdata["Status_Code"]=306;
                    }
                }
                else{
                    $assdata['data']="";
                    $assdata["errorCode"]="user-statue-error";
                    $assdata["ErrorMessage"]="用户名或密码错误！";
                    $assdata["Success"]=false;
                    $assdata["Status_Code"]=306;
                }
            }
            else{

                $assdata['data']="";
                $assdata["errorCode"]="user-statue-error";
                $assdata["ErrorMessage"]="用户名或密码错误！";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=306;
            }
        }




        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);



    }
    //个人中心更新用户信息
    public function update_clien(){
        $assdata=array();
        $where_data=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("clien_name", $info) && array_key_exists("clien_phone", $info) && array_key_exists("clien_openid", $info)) {
            $clien_info = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
            if(count($clien_info)>0){
                $isok=$this->SysModel->table_updateRow('clien_info',$info,array('clien_openid' => $info['clien_openid']));
                if($isok>=0){
                    $clineinfo = $this->SysModel->table_seleRow("*", 'clien_info', array('clien_openid' => $info['clien_openid']));
                    $userinfo=$this->SysModel->table_seleRow("user_name,user_phone", 'userinfo', array('user_id' => $clineinfo[0]['clien_userid']));
                    if(count($clineinfo)>0 && count($userinfo)>0){
                        $clineinfo[0]['manager_name']=$userinfo[0]['user_name'];
                        $clineinfo[0]['manager_phone']=$userinfo[0]['user_phone'];
                    }

                    $assdata['data']=$clineinfo[0];
                    $assdata["errorCode"]="";
                    $assdata["ErrorMessage"]="客户信息更新成功";
                    $assdata["Success"]=true;
                    $assdata["Status_Code"]=200;
                }
                else{
                    $assdata['data']='';
                    $assdata["errorCode"]="addClien-error";
                    $assdata["ErrorMessage"]="客户信息更新失败";
                    $assdata["Success"]=false;
                    $assdata["Status_Code"]=301;
                }
            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="clienquery-error";
                $assdata["ErrorMessage"]="客户信息查询失败";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=303;
            }
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;
        }


        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);



    }
    //订单时间
    public function order_history_time(){
        $assdata=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);
        if (array_key_exists("order_id", $info)) {
            $order_his_info = $this->SysModel->table_seleRow_limit("*", 'order_history', array('order_id' => $info['order_id']),array(),20,'history_Data','DESC');
            if(count($order_his_info)>0) {
                $assdata['data'] = $order_his_info;
                $assdata["errorCode"] = "";
                $assdata["ErrorMessage"] = "";
                $assdata["Success"] = true;
                $assdata["Status_Code"] = 200;
            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="query-error";
                $assdata["ErrorMessage"]="无数据";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=303;
            }
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;
        }


        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);



    }
    //跟单前端更新
    public function update_order(){
        $assdata=array();
        $where_data=array();
        $his_ord=array();
        $agentinfo = file_get_contents('php://input');
        $info = json_decode($agentinfo, true);

        if (array_key_exists("order_id", $info)) {

            $clieninfo= $this->bykey_reitem($info,'order_id');
            $clieninfo=array_filter($clieninfo);
            $isok=$this->SysModel->table_updateRow('order_info',$clieninfo,array('order_id' => $info['order_id']));
            if($isok>=0){
                $orderinfo = $this->SysModel->table_seleRow("*", 'order_info', array('order_id' => $info['order_id']));
                $statue=$this->SysModel->table_seleRow("statue_name",'statue_info',array('statue_id'=>$info['order_id']));
                if(count($orderinfo)>0){
                    $his_ord['order_id']=$orderinfo[0]['order_id'];
                    $his_ord['history_manager']=$orderinfo[0]['order_manager'];
                    $his_ord['history_Data']=date('Y-m-d H:i');
                    $his_ord['history_statue']=$statue[0]['statue_name'];
                    $db_hisresult=$this->SysModel->table_addRow('order_history',$his_ord);
                }

                $assdata['data']='';
                $assdata["errorCode"]="";
                $assdata["ErrorMessage"]="跟单信息更新成功";
                $assdata["Success"]=true;
                $assdata["Status_Code"]=200;
            }
            else{
                $assdata['data']='';
                $assdata["errorCode"]="addClien-error";
                $assdata["ErrorMessage"]="跟单信息更新失败";
                $assdata["Success"]=false;
                $assdata["Status_Code"]=301;
            }
        }
        else{
            $assdata['data']='';
            $assdata["errorCode"]="parameter-error";
            $assdata["ErrorMessage"]="参数接收错误";
            $assdata["Success"]=false;
            $assdata["Status_Code"]=400;
        }


        header("HTTP/1.1 201 Created");
        header("Content-type: application/json");
        echo json_encode($assdata);


    }
    //自有模糊匹配



    //返回密码
    public function build_pws(){

        $agentinfo = file_get_contents('php://input');
        if($agentinfo!=""){
            $info = json_decode($agentinfo, true);
            echo $this->encryption->encrypt($info['uid']);
        }




    }













}