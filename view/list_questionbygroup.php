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
table.dataTable tbody tr {
    background-color: transparent;
}
.dt-hasChild {
    /* background: #f7d4d4 !important; */
    background: #86ff87 !important;
}
.shown .details-btn::before {
    /* content: "-"; */
    content: "ซ่อน";
    /* font-weight: bold; */
    margin-right: 5px;
}
.details-btn::before {
    /* content: "+"; */
    content: "แสดง";
    /* font-weight: bold; */
    margin-right: 5px;
}
</style>

<div class="row">
    <div class="col-12">
        <div class="w-100 d-flex mt-2">
            <h3 class="me-auto mt-1">รายการชุดคำถาม</h3>
            <button type="button" class="btn btn-primary" onclick="window.location.href='addquestionCustom.php';"><i class="bi bi-plus-square"></i> เพิ่มชุดคำถามใหม่</button>
        </div>
        <hr>
    </div>
    <div class="col-12">
        <div class="table-responsive w-100 p-1">
            <table id="questionListTable" class="table table-bordered table-striped shadow-sm w-100">
                <thead class="text-bg-primary" style="vertical-align: middle;">
                    <tr>
                        <td width="100px;">ลำดับ</td>
                        <td >หมวด</td>
                        <td >สายงาน</td>
                        <td >ฝ่าย</td>
                        <td >แผนก</td>
                        <td width="250px;"></td>
                    </tr>
                </thead>
                <tbody class="align-middle text-start"></tbody>
            </table>
        </div>
    </div>
</div>

<?php
    include_once 'v_sidebar_end.php';
    include_once 'v_footer.php';
?>
<script>
    var questionListTable;

    $(function() {

        $('#questionListTable').on('click', 'button.details-btn', function() {
            var tr = $(this).closest('tr');
            var row = questionListTable.row(tr);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                var parentId = row.data().question_questioncategories;
                let indexRow = row.index();
                $.ajax({
                    url: `../phpfunc/questiondatacustom.php`,  // Replace with your actual API endpoint
                    type: "POST",
                    data : {
                        "parent_id" : (parentId ? parentId : "999"),
                        "method" : "getQuestionList",
                    },
                    success: function(info) {
                        let data = info.data;
                        if (data.length > 0) {
                            if (!row.child.isShown()) {
                                row.child(format(data,(indexRow+1))).show();
                            }
                            $(row.node()).addClass('shown');
                        }
                    }
                });
            }
        });

        questionListTable = $("#questionListTable").DataTable({
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
                url: `../phpfunc/questiondatacustom.php?method=getQuestionList`,
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
                    data: "questioncategories_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
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
                    data: "question_compfuncdepsecname",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "giftdetail",
                    render: function (data, type, row) {
                        return `<div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning" onclick="callActionCustom('edit','${row.question_questioncategories}')"><i class="bi bi-pencil-square"></i> แก้ไขทั้งหมด</button>
                                    <button type="button" class="details-btn btn btn-info" ><i class="bi bi-menu-button-wide"></i></button>
                                </div>`;
                        /* return `<div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning" onclick="callActionCustom('edit','${row.question}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                    <button class='details-btn btn btn-secondary'></button>
                                    <a href="`+linkUrlCustom +`?id=`+row.question+`" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i>Link</a>
                                </div>`; */
                    },
                
                },
                /*{
                    data: "giftdetail",
                    render: function (data, type, row) {
                        return `<div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning" onclick="callActionCustom('edit','${row.question}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                    <button type="button" class="btn btn-secondary" onclick="callActionCustom('copy','${row.question}')"><i class="bi bi-copy"></i> Copy</button>
                                    <button type="button" class="btn btn-danger  onclick="callActionCustom('delete','${row.question}','${escapeHtml(row.question_name)}','${row.currrent_user}')"><i class="bi bi-trash3"></i> ลบ</button>
                                    <a href="`+linkUrlCustom +`?id=`+row.question+`" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i>Link</a>
                                </div>`;
                    },
                }, */
            ],
            "drawCallback": function() {
                var api = this.api();
                api.rows().every(function() {
                    var row = this;
                    var parentId = row.data().question_questioncategories;
                    var index = row.index();
                    $.ajax({
                        url: `../phpfunc/questiondatacustom.php`,  // Replace with your actual API endpoint
                        data : {
                            "parent_id" : (parentId ? parentId : "999"),
                            "method" : "getQuestionList",
                        },
                        success: function(info) {
                            let data = info.data;
                            if (data.length > 0) {
                                if (!row.child.isShown()) {
                                    row.child(format(data,(index+1))).show();
                                }
                                $(row.node()).addClass('shown');
                            }
                        }
                    });
                });
            }
        });

    })

    function escapeHtml(str)
    {
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function callActionCustom(action, id, name, currentUserId=0, mode="") {
        if (action == "edit") {
            if(name){
                window.location.href = `addquestionCustom.php?${name}=${id}`;
            }else{
                window.location.href = `addquestionCustom.php?cateid=${id}`;
            }
            

        } else if (action == "delete") {

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
                    if(id > 0 && currentUserId > 0){
                        
                        let urlendpoint = `../phpfunc/curd.php`;
                        $.ajax({
                            url: urlendpoint,
                            type: "POST",
                            // contentType: "application/json",
                            dataType: 'JSON',
                            data: {
                                mode : 'del',
                                id : id,
                                currentUserId : currentUserId,
                            },
                            success: function(data) {
                                if(data.success > 0){
                                    Swal.fire({
                                        icon: "success",
                                        text: "บันทึกข้อมูลสำเร็จ",
                                        timer: 2000,
                                        showConfirmButton: false,
                                        showCloseButton: false,
                                        }).then(() => {
                                        // getItemListDataFromAPI(par_id);
                                        setTimeout(() => {
                                            $('#questionListTable').DataTable().ajax.reload();
                                        }, 100);
                                    });
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

    function format(data,index) {
        let iddt = "id" + Math.random().toString(16).slice(2);
        let html =  `<table id="`+iddt+`" class="table table-bordered table-striped shadow-sm w-100">
                        <thead style="background-color: #b2d1fa;">
                            <tr>
                                <th class="text-center" width="10%">ลำดับ</th>
                                <th class="text-center" width="25%">ชื่อชุดคำถาม</th>
                                <th class="text-center" width="20%">ประเภท</th>
                                <th class="text-center" width="12%">ผู้บันทึก</th>
                                <th class="text-center" width="12%">วันเวลาที่บันทึก</th>
                                <th class="text-center" width="10%"></th>
                            </tr>
                        </thead>
                        <tbody>`;
                   
        for (i = 0; i < data.length; ++i) {
            html += ` <tr>`;
            html += `     <td>`+index+`.`+(i+1)+`</td>`;
            html += `     <td>${data[i].question_name}</td>`;
            html += `     <td>${data[i].questionmode_name}</td>`;
            html += `     <td>${data[i].question_username}</td>`;
            html += `     <td>${dayjs(data[i].question_userdatetime, "YYYY-MM-DD hh:mm").format("DD/MM/BBBB HH:mm")}</td>`;
            html += `     <td><div class="btn-group" role="group">
            <button type="button" class="btn btn-warning" onclick="callActionCustom('edit','${data[i].question}','id')"><i class="bi bi-pencil-square"></i> แก้ไข</button><button type="button" class="btn btn-danger"  onclick="callActionCustom('delete','${data[i].question}','${escapeHtml(data[i].question_name)}','${data[i].currrent_user}')"><i class="bi bi-trash3"></i> ลบ</button></div></td>`;
            html += ` </tr>`;
        }
         
        html += `</tbody>
            </table> `;
        //var table = new DataTable('#'+iddt);

        return html;
    }
</script>