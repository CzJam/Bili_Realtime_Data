<?php 

// 获取用户信息
@$usr = $_GET["usr"];


// 连接数据库
$conn = mysqli_connect($servername, $username, $password, $dbname);
$sql = "SELECT * FROM fansdata WHERE nick='$usr'";
$result = mysqli_query($conn, $sql);

// 通过数据库用户名获取uid
if (mysqli_num_rows($result) > 0) {
while($row = mysqli_fetch_assoc($result)) {
    $uid =$row["uid"];
}
}else{
    $usr=false;
}
    
    // 根据不同用户，可分别设定展示视频数量与自动刷新时间（毫秒）
if ($usr!= null) {
    switch ($usr) {
        case 'bishi':
            $totalVideo=3;
            $refreshtime=20000;
            break;
            
        default:
            $totalVideo=10;
            $refreshtime=180000;
            break;
    }
    
    //错误码
    $errorCode='{"code":-509,"message":"请求过于频繁，请稍后再试","ttl":1}';
    
    // 粉丝数
    $contentsFans = str_replace($errorCode,'',CurlGetData('https://api.bilibili.com/x/relation/stat?vmid='.$uid));
    $fansNum = json_decode($contentsFans);
    $fans =$fansNum->data->follower; 
    
    // 当日(月)新增粉丝
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) {
        $newfansmonth=$fans-$row["monthlyFans"];
        $newfans= $fans-$row["dailyFans"];
    }
    
    
    // up主昵称 & 视频列表
    $contentsNewVideo = str_replace($errorCode,'',CurlGetData('https://api.bilibili.com/x/space/wbi/arc/search?mid='.$uid)); 
    $videoList = json_decode($contentsNewVideo); 
    $totalVideoNum =$videoList->data->page->count; 
    $nick =$videoList->data->list->vlist[0]->author; 
    
    // 获取设定的$totalVideo数量的视频数据
    for ($i=0;$i<$totalVideo;$i++){
        
        
        // 播放量、视频标题、bv、aid
        $totalPlay[$i] =$videoList->data->list->vlist[$i]->play; 
        $title[$i] =$videoList->data->list->vlist[$i]->title; 
        $bvid[$i] =$videoList->data->list->vlist[$i]->bvid; 
        $aid[$i] =$videoList->data->list->vlist[$i]->aid; 
    
        $contentsBasicData = CurlGetData('https://api.bilibili.com/x/web-interface/view?aid='.$aid[$i]); 
        $contentsGetCid = CurlGetData('http://api.bilibili.com/x/player/pagelist?bvid='.$bvid[$i]); 
        $videoBasicData = json_decode($contentsBasicData); 
        $videoCid = json_decode($contentsGetCid); 
        $cid[$i] =$videoCid->data[0]->cid;
        
        
        // 在线观看人数
        $contentsOnline = CurlGetData('https://api.bilibili.com/x/player/online/total?cid='.$cid[$i]."&bvid=".$bvid[$i]);
        $onlineNum = json_decode($contentsOnline);
        $online[$i] =$onlineNum->data->total; 
        
        
        // 三连数据
        $dataLike[$i] =$videoBasicData->data->stat->like; 
        $dataCoin[$i] =$videoBasicData->data->stat->coin;
        $dataFav[$i] =$videoBasicData->data->stat->favorite;
        
        // 三连数据展示格式
        $like[$i]=round(floatval($dataLike[$i]/10000),2)."w";
        $coin[$i]=round(floatval($dataCoin[$i]/10000),2)."w";
        $fav[$i]=round(floatval($dataFav[$i]/10000),2)."w";
        
        // 三连率
        $likeRatio[$i]=round(floatval($dataLike[$i]/$totalPlay[$i]),4)*100 ."%";
        $coinRatio[$i]=round(floatval($dataCoin[$i]/$totalPlay[$i]),4)*100 ."%";
        $favRatio[$i]=round(floatval($dataFav[$i]/$totalPlay[$i]),4)*100 ."%";
        
        
        // 上热门判断
        @$dataHotGate[$i]=$videoBasicData->data->honor_reply->honor[0]->desc;
        if($dataHotGate[$i]=='热门'){
            $dataHotGate[$i]='<a class="headHoriHot">热门</a>';
        }
        else{
            $dataHotGate[$i]="";
        }
        
        
    }
    
}
else{
        $usr=false;
    }

function CurlGetData($url){
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/101.0.4951.61 Safari/537.36'); 
    $tmpInfo = curl_exec($curl); 
    curl_close($curl);
    return $tmpInfo; 
}
?>
