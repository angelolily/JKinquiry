<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/6/30
 * Time: 8:43
 */
require_once './public/phpqrcode/phpqrcode.php';

//生成二维码图片
function buildQr($strtext,$path){
    QRcode::png($strtext, $path,'Q', 10, 0);

}

