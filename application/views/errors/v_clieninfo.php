<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>客户信息管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="../public/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="../public/layuiadmin/style/admin.css" media="all">
</head>

<body>

<div class="layui-fluid">
    <div class="layui-card" >
        <!--搜索条件 -->
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">用户姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="user_name" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">所属业务组</label>
                    <div class="layui-input-block">
                        <select id="user_group" name="user_group" lay-filter='user_group' >
                            <option value="">请选择...</option>
                            <?php
                            if ($grouplist) {
                                foreach ($grouplist as $row) {
                                    ?>
                                    <option
                                            value="<?php echo $row['group_id']; ?>"><?php echo $row['group_name']; ?></option>
                                    <?php
                                }
                            } else {
                                ?>
                                <option value="no">请先新增业务组数据</option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">用户角色</label>
                    <div class="layui-input-block">
                        <select name="user_type" lay-filter="user_type">
                            <option value="">请选择...</option>
                            <option value="1">业务经理</option>
                            <option value="2">业务组长</option>
                            <option value="3">系统管理员</option>
                        </select>
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
                <button class="layui-btn layuiadmin-btn-admin" data-type="add">添加用户</button>
                <button class="layui-btn layuiadmin-btn-admin" data-type="resetpwd">重置密码</button>
            </div>
            <table id="JC_CLIEN" lay-filter="JC_CLIEN"></table>
            <script type="text/html" id="usertype-Tpl">
                {{#  if(d.user_type == 1){ }}
                <i class="layui-icon layui-icon-username" style="font-size: 13px;">业务经理</i>
                {{#  } else if(d.user_type == 2) { }}
                <i class="layui-icon layui-icon-user" style="font-size: 13px; ">业务组长</i>
                {{#  } else { }}
                <i class="layui-icon layui-icon-diamond" style="font-size: 13px;">系统管理员</i>
                {{#  } }}
            </script>
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
        <label class="layui-form-label">用户名称</label>
        <div class="layui-input-inline">
            <input type="text" name="user_name" lay-verify="required" placeholder="请输入用户名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">手机号码</label>
        <div class="layui-input-inline">
            <input type="text" name="user_phone" lay-verify="required|phone|phone_repeat" placeholder="登陆时使用,默认密码相同" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">用户邮箱</label>
        <div class="layui-input-inline">
            <input type="text" name="user_mail" lay-verify="email" placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">用户角色</label>
        <div class="layui-input-block">
            <select name="user_type" lay-filter="user_type" lay-verify="required">
                <option value="">请选择...</option>
                <option value="1">业务经理</option>
                <option value="2">业务组长</option>
                <option value="3">系统管理员</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">所属业务组</label>
        <div class="layui-input-block">
            <select id="user_group" name="user_group" lay-verify="required" lay-filter='user_group' >
                <option value="">请选择...</option>
                <?php
                if ($grouplist) {
                    foreach ($grouplist as $row) {
                        ?>
                        <option
                                value="<?php echo $row['group_id']; ?>"><?php echo $row['group_name']; ?></option>
                        <?php
                    }
                } else {
                    ?>
                    <option value="no">请先新增业务组数据</option>
                    <?php
                }
                ?>
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
        elem: '#JC_CLIEN',　　　//html中table窗口的id
        height: 'full-220',
        url:'./ClienInfo/getClienData',
        loading: true,
        toolbar:true,
        text: {
            none: '空空如也'
        },
        title: '客户信息',
        page: {
            layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
            limit: 20

        },
        cols: [[
            {id:'radio_1',type: 'radio',fixed: 'left'}
            ,{field: 'clien_name', align:'center', title: '客户姓名'}
            ,{field: 'clien_nickname',align:'center', title: '微信昵称'}
            ,{field: 'clien_sex',align:'center', title: '性别'}
            ,{field: 'clien_phone',align:'center', title: '客户手机号'}
            ,{field: 'clien_phone2',align:'center', title: '备用手机号'}
            ,{field: 'clien_username',align:'center', title: '客户经理姓名'}
            ,{field: 'clien_userphone',align:'center', title: '客户经理电话'}
            ,{field: 'clien_rate',align:'center', title: '备注'}
            ,{field: 'user_type', align:'center',title: '用户角色',templet: '#usertype-Tpl'}
            ,{field: 'user_QR', title: '', hide: true}
            ,{field: 'user_id',title: 'ID', hide: true}
            ,{field: 'user_group', title: '所属业务组id', hide: true}
            ,{title: '推广二维码', align:'center', fixed: 'right', toolbar: '#table-tool'}
            ,{title: '操作', align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser'}
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
    //自定义验证
    form.verify({
        //value：表单的值、item：表单的DOM对象
        phone_repeat: function(value, item){
            $.ajax({
                type:'post',
                url:'./UserInfo/isphonerepeat',
                data:{user_phone:value},
                dataType:'text',
                async : false,
                success:function (result) {
                    return result;
                }

            });


        }
        });
    //事件
    var active = {
        resetpwd: function(){
            var checkData = table.checkStatus('JC_USER').data; //得到选中的数据


            if(checkData.length === 0){
                return layer.msg('请选择数据');
            }
            layer.prompt({
                formType: 1
                ,title: '敏感操作，请验证口令'
            }, function(value, index){
                if(value=='<?php echo $op_pwd; ?>'){
                    layer.close(index);
                    layer.confirm('确定重置密码吗？重置后的密码就是登陆手机号', function(index){
                        //提交 Ajax 成功后，删除更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./UserInfo/restPwd',
                            data:{user_phone:checkData[0].user_phone},
                            dataType:'json',
                            async : false,
                            success:function (result) {
                                if(result.code){
                                    layer.msg(result.msg, {icon: 6});
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
            });

        }
        ,add: function(){
            $("#from_user").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });
            layer.open({
                type: 1
                ,title: '添加用户'
                ,content: $("#from_user")
                ,maxmin: true
                ,area: ['600px', '450px']
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
                            url:'./UserInfo/addUser',
                            data:{user_val:field},
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
                if(value=='<?php echo $op_pwd; ?>'){
                    layer.close(index);
                    layer.confirm('确定删除对吗', function(index){
                        //提交 Ajax 成功后，删除更新表格中的数据
                        var selectrow =obj.data;
                        $.ajax({
                            type:'post',
                            url:'./UserInfo/delUser',
                            data:{user_id:selectrow.user_id},
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
                            url:'./UserInfo/updateUser',
                            data:{user_val:field,user_id:selectrow.user_id},
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

    //监听单击事件
    // table.on('row(JC_USER)',function (obj) {
    //     var data = obj.data;
    //     selected = data;
    //     //选中行样式
    //     obj.tr.addClass('layui-table-click').siblings().removeClass('layui-table-click');
    //     //选中radio样式
    //     obj.tr.find('i[class="layui-anim layui-icon"]').trigger("click");
    //
    // });


</script>


</body>
</html>