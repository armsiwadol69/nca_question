<?php
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set("Asia/Bangkok");
$gb_notlogin = true;
require "../include.inc.php";

// echo "apiQuestion";

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

// print_r($ar_prm);

$apiKey = "";

$go_ncadb = new ncadb();

$question = new ncaQuestion();

$question->setQuestionId($ar_prm["id"]);

// print_r($question);

switch ($ar_prm["method"]) {
    case "getQuestionData":
        echo $question->getDataQuestion();
        break;
}

class ncaQuestion{
    public $id;
    public $name;
    public $question = array();

    function __construct($id=0) {
        if($id){
            $this->setQuestionId($id);
        }
    }

    function setQuestionId($id = 0) {
        $this->id = $id;
    }

    function getDataQuestion()
    {
        global $go_ncadb;
        $arr = array();

        #getQuestionData'
        $sql = "SELECT 
                    * 
                FROM tb_question AS Q
                LEFT JOIN tb_questiondt AS QDT 
                    ON(QDT.questiondt_question=Q.question)
                WHERE 
                    Q.question = '".$this->id."'
                ORDER BY QDT.questiondt ASC 
                ";
        $arr = $go_ncadb->ncaretrieve($sql, "question");

        
        $question = $this->ncaArrayConverter($arr);
        
        $formName = $question[0]["question_name"];
        $formDesc = $question[0]["question_detail"];

        // array_shift($question);
        $optionData =  $this->getDataOption($this->id);

        // print_r($question);
        // print_r($optionData);

        // echo json_encode($question);

        // exit();
        
        // foreach ($question as $key => $value) {
        //     print_r($value);
        //     foreach ($optionData as $key => $value) {
        //         $value;
        //     }
        // }
        // exit();

        $questions =  $question;
        $options =   $optionData;

        // Create an empty array to store combined data
$combined_data = array();

// Function to recursively find and add sub-questions and their options
function addSubQuestions(&$question, $allQuestions, $allOptions) {
    $sub_questions = array();

    foreach ($allQuestions as $q) {
        if ($q['questiondt_parent'] == $question['questiondt']) {
            $sub_question = $q;
            $sub_question['option'] = array();

            foreach ($allOptions as $option) {
                if ($option['questionoption_question'] == $sub_question['question'] && $option['questionoption_questiondt'] == $sub_question['questiondt']) {
                    $sub_question['option'][] = $option;
                }
            }

            addSubQuestions($sub_question, $allQuestions, $allOptions);

            $question['option'][] = $sub_question;
        }
    }
}

// Loop through each question
foreach ($questions as $question) {
    $question['option'] = array();

    foreach ($options as $option) {
        if ($option['questionoption_question'] == $question['question'] && $option['questionoption_questiondt'] == $question['questiondt']) {
            $question['option'][] = $option;
        }
    }

    addSubQuestions($question, $questions, $options);

    $combined_data[] = $question;
}

echo json_encode($combined_data);
exit();

        $arr_rtn = array(
            "resCode" => "69",
            "formName" => $formName,
            "formDesc" => $formDesc,
            // "formQuestion" => $question,
            // "formAnswer" => $optionData
            "data" => $combined_data
        );

        return json_encode($arr_rtn);
    }

    function getDataOption($qDt)
    {
        global $go_ncadb;
        $sqlChoice = "  SELECT
                            tb_questionoption.*
                        FROM
                            tb_questionoption
                        WHERE
                            tb_questionoption.questionoption_question = '$qDt' ORDER BY questionoption_order ASC";

        $dataOption = $go_ncadb->ncaretrieve($sqlChoice, "question");
        $data       = $this->ncaArrayConverter($dataOption);
        return  $data;
    }

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

}