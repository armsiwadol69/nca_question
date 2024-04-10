<?php
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
<!-- <main class="main-content" style="height: 94dvh;overflow-x:hidden;"> -->
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
                <table id="questionListTable" class="table table-bordered table-striped shadow-sm w-100">
                    <thead class="text-bg-primary" style="vertical-align: middle;">
                        <tr>
                            <td width="50px;">ลำดับ</td>
                            <td >ชุดคำถาม</td>
                            <td >ฝ่าย</td>
                            <td >แผนก</td>
                            <td >หมวด</td>
                            <td >กลุ่ม</td>
                            <td >ประเภท</td>
                            <td >ผู้บันทึก</td>
                            <td >วันที่บันทึก</td>
                            <!-- <td width="150px;"><button type="button" id="addnewbtn" class="btn btn-primary" onclick="window.location.href='addquestion.php';"><i class="bi bi-plus-square"></i> เพิ่มชุดคำถามใหม่</button></td> -->
                            <td width="200px;"></td>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-start"></tbody>
                </table>
            </div>
        </div>
    </div>
<!-- </main> -->
<?php
    include_once 'v_sidebar_end.php';
    include_once 'v_footer.php';
?>
<script>
    $(function() {
        initListTable();
        getQuestionListDataFromAPI().then(() => {
            handleScriptLoad();
        });
    })

    async function initListTable() {
        questionListTable = $("#questionListTable").DataTable({
            stateSave: false,
            iDisplayLength: 25,
            ordering: false,
            language: dataTableSettings,
            dom: tableDom,
            buttons: [],//tableButton,
            scrollY: ($('#page-content-wrapper').height() - 300),
            scrollCollapse: true,
            scrollX: true,
            columns: [
                {
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    },
                },
                {
                    data: "question_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                /*  {
                    data: "question_detail",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                }, */
                {
                    data: "question_compfuncname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "question_compfuncdepname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questioncategories_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "questiongroup_name",
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
                    data: "question_recname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "question_recdatetime",
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
                                    <button type="button" class="btn btn-warning" onclick="callAction('edit','${row.question}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                    <button type="button" class="btn btn-secondary" onclick="callAction('copy','${row.question}')"><i class="bi bi-copy"></i> Copy</button>
                                    <button type="button" class="btn btn-danger ${isDisabled}" onclick="callAction('delete','${row.question}','${row.question_name}','${row.currrent_user}')"><i class="bi bi-trash3"></i> ลบ</button>
                                    <a href="`+linkUrl +`?formId=`+row.question+`" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i>Link</a>
                                </div>`;
                    },
                },
            ],
        });
    }

    async function getQuestionListDataFromAPI() {
        try {
            var endpoint = `../phpfunc/questiondata.php?method=getQuestionList`;
            const response = await axios.get(endpoint);
            var data = response.data;
            questionListTable.clear();
            questionListTable.rows.add(data);
            questionListTable.draw();
            // questionListTable.columns.adjust().draw();
            // questionListTable.responsive.recalc();
            //console.log(response);


        } catch (error) {
            console.log(error);
        }
    }
</script>