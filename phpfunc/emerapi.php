<?
header("Content-type: application/json; charset=utf-8");
$gb_notlogin = true;
require "../include.inc.php";

$go_ncadb = new ncadb();

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

function ArrayKeyRemover($par_array){
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

$selectDepartment = $ar_prm["selectDepartment"];
$selectCategory = $ar_prm["selectCategory"];
$selectFormType = $ar_prm["selectFormType"];

$whereD = "";
$whereC = "";
$whereF = "";

if(!empty($selectDepartment)){
    $whereD = "AND tb_question.question_compfuncdepsec = '$selectDepartment'";
}

if(!empty($selectCategory)){
    $whereC = "AND tb_question.question_questioncategories = '$selectCategory'";
}

if(!empty($selectFormType)){
    $whereF = "AND tb_question.question_questionmode = '$selectFormType'";
}


if(empty($selectDepartment) && empty($selectCategory) && empty($selectFormType)){

}

$sql = "SELECT
	tb_question.*
    FROM
	tb_question
    WHERE
    1
    $whereD
    $whereC
    $whereF
	";

    // echo $sql;

$exc = $go_ncadb->ncaretrieve($sql, "question");

echo json_encode(ncaArrayConverter($exc));
