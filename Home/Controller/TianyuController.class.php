<?php
namespace Home\Controller;
use Think\Controller;
class TianyuController extends Controller {
	function request_post($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        
        $postUrl = $url;
        $curlPost = $param;
        
        dump($postUrl);
        dump($curlPost);
        
        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式
        curl_setopt($curl, CURLOPT_POSTFIELDS, $curlPost);
        $data = curl_exec($curl);//运行curl
        curl_close($curl);
        return $data;
  }
  
  function gettoken($pic1,$id){
    $url = 'https://aip.baidubce.com/oauth/2.0/token';
    $post_data['grant_type']       = 'client_credentials';
    $post_data['client_id']      = '7XtPdkO4nhBP6N4H1NthUeqj';
    $post_data['client_secret'] = 'OT25NB8KuNystNDyMRs7ShX2ou3Va8vU';
    $o = "";
    foreach ( $post_data as $k => $v ) 
    {
        $o.= "$k=" . urlencode( $v ). "&" ;
    }
    $post_data = substr($o,0,-1);
    
    //var_dump($post_data);
    $memcache = new \Memcache;
		$mem_ret=$memcache->connect('localhost', 11211);
		if($mem_ret===true){

			$get_result = $memcache->get('access_token');
			if($get_result===false){
		    $res = http($url, $post_data,'POST');
				$ret=json_decode($res,TRUE);
				$access_token=$ret['access_token'];
				$expire_time=2592000;
				$memcache->set('access_token',$access_token, false, $expire_time);
			}else{
				$access_token=$get_result;
			}
			$memcache->close();
			//dump($access_token);
		}    
		
		if($id==1){
			$pic2="444.jpg";
		}else if($id==2){
			$pic2="555.jpg";
		}else if($id==3){
			$pic2="666.jpg";
		}else if($id==4){
			$pic2="777.jpg";
		}else if($id==5){
			$pic2="888.jpg";
		}else if($id==6){
			$pic2="999.jpg";
		}else if($id==7){
			$pic2="1000.jpg";
		}else if($id==8){
			$pic2="1001.jpg";
		}else if($id==9){
			$pic2="1002.jpg";
		}else{
			$pic2="444.jpg";
		}
		
    $url = 'https://aip.baidubce.com/rest/2.0/face/v1/merge?access_token='.$access_token;

    $image['image_template']['image']="http://engine.mjiangtech.cn/pg/".$pic1;
		$image['image_template']['image_type']="URL";
		$image['image_template']['quality_control']='NONE';
		
    $image['image_target']['image']="http://engine.mjiangtech.cn/pic/".$pic2;
		$image['image_target']['image_type']="URL";
		$image['image_target']['quality_control']='NONE';
		
		//dump($image);
		
		$data=json_encode($image);
		$res=http($url,$data,'POST');
		$ret=json_decode($res,TRUE);
		$img=$ret['result']['merge_image'];
		//dump($ret);
		
		$imageName = "25220_".date("His",time())."_".rand(1111,9999).'.png';
		$path = "normalup/".date("Y-m-d",time());

		if (!is_dir($path)){ //判断目录是否存在 不存在就创建
		   mkdir($path,0777,true);
		}
		if($img){
			$imageSrc=  $path."/". $imageName;  
			$r = file_put_contents($imageSrc, base64_decode($img));
			return $imageSrc;
		}
		else{
			return false;
		}
  }
  
	public function upload(){
	    $upload = new \Think\Upload();// 实例化上传类
	    $upload->maxSize   =     3145728 ;// 设置附件上传大小
	    $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
	    $upload->rootPath  =     'normalup/'; // 设置附件上传根目录
	    $upload->savePath  =     ''; // 设置附件上传（子）目录
	    // 上传文件 
	    $info   =   $upload->upload();
	    //dump($_POST['rate_id']);
	    $id=(int)$_POST['rate_id'];
	    if(!$info) {// 上传错误提示错误信息
	        dump($info);
	        exit;
	    }else{// 上传成功
	        //dump($info);
	        $img='normalup/'.$info['photo']['savepath'].$info['photo']['savename'];
	        //dump($img);
	        $ret=$this->gettoken($img,$id);
	    }
	    //dump($ret);
	    //exit;
	  $this->assign('img1',"http://engine.mjiangtech.cn/pg/".$img);
		$this->assign('img2',"http://engine.mjiangtech.cn/pg/".$ret);
		$this->display();
	}
  public function index(){
  	$this->display();
  }
  
}