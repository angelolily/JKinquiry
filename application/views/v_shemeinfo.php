<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>方案管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="../public/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="../public/layuiadmin/style/admin.css" media="all">


    <style>
        .layui-form-label{
            white-space:nowrap!important;
        }

    </style>

</head>

<body>

<div class="layui-fluid">
    <div class="layui-card" >
        <!--搜索条件 -->
        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">方案名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="scheme_name" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">方案状态</label>
                    <div class="layui-input-block">
                        <select name="scheme_statue" lay-filter="scheme_statue">
                            <option value="">请选择...</option>
                            <option value="1">已启用</option>
                            <option value="0">未启用</option>
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
                <button class="layui-btn layuiadmin-btn-admin" data-type="add">添加方案</button>
            </div>
            <table id="JC_SCHEME" lay-filter="JC_SCHEME"></table>
            <script type="text/html" id="Scheme_statue-Tpl">
                {{#  if(d.scheme_statue == 1){ }}
                <button class="layui-btn layui-btn-xs" lay-event="enablestatue">已启用</button>
                {{#  } else { }}
                <button class="layui-btn layui-btn-primary layui-btn-xs" lay-event="enablestatue">未启用</button>
                {{#  } }}
            </script>
            <script type="text/html" id="table-useradmin-webuser">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
        </div>
    </div>
</div>




</body>


<from class="layui-form" lay-filter="from_scheme" id="from_scheme" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label">方案名称</label>
        <div class="layui-input-inline">
            <input type="text" name="scheme_name" lay-verify="required" placeholder="请输入方案名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">贷款金额</label>
        <div class="layui-input-inline">
            <input type="number" name="loan_amount" value="" min="0.0" step="0.1" lay-verify="required" placeholder="请填写乘法系数" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">贷款利率</label>
        <div class="layui-input-inline">
            <input type="number" name="loan_rate" min="0.0" step="0.1" lay-verify="required" placeholder="保留一位小数" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">%</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">贷款年限</label>
        <div class="layui-input-inline">
            <input type="number" name="loan_year" value="" min="1" step="1" lay-verify="required" placeholder="" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">还款方式</label>
        <div class="layui-input-inline">
            <input type="text" name="repayment_way" lay-verify="required" placeholder="" autocomplete="off" class="layui-input" style="width:350px">
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
        elem: '#JC_SCHEME',　　　//html中table窗口的id
        height: 'full-220',
        url:'./Scheme/getSchemeData',
        loading: true,
        toolbar:true,
        text: {
            none: '空空如也'
        },
        title: '用户信息',
        page: {
            layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
            limit: 20

        },
        cols: [[
            {type: 'radio',fixed: 'left'}
            ,{field: 'scheme_name', title: '方案名称'}
            ,{field: 'loan_amount', title: '贷款金额'}
            ,{field: 'loan_rate', title: '贷款利率'}
            ,{field: 'loan_year', title: '贷款年限'}
            ,{field: 'repayment_way', title: '还款方式'}
            ,{field: 'scheme_person', title: '方案创建人'}
            ,{field: 'scheme_date', title: '方案创建时间'}
            ,{field: 'scheme_id', title: '方案id',hide: true}
            ,{field: 'scheme_statue', align:'center',title: '方案状态',templet: '#Scheme_statue-Tpl'}
            ,{title: '操作',  align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser'}
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
        enablestatue: function(){
            var checkData = table.checkStatus('JC_USER').data; //得到选中的数据
            if(checkData.length === 0){
                return layer.msg('请选择数据');
            }

        }
        ,add: function(){
            $("#from_scheme").find('input[type=text],input[type=number],select,input[type=hidden]').each(function() {
                $(this).val('');
            });
            layer.open({
                type: 1
                ,title: '添加方案'
                ,content: $("#from_scheme")
                ,maxmin: true
                ,area: ['600px', '550px']
                ,btn: ['确定', '取消']
                ,anim: 3
                ,end:function () {
                    $("#from_scheme").find('input[type=text],select,input[type=hidden]').each(function() {
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
                            url:'./Scheme/addScheme',
                            data:{scheme_val:field},
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
                            },
                            error:function (result) {

                                layer.msg(result.msg, {icon: 5});
                            }

                        });
                    });

                    submit.trigger('click');
                }
                ,btn2:function (index, layero){
                    $("#from_scheme").find('input[type=text],select,input[type=hidden]').each(function() {
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
    table.on('tool(JC_SCHEME)', function(obj){
        var data = obj.data;
        if(obj.event === 'del'){
            layer.prompt({
                formType: 1
                ,title: '敏感操作，请验证口令'
            }, function(value, index){
                if(value=='<?php echo $op_pwd; ?>'){
                    layer.close(index);
                    layer.confirm('确定删除对吗,删除后业务组绑定方案自动解除', function(index){
                        //提交 Ajax 成功后，删除更新表格中的数据
                        var selectrow =obj.data;
                        $.ajax({
                            type:'post',
                            url:'./Scheme/delScheme',
                            data:{scheme_id:selectrow.scheme_id},
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
            form.val('from_scheme',checkData);
            layer.open({
                type: 1
                ,title: '编辑方案'
                ,content: $("#from_scheme")
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
                            url:'./Scheme/updateScheme',
                            data:{scheme_val:field,scheme_id:selectrow.scheme_id},
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
        else if(obj.event === 'enablestatue'){
            var checkData=obj.data;
            if(checkData.length === 0){
                return layer.msg('请选择数据');
            }
            var upScheme={};
            console.log(checkData.scheme_statue);
            if(checkData.scheme_statue==1){
                $msg="确定关闭方案吗？";
                upScheme.scheme_statue=0;
            }
            else{
                $msg="确定启用方案吗？";
                upScheme.scheme_statue=1;
            }
            layer.confirm($msg, function(index){
                //提交 Ajax 成功后，删除更新表格中的数据
                $.ajax({
                    type:'post',
                    url:'./Scheme/updateScheme',
                    data:{scheme_val:upScheme,scheme_id:checkData.scheme_id},
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
                            g_table.reload();//数据刷新
                            layer.close(index);//关闭弹层
                        }
                    }

                });
            });

        }
    });



</script>


</body>
</html>