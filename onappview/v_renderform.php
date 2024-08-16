<?php
    header('Content-Type: text/html; charset=utf-8');
    date_default_timezone_set("Asia/Bangkok");
    session_start();
    $gb_notlogin = true;
    require_once('../include.inc.php');
    require_once('../class/class.question.php');


    if(!isset($_SESSION["credential"]) && empty( $_SESSION["credential"]) ){
        echo "<script>window.location.href = 'index.php';</script>";
        exit();
    }

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

    //DEFIND
    $go_ncadb = new ncadb();
    $ncaquestion = new question();

    //GET ALL QUESTION CATEGORY
    $section = $ncaquestion->getSectionData();
    $sectionList = $section["data"];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แบบบันทึกและลงสถิติ</title>
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
    @import url('https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    body {
        /* overflow: hidden; */
        background-color: white !important;
        background: white !important;
        font-family: "Prompt", sans-serif;
        font-weight: 400;
        font-style: normal;
    }

    .main-panel-bg-blur {
        background-color: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
    }

    .list-group-item:hover {
        background-color: rgba(0, 0, 0, 0.4) !important;
        color: white;
        cursor: pointer;
    }

    p {
        margin-bottom: 0px !important;
    }

    .nca-g-bg-1 {
        background: rgb(36, 37, 82);
        background: linear-gradient(180deg, rgba(36, 37, 82, 1) 0%, rgba(74, 87, 163, 1) 100%);
        background-size: 100% 100%;
    }

    .float-btn-bottom {
    --left-offset: 5.5rem;
    position: fixed;
    bottom: 0;
    /* left: calc(var(--left-offset)); */
    margin: 0 auto;
}
    </style>
</head>

<body>
    <?
        $empid = $_SESSION["credential"]["empid"];
        $empcode = $_SESSION["credential"]["empcode"];
        $username = $_SESSION["credential"]["empname"];
        // print_r($_SESSION["credential"]);
    ?>
    <div class="container-fluid p-0">
        <div class="row g-0 shadow-sm">
            <div class="col-12 nca-g-bg-1 text-center text-white">
                <!-- <h6 class="mt-2 text-white">รายการคําถาม</h6> -->
                <h5 class="mt-2 text-white">แบบบันทึกและลงสถิติ</h5>
                <p class="text-white mb-2 h6">บันทึกโดย :
                    <? echo $empcode; ?>
                    <? echo $username; ?> <a class="btn btn-sm btn-danger ms-2" href="#" onclick="askBeforeLogout();"><span>ออกจากระบบ</span></a>
                </p>
            </div>
            <!-- SECTION -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                <!-- <h6 class="mt-2 text-white fw-bold">Survey</h6> -->
                <div class="form-floating">
                    <select class="form-select rounded-0" id="par_section" name="par_section" onchange="queryDepSecQuestionCate();" aria-label="question type">
                        <option value="" selected disabled>เลือก...</option>
                        <?php
                        foreach ($sectionList as $key => $value) {
                            echo "<option value='" . $value["section_id"] . "'>" . $value["section_name"] . "</option>";
                        }
                        ?>
                    </select>
                    <label for="floatingSelect">แผนก</label>
                </div>

            </div>
            <!-- QUESTION CATE -->
            <div class="col-lg-4 col-md-6 col-sm-6 col-6">
                <!-- <h6 class="mt-2 text-white fw-bold">Survey</h6> -->
                <div class="form-floating">
                    <select class="form-select rounded-0" id="par_category" name="par_category" onchange="queryQestionBySecAndCate();" aria-label="par_category">
                        <option value="" selected disabled>เลือก...</option>
                        <?php
                        ?>
                    </select>
                    <label for="floatingSelect">ประเภท</label>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-12 col-12">
                <!-- <h6 class="mt-2 text-white fw-bold">Survey</h6> -->
                <div class="form-floating">
                    <select class="form-select rounded-0" id="par_qset" name="par_qset" onchange="queryQestionBySecCateGroup();" aria-label="par_qset">
                        <option value="0" selected>ทั้งหมด</option>
                        <?php
                        ?>
                    </select>
                    <label for="floatingSelect">หัวข้อปัญหาที่พบ</label>
                </div>
            </div>
            <div class="col-12">
            </div>
        </div>
        <form action="" id="frm_main">
            <div class="col-12 w-100" style=" overflow-y: scroll; overflow-x: hidden;">
                <div class="row g-2 mt-2" id="div_frm_main"></div>
            </div>
        </form>
    </div>
    </div>
    <div class="w-100" style="margin-top: 30px"></div>
    <button type="button" id="btn_submit" class="btn btn-primary rounded-0 w-100 float-btn-bottom" onclick="submitForm();"
                    disabled>บันทึก</button></button>
    <script>

    let q_category = "0";
    let q_group = "0";
    let q_type = "0";

    let empid = "<?= $_SESSION["credential"]["empid"] ?>";
    let empcode = "<?= $_SESSION["credential"]["empcode"] ?>";
    let empname = "<?= $_SESSION["credential"]["empname"] ?>";

    function objectToQueryString(obj) {
        return Object.keys(obj).map(key => key + '=' + encodeURIComponent(obj[key])).join('&');
    }

    function closeWindow() {
        window.close();
    }

    function openFormPage(id) {
        const obj_prm = <?php echo json_encode($ar_prm); ?>;
        get_prm = `&formId=${id}`;
        window.location = '../view/v_answerForm.php?' + get_prm;
    }
    </script>
    <?php include_once '../view/v_footer.php';?>
    <script>
    async function getQuestionList() {
        fireSwalOnSubmit();
        const apiEndpoint = '../phpfunc/appapi.php';

        $.post(apiEndpoint, {
                "method": "getQuestionList",
                "q_category": q_category,
                "q_group": q_group,
                "q_type": q_type
            },
            function(data, status) {
                Swal.close();
                genareateQuestionList(data);
            });
    }

    async function queryDepSecQuestionCate() {
        const par_section = $("#par_section").val();
        $.post('../class/apiform.php', {
                "method": "getDepSecFormCategory",
                "section": par_section
            },
            function(data, status) {
                setFormCateDropdown(data);
            });
    }

    async function queryQestionBySecAndCate() {

        const par_section = $("#par_section").val();
        const par_category = $("#par_category").val();
        const par_qset = $("#par_qset").val();

        $.post('../class/apiform.php', {
                "method": "generateQuestion",
                "category": par_category,
                "section": par_section,
                "qset": par_qset
            },

            function(data, status) {
                genareateQuestionFormHTMLElement(data);
                if (data.length > 0) {
                    queryQestionListNameAndId();
                    setSubmitBtnAvailable(true);
                } else {
                    setSubmitBtnAvailable(false);
                }
            });

    }

    async function queryQestionBySecCateGroup() {

        const par_section = $("#par_section").val();
        const par_category = $("#par_category").val();
        const par_qset = $("#par_qset").val();

        $.post('../class/apiform.php', {
                "method": "generateQuestion",
                "category": par_category,
                "section": par_section,
                "qset": par_qset
            },

            function(data, status) {
                genareateQuestionFormHTMLElement(data);
                if (data.length > 0) {
                    setSubmitBtnAvailable(true);
                } else {
                    setSubmitBtnAvailable(false);
                }
            });

    }

    async function queryQestionListNameAndId() {
        const par_section = $("#par_section").val();
        const par_category = $("#par_category").val();

        $.post('../class/apiform.php', {
                "method": "getQuestionNameAndIdByFilter",
                "category": par_category,
                "section": par_section,
            },

            function(data, status) {
                setFormQuestionDropdown(data);
            });

    }

    function setFormQuestionDropdown(data) {

        var html = "";

        html += `<option value="0" selected>ทั้งหมด</option>`;

        data.forEach(el => {
            html += `<option value="${el.id}">${el.name}</option>`;
        });

        const sec_question = $("#par_qset");

        sec_question.html(html);
    }

    function askBeforeLogout() {

        Swal.fire({
            icon: 'question',
            title: 'คุณต้องการออกจากระบบหรือไม่?',
            confirmButtonText: 'ใช่, ออกจากระบบ',
            cancelButtonText: 'ยกเลิก, ย้อยกลับ',
            showCancelButton: true,
            showConfirmButton: true
        }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '../onappview/logout.php';
                }
            })
    }

    function setFormCateDropdown(data) {

        var html = "";

        html += `<option value="0" selected disabled>เลือก...</option>`;

        data.forEach(el => {
            html += `<option value="${el.id}">${el.c_name}</option>`;
        });

        const sec_cate = $("#par_category");

        sec_cate.html(html);

        sec_cate.focus();
    }


    function genareateQuestionFormHTMLElement(dataObj) {

        const div = $("#div_frm_main");

        var htmlContent = "";

        dataObj.forEach(el => {

            htmlContent += `<div class="col-12 w-100">`;

            htmlContent += `<h5 class="ms-3">${el.question_name}</h5>`;

            const q = el.qestion_dt;

            for (let i = 0; i < q.length; i++) {
                let ele = q[i];
                let qNum = i + 1;
                // htmlContent += `
                //     <!-- Question Nawae -->
                //     <h5 class="question_1 fw-bold mb-0 ms-4" id="question_1">${qNum}. ${ele.questiondt_title}</h5>
                //     <div class="ms-5">
                //         <div class="form-check">
                //             <input class="form-check-input" type="radio" name="q[answer][${ele.questiondt_question}][${ele.questiondt}]" id="q_${ele.questiondt}_a_1" value="1" required>
                //             <label class="form-check-label text-success" for="q_${ele.questiondt}_a_1">ไม่พบมีปัญหา</label>
                //         </div>
                //         <div class="form-check">
                //             <input class="form-check-input" type="radio" name="q[answer][${ele.questiondt_question}][${ele.questiondt}]" id="q_${ele.questiondt}_a_2" value="2" required>
                //             <label class="form-check-label text-danger" for="q_${ele.questiondt}_a_2">พบปัญหา</label>
                //         </div>
                //     </div>`;

                htmlContent += `
                        <!-- Question Nawae -->
                        <!-- <h5 class="question_1 fw-bold mb-0 ms-4" id="question_${ele.questiondt}">${qNum}. ${ele.questiondt_title}</h5>  -->
                            <div class="ms-4">
                                <div class="form-check mt-3 d-flex align-items-center">
                                <input class="form-check-input me-2" type="checkbox" id="check_${ele.questiondt}" style="margin-bottom: 5px;" onchange="toggleInput(this)">
                                <label class="form-check-label fw-bold me-2" for="check_${ele.questiondt}">
                                    ${ele.questiondt_title}
                                </label>
                                <span>จำนวน</span>
                                <input type="number" class="mx-2 form-control form-control-sm" style="width: 60px" id="input_${ele.questiondt}" name="q[answer][${ele.questiondt_question}][${ele.questiondt}]" value="" min="1" max="99" disabled>
                                <span>ครั้ง</span>
                            </div>
                        </div>`;
            }

            htmlContent += `<hr>`;
            htmlContent += `</div>`;
        });

        div.html(htmlContent);
    }

    function toggleInput(checkbox) {
        const input = document.getElementById(`input_${checkbox.id.split('_')[1]}`);
        input.disabled = !checkbox.checked; // Disable input if checkbox is unchecked
    }

    function setSubmitBtnAvailable(isAvailable) {
        if (isAvailable) {
            $("#btn_submit").prop("disabled", false);
        } else {
            $("#btn_submit").prop("disabled", true);
        }
    }

    function submitForm() {
        const form = document.getElementById("frm_main");
        const checkboxes = form.querySelectorAll('input[type="checkbox"]');
        const inputs = form.querySelectorAll('input[type="number"]');
        const requiredFields = form.querySelectorAll('input[required]');

        for (let field of requiredFields) {
            if (field.type === 'radio' && !form.querySelector(`input[name="${field.name}"]:checked`)) {
                showAlertToast(`กรุณาเลือกตัวเลือกสำหรับ : ${field.name}`);
                return false;
            }
        }

        let atLeastOneChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
        if (!atLeastOneChecked) {
            showAlertToast("กรุณาทำเครื่องหมายอย่างน้อยหนึ่งช่อง");
            return false;
        }

        for (let checkbox of checkboxes) {
            const associatedInput = document.getElementById(`input_${checkbox.id.split('_')[1]}`);
            if (checkbox.checked && associatedInput.disabled) {
                showAlertToast(`ช่องป้อนข้อมูลสำหรับ ${checkbox.nextElementSibling.textContent} ควรเปิดใช้งานหากมีการทำเครื่องหมายที่ช่องกาเครื่องหมาย`);
                associatedInput.focus();
                return false;
            }
        }

        for (let input of inputs) {
            if (!input.disabled && !input.value.trim()) {
                const label = input.previousElementSibling.previousElementSibling.textContent.trim();
                showAlertToast(`กรุณากรอกจำนวนครั้งที่พบสำหรับ : ${label}`);
                input.focus();
                return false;
            }
        }

        askBeforeSubmit();
    }

    function askBeforeSubmit() {
        Swal.fire({
            showConfirmButton: true,
            showCloseButton: false,
            showCancelButton: true,
            backdrop: true,
            allowOutsideClick: false,
            icon: 'info',
            html: 'คุณแน่ใจหรือไม่ว่าต้องการส่งแบบฟอร์มนี้?',
            confirmButtonText: 'ใช่, ส่งแบบฟอร์มเลย',
            cancelButtonText: 'ไม่, ยกเลิก',
        }).then((result) => {
            if (result.isConfirmed) {
                uploadData();
            }
        });
    }

    async function showAlertToast(msg, icon = "info", position = "center") {
        Swal.fire({
            icon: icon,
            html: msg,
            toast: true,
            position: position,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }

    function fireSwalOnSubmit() {
        Swal.fire({
            imageUrl: "../assets/images/loading-37.webp",
            imageWidth: "77px",
            showConfirmButton: false,
            showCloseButton: false,
            showCancelButton: false,
            backdrop: true,
            allowOutsideClick: false,
        });
    }

    function uploadData() {
        const frm = document.getElementById("frm_main");
        const formData = new FormData(frm);
        formData.append("method", "saveAnswer");
        formData.append("empid", empid);
        formData.append("empcode", empcode);
        formData.append("empname", empname);

        fireSwalOnSubmit();

        const endpoint = "../class/apiform.php";

        $.ajax({
            url: endpoint,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "JSON",
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener(
                    "progress",
                    function(event) {
                        if (event.lengthComputable) {
                            const percentComplete = (event.loaded / event.total) * 100;
                            Swal.getHtmlContainer().querySelector(
                                "#uploadProgressText"
                            ).textContent = `กำลังบันทึกข้อมูล : ${Math.round(percentComplete)}%`;
                        }
                    },
                    false
                );
                return xhr;
            },
            beforeSend: function() {
                Swal.fire({
                    title: "กำลังบันทึกข้อมูล...",
                    html: '<div id="uploadProgressText"></div>',
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
            },
            success: function(response) {
       
                Swal.close(); // Close the progress modal

                const resCode = response.resCode;

                if (resCode == "1") {
                    Swal.fire({
                        icon: "success",
                        title: "บันทึกสำเร็จ",
                        text: response.resMsg,
                        confirmButtonText: "ตกลง",
                    }).then(() => {
                        window.location.href = "v_renderform.php";
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "ไม่สามารถบันทึกได้",
                        text: "โปรดลองอีกครั้ง",
                    });
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                Swal.close(); // Close the progress modal
                Swal.fire({
                    icon: "error",
                    title: "ไม่สามารถบันทึกได้",
                    text: "โปรดลองอีกครั้ง",
                });
                console.error("E rror:", xhr.statusText);
            },
        });
    }

    $(document).ready(function() {
        // console.log("Ready!");
        // getQuestionList();
    });
    </script>