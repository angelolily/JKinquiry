<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/11
 * Time: 19:56
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>设置我的密码</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="../public/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="../public/layuiadmin/style/admin.css" media="all">
</head>
<body>

<div class="layui-fluid">
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md12">
            <div class="layui-card">
                <div class="layui-card-header">修改密码</div>
                <div class="layui-card-body">
                    <from class="layui-form" lay-filter="from_clien" id="from_clien">
                        <div class="layui-form-item">
                            <label class="layui-form-label">当前密码</label>
                            <div class="layui-input-inline">
                                <input type="password" name="oldPassword" lay-verify="required" lay-verType="tips" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">新密码</label>
                            <div class="layui-input-inline">
                                <input type="password" name="password1" lay-verify="pass" lay-verType="tips" autocomplete="off" id="LAY_password" class="layui-input">
                            </div>
                            <div class="layui-form-mid layui-word-aux">6到16个字符</div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">确认新密码</label>
                            <div class="layui-input-inline">
                                <input type="password" name="repassword" lay-verify="repass" lay-verType="tips" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <button class="layui-btn" lay-submit lay-filter="setmypass">确认修改</button>
                            </div>
                        </div>
                    </from>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="../public/layui/layui.all.js"></script>
<script>
    var form = layui.form;

    var $ = layui.$;
    //自定义验证
    form.verify({
        nickname: function(value, item){ //value：表单的值、item：表单的DOM对象
            if(!new RegExp("^[a-zA-Z0-9_\u4e00-\u9fa5\\s·]+$").test(value)){
                return '用户名不能有特殊字符';
            }
            if(/(^\_)|(\__)|(\_+$)/.test(value)){
                return '用户名首尾不能出现下划线\'_\'';
            }
            if(/^\d+\d+\d$/.test(value)){
                return '用户名不能全为数字';
            }
        }

        //我们既支持上述函数式的方式，也支持下述数组的形式
        //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
        ,pass: [
            /^[\S]{6,12}$/
            ,'密码必须6到12位，且不能出现空格'
        ]

        //确认密码
        ,repass: function(value){
            if(value !== $('#LAY_password').val()){
                return '两次密码输入不一致';
            }
        }
    });


    //监听提交
    form.on('submit(setmypass)', function(data){
        var field = data.field; //获取提交的字段
        //提交 Ajax 成功后，静态更新表格中的数据
        $.ajax({
            type:'post',
            url:'./Password/updatePwd',
            data:{user_val:field},
            dataType:'json',
            async : false,
            success:function (result) {
                if(result.code){
                    layer.msg(result.msg, {
                        icon: 5,
                        time: 5000 //5秒关闭（如果不配置，默认是3秒）
                    }, function(){
                        window.location.reload();
                    });

                }
                else{
                    layer.msg(result.msg, {icon: 5});
                }
            }

        });
    });
</script>
</body>
</html>
