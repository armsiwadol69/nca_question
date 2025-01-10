<?php
session_start();
include_once 'v_head.php';
include_once 'v_sidebar_start.php';
require_once ("../class/class.question.php");

$go_ncadb = new ncadb();
$ncaquestion = new question($_GET['id']);


$staffcompfuncval       = $_SESSION['userData']['staffcompfunc'];
$staffcompfuncdepval    = $_SESSION['userData']['staffcompfuncdep'];
$staffcompfuncdepsecval = $_SESSION['userData']['staffcompfuncdepsec'];
                         
?>
<style>
  .sorting_disabled{
    text-align: center !important;
  }
  td{
    text-align: center;
  }
</style>

<div class="row">
    <div class="col-12">
        <div class="w-100 d-flex mt-2">
            <h3 class="me-auto mt-1">หมวดคำตอบ</h3>
            <button type="button" class="btn btn-primary"  onclick="callQuestionCate('addquestioncategories','0')"><i class="bi bi-plus-square"></i> เพิ่มหมวดใหม่</button>
        </div>
        <hr>
    </div>
    <div class="col-12">
        <div class="table-responsive w-100 p-1">
            <table id="list_questioncat" class="table table-bordered table-striped shadow-sm w-100">
                <thead class="text-bg-primary" style="vertical-align: middle;">
                    <tr>
                        <td width="50px;">ลำดับ</td>
                        <td>หมวด</td>
                        <!-- <td>รายละเอียด</td> -->
                        <td>สายงาน</td>
                        <td>ฝ่าย</td>
                        <td>แผนก</td>
                        <td width="70px;">สถานะ</td>
                        <td width="200px;">ผู้บันทึก</td>
                        <td width="120px;">วันที่บันทึก</td>
                        <td width="150px;"></td>
                    </tr>
                </thead>
                <tbody class="align-middle text-start">
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalquestioncate" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">แก้ไขหมวด</h1>
                <span class="btn-close" data-bs-dismiss="modal" aria-label="Close"></span>
            </div>

            <form  action="" enctype="multipart/form-data" method="POST" id="frm_submit" novalidate>

                <div class="modal-body">

                    <div class="row gy-3">

                        <div id="catecomdata">

                            <div class="col-md-12">

                                <label for="questioncategories_compfunc" class="form-label">สายงาน<span class="text-danger">*</span></label>
                                <select class="form-select" name="questioncategories_compfunc" id="questioncategories_compfunc" onchange="getDepartment();">
                                    <option value="0">เลือกสายงาน</option>
                                </select>
                            
                            </div>
                            
                            <div class="col-md-12">

                                <label for="questioncategories_compfuncdep" class="form-label">ฝ่าย<span class="text-danger">*</span></label>
                                <select class="form-select" name="questioncategories_compfuncdep" id="questioncategories_compfuncdep" onchange="getSection()">
                                    <option value="0">เลือกฝ่าย</option>
                                </select>
                            
                            </div>

                            <div class="col-md-12">

                                <label for="questioncategories_compfuncdepsec" class="form-label">แผนก<span class="text-danger">*</span></label>
                                <select class="form-select" name="questioncategories_compfuncdepsec" id="questioncategories_compfuncdepsec" onchange="">
                                    <option value="0">เลือกแผนก</option>
                                </select>
                            
                            </div>
                        
                        </div>
         
                        <div class="col-12">
                            <label class="form-label" for="option">ชื่อ<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="questioncategories_name" id="questioncategories_name" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label" for="questioncategories_hidden">สถานะ<span class="text-danger">*</span></label>
                            <select class="form-select" name="questioncategories_hidden" id="questioncategories_hidden" aria-label="isshowing">
                                <option value="">เลือกสถานะ</option>
                                <option value="0">เเสดง</option>
                                <option value="1">ซ่อน</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <textarea class="form-control" id="questioncategories_description" name="questioncategories_description" rows="5" required=""></textarea>
                            
                            <input type="hidden" name="questioncategories" id="questioncategories" value="">
                            <input type="hidden" name="method" id="method" value="">

                        </div>
                        
                        <div class="col-12">
                            <span id="createOption" class="btn btn-primary w-100" onclick="submitFrom()"><i class="bi bi-save"></i> บันทึกข้อมูล</span>
                        </div>
                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

<?php
    include_once 'v_sidebar_end.php';
    include_once 'v_footer.php';
?>
<script>

    var arrmcompfunc = [];
    var arrmcompfuncdep = [];
    var arrmcompfuncdepsec = [];
    var listTable;

    $(document).ready(function(){

        listTable = $("#list_questioncat").DataTable({
            serverSide: true,
            processing: true,
            aLengthMenu: [
                [10, 25, 50, 100, 200],
                [10, 25, 50, 100, 200],
            ],
            iDisplayLength: 25,
            ordering: false,
            language: dataTableSettings,
            dom: tableDom,
            // buttons: ["copy", "excel", "print"],
            buttons: [],
            scrollY: ($('#page-content-wrapper').height() - 300),
            scrollCollapse: true,
            searchDelay: 1500,
            pagingType: 'simple_numbers',
            ajax: {
                url: `../phpfunc/questioncategories.php?method=getlist`,
                type: "POST",
                data: function (d) {

                },
                dataSrc: function (json) {
                    handleScriptLoad();
                    return json.data;
                },
            },
            columns: 
            [
                {
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                {
                    data: "questioncategories_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                /* {
                    data: "questioncategories_description",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                }, */
                {
                    data: "questioncategories_compfuncname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questioncategories_compfuncdepname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questioncategories_compfuncdepsecname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questioncategories_hidden",
                    render: function (data, type, row, meta) {
                        // return `${data}`;
                        if(data > 0){
                            return `ซ่อน`;
                        }else{
                            return `แสดง`;
                        }
                    },
                },
                {
                    data: "questioncategories_username",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questioncategories_userdatetime",
                    render: function (data, type, row) {
                        return (
                            dayjs(data, "YYYY-MM-DD hh:mm").format("DD/MM/BBBB HH:mm")
                        );
                    },
                },
                {
                    data: "giftdetail",
                    render: function (data, type, row) {
                        let isDisabled;
                        if (row.questioncategories_default == "1") {
                            isDisabled = "disabled";
                        } else {
                            isDisabled = "";
                        }
                        // isDisabled = "";

                        return `<div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning `+isDisabled+`" onclick="callQuestionCate('editquestioncategories','${row.questioncategories}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                    <button type="button" class="btn btn-danger `+isDisabled+`" onclick="callQuestionCate('delete','${row.questioncategories}','${row.questioncategories_name}')"><i class="bi bi-trash3"></i> ลบ</button>
                                </div>`;
                    },
                },
            ],
        });
        getCompfunc();

    });

    function callQuestionCate(mode,id,name){

        if(mode == "editquestioncategories"){
            var actionUrl =  `../phpfunc/questioncategories.php`;
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: {
                    method: "getlistquestioncategories",
                    questioncategories : id,
                },
                dataType: "JSON",
                success: function(obj)
                {   
                    handleScriptLoad();
                    if(obj.length > 0){
                        let res = obj[0];
                        $("#catecomdata").hide();
                        // $("#questioncategories_compfunc").val(res.questioncategories_compfunc);
                        // $("#questioncategories_compfuncdep").val(res.questioncategories_compfuncdep);
                        $("#questioncategories_name").val(res.questioncategories_name);
                        $("#questioncategories_hidden").val(res.questioncategories_hidden);
                        $("#questioncategories_description").val(res.questioncategories_description);
                        $("#questioncategories").val(res.questioncategories);
                        $("#method").val(mode);
                    }   
                }
            });

            $("#modalquestioncate").modal("show");

        }else if(mode == "addquestioncategories"){
            $("#catecomdata").show();
            $("#questioncategories_compfunc").val(0);
            $("#questioncategories_compfuncdep").val(0);
            $("#questioncategories_compfuncdepหำแ").val(0);
            $("#questioncategories_name").val("");
            $("#questioncategories_hidden").val("");
            $("#questioncategories_description").val("");
            $("#questioncategories").val("");
            $("#method").val(mode);

            $("#modalquestioncate").modal("show");

        }else if(mode == "delete"){

            Swal.fire({
                title: "คุณต้องการที่จะลบรายการนี้หรือไม่?",
                text: `รายการ : ${name}`,
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "ใช่",
                cancelButtonText: "ไม่",
                confirmButtonColor: "#FE0000",
            }).then((result) => {

                if (result.isConfirmed == true) {

                    if(id > 0){
                        
                        let urlendpoint = `../phpfunc/questioncategories.php`;
                        $.ajax({
                            url: urlendpoint,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                method : 'deletequestioncategories',
                                questioncategories : id,
                            },
                            success: function(data) {

                                var res = data[0];
                                if(res.success > 0){
                                    Swal.fire({
                                        icon: "success",
                                        text: "บันทึกข้อมูลสำเร็จ",
                                        timer: 1000,
                                        showConfirmButton: false,
                                        showCloseButton: false,
                                        }).then(() => {
                                        setTimeout(() => {
                                            listTable.ajax.reload();
                                        }, 100);
                                    });
                                }else{
                                    fireSwalOnError("บันทึกข้อมูลไม่สำเร็จ <br>"+res.sql);
                                }   
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.log(jqXHR, textStatus, errorThrown);
                                alert('Error occurred!');
                            }
                        
                        });
                    }
                }

            });

        }

    }

    function submitFrom(){

        var validate = true;
        if($("#addquestioncategories").val() == "addquestioncategories"){

            if($("#questioncategories_compfunc").val() == ""){
                validate = false;
            }
            if($("#questioncategories_compfuncdep").val() == ""){
                validate = false;
            }
            if($("#questioncategories_compfuncdepdec").val() == ""){
                validate = false;
            }
        }

        if($("#questioncategories_name").val() == ""){
            validate = false;
        }

        if($("#questioncategories_hidden").val() == "" ){
            validate = false;
        }

        if(validate == true){
            var form = $("#frm_submit");
            var actionUrl = "../phpfunc/questioncategories.php";

            let questioninfoid = $("#questioninfoid").val();
            
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(),
                dataType: "JSON",
                success: function(obj)
                {
                    var res = obj[0];
                    if(res.success > 0){
                        $("#modalquestioncate").modal("hide");
                        alertSwalSuccess();
                        setTimeout(() => {
                            listTable.ajax.reload();
                            swal.close();
                        }, 1200);
                        
                    }else{
                        fireSwalOnError("บันทึกข้อมูลไม่สำเร็จ <br>"+res.sql);
                    }
                }
            });

        }else{
            fireSwalOnError("กรุณาใส่ข้อมูลให้ครบ ด้วยค่ะ");
        }  

    }

    function getCompfunc() {
        const endpoint = "../phpfunc/proxGetData.php";
        const params = {
            method: 'getCompfunc',
        };

        $.ajax({
            type: "POST",
            url: endpoint,
            data: params,
            dataType: "json",
            success: function(data) {
                let res = data;
                if (res.respCode == 1) {
                    let objData = res.data;
                    arrmcompfunc = objData;
                    changeStaffComp();
                }
            },
            error: function(xhr, status, error) {
                console.log("An error occurred: ", status, error);
            },
        });
    }

    function changeStaffComp() {
        let html = ``;
        html = `<option value="0">เลือกสายงาน</option>`;
        arrmcompfunc.forEach(element => {
            if (arrmcompfunc.length > 0) {
                let selectted = "";
                // if (arrmcompfunc_val == element.compfunc_id) {
                //     selectted = "selected";
                // }
                html += `<option value="` + element.compfunc_id + `" `+selectted+`>` + element.compfunc_name + `</option>`;
            }
        });

        html += `</select>`;

        $("#questioncategories_compfunc").html(html);

    }

    function getDepartment(id="") {
        let compfunc = $("#questioncategories_compfunc").val(); 
        
        if(compfunc){
            const endpoint = "../phpfunc/proxGetData.php";
            const params = {
                method: 'getDepartment',
                par_compfuncid: compfunc,
            };

            $.ajax({
                type: "POST",
                url: endpoint,
                data: params,
                dataType: "json",
                success: function(data) {
                    let res = data;
                    if (res.respCode == 1) {
                        let objData = res.data;
                        arrmcompfuncdep = objData;
                        changestaffcompfunc(id);
                    }

                },
                error: function(xhr, status, error) {
                    console.log("An error occurred: ", status, error);
                },
            });
        }
    }

    function changestaffcompfunc(id="") {
        let html = ``;
        html += `<option value="0">เลือกฝ่าย</option>`;

        arrmcompfuncdep.forEach(element => {
            let selectted = "";
            // if (arrmcompfuncdep_val == element.department_id) {
            //     selectted = "selected";
            // }
            html += `<option value="` + element.department_id + `" `+selectted+`>` + element.department_name + `</option>`;
        });
        html += `</select>`;

        $("#questioncategories_compfuncdep").html(html);

    }

    function getSection(id="") {
        
        let compfuncdep = $("#questioncategories_compfuncdep").val();


        if(compfuncdep > 0){
        
            const endpoint = "../phpfunc/proxGetData.php";
            const params = {
                method: 'getSection',
                par_departmentid: compfuncdep,
            };

            $.ajax({
                type: "POST",
                url: endpoint,
                data: params,
                dataType: "json",
                success: function(data) {
                    let res = data;
                    if (res.respCode == 1) {
                        let objData = res.data;
                        arrmcompfuncdepsec = objData;
                        changestaffcompfuncsec(id);
                    }
                },
                error: function(xhr, status, error) {
                    console.log("An error occurred: ", status, error);
                },
            });
        }
    }

    function changestaffcompfuncsec(id="") {
        let html = ``;
        html += `<option value="0">เลือกแผนก</option>`;
        arrmcompfuncdepsec.forEach(element => {
            let selectted = "";
            // if (arrmcompfuncdep_val == element.department_id) {
            //     selectted = "selected";
            // }
            html += `<option value="` + element.section_id + `" ` + selectted + `>` + element.section_name + `</option>`;
        });

        html += `</select>`;

        $("#questioncategories_compfuncdepsec").html(html);
    }
</script>