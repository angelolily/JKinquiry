<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>订单管理</title>
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
                        <input type="text" name="order_clienname" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">客户电话</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_clien_phone" placeholder="请输入" autocomplete="off" style="width: 300px" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">贷款额度</label>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="loan_amount_min" placeholder="￥" autocomplete="off" class="layui-input">
                    </div>
                    <div class="layui-form-mid">-</div>
                    <div class="layui-input-inline" style="width: 100px;">
                        <input type="text" name="loan_amount_max" placeholder="￥" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">方案名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="order_planid" placeholder="请输入" autocomplete="off" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <label class="layui-form-label">订单状态</label>
                    <div class="layui-input-block">
                        <select id="order_statue" name="order_statue" style="width: 300px" lay-filter='order_statue' >
                            <option value="">请选择...</option>
                            <?php
                            if ($statuelist) {
                                foreach ($statuelist as $srow) {
                                    ?>
                                    <option
                                            value="<?php echo $srow['statue_id']; ?>"><?php echo $srow['statue_name']; ?></option>
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
                    <label class="layui-form-label">客户经理</label>
                    <div class="layui-input-block">
                        <select id="order_manager" name="order_manager" style="width: 300px" lay-filter='order_manager' >
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
                    <label class="layui-form-label">房屋地址</label>
                    <div class="layui-input-block">
                        <input type="text" name="local_address" placeholder="请输入" autocomplete="off" style="width: 450px" class="layui-input">
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
                <button class="layui-btn layuiadmin-btn-admin" data-type="add">添加订单</button>
            </div>
            <table id="JC_TABLE" lay-filter="JC_TABLE"></table>

            <script type="text/html" id="Y-Tpl">
                {{#  if(d.order_EvaluateAllPriceAT != null){ }}
                <i class="layui-icon" style="font-size: 13px;color: #1E9FFF">{{d.order_EvaluateAllPriceAT}}万元</i>
                {{#  } else { }}
                <i class="layui-icon" style="font-size: 13px;">--</i>
                {{#  } }}
            </script>

            <script type="text/html" id="table-useradmin-webuser">
                <a class="layui-btn layui-btn-normal layui-btn-xs" lay-event="edit"><i class="layui-icon layui-icon-edit"></i>编辑</a>
                <a class="layui-btn layui-btn-warm layui-btn-xs" lay-event="history"><i class="layui-icon layui-icon-release"></i>历史</a>
                <a class="layui-btn layui-btn-danger layui-btn-xs" lay-event="del"><i class="layui-icon layui-icon-delete"></i>删除</a>
            </script>

        </div>
    </div>
</div>




</body>


<from class="layui-form" lay-filter="from_clien" id="from_clien" style="display:none;padding: 20px;">
    <div class="layui-form-item">
        <label class="layui-form-label">订单状态</label>
        <div class="layui-input-block">
            <select id="order_statue" name="order_statue" lay-verify="required" lay-filter='order_statue' >
                <option value="">请选择...</option>
                <?php
                if ($statuelist) {
                    foreach ($statuelist as $srow) {
                        ?>
                        <option
                                value="<?php echo $srow['statue_id']; ?>"><?php echo $srow['statue_name']; ?></option>
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
    <div class="layui-form-item">
        <label class="layui-form-label">客户名称</label>
        <div class="layui-input-inline">
            <input type="text" name="order_clienname" lay-verify="required" placeholder="请输入客户名称" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客户号码</label>
        <div class="layui-input-inline">
            <input type="text" name="order_clien_phone" lay-verify="required|phone" placeholder="请输入手机号码" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">房屋地址</label>
        <div class="layui-input-inline">
            <input type="text" name="local_address" style="width: 600px" lay-verify="required"  placeholder="请输入详细地址" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">房屋面积</label>
        <div class="layui-input-inline">
            <input type="text" name="area"  placeholder="请输入面积" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">平方米</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">税前总价</label>
        <div class="layui-input-inline">
            <input type="text" name="order_EvaluateAllPriceAT"  placeholder="" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">万元</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">客户经理</label>
        <div class="layui-input-block">
            <select id="order_manager" name="order_manager" lay-verify="required" lay-filter='order_manager' >
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
    <div class="layui-form-item">
        <label class="layui-form-label">贷款方案</label>
        <div class="layui-input-block">
            <?php
            if ($schemelist) {
                foreach ($schemelist as $row) {
                    ?>
                    <input type="checkbox" name="order_planid" lay-verify="required" lay-filter="order_planid" title="<?php echo $row['scheme_name']; ?>" value="<?php echo $row['scheme_id']; ?>">
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
    <div class="layui-form-item">
        <label class="layui-form-label">每月还款金额</label>
        <div class="layui-input-inline">
            <input type="text" name="order_Repayment"  placeholder="" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">元</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">贷款金额</label>
        <div class="layui-input-inline">
            <input type="text" name="loan_amount"  placeholder="" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">万元</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">贷款利率</label>
        <div class="layui-input-inline">
            <input type="text" name="loan_rate"  placeholder="" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">%年利率</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">贷款年限</label>
        <div class="layui-input-inline">
            <input type="text" name="loan_year"  placeholder="" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
        <div class="layui-form-mid layui-word-aux">年</div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">还款方式</label>
        <div class="layui-input-inline">
            <input type="text" name="order_Payment"  placeholder="" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">每月还款提醒日</label>
        <div class="layui-input-inline">
            <input type="text" name="order_Monther_remind"  placeholder="" lay-verify="required" autocomplete="off" class="layui-input">
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
        <label class="layui-form-label">备注</label>
        <div class="layui-input-block">
            <textarea name="order_rate" placeholder="请输入内容" class="layui-textarea"></textarea>
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
        elem: '#JC_TABLE',　　　//html中table窗口的id
        height: 'full-220',
        url:'./Order/getOrderData',
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
            ,{field: 'order_date', title: '订单建立日期', width:180,sort: true}
            ,{field: 'order_statue_name', title: '订单状态', width:150}
            ,{field: 'order_clienname', title: '用户名称', width:150}
            ,{field: 'order_clien_phone', title: '用户电话', width:150}
            ,{field: 'order_EvaluateUnitPrice', title: '评估税前单价', hide: true}
            ,{field: 'order_EvaluateAllPriceAT', title: '评估税前总价', width:150,sort: true,templet:'#Y-Tpl'}
            ,{field: 'order_userid_name', title: '业务经理', width:150,sort: true}
            ,{field: 'order_planid', title: '方案名称', width:150}
            ,{field: 'order_Repayment', title: '每月还款金额', width:150}
            ,{field: 'loan_amount', title: '贷款金额', width:150,sort: true}
            ,{field: 'loan_rate', title: '贷款利率', width:150}
            ,{field: 'loan_year', title: '贷款年限', width:150,sort: true}
            ,{field: 'local_address', title: '房屋地址',width:250}
            ,{field: 'order_Payment', title: '还款方式', width:150}
            ,{field: 'order_Monther_remind', title: '每月提醒日期', width:100}
            ,{field: 'area', title: '订单房屋面积',width:100}
            ,{field: 'HasLift', title: '有否电梯',width:100}
            ,{field: 'PropertyRegFullTwoYear', title: '产权时间',width:150}
            ,{field: 'LegalUsage', title: '法定用途',width:150}
            ,{field: 'order_rate', title: '订单备注',width:250}
            ,{field: 'order_manager', title: '业务经理' , hide: true}
            ,{field: 'order_statue', title: '订单状态' , hide: true}
            ,{field: 'order_id',title: 'ID', hide: true}
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
            $("#from_clien").find('input[type=text],select,input[type=hidden]').each(function() {
                $(this).val('');
            });
            layer.open({
                type: 1
                ,title: '添加客户'
                ,content: $("#from_clien")
                ,maxmin: true
                ,area: ['1000px', '750px']
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
                            url:'./Order/addOrder',
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
        var datas = obj.data;
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
                            url:'./Order/delOrder',
                            data:{order_id:selectrow.order_id},
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
                            url:'./Order/updateOrder',
                            data:{user_val:field,order_id:selectrow.order_id},
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
        else if(obj.event === 'history'){
            var selectrow =obj.data;
            var us="http://temptest.fjspacecloud.com/JIACHENG/index.php/Timeline/orderTime/"+selectrow.order_id;
            layer.open({
                type: 2
                ,title: ''
                ,content: us
                ,maxmin: true
                ,area: ['600px', '550px']
                ,btn: ['确定', '取消']
                ,yes: function(index, layero){
                    layer.close(index);//关闭弹层
                }
                ,btn2:function (index,layero) {
                    layer.close(index);//关闭弹层

                }
                ,success: function(layero, index){

                }
            });


        }
    });




</script>


</body>
</html>