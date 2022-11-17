<?php
/**
 * Created by PhpStorm.
 * User: lchangelo
 * Date: 2018/8/7
 * Time: 10:30
 * 微信工具类：一些微信的操作放在这里
 */
defined('BASEPATH') OR exit('No direct script access allowed');
require_once 'vendor/autoload.php';
use EasyWeChat\Factory;


class Wechat_Tool_Model extends CI_Model
{
    private $appid = "wx71b2317f2015c429";
    private $secret = "c94eb5b0801ee5d5046a6ff84069a2ac";
    private $app;
    function __construct()
    {
        parent::__construct();
        $config = [
            'app_id' => $this->appid,
            'secret' => $this->secret,
            'token' => 'jkoffice',
            // 指定 API 调用返回结果的类型：array(default)/collection/object/raw/自定义类名
            'response_type' => 'array'

            //...
        ];
        $this->load->database('default');
        $this->app = Factory::officialAccount($config);
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
    //get获取JSON
    public function getJson($url){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output, true);
    }

    /**
     *等比例缩放函数（以保存新图片的方式实现）
     * @param string $picName 被缩放的处理图片源
     * @param string $savePath 保存路径
     * @param int $maxx 缩放后图片的最大宽度
     * @param int $maxy 缩放后图片的最大高度
     * @param string $pre 缩放后图片的前缀名
     * @return $string 返回后的图片名称（） 如a.jpg->s.jpg
     *
     **/
    protected function scaleImg($picName,$savePath, $maxx = 85, $maxy = 85)
    {
        $info = getimageSize($picName);//获取图片的基本信息
        $w = $info[0];//获取宽度
        $h = $info[1];//获取高度

        if($w<=$maxx&&$h<=$maxy){
            return $picName;
        }
        //获取图片的类型并为此创建对应图片资源
        switch ($info[2]) {
            case 1://gif
                $im = imagecreatefromgif($picName);
                break;
            case 2://jpg
                $im = imagecreatefromjpeg($picName);
                break;
            case 3://png
                $im = imagecreatefrompng($picName);
                break;
            default:
                die("图像类型错误");
        }
        //计算缩放比例
        if (($maxx / $w) > ($maxy / $h)) {
            $b = $maxy / $h;
        } else {
            $b = $maxx / $w;
        }
        //计算出缩放后的尺寸
        $nw = floor($w * $b);
        $nh = floor($h * $b);
        //创建一个新的图像源（目标图像）
        $nim = imagecreatetruecolor($nw, $nh);

        //透明背景变黑处理
        //2.上色
        $color=imagecolorallocate($nim,255,255,255);
        //3.设置透明
        imagecolortransparent($nim,$color);
        imagefill($nim,0,0,$color);


        //执行等比缩放
        imagecopyresampled($nim, $im, 0, 0, 0, 0, $nw, $nh, $w, $h);
        //输出图像（根据源图像的类型，输出为对应的类型）
        $picInfo = pathinfo($picName);//解析源图像的名字和路径信息
        //$savePath = $savePath. "/" .date("Ymd")."/".$this->pre . $picInfo["basename"];
        switch ($info[2]) {
            case 1:
                imagegif($nim, $savePath);
                break;
            case 2:
                imagejpeg($nim, $savePath);
                break;
            case 3:
                imagepng($nim, $savePath);
                break;

        }
        //释放图片资源
        imagedestroy($im);
        imagedestroy($nim);
        //返回结果
        return $savePath;
    }

    //获取微信token
    public function getToken(){


        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appid&secret=$this->secret";
        $token = getJson($url);

        if(array_key_exists("errcode", $token)){
            $assdata['data']='';
            $assdata[ "errorCode"]="user-error";
            $assdata[ "ErrorMessage"]=$token['errmsg'];
            $assdata[ "Success"]=false;
            $assdata[ "Status_Code"]=601;
            header("HTTP/1.1 201 Created");
            header("Content-type: application/json");
            log_message("error",$token['errmsg']);

            return false;

        }
        else{
            return $token["access_token"];
        }

    }

    //获取二维码图片
    public function getwechartQR($ticket,$qrimg_path){


        $url = $this->app->qrcode->url($ticket);
        $content = file_get_contents($url); // 得到二进制图片内容

        file_put_contents($qrimg_path, $content); // 写入文件

        if(file_exists($qrimg_path)){


            $this->scaleImg($qrimg_path,$qrimg_path);

        }


    }

    public function createTempMenu($menu)
    {
        $ab=$this->app->menu->create($menu);

        return $ab;


    }

    public function getWMenulist()
    {
        $list =  $this->app->menu->list();
        return $list;
    }


    //查询报告状态 向服务器发送消息
    public function send_Report_Statue(){

        try{
            $this->app->server->push(function ($message) {

                if($message['MsgType']=='event'){
                    $pid=$message['EventKey'];
                    $keyArray = explode("_", $pid);
                    if (count($keyArray) != 1){
                        $pid=$keyArray[1];
                    }

                    if($pid){

                        $projinfo=$this->table_seleRow('c_amount,c_rpoid,c_projname,c_projstate','jko_projinfotb',array('pid'=>$pid));

                        if(count($projinfo)>0){
                            if(!(explode($projinfo[0]['c_projname'],'[2019]'))){
                                $projinfo[0]['c_rpoid']= '闽建科估字[2020]第'.$projinfo[0]['c_rpoid'];
                            }
                            return "您查询的坐落于".$projinfo[0]['c_projname'].'房产估价报告，报告编号：'.$projinfo[0]['c_rpoid'].',评估额'.$projinfo[0]['c_amount'].'万元,当前状态：'.$projinfo[0]['c_projstate'];
                        }
                        else{
                            return "您查询的报告系伪造";
                        }

                    }
                    else{
                        return "您查询的报告系伪造";
                    }


                }

            });
            $response = $this->app->server->serve();
            $response->send();
        }
        catch (Exception $ex){

        }




    }

    //设置微信菜单
    public function setMenu($buttons){
        $this->app->menu->delete();
        $result=$this->app->menu->create($buttons);
        if($result['errcode']==0)
        {
            return "更新成功";

        }
        else{
            return $result['errmsg'];
        }



    }

    //获取素材id
    public function getMediaList($type, $offset, $count)
    {
        $json_list=$this->app->material->list($type, $offset, $count);

        return $json_list;
    }


}