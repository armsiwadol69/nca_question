<?
date_default_timezone_set("Asia/Bangkok");
session_start();
$gb_notlogin = true;
require "../include.inc.php";
require "customfunction.php";
require_once ("../class/class.question.php");

$ncaquestion = new question();

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

$method = $ar_prm["method"];

$proxy = new proxGetData();

switch ($method) {
    case 'getCompfunc';
        echo json_encode( $ncaquestion->getCompfuncData());
        break;
    case 'getDepartment';
        echo json_encode($ncaquestion->getDepartmentData($ar_prm["par_compfuncid"]));
        break;
    case 'getSection';
        echo json_encode($ncaquestion->getSectionData($ar_prm["par_departmentid"]));
        break;
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
    case 'getQestionForRender';
        echo $proxy->getQestionForRender($ar_prm);
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
        
    private $masterJoAuth = array(
        "user" => "OTSCRM",
        "password" => "WV5dTVxX"
    );

    public function getCustomerRefBy($type, $refNo){
        global $proxy;
        switch ($type) {
            case '1':
                echo $proxy->getTicketData($refNo);
                break;
            case '2':
                echo $proxy->getBookingData($refNo);
                break;
            case '3':
                echo $proxy->getParcelData($refNo);
                break;
            default:
                echo json_encode(array("resCode" => "99", "resMsg" => "No Method was specified"));
                break;
        }
    }


    public function getParcelData($ref){
        $endpoint = "http://203.146.21.213/ncaprj/nca_project/nca_project/parcel/app_webservice/ncaparcel.crm.api.php";

        $parameters = array("method" => "getparcelinfo", "par_parcelrefno" => "$ref");

        $exc = curlPostNca($endpoint, $parameters);
        $dataInArray = json_decode($exc, true);
        if(!is_array($dataInArray)){
            $ar_rtn = array(
                "resCode" => "0",
                "respMsg" => "Not Found"
            );
            return json_encode($ar_rtn);
        }else{
            $specific = $this->findTraceParcel($dataInArray);
            $dataInArray["target"] = $specific;
            return json_encode($dataInArray);
        }
    }

    public function findTraceParcel($data){
        $data = $data["data"]["parcel"];
        $target = array();
        foreach ($data as $rk => $rv) {
            if($rv["v_refnotrace"] == "1"){
                $target = $rv;
            }
        }
        return $target;
    }

    public function getTicketData($v_ticketno){
        $endpoint = "http://203.146.21.212/apicrm/apicrm.php";

        $parameters = array(
            "command" => "get_ticketno", 
            "user" => $this->masterJoAuth["user"], 
            "password" => $this->masterJoAuth["password"], 
            "v_ticketno" => "$v_ticketno"
        );

        $exc = curlPostNca($endpoint, $parameters);

        
        $rtn = json_decode($exc, true);
        
        $rtn[0]["method"] = "getTicketData";

        return json_encode($rtn[0]);
    }

    public function getBookingData($v_refcodeno){
        $endpoint = "http://203.146.21.212/apicrm/apicrm.php";

        $parameters = array(
            "command" => "get_ticketno", 
            "user" => $this->masterJoAuth["user"], 
            "password" => $this->masterJoAuth["password"], 
            "v_ticketno" => "$v_refcodeno"
        );

        $exc = curlPostNca($endpoint, $parameters);

        
        $rtn = json_decode($exc, true);

        $rtn[0]["method"] = "getBookingData";

        return json_encode($rtn[0]);
    }

    public function getSimilarCase($cusName, $cusTel){
        global $ncacrm;
        $data = $ncacrm->findCaseSameCompaint($cusName, $cusTel, "");
        return $data;
    }

    function getCompfunc(){ // สายงาน
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getcompfunc";
        $exc = curlGetNca($endpoint);
        return $exc;
    }

    function getDepartment($par_compfuncid){ // ฝ่าย
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getdepartment&par_compfuncid=$par_compfuncid";
        $exc = curlGetNca($endpoint);
        return $exc;
    }

    function getSection($par_departmentid){ // แผนก
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getsection&par_departmentid=$par_departmentid";
        $exc = curlGetNca($endpoint);
        return $exc;
    }

    public function defaultReturn(){
        return json_encode(array("resCode" => "99", "respMsg" => "This function is not ready yet!"));
    }

    function getEmp($par_func,$par_dep,$par_sec){ // แผนก
        $par_ram = "?method=getemp&par_func=".$par_func."&par_dep=$par_dep"."&par_sec=$par_sec";
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php".$par_ram;
        $exc = curlGetNca($endpoint);
        return $exc;
    }

    function getEmpCode($par_empcode){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
        $exc = curlGetNca($endpoint);
        return $exc;
    }

    function getEmpCodeEX($par_empcode){
        $endpoint = "http://192.168.10.47/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
        $exc = curlGetNca($endpoint);
        return $exc;
    }

    function getQestionForRender($ar){
        $ncaq = new question();
        return $ncaq->api_getQuestion($ar["v_section"],$ar["v_offensegroup"],$ar["v_offensecategory"],$ar["v_order"]);
    }

}
