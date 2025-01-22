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
        if($datanswer){

            /* echo "<pre>";
            print_r($datanswer);
            echo "</pre>"; */

            $datanswerdt = $ncaanswer->getAnswerdt();
            $questioninfo = $ncaanswer->getDataQuestion();

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
            }else if($datanswer[0]['answer_type'] == 3){
                $arrAnswerinfo['answer_type'] = "สาขา";
            }else{
                $arrAnswerinfo['answer_type'] = "-";
            }

            $arrAnswerinfo['answer_recdatetime'] = $datanswer[0]['answer_recdatetime'];

            $staff = explode("|", $datanswer[0]['answer_remark']);

            $arrAnswerinfo['staff'] = $staff[0];

            $arrAnswerinfo['staff_code'] = $staff[1];

            $arrAnswerinfo['staff_name'] = $staff[2];

            /* echo "<pre>";
            print_r($datanswerdt);
            echo "</pre>"; */

            if($datanswerdt){

                $arr_parent = array();
                $htmlQuestion = "";
                $ii = 0;
                foreach($datanswerdt AS $key => $val){
                    // if(!$val['questiondt_parent']){
                        $ii++;
                        $htmlQuestion  .= "II : ".$ii." ".$ncaanswer->genareteViewAnswerFormData("answerdt_questiondt",$val['answerdt_questiondt'],0,$arr_parent);
                    // }
                }
            }
        }

        $rtn = array(
            "resCode"    => "1",
            "resMsg"     => "Successfully",
            "params"     => $post,
            "datainfo"   => $arrAnswerinfo,
            "dataAnswer" => $datanswer,
            "datahtml"   => $htmlQuestion,
        );

        return json_encode($rtn);

    }

}
