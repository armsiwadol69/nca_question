<?php
error_reporting(0);
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
    } elseif ($action == "done") {
        chmod($path, 0755);
    } else {
        echo "Invalid action specified<br>";
    }
}

function changeSubFolderPermissions($id, $action) {
    $path = "../storage/$id";
    $original_permissions = fileperms($path);
    if ($action == "upload") {
        chmod($path, 0777);
    } elseif ($action == "done") {
        chmod($path, 0755);
    } else {
        echo "Invalid action specified<br>";
    }
}


$ncaquestion = new question();

if($methodRequest == "addQuestion") {

    $data = array();
    $questiondata = array();
    $questionmaindata = array();

    if($_POST['catemode'] > 0){

        foreach (array_unique($_POST['question']) as $k => $v) {

            $array_info[$value] = array(
                "par_questioninfoid"  => $_POST['questioninfoid'],
                "par_qname"           => $_POST['par_qname'],
                "par_qdatail"         => $_POST['par_qdatail'],
                "par_userid"          => $_POST['par_userId'],
                "oldquestion"         => $_POST['oldquestion'],
                // "questionid"          => $_POST['questionid'],
                "questionid"          => $v,
                "staffcompfunc"       => $_POST['staffcompfunc'],
                "staffcompfuncdep"    => $_POST['staffcompfuncdep'],
                "staffcompfuncdepsec" => $_POST['staffcompfuncdepsec'],
                "mquestiontype"       => $_POST['mquestiontype'],
                "questiongroup"       => $_POST['questiongroup'],
                "questionmode"        => "1",
                "departmentid"        => $_POST['question_departmentid'],
                "offensegroupid"      => $_POST['question_offensegroupid'],
                "mquestiontypecheck"  => $_POST['mquestiontypecheck'],
                "mquestiontype_name"  => $_POST['mquestiontype_name'],
                "questiongroupcheck"  => $_POST['questiongroupcheck'],
                "questiongroup_name"  => $_POST['questiongroup_name'],
                "questioncopy"        => $_POST['questioncopy'],
                "question"            => array_unique($_POST['question']),
            );


            foreach ($_POST['questionparent'][$v] as $key => $value) {

                if($value){
                    $dataM['question']        = $value;
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

            foreach ($_POST['questionparent'][$v] as $key => $value) {

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
                $data['dataactivities']           = $_POST['questionactivities'][$value];
                $data['questiondtdeleted']        = $_POST['questiondtdeleted'][$value];
                $optionid                         = "optionid".$value;
                $data['questionoption']           = $_POST[$optionid];
                $data['dataoption']               = $_POST[$option];
                $data['dataoptionvalue']          = $_POST[$optionval];
                $data['dataafteroption']          = $_POST['questionnameinputafteroptoion'][$value];
                $optionkey                        = $_POST[$optionid][$key];
                $data['optionimages']             = $_POST["questionoption_images"][$value];
                $data['optionmistakelevel']       = $_POST["questionoption_mistakelevel"][$value];
                $questiontype                     = $ncaquestion->getInpustType("questiontype_type",$_POST['questionnameinput'][$value]);
                $data['datainputtype']            = $questiontype['questiontype'];
                $data['dataoffense']              = $_POST['dataoffense'][$value];
                $questiondata[$v][$value]         = $data;

            }

        }

        $data = $ncaquestion->manageDataQuestion($questiondata);


    }else{
        
        $array_info = array(
            "par_questioninfoid"  => $_POST['questioninfoid'],
            "par_qname"           => $_POST['par_qname'],
            "par_qdatail"         => $_POST['par_qdatail'],
            "par_userid"          => $_POST['par_userId'],
            "oldquestion"         => $_POST['oldquestion'],
            "questionid"          => $_POST['questionid'],
            "staffcompfunc"       => $_POST['staffcompfunc'],
            "staffcompfuncdep"    => $_POST['staffcompfuncdep'],
            "staffcompfuncdepsec" => $_POST['staffcompfuncdepsec'],
            "mquestiontype"       => $_POST['mquestiontype'],
            "questiongroup"       => $_POST['questiongroup'],
            "questionmode"        => "1",
            "departmentid"        => $_POST['question_departmentid'],
            "offensegroupid"      => $_POST['question_offensegroupid'],
            "mquestiontypecheck"  => $_POST['mquestiontypecheck'],
            "mquestiontype_name"  => $_POST['mquestiontype_name'],
            "questiongroupcheck"  => $_POST['questiongroupcheck'],
            "questiongroup_name"  => $_POST['questiongroup_name'],
            "questioncopy"        => $_POST['questioncopy'],
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
            $data['dataactivities']           = $_POST['questionactivities'][$value];
            $data['questiondtdeleted']        = $_POST['questiondtdeleted'][$value];
            $optionid                         = "optionid".$value;
            $data['questionoption']           = $_POST[$optionid];
            $data['dataoption']               = $_POST[$option];
            $data['dataoptionvalue']          = $_POST[$optionval];
            $data['dataafteroption']          = $_POST['questionnameinputafteroptoion'][$value];
            $optionkey                        = $_POST[$optionid][$key];
            $data['optionimages']             = $_POST["questionoption_images"][$value];
            $data['optionmistakelevel']       = $_POST["questionoption_mistakelevel"][$value];
            $questiontype                     = $ncaquestion->getInpustType("questiontype_type",$_POST['questionnameinput'][$value]);
            $data['datainputtype']            = $questiontype['questiontype'];
            $data['dataoffense']              = $_POST['dataoffense'][$value];
            $questiondata[$value]             = $data;
    
        }
    
        $data = $ncaquestion->addNewQuestionCustom($array_info,$questionmaindata,$questiondata);
    }

    echo json_encode(array("data"=>$data));

} else if($methodRequest == "del") {

    $data = $ncaquestion->deleteMainQuestion($id,$currentUserId);
    echo json_encode($data);

} else if($methodRequest == "updatemtype") {

    $data = $ncaquestion->updateMtype($_POST);
    echo json_encode(array("data"=>$data));

} else if($methodRequest == "mquestiontypedata") {

    $data = $ncaquestion->generateMtype($_POST['currentmquestiontype'],$_POST['compfunc']);
    echo json_encode(array("data"=>$data));

} else if($methodRequest == "questionGroupdata") {

    $data = $ncaquestion->generatequestiongroup($_POST['questioncate'],$_POST['current_group']);
    echo json_encode(array("data"=>$data));

}else if($methodRequest == "updategroup") {

    $data = $ncaquestion->updategroup($_POST);
    echo json_encode(array("data"=>$data));

}
