<?php
include_once 'v_head.php';
// include_once 'v_sidebar_start.php';
?>
<style>
  .sorting_disabled{
    text-align: center !important;
  }
  td{
    text-align: center;
  }
</style>
<!-- <main class="main-content" style="height: 94dvh;overflow-x:hidden;"> -->
    <div class="row">
        <div class="col-12">
            <div class="table-responsive w-100 p-1">
                <table id="rewardListTable" class="table table-bordered table-striped shadow-sm w-100">
                    <thead class="text-bg-primary">
                        <tr>
                            <td>ลำดับ</td>
                            <td>ชุดคำถาม</td>
                            <td>รายละเอียด</td>
                            <td>ผู้บันทึก</td>
                            <td>วันที่บันทึก</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-start">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<!-- </main> -->
<?php
    // include_once 'v_sidebar_end.php';
    include_once 'v_footer.php';
?>
<script>
    $(function() {
        initListTable();
        getQuestionListDataFromAPI().then(() => {
            handleScriptLoad();
        });
    })
</script>