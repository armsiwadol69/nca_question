<?php
session_start();
$gb_notlogin = true;
require_once "../include.inc.php";
require_once "customfunction.php";
require_once ("../class/class.question.php");
header('Content-type: text/plain; charset=utf-8');

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

$debug = 0;

$go_ncadb = new ncadb();
$ncaquestion = new question();

$get_username = $par_username;
$get_password = $par_password;
$userLogin = $ncaquestion->checkUserLogin($get_username, $get_password);

if ($userLogin['resCode'] == "1") {

    $userInfo = $userLogin['ncaData'];
    $info = $ncaquestion->getEmpCode($userInfo['usr_stfcd']);
    if($info['respCode'] == '1'){
        $userInfo['m_compfunc']       = $info['data'][0]['emp_func'];
        $userInfo['m_compfuncdep']    = $info['data'][0]['emp_dep'];
        $userInfo['m_compfuncdepsec'] = $info['data'][0]['emp_sec'];
    }

    $_SESSION['userData'] = array(
        'stf' => $userInfo['usr'],
        'staffId' => $userInfo['usr'],
        'userdspms' => $userInfo['usr_stfdspnm'],
        'loginTime' => time(),
        'staffcomp' => "1",
        'staffcompfunc' => $userInfo['m_compfunc'],
        'staffcompfuncdep' => $userInfo['m_compfuncdep'],
        'staffcompfuncdepsec' => $userInfo['m_compfuncdepsec'],
    );

    header('location: ../view/list_questionbygroup.php');
} else {
    header('location: ../view/v_login.php?loginrtn=1');
}
