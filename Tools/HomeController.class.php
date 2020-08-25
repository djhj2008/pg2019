<?php
namespace Tools;
use Think\Controller;

class HomeController extends Controller{
    public function jump($url){
        ob_end_clean();
        header("Location:$url");
            exit; 
    }


 

     function getOS(){
            $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
            if(strpos($agent, 'windows nt')) {
              $platform = 'windows';
            } elseif(strpos($agent, 'macintosh')) {
              $platform = 'mac';
            } elseif(strpos($agent, 'ipod')) {
              $platform = 'ipod';
            } elseif(strpos($agent, 'ipad')) {
              $platform = 'ipad';
            } elseif(strpos($agent, 'iphone')) {
              $platform = 'iphone';
            } elseif (strpos($agent, 'android')) {
              $platform = 'android';
            } elseif(strpos($agent, 'unix')) {
              $platform = 'unix';
            } elseif(strpos($agent, 'linux')) {
              $platform = 'linux';
            } else {
              $platform = 'other';
            }
            return $platform;
    }
    //构造方法
    function __construct(){
        //为了避免覆盖父类的构造方法，先执行父类的构造方法
        parent::__construct();
        //用户访问权限控制
        //获得当前正在访问的"控制器-操作方法 nowac"
        //判断nowac是否在用户的角色权限列表里边存在
        //① 当前请求的控制器-操作方法
        $nowac = CONTROLLER_NAME."-".ACTION_NAME;
        
        //用户没有登录系统，就使其退出并进入到登录页面
        //有一些操作，允许在"退出的状态"也让访问
        $rang_ac = "Manager-loginbase,Manager-register,Manager-devlist,Manager-querytemp,Devselect-startnew,Longdx-getstatesync,Longdx-gettempnow,Longdx-sendsmscode,Longdx-test";
        //进行访问设备判断,也就是对操作系统的判断
        if ($this->getOS()=='windows' || $this->getOS()=='mac' || $this->getOS()=='unix' || $this->getOS()=='linux') { 
        //① 用户不在登录状态
        //② 用户的操作 还没有在$rang_ac出现
           function userset(){
              	//dump($_SESSION['userid']);
              	//dump($_SESSION['admin_userid']);
              	//dump($nowac);
              if($_SESSION['userid']||$_SESSION['admin_userid']){
                return false;
               }
             
               return true;
           }
           
           if(userset() && strpos($rang_ac,$nowac)===false){
                   $js = <<<eof
                <script type="text/javascript">
                window.top.location.href="/pg/index.php/Home/Manager/loginbase";
                </script>
eof;
            echo $js;
            }
            

        }
        
          
    }
}
