<?php
session_start();
include_once 'v_head.php';
include_once 'v_sidebar_start.php';
?>
<style>
.sorting_disabled{
    text-align: center !important;
}
td{
    text-align: center;
}
.textcenterd{
    text-align: center;
}
</style>

<div class="row">
    <div class="col-12">
        <div class="w-100 d-flex mt-2">
            <h3 class="me-auto mt-1">รายการชุดคำถาม</h3>
            <button type="button" class="btn btn-primary" onclick="window.location.href='addquestion.php';"><i class="bi bi-plus-square"></i> เพิ่มชุดคำถามใหม่</button>
        </div>
        <hr>
    </div>
    <div class="col-12">
        <div class="table-responsive w-100 p-1">
            <table id="answerListTable" class="table table-bordered table-striped shadow-sm w-100">
                <thead class="text-bg-primary" style="vertical-align: middle;">
                    <tr>
                        <td width="50px;">ลำดับ</td>
                        <td >ผู้ตรวจ</td>
                        <td >วันที่ตรวจ</td>
                        <td >หัวข้อการตรวจ</td>
                        <td >ตรวจ</td>
                        <td >ประเภท</td>
                        <td >ref</td>
                        <td width="200px;"></td>
                    </tr>
                </thead>
                <tbody class="align-middle text-start"></tbody>
            </table>
        </div>
    </div>
</div>


<!-- Modal generate Answer question -->

<div class="modal fade" id="generateanswerquestion" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">ผลการตรวจ</h1>
                    <span class="btn-close" data-bs-dismiss="modal" aria-label="Close"></span>
                </div>

                <div class="modal-body" id="answerquestionbody">

                    
                    
                </div>

            </div>

        </div>

    </div>

<!-- </main> -->

<?php
    /* echo "<pre>";
    print_r($_SESSION);
    echo "</pre>"; */
    include_once 'v_sidebar_end.php';
    include_once 'v_footer.php';
?>
<script>
    $(function() {
        initListTable();
    })

    function initListTable() {
        answerListTable = $("#answerListTable").DataTable({
            serverSide: true,
            processing: true,
            aLengthMenu: [
                [1,5, 10, 25, 50, 100, 200],
                [1,5, 10, 25, 50, 100, 200],
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
                url: `../phpfunc/answerdata.php?method=getAnswerList`,
                type: "POST",
                data: function (d) {

                },
                dataSrc: function (json) {
                    handleScriptLoad();
                    return json.data;
                },
            },
            columns: [
                {
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                {
                    data: "answer_recname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "answer_recdatetime",
                    render: function (data, type, row) {
                        return (
                            dayjs(data, "YYYY-MM-DD hh:mm").format("DD/MM/BBBB HH:mm")
                        );
                    },
                },
                {
                    data: "question_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "answer_type",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questionmode_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "answer_ref",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "answer_remark",
                    render: function (data, type, row) {
                        return `<div class="btn-group" role="group">
                                    <button type="button" class="btn btn-info" onclick="openmodalAnswerQuestion('${row.answer}','${row.question}')"><i class="bi bi-menu-button-wide"></i> ผลการตรวจ</button>
                                    <!--<a href="viewAnswer.php?id=`+row.question+`&answerid=`+row.answer+`" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i> ผลการตรวจ</a>-->
                                    <!--<a href="viewAnswer.php" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i> ผลการตรวจ</a>-->
                                </div>`;
                    },
                },
            ],
        });
    }

    function escapeHtml(unsafe)
    {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function openmodalAnswerQuestion(question,answer){
        $("#generateanswerquestion").modal('show');

        var actionUrl = "../phpfunc/getanswerdata.php";

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: {
                method: "getDataAnswerQuestion",
                question : question,
                answer : answer,
            },
            dataType: "JSON",
            // beforeSend: function(){
            //     fireSwalOnSubmit();
            // },
            success: function(obj)
            {
                var resinfo = obj.datainfo;
                var reshtml = obj.datahtml;
                var html = "";
                html += `<div class="col-lg-12 col-md-12 col-sm-12 p-2">`;
                html += `    <div class="row"> `;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_staffcompfunc" class="form-label">สายงาน : `+resinfo.compfuncname+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_staffcompfuncdep" class="form-label">ฝ่าย : `+resinfo.compfuncdepname+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_staffcompfuncdepsec" class="form-label">แผนก : `+resinfo.compfuncdepsecname+`</label>`;
                html += `        </div>`; 
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_mquestiontype" class="form-label">หมวด : `+resinfo.questioncategories_name+` </label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_mquestiontype" class="form-label">กลุ่ม : `+resinfo.questiongroup_name+` </label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qname" class="form-label">ประเภท : `+resinfo.questionmode_name+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qname" class="form-label">ชื่อของชุดคำถาม : `+resinfo.questiongroup_name+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qdatail" class="form-label">รายละเอียด : `+resinfo.question_detail+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qdatail" class="form-label">ผู้ตรวจ : `+resinfo.question_detail+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qdatail" class="form-label">วันที่ตรวจ : `+dayjs(resinfo.answer_recdatetime, "YYYY-MM-DD hh:mm").format("DD/MM/BBBB HH:mm")+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qdatail" class="form-label">ตรวจ : `+resinfo.answer_type+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qdatail" class="form-label">รหัสพนักงาน : `+resinfo.staff_code+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-6 col-md-6 col-sm-6 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <label for="par_qdatail" class="form-label">ชื่อพนักงาน : `+resinfo.staff_name+`</label>`;
                html += `        </div>`;
                html += `        <div class="col-lg-12 col-md-12 col-sm-12 shadow rounded-3 border border-info p-2 mb-1 mt-1">`;
                html += `            <div class="row ">`;
                html += `                <div class="col-md-12 col-lg-12">`;

                html += reshtml;
                
                html += `                </div>`;
                html += `            </div>`;
                html += `        </div>`;
                html += `    </div>`;
                html += `</div>`;

                $("#answerquestionbody").html(html);

            }
        });

    };
</script>