<?php
    session_start();
    if(isset($_SESSION["userData"])){
        header('location: list_question.php');
    }
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCA Reward System</title>
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
        }
        .main-panel-bg-blur {
            background-color: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
</style>
</head>
<body>
<div class="row d-flex justify-content-center align-items-center" style="height:100dvh;">
    <div class="col-xl-4 col-lg-4 cold-md-6 col-sm-12">
        <div class="card text-center shadow rounded-4 main-panel-bg-blur ">
            <div class="card-body">
                <div class="w-100 text-center">
                    <img src="../assets/images/logo.png" alt="ncaLogo">
                    <p class="mt-1">Question&Answer System</p>
                    <hr>
                </div>
                <form class="text-start" method="post" action="../phpfunc/auth.php">
                    <div class="mb-3">
                        <label for="par_username" class="form-label">ชื่อผู้ใช้</label>
                        <input type="text" class="form-control" id="par_username" name="par_username" aria-describedby="username">
                    </div>
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">รหัสผ่าน</label>
                        <input type="password" class="form-control" id="par_password" name="par_password" aria-describedby="password">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">เข้าสู่ระบบ</button>
                </form>
                <div class="w-100 mt-5 text-center">
                    <!-- <a class="text-decoration-none text-dark" href="#" onclick="forgetPassword();">Forgotten password?!</a> -->
                </div>

                <?php
                $loginRtn = $_GET['loginrtn'];
                if($loginRtn == "1"){
                    echo '
                    <div class="alert alert-info text-center shadow-sm mt-5" role="alert">
                        <i class="bi bi-info-circle"></i> ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง
                    </div>
                    ';
                };
                if($loginRtn == "2"){
                    echo '
                    <div class="alert alert-info text-center shadow-sm mt-5" role="alert">
                        <i class="bi bi-info-circle"></i> คุณไม่มีสิทธิในการเข้าใช้งานระบบนี้
                    </div>
                    ';
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    function forgetPassword(){
        Swal.fire({
  title: '<strong>Did you forget?</strong>',
//   icon: 'info',
  html:`
  <iframe width="500" height="300" src="https://www.youtube.com/embed/dQw4w9WgXcQ?si=KS2n8b8QwG9ILrw_&amp;controls=0&?autoplay=1" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
  `,
  showCloseButton: true,
  closeButtonText : `I see...`
})
    }
</script>
<?php include_once 'v_footer.php';?>