<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set("Asia/Bangkok");
$gb_notlogin = true;

require "../include.inc.php";
require "./class.form.php";
require "./class.report.php";

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

$apiKey = "";

$go_ncadb = new ncadb();

$go_form = new NcaQuestionForm();

$api = new apiQuestionForm();

$cl_report = new NcaStatReportGenerator();

switch ($ar_prm["method"]) {
    case "getQuestionList":
        echo $api->api_getQuestionList($ar_prm["category"],$ar_prm["section"]);
        break;
    case 'generateQuestion':
        echo $api->generateQuestion($ar_prm["category"],$ar_prm["section"],$ar_prm["qset"]);
        break;
    case 'getQuestionNameAndIdByFilter':
        echo $api->getQuestionNameAndIdByFilter($ar_prm["category"],$ar_prm["section"],$ar_prm["qset"]);
        break;
    case "getDepSecFormCategory":
        echo $api->getDepSecFormCategory($ar_prm["section"]);
        break;
    case "saveAnswer":
        echo $api->saveAnswer($ar_prm);
        break;
    case "generateSummaryReport":
        echo $cl_report->generateSummaryReport($ar_prm["startDate"],$ar_prm["endDate"]);
        break;
    case "generateDepSecReport":
        echo $cl_report->generateDepSecReport($ar_prm["section"],$ar_prm["startDate"],$ar_prm["endDate"]);
        break;
    case "generateTopStatOfDepSecReport":
        echo $cl_report->generateTopStatOfDepSecReport($ar_prm["startDate"],$ar_prm["endDate"]);
        break;
    case "generateCountSumByDepSec":
        echo $cl_report->generateCountSumByDepSec($ar_prm["startDate"],$ar_prm["endDate"]);
        break;
    case "debugPrint":
        $api->debugPrint($ar_prm);
        break;
}

class apiQuestionForm{

    public function api_getQuestionList($category,$section){
        global $go_form;
        global $ar_prm;

        $result =  $go_form->getQuestionList($ar_prm["category"],$ar_prm["section"]);

        return json_encode($result);
    }

    public function generateQuestionFormJSON($ar_qlist){
        global $go_form;
        global $ar_prm;

        // print_r($ar_qlist);
        
        $ar_result = array();

        foreach ($ar_qlist as $key => $value) {
            $ar_result[$key];
            $ar_result[$key]["question_id"] = $value["question"];
            $ar_result[$key]["question_name"] = $value["question_name"];
            $ar_result[$key]["qestion_dt"] = $go_form->getQuestionDt($value["question"]);
        }

        return json_encode($ar_result);
    }

    public function getQuestionNameAndIdByFilter($category,$section){
        global $go_form;
        global $ar_prm;

        $result =  $go_form->getQuestionNameAndIdByFilter($ar_prm["category"],$ar_prm["section"]);

        return json_encode($result);
    }

    public function getDepSecFormCategory($depSec){
        global $go_form;

        $result =  $go_form->getDepSecFormCategory($depSec);

        return json_encode($result);
    }

    function generateQuestion($category,$section,$qset){
        global $go_form;

        $ar_qlist = $go_form->getQuestionList($category,$section,$qset);
        $ar_result = $this->generateQuestionFormJSON($ar_qlist);
        return $ar_result;
    }

    function saveAnswer($data){
        global $go_form;
        $exc = $go_form->saveAnswer($data);
        return $exc;
    }

    function debugPrint($ev){
        echo "<pre>";
        print_r($ev);
        echo "</pre>";
    }
}