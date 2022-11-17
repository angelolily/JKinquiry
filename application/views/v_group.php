<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>业务组管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="../public/layuiadmin/layui/css/layui.css" media="all">
    <link rel="stylesheet" href="../public/layuiadmin/style/admin.css" media="all">
</head>

<body>

<div class="layui-fluid">
    <div class="layui-card" >

        <div class="layui-form layui-card-header layuiadmin-card-header-auto">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <label class="layui-form-label">业务组名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="group_name" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
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
                <button class="layui-btn layuiadmin-btn-admin" data-type="add">添加机构</button>
            </div>
            <table id="JC_GROUP" lay-filter="JC_GROUP"></table>
            <script type="text/html" id="table-useradmin-webuser">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>
        </div>
    </div>
</div>




</body>


<from class="layui-form" lay-filter="from_group" id="from_group" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label">业务组名称</label>
        <div class="layui-input-inline">
            <input type="text" name="group_name" lay-verify="required" placeholder="请输入业务组名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">负责人姓名</label>
        <div class="layui-input-inline">
            <input type="text" name="group_head" lay-verify="required" placeholder="请输入业务组名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">负责人手机号码</label>
        <div class="layui-input-inline">
            <input type="text" name="group_tel" lay-verify="required|phone" placeholder="请输入号码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">机构地区</label>
        <div class="layui-input-block">
            <select name="group_city" lay-filter="group_city">
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
        <label class="layui-form-label">贷款方案</label>
        <div class="layui-input-block">
            <?php
            if ($schemelist) {
                foreach ($schemelist as $row) {
                    ?>
                    <input type="checkbox" name="group_scheme" lay-filter="group_scheme" title="<?php echo $row['scheme_name']; ?>" value="<?php echo $row['scheme_id']; ?>">
                    <?php
                }
            } else {
                ?>
                <i class="layui-icon layui-icon-face-cry" style="font-size: 16px; color: #1E9FFF;">请先新增方案数据</i>
                <?php
            }
            ?>
        </div>
    </div>

    <div class="layui-form-item layui-hide">
        <input type="button" lay-submit lay-filter="LAY-user-front-submit" id="LAY-user-front-submit" value="确认">
        <input type="text" name="group_id" class="layui-input">
    </div>

</from>

<script src="../public/layui/layui.all.js"></script>
<script>
    var $ = layui.$;
    var table = layui.table;

    var g_table =table.render({
        elem: '#JC_GROUP',　　　//html中table窗口的id
        height: 'full-220',
        url:'./Group/getGroupData',
        loading: true,
        text: {
            none: '空空如也'
        },
        title: '业务组信息',
        page: {
            layout: ['count', 'prev', 'page', 'next', 'limit', 'refresh', 'skip'],
            limit: 20

        },
        cols: [[
            ,{field: 'group_id', width: 100, title: 'ID', hide: true}
            ,{type: 'radio',fixed: 'left',width: 30,}
            ,{field: 'group_name', title: '业务组名称'}
            ,{field: 'group_city', title: '业务组地区'}
            ,{field: 'group_head', title: '业务组负责人'}
            ,{field: 'group_tel', title: '机构联系人电话'}
            ,{field: 'group_regnum', title: '机构注册人数'}
            ,{field: 'group_schemText', title: '机构选择方案', minWidth: 150}
            ,{field: 'group_scheme', title: '', hide: true}
            ,{title: '操作', width: 150, align:'center', fixed: 'right', toolbar: '#table-useradmin-webuser'}
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
        batchdel: function(){
            var checkStatus = g_table.checkStatus('LAY-user-manage')
                ,checkData = checkStatus.data; //得到选中的数据

            if(checkData.length === 0){
                return layer.msg('请选择数据');
            }




            layer.prompt({
                formType: 1
                ,title: '敏感操作，请验证口令'
            }, function(value, index){
                layer.close(index);

                layer.confirm('确定删除吗？', function(index) {

                    //执行 Ajax 后重载
                    /*
                    admin.req({
                      url: 'xxx'
                      //,……
                    });
                    */
                    table.reload('LAY-user-manage');
                    layer.msg('已删除');
                });
            });
        }
        ,add: function(){
            $("#from_group").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });
            layer.open({
                type: 1
                ,title: '添加机构'
                ,content: $("#from_group")
                ,maxmin: true
                ,area: ['600px', '450px']
                ,btn: ['确定', '取消']
                ,anim: 3
                ,end:function () {
                    $("#from_group").find('input[type=text],select,input[type=hidden]').each(function() {
                        $(this).val('');
                    });
                }
                ,yes: function(index, layero){
                    var submit = $("#LAY-user-front-submit");
                    //监听提交
                    form.on('submit(LAY-user-front-submit)', function(data){
                        var field = data.field; //获取提交的字段
                        var arr_box = [];
                        $('input[type=checkbox]:checked').each(function() {
                            arr_box.push($(this).val());
                        });
                        field.group_scheme=arr_box.toString();
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $.ajax({
                            type:'post',
                            url:'./Group/addGroup',
                            data:{group_val:field},
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
    table.on('tool(JC_GROUP)', function(obj){
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
                            url:'./Group/delGroup',
                            data:{group_id:selectrow.group_id},
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
            form.val('from_group',checkData);
            layer.open({
                type: 1
                ,title: '编辑用户'
                ,content: $("#from_group")
                ,maxmin: true
                ,area: ['500px', '450px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    var submit = $("#LAY-user-front-submit");
                    //监听提交
                    form.on('submit(LAY-user-front-submit)', function(data){
                        var field = data.field; //获取提交的字段
                        var selectrow =obj.data;
                        var arr_box = [];
                        //提交 Ajax 成功后，静态更新表格中的数据
                        $('input[type=checkbox]:checked').each(function() {
                            arr_box.push($(this).val());
                        });
                        field.group_scheme=arr_box.toString();
                        $.ajax({
                            type:'post',
                            url:'./Group/updateGroup',
                            data:{group_val:field,group_id:selectrow.group_id},
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