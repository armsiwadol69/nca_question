<?php
    header('Content-Type: text/html; charset=utf-8');
    date_default_timezone_set("Asia/Bangkok");
    $gb_notlogin = true;
    require_once('../include.inc.php');

    // include_once('../phpfunc/curd.php');
    
    function curlGetNca($url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function curlPostNca($url, $data)
{   
    //$data = json_decode(json_encode($data), true);
    
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    //$data = json_encode($data);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
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

    #GET FORM LIST DATA
    $go_ncadb = new ncadb();

    if(!empty($ar_prm["busnumber"]) || true){
        // $busnumber = $ar_prm["busnumber"];

    $sql = "SELECT * 
    FROM tb_question 
    -- WHERE question_busrecordnumber LIKE '%$busnumber%'
    ORDER BY question DESC
    ";

    $arr = $go_ncadb->ncaretrieve($sql, "question");

    $questionList = ncaArrayConverter($arr);
    // echo "<pre>";
    // print_r($questionList);
    // echo "</pre>";

    }else{
        $result = array();
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCA QC : FORM LIST</title>
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
    </style>
</head>

<body>
    <div class="row d-flex justify-content-center align-items-center g-0" style="height:100dvh;">
        <div class="col-12 h-100">
            <div class=" bg-dark w-100 text-center position-absolute" style="z-index:3;">
                <h1 class="mt-2 text-white">เลือกฟอร์ม</h1>
                <form action="#" method="post" id="q_filter"></form>
                <div class="row g-0">

                    <div class="col-6">
                        <?
                            // Department
                            $getDepartment = json_decode(curlGetNca("http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getsection"),true);
                            // print_r($getDepartment["data"]);
                        ?>
                        <div class="form-floating">
                            <select class="form-select rounded-0" id="selectDepartment" name="selectDepartment" aria-label="สายงาน">
                                <option value="" selected>Select...</option>
                                <?
                                foreach ($getDepartment["data"] as $rk => $vk) {
                                    $d_id = $vk["section_id"];
                                    $d_name = $vk["section_name"];
                                    echo "<option value='$d_id'>$d_name</option>";
                                }
                                ?>
                            </select>
                            <label for="floatingSelect">สายงาน</label>
                        </div>
                    </div>
                    <div class="col-6">
                        <?
                            // Category
                            $cate_sql = "SELECT tb_questioncategories.* FROM tb_questioncategories WHERE tb_questioncategories.questioncategories_active = '1';";
                            $categoryData = $go_ncadb->ncaretrieve($cate_sql, "question");
                            $cd_utf8 = ncaArrayConverter($categoryData);
                        ?>
                        <div class="form-floating">
                            <select class="form-select rounded-0" id="selectCategory" name="selectCategory" aria-label="หมวดคำถาม" disabled>
                                <!-- <option value="" selected>Select...</option> -->
                                <?
                              foreach ($cd_utf8 as $key => $value) {
                                $c_id = $value["questioncategories"];
                                $c_name = $value["questioncategories_name"];
                                $selected = ($c_id == 29) ? 'selected' : '';
                                echo "<option value='$c_id' $selected>$c_name</option>";
                            }
                            
                                ?>
                            </select>
                            <label for="floatingSelect">หมวดคำถาม</label>
                        </div>
                    </div>
                    <div class="col-6" hidden>
                        <?
                        //Get Form Type
                        $formTypeSql = "SELECT tb_questionmode.* FROM tb_questionmode WHERE tb_questionmode.questionmode_active = '1' ORDER BY tb_questionmode.questionmode ASC;";
                        $formTypeData = $go_ncadb->ncaretrieve($formTypeSql, "question");
                        $ftd_utf8 = ncaArrayConverter($formTypeData);
                        // print_r($ftd_utf8);
                        ?>
                        <div class="form-floating">
                            <select class="form-select rounded-0" id="selectFormType" name="selectFormType" aria-label="ประเภท">
                                <option value="" selected>Select...</option>
                                <?
                                    foreach ($ftd_utf8 as $key => $value) {
                                       $ftd_id = $value["questionmode"];
                                       $ftd_name = $value["questionmode_name"];
                                       echo "<option value='$ftd_id'>$ftd_name</option>";
                                    }
                                ?>
                            </select>
                            <label for="floatingSelect">ประเภท</label>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <div class="bg-gradient w-100" style="max-height:100%;overflow-y:auto;">
                <div class="row" id="result" style="margin-top: 150px;">

                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.slim.min.js" integrity="sha256-kmHvs0B+OpCW5GVHUNjv9rOmY0IvSIRcf7zGUDTDQM8=" crossorigin="anonymous"></script>
    <script>
    function objectToQueryString(obj) {
        return Object.keys(obj).map(key => key + '=' + encodeURIComponent(obj[key])).join('&');
    }

    function closeWindow() {
        window.close();
    }

    function openFormPage(id) {
        const obj_prm = <?php echo json_encode($ar_prm) ?>;
        // let get_prm = objectToQueryString(obj_prm);
        get_prm = `&formId=${id}`;
        console.log(get_prm);
        window.location = 'v_answerForm.php?' + get_prm;
    }

    async function getQuestionList() {

        const department = $("#selectDepartment").val();
        const category = $("#selectCategory").val();
        const formType = $("#selectFormType").val();



        const params = {
            selectDepartment: department,
            selectCategory: category,
            selectFormType: formType
        };

        console.log(params);

        $.ajax({
            type: "POST",
            url: '../phpfunc/emerapi.php',
            data: params,
            // dataType: "JSON",
            success: function(data) {
                console.log(data);
                genBox(data);
            },
            error: function(xhr, status, error) {
                console.error("An error occurred: ", status, error);
                // showAlertToast(`พบข้อผิดพลาดในการค้นหา + ${error}`, "center");
            },
        });
    }

    $('#selectDepartment, #selectCategory, #selectFormType').bind('change', function() {
        getQuestionList();
    });

    function genBox(data) {

        
        if (data.length == 0) {
            const html = `
                <div class="col-12">
                    <div class="alert alert-warning w-100" role="alert">
                    ไม่พบข้อมูล
                    </div>
                </div>
            `;
            document.getElementById("result").innerHTML = html;
            return;
        }

        let html = "";
        let gid = "";

        var departName = $('#selectDepartment option:selected').html();
        var fCate = $('#selectCategory option:selected').html();


        html += `<div class="col-12">`;
        html += `<div class="card"">
                <div class="card-body">
                    <h5 class="card-title">แบบ${fCate}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">${departName}</h6>
                    <ul>
                `;
        data.forEach(el => {
            html += `<li><p class="card-text fw-bold">${el.question_name}</p></li>`;
            gid += ',' + el.question;
        });
        html += `</ul>
                <a href="v_askFormV2.php?id=${gid}" class="card-link mt-5"><button class="btn btn-sm w-100 btn-primary mt-5">เลือก</button></a>
                </div>
            </div>`;
        document.getElementById("result").innerHTML = html;
    }
    </script>

    <?php include_once 'v_footer.php';?>