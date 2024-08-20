<?
header('Content-Type: text/html; charset=utf-8');
session_start();

require_once "../phpfunc/curd.php";


$debugMode = false;

if($debugMode){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (is_array($_GET)) {
    foreach ($_GET as $k => $v) {
        $ar_prm[$k] = $v;
    }
}
if (is_array($_POST)) {
    foreach ($_POST as $k => $v) {
        $ar_prm[$k] = $v;
    }
}

$onappauth = new onappauth();

switch ($ar_prm["method"]) {
    case 'login':
        $onappauth->login($ar_prm["par_username"], $ar_prm["par_password"]);
        break;
    default:
        echo "nothing is but what is not. method not found";
        break;
}

class onappauth {
    

    //same api as nca sleep check lol
    static $loginapi = "http://61.91.248.21/nca_project/app_webservice/ncadriverdidntsleep.checkusr.json.php";

    function login($username, $password) {
      
        $result = $this->curlPost(self::$loginapi, array('username' => $username, 'password' => $password));

        $ar_userData = json_decode($result, true);

        $resCode =  $ar_userData["resCode"];

        if($resCode == "1"){
            $this->createSession($ar_userData);
        }else{
            $this->redirect("v_login.php?rtn=1");
        }
        
    }

    function createSession($userData) {

        $_SESSION["credential"] = array(
            "empid" => $userData["data"]["userId"],
            "empcode" => $userData["data"]["empCode"],
            "empname" => $userData["data"]["displayName"],
        );
        
        // print_r($_SESSION["credential"]);

        $this->redirect("v_renderform.php");
    }

    function redirect($url) {
        echo "<script>window.location = '$url';</script>";
        return;
    }

    function curlPost($url, $data){   
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }
        
}