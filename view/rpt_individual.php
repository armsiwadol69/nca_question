<?php
session_start();

require_once ("../class/class.curlmanagedata.php");

include_once 'v_head.php';
include_once 'v_sidebar_start.php';


$curlNcaData = new curlManageData();

?>
<style>
.sorting_disabled {
    text-align: center !important;
}

td {
    text-align: center;
}

.textcenterd {
    text-align: center;
}
</style>

<div class="row">
    <div class="col-12">
        <div class="w-100 d-flex mt-2">
            <h3 class="me-auto mt-1 mb-0">รายงานสถิติความผิดรายบุคคล</h3>
        </div>
        <hr class="mt-2">
    </div>
    <div class="col-12">
        <div class="row g-2">
            <div class="col-xl-4 col-lg-4 col-md-4 col-sm-12 col-12">
                <div class="card w-100 h-100 shadow-sm">
                    <div class="card-header">
                        ระบุข้อมูลที่ต้องการ
                    </div>
                    <div class="card-body p-0">

                        <div class="my-2 mx-2">
                            <div class="input-group input-group-sm float-end w-100">
                                <span class="input-group-text datepicker-icon text-primary"><i class="bi bi-calendar-range"></i></span>
                                <input type="text" class="form-control form-control-sm border ps-2"
                                    style="font-size: 0.8rem;background-color: #fff !important;border-radius: 0rem .5rem .5rem 0rem" readonly="readonly" name="daterange" id="daterange" value=""
                                    onchange="">
                                <div>

                                </div>
                            </div>
                        </div>

                        <div class="btn-group btn-group-sm p-2 w-100" role="group" id="btn-group-check-type" aria-label="Basic radio toggle button group">
                            <input type="radio" class="btn-check btn-check-type-group" name="check_type" id="check_type_1" value="1" autocomplete="off" required>
                            <label class="btn btn-outline-primary border border-primary" for="check_type_1">บุคคล</label>

                            <input type="radio" class="btn-check btn-check-type-group" name="check_type" id="check_type_2" value="2" autocomplete="off" required>
                            <label class="btn btn-outline-primary border border-primary" for="check_type_2">สาขา</label>

                            <input type="radio" class="btn-check btn-check-type-group" name="check_type" id="check_type_3" value="3" autocomplete="off" required>
                            <label class="btn btn-outline-primary border border-primary disabled" for="check_type_3">รถ</label>
                        </div>
                        <div class="frm-check-type-1 w-100 p-2" id="frm-check-type-1" hidden>
                            <!-- emp -->
                            <input type="hidden" name="empid" id="empid" value="" required>
                            <input type="hidden" name="empcodestring" id="empcodestring" value="" required>
                            <div class="row g-2">
                                <div class="col-xl-10 col-lg-10 col-md-9 col-sm-8">
                                    <div class="form-floating">
                                        <input class="form-control rounded-0 input-t-1" id="empcode" name="empcode" type="text" placeholder="รหัสพนักงาน" />
                                        <label for="รหัสพนักงาน">รหัสพนักงาน<span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-xl-2 col-lg-2 col-md-3 col-sm-2">
                                    <button class="btn btn-sm btn-info btn-emp-search w-100 rounded-0 h-100" type="button" onclick="searchEmp();" id="btn-emp-search">ค้นหา</button>
                                </div>
                            </div>
                            <div class="form-floating mt-2">
                                <input class="form-control rounded-0 input-t-1" id="empname" name="empname" type="text" placeholder="ชื่อพนักงาน" readonly />
                                <label for="ชื่อพนักงาน">ชื่อพนักงาน</label>
                            </div>
                        </div>
                        <div class="frm-check-type-2 w-100 p-2" id="frm-check-type-2" hidden>
                            <!-- outlet -->
                            <div class="form-floating">
                                <select class="form-select rounded-0 input-t-2" id="outletId" name="outletId" aria-label="สาขา">
                                    <option value="" disabled selected>เลือก...</option>
                                    <?
                                        $sec_outletlist = $curlNcaData->getoutlet();
                                        foreach ($sec_outletlist["data"] as $rk => $rv) {
                                            echo '<option value="'.$rv['outlet_id'].'">'.$rv['outlet_nameth'].' ('.$rv['outlet_nmth'].')'.'</option>';
                                        }
                                    ?>
                                </select>
                                <label for="สาขา">สาขา</label>
                            </div>
                        </div>
                        <div class="frm-check-type-3 w-100 p-2" id="frm-check-type-3" hidden>
                            <h5 class="mb-2">ข้อมูลรถ</h5>
                            <!-- bus -->
                            <div class="row g-1">
                                <?php //echo arrayToInputsBootstrap($_GET);?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-8 col-md-8 col-sm-12">
                <div class="card w-100 shadow-sm" style="height:35dvh;">
                    <div class="card-header">
                        แผนผังจำนวนความผิดเรียงตามหัวข้อ
                    </div>
                    <div class="card-body">
                        <div class="w-100 h-100">
                            <canvas id="chart-individual"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card h-100 w-100 shadow-sm">
                    <div class="card-header">
                        ประวัติความผิด
                    </div>
                    <div clas="card-body">
                        <div class="col-12">
                            <div class="table-responsive w-100 p-1">
                                <table id="table_history" class="table table-bordered table-striped shadow-sm w-100">
                                    <thead class="text-bg-primary" style="vertical-align: middle;">
                                        <tr>
                                            <td width="50px;">ลำดับ</td>
                                            <td>วันที่บันทึก</td>
                                            <td>หัวข้อ</td>
                                            <td>ระดับความผิด</td>
                                            <td>น้ำหนัก</td>
                                        </tr>
                                    </thead>
                                    <tbody class="align-middle text-start"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

    include_once 'v_sidebar_end.php';
    include_once 'v_footer.php';

?>

    <script>
    $(document).ready(function() {
        initializeDateRange();
        addEventToRadioCheckType();
        initChart();
        initHistoryTable();
        handleScriptLoad();
    })

    function addEventToRadioCheckType() {
        $(".btn-check-type-group").bind("change", function(event) {
            const selectedValue = $(this).val();
            pickedCheckType(selectedValue);
            isSeeAll = 0;
            clearEmpDt();
        });
    }

    function clearEmpDt() {
        $("#empid").val("");
        $("#empname").val("");
        $("#empcode").val("");
        isSeeAll = 0;
    }

    function pickedCheckType(type_v) {

        const el_input_type_1 = $(".input-t-1");
        const el_input_type_2 = $(".input-t-2");
        const el_input_type_3 = $(".input-t-3");

        const div_check_type_1 = $("#frm-check-type-1");
        const div_check_type_2 = $("#frm-check-type-2");
        const div_check_type_3 = $("#frm-check-type-3");

        el_input_type_1.prop("disabled", true).prop("required", false);
        el_input_type_2.prop("disabled", true).prop("required", false);
        el_input_type_3.prop("disabled", true).prop("required", false);

        div_check_type_1.attr("hidden", true);
        div_check_type_2.attr("hidden", true);
        div_check_type_3.attr("hidden", true);

        if (type_v == "1") {
            el_input_type_1.prop("disabled", false);
            div_check_type_1.attr("hidden", false);
        } else if (type_v == "2") {
            el_input_type_2.prop("disabled", false);
            div_check_type_2.attr("hidden", false);
        } else if (type_v == "3") {
            el_input_type_3.prop("disabled", false);
            div_check_type_3.attr("hidden", false);
        }

    }

    async function searchEmp() {
        const pickedDept = $("#sec_section").val();

        if (pickedDept == "0") {
            showAlertToast("กรุณาเลือกแผนกก่อน", "info", "center");
            return;
        }

        const empCode = $("#empcode").val();

        if (!empCode) {
            showAlertToast("กรอกรหัสพนักงาน", "info", "center");
            return;
        }

        showLoadingOnQuery();

        const url = "../class/apiproxy.php?method=getEmpData&empCode=" + empCode;

        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            Swal.close();

            const json = await response.json();
            if (json.respCode == "1") {
                const empDept = json.data[0]["emp_sec"];
                setEmpDt(json.data[0]);
                showAlertToast("ค้นหาสำเร็จ", "success", "bottom");
                getReport();
            } else {
                showAlertToast("ไม่พบข้อมูลพนักงาน", "info", "center");
                clearEmpDt();
            }
        } catch (error) {
            console.error(error.message);
        }
    }

    async function showLoadingOnQuery() {
        Swal.fire({
            title: "รอซักครู่...",
            html: "กำลังเรียกข้อมูลที่จำเป็น",
            toast: true,
            position: "bottom",
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });
    }

    function setEmpDt(data) {
        console.log(data);
        $("#empcodestring").val(`${data.empcd}`);
        $("#empid").val(`${data.empicms_id}`).trigger('change');
        $("#empname").val(`${data.emp_firstname} ${data.emp_lastname}`);
    }

    async function showAlertToast(msg, icon = "info", position = "center") {
        Swal.fire({
            icon: icon,
            html: msg,
            toast: true,
            position: position,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });
    }

    let chart;

    let dataOfChart = {
        labels: [],
        datasets: [{
            label: "จำนวนครั้งที่ผิดในหัวข้อ",
            data: [],
            backgroundColor: [
                "rgb(255, 99, 132)",
                "rgb(54, 162, 235)",
                "rgb(255, 205, 86)",
                "rgb(75, 192, 192)",
                "rgb(153, 102, 255)",
                "rgb(255, 159, 64)",
                "rgb(201, 203, 207)",
                "rgb(255, 99, 71)",
                "rgb(124, 252, 0)",
                "rgb(173, 216, 230)",
                "rgb(238, 130, 238)",
                "rgb(240, 230, 140)",
                "rgb(255, 69, 0)",
                "rgb(30, 144, 255)",
            ],
            hoverOffset: 20,
            offset: 10,
        }, ],
    };

    async function initChart() {

        var optionsBar = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    stacked: true,
                    offset: true,
                    ticks: {
                        align: "center",
                    },
                },
                y: {
                    // stacked: true,
                    beginAtZero: true,
                    grace: "20%",
                    ticks: {
                        precision: 0
                    }
                },
            },
            plugins: {
                tooltip: {
                    mode: "index", // Show all items at the same index
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            var label = context.dataset.label || "";
                            if (label) {
                                label += ": ";
                            }
                            label += context.parsed.y.toLocaleString("th-TH");
                            return label;
                        },
                    },
                },
            },
        };

        chart = new Chart(
            document.getElementById('chart-individual'), {
                type: 'bar',
                options: optionsBar,
                data: dataOfChart
            }
        );

    }

    function convertRawData(data) {
        var result = {
            labels: [],
            datasets: [],
        };

        data.forEach(function(item) {
            result.labels.push(item.questiondt_title);
            result.datasets.push(parseInt(item.count));
        });

        return result;
    }

    async function setChartData(data) {

        const objChartData = convertRawData(data);

        dataOfChart.labels = objChartData.labels;

        dataOfChart.datasets[0].data = objChartData.datasets;

        chart.update();

    }

    let startDate = moment().startOf("day").format("YYYY-MM-DD");

    let endDate = moment().endOf("day").format("YYYY-MM-DD");

    async function getReport() {

        const searchType = $("input[name='check_type']:checked").val();

        let ref = "";

        if (searchType == "1") {

            ref = $("#empcodestring").val();

        } else if (searchType == "2") {

            ref = $("#outletId").val();

        } else if (searchType == "3") {

            //NOT NOW
        }

        const apiEndpoint = "../class/apiReport.php";

        try {

            showLoadingOnQuery();

            let formData = new FormData();

            formData.append('method', 'getReport');
            formData.append('type', searchType);
            formData.append('ref', ref);
            formData.append('startDate', startDate);
            formData.append('endDate', endDate);


            const response = await fetch(apiEndpoint, {
                method: "POST",
                body: formData
            });

            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            Swal.close();

            const json = await response.json();

            const respCode = json.resCode;

            if (respCode != "1") {
                return;
            }

            setChartData(json.data);

            tableData = json.allmistake;

            tableHistory.clear().rows.add(tableData).draw();

        } catch (error) {

            console.error(error.message);

        }


    }

    let tableHistory;

    let tableData = [];

    function initHistoryTable() {

        const tableDom = "<'row mb-2'>" + // Buttons
            "<'row'<'col-2'l><'col-6'B><'col-4'f>>" + // Length and Filtering
            "<'row'<'col-12't>>" + // Table
            "<'row'<'col-6'i><'col-6'p>>" + // Information and Pagination
            "<'clear'>";

        const dataTableSettings = {
            emptyTable: "ไม่มีรายการ",
            info: "รายการที่ _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            lengthMenu: "แสดง _MENU_ รายการ",
            infoEmpty: "ไม่มีข้อมูล",
            loadingRecords: "โปรดรอซักครู่ กำลังเรียกขอมูล...",
            infoFiltered: "ค้นหาจาก _MAX_ รายการ",
            search: "ค้นหา :",
            paginate: {
                first: "หน้าแรก",
                last: "หน้าสุดท้าย",
                next: "ถัดไป",
                previous: "ก่อนหน้า",
            },
        };

        tableHistory = $("#table_history").DataTable({
            aLengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100],
            ],
            iDisplayLength: 25,
            ordering: false,
            language: dataTableSettings,
            dom: tableDom,
            buttons: ["copy", "excel", "print"],
            scrollY: '40dvh',
            scrollCollapse: true,
            pagingType: 'simple_numbers',
            data: tableData,
            columns: [{
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                {
                    data: "answerdt_recdatetime",
                    render: function(data, type, row, meta) {
                        return dayjs(data, "YYYY-MM-DD HH:mm:ss").format("HH:mm DD/MM/BB");
                    },
                },
                {
                    data: "questiondt_title",
                    render: function(data, type, row, meta) {
                        return data;
                    },
                },
                {
                    data: "mistakelevel_name",
                    render: function(data, type, row, meta) {
                        return data;
                    },
                },
                {
                    data: "weight",
                    render: function(data, type, row, meta) {
                        return data;
                    },
                },
            ],
        });
    }

    function initializeDateRange() {
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                    ranges: {
                        วันนี้: [moment(), moment()],
                        เมื่อวาน: [moment().subtract(1, "days"), moment().subtract(1, "days")],
                        "ล่าสุด 7 วัน": [moment().subtract(6, "days"), moment()],
                        "ล่าสุด 30 วัน": [moment().subtract(29, "days"), moment()],
                        เดือนนี้: [moment().startOf("month"), moment().endOf("month")],
                        เดือนที่แล้ว: [
                            moment().subtract(1, "month").startOf("month"),
                            moment().subtract(1, "month").endOf("month"),
                        ],
                        ปีนี้: [moment().startOf("years"), moment().endOf("years")],
                        ปีที่แล้ว: [
                            moment().subtract(1, "years").startOf("years"),
                            moment().subtract(1, "years").endOf("years"),
                        ],
                    },
                    locale: {
                        format: "DD/MM/YYYY",
                        applyLabel: "ยืนยัน",
                        cancelLabel: "ยกเลิก",
                        customRangeLabel: "กำหนดเอง",
                        daysOfWeek: ["อา", "จ", "อ", "พ", "พฤ", "ศ", "ส"],
                        monthNames: [
                            "มกราคม",
                            "กุมภาพันธ์",
                            "มีนาคม",
                            "เมษายน",
                            "พฤษภาคม",
                            "มิถุนายน",
                            "กรกฎาคม",
                            "สิงหาคม",
                            "กันยายน",
                            "ตุลาคม",
                            "พฤศจิกายน",
                            "ธันวาคม",
                        ],
                        linkedCalendars: true,
                    },
                },
                function(start, end, label) {
                    startDate = start.startOf("day").format("YYYY-MM-DD");
                    endDate = end.endOf("day").format("YYYY-MM-DD");
                    getReport();
                }
            );
        });
    }
    </script>