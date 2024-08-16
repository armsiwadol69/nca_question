<?php

class NcaQuestionForm {

    function __construct($id=0) {

    }

    function getQuestionList($category = '0', $depsec = '0' , $qset = "0") {
        global $go_ncadb;

        $wherebyqset = "";

        if($qset != "0"){
            $wherebyqset = "AND NQ.question = '".$qset."'";
        }

        $sql = "SELECT
	            NQ.*
                FROM nca_question.tb_question AS NQ
                WHERE
                1 AND
                NQ.question_active = '1' AND
                NQ.question_questioncategories = '".$category."' 
                AND NQ.question_compfuncdepsec = '".$depsec."'
                $wherebyqset";

        $arr = $go_ncadb->ncaretrieve($sql, "question");

        return $this->ArrayKeyRemover($arr);
    }

    function getQuestionDt($qId){
        global $go_ncadb;

        $sql = "SELECT *
                FROM nca_question.tb_questiondt AS QDT
                WHERE QDT.questiondt_question = '".$qId."'";

        $arr = $go_ncadb->ncaretrieve($sql, "question");
        
        return $this->ArrayKeyRemover($arr);
    }

    function getDepSecFormCategory($depSec){
        global $go_ncadb;

        $sql = "SELECT 
                QCG.questioncategories AS 'id',
                QCG.questioncategories_name AS 'c_name'
                FROM tb_questioncategories AS QCG
                WHERE
                QCG.questioncategories_active = '1' AND
                QCG.questioncategories_compfuncdepsec = '".$depSec."'";

        $arr = $go_ncadb->ncaretrieve($sql, "question");

        return $this->ArrayKeyRemover($arr);
    }

    function getQuestionNameAndIdByFilter($category = '0', $depsec = '0'){
        global $go_ncadb;

        $sql = "SELECT
	            NQ.question AS 'id',
                NQ.question_name AS 'name'
                FROM nca_question.tb_question AS NQ
                WHERE
                1 AND
                NQ.question_active = '1' AND
                NQ.question_questioncategories = '".$category."' 
                AND NQ.question_compfuncdepsec = '".$depsec."';";

        $arr = $go_ncadb->ncaretrieve($sql, "question");

        return $this->ArrayKeyRemover($arr);
    }

    function saveAnswer($data){
        global $go_ncadb;

        $saveParent = $this->saveAnswerParentIntoStatAnswer($data);
        
        if(!$saveParent["status"]){
            $go_ncadb->ncaRollback("question");
            $ar_rtn = array(
                "resCode" => 0,
                "resMsg" => $saveParent["msg"]
            );

            return json_encode($ar_rtn);
        }

        $parentId = $saveParent["id"];

        $saveChild = $this->saveStatChlidAnswer($data, $parentId, $data["empid"], $data["empcode"], $data["empname"]);

        if(!$saveChild){
            $ar_rtn = array(
                "resCode" => "0",
                "resMsg" => "Failed to save child answerdt"
            );

            return json_encode($ar_rtn);
        }else{

            $ar_rtn = array(
                "resCode" => "1",
                "resMsg" => "Success"
            );

            return json_encode($ar_rtn);
        }
        
    }

    function saveAnswerParentIntoStatAnswer($data){
        global $go_ncadb;
        $g_date = date("Y-m-d");
        $g_datetime = date("Y-m-d H:i:s");

        $sqlInsert = new SqlBuilder();
        $sqlInsert->SetTableName("tb_statanswer");
        $ii = 0;
        $sqlObj = null;
        $sqlObj[$ii++] = new TField("statanswer_date", $g_date, "string");
        $sqlObj[$ii++] = new TField("statanswer_recspid", $data["empid"], "string");
        $sqlObj[$ii++] = new TField("statanswer_recdsnm", $data["empname"], "string");
        $sqlObj[$ii++] = new TField("statanswer_recspcode", $data["empcode"], "string");
        $sqlObj[$ii++] = new TField("statanswer_recdatetime", $g_datetime, "string");

        $sqlInsert->SetField($sqlObj);
        $query = $sqlInsert->InsertSql();

        $exc = $go_ncadb->ncaexec($query, "question");

        if ($exc) {
            $answerMasterJJId = $go_ncadb->ncaGetInsId("question");
            return array("status" => true, "msg" => "Success" , "id" => $answerMasterJJId);
        }else{
            return array("status" => false, "msg" => "Failed" , "id" => "");
        }

    }

    function saveStatChlidAnswer($dt , $parentId, $empid, $empcode,$empname){
        global $go_ncadb;
        $g_date = date("Y-m-d");
        $g_datetime = date("Y-m-d H:i:s");

        foreach ($dt["q"]["answer"] as $rk => $rv) {

            $qsetId = $rk;

            foreach ($rv as $rk_b => $rv_b) {
 
                $dtId = $rk_b;
                $dtCount = $rv_b;

                $sqlInsert = new SqlBuilder();
                $sqlInsert->SetTableName("tb_statanswerdt");
                $ii = 0;
                $sqlObj = null;
                $sqlObj[$ii++] = new TField("statanswerdt_statanswer", $parentId, "numeric");
                $sqlObj[$ii++] = new TField("statanswerdt_question", $qsetId, "numeric");
                $sqlObj[$ii++] = new TField("statanswerdt_questiondt", $dtId, "numeric");
                $sqlObj[$ii++] = new TField("statanswerdt_count", $dtCount, "numeric");
                $sqlObj[$ii++] = new TField("statanswerdt_date", $g_date, "string");
                $sqlObj[$ii++] = new TField("statanswerdt_recspid", $empid, "numeric");
                $sqlObj[$ii++] = new TField("statanswerdt_recspcode", $empcode, "string");
                $sqlObj[$ii++] = new TField("statanswerdt_recspnm", $empname, "string");
                $sqlObj[$ii++] = new TField("statanswerdt_recdatetime", $g_datetime, "string");


                $sqlInsert->SetField($sqlObj);
                $query = $sqlInsert->InsertSql();

                $exc = $go_ncadb->ncaexec($query, "question");

                if(!$exc){
                    $go_ncadb->ncaRollback("question");
                    return false;
                }
            }
        }

        return true;

    }

    public function ArrayKeyRemover($par_array){
        if (empty($par_array)) {
            return array();
        }
        $ar = array();
        foreach ($par_array as $key => $value) {
            $xx = array();
            foreach ($par_array[$key] as $k => $v) {
                if (is_int($k)) {
                    continue;
                }
                $xx[$k] = $v;
            }
            $ar[$key] = $xx;
        }
        return $ar;
    }

    
}