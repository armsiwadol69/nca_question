<?php 
    include_once '../phpfunc/loadingIndicator.php';
?>
<div class="d-flex" id="wrapper">
    <div class="border-end text-white shadow" id="sidebar-wrapper">
        <div class="sidebar-heading border-bottom text-center mt-3">
            <!-- <a class="navbar-brand" href="v_dashboard.php"> -->
            <a class="navbar-brand" href="list_question.php">
                <img src="../assets/images/logo.png" class="ms-3 mt-2 w-75">
            </a>
            <!-- <p class="text-primary mt-2">Inquiry system</p> -->
        </div>

        <div class="list-group list-group-flush d-flex">
            <a class="list-group-item list-group-item-action text-left pe-none"></a>
            <a class="list-group-item list-group-item-action p-3 text-left" href="list_question.php" id="listquestion"><i class="bi bi-hourglass-split"></i> รายการคำถาม</a>
            <a class="list-group-item list-group-item-action text-left pe-none"></a>
        </div>
        <div class="sidebar-footer list-group list-group-flush d-flex">
            <!-- User login -->
            <button class="list-group-item list-group-item-action list-group-item-secondary p-1 text-center pe-none" style=""><i class="bi bi-person-circle"></i> <?php echo $_SESSION['userData']['userdspms']?></button>
            <!-- Log out -->
            <button class="list-group-item list-group-item-action list-group-item-danger p-1 text-center my-3" onclick="clickToLogout();"><i class="bi bi-box-arrow-left"></i> ออกจากระบบ</button>
            <!-- <a class="list-group-item list-group-item-action list-group-item-secondary user-select-none p-1 text-center">v1.0</a> -->
        </div>
    </div>
    <!-- Page content wrapper-->
    <div id="page-content-wrapper">
        <!-- Top navigation-->
        <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom shadow-sm">
            <div class="container-fluid">
                <!-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button> -->
                <button class=" btn btn-outline-primary" id="sidebarToggle"><i class="bi bi-list"></i></button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    </ul>
                    <div class="d-flex">
                        <!-- <button class="btn btn-outline-primary d-block d-xl-block d-xxl-block d-xxl-none me-2"
                                id="sidebarToggle">MENU</button> -->
                        <button class="btn btn-outline-danger" onclick="clickToLogout();"> <i class="fa-solid fa-power-off"></i>ออกจากระบบ</button> <i class="fa-solid fa-power-off"></i>
                    </div>  
                </div>
            </div>
        </nav>
        <!-- Page content-->
        <div class="container-fluid" style="padding-right: 14px; padding-left: 14px;">
            <main class="main-content" style="height: 94dvh; overflow-x:hidden; overflow-y:auto;">