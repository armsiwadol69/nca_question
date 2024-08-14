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
    background: #e5e5e5 !important;
}
.shown .details-btn::before {
    /* content: "-"; */
    content: "ซ่อน";
    font-weight: bold;
    margin-right: 5px;
}
.details-btn::before {
    /* content: "+"; */
    content: "แสดง";
    font-weight: bold;
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
                        <td >ประเภท</td>
                        <td >สายงาน</td>
                        <td >ฝ่าย</td>
                        <td >แผนก</td>
                        <td width="250px;">Detail</td>
                    </tr>
                    <!-- <tr>
                        <td width="50px;">ลำดับ</td>
                        <td >สายงาน</td>
                        <td >ฝ่าย</td>
                        <td >แผนก</td>
                        <td >หมวด</td>
                        <td >กลุ่มคำถาม</td>
                        <td >ประเภท</td>
                        <td >ผู้บันทึก</td>
                        <td >วันที่บันทึก</td>
                        <td width="200px;"></td>
                    </tr> -->
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
        initListTable();

        $('#questionListTable').on('click', 'button.details-btn', function() {
            var tr = $(this).closest('tr');
            // console.log("tr click ",tr);
            var row = questionListTable.row(tr);
            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                var parentId = row.data().question;
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
                                row.child(format(data)).show();
                            }
                            $(row.node()).addClass('shown');
                        }
                    }
                });
            }
        });
    })

    function initListTable() {
        questionListTable = $("#questionListTable").DataTable({
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
                    data: "questionmode_name",
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
                                    <button type="button" class="btn btn-warning" onclick="callActionCustom('edit','${row.question}')"><i class="bi bi-pencil-square"></i> แก้ไขทั้งหมด</button>
                                    <a href="`+linkUrlCustom +`?id=`+row.question+`" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i>Link</a>
                                </div>`;
                        /* return `<div class="btn-group" role="group">
                                    <button type="button" class="btn btn-warning" onclick="callActionCustom('edit','${row.question}')"><i class="bi bi-pencil-square"></i> แก้ไข</button>
                                    <button class='details-btn btn btn-secondary'></button>
                                    <a href="`+linkUrlCustom +`?id=`+row.question+`" target="_blank" class="btn btn-info"><i class="bi bi-menu-button-wide"></i>Link</a>
                                </div>`; */
                    },
                
                }/*,
                {
                    "data": null,
                    "defaultContent": "<button class='details-btn btn btn-secondary'></button>"
                }*/
                /* {
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
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
                    data: "questioncategories_name",
                    render: function (data, type, row, meta) {
                        return `${data}`;
                    },
                },
                {
                    data: "question_name",
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
                // console.log(api.rows());
                api.rows().every(function() {
                    var row = this;
                    var parentId = row.data().question_questioncategories;
                    $.ajax({
                        // url: `https://yourserver.com/api/children/${parentId}`,  // Replace with your actual API endpoint
                        url: `../phpfunc/questiondatacustom.php`,  // Replace with your actual API endpoint
                        data : {
                            "parent_id" : (parentId ? parentId : "999"),
                            "method" : "getQuestionList",
                        },
                        success: function(info) {
                            let data = info.data;
                            if (data.length > 0) {
                                if (!row.child.isShown()) {
                                    row.child(format(data)).show();
                                }
                                $(row.node()).addClass('shown');
                            }
                        }
                    });
                });
            }
        });
    }

    async function getQuestionListDataFromAPI() {
        try {
            // var endpoint = `../phpfunc/questiondata.php?method=getQuestionList`;
            // const response = await axios.get(endpoint);
            // var data = response.data;
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

    function escapeHtml(unsafe)
    {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function callActionCustom(action, id, name, currentUserId=0) {
        if (action == "edit") {

            window.location.href = `addquestionCustom.php?id=${id}`;

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
                // window.location.href = `../phpfunc/curd.php?method=del&id=${id}&currentUserId=${$currentUserId}`;
                // var endpoint = `../phpfunc/rewardapi.php?method=getItemList&par_id=${par_id}`;
                    if(id > 0 && currentUserId > 0){

                        // var endpoint = `../phpfunc/curd.php?mode=del&id=${id}&currentUserId=${currentUserId}`;
                        // const response = axios.get(endpoint);
                        // var data = response.data;
                        // console.log(response,"check data",data);
                        // if (data != null) {
                        // 	itemListTable.clear();
                        // 	itemListTable.rows.add(data.rtn);
                        // 	itemListTable.draw();
                        // }
                    
                        // let urlendpoint = `../phpfunc/curd.php`;
                        let urlendpoint = `../phpfunc/curd.php`;
                        $.ajax({
                            url: urlendpoint,
                            type: "POST",
                            // contentType: "application/json",
                            // dataType: 'JSON',
                            data: {
                                mode : 'del',
                                id : id,
                                currentUserId : currentUserId,
                            },
                            success: function(data) {

                                console.log(data);
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
                                            getQuestionListDataFromAPI().then(() => {
                                                handleScriptLoad();
                                            });
                                        }, 100);
                                    });
                                }
                                /* 
                                    Swal.fire({
                                        icon: "success",
                                        text: "บันทึกข้อมูลสำเร็จ",
                                        timer: 2000,
                                        showConfirmButton: false,
                                        showCloseButton: false,
                                        }).then(() => {
                                        // getItemListDataFromAPI(par_id);
                                        setTimeout(() => {
                                            // window.location.reload();
                                            itemListTable.ajax.reload();
                                        }, 100);
                                    });
                                */

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

    function format(data) {

        // console.log("dtata",data);
        for (i = 0; i < data.length; ++i) {
            // console.log("data i ",data[i])

        //     return `
        //     <table class="child-table">
        //         <thead>
        //             <tr>
        //                 <th class"text-center">ID</th>
        //                 <th class"text-center">Child ID</th>
        //                 <th class"text-center">Child Name</th>
        //                 <th class"text-center">Description</th>
        //             </tr>
        //         </thead>
        //         <tbody>
        //             ${data.map(child => `
        //                 <tr>
        //                     <td>${child.child}</td>
        //                     <td>${child.question}</td>
        //                     <td>${child.question_name}</td>
        //                     <td>${child.question_questioncategoriesname}</td>
        //                 </tr>
        //             `).join('')}
                    
        //         </tbody>
        //     </table>
        // `;
        }
        let iddt = "id" + Math.random().toString(16).slice(2);
        let html =  `<table id="`+iddt+`" class="table table-bordered table-striped shadow-sm w-100">
                        <thead style="background-color: #b2d1fa;">
                            <tr>
                                <th class="text-center" width="100px;">ลำดับ</th>
                                <!--<th class="text-center">Child ID</th>-->
                                <th class="text-center">ชื่อชุดคำถาม</th>
                                <th class="text-center">หมวด</th>
                            </tr>
                        </thead>
                        <tbody>`;
                   
        for (i = 0; i < data.length; ++i) {
            // console.log("data i ",data[i])
            html += ` <tr>`;
            html += `     <td>`+(i+1)+`</td>`;
            // html += `     <td>${data[i].question}</td>`;
            html += `     <td>${data[i].question_name}</td>`;
            html += `     <td>${data[i].question_questioncategoriesname}</td>`;
            html += ` </tr>`;
        }
         
        html += `</tbody>
            </table>
        `;
        var table = new DataTable('#'+iddt);

        return html;
    }
</script>