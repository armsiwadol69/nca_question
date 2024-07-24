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
        body {
            /* overflow: hidden; */
            background-color: white !important;
            background: white !important;
        }
        .main-panel-bg-blur {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }

        .list-group-item:hover{
            background-color: rgba(0, 0, 0, 0.4) !important;
            color:white;
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
            </div>
            <div class="bg-gradient w-100" style="max-height:100%;overflow-y:auto;">
                <div class="list-group mt-5">
                <?  
                    $no = 0;
                    foreach ($questionList as $key => $formData) {
                        if( ($no % 2) != 0 ) {
                            $style="style='background-color:rgba(210, 210, 210,0.3);'";
                        } else {
                            $style="";
                        }
                        echo '
                            <li class="list-group-item" '.$style.' onclick="openFormPage(\''.$formData["question"].'\');">
                                <h5>'.$no.' : '.$formData["question_name"].'</h5>
                                <p>'.$formData["question_detail"].'</p>
                            </li>
                        '."\r\n";
                        $no++;
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
    <script>
        function objectToQueryString(obj) {
            return Object.keys(obj).map(key => key + '=' + encodeURIComponent(obj[key])).join('&');
        }

        function closeWindow(){
            window.close();
        }

        function openFormPage(id){
            const obj_prm = <? echo json_encode($ar_prm)?>;
            // let get_prm = objectToQueryString(obj_prm);
            get_prm = `&formId=${id}`;
            console.log(get_prm);
            window.location = 'v_answerForm.php?'+get_prm;
        }
    </script>
<?php include_once 'v_footer.php';?>