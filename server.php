<?php 

header("Content-type:text/html;charset=gb2312");
// error_reporting(E_ALL ^ E_NOTICE);
error_reporting(E_ALL);
//ob_implicit_flush();
set_time_limit(0);
class Socket{
	private $sockets = [];
	private $users = [];
    private $server;
	public $logFile;//日志文件
	public function __construct($addr,$port){		
		//创建一个通讯节点
		//AF_INET ipV4网络协议
		//SOCK_STREAM 给予我们传数据的字节流
		//SOL_TCP 服务需要的协议 另外还有SOL_UDP;
		$this->server = socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
		//接受所有数据
		socket_set_option($this->server, SOL_SOCKET, SO_REUSEADDR,1);
		//节点绑定地址和端口
		socket_bind($this->server,$addr,$port);
		//监听端口
		socket_listen($this->server,$port);
		//所有人
		//
        $this->sockets[] = ['resource' => $this->server];
		//命名日志文件
		$this->logFile = "log/log".date("Y-m-d").'.txt';
		$this->log("started server ".date("Y-m-d H:i:s")."\n");
		$this->log("listen port ".$port."\n");
		echo "链接成功";
		
		while(true){
			$this->goRun();
		}			

		
	}
	public function goRun(){	
		$write = $except = NULL;	
    	$sockets = array_column($this->sockets, 'resource');
    	//$write监听客户端是否有写变化，$except为null是监听全部
    	socket_select ($sockets, $write, $except, NULL);
    	var_dump($this->sockets);
    	foreach($sockets as $socket){
    		if($socket == $this->server){
    			$client = socket_accept($this->server);
    			var_dump($client);
    			$this->sockets[] = $this->server;
    			$key = "a".rand(1,10000);
    			$this->users[$key] = array(
    				"socket"=>$client,
    				"show"=>false
    			);

    		}else{
    			$len = 0;
    			$buf = "";
    			echo "111";
    			do{
    				//读取socket信息，$buf为引用出传参接受数据，第三个是数据长度
    				$l=socket_recv($socket,$buf,1000,0);
                    $len+=$l;
                    $buffer.=$buf;
    			}while($l==1000);

    			$k = $this->search($socket);
    			echo $k;
    			var_dump($buffer);
    			if($len<7){
    				continue;
    			}
    			if(!$this->users[$k]['show']){
    				$this->getHand($k,$buffer);
    			}else{
    				var_dump($buffer);
    				$this->send($k,$buffer);
    			}
    		}
    	}
	}
    function getHand($k,$buffer){

	    //截取Sec-WebSocket-Key的值并加密，其中$key后面的一部分258EAFA5-E914-47DA-95CA-C5AB0DC85B11字符串应该是固定的
	    $buf  = substr($buffer,strpos($buffer,'Sec-WebSocket-Key:')+18);
	    $key  = trim(substr($buf,0,strpos($buf,"\r\n")));
	    $new_key = base64_encode(sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11",true));
	     
	    //按照协议组合信息进行返回
	    $new_message = "HTTP/1.1 101 Switching Protocols\r\n";
	    $new_message .= "Upgrade: websocket\r\n";
	    $new_message .= "Sec-WebSocket-Version: 13\r\n";
	    $new_message .= "Connection: Upgrade\r\n";
	    $new_message .= "Sec-WebSocket-Accept: " . $new_key . "\r\n\r\n";
	    socket_write($this->users[$k]['socket'],$new_message,strlen($new_message));

	    //对已经握手的client做标志
	    $this->users[$k]['show']=true;
	    return true;
     
	}
    public function search($socket){
        foreach ($this->users as $k=>$v){
            if($socket==$v['socket'])
            return $k;
        }
        return false;
    }
	public function log($str){
		$fp = fopen($this->logFile,"a+");
		fwrite($fp,date("Y-m-d H:i:s").":  ".$str);
		fclose($fp); 
	}
}
$ws = new Socket("127.0.0.1",9000);


 ?>