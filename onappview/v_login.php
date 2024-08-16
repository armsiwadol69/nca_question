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

    if(isset( $_SESSION["credential"]) && !empty( $_SESSION["credential"]) ){
        echo "<script>window.location.href = 'v_renderform.php';</script>";
        exit();
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
    <script src="../assets/sweetalert2/sweetalert2.all.min.js"></>
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
    <div class="container-fluid p-0 d-flex justify-content-center align-items-center" style="height: 100dvh">
        <div class="col-lg-4 col-md-8 col-ms-12">
            <div class="card shadow-sm rounded-0">
                <div class="card-body">
                    <form action="auth.php" method="post">
                        <input type="hidden" name="method" value="login">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control" id="par_username" name="par_username" placeholder="ชื่อผู้ใช้" required>
                                    <label for="floatingInput">ชื่อผู้ใช้</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating">
                                    <input type="password" class="form-control" id="par_password" name="par_password" placeholder="รหัสผ่าน" required>
                                    <label for="floatingPassword">รหัสผ่าน</label>
                                </div>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-primary w-100" type="submit">เข้าสู่ระบบ</button>
                            </div>

                            <?
                                if($ar_prm["rtn"] == "1"){
                                    echo "
                                        <div class=\"col-12 mt-5\">
                                            <div class=\"alert alert-warning w-100 text-center fw-bold\" role=\"alert\">
                                                ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
                                            </div>
                                        </div>
                                    ";
                                }
                            ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
<script>

</script>

</html>