<?
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("Asia/Bangkok");
$gb_notlogin = true;
require "../include.inc.php";

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

$proxy = new proxGetData();

$method = $ar_prm["method"];

switch ($method) {
    case 'getcompfunc';
        echo $proxy->getCompfunc();
        break;
    case 'getdepartment';
        echo $proxy->getDepartment($ar_prm["par_compfuncid"]);
        break;
    case 'getsection';
        echo $proxy->getSection($ar_prm["par_departmentid"]);
        break;
    case 'getCompfunc';
        echo $proxy->getCompfunc();
        break;
    case 'getDepartment';
        echo $proxy->getDepartment($ar_prm["par_compfuncid"]);
        break;
    case 'getSection';
        echo $proxy->getSection($ar_prm["par_departmentid"]);
        break;
    case 'getEmp';
        echo $proxy->getEmp($ar_prm["par_func"],$ar_prm["par_dep"],$ar_prm["par_sec"]);
        break;
    case 'getEmpData':
        echo $proxy->getEmpCode($ar_prm["empCode"]);
        break;
    case 'getEmpCodeEX':
        echo $proxy->getEmpCodeEX($ar_prm["empCode"]);
        break;
    default:
        echo json_encode(array("resCode" => "99", "resMsg" => "No Method was specified"));
        break;
}

class proxGetData{
        
    function getCompfunc(){ // สายงาน
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getcompfunc";
        $exc = $this->curlGetNca($endpoint);
        return $exc;
    }

    function getDepartment($par_compfuncid){ // ฝ่าย
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getdepartment&par_compfuncid=$par_compfuncid";
        $exc = $this->curlGetNca($endpoint);
        return $exc;
    }

    function getSection($par_departmentid){ // แผนก
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getsection&par_departmentid=$par_departmentid";
        $exc = $this->curlGetNca($endpoint);
        return $exc;
    }

    public function defaultReturn(){
        return json_encode(array("resCode" => "99", "respMsg" => "This function is not ready yet!"));
    }

    function getEmp($par_func,$par_dep,$par_sec){ // แผนก
        $par_ram = "?method=getemp&par_func=".$par_func."&par_dep=$par_dep"."&par_sec=$par_sec";
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php".$par_ram;
        $exc = $this->curlGetNca($endpoint);
        return $exc;
    }

    function getEmpCode($par_empcode){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
        $exc = $this->curlGetNca($endpoint);
        return $exc;
    }

    function getEmpCodeEX($par_empcode){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
        $exc = $this->curlGetNca($endpoint);
        return $exc;
    }

    function curlPostNca($url, $data){  
        //echo var_dump($data);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //$data = json_encode($data);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

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

}