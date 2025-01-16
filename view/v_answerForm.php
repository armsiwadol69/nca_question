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
require_once ("../class/class.curlmanagedata.php");

$go_ncadb = new ncadb();

$curlNcaData = new curlManageData();

#there is no form ref for bus now so use fix id instead;
// $_GET['formId'] = "109";

$ncaquestion = new questionview($_GET['formId']);

if($_GET['formId']){
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

        $dontShow = array();

        if(in_array($escapedKey,$dontShow)){
            continue;
        }

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
        background: white !important;
    }

    .list-group-item {
        background-color: transparent !important;
        border: none !important;
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
                <h1 class="fw-bold mt-3">
                    <?echo $formName?>
                </h1>
                <h3>
                    <?echo $formDes?>
                </h3>
                <br>
            </div>
            <div class="col-12" id="questionnaire">

            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <form class="needs-validation" action="../class/apiQuestion.php?method=saveAnswer" method="POST" id="mForm" enctype="multipart/form-data">
                    <div class="row mb-5">
                        <div class="col-12">
                            <div class="collapse" id="showGet">
                                <div class="card card-body">
                                    <?php echo arrayToInputsBootstrap($_GET);?>
                                </div>
                            </div>
                            <button class="btn btn-sm w-100 btn-info my-2" type="button" hidden data-bs-toggle="collapse" data-bs-target="#showGet" aria-expanded="false"
                                aria-controls="showGet">แสดงข้อมูลรถ</button>
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
                        <div class="col-12">
                            <div class="card rounded-0 bg-white">
                                <div class="card-body p-0">
                                    <label class="h5 my-2 ms-2" for="btn-group-check-type">เลือกการตรวจสอบ</label>
                                    <div class="btn-group btn-group-sm p-2 w-100" role="group" id="btn-group-check-type" aria-label="Basic radio toggle button group">
                                        <input type="radio" class="btn-check btn-check-type-group" name="check_type" id="check_type_1" value="1" autocomplete="off" required>
                                        <label class="btn btn-outline-primary border border-primary" for="check_type_1">บุคคล</label>

                                        <input type="radio" class="btn-check btn-check-type-group" name="check_type" id="check_type_2" value="2" autocomplete="off" required>
                                        <label class="btn btn-outline-primary border border-primary" for="check_type_2">สาขา</label>

                                        <input type="radio" class="btn-check btn-check-type-group" name="check_type" id="check_type_3" value="3" autocomplete="off" required>
                                        <label class="btn btn-outline-primary border border-primary" for="check_type_3">รถ</label>
                                    </div>
                                    <div class="w-100 p-2">
                                        <hr>
                                    </div>
                                    <div class="frm-check-type-1 w-100 p-2" id="frm-check-type-1" hidden>
                                        <h5 class="mb-2">ข้อมูลพนักงาน</h5>
                                        <!-- emp -->
                                        <input type="hidden" name="empid" id="empid" value="" required>
                                        <div class="row g-2">
                                            <div class="col-xl-10 col-lg-10 col-md-9 col-sm-8">
                                                <div class="form-floating">
                                                    <input class="form-control rounded-0 input-t-1" id="empcode" name="empcode" type="text" placeholder="รหัสพนักงาน" />
                                                    <label for="รหัสพนักงาน">รหัสพนักงาน<span class="text-danger">*</span> มีขีด เช่น 3-1150</label>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-2 col-md-3 col-sm-2">
                                                <button class="btn btn-sm btn-info btn-emp-search w-100 rounded-0 h-100" type="button" onclick="searchEmp();" id="btn-emp-search">ค้นหา</button>
                                            </div>
                                        </div>
                                        <div class="form-floating mt-2">
                                            <input class="form-control rounded-0 input-t-1" id="empname" name="empname" type="text" placeholder="ชื่อพนักงาน" readonly />
                                            <label for="ชื่อพนักงาน">ชื่อพนักงาน</label>
                                        </div>
                                    </div>
                                    <div class="frm-check-type-2 w-100 p-2" id="frm-check-type-2" hidden>
                                        <!-- outlet -->
                                        <h5 class="mb-2">ข้อมูลสาขา</h5>
                                        <div class="form-floating">
                                            <select class="form-select rounded-0 input-t-2" id="outletId" name="outletId" aria-label="สาขา">
                                                <option value="" disabled selected>เลือก...</option>
                                                <?
                                        $sec_outletlist = $curlNcaData->getoutlet();
                                        foreach ($sec_outletlist["data"] as $rk => $rv) {
                                            echo '<option value="'.$rv['outlet_id'].'">'.$rv['outlet_nameth'].' ('.$rv['outlet_nmth'].')'.'</option>';
                                        }
                                    ?>
                                            </select>
                                            <label for="สาขา">สาขา</label>
                                        </div>
                                    </div>
                                    <div class="frm-check-type-3 w-100 p-2" id="frm-check-type-3" hidden>
                                        <h5 class="mb-2">ข้อมูลรถ</h5>
                                        <!-- bus -->
                                        <div class="row g-1">
                                            <?php echo arrayToInputsBootstrap($_GET);?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <? echo $htmlQuestion; ?>
                    <input type="hidden" name="id" id="id" value="<?echo $formId?>" />
                    <button type="submit" class="btn btn-primary w-100 mt-5 mb-5">บันทึกข้อมูล</button>
                    <!-- <button type="button" class="btn btn-primary w-100" onclick="logFormData('mForm');submitForm();">logFormData</button> -->
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

        const imageUploadInput = $(this).next().next('.file-upload-option');

        console.log(imageUploadInput);

        if (groupOfAnswerBox.length == "2" && groupOfAnswerBox.length == "2") {
            groupOfHeading4 = parentElement.find('h4').first();
            groupOfAnswerBox = parentElement.find('.answerBox').first();
        }

        if (isCheckedOrNotEmpty) {
            // console.log("checked");
            correspondingH4.removeAttr('hidden');
            correspondingList.removeAttr('hidden');

            groupOfHeading4.each(function() {
                $(this).removeAttr('hidden');
            })
            
            groupOfAnswerBox.each(function() {
                $(this).removeAttr('hidden');
                $(this).removeAttr('required');
                $(this).find('input[type^="text"], input[type^="number"], input[type^="date"]').prop('required', 'true');
            })

            imageUploadInput.removeAttr('hidden');
            imageUploadInput.prop('required', 'true');

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
                fileUploadList.each(function() {
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

    function pickedCheckType(type_v) {

        const el_input_type_1 = $(".input-t-1");
        const el_input_type_2 = $(".input-t-2");
        const el_input_type_3 = $(".input-t-3");

        const div_check_type_1 = $("#frm-check-type-1");
        const div_check_type_2 = $("#frm-check-type-2");
        const div_check_type_3 = $("#frm-check-type-3");

        // Disable all input fields initially
        el_input_type_1.prop("disabled", true).prop("required", false);
        el_input_type_2.prop("disabled", true).prop("required", false);
        el_input_type_3.prop("disabled", true).prop("required", false);

        // Hide all divs initially
        div_check_type_1.attr("hidden", true);
        div_check_type_2.attr("hidden", true);
        div_check_type_3.attr("hidden", true);

        if (type_v == "1") {
            // Check emp
            el_input_type_1.prop("disabled", false);
            div_check_type_1.attr("hidden", false);
        } else if (type_v == "2") {
            // Check outlet
            el_input_type_2.prop("disabled", false);
            div_check_type_2.attr("hidden", false);
        } else if (type_v == "3") {
            // Check bus
            el_input_type_3.prop("disabled", false);
            div_check_type_3.attr("hidden", false);
        }

        // Add required attribute to visible input fields
        // $("input:visible").prop("required", true);
    }

    function clearEmpDt() {
        $("#empid").val("");
        $("#empname").val("");
        $("#empcode").val("");
        isSeeAll = 0;
    }

    function addEventToRadioCheckType() {
        $(".btn-check-type-group").bind("change", function(event) {
            const selectedValue = $(this).val();
            pickedCheckType(selectedValue);
            isSeeAll = 0;
            clearEmpDt();
            // getQuestion();
        });
    }

    async function searchEmp() {
        const pickedDept = $("#sec_section").val();

        if (pickedDept == "0") {
            showAlertToast("กรุณาเลือกแผนกก่อน", "info", "center");
            return;
        }

        const empCode = $("#empcode").val();

        if (!empCode) {
            showAlertToast("กรอกรหัสพนักงาน", "info", "center");
            return;
        }

        showLoadingOnQuery();

        const url = "../class/apiproxy.php?method=getEmpData&empCode=" + empCode;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            Swal.close();

            const json = await response.json();
            if (json.respCode == "1") {
                const empDept = json.data[0]["emp_sec"];
                setEmpDt(json.data[0]);
                showAlertToast("ค้นหาสำเร็จ", "success", "bottom");
            } else {
                showAlertToast("ไม่พบข้อมูลพนักงาน", "info", "center");
                clearEmpDt();
            }
        } catch (error) {
            console.error(error.message);
        }
    }

    function setEmpDt(data) {
        $("#empid").val(`${data.empicms_id}`).trigger('change');
        $("#empname").val(`${data.emp_firstname} ${data.emp_lastname}`);
    }

    async function showLoadingOnQuery() {
        Swal.fire({
            title: "รอซักครู่...",
            html: "กำลังเรียกข้อมูลที่จำเป็น",
            toast: true,
            position: "bottom",
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
    }

    $(document).ready(function() {
        const setReq = $('#mForm > .answerBox > div');
        // .prop('required', true);
        // console.log($('#mForm > div .answerBox > div > div > input'));
        addEventToRadioCheckType();
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