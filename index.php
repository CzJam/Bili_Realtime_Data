<?php


require('database.php');
require('controller.php');

// 禁止未授权的访问
    if(!$usr){
     return http_response_code(404);
    }

?>

<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title id="name"><?php echo "数据统计 - $nick  " ?></title>
		<link rel="icon" href="bili.ico">
		
		<!--两种主题-->
		<link href="light.css" rel="stylesheet">
		<!--<link href="dark.css" rel="stylesheet">-->
	
		<!--自动刷新-->
		<script>
	        setTimeout('refresh()','<?php echo $refreshtime?>'); 
        function refresh()
        { 
            window.location.reload();
        }
        
	    </script>

        

	</head>
	<body>
	   
	    
	    <!--时钟与日期-->
        
        <!--<p id="time" style="text-align:center;font-size:90px;margin:-10px 1px 1px 1px;font-family:font2;"><?php echo $showtime=date("H:i");?></p>-->
        <!--<p style="text-align:center;font-size:22px;margin:-12px 3px 2px 3px;font-family:font2;"><?php echo $showtime=date("Y年m月d日");?></p>-->
	    
        <div class="fansNum" style="margin:4% auto">
            <p class="realTimeFans"><?php echo " @$nick  实时粉丝" ?></p>
            <p class="realTimeFansNum"><?php echo number_format($fans); ?></p>
            <p class="incFans" style="text-align:center;">日涨粉: <a class="incFansNum";><?php echo number_format($newfans)?></a style="color:white">&emsp;月涨粉: <a class="incFansNum";><?php echo number_format($newfansmonth)?></a></p>
            
        </div>
        
        <?php for($vlist=0;$vlist<$totalVideo;$vlist++){?>
        <hr>
        <div class="dataList">
            <p><?php echo $dataHotGate[$vlist].'<a class="videoCaption" style="vertical-align:middle;" href="https://www.bilibili.com/video/'.$bvid[$vlist].'">'.$title[$vlist].'</a>'?></p>
            <p style="text-align:center;margin:-1% 1%">播放：<a class="play"><?php echo round(floatval($totalPlay[$vlist]/10000),2)."w" ?></a>
            &emsp; 在线：<a style="color:#00E676;font-size:30px"><?php echo "$online[$vlist]" ?></a></p><br>
            
            <p class="dataCaption">
            点赞: <a class="triCombo";><?php echo $like[$vlist] ?></a>
            &nbsp; 投币: <a class="triCombo"><?php echo $coin[$vlist]?></a>
            &nbsp; 收藏: <a class="triCombo"><?php echo $fav[$vlist] ?></a><br></p>
            
            <p class="dataCaption">
            赞率: <a class="triCombo";><?php echo $likeRatio[$vlist] ?></a>
            &nbsp; 币率: <a class="triCombo"><?php echo $coinRatio[$vlist] ?></a>
            &nbsp; 收率: <a class="triCombo"><?php echo $favRatio[$vlist] ?></a><br></p>
        </div> 
        
        <?php }?>
        
        
       
	</body>
	
	</html>
