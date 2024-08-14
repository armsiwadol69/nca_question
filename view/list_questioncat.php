<?php
session_start();
include_once 'v_head.php';
include_once 'v_sidebar_start.php';
require_once ("../class/class.question.php");

$go_ncadb = new ncadb();
$ncaquestion = new question($_GET['id']);

// Get ฝ่าย
$sqlmcompfunc = "SELECT * FROM m_compfunc WHERE m_compfunc_active = 1 ";
$arr_mcompfunc = $go_ncadb->ncaretrieve($sqlmcompfunc, "icms");
$arrmcompfunc = $ncaquestion->ncaArrayConverter($arr_mcompfunc);

// Get แผนก
$sqlmcompfuncdep = "SELECT * FROM m_compfuncdep WHERE m_compfuncdep_active = 1 ";
$arr_mcompfuncdep = $go_ncadb->ncaretrieve($sqlmcompfuncdep, "icms");
$arrmcompfuncdep = $ncaquestion->ncaArrayConverter($arr_mcompfuncdep);


$staffcompfunc    = $_SESSION['userData']['staffcompfunc'];
$staffcompfuncdep = $_SESSION['userData']['staffcompfuncdep'];


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
                        <td>รายละเอียด</td>
                        <td>ฝ่าย</td>
                        <td>แผนก</td>
                        <td>สถานะ</td>
                        <td width="200px;">ผู้บันทึก</td>
                        <td width="150px;">วันที่บันทึก</td>
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

                        <div class="col-lg-12 col-md-12 mt-2">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">

                                    <label for="questioncategories_compfunc" class="form-label">ฝ่าย<span class="text-danger">*</span></label>
                                    <select class="form-select" name="questioncategories_compfunc" id="questioncategories_compfunc" >
                                        <!-- <option>เลือกฝ่าย</option> -->
                                        <?php
                                            foreach ($arrmcompfunc as $key => $value) {
                                                $selected = "";
                                                if($value['m_compfunc'] == $staffcompfunc){
                                                    //$selected = "selected";
                                                    echo '<option value="'.$value['m_compfunc'].'" '.$selected.'> '.$value['m_compfunc_name_th'].' </option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 mt-2">

                                    <label for="questioncategories_compfuncdep" class="form-label">แผนก<span class="text-danger">*</span></label>
                                    <select class="form-select" name="questioncategories_compfuncdep" id="questioncategories_compfuncdep" onchange="changestaffcompfuncdep()">
                                        <option value="">เลือกแผนก</option>
                                        <?php

                                            foreach ($arrmcompfuncdep as $key => $value) {
                                                $selected = "";
                                                // if($value['m_compfuncdep'] == $staffcompfuncdep){
                                                //     $selected = "selected";
                                                // }
                                                if($value['m_compfuncdep_compfunc'] == $staffcompfunc){
                                                    if($value['m_compfuncdep'] == $staffcompfuncdep){
                                                        $selected = "selected";
                                                    }
                                                    echo '<option value="'.$value['m_compfuncdep'].'" '.$selected.'> '.$value['m_compfuncdep_name_th'].' </option>';
                                                }
                                            }
                                        ?>
                                    </select>
                                
                                </div>
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

    $(document).ready(function(){

        initListTable();
        getQuestionCategories();

    });

    async function getQuestionCategories() {
        try {
            var endpoint = `../phpfunc/questioncategories.php?method=getlist&staffcompfunc=`+'<? echo $staffcompfunc; ?>';
            console.log("Test",endpoint);
            const response = await axios.get(endpoint);
            var data = response.data;
            rewardListTable.clear();
            rewardListTable.rows.add(data);
            rewardListTable.draw();
            handleScriptLoad();
        } catch (error) {
            console.log(error);
        }
    }

    async function initListTable() {
        rewardListTable = $("#list_questioncat").DataTable({
            stateSave: false,
            aLengthMenu: aLengthMenu,
            iDisplayLength: 25,
            ordering: false,
            language: dataTableSettings,
            dom: tableDom,
            buttons: [],
            scrollY: ($('#page-content-wrapper').height() - 300),
            scrollCollapse: true,
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
                {
                    data: "questioncategories_description",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
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
                    data: "questioncategories_recname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questioncategories_recdatetime",
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
                        if (row.total_items != "0") {
                            isDisabled = "disabled";
                        } else {
                            isDisabled = "";
                        }
                        isDisabled = "";

                        return `<div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning `+(row.questioncategories_default == 1 ? "" : "")+`" onclick="callQuestionCate('editquestioncategories','${row.questioncategories}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                    <button type="button" class="btn btn-danger `+(row.questioncategories_default == 1 ? "" : "")+`" onclick="callQuestionCate('delete','${row.questioncategories}','${row.questioncategories_name}')"><i class="bi bi-trash3"></i> ลบ</button>
                                </div>`;
                    },
                },
            ],
        });
    }

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
                /*  beforeSend: function(){
                    fireSwalOnSubmit();
                }, */
                success: function(obj)
                {   
                    handleScriptLoad();
                    if(obj.length > 0){
                        let res = obj[0];
                        $("#questioncategories_compfunc").val(res.questioncategories_compfunc);
                        $("#questioncategories_compfuncdep").val(res.questioncategories_compfuncdep);
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

            $("#questioncategories_compfunc").val(<? echo $staffcompfunc; ?>);
            $("#questioncategories_compfuncdep").val(<? echo $staffcompfuncdep; ?>);
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

                console.log(result.isConfirmed);
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
                                            getQuestionCategories();
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
        if($("#questioncategories_compfunc").val() == ""){
            validate = false;
        }
        if($("#questioncategories_compfuncdep").val() == ""){
            validate = false;
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
                            getQuestionCategories();
                            
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
</script>