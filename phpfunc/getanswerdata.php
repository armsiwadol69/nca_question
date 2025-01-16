<?php
date_default_timezone_set("Asia/Bangkok");
session_start();
$gb_notlogin = true;
require "../include.inc.php";
require "customfunction.php";
require_once ("../class/class.question.php");
require_once ("../class/class.answer.php");

// $method = $_GET["method"];

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

$go_ncadb = new ncadb();

$debug = 0;
if ($debug) {
    echo '<pre>';
    print_r($ar_prm);
    echo '</pre>';
}
// header('Content-Type: application/json; charset=utf-8');
$apiCalling = new ncaapicalling();

switch ($ar_prm["method"]) {
    case "getDataAnswerQuestion":
        echo $apiCalling->getAnswerData($ar_prm);
        break;
}

class ncaapicalling
{
    public function ncaArrayConverter($par_array)
    {
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
                $xx[$k] = iconv('tis-620', 'utf-8', $v);
            }
            $ar[$key] = $xx;
        }
        return $ar;
    }

    public function areDatesInDifferentWeeks(DateTime $date1, DateTime $date2)
    {
        // Set Sunday as the first day of the week
        $customWeekStart = 0;
    
        // Adjust the days based on the custom week start
        $dayDiff1 = ($date1->format('w') - $customWeekStart + 7) % 7;
        $dayDiff2 = ($date2->format('w') - $customWeekStart + 7) % 7;
    
        // Calculate the week number
        $weekNumber1 = floor(($date1->format('z') - $dayDiff1) / 7) + 1;
        $weekNumber2 = floor(($date2->format('z') - $dayDiff2) / 7) + 1;
    
        return ($date1->format('Y') != $date2->format('Y')) || ($weekNumber1 != $weekNumber2);
    }

    public function getAnswerData($post)
    {   

        global $go_ncadb;
        $ncaanswer = new answer($post['question'],$post['answer']);

        
        $arrAnswerinfo = array();
        $datanswer = $ncaanswer->getDataAnswer();
        $questioninfo = $ncaanswer->getDataQuestion();
        // $datanswerdt = $ncaanswer->getAnswerdt();

        /* echo "<pre>";
        print_r($datanswer);
        echo "</pre>"; */

        $arrCompfunc = array();
        $arrcompfunc = $ncaanswer->getCompfuncData();
        $compfuncname = "";
        if($arrcompfunc['respCode'] == "1"){
            $compfun = $arrcompfunc['data'];
            foreach ($compfun as $key1 => $value1) {
                if($datanswer[0]['answer_compfunc'] == $value1['compfunc_id']){
                    $compfuncname = $value1['compfunc_name'];
                }
            }
        }
        $arrAnswerinfo['compfuncname'] = $compfuncname;

        $compfuncdepname = "";
        $arrCompfuncdep = array();
        $arrcompfuncdep = $ncaanswer->getDepartmentData($datanswer[0]['answer_compfunc']);
        if($arrcompfuncdep['respCode'] == "1"){
            $compfuncdep = $arrcompfuncdep['data'];
            
            foreach ($compfuncdep as $key1 => $value1) {
                if($datanswer[0]['answer_compfuncdep'] == $value1['department_id']){
                    $compfuncdepname  = $value1['department_name'];
                }
            }
        }
        $arrAnswerinfo['compfuncdepname'] = $compfuncdepname;

        $compfuncdepsecname = "";
        $arrCompfuncdepsec = array();
        $arrcompfuncdepsec = $ncaanswer->getSectionData($datanswer[0]['answer_compfuncdep']);
        if($arrcompfuncdepsec['respCode'] == "1"){
            $compfuncdepsec = $arrcompfuncdepsec['data'];
            foreach ($compfuncdepsec as $key1 => $value1) {
                if($datanswer[0]['answer_compfuncdepsec'] == $value1['section_id']){
                    $compfuncdepsecname  = $value1['section_name'];
                }
            }
        }
        $arrAnswerinfo['compfuncdepsecname'] = $compfuncdepsecname;

        $mquestiontype = $datanswer[0]['answer_questioncategories'];
        $questiongroup = $datanswer[0]['answer_questioncategroup'];
        $questionmode  = $datanswer[0]['answer_questionmode'];

        $sqlmquestiontype = "SELECT * FROM tb_questioncategories WHERE questioncategories = '".$mquestiontype."' ";
        $arrmquestiontype = $go_ncadb->ncaretrieve($sqlmquestiontype, "question");
        
        $sqlquestiongroup  = "SELECT * FROM tb_questiongroup WHERE questiongroup = '".$questiongroup."'";
        $arr_questiongroup = $go_ncadb->ncaretrieve($sqlquestiongroup, "question");
        $arrquestiongroup  = $ncaanswer->ncaArrayConverter($arr_questiongroup);

        $sqlquestionmode  = "SELECT * FROM tb_questionmode WHERE questionmode = '".$questionmode."'";
        $arr_questionmode = $go_ncadb->ncaretrieve($sqlquestionmode, "question");
        $arrquestionmode  = $ncaanswer->ncaArrayConverter($arr_questionmode);

        $arrAnswerinfo['questioncategories_name'] = $arrmquestiontype[0]['questioncategories_name'];
        $arrAnswerinfo['questiongroup_name']      = $arrquestiongroup[0]['questiongroup_name'];
        $arrAnswerinfo['questionmode_name']       = $arrquestionmode[0]['questionmode_name'];
        $arrAnswerinfo['question_name']           = $questioninfo[0]['question_name'];
        $arrAnswerinfo['question_detail']         = $questioninfo[0]['question_detail'];

        if($datanswer[0]['answer_type'] == 1){
            $arrAnswerinfo['answer_type'] = "คน";
        }else if($datanswer[0]['answer_type'] == 2){
            $$arrAnswerinfo['answer_type'] = "รถ";
        }else{
            $arrAnswerinfo['answer_type'] = "สาขา";
        }

        $arrAnswerinfo['answer_recdatetime'] = $datanswer[0]['answer_recdatetime'];

        $staff = explode("|", $datanswer[0]['answer_remark']);

        $arrAnswerinfo['staff'] = $staff[0];

        $arrAnswerinfo['staff_code'] = $staff[1];

        $arrAnswerinfo['staff_name'] = $staff[2];

        if($questioninfo){


            $arr_parent = array();
            $htmlQuestion = "";
            $formId = $questioninfo[0]["question"];
            $formName = $questioninfo[0]["question_name"];
            $formDes = $questioninfo[0]["question_detail"];
            foreach($questioninfo AS $key => $val){
                if(!$val['questiondt_parent']){
                    $htmlQuestion  .= $ncaanswer->genareteViewAnswerFormData("questiondt",$val['questiondt'],0,$arr_parent);
                }
            }
        }

        
        // if($post['search']['value']){
        //     $textSearch = $post['search']['value'];
        //     $serach = " AND Q.question_name LIKE '%".iconv('utf-8', 'tis-620', $textSearch)."%' ";
        // }

        // $sqlCount = "SELECT COUNT(A.answer) AS count
        //             FROM tb_answer AS A

        //             WHERE 
        //                 A.answer_active = '1' 
        //             ORDER BY A.answer_recdatetime ASC";

        // $resultCount = $go_ncadb->ncaretrieve($sqlCount, "question");

        // $sql_data = "SELECT *
        //             FROM tb_answer AS A
        //                 LEFT JOIN tb_question AS Q ON (Q.question = A.answer_question)
        //                 LEFT JOIN tb_questionmode AS QM ON (QM.questionmode = Q.question_questionmode)
        //             WHERE 
        //                 A.answer_active = '1' 
        //             ORDER BY A.answer_recdatetime ASC LIMIT ".$post['start'].",".$post['length'];

       

        // $result = $go_ncadb->ncaretrieve($sql_data, "question");
        // $data = array();
        // if(count($result) > 0){

        //     // $arrCompfunc = array();
        //     // $arrcompfunc = $ncaquestion->getCompfuncData();
        //     // if($arrcompfunc['respCode'] == "1"){
        //     //     $compfun = $arrcompfunc['data'];
        //     //     foreach ($compfun as $key1 => $value1) {
        //     //         $arrCompfunc[$value1['compfunc_id']] = $value1;
        //     //     }
        //     // }

        //     // $arrCompfuncdep = array();
        //     // $arrcompfuncdep = $ncaquestion->getDepartmentData();
        //     // if($arrcompfuncdep['respCode'] == "1"){
        //     //     $compfuncdep = $arrcompfuncdep['data'];
        //     //     foreach ($compfuncdep as $key1 => $value1) {
        //     //         $arrCompfuncdep[$value1['department_id']] = $value1;
        //     //     }
        //     // }

        //     // $arrCompfuncdepsec = array();
        //     // $arrcompfuncdepsec = $ncaquestion->getSectionData();
        //     // if($arrcompfuncdepsec['respCode'] == "1"){
        //     //     $compfuncdepsec = $arrcompfuncdepsec['data'];
        //     //     foreach ($compfuncdepsec as $key1 => $value1) {
        //     //         $arrCompfuncdepsec[$value1['section_id']] = $value1;
        //     //     }
        //     // }

        //     foreach ($result as $key => $value) {

        //         if($value['answer_recspid'] > 0){
        //             $sql = "SELECT staff_dspnm FROM staff WHERE staff = '".$value['answer_recspid']."' ";
        //             $res = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sql, "icms"));
        //             $value['answer_recname'] = $res[0]['staff_dspnm'];
        //         }else{
        //             $value['answer_recname'] = "";
        //         }

        //         // $value['question_compfuncname'] = $arrCompfunc[$value['question_compfunc']]['compfunc_name'];
        //         // $value['question_compfuncdepname'] =  $arrCompfuncdep[$value['question_compfuncdep']]['department_name'];
        //         // $value['question_compfuncdepsecname'] =  $arrCompfuncdepsec[$value['question_compfuncdepsec']]['section_name'];

        //         /* if($value['question_modispid']){

        //             $value['answerdt_recspid'] = $value['answerdt_recspid'];
        //             $value['answerdt_recdatetime'] = $value['answerdt_recdatetime'];
    
        //         } */

        //         $value['answer_recspid'] = $value['answer_recspid'];
        //         $value['answer_recdatetime'] = $value['answer_recdatetime'];

        //         $value['currrent_user'] = $_SESSION['userData']['stf'];

        //         if($value['answer_type'] == "1"){

        //             $value['answer_type'] = "คน";
        //             $sql = "SELECT staff_dspnm FROM staff WHERE staff = '".$value['answer_recspid']."' ";
        //             $res1 = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sql, "icms"));
        //             $value['answer_ref'] = $res1[0]['staff_dspnm'];

        //         }else if($value['answer_type'] == "2"){

        //             $value['answer_type'] = "รถ";
        //             $value['answer_ref'] = $value['answer_busref'];

        //         }else if($value['answer_type'] == "3"){

        //             $value['answer_type'] = "สาขา";
        //             $value['answer_ref'] = "-";

        //         }else{

        //             $value['answer_type'] = "-";
        //             $value['answer_ref'] = "-";
                    
        //         }

        //         $data[] = $value;
        //     }
        // }


        $rtn = array(
            "resCode" => "1",
            "resMsg" => "Successfully",
            "params" => $post,
            "datainfo" => $arrAnswerinfo,
            "dataAnswer" => $datanswer,
            "datahtml" => $htmlQuestion,

        );

        return json_encode($rtn);

    }

}
