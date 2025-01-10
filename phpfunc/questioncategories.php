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

    if($ar_prm['search']['value'] != ""){
        $textSearch = $ar_prm['search']['value'];
        $serach = " AND questioncategories_name LIKE '%".$textSearch."%' ";
    }

    if($ar_prm['length']){
        $limit = "LIMIT ".$ar_prm['start'].",".$ar_prm['length'];
    }

    $sql = "SELECT 
                *
            FROM tb_questioncategories 
            WHERE 
                questioncategories_compfunc = '".$_SESSION['userData']['staffcompfunc']."' 
                AND questioncategories_compfuncdep = '".$_SESSION['userData']['staffcompfuncdep']."' 
                AND questioncategories_compfuncdepsec = '".$_SESSION['userData']['staffcompfuncdepsec']."' 
                AND questioncategories_active = 1 ". 
                $serach.
                $limit;

    $result = $go_ncadb->ncaretrieve($sql, "question");

    $sqlCount = "   SELECT 
                        COUNT(*) AS count
                    FROM tb_questioncategories 
                    WHERE 
                        questioncategories_compfunc = '".$_SESSION['userData']['staffcompfunc']."' 
                        AND questioncategories_compfuncdep = '".$_SESSION['userData']['staffcompfuncdep']."' 
                        AND questioncategories_compfuncdepsec = '".$_SESSION['userData']['staffcompfuncdepsec']."' 
                        AND questioncategories_active = 1 
                        ";

    $resultCount = $go_ncadb->ncaretrieve($sqlCount, "question");
    $data = array();
    if(count($result) > 0){

        $arrCompfunc = array();
        $arrcompfunc = $ncaquestion->getCompfuncData();
        if($arrcompfunc['respCode'] == "1"){
            $compfun = $arrcompfunc['data'];
            foreach ($compfun as $key1 => $value1) {
                $arrCompfunc[$value1['compfunc_id']] = $value1;
            }
        }

        $arrCompfuncdep = array();
        $arrcompfuncdep = $ncaquestion->getDepartmentData();
        if($arrcompfuncdep['respCode'] == "1"){
            $compfuncdep = $arrcompfuncdep['data'];
            foreach ($compfuncdep as $key1 => $value1) {
                $arrCompfuncdep[$value1['department_id']] = $value1;
            }
        }

        $arrCompfuncdepsec = array();
        $arrcompfuncdepsec = $ncaquestion->getSectionData();
        if($arrcompfuncdepsec['respCode'] == "1"){
            $compfuncdepsec = $arrcompfuncdepsec['data'];
            foreach ($compfuncdepsec as $key1 => $value1) {
                $arrCompfuncdepsec[$value1['section_id']] = $value1;
            }
        }

        $arr_staff = array();
        foreach ($result as $key => $value) {

            if($value['questioncategories_modispid']){
                $value['questioncategories_username'] = $value['questioncategories_modiname'];
                $value['questioncategories_userdatetime'] = $value['questioncategories_modidatetime'];
            }else{
                $value['questioncategories_username'] = $value['questioncategories_recanme'];
                $value['questioncategories_userdatetime'] = $value['questioncategories_recdatetime'];
            }

            if($value['questioncategories_default'] == 1){
                $value['questioncategories_compfuncname'] = "-";
                $value['questioncategories_compfuncdepname'] = "-";
                $value['questioncategories_compfuncdepsecname'] = "-";
                $value['questioncategories_recname'] = "-";
            }else{
                $value['questioncategories_compfuncname'] = $arrCompfunc[$value['questioncategories_compfunc']]['compfunc_name'];
                $value['questioncategories_compfuncdepname'] =  $arrCompfuncdep[$value['questioncategories_compfuncdep']]['department_name'];
                $value['questioncategories_compfuncdepsecname'] =  $arrCompfuncdepsec[$value['questioncategories_compfuncdepsec']]['section_name'];
            }

            $value['currrent_user'] = $_SESSION['userData']['stf'];
            $data[] = $value;
        }

        $rtn = array(
            "resCode" => "1",
            "resMsg" => "Successfully",
            "params" => $ar_prm,
            "draw" => $ar_prm['draw'],
            "recordsTotal" => $resultCount[0]["count"],
            "data" => $data,
            "recordsFiltered" => $resultCount[0]["count"],
            "sql" => $sql,
            "sqlCount" => $sqlCount,
        );

        echo json_encode($rtn);

    } else if (empty($data)) {

        return json_encode(array());

    }

}

if($ar_prm["method"] == "getlistquestioncategories"){

    $sql = "SELECT *  FROM tb_questioncategories WHERE questioncategories = '".$ar_prm["questioncategories"]."' ";
    $result = $go_ncadb->ncaretrieve($sql, "question");
    $data = array();
    if(count($result) > 0){

        echo json_encode($result);

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

    $sqlObj[$ii++] = new TField("questioncategories_name", $ar_prm['questioncategories_name'], "string");
    $sqlObj[$ii++] = new TField("questioncategories_description", $ar_prm['questioncategories_description'], "string");
    $sqlObj[$ii++] = new TField("questioncategories_hidden", $ar_prm['questioncategories_hidden'], "string");

    if($ar_prm["method"] == "addquestioncategories"){
        $sqlObj[$ii++] = new TField("questioncategories_compfunc", $ar_prm['questioncategories_compfunc'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_compfuncdep", $ar_prm['questioncategories_compfuncdep'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_compfuncdepsec", $ar_prm['questioncategories_compfuncdepsec'], "string");
    }
    
    if($ar_prm['questioncategories'] > 0){

        $sqlObj[$ii++] = new TField("questioncategories_modispid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_modispcode", $_SESSION['userData']['staffcd'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_modiname", $_SESSION['userData']['userdspms'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_modidatetime", $datetime, "string");

        $sqlInsertMquestiontype->SetField($sqlObj);
        $sqlInsertMquestiontype->SetWhereClause(" questioncategories = '".$ar_prm['questioncategories']."'");
        $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();

    }else{

        $sqlObj[$ii++] = new TField("questioncategories_recspid", $_SESSION['userData']['stf'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_recspcode", $_SESSION['userData']['staffcd'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_recanme", $_SESSION['userData']['userdspms'], "string");
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
        $sqlObj[$ii++] = new TField("questioncategories_modispcode", $_SESSION['userData']['staffcd'], "string");
        $sqlObj[$ii++] = new TField("questioncategories_modiname", $_SESSION['userData']['userdspms'], "string");
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
