<?php
    // session_start();
    // if(isset($_SESSION["userData"])){
    //     header('location: list_question.php');
    // }
    // header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCA QC</title>
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
            overflow: hidden;
            background-color: white !important;
            background: white !important;
        }
        .main-panel-bg-blur {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
</style>
</head>
<body>
    <div class="row d-flex justify-content-center align-items-center" style="height:100dvh;">
        <div class="col-12 text-center" <? if($_GET["result"] == "0") echo "hidden"; ?>>
            <h1 class="text-success display-1">บันทึกสำเร็จ</h1>
            <h6>ท่านสามารถออกจากหน้านี้ได้</h6>
            <!-- <button type="button" class="btn btn-success w-25 mt-5" onclick="closeWindow();"><i class="bi bi-x-lg"></i> ปิด</button> -->
        </div>
        <div class="col-12 text-center" <? if($_GET["result"] == "1") echo "hidden"; ?>>
            <h1 class="text-danger display-1">บันทึกไม่สำเร็จ</h1>
            <h6>กรุณาลองใหม่อีกครั้ง</h6>
        </div>
    </div>
    <script>
        function closeWindow(){
            window.close();
        }
    </script>
<?php include_once 'v_footer.php';?>