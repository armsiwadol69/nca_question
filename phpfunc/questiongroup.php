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

    $sql = "SELECT *  FROM tb_questiongroup AS QG LEFT JOIN tb_questioncategories AS QC ON (QC.questioncategories=QG.questiongroup_questioncategories)  WHERE QG.questiongroup_active = '1' AND QC.questioncategories_compfunc = '".$_SESSION['userData']['staffcompfunc']."' OR (QC.questioncategories_default = 1)";

    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        foreach ($result as $key => $value) {

            if($value['questiongroup_modispid']){
                $rec_id = $value['questiongroup_modispid'];
            }else{
                $rec_id = $value['questiongroup_recspid'];
            }

            $sql_staff = "SELECT staff_dspnm FROM staff WHERE staff = '".$rec_id."' ";
            $res_staff = $go_ncadb->ncaretrieve($sql_staff, "icms");
            $value['questiongroup_recname'] = $res_staff[0]['staff_dspnm'];
            

            /* if($value['questiongroup_questioncategories'] > 0){
                $sql = "SELECT questioncategories_name FROM tb_questioncategories WHERE questioncategories = '".$value['questiongroup_questioncategories']."' ";
                $res = $go_ncadb->ncaretrieve($sql, "question");
                $value['questiongroup_categoriesname'] = $res[0]['questioncategories_name'];
            } */
            if($value['questiongroup_modispid']){

                $value['questiongroup_recspid'] = $value['questiongroup_modispid'];
                $value['questiongroup_recdatetime'] = $value['questiongroup_modidatetime'];

            }
            $value['currrent_user'] = $_SESSION['userData']['stf'];
            $value['sql'] = $sql;
            $data[] = $value;
        }

        echo json_encode($ncaquestion->ncaArrayConverter($data));

    } else if (empty($data)) {

        return json_encode(array());

    }

}

if($ar_prm["method"] == "getlistquestiongroup"){

    $sql = "SELECT *  FROM tb_questiongroup WHERE questiongroup = '".$ar_prm["questiongroup"]."' ";

    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        echo json_encode($ncaquestion->ncaArrayConverter($result));

    } else if (empty($data)) {

        echo  json_encode(array());

    }
}

if($ar_prm["method"] == "editquestionegroup" || $ar_prm["method"] == "addquestionegroup"){
    
    $datetime = date('Y-m-d H:i:s');
    $data = array();
    
    $sqlInsertMquestiontype = new SqlBuilder();
    $sqlInsertMquestiontype->SetTableName("tb_questiongroup");
    $questionId = 0;
    $questionIddt = 0;
    $ii = 0;
    $sqlObj = null;

    $sqlObj[$ii++] = new TField("questiongroup_name", iconv('utf-8', 'tis-620', $ar_prm['questiongroup_name']), "string");
    $sqlObj[$ii++] = new TField("questiongroup_description", iconv('utf-8', 'tis-620', $ar_prm['questiongroup_description']), "string");
    $sqlObj[$ii++] = new TField("questiongroup_questioncategories", iconv('utf-8', 'tis-620', $ar_prm['questiongroup_questioncategories']), "string");
    $sqlObj[$ii++] = new TField("questiongroup_hidden", iconv('utf-8', 'tis-620', $ar_prm['questiongroup_hidden']), "string");

    if($ar_prm['questiongroup'] > 0){

        $sqlObj[$ii++] = new TField("questiongroup_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questiongroup_modidatetime", $datetime, "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" questiongroup = '".$ar_prm['questiongroup']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

    }else{

        $sqlObj[$ii++] = new TField("questiongroup_recspid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questiongroup_recdatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("questiongroup_active", "1", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $queryMquestiontype = $sqlInsertMquestiontype->InsertSql();

    }

    if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

        if($ar_prm['questiongroup'] > 0){
            $questiongroup = $ar_prm['questiongroup'];
        }else{
            $questiongroup = $go_ncadb->ncaGetInsId("question");
        }
        $info['mquestiontype'] = $questiongroup;

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

if($ar_prm["method"] == "deletequestiongroup"){

    if($ar_prm['questiongroup'] > 0){
    
        $datetime = date('Y-m-d H:i:s');
        $data = array();
        
        $sqlInsertMquestiontype = new SqlBuilder();
        $sqlInsertMquestiontype->SetTableName("tb_questiongroup");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;

        $sqlObj[$ii++] = new TField("questiongroup_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questiongroup_modidatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("questiongroup_active", "0", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" questiongroup = '".$ar_prm['questiongroup']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

        if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

            if($ar_prm['questiongroup'] > 0){
                $questiongroup = $ar_prm['questiongroup'];
            }else{
                $questiongroup = $go_ncadb->ncaGetInsId("question");
            }
            $info['mquestiontype'] = $questiongroup;

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
