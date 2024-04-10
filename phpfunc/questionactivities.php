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

    $sql = "SELECT *  FROM tb_activities WHERE activities_active = '1'";
    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        foreach ($result as $key => $value) {

            if($value['activities_modispid']){
                $rec_id = $value['activities_modispid'];
            }else{
                $rec_id = $value['activities_recspid'];
            }
            
            $sql = "SELECT staff_dspnm FROM staff WHERE staff = '".$rec_id."' ";
            $res = $go_ncadb->ncaretrieve($sql, "icms");
            $value['activities_recname'] = $res[0]['staff_dspnm'];
            
            if($value['activities_modispid']){
                $value['activities_recspid'] = $value['activities_modispid'];
                $value['activities_recdatetime'] = $value['activities_modidatetime'];
            }

            $value['currrent_user'] = $_SESSION['userData']['stf'];
            $data[] = $value;
        }

        echo json_encode($ncaquestion->ncaArrayConverter($data));

    } else if (empty($data)) {

        return json_encode(array());

    }

}

if($ar_prm["method"] == "getlistactivities"){

    $sql = "SELECT *  FROM tb_activities WHERE activities = '".$ar_prm["activities"]."' ";

    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        echo json_encode($ncaquestion->ncaArrayConverter($result));

    } else if (empty($data)) {

        echo  json_encode(array());

    }
}

if($ar_prm["method"] == "editquestionctivities" || $ar_prm["method"] == "addquestionctivities"){
    
    $datetime = date('Y-m-d H:i:s');
    $data = array();
    
    $sqlInsertMquestiontype = new SqlBuilder();
    $sqlInsertMquestiontype->SetTableName("tb_activities");
    $questionId = 0;
    $questionIddt = 0;
    $ii = 0;
    $sqlObj = null;

    $sqlObj[$ii++] = new TField("activities_name", iconv('utf-8', 'tis-620', $ar_prm['activities_name']), "string");
    $sqlObj[$ii++] = new TField("activities_description", iconv('utf-8', 'tis-620', $ar_prm['activities_description']), "string");

    if($ar_prm['activities'] > 0){

        $sqlObj[$ii++] = new TField("activities_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("activities_modidatetime", $datetime, "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" activities = '".$ar_prm['activities']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

    }else{

        $sqlObj[$ii++] = new TField("activities_recspid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("activities_recdatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("activities_active", "1", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $queryMquestiontype = $sqlInsertMquestiontype->InsertSql();

    }

    if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

        if($ar_prm['activities'] > 0){
            $activities = $ar_prm['activities'];
        }else{
            $activities = $go_ncadb->ncaGetInsId("question");
        }
        $info['mquestiontype'] = $activities;

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

if($ar_prm["method"] == "deleteactivities"){

    if($ar_prm['activities_id'] > 0){
    
        $datetime = date('Y-m-d H:i:s');
        $data = array();
        
        $sqlInsertMquestiontype = new SqlBuilder();
        $sqlInsertMquestiontype->SetTableName("tb_activities");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;

        $sqlObj[$ii++] = new TField("activities_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("activities_modidatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("activities_active", "0", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" activities = '".$ar_prm['activities_id']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

        if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

            if($ar_prm['activities'] > 0){
                $activities = $ar_prm['activities'];
            }else{
                $activities = $go_ncadb->ncaGetInsId("question");
            }
            $info['mquestiontype'] = $activities;

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
