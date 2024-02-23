<?php
// header('Content-Type: application/json; charset=utf-8');
header('Content-Type: text/html; charset=utf-8');
date_default_timezone_set("Asia/Bangkok");
$gb_notlogin = true;
require "../include.inc.php";

$debugMode = false;
if($debugMode){
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

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

//NotUse
$apiKey = "";

$go_ncadb = new ncadb();

$question = new ncaQuestion();

$question->setQuestionId($ar_prm["id"]);

// print_r($question);

switch ($ar_prm["method"]) {
    case "getQuestionData":
        echo $question->getDataQuestion();
        break;
    case "getOptionData":
        echo $question->getOptionData($ar_prm["id"]);
        break;
    case "saveAnswer":
        echo $question->saveAnswer($ar_prm);
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
        
        // $formName = $question[0]["question_name"];
        // $formDesc = $question[0]["question_detail"];

        // // array_shift($question);
        // $optionData =  $this->getDataOption($this->id);

        // print_r($question);
        // print_r($optionData);

        return json_encode($question);
    }

    function getOptionData($qDt)
    {
        global $go_ncadb;
        $sqlChoice = "SELECT
        tb_questionoption.*
        FROM
        tb_questionoption
        WHERE
        tb_questionoption.questionoption_question = '$qDt' ORDER BY questionoption_order ASC";

        $dataOption = $go_ncadb->ncaretrieve($sqlChoice, "question");
        $data       = $this->ncaArrayConverter($dataOption);
        return  json_encode($data);
    }

    function saveAnswer($ar_prm){
        global $go_ncadb;
        global $question;
        global $debugMode;
       
        if($debugMode){
            echo "<pre>";
            print_r($ar_prm);
            echo "</pre>";
            echo "<pre>";
                print_r($this->groupFilesArrayById($_FILES));
            echo "</pre>";
        }

        // echo "HIHI";
        $questionId =  $question->id;
        // echo $questionId;
        // exit();

        $answer_busref = $ar_prm["formApp_busnumber"];
        $answer_remark = $ar_prm["formApp_busnumber"]." | ".$ar_prm["formApp_busline"]."-".$ar_prm["formApp_buslinetype"]." ".$ar_prm["formApp_queueRouteName"]." ".$ar_prm["formApp_queuedtime"]
                         ." ".$ar_prm["formApp_queuedtdate"];
        $answer_userId = $ar_prm["formApp_userId"];

        // echo $answer_remark;

        if(!empty($questionId)){
                #MAKE Q
                $InsertSqlBuilder = new SqlBuilder();
                $InsertSqlBuilder->setTableName("tb_answer");
                $i = 0;
                $sqlBuild[$i++] = new TField("answer_busref", $answer_busref, "string");
                $sqlBuild[$i++] = new TField("answer_remark", iconv('utf-8','tis-620',$answer_remark), "string");
                $sqlBuild[$i++] = new TField("answer_question", $questionId, "string");
                $sqlBuild[$i++] = new TField("answer_status", '1', "string");
                $sqlBuild[$i++] = new TField("answer_active", '1', "string");
                $sqlBuild[$i++] = new TField("answer_recspid", $answer_userId, "string");

                $InsertSqlBuilder->setField($sqlBuild);

                $query = $InsertSqlBuilder->InsertSql();

                $result = $go_ncadb->ncaexec($query, "question");
                if($result){
                    $insert_id = $go_ncadb->ncaGetInsId("question"); 
                   if($debugMode){
                        echo "<br>INSERT ID :: $insert_id<br>";
                   }
                }else{
                   return $rtn_array = array("resCode" => "0");
                }
                
                #For easier understanding
                $answerId = $insert_id;
        }

        #GET Questions
        $questionData = $question->getDataQuestion();
        $arr_question = json_decode($questionData,true);
        #GET Options
        $optionsData = $this->getOptionData($this->id);
        $arr_options = json_decode($optionsData,true);

        // echo "<pre>";
        //     print_r($arr_question);
        //     print_r($arr_options);
        // echo "</pre>";

        $attachmentArray = array();

        if(!empty($_FILES)){
            $imageArray = $this->groupFilesArrayById($_FILES);
            $uploadResult = $this->uploadImagesHandler($imageArray);
            if($uploadResult["result"]== "1"){
                $attachmentArray = $uploadResult["files"];
            }else{
                echo "Failed In Image uploading! ";
                $go_ncadb->ncarollback();
                exit();
            }
        }

        if($debugMode){
            echo "<pre style='background-color:pink;color:black;'>";
            print_r($attachmentArray);
            echo "</pre>";
        }

        $branchSqlInsert = array();

        foreach ($arr_question as $key => $value) {
          
            $g_qusetionId = $value["question"];
            $questionDt = $value["questiondt"];
            $questionType = $value["questiondt_questiontype"];
            $questionOrder = $value["questiondt_order"];

            if($debugMode){
                $bgColor = $this->randomBackgroundColor();
                echo "<div style='background-color:$bgColor '>";
                echo "<pre>";
                print_r($value);
                echo "</pre>";  
            }

            $haveOptions = array();

            foreach ($arr_options as $keyA => $valueA) {
                $questionOptionDt = $valueA["questionoption_questiondt"];
                $questionId = $valueA["questionoption"];
             
                if($questionDt == $questionOptionDt){
                    array_push($haveOptions, $valueA);
                    // echo "<div style='background-color:white; padding:20px;'><pre>";
                    // print_r($value);
                    // echo "</pre></div>";
                } 
            }

            if($debugMode){
                echo "<pre>";
                print_r($haveOptions);
                echo "</pre>"; 
            }
            
            $answerOptionsId = null;

            foreach ($ar_prm as $keyAR => $valueAR) {
               if($keyAR == "optionid$questionDt"){
                $answerOptionsId = $valueAR;
                break;
               }
            }
            
            $questionSigleAnswerType = array("1","2","3");

            if(in_array($questionType, $questionSigleAnswerType)){
                $optionId = $this->findOptionIdWithQuestiondtDt($questionDt,$arr_options);
                #PREPAR SQL INSERT TO tb_answerdt :: Answer Type TEXT
                $InsertSqlBuilder = new SqlBuilder();
                $InsertSqlBuilder->setTableName("tb_answerdt");
                $i = 0;
                $sqlBuild[$i++] = new TField("answerdt_answer", $answerId, "string");
                $sqlBuild[$i++] = new TField("answerdt_question", $g_qusetionId , "string");
                $sqlBuild[$i++] = new TField("answerdt_questiondt", $questionDt , "string");
                $sqlBuild[$i++] = new TField("answerdt_questiontype", $questionType , "string");
                $sqlBuild[$i++] = new TField("answerdt_optionid", $optionId, "string");
                $sqlBuild[$i++] = new TField("answerdt_order", $questionOrder , "string");
                $sqlBuild[$i++] = new TField("answerdt_recspid", $answer_userId , "string");
                $sqlBuild[$i++] = new TField("answerdt_questiondtorder", $questionOrder , "string");
                #VALUE
                $sqlBuild[$i++] = new TField("answerdt_value", iconv("utf-8","tis-620", $answerOptionsId) ,"string");
                $InsertSqlBuilder->setField($sqlBuild);
                $query = $InsertSqlBuilder->InsertSql();
                $insertAnswerIdType1 = $go_ncadb->ncaGetInsId("question"); 
                $branchSqlInsert[] = $query.";";

                $this->insertImageToAttachments($go_ncadb,$insert_id,$insertAnswerIdType1,$g_qusetionId,$questionDt,$optionId,$attachmentArray,$answer_userId);

            }else if($questionType == "4"){
                #GET OPTION's Array Data
                $answerValue = $this->findValueFromOptionArray($answerOptionsId,$arr_options);
                if(!empty($answerValue)){
                    #PREPAR SQL INSERT TO tb_answerdt :: Answer Type RADIO
                    $InsertSqlBuilder = new SqlBuilder();
                    $InsertSqlBuilder->setTableName("tb_answerdt");
                    $i = 0;
                    $sqlBuild[$i++] = new TField("answerdt_answer", $answerId, "string");
                    $sqlBuild[$i++] = new TField("answerdt_question", $g_qusetionId , "string");
                    $sqlBuild[$i++] = new TField("answerdt_questiondt", $questionDt , "string");
                    $sqlBuild[$i++] = new TField("answerdt_questiontype", $questionType , "string");
                    $sqlBuild[$i++] = new TField("answerdt_questiondtorder", $questionOrder , "string");
                    $sqlBuild[$i++] = new TField("answerdt_value", $answerValue["questionoption_value"] ,"string");
                    $sqlBuild[$i++] = new TField("answerdt_optionid", $answerValue["questionoption"] , "string");
                    $sqlBuild[$i++] = new TField("answerdt_recspid", $answer_userId , "string");
                    $sqlBuild[$i++] = new TField("answerdt_order", $answerValue["questionoption_order"] , "string");
                    // echo "<pre style='margin-left:10rem'>";
                    // print_r($answerValue);
                    // echo "</pre>";
                    $InsertSqlBuilder->setField($sqlBuild);
                    $query = $InsertSqlBuilder->InsertSql();
                    $go_ncadb->ncaexec($query, "question");
                    $insertAnswerIdType4 = $go_ncadb->ncaGetInsId("question");
                    $this->insertImageToAttachments($go_ncadb,$insert_id,$insertAnswerIdType4,$g_qusetionId,$questionDt,$answerValue["questionoption"],$attachmentArray,$answer_userId);
                }

                
            }else if($questionType == "5"){

                #PREPAR SQL INSERT TO tb_answerdt :: Answer Type CHECKBOX (NEED TO LOOP FOR EVERY CHECKED)
                #GET OPTION's Array Data
                if(!empty($answerOptionsId)){
                    foreach ($answerOptionsId as $keyType5 => $valueType5) {
                        $answerValue = $this->findValueFromOptionArray($valueType5,$arr_options);
                        $InsertSqlBuilder = new SqlBuilder();
                        $InsertSqlBuilder->setTableName("tb_answerdt");
                        $i = 0;
                        $sqlBuild[$i++] = new TField("answerdt_answer", $answerId, "string");
                        $sqlBuild[$i++] = new TField("answerdt_question", $g_qusetionId , "string");
                        $sqlBuild[$i++] = new TField("answerdt_questiondt", $questionDt , "string");
                        $sqlBuild[$i++] = new TField("answerdt_questiontype", $questionType , "string");
                        $sqlBuild[$i++] = new TField("answerdt_questiondtorder", $questionOrder , "string");
                        $sqlBuild[$i++] = new TField("answerdt_recspid", $answer_userId , "string");
                        $sqlBuild[$i++] = new TField("answerdt_value", $answerValue["questionoption_value"] ,"string");
                        $sqlBuild[$i++] = new TField("answerdt_optionid", $answerValue["questionoption"] , "string");
                        $sqlBuild[$i++] = new TField("answerdt_order", $answerValue["questionoption_order"] , "string");
                        // echo "<pre style='margin-left:10rem'>";
                        // print_r($answerValue);
                        // echo "</pre>";
                        $InsertSqlBuilder->setField($sqlBuild);
                        $query = $InsertSqlBuilder->InsertSql();
                        $go_ncadb->ncaexec($query, "question");
                        $insertAnswerIdType5 = $go_ncadb->ncaGetInsId("question");
                        $this->insertImageToAttachments($go_ncadb,$insert_id,$insertAnswerIdType5,$g_qusetionId,$questionDt,$answerValue["questionoption"],$attachmentArray,$answer_userId);
                        // $branchSqlInsert[] = $query.";";
                    }

                }
            }

            if($debugMode){
                echo "<br>---------------------------------------------------------------------------------<br>";
                echo '</div>';
            }
         

            #handle image upload
            
            #joinSql
            // $query = $InsertSqlBuilder->InsertSql();
            // $branchSqlInsert .= $query.";";
        }
        // echo iconv("tis-620","utf-8", $branchSqlInsert);
        // return json_encode($ar_prm);

        // $insertAnswerDt = $go_ncadb->ncaexec($branchSqlInsert, "question");
        // if($insertAnswerDt){
        //     echo '<script charset="" language="javascript">alert("บันทึกสำเร็จ"); window.close()";</script>';
        // }else{
        //     echo '<script charset="" language="javascript">alert("บันทึกไม่สำเร็จ"); window.close()";</script>';
        // }
        echo '
                <script charset="utf-8" language="javascript">
                     window.location.href = "../view/v_submitted.php?result=1";
                </script>
             ';
}

    public function insertImageToAttachments($go_ncadb,$answerId,$answerDt,$questionId,$questionDt,$optionId,$attachmentData,$answer_userId)
    {   
        // echo "<pre>insertImageToAttachments"."<br>";
        // echo $optionId;
        // echo "</pre>";
        // print_r($attachmentData);
        
        foreach ($attachmentData as $attachmentDataKey => $attachmentDataValue) {
            print_r($attachmentDataValue);
            if($attachmentDataValue["optionid"] == $optionId){
                $InsertSqlBuilder = new SqlBuilder();
                $InsertSqlBuilder->setTableName("tb_answerattachment");
                $i = 0;
                $sqlBuild[$i++] = new TField("answerattachment_answer", $answerId, "string");
                $sqlBuild[$i++] = new TField("answerattachment_answerdt", $answerDt, "string");
                $sqlBuild[$i++] = new TField("answerattachment_question", $questionId, "string");
                $sqlBuild[$i++] = new TField("answerattachment_questiondt", $questionDt, "string");
                $sqlBuild[$i++] = new TField("answerattachment_questiondoption", $optionId, "string");
                $sqlBuild[$i++] = new TField("answerattachment_type", $attachmentDataValue["type"], "string");
                $sqlBuild[$i++] = new TField("answerattachment_name", $attachmentDataValue["filename"], "string");
                $sqlBuild[$i++] = new TField("answerattachment_path", $attachmentDataValue["path"], "string");
                $sqlBuild[$i++] = new TField("answerattachment_recspid", $answer_userId, "string");      
                $InsertSqlBuilder->setField($sqlBuild);
                $query = $InsertSqlBuilder->InsertSql();
                $result = $go_ncadb->ncaexec($query, "question");
                if(!$result){
                    $go_ncadb->ncarollback("question");
                    // echo "ERROR while executing image uploading";
                    exit();
                }
            }
        }  
    }

    #FOR RADIO CHECKBOX etc.
    public function findValueFromOptionArray($questionoption, $optionArray){
        // print_r($optionArray);
        foreach ($optionArray as $key => $array) {
           if($array["questionoption"] == $questionoption){
            return $array;
           }
        }
    }

    #ONLY USE WITH SINGEL INPUT (TEXT,DATE)
    public function findOptionIdWithQuestiondtDt($questiondt, $optionArray){
        foreach ($optionArray as $key => $array) {
           if($array["questionoption_questiondt"] == $questiondt){
            return $array["questionoption"];
           }
        }
    }

    public function randomBackgroundColor() {
   
        $red = mt_rand(0, 255);
        $green = mt_rand(0, 255);
        $blue = mt_rand(0, 255);

        $hexRed = dechex($red);
        $hexGreen = dechex($green);
        $hexBlue = dechex($blue);

        $hexRed = str_pad($hexRed, 2, "0", STR_PAD_LEFT);
        $hexGreen = str_pad($hexGreen, 2, "0", STR_PAD_LEFT);
        $hexBlue = str_pad($hexBlue, 2, "0", STR_PAD_LEFT);
    
   
        $hexColor = "#" . $hexRed . $hexGreen . $hexBlue;
    
        return $hexColor;
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

    public function reArrayFiles(&$file_post) {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);
    
        for ($i=0; $i<$file_count; $i++) {
            foreach ($file_keys as $key) {
                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }
    
        return $file_ary;
    }

    public function groupFilesArrayById($inputArray) {
        $outputArray = array();
        if (isset($inputArray['fileUploadOption']['name'][0])) {
            foreach ($inputArray['fileUploadOption']['name'] as $key => $name) {
                $id = key($name);
                if (!isset($outputArray[$id])) {
                    $outputArray[$id] = array();
                }
                $fileData = array(
                    'name' => $name[$id],
                    'type' => $inputArray['fileUploadOption']['type'][$key][$id],
                    'tmp_name' => $inputArray['fileUploadOption']['tmp_name'][$key][$id],
                    'error' => $inputArray['fileUploadOption']['error'][$key][$id],
                    'size' => $inputArray['fileUploadOption']['size'][$key][$id]
                );
                $outputArray[$id][] = $fileData;
            }
        }
        $resultArray = array();
        foreach ($outputArray as $id => $files) {
            $resultArray[] = array($id, $files);
        }
        return $resultArray;
    }

    public function uploadImagesHandler($resultArray) {
        $uploadDir = "../storage/"; // Directory
    
        $successfulUploads = array(); // To keep track of successful uploads
    
        foreach ($resultArray as $item) {
            $id = $item[0];
            $images = $item[1];
    
            foreach ($images as $image) {
                $name = $image['name'];
                $tmp_name = $image['tmp_name'];
                $error = $image['error'];
    
                // Skip if no file uploaded (UPLOAD_ERR_NO_FILE)
                if ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }

                // Check for other errors
                if ($error !== UPLOAD_ERR_OK) {
                    // If there's an error, delete all uploaded files and return false
                    foreach ($successfulUploads as $file) {
                        unlink($file);
                    }
                    return array(
                        "result" => false,
                        "files" => array()
                    );
                }
                // Get the file extension
                $fileExtension = pathinfo($name, PATHINFO_EXTENSION);
    
                // Generate a new unique name for the file
                $newName = uniqid($id . "_") . "." . $fileExtension;
    
                $destination = $uploadDir . $newName;

                if (move_uploaded_file($tmp_name, $destination)) {
                    $successfulUploads[] = array(
                                            "filename" => $newName,
                                            "optionid" => $id,
                                            "path" => $destination,
                                            "type" => $fileExtension
                                           ); // Keep track of successful uploads
                } else {
                    // If there's an error during upload, delete all uploaded files and return false
                    foreach ($successfulUploads as $file) {
                        unlink($file);
                    }
                    return array(
                        "result" => "0",
                        "files" => $destination
                    );
                }
            }
        }
        return array(
            "result" => "1",
            "files" => $successfulUploads
        );
    }
}