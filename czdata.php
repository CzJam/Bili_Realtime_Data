<?php



@$auth = $_GET["auth"];

$servername = "localhost";
$username = "bilifans";
$password = "rZKf7tjsiaMfXaLK";
$dbname = "bilifans";
$conn = mysqli_connect($servername, $username, $password, $dbname);

    $sql = "SELECT * FROM fansdata WHERE nick='$auth'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $uid =$row["uid"];
    }
    }else{
        return http_response_code(404);
    }
    $refreshtime=180000;
    
            

    
if ($uid != null) {
    switch ($auth) {
        case 'czdata':
            $totalVideo=3;
            $refreshtime=20000;
            break;
        
        default:
            $totalVideo=10;
            break;
    }
    
    
    $contentsFans = curl_get_https('https://api.bilibili.com/x/relation/stat?vmid='.$uid);
    $fansNum = json_decode($contentsFans);
    $fans =$fansNum->data->follower; 
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)) {
        $newfansmonth=$fans-$row["monthfans"];
        $newfans= $fans-$row["fans"];
    }
    
    $contentsNick = curl_get_https('https://api.bilibili.com/x/space/acc/info?mid='.$uid);
    $userNick = json_decode($contentsNick);
    $nick =$userNick->data->name; 
    
    $contentsNewVideo = curl_get_https('https://api.bilibili.com/x/space/arc/search?mid='.$uid); 
    $videoList = json_decode($contentsNewVideo); 

    
    for ($i=0;$i<$totalVideo;$i++){
        
        $totalPlay[$i] =$videoList->data->list->vlist[$i]->play; 
        $title[$i] =$videoList->data->list->vlist[$i]->title; 
        $bvid[$i] =$videoList->data->list->vlist[$i]->bvid; 
        $aid[$i] =$videoList->data->list->vlist[$i]->aid; 
    
        $contentsBasicData = curl_get_https('https://api.bilibili.com/x/web-interface/view?aid='.$aid[$i]); 
        $contentsGetCid = curl_get_https('http://api.bilibili.com/x/player/pagelist?bvid='.$bvid[$i]); 
        $videoBasicData = json_decode($contentsBasicData); 
        $videoCid = json_decode($contentsGetCid); 
        $cid[$i] =$videoCid->data[0]->cid;
        
        $datalike[$i] =$videoBasicData->data->stat->like; 
        $datacoin[$i] =$videoBasicData->data->stat->coin;
        $datafavorite[$i] =$videoBasicData->data->stat->favorite;
        @$dataishotgate[$i]=$videoBasicData->data->honor_reply->honor[0]->desc;
        
        if($dataishotgate[$i]=="热门"){
            $dataishotgate[$i]='<a class="headHoriHot">热门</a>';
        }
        else{
            $dataishotgate[$i]="";
        }
        
        $contentsOnline = curl_get_https('https://api.bilibili.com/x/player/online/total?cid='.$cid[$i]."&bvid=".$bvid[$i]);
        $onlineNum = json_decode($contentsOnline);
        $online[$i] =$onlineNum->data->total; 
    }
    
}

function curl_get_https($url){
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
    $tmpInfo = curl_exec($curl); 
    curl_close($curl);
    return $tmpInfo; 
}

?>

<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title id="name"><?php echo "数据统计 - $nick  " ?></title>
		<link rel="icon" href="bili.ico">
		<?php 
		if($auth=="czdata"){
		    echo '<link href="light.css?1" rel="stylesheet">';
		}
		else{
		    echo '<link href="dark.css?2" rel="stylesheet">';
		}
		?>
		
		<script>
	        setTimeout('refresh()','<?php echo $refreshtime?>'); 
	        
        function refresh()
        { 
            window.location.reload();
        }
	    </script>

        

	</head>
	<body>
	   
	    
	    
        
        <!--<p id="time" style="text-align:center;font-size:90px;margin:-10px 1px 1px 1px;font-family:font2;"><?php echo $showtime=date("H:i");?></p>-->
        <!--<p style="text-align:center;font-size:22px;margin:-12px 3px 2px 3px;font-family:font2;"><?php echo $showtime=date("Y年m月d日")." 周".$weekarray[date("w")];?></p>-->
	    
        <div class="fansNum" style="margin:4% auto">
            <p class="realTimeFans"><?php echo " @$nick  实时粉丝" ?></p>
            <p style="text-align:center;margin:-12px 1px 0px 1px;font-size:48px;color:#04B0FD"><?php echo number_format($fans); ?></p>
            <p class="incFans" style="text-align:center;">日涨粉: <a style="color:#00E676;font-size:30px";><?php echo number_format($newfans)?></a style="color:white">&emsp;月涨粉: <a style="color:#00E676;font-size:30px";><?php echo number_format($newfansmonth)?></a></p>
            
        </div>
        
        <?php for($vlist=0;$vlist<$totalVideo;$vlist++){?>
        <hr>
        <div class="dataList">
            <p><a style="vertical-align:middle;"><?php echo $dataishotgate[$vlist] ?></a><a style="vertical-align:middle;"><?php echo $title[$vlist] ?></a></p>
            <p style="text-align:center;margin:-1% 1%">播放：<a class="play"><?php echo round(floatval($totalPlay[$vlist]/10000),2)."w" ?></a>
            &emsp; 在线：<a style="color:#00E676;font-size:30px"><?php echo "$online[$vlist]" ?></a></p><br>
            
            <p class="dataCaption">
            点赞: <a class="triCombo";><?php echo round(floatval($datalike[$vlist]/10000),2)."w" ?></a>
            &nbsp; 投币: <a class="triCombo"><?php echo round(floatval($datacoin[$vlist]/10000),2)."w" ?></a>
            &nbsp; 收藏: <a class="triCombo"><?php echo round(floatval($datafavorite[$vlist]/10000),2)."w" ?></a><br></p>
            
            <p class="dataCaption">
            赞率: <a class="triCombo";><?php echo round(floatval($datalike[$vlist]/$totalPlay[$vlist]),4)*100 ."%" ?></a>
            &nbsp; 币率: <a class="triCombo"><?php echo round(floatval($datacoin[$vlist]/$totalPlay[$vlist]),4)*100 ."%" ?></a>
            &nbsp; 收率: <a class="triCombo"><?php echo round(floatval($datafavorite[$vlist]/$totalPlay[$vlist]),4)*100 ."%" ?></a><br></p>
        </div> 
        
        <?php }?>
        
        
       
	</body>
	
	</html>