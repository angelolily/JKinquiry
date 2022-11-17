<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-04-06 16:23:44 --> Query error: Table 'jkinquiry.jko_projinfotb' doesn't exist - Invalid query: INSERT INTO `jko_projinfotb` (`c_projname`, `c_projtype`, `c_projstate`, `c_jzarea`, `c_lookhouse`, `c_housetel`, `c_rem`, `c_projid`) VALUES ('士大夫大師傅但是手動閥', '住宅', '', '100', '咋説', '13325289965', '询价：税前单价：30000税后总价：3.2', '234234234')
ERROR - 2021-04-06 16:24:45 --> Query error: Table 'jkinquiry.jko_projinfotb' doesn't exist - Invalid query: INSERT INTO `jko_projinfotb` (`c_projname`, `c_projtype`, `c_projstate`, `c_jzarea`, `c_lookhouse`, `c_housetel`, `c_rem`, `c_projid`) VALUES ('士大夫大師傅但是手動閥', '住宅', '', '100', '咋説', '13325289965', '询价：税前单价：30000税后总价：3.2', '234234234')
ERROR - 2021-04-06 16:27:34 --> Severity: error --> Exception: Call to undefined method CI_Loader::table_addRow() F:\phpStudy\PHPTutorial\WWW\JKInquiry\application\controllers\interface_Applets.php 1510
ERROR - 2021-04-06 16:28:36 --> Severity: error --> Exception: Call to undefined method CI_Loader::table_addRow() F:\phpStudy\PHPTutorial\WWW\JKInquiry\application\controllers\interface_Applets.php 1510
ERROR - 2021-04-06 16:29:09 --> Severity: error --> Exception: Call to undefined method CI_Loader::table_addRow() F:\phpStudy\PHPTutorial\WWW\JKInquiry\application\controllers\interface_Applets.php 1510
ERROR - 2021-04-06 16:30:38 --> Query error: Unknown column 'c_lookhouse' in 'field list' - Invalid query: UPDATE `history_price` SET `c_lookhouse` = '咋説', `c_housetel` = '13325289965', `c_rem` = '', `c_projstate` = '委托中'
WHERE `c_priceid` = '234234234'
