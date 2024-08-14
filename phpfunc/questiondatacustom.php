<?php
date_default_timezone_set("Asia/Bangkok");
session_start();
$gb_notlogin = true;
require "../include.inc.php";
require "customfunction.php";
require_once ("../class/class.question.php");

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
$ncaquestion = new question();

$debug = 0;
if ($debug) {
    echo '<pre>';
    print_r($ar_prm);
    echo '</pre>';
}
header('Content-Type: application/json; charset=utf-8');
$apiCalling = new ncaapicalling();

switch ($ar_prm["method"]) {
    case "getQuestionList":
        echo $apiCalling->getQuestionList($ar_prm);
        break;
    // case "getItemList":
    //     echo $apiCalling->getItemList($ar_prm["par_id"], $ar_prm["offset"], $ar_prm["limit"], $ar_prm["draw"], $ar_prm["giftType"], $ar_prm["search"]);
    //     break;
    

}

class ncaapicalling
{
    //Thanks to p'JJ aka MASTER'JJ
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

    public function getQuestionList($post)
    {   

        global $go_ncadb;
        $ncaquestion = new question();
        if($post['search']){
            $textSearch = $post['search']['value'];
            $serach = " AND Q.question_name LIKE '%".iconv('utf-8', 'tis-620', $textSearch)."%' ";
        }

        $sqlCount = "SELECT COUNT(Q.question) AS count
                FROM tb_question AS Q
                LEFT JOIN tb_questioncategories AS QC ON (QC.questioncategories=Q.question_questioncategories)
                LEFT JOIN tb_questionmode AS QM ON (QM.questionmode=Q.question_questionmode)
                LEFT JOIN tb_questiongroup AS QG ON (QG.questiongroup=Q.question_questioncategroup)
                WHERE 
                    Q.question_active = '1' 
                    -- AND Q.question_compfunc = '".$_SESSION['userData']['staffcompfunc']."'
                    ".$serach."
                ORDER BY Q.question_name ASC";
        $resultCount = $go_ncadb->ncaretrieve($sqlCount, "question");

        $sql_data = "SELECT Q.*, QM.questionmode_name, QG.questiongroup_name, QC.questioncategories_name
                FROM tb_question AS Q
                LEFT JOIN tb_questioncategories AS QC ON (QC.questioncategories=Q.question_questioncategories)
                LEFT JOIN tb_questionmode AS QM ON (QM.questionmode=Q.question_questionmode)
                LEFT JOIN tb_questiongroup AS QG ON (QG.questiongroup=Q.question_questioncategroup)
                WHERE 
                    Q.question_active = '1' 
                    -- AND Q.question_compfunc = '".$_SESSION['userData']['staffcompfunc']."'
                    ".$serach."
  
                ORDER BY Q.question_name ASC";
        /* $sql_data = "SELECT Q.*, QM.questionmode_name, QG.questiongroup_name, QC.questioncategories_name
                FROM tb_question AS Q
                LEFT JOIN tb_questioncategories AS QC ON (QC.questioncategories=Q.question_questioncategories)
                LEFT JOIN tb_questionmode AS QM ON (QM.questionmode=Q.question_questionmode)
                LEFT JOIN tb_questiongroup AS QG ON (QG.questiongroup=Q.question_questioncategroup)
                WHERE 
                    Q.question_active = '1' 
                    -- AND Q.question_compfunc = '".$_SESSION['userData']['staffcompfunc']."'
                    ".$serach."
  
                ORDER BY Q.question_name ASC LIMIT ".$post['start'].",".$post['length']; */

       

        $result = $go_ncadb->ncaretrieve($sql_data, "question");
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

           /*  echo "<pre>";
            print_r($arrCompfunc);
            print_r($arrCompfuncdep);
            print_r($arrCompfuncdepsec);
            echo "</pre>"; */

            foreach ($result as $key => $value) {

                /* echo "<pre>";
            print_r($value);
            echo "</pre>";
            die(); */

                if($value['question_recspid'] > 0){
                    $sql = "SELECT staff_dspnm FROM staff WHERE staff = '".$value['question_recspid']."' ";
                    $res = $go_ncadb->ncaretrieve($sql, "icms");
                    $value['question_recname'] = $res[0]['staff_dspnm'];
                }

                $value['question_compfuncname'] = $arrCompfunc[$value['question_compfunc']]['compfunc_name'];
                $value['question_compfuncdepname'] =  $arrCompfuncdep[$value['question_compfuncdep']]['department_name'];
                $value['question_compfuncdepsecname'] =  $arrCompfuncdepsec[$value['question_compfuncdepsec']]['section_name'];

                if($value['question_questioncategories'] > 0){
                    $sqlmquestiontype = "SELECT questioncategories_name FROM tb_questioncategories WHERE questioncategories = '".$value['question_questioncategories']."' ";
                    $arr_mquestiontype= $go_ncadb->ncaretrieve($sqlmquestiontype, "question");;
                    $value['question_questioncategoriesname'] = $arr_mquestiontype[0]['questioncategories_name'];
                }

                if($value['question_modispid']){

                    $value['question_recspid'] = $value['question_modispid'];
                    $value['question_recdatetime'] = $value['question_modidatetime'];
    
                }
                $value['currrent_user'] = $_SESSION['userData']['stf'];
                $data[] = $value;
            }
        }


        $rtn = array(
            "resCode" => "1",
            "resMsg" => "Successfully",
            "params" => $post,
            "draw" => $post['draw'],
            "recordsTotal" => $resultCount[0]["count"],
            "data" => $data,
            "recordsFiltered" => $resultCount[0]["count"],
            "sql" => $sql_data,
            "sqlCount" => $sqlCount,
        );

        return json_encode($rtn);

    }

}
