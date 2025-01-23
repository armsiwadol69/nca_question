<?php
    header('Content-Type: text/html; charset=utf-8');
    date_default_timezone_set("Asia/Bangkok");
    session_start();
    // $gb_notlogin = true;
    // require_once('../include.inc.php');
    // require_once('../class/class.question.php');

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

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ยืนยันตัวตน | NCA QA</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/sidebarComponents/sidebar.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/aos/aos.css">
    <link rel="stylesheet" href="../assets/DataTables/datatables.min.css">
    <link rel="stylesheet" href="../assets/jquery-ui/jquery-ui.min.css">
    <link rel="stylesheet" href="../assets/jquery-ui/jquery-ui.theme.min.css">
    <link rel="stylesheet" href="../assets/swiper/swiper-bundle.min.css">
    <script src="../assets/sweetalert2/sweetalert2.all.min.js">
    <!-- <script src="../assets/livejs/live.js"></script> 
    -->
    <script src="../assets/axios/axios.min.js"></script>
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

    body {
        overflow: hidden;
        background: rgba(74, 87, 163, 1) !important;
        /* background: white !important; */
        font-family: "Prompt", sans-serif;
        font-weight: 400;
        font-style: normal;
        /* background: rgb(36,37,82) !important; */
        /* background: linear-gradient(180deg, rgba(36,37,82,1) 0%, rgba(74,87,163,1) 100%) !important; */
        background-size: cover;
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

<body class="g-sidenav-show" style="height: 100dvh;">
    <? // include('../v_components/sidebar.php'); ?>
    <main class="main-content position-relative border-radius-lg d-flex justify-content-center align-items-center" style="height: 100%;">
        <? // include('../v_components/navbar.php');?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <h4 class="mb-0 fw-bold">เมนู</h4>
                                    <hr class="my-2">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100" onclick="openCheckQC();">ตรวจสอบความผิด</button>
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-primary w-100 mt-3" onclick="openCheckStat();">บันทึกสถิติ</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <? include('../v_components/script.php');?>

    <script>
    const userId = '<?php echo $_SESSION['credential']["empid"]; ?>';
    const userName = '<?php echo $_SESSION['credential']["empname"]; ?>';

    function openCheckQC() {

        const url = `../view/v_formlist.php?formApp_userId=${userId}&formApp_username=${userName}`;

        window.location = url;

    }

    function openCheckStat() {

        const url = `../onappview/v_renderform.php`;

        window.location = url;

    }
    </script>

</body>

</html>