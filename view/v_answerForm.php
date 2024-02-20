<?php
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

$ncaquestion = new questionview($_GET['id']);

if($_GET['id']){
    $questioninfo = array();
    $questioninfo = $ncaquestion->getDataQuestion();
    $arr_parent = array();
    $htmlQuestion = "";
    foreach($questioninfo AS $key => $val){

        if(!$val['questiondt_parent']){
            $htmlQuestion  .= $ncaquestion->genareteViewFormData("questiondt",$val['questiondt'],0,$arr_parent);
        }
        
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
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
                <pre>
                <?
                    print_r($ar_prm);
                ?>
                </pre>
            </div>
            <div class="col-12 text-center">
                <!-- <h1>agaite ikou ze saigo made</h1> -->
            </div>
            <div class="col-12" id="questionnaire">
                
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form action="" method="get" id="mForm">
                    <? echo $htmlQuestion; ?>
                    <input type="hidden" name="id" id="id" value="<?echo $ar_prm["id"]?>"/>
                    <button type="submit" class="btn btn-secondary">Submit</button>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary w-100" onclick="logFormData('mForm');">logFormData</button>
            </div>
        </div>
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
            const data = {};

            formData.forEach((value, key) => {
                data[key] = value;
            });

            console.log('Form Data:', data);
        }

    // Event listener for all radio buttons and text inputs
    $('input[name^="optionid"], input[name^="optionid"], input').on('change keyup', function() {
            // Get the id of the current input
            var inputId = $(this).attr('id');

            console.log(inputId);
            
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

            if(groupOfAnswerBox.length == "2" && groupOfAnswerBox.length == "2"){
                groupOfHeading4 = parentElement.find('h4').first();
                groupOfAnswerBox = parentElement.find('.answerBox').first();;
            }

            if (isCheckedOrNotEmpty) {
                // console.log("checked");
                correspondingH4.removeAttr('hidden');
                correspondingList.removeAttr('hidden');
                
                // Add 'required' attribute to inputs in class 'answer'
                groupOfHeading4.each(function(){
                    $(this).removeAttr('hidden');
                })
                groupOfAnswerBox.each(function(){
                    $(this).removeAttr('hidden');
                })
                
                parentElement.find('.answer input').not('[type="checkbox"]').first().prop('required', true);
            } else {
                console.log("unchecked");
                correspondingH4.attr('hidden', 'hidden');
                correspondingList.attr('hidden', 'hidden');
                
                // Remove 'required' attribute from inputs in class 'answer'
                parentElement.find('.answer input').prop('required', false);
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
                    correspondingH4.each(function() {
                        $(this).attr('hidden', 'hidden');
                    });
                    correspondingList.each(function() {
                        $(this).attr('hidden', 'hidden');
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
        // Select all input elements that are direct children of elements with the class "answerBox" and add the "required" attribute
        $('#mForm > div > div > div > input').prop('required', true);
        });
    </script>
