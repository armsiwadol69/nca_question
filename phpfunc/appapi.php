<?
//THIS IN FILE THAT CONTAINS API FOR NCA APP
//SIWADOl M.
//TOTAL TIME SPENT HERE : 0 HRS

error_reporting(E_ERROR | E_PARSE);
date_default_timezone_set("Asia/Bangkok");
header('Content-Type: application/json; charset=utf-8');

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

$apiObj = new ncaAppApi();


//START SWITCH CASE

switch ($ar_prm["method"]) {
    case 'getQuestionCategory':
        echo $apiObj->getQuestionCategory();
        break;
    case 'getQuestionGroup':
        echo $apiObj->getQuestionGroup($ar_prm["q_category"]);
        break;
    case 'getQuestionList':
        echo $apiObj->getQuestionList($ar_prm["q_category"],$ar_prm["q_group"],$ar_prm["q_type"]);
        break;
    default:
        echo $apiObj->defualtReturn();
        break;
}

class ncaAppApi {

    public function defualtReturn(){

        $rtn = array(
            "resCode" => "99",
            "resMsg" => "No Method"
        );

        return json_encode($rtn);
    }

    public function getQuestionCategory(){

        $debug = 0;

        global $go_ncadb;

        $sql = "SELECT tb_questioncategories.* FROM tb_questioncategories WHERE tb_questioncategories.questioncategories_active = '1';";

        $resutlArray = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sql, "question"));

        if($debug){
            echo "<pre>";
            print_r($resutlArray);
            echo "</pre>";
        }

        return json_encode($resutlArray);

    }

    public function getQuestionGroup($questionCate){
        
        $debug = 0;

        global $go_ncadb;

        $sql = "SELECT tb_questiongroup.* FROM tb_questiongroup WHERE questiongroup_questioncategories = '$questionCate'";

        $resutlArray = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sql, "question"));

        if($debug){
            echo "<pre>";
            print_r($resutlArray);
            echo "</pre>";
        }

        return json_encode($resutlArray);
    }

    public function getQuestionList($q_category, $q_group, $q_type){
        
        $debug = 0;

        global $go_ncadb;

        if($q_category  != "0"){
            $category = "AND tb_question.question_questioncategories = '$q_category' ";
        }else{
            $category = "";
        }
        
        if($q_group != "0"){
            $group = "AND tb_question.question_questioncategroup = '$q_group' ";
        }else{
            $group = "";
        }

        if($q_type != "0"){
            $type = "AND tb_question.question_questionmode = '$q_type'";
        }else{
            $type = "";
        }

        $sql = "SELECT
        tb_question.*
        FROM
        tb_question
        WHERE
        tb_question.question_active = '1' 
        $category
        $group
        $type
        ;";

        $resutlArray = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sql, "question"));

        return json_encode($resutlArray);
    }

    function ncaArrayConverter($par_array){
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
}