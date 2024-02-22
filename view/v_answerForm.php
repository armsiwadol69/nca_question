<?php
header('Content-Type: text/html; charset=utf-8');
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
$gb_notlogin = true;
require "../include.inc.php";
require_once ("../class/class.renderView.php");

$go_ncadb = new ncadb();

$_GET['id'] = "109";

$ncaquestion = new questionview($_GET['id']);

if($_GET['id']){
    $questioninfo = array();
    $questioninfo = $ncaquestion->getDataQuestion();
    $arr_parent = array();
    $htmlQuestion = "";
    $formId = $questioninfo[0]["question"];
    $formName = $questioninfo[0]["question_name"];
    $formDes = $questioninfo[0]["question_detail"];
    foreach($questioninfo AS $key => $val){

        if(!$val['questiondt_parent']){
            $htmlQuestion  .= $ncaquestion->genareteViewFormData("questiondt",$val['questiondt'],0,$arr_parent);
        }
        
    }
}

function arrayToHiddenInputs($array) {
    $hiddenInputs = '';
    foreach ($array as $key => $value) {
        $escapedKey = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
        $escapedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $hiddenInputs .= "<input type='hidden' name='userData[$escapedKey]' value='$escapedValue'>";
    }
    return $hiddenInputs;
}

function arrayToInputsBootstrap($array) {
    $hiddenInputs = '';
    foreach ($array as $key => $value) {
        $escapedKey = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
        $escapedValue = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        $hiddenInputs .= "<div class='col-xl-3 col-lg-3 col-md-3 col-sm-12'><lable for'$escapedKey'>$escapedKey<lable><input type='text' name='formApp_$escapedKey' value='$escapedValue' class='form-control form-control-sm' readonly></div>";
    }
    return $hiddenInputs;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCA QA</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/sidebarComponents/sidebar.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/aos/aos.css">
    <link rel="stylesheet" href="../assets/DataTables/datatables.min.css">
    <link rel="stylesheet" href="../assets/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="../assets/jquery-ui/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="../assets/swiper/swiper-bundle.min.css">
    <script src="../assets/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- <script src="../assets/livejs/live.js"></script> -->
    <script src="../assets/axios/axios.min.js"></script>
    <style>
    body {
        /* overflow: hidden; */
        background-color: whitesmoke;
    }

    .list-group-item {
        background-color: transparent !important;
        border : none !important;
        padding: .125rem 0rem;
    }

    .main-panel-bg-blur {
        background-color: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <pre id="jsonviewer">

                </pre>
            </div>
            <div class="col-12 text-center">
                <h1 class="fw-bold mt-3"><?echo $formName?></h1>
                <h3><?echo $formDes?></h3>
                <br>
            </div>
            <div class="col-12" id="questionnaire">
                
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form action="../class/apiQuestion.php?method=saveAnswer" method="POST" id="mForm" enctype="multipart/form-data">
                    <div class="row mb-5">
                    <div class="col-12">
                        <div class="collapse" id="showGet">
                            <div class="card card-body">
                            <?php echo arrayToInputsBootstrap($_GET);?>
                            </div>
                        </div>
                        <button class="btn btn-sm w-100 btn-info my-2" type="button" hidden data-bs-toggle="collapse" data-bs-target="#showGet" aria-expanded="false" aria-controls="showGet">แสดงข้อมูลรถ</button>
                    </div>
                    <div class="col-12 text-center">
                            <?
                                if(isset($_GET["busnumber"])){
                                    echo "เบอร์รถ : ".$_GET["busnumber"];
                                }
                                echo "<br>";
                                if(isset($_GET["busline"])){
                                    echo "สายรถ : ".$_GET["busline"]."-".$_GET["buslinetype"];
                                }
                                echo "<br>";
                                if(isset($_GET["queueRouteName"])){
                                    echo "เส้นทาง : ".$_GET["queueRouteName"];
                                }
                                echo "<br>";
                                if(isset($_GET["queuedtdate"])){
                                    echo "เที่ยวเวลา : ".$_GET["queuedtime"]." ".$_GET["queuedtdate"];
                                }
                            ?>
                    </div>
                    </div>
                    <? echo $htmlQuestion; ?>
                    <input type="hidden" name="id" id="id" value="<?echo $formId?>"/>
                    <button type="submit" class="btn btn-primary w-100 mb-3">บันทึกข้อมูล</button>
                    <button type="button" class="btn btn-primary w-100" onclick="logFormData('mForm');submitForm();">logFormData</button>
                </form>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-12">
                
            </div>
        </div> -->
    </div>
    <?php include_once 'v_footer.php';?>
    <script>
        function logFormData(formId) {
            const form = document.getElementById(formId);
            
            if (!form) {
                console.error('Form not found');
                return;
            }

            const formData = new FormData(form);

            console.log(JSON.stringify(Object.fromEntries(formData)));

            const data = {};

            formData.forEach((value, key) => {
                data[key] = value;
            });

            console.log('Form Data:', data);
            document.getElementById('jsonviewer').innerHTML = JSON.stringify(data);
        }

    // Event listener for all radio buttons and text inputs
    $('input[name^="optionid"], input[name^="optionid"], input').on('change keyup', function() {
            // Get the id of the current input
            var inputId = $(this).attr('id');

            console.log(inputId);

            const filePicker = $(this).parent().find('input[type="file"]');

            console.log(filePicker);
            
            // Check if the current input is checked or not empty
            var isCheckedOrNotEmpty = $(this).is(':checked');
            
            // Get the parent element
            var parentElement = $(this).closest('.list-group-item');

            // console.log(parentElement.prevAll());
            
            // Get the corresponding h4 and .list-group-item elements within the parent element
            var correspondingH4 = parentElement.find('h4').first();
            var correspondingList = parentElement.find('.answerBox').first();

            //checkForGroup
            var groupOfHeading4 = parentElement.find('h4');
            var groupOfAnswerBox = parentElement.find('.answerBox');
            // console.log(groupOfHeading4);
            // console.log(groupOfAnswerBox);
            console.log(groupOfHeading4.length);
            console.log(groupOfAnswerBox.length);

            const imageUploadInput = $(this).next('.file-upload-option');

            console.log(imageUploadInput);

            if(groupOfAnswerBox.length == "2" && groupOfAnswerBox.length == "2"){
                groupOfHeading4 = parentElement.find('h4').first();
                groupOfAnswerBox = parentElement.find('.answerBox').first();
            }

            if (isCheckedOrNotEmpty) {
                // console.log("checked");
                correspondingH4.removeAttr('hidden');
                correspondingList.removeAttr('hidden');
      
                groupOfHeading4.each(function(){
                    $(this).removeAttr('hidden');
                })
                groupOfAnswerBox.each(function(){
                    $(this).removeAttr('hidden');
                    $(this).removeAttr('required');
                    $(this).find('input[type^="text"], input[type^="number"], input[type^="date"]').prop('required','true');
                })

                imageUploadInput.removeAttr('hidden');
                imageUploadInput.prop('required','true');
                
                parentElement.find('.answer input').not('[type="checkbox"]').first().prop('required', true);
            } else {
                console.log("unchecked");
                correspondingH4.attr('hidden', 'hidden');
                correspondingList.attr('hidden', 'hidden');
                
                // Remove 'required' attribute from inputs in class 'answer'
                parentElement.find('.answer input').prop('required', false);
                imageUploadInput.attr('hidden', 'hidden');
            }

            console.log(isCheckedOrNotEmpty);

            var selectedValue = $(this).val();
            var otherRadioButtons = $('input[name="' + $(this).attr('name') + '"]');

            otherRadioButtons.each(function() {
                if ($(this).val() !== selectedValue) {
                    console.log("Value of other radio button: " + $(this).val());
                    const parentElement = $(this).closest('.list-group-item');
                    const correspondingH4 = parentElement.find('h4'); // Select only the first h4
                    const correspondingList = parentElement.find('.answerBox'); // Select only the first .list-group-item
                    const fileUploadList = parentElement.find('.file-upload-option'); 
                    correspondingH4.each(function() {
                        $(this).attr('hidden', 'hidden');
                    });
                    correspondingList.each(function() {
                        $(this).attr('hidden', 'hidden');
                    });
                    fileUploadList.each(function(){
                        $(this).attr('hidden', 'hidden');
                        $(this).removeAttr('required')
                    });
                    const allOptions = correspondingList.find('input[name^="optionid"]');
                    allOptions.each(function() {
                        var elementType = $(this).attr('type');
                        if (elementType === 'text') {
                            $(this).val(''); // Set value to empty for text input
                            } else if (elementType === 'date') {
                                $(this).val(''); // Set value to empty for date input
                            } else if (elementType === 'checkbox') {
                                $(this).prop('checked', false); // Uncheck checkbox
                            } else if (elementType === 'radio') {
                                $(this).prop('checked', false); // Uncheck radio button
                            }
                            $(this).prop('required', false);
                    });
                }
            });
        });

        $(document).ready(function() {
            const setReq = $('#mForm > .answerBox > div');
            // .prop('required', true);
            // console.log($('#mForm > div .answerBox > div > div > input'));
        });

        function submitFormData(formId, successCallback, errorCallback) {
            let form = document.getElementById(formId);
            if (!form) {
                console.error("Form with id '" + formId + "' not found.");
                return;
            }

            let formData = new FormData(form);

            fetch(form.action, {
                method: form.method,
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    if (successCallback && typeof successCallback === 'function') {
                        successCallback(response);
                    }
                } else {
                    throw new Error('Failed to submit form');
                }
            })
            .catch(error => {
                if (errorCallback && typeof errorCallback === 'function') {
                    errorCallback(error);
                } else {
                    console.error('Error submitting form:', error);
                }
            });
        }

        function submitForm() {
            submitFormData('mForm', 
                function(response) {
                    console.log('Form submitted successfully');
                    // Handle success response
                },
                function(error) {
                    console.error('Form submission error:', error);
                    // Handle error
                }
            );
        }

    </script>
