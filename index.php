<?php
header("content-type:text/html;charset=utf-8");
//设置模式
define('APP_DEBUG',true);//开发调试模式
//路径
define('ACSS_URL','/pg/Home/Public/css/');
define('IMG_URL','/pg/Home/Public/img/');
define('JQPLOT','/pg/Home/Public/jqplot/');

define('AJS_URL','/pg/Home/Public/js/');
define('ABOOT_URL','/pg/Home/Public/bootstrap/');
define('ACK_URL','/pg/Home/Public/ckeditor/');
define('PUBLIC_ROOT_URL','/pg/Home/Public/');
define('WEUI_URL','/pg/Home/Public/weui');
define('CON_URL','/pg/Home/Controller/');
//引入tp框架的接口文件
include "./ThinkPHP/ThinkPHP.php";