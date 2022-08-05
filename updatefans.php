<?php

$update = $_GET["update"];

require('database.php');
     
    
    $conn = mysqli_connect($servername, $username, $password, $dbname);
    if (!$conn) {
        die("连接失败: " . mysqli_connect_error());
    }
    
    
    $sql = "SELECT * FROM fansdata";
    $result = mysqli_query($conn, $sql);
    
    if($update=="daily"){
    while($row = mysqli_fetch_assoc($result)){
         
         if (mysqli_query($conn, "UPDATE fansdata SET dailyFans=".get_fans($row["uid"])." WHERE nick='{$row["nick"]}'")==1){
             echo "每日数据更新完成：".$row["nick"];
             echo "<hr>";
         }
    }
    }
    
    
    if($update=="monthly"){
    while($row = mysqli_fetch_assoc($result)){
         
         if (mysqli_query($conn, "UPDATE fansdata SET monthlyFans=".get_fans($row["uid"])." WHERE nick='{$row["nick"]}'")==1){
             echo "每月数据更新完成：".$row["nick"];
             echo "<hr>";
         }
    }
    
    }
    
    mysqli_close($conn);




function CurlGetData($url){
    $curl = curl_init(); 
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
    $tmpInfo = curl_exec($curl); 
    curl_close($curl);
    return $tmpInfo; 
}

function get_fans($uid){
    $contentsFans = CurlGetData('https://api.bilibili.com/x/relation/stat?vmid='.$uid);
    $fansNum = json_decode($contentsFans);
    return $fansNum->data->follower; 
}

?>