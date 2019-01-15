<?php
return array(
	'SHOW_PAGE_TRACE' => true,
	//设置请求的默认分组
    'MODULE_ALLOW_LIST' => array('Home','Admin'),//设置一个对比的分组列表    
	'DEFAULT_MODULE'  => 'Home',//默认模块
    //启用路由功能
    'URL_ROUTER_ON' => true,
    //配置路由规则
    //'URL_ROUTE_RULES' => array(
         
     //   ),
    //报错文件,error为空
    //'TMPL_EXCEPTION_FILE'=>'./public/tpl/error.html',
    //开启不区分大小写
    'URL_CASE_INSENSITIVE' => true,
    //后缀设置,为空为任意后缀
    'URL_HTML_SUFFIX' => '',
    //配置静态路由
    //'URL_MAP_RULES' => array(
         
     //   ),

    //开启Smarty模板引擎
     'TMPL_ENGINE_TYPE'      =>  'Smarty',     // 默认模板引擎
    
    // //给smarty做相关配置
    'TMPL_ENGINE_CONFIG' => array(
        'left_delimiter'  => '<%',
        'right_delimiter'  => '%>',
    ),

    'DB_TYPE'              =>  'mysql',     // 数据库类型
    'DB_HOST'               =>  'localhost', // 服务器地址
    'DB_NAME'               =>  'base',          // 数据库名
    'DB_USER'               =>  'root',      // 用户名
    'DB_PWD'                =>  'dj919',          // 密码
    'DB_PORT'               =>  '3306',        // 端口
    //'DB_PREFIX'             =>  '',    // 数据库表前缀
    'DB_PARAMS'          	=>  array(), // 数据库连接参数    
    'DB_DEBUG'  			=>  TRUE, // 数据库调试模式 开启后可以记录SQL日志
    //'DB_FIELDS_CACHE'       =>  true,        // 启用字段缓存
    'DB_CHARSET'            =>  'utf8',      // 数据库编码默认采用utf8

);