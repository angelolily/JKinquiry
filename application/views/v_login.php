<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>登入 - 建科询价管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="./public/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="./public/layuiadmin/style/admin.css" media="all">
    <link rel="stylesheet" href="./public/layuiadmin/style/login.css" media="all">
    <link rel="stylesheet" href="./public/verify-master/css/verify.css" media="all">

</head>
<body>

<div class="layadmin-user-login layadmin-user-display-show" id="LAY-user-login" style="display: none;">

    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <img src='./public/logo.fw.png' height="150" width="150"></img>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body layui-form">
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                <input type="text" name="user_phone" id="LAY-user-login-username" lay-verify="required|phone" placeholder="手机号登陆" class="layui-input">
            </div>
            <div class="layui-form-item">
                <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                <input type="password" name="user_pwd" id="LAY-user-login-password" lay-verify="required" placeholder="密码" class="layui-input">
            </div>
            <div class="layui-form-item">
                <div class="layui-row" id="verndo">
                </div>
            </div>
            <div class="layui-form-item">
                <button class="layui-btn layui-btn-warm layui-btn-fluid" lay-submit lay-filter="LAY-user-login-submit" style="background-color:#8E088E ">登 入</button>
            </div>
        </div>
    </div>

    <div class="layui-trans layadmin-user-login-footer">
        <p>© 2020 所有权归属:福建航天云平台信息服务公司<a href="" target="_blank"></a></p>
    </div>

</div>

<script src="./public/layui/layui.all.js"></script>
<script src="./public/verify-master/js/jquery.min.js"></script>
<script src="./public/verify-master/js/verify.min.js"></script>


<script>
    var yz=0;

    $(function(){

        alert(window.location.search);



    });

    $('#verndo').slideVerify({

        type : 2,
        mode : 'pop',
        vOffset : 5,
        vSpace : 5,
        explain : '向右滑动完成验证',
        imgUrl : './public/verify-master/images/',
        imgName : ['1.jpg', '2.jpg','3.jpg','4.jpg','5.jpg'],
        imgSize : {
            width: '330px',
            height: '200px',
        },
        blockSize : {
            width: '40px',
            height: '40px',
        },
        barSize : {
            width: '330px',
            height : '40px',
        },
        ready : function() {
        },
        success : function() {
            yz=1;
        },
        error : function() {
            layer.msg('验证码不匹配！');
        }

    });
    var form = layui.form;
    form.render();
    form.on('submit(LAY-user-login-submit)', function(obj){
        var field = obj.field;
        if(yz==1){
            $.ajax({
                url:'./index.php/Login/userlogin',
                data:{login_val:field},
                dataType:'json',
                async : false,
                success:function (res) {
                    if(res.code==0){
                        layer.msg(res.msg, {
                             icon: 1
                            ,time: 1000
                        }, function(){
                            location.href = './index.php/Main'; //跳转到主页
                        });
                    }
                    else{
                        layer.msg(res.msg,{icon: 5});
                    }
                },
                error:function (xhr, textStatus, errorThrown) {
                    alert("进入error---");

                }
            });
        }
        else{
            layer.msg("验证不通过",{icon: 5});
        }

    });


    function repPwd() {
        layer.msg('如果需要修改，请联系管理员', {
            offset: '15px'
            ,icon: 1
            ,time: 3000
        }, function(){
            location.href = '/JKinquiry/index.php/Login'; //跳转到登入页
        });

    }





</script>
</body>
</html>
