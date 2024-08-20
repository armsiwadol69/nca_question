<?php
session_start();
include_once 'v_head.php';
include_once 'v_sidebar_start.php';
// require_once '../class/curlManageData.php';
?>
<link rel="stylesheet" href="../assets/daterangepicker/daterangepicker.css">
<div class="row">
    <div class="col-12">
        <div class="w-100 d-flex mt-2">
            <h3 class="me-auto mt-1">รายงานสถิติ</h3>
            <div class="input-group shadow-sm h-100 w-25">
                <span class="input-group-text datepicker-icon"><i class="bi bi-calendar-range"></i></span>
                <input type="text" class="form-control form-control-lg fix-input-group rounded-1" style="font-size: 1.1rem;background-color: #fff !important;" readonly="readonly" name="daterange"
                    id="daterange" value="">
                <div></div>
            </div>
        </div>
        <hr>
        <div class="row g-2 w-100" style="height: 85dvh; overflow-y: scroll;">
            <div class="col-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        รายงานสถิติโดยรวม 4 อันดับแรกของแต่ละหน่วยงาน
                    </div>
                    <div class="card-body">
                        <div>
                            <canvas id="chart_overall" style="max-height: 30dvh;"></canvas>
                        </div>
                    </div>
                </div>
            </div>     
            <div class="col-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        จำนวนปัญหาแยกตามหน่วยงาน (เรียงจากมากไปน้อย)
                    </div>
                    <div class="card-body">
                        <div>
                            <canvas id="chart_totalByDepSec" style="max-height: 30dvh;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-12 col-md-12 col-sm-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        รายงานสถิติแบ่งตามหน่วยงาน
                    </div>
                    <div class="card-body">
                        <?
                            require_once '../class/class.question.php';
                            $cmd = new question();
                            $depsecraw = $cmd->getSectionData();
                            $depsec = $depsecraw["data"];
                            // echo "<pre>";
                            // print_r($depsec);
                            // echo "</pre>";
                            ?>
                        <div class="form-floating w-100 shadow-sm">
                            <select class="form-select" id="par_depsec" name="par_depsec" aria-label="หน่วยงาน">
                                <option value="0" selected>เลือก...</option>
                                <?
                                foreach ($depsec as $key => $value) {
                                    echo "<option value='" . $value["section_id"] . "'>" . $value["section_name"] ." (".$value["section_nameen"].")". "</option>";
                                }
                                ?>
                            </select>
                            <label for="floatingSelect">หน่วยงาน</label>
                        </div>
                        <hr>
                        <div>
                            <canvas id="chart_bydepsec" style="max-height: 30dvh;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
    include_once 'v_sidebar_end.php';
    include_once 'v_footer.php';
?>
        <script src="../assets/momentjs/moment.js"></script>
        <script src="../assets/daterangepicker/daterangepicker.js"></script>
        <script src="../assets/chartjs/chart.umd.min.js"></script>
        <script src="../assets/chartjs/chartjs-plugin-datalabels.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js"
            integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="../assets/js/report_chart.js"></script>
        <script>
        //on document ready
        $(function() {
            handleScriptLoad();
        })
        </script>