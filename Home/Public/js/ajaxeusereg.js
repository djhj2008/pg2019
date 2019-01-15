    

   //manager_eusereg的ajax验证
    <script type="text/javascript">
         function CheckName(){
           
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function(){
          if(xhr.readyState==4){
            document.getElementById('ts5').innerHTML = xhr.responseText;
            }
          }
          var ming = document.getElementById('name_id').value;

          //thinkphp框架所有的操作都是‘控制器—操作方法’的结合
          xhr.open('get','<%%%$smarty.const.__CONTROLLER__%%%>/CheckName/name/'+ming);
          xhr.send(null);
                        }
    function pwd(){
           
            var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function(){
          if(xhr.readyState==4){
            document.getElementById('pw').innerHTML = xhr.responseText;
            }
          }
          var ming = document.getElementById('pawd').value;

          //thinkphp框架所有的操作都是‘控制器—操作方法’的结合
          xhr.open('get','<%%%$smarty.const.__CONTROLLER__%%%>/paword/name/'+ming);
          xhr.send(null);
        
        }    
     function code1(){
           
            var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function(){
          if(xhr.readyState==4){
            document.getElementById('co').innerHTML = xhr.responseText;
            }
          }
          var ming = document.getElementById('cod').value;

          //thinkphp框架所有的操作都是‘控制器—操作方法’的结合
          xhr.open('get','<%%%$smarty.const.__CONTROLLER__%%%>/code2/name/'+ming);
          xhr.send(null);
        
        }    
    function  email(){
           
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function(){
                if(xhr.readyState==4){
                    document.getElementById('eml').innerHTML = xhr.responseText;
                    }
                }
                var ming = document.getElementById('ema').value;

                //thinkphp框架所有的操作都是‘控制器—操作方法’的结合
                xhr.open('get','<%%%$smarty.const.__CONTROLLER__%%%>/email/name/'+ming);
                xhr.send(null);
        
        }  

        function shouji(){
               
                var xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function(){
                    if(xhr.readyState==4){
                        document.getElementById('ts70').innerHTML = xhr.responseText;
                        }
                    }
                    var ming = document.getElementById('inp10').value;

                    //thinkphp框架所有的操作都是‘控制器—操作方法’的结合
                    xhr.open('get','<%%%$smarty.const.__CONTROLLER__%%%>/checkshouji/name/'+ming);
                    xhr.send(null);
            
            }
       function  pid1(){
           
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function(){
                if(xhr.readyState==4){
                    document.getElementById('p1').innerHTML = xhr.responseText;
                    }
                }
                var ming = document.getElementById('p').value;

                //thinkphp框架所有的操作都是‘控制器—操作方法’的结合
                xhr.open('get','<%%%$smarty.const.__CONTROLLER__%%%>/pid2/name/'+ming);
                xhr.send(null);
        
        }  
    </script>
