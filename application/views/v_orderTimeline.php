<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/7/11
 * Time: 16:00
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>时间线</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="<?php echo base_url('public/layuiadmin/layui/css/layui.css')?>" media="all">
    <link rel="stylesheet" href="<?php echo base_url('public/layuiadmin/style/admin.css')?>" media="all">
</head>
<body>
<div class="layui-card">
    <div class="layui-card-header">订单进度更新历史</div>
    <div class="layui-card-body">
        <ul class="layui-timeline">
            <?php
            if ($orderlist) {
                foreach ($orderlist as $row) {
                    ?>
                    <li class="layui-timeline-item">
                        <i class="layui-icon layui-timeline-axis">&#xe60e;</i>
                        <div class="layui-timeline-content layui-text">
                            <div class="layui-timeline-title">
                                <?php echo $row['history_Data']; ?>，<?php echo $row['history_statue']; ?>
                            </div>
                        </div>
                    </li>
                    <?php
                }
            }
                ?>
        </ul>
    </div>
</div>
<script src="<?php echo base_url('public/layuiadmin/layui/layui.js')?>"></script>
</body>
</html>