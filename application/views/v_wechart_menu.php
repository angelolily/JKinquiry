<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>公众号菜单管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="../public/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="../public/layuiadmin/style/admin.css" media="all">
    <style>
        .layui-form-label {
            width: 100px;
        }
        .layui-input-block {
            margin-left: 130px;
        }
        .layui-form-item .layui-input-inline {
            width: 350px;
        }
    </style>
</head>

<body>

<div class="layui-fluid">
    <!--搜索条件 -->
    <div class="layui-card" >

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">状态名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="statue_name" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button class="layui-btn layuiadmin-btn-admin" lay-submit lay-filter="LAY-group-search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <div style="padding-bottom: 10px;">
                <button class="layui-btn layuiadmin-btn-admin" data-type="add">添加一级菜单</button>
                <button class="layui-btn layuiadmin-btn-admin" data-type="updateWechat">更新至微信</button>
            </div>
            <table id="JC_USER" lay-filter="JC_USER"></table>
            <script type="text/html" id="table-useradmin-webuser">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>

            <script type="text/html" id="table-tool">
                <a class="layui-btn layui-btn layui-btn-xs" lay-event="qr"><i class="layui-icon layui-icon-login-wechat" style="color: #FFFFFF;"></i>二维码</a>
            </script>

        </div>
    </div>
</div>




</body>


<from class="layui-form" lay-filter="from_user" id="from_user" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label">菜单名称</label>
        <div class="layui-input-inline">
            <input type="text" name="menu_name" lay-verify="required"  placeholder="请输入菜单名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">链接地址</label>
        <div class="layui-input-inline">
            <input type="text" name="menu_url"  placeholder="类型为网页时填写" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">小程序首页</label>
        <div class="layui-input-inline">
            <input type="text" name="menu_pagepath"  placeholder="小程序首页地址写入这里" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">小程序必填</label>
        <div class="layui-input-inline">
            <input type="text" name="menu_appid"  placeholder="小程序首页地址写入这里" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">图片素材</label>
        <div class="layui-input-inline">
            <input type="text" name="menu_media_id"  placeholder="填写图文或者图片素材ID" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">菜单类型</label>
        <div class="layui-input-block">
            <select id="menu_type" name="menu_type" lay-verify="required" lay-filter='menu_type' >
                <option value="">请选择...</option>
                <option value="view">网页类型</option>
                <option value="click">小程序类型</option>
                <option value="view_limited">图文消息</option>
                <option value="media_id">图片</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="LAY-user-front-submit" id="LAY-user-front-submit" value="确认">
    </div>

</from>

<script src="../public/layui/layui.all.js"></script>
<script>
    var $ = layui.$;
    var table = layui.table;

    var g_table =table.render({
        elem: '#JC_USER',　　　//html中table窗口的id
        height: 'full-220',
        url:'./Wmenu/getMenuData',
        loading: true,
        toolbar:true,
        text: {
            none: '空空如也'
        },
        title: '状态信息',
        page: {
            layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
            limit: 20

        },
        cols: [[
            {id:'radio_1',type: 'radio',fixed: 'left'}
            ,{field: 'menu_id', title: '菜单id', width:150}
            ,{field: 'menu_name', title: '菜单名称', width:150}
            ,{field: 'menu_type', title: '菜单类型', width:150}
            ,{field: 'menu_url', title: '菜单链接地址', width:150}
            ,{field: 'menu_appid', title: '菜单appid', width:150}
            ,{field: 'menu_pagepath', title: '小程序首页', width:150}
            ,{field: 'menu_media_id', title: '图文信息id', width:150}
            ,{title: '操作', align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser', width:250}
        ]]


    });//表格
    var form = layui.form;
    //监听搜索
    form.on('submit(LAY-group-search)', function(data){
        var field = data.field;
        //执行重载
        g_table.reload({
            where: { //设定异步数据接口的额外参数，任意设
                val: field
            }
            ,page: {
                curr: 1 //重新从第 1 页开始
            }
        });
    });

    //事件
    var active = {
        add: function(){
            $("#from_user").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });
            layer.open({
                type: 1
                ,title: '添微信菜单'
                ,content: $("#from_user")
                ,maxmin: true
                ,area: ['550px', '600px']
                ,btn: ['确定', '取消']
                ,anim: 3
                ,end:function () {
                    $("#from_user").find('input[type=text],select,input[type=hidden]').each(function() {
                        $(this).val('');
                    });
                }
                ,yes: function(index, layero){
                    var submit = $("#LAY-user-front-submit");
                    //监听提交
                    form.on('submit(LAY-user-front-submit)', function(data){
                        var field = data.field; //获取提交的字段
                        console.log(field);
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Wmenu/addMenu',
                            data:{menu_val:field},
                            dataType:'json',
                            async : false,
                            success:function (result) {
                                if(result.code){
                                    layer.msg(result.msg, {icon: 6});
                                    g_table.reload();//数据刷新
                                    layer.close(index);//关闭弹层
                                }
                                else{
                                    layer.msg(result.msg, {icon: 5});
                                }
                            }

                        });
                    });

                    submit.trigger('click');
                }
                ,btn2:function (index, layero){
                    $("#from_group").find('input[type=text],select,input[type=hidden]').each(function() {
                        $(this).val('');
                    });
                }
            });
        }
       ,updateWechat:function () {
            //提交 Ajax 成功后，静态更新表格中的数据
            $.ajax({
                type:'post',
                url:'./Wmenu/add_push_menu',
                data:{},
                dataType:'text',
                async : false,
                success:function (result) {
                    layer.msg(result);
                }

            });
        }
    };

    $('.layui-btn.layuiadmin-btn-admin').on('click', function(){
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    //监听工具条
    table.on('tool(JC_USER)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.prompt({
                formType: 1
                ,title: '敏感操作，请验证口令'
            }, function(value, index){
                if(obj.data.menu_id!=1){
                    if(value=='<?php echo $op_pwd; ?>'){
                        layer.close(index);
                        layer.confirm('确定删除对吗', function(index){
                            //提交 Ajax 成功后，删除更新表格中的数据
                            var selectrow =obj.data;
                            $.ajax({
                                type:'post',
                                url:'./Wmenu/delStatue',
                                data:{statue_id:selectrow.menu_id},
                                dataType:'json',
                                async : false,
                                success:function (result) {
                                    if(result.code){
                                        layer.msg(result.msg, {icon: 6});
                                        g_table.reload();//数据刷新
                                        layer.close(index);//关闭弹层
                                    }
                                    else{
                                        layer.msg(result.msg, {icon: 5});
                                        layer.close(index);//关闭弹层
                                    }
                                }

                            });
                        });
                    }
                    else{
                        layer.msg("口令错误", {icon: 5});
                    }
                }
                else{
                    layer.msg("第一条订单状态，不能删除，请联系管理员", {icon: 5});
                }

            });
        }
        else if(obj.event === 'edit'){
            var checkData=obj.data;
            if(checkData.length === 0){
                return layer.msg('请选择数据');
            }
            form.val('from_user',checkData);
            layer.open({
                type: 1
                ,title: '编辑用户'
                ,content: $("#from_user")
                ,maxmin: true
                ,area: ['600px', '450px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var submit = $("#LAY-user-front-submit");
                    //监听提交
                    form.on('submit(LAY-user-front-submit)', function(data){
                        var field = data.field; //获取提交的字段
                        var selectrow =obj.data;
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Statue/updateStatue',
                            data:{statue_val:field,statue_id:selectrow.statue_id},
                            dataType:'json',
                            async : false,
                            success:function (result) {
                                if(result.code){
                                    layer.msg(result.msg, {icon: 6});
                                    g_table.reload();//数据刷新
                                    layer.close(index);//关闭弹层
                                }
                                else{
                                    layer.msg(result.msg, {icon: 5});
                                }
                            }

                        });
                    });

                    submit.trigger('click');
                }
                ,success: function(layero, index){

                }
            });
        }
    });




</script>


</body>
</html>