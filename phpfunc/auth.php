<?php
session_start();
$gb_notlogin = true;
require "../include.inc.php";
require "customfunction.php";

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

$get_username = ncaencode(ncaiconvutf8($par_username, "de"), "en");
$get_password = ncaencode(ncaiconvutf8($par_password, "de"), "en");

$sql = "SELECT * FROM usr
        WHERE usr_username = '$get_username' AND
        usr_password = '$get_password';";

$user_infomation = $go_ncadb->ncaretrieve($sql, "icms");

if (!empty($user_infomation)) {
    $userInfo = convertArrayToUtf8($user_infomation[0]);
    $_SESSION['userData'] = array(
        'stf' => $userInfo['stf'],
        'staffId' => $userInfo['usr_stfcd'],
        'userdspms' => $userInfo['usr_stfdspnm'],
        'active' => $userInfo['usr_active'],
        'loginTime' => time(),
    );

    if ($debug) {
        print_r($_SESSION['userData']);
    }
    // header('location: ../view/v_summary.php');
    header('location: ../view/list_question.php');
} else {
    header('location: ../view/v_login.php?loginrtn=1');
}

// echo "<pre>";
// echo print_r($user_infomation[0]);
// echo '----------------------------------------------------------------';
// print_r(convertArrayToUtf8($user_infomation[0]));
// echo "</pre>";
