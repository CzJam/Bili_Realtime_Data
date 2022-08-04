<?php

$auth = $_GET["auth"];
 if($auth=="updatenow"){

   $servername = "localhost";
$username = "bilifans";
$password = "rZKf7tjsiaMfXaLK";
$dbname = "bilifans";
     
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die("连接失败: " . mysqli_connect_error());
    }
    
    // $sql = "INSERT INTO FansData (JAM,GKW,BBPCS) VALUES (12, 23, 34)";
    
    
    $sql = "SELECT * FROM fansdata";
    $result = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($result)){
         if (mysqli_query($conn, "UPDATE fansdata SET fans=".get_fans($row["uid"])." WHERE nick='{$row["nick"]}'")==1){
             echo "更新完成：".$row["nick"];
             echo "<hr>";
         }
    }
    
    mysqli_close($conn);
    }
    
else{
    echo "permission denied";
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

function get_fans($uid){
    $contentsFans = curl_get_https('https://api.bilibili.com/x/relation/stat?vmid='.$uid);
    $fansNum = json_decode($contentsFans);
    return $fansNum->data->follower; 
}

?>