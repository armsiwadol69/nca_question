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

$sqlquestioncategories  = "SELECT *  FROM tb_questioncategories WHERE questioncategories_active = '1' ";
$arr_questioncategories = $go_ncadb->ncaretrieve($sqlquestioncategories, "question");
$arrquestioncategories = $ncaquestion->ncaArrayConverter($arr_questioncategories);

?>

<div class="row">
    <div class="col-12">
        <div class="w-100 d-flex mt-2">
            <h3 class="me-auto mt-1">ลักษณะของการตรวจ</h3>
            <button type="button" class="btn btn-primary"  onclick="callactivities('addquestionctivities','0')"><i class="bi bi-plus-square"></i> เพิ่มลักษณะของการตรวจใหม่</button>
        </div>
        <hr>
    </div>
    <div class="col-12">
        <div class="table-responsive w-100 p-1">
            <table id="list_activities" class="table table-bordered table-striped shadow-sm w-100">
                <thead class="text-bg-primary" style="vertical-align: middle;">
                    <tr>
                        <td width="50px;">ลำดับ</td>
                        <td>ลักษณะของการตรวจ</td>
                        <td>รายละเอียด</td>
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

<div class="modal fade" id="modalactivities" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">แก้ไขกลุ่มคำถาม</h1>
                <span class="btn-close" data-bs-dismiss="modal" aria-label="Close"></span>
            </div>

            <form  action="" enctype="multipart/form-data" method="POST" id="frm_submit" novalidate>

                <div class="modal-body">

                    <div class="row gy-3">
                        
                        <div class="col-12">
                            <label class="form-label" for="option">ชื่อ<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="activities_name" id="activities_name" required>
                        </div>

                        <div class="col-12">
                            <textarea class="form-control" id="activities_description" name="activities_description" rows="5" required=""></textarea>
                            
                            <input type="hidden" name="activities" id="activities" value="">
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
        getquestioncategroup();

    });

    async function getquestioncategroup() {
        try {
            var endpoint = `../phpfunc/questionactivities.php?method=getlist`;
            console.log("Test",endpoint);
            const response = await axios.get(endpoint);
            var data = response.data;
            ListTable.clear();
            ListTable.rows.add(data);
            ListTable.draw();
            handleScriptLoad();
        } catch (error) {
            console.log(error);
        }
    }

    async function initListTable() {
        ListTable = $("#list_activities").DataTable({
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
                    data: "activities_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "activities_description",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "activities_recname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "activities_recdatetime",
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
                                    <button type="button" class="btn btn-warning" onclick="callactivities('editquestionctivities','${row.activities}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                    <button type="button" class="btn btn-danger ${isDisabled}" onclick="callactivities('delete','${row.activities}','${row.activities_name}')"><i class="bi bi-trash3"></i> ลบ</button>
                                </div>`;
                    },
                },
            ],
        });
    }

    function callactivities(mode,id,name){

        if(mode == "editquestionctivities"){
            var actionUrl =  `../phpfunc/questionactivities.php`;
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: {
                    method: "getlistactivities",
                    activities : id,
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

                        $("#activities_name").val(res.activities_name);
                        $("#activities_description").val(res.activities_description);
                        $("#activities").val(res.activities);
                        $("#method").val(mode);
                    } 
                }
            });

            $("#modalactivities").modal("show");

        }else if(mode == "addquestionctivities"){

            
            $("#activities_name").val("");
            $("#activities_description").val("");
            $("#activities").val("");
            $("#method").val(mode);

            $("#modalactivities").modal("show");

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
                        
                        let urlendpoint = `../phpfunc/questionactivities.php`;
                        $.ajax({
                            url: urlendpoint,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                method : 'deleteactivities',
                                activities_id : id,
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
                                            getquestioncategroup();
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
        if($("#activities_name").val() == ""){
            validate = false;
        }

        if(validate == true){
            var form = $("#frm_submit");
            var actionUrl = "../phpfunc/questionactivities.php";

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
                        $("#modalactivities").modal("hide");
                        alertSwalSuccess();
                        setTimeout(() => {
                            getquestioncategroup();
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