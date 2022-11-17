<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>客户资料管理</title>
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
                    <label class="layui-form-label">客户姓名</label>
                    <div class="layui-input-block">
                        <input type="text" name="clien_name" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">客户电话</label>
                    <div class="layui-input-block">
                        <input type="text" name="clien_phone" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">房屋地址</label>
                    <div class="layui-input-block">
                        <input type="text" name="clien_local" placeholder="请输入" autocomplete="off" style="width: 450px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">客户经理</label>
                    <div class="layui-input-block">
                        <select id="clien_userid" name="clien_userid" style="width: 300px" lay-filter='clien_userid' >
                            <option value="">请选择...</option>
                            <?php
                            if ($userlist) {
                                foreach ($userlist as $row) {
                                    ?>
                                    <option
                                            value="<?php echo $row['user_id']; ?>"><?php echo $row['user_name']; ?></option>
                                    <?php
                                }
                            } else {
                                ?>
                                <option value="no">无数据可选择...</option>
                                <?php
                            }
                            ?>
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
                <button class="layui-btn layuiadmin-btn-admin" data-type="add">添加客户</button>
            </div>
            <table id="JC_TABLE" lay-filter="JC_TABLE"></table>

            <script type="text/html" id="sex-Tpl">
                {{#  if(d.clien_sex == '1'){ }}
                <i class="layui-icon layui-icon-male" style="font-size: 13px;color: #1E9FFF">男</i>
                {{#  } else if(d.clien_sex == '2') { }}
                <i class="layui-icon layui-icon-female" style="font-size: 13px;color: #CD2626">女</i>
                {{#  } else { }}
                <i class="layui-icon layui-icon-female" style="font-size: 13px;">未知</i>
                {{#  } }}
            </script>

            <script type="text/html" id="table-useradmin-webuser">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
            </script>

        </div>
    </div>
</div>




</body>


<from class="layui-form" lay-filter="from_clien" id="from_clien" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label">客户名称</label>
        <div class="layui-input-inline">
            <input type="text" name="clien_name" lay-verify="required" placeholder="请输入客户名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客户号码</label>
        <div class="layui-input-inline">
            <input type="text" name="clien_phone" lay-verify="required|phone|phone_repeat" placeholder="请输入手机号码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">备用号码</label>
        <div class="layui-input-inline">
            <input type="text" name="clien_phone2"  placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">性别</label>
        <div class="layui-input-block">
            <input type="radio" name="clien_sex" value="男" title="男">
            <input type="radio" name="clien_sex" value="女" title="女" checked>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">房屋地址</label>
        <div class="layui-input-inline">
            <input type="text" name="clien_local" style="width: 600px"  placeholder="请输入详细地址" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">房屋面积</label>
        <div class="layui-input-inline">
            <input type="text" name="clien_area"  placeholder="请输入面积" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">平方米</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">所在城市</label>
        <div class="layui-input-block">
            <select name="clien_city" lay-filter="clien_city">
                <option value="福州">福州</option>
                <option value="厦门">厦门</option>
                <option value="莆田">莆田</option>
                <option value="三明">三明</option>
                <option value="宁德">宁德</option>
                <option value="漳州">漳州</option>
                <option value="泉州">泉州</option>
                <option value="龙岩">龙岩</option>
                <option value="南平">南平</option>
            </select>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客户经理</label>
        <div class="layui-input-block">
            <select id="clien_userid" name="clien_userid" lay-verify="required" lay-filter='clien_userid' >
                <option value="">请选择...</option>
                <?php
                if ($userlist) {
                    foreach ($userlist as $row) {
                        ?>
                        <option
                                value="<?php echo $row['user_id']; ?>"><?php echo $row['user_name']; ?></option>
                        <?php
                    }
                } else {
                    ?>
                    <option value="no">无数据可选择...</option>
                    <?php
                }
                ?>
            </select>
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea name="clien_rate" placeholder="请输入内容" class="layui-textarea"></textarea>
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
    var goble_isenable=0;
    var g_table =table.render({
        elem: '#JC_TABLE',　　　//html中table窗口的id
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
            ,{field: 'clien_photo', align:'center',title: '客户微信头像', templet: '<div><img src="{{ d.clien_photo }}" style="width:40px; height:40px;"></div>'}
            ,{field: 'clien_name', title: '客户姓名'}
            ,{field: 'clien_phone', title: '客户手机号'}
            ,{field: 'clien_phone2', title: '备用手机号'}
            ,{field: 'clien_nickname', title: '微信昵称'}
            ,{field: 'clien_sex', title: '客户性别',templet:'#sex-Tpl'}
            ,{field: 'clien_local', title: '房屋地址',width: '20%'}
            ,{field: 'clien_area', title: '面积'}
            ,{field: 'clien_userid_name', title: '客户经理'}
            ,{field: 'clien_city', title: '城市'}
            ,{field: 'clien_id',title: 'ID', hide: true}
            ,{field: 'clien_userid', title: '对应客户经理',hide: true}
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
            var msg="";
            $.ajax({
                type:'post',
                url:'./ClienInfo/isphonerepeat',
                data:{clien_phone:value,enable:goble_isenable},
                dataType:'text',
                async : false,
                success:function (result) {
                    msg=result;
                }
            });

            return msg;//返回值必须在这里返回 只能返回 字符串,不能返回true or false


        }
        });
    //事件
    var active = {
        add: function(){
            $("#from_clien").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });
            goble_isenable=0;
            layer.open({
                type: 1
                ,title: '添加客户'
                ,content: $("#from_clien")
                ,maxmin: true
                ,area: ['800px', '750px']
                ,btn: ['确定', '取消']
                ,anim: 3
                ,end:function () {
                    $("#from_clien").find('input[type=text],select,input[type=hidden]').each(function() {
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
                            url:'./ClienInfo/addUser',
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
    table.on('tool(JC_TABLE)', function(obj){
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
            goble_isenable=1;
            if(checkData.length === 0){
                return layer.msg('请选择数据');
            }
            form.val('from_clien',checkData);
            layer.open({
                type: 1
                ,title: '编辑用户'
                ,content: $("#from_clien")
                ,maxmin: true
                ,area: ['800px', '750px']
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
                            url:'./ClienInfo/updateUser',
                            data:{user_val:field,clien_id:selectrow.clien_id},
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