<?php

date_default_timezone_set("Asia/Bangkok");
session_start();
$gb_notlogin = true;

require "../include.inc.php";
require "customfunction.php";
require "../class/class.question.php";

$go_ncadb = new ncadb();

$methodRequest = $_GET["mode"];
if(!$methodRequest){
    $methodRequest = $_POST["mode"];
}
// $conn = $GLOBALS["connect"];

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
    print_r($_POST);
    print_r($_FILES["imageFiles"]);
    // echo '<br>------------------------------------------------------<br>';
    // print_r($ar_prm["imageFilesOld"]);
    echo '<br>------------------------------------------------------<br>';
    echo '</pre>';
}

function upload_file($files, $id)
{   
    $get_files = $files;
    $uploadpath = '../storage/' . $id . '/';
    if (!file_exists($uploadpath)) {
        mkdir($uploadpath, 0755, true);
    }
    changeSubFolderPermissions($id,"upload");
    $uploaded_file_names = array(); // Initialize an empty array to store file names

    foreach ($get_files['name'] as $key => $name) {
        $files = $_FILES['imageFiles'];
        if ($files['error'][$key] == 0) {
            $ext = substr($name, strrpos($name, '.') + 1);
            $filename = mt_rand() . '.' . $ext;
            $destination = $uploadpath . $filename;
            if (move_uploaded_file($files['tmp_name'][$key], $destination)) {
                // echo "Your file '$filename' has been uploaded.<br>";
                $uploaded_file_names[] = $filename; // Append the filename to the array
            }
        } else {
            $uploaded_file_names[] = '';
        }
    }
    // Now $uploaded_file_names contains the names of the uploaded files
    // print_r($uploaded_file_names);
    changeSubFolderPermissions($id,"done");
    return $uploaded_file_names; // Return the array of file names
}

function deleteFiles($filesOld, $filesNew, $id)
{
    $path = '../storage/' . $id . '/';
    foreach ($filesNew as $key => $value) {
        // Check if the file in $filesNew has a value
        if (!empty($value)) {
            // Check if the corresponding index in $filesOld exists and is a valid file
            // echo $path . $filesOld[$key]."<br>";
            if (!empty($filesOld[$key]) && file_exists($path . $filesOld[$key])) {
                // Delete the file
                unlink($path . $filesOld[$key]);
                // echo "Deleted file: " . $filesOld[$key] . "<br>";
            }
        }
    }
}

function deleteFilesOnDelete($fileNames, $par_id)
{
    $path = '../storage/' . $par_id . '/';
    for ($i = 0; $i < count($fileNames); $i++) {
        $fileName = $fileNames[$i];
        $filePath = $path . $fileName;
        
        if (empty($fileName)) {
            continue;
        }
        
        if (file_exists($filePath)) {
            unlink($filePath);
            //echo "Deleted file: $filePath<br>";
        }
    }
    rmdir('../storage/' . $par_id);
}

function tis620_to_utf8($tis)
{
    for ($i = 0; $i < strlen($tis); $i++) {
        $s = substr($tis, $i, 1);
        $val = ord($s);
        if ($val < 0x80) {
            $utf8 .= $s;
        } elseif ((0xA1 <= $val and $val <= 0xDA) or (0xDF <= $val and $val <= 0xFB)) {
            $unicode = 0x0E00 + $val - 0xA0;
            $utf8 .= chr(0xE0 | ($unicode >> 12));
            $utf8 .= chr(0x80 | (($unicode >> 6) & 0x3F));
            $utf8 .= chr(0x80 | ($unicode & 0x3F));
        }
    }
    return $utf8;
}

function changePermissions($path, $action) {
    $original_permissions = fileperms($path);
    if ($action == "upload") {
        chmod($path, 0777);
        // echo "777<br>";
    } elseif ($action == "done") {
        chmod($path, 0755);
        // echo "755<br>";
    } else {
        echo "Invalid action specified<br>";
        // return;
    }
}

function changeSubFolderPermissions($id, $action) {
    $path = "../storage/$id";
    $original_permissions = fileperms($path);
    if ($action == "upload") {
        chmod($path, 0777);
        // echo "777<br>";
    } elseif ($action == "done") {
        chmod($path, 0755);
        // echo "755<br>";
    } else {
        echo "Invalid action specified<br>";
        // return;
    }
}


$ncaquestion = new question();

if($methodRequest == "addQuestion") {

    /* echo "<pre>";
    print_r($_POST);
    die(); */
    $data = array();
    $questiondata = array();
    $questionmaindata = array();
    $array_info = array(
        "par_questioninfoid" => $_POST['questioninfoid'],
        "par_qname" => $_POST['par_qname'],
        "par_qdatail" => $_POST['par_qdatail'],
        "par_userid" => $_POST['par_userId'],
        "oldquestion" => $_POST['oldquestion'],
        "questionid" => $_POST['questionid'],
    );

    foreach ($_POST['questionismainname'] as $key => $value) {

        if($value){
            $dataM['mainkey']         = $key;
            $dataM['maintext']        = $_POST['questiontext'][$key];
            $dataM['mainparent']      = $_POST['questionnameinputparent'][$key];
            $dataM['main']            = $_POST['questionismain'][$key];
            $option                   = "option".$key;
            $optionval                = "optionvalue".$key;
            $dataM['mainoptiontype']  = $_POST['questionnameinput'][$key];
            $dataM['mainoption']      = $_POST[$option];
            $dataM['mainoptionvalue'] = $_POST[$optionval];
            $dataM['mainafteroption'] = $_POST['questionnameinputafteroptoion'][$key];
            $questiontype             = $ncaquestion->getInpustType("questiontype_type",$_POST['questionnameinput'][$key]);
            $dataM['maininputtype']   = $questiontype['questiontype'];
            array_push($questionmaindata,$dataM);
            
        }
        
    }

    foreach ($_POST['questionname'] as $key => $value) {

        $data['mainkey']                  = $_POST['questionnameinputafter'][$value];
        $data['questiondt']               = $_POST['questiondt'][$value];
        $data['datakey']                  = $value;
        $data['dataparent']               = $_POST['questionnameinputparent'][$value];
        $data['datatext']                 = $_POST['questiontext'][$value];
        $data['datamain']                 = $_POST['questionismain'][$value];
        $option                           = "option".$value;
        $data['optionnm']                 = "option".$_POST['questionnameinputafter'][$value];
        $optionval                        = "optionvalue".$value;
        $data['dataoptiontype']           = $_POST['questionnameinput'][$value];
        $optionid                         = "optionid".$value;
        $data['questionoption']           = $_POST[$optionid];
        $data['dataoption']               = $_POST[$option];
        $data['dataoptionvalue']          = $_POST[$optionval];
        $data['dataafteroption']          = $_POST['questionnameinputafteroptoion'][$value];
        $optionkey                        = $_POST[$optionid][$key];
        $data['optionimages']             = $_POST["questionoption_images"][$value];
        $questiontype                     = $ncaquestion->getInpustType("questiontype_type",$_POST['questionnameinput'][$value]);
        $data['datainputtype']            = $questiontype['questiontype'];
        $questiondata[$value]             = $data;

    }

    /* $questionmaindatafilter = array();

    foreach ($questiondata as $key => $value) {

        if($value['datamain'] > 0){

            $dataM['mainkey']         = $key;
            $dataM['maintext']        = $_POST['questiontext'][$key];
            $dataM['mainparent']      = $_POST['questionnameinputparent'][$key];
            $dataM['main']            = $_POST['questionismain'][$key];
            $option                   = "option".$key;
            $optionval                = "optionvalue".$key;
            $dataM['mainoptiontype']  = $_POST['questionnameinput'][$key];
            $dataM['mainoption']      = $_POST[$option];
            $dataM['mainoptionvalue'] = $_POST[$optionval];
            $dataM['mainafteroption'] = $_POST['questionnameinputafteroptoion'][$key];
            $questiontype             = $ncaquestion->getInpustType("questiontype_type",$_POST['questionnameinput'][$key]);
            $dataM['maininputtype']   = $questiontype['questiontype'];
            $dataM['allquestion']     = $value['oldquestion'];
            array_push($questionmaindatafilter,$dataM);
            
        }
        
    } */
    echo "<pre>";
    print_r($_POST);
    print_r($questiondata);
    // die();
    $ncaquestion->addNewQuestion($array_info,$questionmaindata,$questiondata);

} else if($methodRequest == "del") {

    $data = $ncaquestion->deleteMainQuestion($id,$currentUserId);
    header("Content-Type: application/json");
    
    echo json_encode($data);

}
