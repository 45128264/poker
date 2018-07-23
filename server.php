
<?php
define('cfg', include_once('config.php'));
include_once('./json.php');

class Poker {
    public $server;
    public $cfg;
    public $numpoker;
    public $rule;
    public $userlist = [];
    public $group = [['user'=>[]],['user'=>[]],['user'=>[]]];
    public function __construct() {
        $this->server = new swoole_websocket_server('0.0.0.0', 9501);
        $this->cfg = include_once('config.php');
        $this->numpoker = $this->cfg['card'];
        $this->rule = $this->cfg['rule'];
        $this->server->on('open', function (swoole_websocket_server $server, $request) {
                $this->onOpen($server, $request);
            });
        $this->server->on('message', function (swoole_websocket_server $server, $frame){
            $this->onMessage($server, $frame);
        });
        $this->server->on('request', function ($request, $response) {
                $this->onRequest($request, $response);
            });
        $this->server->on('close', function ($ser, $fd) {
                $this->onClose($ser, $fd);
        });

        $this->server->start();
    }
    /*message数据结构
    *type:当前消息类型:
        *login 进入游戏
        *loginout 登出游戏
        *enter 进入房间
        *exit  退出房间
        *ready 准备游戏 数据结构:0未准备 1准备
        *putcard  出牌 数据结构:0不要，1出牌
        *call    叫地主0，抢地主1，不要2
        *start   开始游戏
        *end     游戏结束
    *data
    */
   /*$oepartion 
        0:'叫地主'
        1:'抢地主'
        2:'不叫'
        3:'准备'
        4:'取消'
        5:'退出房间'
        6:'不要'
        7:'出牌'
        8:'提示'
   */
    public function onMessage($server, $frame){
        //type::login,message,loginout,call,enter
        $data = json_decode($frame->data);
        //回复消息的数组message
        $m = [];
        var_dump($data);
        $m['name'] = $data['name'];
        $m['type'] = $data['type'];
        $id = $this->getId($data['name']);
        switch($data['type']){
            case 'login':
                    $list = array_column($this->userlist, 'name');
                    if(in_array($data['name'], $list)){
                        foreach($this->userlist as &$v){
                            if($v['name'] == $data['name']){
                                if($v['login']){
                                    $m['message'] = '该帐号已经登录,请重新输入用户名';
                                    $m['type'] =  'error';
                                }else{
                                    $v['login'] = true;
                                    $v['id'] = $frame->fd;
                                }
                            }
                        }
                    }else{
                        $user = [];
                        $fd = $frame->fd;
                        $user['login'] = true;
                        $user['name'] = $data['name'];
                        $user['ready'] = false;
                        $this->userlist[$fd] = $user;
                    }
                    $m['group'] = $this->group;
                    break;
            case 'loginout':break;
            case 'play':
                $card = $m['data'];
                //判断牌型是否正确
                $thegroup = &$this->group[$data['group']];
                if($iscard = isTrueCard($card['data'])){
                    if($thegroup['prep']){
                        if(dataHandle($thegroup['prep'],$iscard)){
                            delCard($frame->fd,$card['list']);
                            $thegroup['prep'] = $iscard['data'];

                            $user = [];
                            array_push($user,$this->userlist[$thegroup[0]]);
                            array_push($user,$this->userlist[$thegroup[1]]);
                            array_push($user,$this->userlist[$thegroup[2]]);
                            $m['msg'] = '牌型正确';
                            $m['groupval'] = $thegroup;
                            break;
                        }
                    }else{
                        $thegroup['prep'] = $iscard['data'];
                        break;
                    }
                }
                $m['type'] == 'error';
                $m['message'] = '您出的牌不正确';
                break;
            case 'enter':
                    
                    $m['operation']=[3,5];
                    foreach($this->group as $v){
                        if(in_array($frame->fd,$v)){
                            $m['message'] = '你已经加入其他房间,请勿多次加入';
                            $m['type'] = 'error';
                        }
                    }
                    $currGroup = $this->group[$data['group']]['user'];
                    if(count($currGroup) < 3){
                        array_push($this->group[$data['group']]['user'],$frame->fd);
                        $m['group'] = $data['group']; 
                    }else{
                        $m['message'] = '该房间已经满员,请重新选择房间';
                        $m['type'] = 'error';
                    }
                    break;
            case 'exit':break;
            case 'ready':
                    //
                    $this->userlist[$id]['ready'] = !$this->userlist[$id]['ready'];
                    if($this->userlist[$id]['ready']){
                        $m['operation']=[4];
                    }else{
                        $m['operation']=[3,5];
                    }
                    $start = true;
                    $groupId = $data['group'];
                    $currgroup = $this->group[$groupId];
                    $groupUser =  $currgroup['user'];
                    foreach($groupUser  as $k => $v){
                        if($this->userlist[$v]['ready'] == 0){
                            $start = false;
                        }
                    }
                    if(count($groupUser) == 3 && $start){
                        $m['type'] = 'putcard';
                        $m['card'] = $this->randCard($groupId);
                        $m['group'] = $groupId;
                        $m['operation'] = [0,2];
                        print_r($m);
                        //初始化当前牌局
                        $this->group[$groupId]['card'] = $m['card'];
                        $this->group[$groupId]['start'] = rand(0,2);
                        echo $this->group[$groupId]['start'];
                        echo '我是随机数';
                        $this->group[$groupId]['lander'] = $this->group[$groupId]['start'];

                        $this->group[$groupId]['call'] = 0;
                        $m['currgroup'] = $this->group[$groupId];
                    }

                    break;
            case 'start':break;
            case 'end':break;
            case 'call':
                $currGroup = &$this->group[$data['group']];
                $currGroup['call']++;
                print_r($currGroup);
                
                if($data['opera']!=2){
                     $currGroup['lander'] = $data['value'];
                }
                if($currGroup['call'] <= 3){
                    //判断是否为不叫

                    $m['operation'] = [1,2];
                    if($currGroup['start']==2){
                        $currGroup['start']=0;
                    }else{
                        $currGroup['start']++;
                    }
                }else{
                    //三轮地主抢完，确认地主人选
                    $m['type'] = 'start';
                    //出牌和提示
                    $m['operation'] = [7,8];
                    $this->group[$data['group']]['card'][0][$currGroup['lander']]['card'] = array_merge($currGroup['card'][0][$currGroup['lander']]['card'],$currGroup['card'][1]);
                }

                $m['currgroup'] = $this->group[$data['group']];
                break;

        }
        print_r($m);
        $this->sendMessage($m);
    }
    public function onOpen($server, $request){
    //  $user = [];
    //  if(count($this->userlist)==0){
    //      // $user['name'] = 'user'.$request->fd;
    //      $user['id'] = $request->fd;
    //      $user['login'] = true;
    //      array_push($this->userlist,$user);
    //  }
    //  $status=1;
    }
    public function onRequest($request, $response){
        // 接收http请求从get获取message参数的值，给用户推送
        // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
        foreach ($this->server->connections as $fd) {
            var_dump($fd);
            $this->server->push($fd, $request->get['message']);
        }
    }
    public function onClose($server,$fd){
        var_dump($server);
        var_dump($fd);
        echo 'client {$fd} closed\n';
    }
   
    public function sendMessage($m){
        $message = __json_encode($m);
        foreach ($this->server->connections as $fd) {
            $this->server->push($fd,$message);
        }
    }
    /*大小对比***********
    *prep:上一手牌
    *next:当前出牌
    *数据格式
    *   [
    *       'data'=>[['type'=>'space','value'=>'5','level'=>'3']],
    *       'len' => 1,
    *       'type' = 'single'
    *   ]
    */
    public function dataHandle($prep,$curr){
        $bool = false;
        if($prep['type'] == $currp['type']){
            if($prep['data']['level'] < $currp['data']['level']){
                $bool = true;
            }
        }else if($this->rule[$prep['type']]['level'] < $this->rule[$currp['type']]['level']){
            $bool = true;
        }
        return $bool;
    }


    /*删除已出的牌***********
    *n:组
    *id,用户id
    *data:data
    */
    public function delCard($id,$data){
        $card = &$this->userlist[$id]['card'];
        forEach($data as $v){
            $offset = array_search($v,$card)
            array_splice($card,$offset,1);
        }
    }
    //牌型去重
    public function repeat($data){
        $list = [];
        $arr = [];
        forEach($data as $item){
            $key = $arr.indexOf($item['level']);
            if($key == -1){
                $obj = {};
                $obj['level']=$item['level'];
                $obj['num']=1;              
                array_push($arr,$item['level']);
                array_push($list,push($obj));
            }else{
                $list[$key]['num']++;
            }
        })
        return $list;
    }
    //牌型确认
    public function isTrueCard ($data){
        //从小到大排序,数组反转
        $list = $data;
        $arr = (array_sort($this->repeat($list)),'num',SORT_DESC);
        $current ={};
        $isKing = [20,30]        
        $current['data'] = $list;
        if(count($arr)==0){
            return false;
        }
        switch($arr[0]['num']){
            case 1:
                if(count($list) == 1){
                    $current['type'] = 'one';
                    $current['level'] = $arr[0]['level'];
                    $current['other'] = '';
                    break;
                }else if(array_search($isKing,$list[0]['level']) != -1 && array_search($isKing,$list[0]['level'] != -1 &&  count($list) == 2){
                    $current['type'] = 'kingtwo';
                    break;
                }else if(count($list) > 4 ){
                    $sarr = array_sort($arr,'level',SORT_DESC);
                    $drr = $this->cardfilter($sarr);
                    if(count($drr) == count($arr)){
                        $current['type'] = 'list';
                        $current['len']= count($sarr);
                        break;
                    }
                }
                // console.log($current);
                return false;
                break;
            case 2:
                if(count($arr)== 1){
                    $current['type'] = 'two';
                    $current['level'] = $arr[0]['level'];
                    break;
                }else if(count($arr) >2 ){
                    //以arr降序排列，进行顺子筛选和单牌数量筛选
                    $sarr =  array_filter($this->cardfilter(array_sort($arr,'level',SORT_DESC), function($v, $k) {
                            return $v['num']==2;
                        }, ARRAY_FILTER_USE_BOTH);
                    if(count($sarr) == count($arr)){
                        $current['type'] = 'twomore';
                        $current['len'] = count($arr);
                        break;
                    }
                }
                return false;
                break;
            case 3:
                if(count($arr) == 1){
                    $current['type'] = 'three';
                    $current['level'] = $list[0]['level'];
                    break;
                }else   if(count($arr) >1){
                        $threeArr = array_filter($arr, function($v, $k) {
                            return $v['num']==3;
                        }, ARRAY_FILTER_USE_BOTH);

                        $val = 0;
                        forEach($arr as $k=>$v){
                            $val += $v['num'];
                        })
                        $onenum = $val-count($threeArr)*3;
                        if($onenum==0 && count($threeArr) > 1){
                            $current['type'] = 'threemore';
                            $current['len'] = count($threeArr);
                            break;
                        }else if($onenum == 1 && count($threeArr) == 1){
                            $current['type'] = 'three';
                            $current['level'] = arr[0]['level'];
                            $current['other'] = 1;
                            break;
                        }else if($onenum == count($threeArr)){
                            $darr = $this->cardfilter(array_sort($threeArr,'level',SORT_DESC));
                            if(count($darr) == count($threeArr)){
                                $current['type'] = 'threemore';
                                $current['len'] = count($threeArr)
                                break;
                            }
                        }
                }
                return false;
                break;
            case 4:
                if(count($arr) == 1){
                    $current['type'] = 'four';
                    $current['level'] = $list[0]['level'];
                    break;
                }else if(count($arr)>1){
                    $fourArr = array_filter($arr, function($v, $k) {
                            return $v['num']==4;
                        }, ARRAY_FILTER_USE_BOTH);
                    $val = 0;
                    forEach($arr as $k=>$v){
                        $val += $v['num'];
                    })
                    $onenum = $val-$count($fourArr)*4;;
                    if($onenum==0 && count($fourArr) > 1){
                        $current['type'] = 'fourmore';
                        break;
                    }else if($onenum == count($fourArr) || $onenum/count($fourArr)==2){
                        $darr = $this->cardfilter(array_sort($fourArr,'level',SORT_DESC));
                        if(count($darr) == count($fourArr)){
                            $current['type'] = 'fourmore';
                            $current['len'] = count($fourArr);
                            break;
                        }
                    }else {
                        $fournum = array_filter($arr, function($v, $k) {
                            return $v['num']==4;
                        }, ARRAY_FILTER_USE_BOTH);
                        $threenum = array_filter($arr, function($v, $k) {
                            return $v['num']==3;
                        }, ARRAY_FILTER_USE_BOTH); 
                        $othernum = array_filter($arr, function($v, $k) {
                            return $v['num'] !=3 && $v['num'] != 4;
                        }, ARRAY_FILTER_USE_BOTH);
                        if(count($threenum) == count($othernum)){
                            $current['type'] = 'threemore';
                            $current['len'] = count($threenum)+count($fournum);
                            break;
                        }
                    }
                }   
                return false;
                break;
        }
        return $current;

    }
    //得到当前人物的数据
    public function getId ($name){
        foreach ($this->userlist as $k => $v){
            if($v['name'] == $name){
                return $k;
            }
        }
    }
    public function cardfilter($data){
        $list = [];
        array_push($list,$data[0]);
        for($i=0;$i<count($data)-1;$i++){
            if($data[$i]['level']-$data[$i+1]['level']==1){
                array_push($list,$data[i+1]);
            }else{
                return $list;
            }
        }
    }
    /*发牌，初始化组数据*********** 
    *n:组ID
    */
    public function randCard($group_number){
        $list = shuffle($this->numpoker);
        $currGroup = $this->group[$group_number]['user'];
        $this->userlist[$currGroup[0]]['card']=[];
        $this->userlist[$currGroup[1]]['card']=[];
        $this->userlist[$currGroup[2]]['card']=[];
        $endhand = [];
        for($i=0;$i<54;$i++){
            if($i>50){
                array_push($endhand,$i);
                continue;
            }
            $k = floor(($i)/17);
            array_push($this->userlist[$currGroup[$k]]['card'],$i);
        }
        $user = [];
        $card = [];
        array_push($user,$this->userlist[$currGroup[0]]);
        array_push($user,$this->userlist[$currGroup[1]]);
        array_push($user,$this->userlist[$currGroup[2]]);
        array_push($card,$user);
        array_push($card,$endhand);
        return $card;


    }
}
new Poker();
?>