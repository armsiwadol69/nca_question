<?php

date_default_timezone_set("Asia/Bangkok");
session_start();
$gb_notlogin = true;
require "../include.inc.php";
require "customfunction.php";
require_once ("../class/class.question.php");

$go_ncadb = new ncadb();
$ncaquestion = new question($_GET['id']);

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
if ($debug) {
    echo '<pre>';
    print_r($ar_prm);
    echo '</pre>';
}


if($ar_prm["method"] == "getlist"){

    $sql = "SELECT *  FROM tb_questionmode WHERE questionmode_active = '1'";

    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        foreach ($result as $key => $value) {

            if($value['questionmode_modispid']){
                $rec_id = $value['questionmode_modispid'];
            }else{
                $rec_id = $value['questionmode_recspid'];
            }
            
            $sql = "SELECT staff_dspnm FROM staff WHERE staff = '".$rec_id."' ";
            $res = $go_ncadb->ncaretrieve($sql, "icms");
            $value['questionmode_recname'] = $res[0]['staff_dspnm'];
            
            if($value['questionmode_modispid']){

                $value['questionmode_recspid'] = $value['questionmode_modispid'];
                $value['questionmode_recdatetime'] = $value['questionmode_modidatetime'];

            }
            $value['currrent_user'] = $_SESSION['userData']['stf'];
            $data[] = $value;
        }

        echo json_encode($ncaquestion->ncaArrayConverter($data));

    } else if (empty($data)) {

        return json_encode(array());

    }

}

if($ar_prm["method"] == "getlistquestionmode"){

    $sql = "SELECT *  FROM tb_questionmode WHERE questionmode = '".$ar_prm["questionmode"]."' ";

    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        echo json_encode($ncaquestion->ncaArrayConverter($result));

    } else if (empty($data)) {

        echo  json_encode(array());

    }
}

if($ar_prm["method"] == "editquestionemode" || $ar_prm["method"] == "addquestionemode"){
    
    $datetime = date('Y-m-d H:i:s');
    $data = array();
    
    $sqlInsertMquestiontype = new SqlBuilder();
    $sqlInsertMquestiontype->SetTableName("tb_questionmode");
    $questionId = 0;
    $questionIddt = 0;
    $ii = 0;
    $sqlObj = null;

    $sqlObj[$ii++] = new TField("questionmode_name", iconv('utf-8', 'tis-620', $ar_prm['questionmode_name']), "string");
    $sqlObj[$ii++] = new TField("questionmode_description", iconv('utf-8', 'tis-620', $ar_prm['questionmode_description']), "string");

    if($ar_prm['questionmode'] > 0){

        $sqlObj[$ii++] = new TField("questionmode_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questionmode_modidatetime", $datetime, "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" questionmode = '".$ar_prm['questionmode']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

    }else{

        $sqlObj[$ii++] = new TField("questionmode_recspid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questionmode_recdatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("questionmode_active", "1", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $queryMquestiontype = $sqlInsertMquestiontype->InsertSql();

    }

    if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

        if($ar_prm['questionmode'] > 0){
            $questionmode = $ar_prm['questionmode'];
        }else{
            $questionmode = $go_ncadb->ncaGetInsId("question");
        }
        $info['mquestiontype'] = $questionmode;

        $data['success'] = 1;
        $data['sql'] = $queryMquestiontype;
        $data['fail'] = 0;

    } else {
        $go_ncadb->ncarollback("question");
        $data['success'] = 0;
        $data['fail'] = 1;
        $data['sql'] = $queryMquestiontype;
        
    }

    echo json_encode(array($data));

}

if($ar_prm["method"] == "deletequestionmode"){

    if($ar_prm['questionmode'] > 0){
    
        $datetime = date('Y-m-d H:i:s');
        $data = array();
        
        $sqlInsertMquestiontype = new SqlBuilder();
        $sqlInsertMquestiontype->SetTableName("tb_questionmode");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;

        $sqlObj[$ii++] = new TField("questionmode_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questionmode_modidatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("questionmode_active", "0", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" questionmode = '".$ar_prm['questionmode']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

        if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

            if($ar_prm['questionmode'] > 0){
                $questionmode = $ar_prm['questionmode'];
            }else{
                $questionmode = $go_ncadb->ncaGetInsId("question");
            }
            $info['mquestiontype'] = $questionmode;

            $data['success'] = 1;
            $data['sql'] = $queryMquestiontype;
            $data['fail'] = 0;

        } else {
            $go_ncadb->ncarollback("question");
            $data['success'] = 0;
            $data['fail'] = 1;
            $data['sql'] = $queryMquestiontype;
            
        }

        echo json_encode(array($data)); 
    }

}
