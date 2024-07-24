<?php
    header('Content-Type: text/html; charset=utf-8');
    date_default_timezone_set("Asia/Bangkok");
    $gb_notlogin = true;
    require_once('../include.inc.php');

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

    //DEFIND
    $go_ncadb = new ncadb();

    //GET ALL Question Type
    $sql = "SELECT tb_category.* FROM tb_category WHERE m_questiontype_active = '1' ORDER BY m_questiontype DESC";
    $rawData = $go_ncadb->ncaretrieve($sql, "question");
    $arrayResult = ncaArrayConverter($rawData);

    //GET ALL Question Group
    $sqlGroup = "SELECT tb_questiongroup.* FROM tb_questiongroup WHERE questiongroup_active = '1' ORDER BY questiongroup DESC";
    $rawDataGroup = $go_ncadb->ncaretrieve($sqlGroup, "question");
    $arrayResultGroup = ncaArrayConverter($rawDataGroup);

    //GET ALL Question Type/Mode Whatever
    $sqlType = "SELECT tb_questionmode.* FROM tb_questionmode WHERE questionmode_active = '1' ORDER BY questionmode DESC";
    $rawDataType = $go_ncadb->ncaretrieve($sqlType, "question");
    $arrayResultType = ncaArrayConverter($rawDataType);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question List</title>
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
        background-color: white !important;
        background: white !important;
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
    </style>
</head>

<body>
    <div class="row d-flex justify-content-center align-items-center g-0" style="height:100dvh;">
        <div class="col-12 h-100">
            <div class=" bg-dark w-100 text-center position-absolute" style="z-index:3;">
                <!-- <h6 class="mt-2 text-white fw-bold">Survey</h6> -->
                <div class="form-floating">
                    <select class="form-select rounded-0" id="par_category" onchange="q_category = this.value;getQuestionList();" aria-label="question type">
                        <option value="0" selected>ทั้งหมด</option>
                        <?php
                            foreach($arrayResult as $value) {
                                echo '<option value="'.$value["m_questiontype"].'">'.$value["m_questiontype_name"].'</option>';
                            }
                        ?>
                    </select>
                    <label for="floatingSelect">หมวดหมู่</label>
                </div>
                <div class="form-floating">
                    <select class="form-select rounded-0" id="par_group" onchange="q_group = this.value;getQuestionList();"  aria-label="question group">
                        <option value="0" selected>ทั้งหมด</option>
                        <?php
                            foreach($arrayResultGroup as $value) {
                                echo '<option value="'.$value["questiongroup"].'">'.$value["questiongroup_name"].'</option>';
                            }
                        ?>
                    </select>
                    <label for="floatingSelect">กลุ่ม</label>
                </div>
                <div class="form-floating">
                    <select class="form-select rounded-0" id="par_type" onchange="q_type = this.value;getQuestionList();" aria-label="question type/mode">
                        <option value="0" selected>ทั้งหมด</option>
                        <?php
                            foreach($arrayResultType as $value) {
                                echo '<option value="'.$value["questionmode"].'">'.$value["questionmode_name"].'</option>';
                            }
                        ?>
                        
                    </select>
                    <label for="floatingSelect">ประเภท</label>
                </div>
            </div>
            <div class="bg-gradient w-100 h-100" style="margin-top:10.75rem !important;max-height:100%;overflow-y:scroll;">
                <div class="list-group overflow-hidden" id="questionList">
                </div>
            </div>
        </div>
    </div>
    <script>
    let q_category = "0";
    let q_group = "0";
    let q_type = "0";

    function objectToQueryString(obj) {
        return Object.keys(obj).map(key => key + '=' + encodeURIComponent(obj[key])).join('&');
    }

    function closeWindow() {
        window.close();
    }

    function openFormPage(id) {
        const obj_prm = <?php echo json_encode($ar_prm); ?>;
        // let get_prm = objectToQueryString(obj_prm);
        get_prm = `&formId=${id}`;
        console.log(get_prm);
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
                // console.log(`STATUS: ${status}`);
                // console.log(data);
                Swal.close();
                genareateQuestionList(data);
            });
    }

    function genareateQuestionList(dataObj){

        const elementTarget = document.getElementById("questionList");
        
        var html = "";

        if(!dataObj){
            console.log('ไม่มีรายการที่ตรงกับ หมวดหมู่ กลุ่ม และประเภที่คุณเลือก');
            html = `<h5>ไม่มีรายการที่ตรงกับ หมวดหมู่ กลุ่ม และประเภที่คุณเลือก</h5>`;
            elementTarget.innerHTML = html;
            return;
        }

        dataObj.forEach(element => {
            html += `<li class="list-group-item" onclick="openFormPage('${element.question}');">
                        <h5>${element.question_name}</h5>
                        <p>${element.question_detail}</p>
                    </li>`;
        });

        elementTarget.innerHTML = html;
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

    $(document).ready(function() {
        console.log("Ready!");
        getQuestionList();
    });
    
    </script>