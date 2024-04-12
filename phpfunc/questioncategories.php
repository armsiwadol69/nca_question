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

    $sql = "SELECT *  FROM tb_questioncategories WHERE (questioncategories_active = '1' AND questioncategories_compfunc = '".$ar_prm['staffcompfunc']."' ) OR questioncategories_default = 1";

    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        foreach ($result as $key => $value) {

            if($value['questioncategories_recspid'] > 0){
                
                if($value['questioncategories_modispid']){
                    $rec_id = $value['questioncategories_recspid'];
                }else{
                    $rec_id = $value['questioncategories_modispid'];
                }
                $sql = "SELECT staff_dspnm FROM staff WHERE staff = '".$rec_id."' ";
                $res = $go_ncadb->ncaretrieve($sql, "icms");
                $value['questioncategories_recname'] = $res[0]['staff_dspnm'];
            }
            if($value['questioncategories_compfunc'] > 0){
                $sqlmcompfunc = "SELECT m_compfunc_name_th FROM m_compfunc WHERE m_compfunc = '".$value['questioncategories_compfunc']."' ";
                $arr_mcompfunc = $go_ncadb->ncaretrieve($sqlmcompfunc, "icms");
                $value['questioncategories_compfuncname'] = $arr_mcompfunc[0]['m_compfunc_name_th'];
            }
            if($value['questioncategories_compfuncdep'] > 0){
                $sqlmcompfuncdep = "SELECT m_compfuncdep_name_th FROM m_compfuncdep WHERE m_compfuncdep = '".$value['questioncategories_compfuncdep']."' ";
                $arr_mcompfuncdep = $go_ncadb->ncaretrieve($sqlmcompfuncdep, "icms");
                $value['questioncategories_compfuncdepname'] = $arr_mcompfuncdep[0]['m_compfuncdep_name_th'];
            }

            if($value['questioncategories_modispid']){
                $value['questioncategories_recdatetime'] = $value['questioncategories_modidatetime'];
            }

            if($value['questioncategories_default'] == 1){
                $value['questioncategories_compfuncname'] = "-";
                $value['questioncategories_compfuncdepname'] = "-";
                $value['questioncategories_recname'] = "-";
            }

            $value['currrent_user'] = $_SESSION['userData']['stf'];
            $data[] = $value;
        }

        echo json_encode($ncaquestion->ncaArrayConverter($data));

    } else if (empty($data)) {

        return json_encode(array());

    }

}

if($ar_prm["method"] == "getlistquestioncategories"){

    $sql = "SELECT *  FROM tb_questioncategories WHERE questioncategories = '".$ar_prm["questioncategories"]."' ";
    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        echo json_encode($ncaquestion->ncaArrayConverter($result));

    } else if (empty($data)) {

        echo  json_encode(array());

    }
}

if($ar_prm["method"] == "editquestioncategories" || $ar_prm["method"] == "addquestioncategories"){
    
    $datetime = date('Y-m-d H:i:s');
    $data = array();
    
    $sqlInsertMquestiontype = new SqlBuilder();
    $sqlInsertMquestiontype->SetTableName("tb_questioncategories");
    $questionId = 0;
    $questionIddt = 0;
    $ii = 0;
    $sqlObj = null;
    $sqlObj[$ii++] = new TField("questioncategories_compfunc", iconv('utf-8', 'tis-620', $ar_prm['questioncategories_compfunc']), "string");
    $sqlObj[$ii++] = new TField("questioncategories_compfuncdep", iconv('utf-8', 'tis-620', $ar_prm['questioncategories_compfuncdep']), "string");
    $sqlObj[$ii++] = new TField("questioncategories_name", iconv('utf-8', 'tis-620', $ar_prm['questioncategories_name']), "string");
    $sqlObj[$ii++] = new TField("questioncategories_hidden", $ar_prm['questioncategories_hidden'], "string");
    $sqlObj[$ii++] = new TField("questioncategories_description", iconv('utf-8', 'tis-620', $ar_prm['questioncategories_description']), "string");

    if($ar_prm['questioncategories'] > 0){

        $sqlObj[$ii++] = new TField("questioncategories_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_modidatetime", $datetime, "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" questioncategories = '".$ar_prm['questioncategories']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

    }else{

        $sqlObj[$ii++] = new TField("questioncategories_recspid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_recdatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("questioncategories_active", "1", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $queryMquestiontype = $sqlInsertMquestiontype->InsertSql();

    }

    if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

        if($ar_prm['questioncategories'] > 0){
            $questioncategories = $ar_prm['questioncategories'];
        }else{
            $questioncategories = $go_ncadb->ncaGetInsId("question");
        }
        $info['mquestiontype'] = $questioncategories;

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

if($ar_prm["method"] == "deletequestioncategories"){

    if($ar_prm['questioncategories'] > 0){
    
        $datetime = date('Y-m-d H:i:s');
        $data = array();
        
        $sqlInsertMquestiontype = new SqlBuilder();
        $sqlInsertMquestiontype->SetTableName("tb_questioncategories");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;

        $sqlObj[$ii++] = new TField("questioncategories_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_modidatetime", $datetime, "string");
        $sqlObj[$ii++] = new TField("questioncategories_active", "0", "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" questioncategories = '".$ar_prm['questioncategories']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

        if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {

            if($ar_prm['questioncategories'] > 0){
                $questioncategories = $ar_prm['questioncategories'];
            }else{
                $questioncategories = $go_ncadb->ncaGetInsId("question");
            }
            $info['mquestiontype'] = $questioncategories;

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
