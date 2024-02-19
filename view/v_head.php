<?php 
session_start();
$gb_notlogin = true;
$version = strtotime("now");
require "../include.inc.php";
require "../phpfunc/customfunction.php";
if(!isset($_SESSION['userData'])){
    header('location: ../phpfunc/logout.php');
    exit();   
}

if(time() - $_SESSION['userData']['loginTime'] >= 3600){
    header('location: ../phpfunc/logout.php');
    exit();   
}else{
    $_SESSION['userData']['loginTime'] = time();
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCA Question System</title>
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css?v=<?php echo $version; ?>">
    <link rel="stylesheet" href="../assets/sidebarComponents/sidebar.css?v=<?php echo $version; ?>">
    <link rel="stylesheet" href="../assets/css/main.css?v=<?php echo $version; ?>">
    <link rel="stylesheet" href="../assets/aos/aos.css?v=<?php echo $version; ?>">
    <link rel="stylesheet" href="../assets/DataTables/datatables.min.css?v=<?php echo $version; ?>">
    <!-- <link rel="stylesheet" href="../assets/jquery-ui/jquery-ui.min.css"> -->
    <!-- <link rel="stylesheet" href="../assets/jquery-ui/jquery-ui.theme.min.css"> -->
    <link rel="stylesheet" href="../assets/swiper/swiper-bundle.min.css?v=<?php echo $version; ?>">
    <link rel="stylesheet" href="../assets/bootstrap-datepicker/css/bootstrap-datepicker.min.css?v=<?php echo $version; ?>">
    <link href="../assets/fontawesome-free-6.2.1-web/css/all.css?v=<?php echo $version; ?>" rel="stylesheet"/>
    <script src="../assets/sweetalert2/sweetalert2.all.min.js?v=<?php echo $version; ?>"></script>
    <!-- <script src="../assets/livejs/live.js"></script> -->
    <script src="../assets/axios/axios.min.js?v=<?php echo $version; ?>"></script>

</head>
<body>

