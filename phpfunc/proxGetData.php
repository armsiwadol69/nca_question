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



// echo "<pre>";
// print_r($data);
// die("-----");
switch ($method) {
    case 'getCompfunc';
        echo json_encode($ncaquestion->getCompfuncData());
        break;
    case 'getDepartment';
        echo json_encode($ncaquestion->getDepartmentData($ar_prm["par_compfuncid"]));
        break;
    case 'getSection';
        echo json_encode($ncaquestion->getSectionData($ar_prm["par_departmentid"]));
        break;
    default:
        echo json_encode(array("resCode" => "99", "resMsg" => "No Method was specified"));
        break;
}