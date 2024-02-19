<?php
header('Content-Type: text/html; charset=utf-8');
session_start();

// if(!isset($_SESSION["userInfo"])){
//     header("location:  phpfucnstaff/logout.php");
// }
function curlGetNca($url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function curlPostNca($url, $data)
{   
    //$data = json_decode(json_encode($data), true);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //$data = json_encode($data);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

?>