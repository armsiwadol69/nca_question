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
</style>
<main class="main-content" style="height: 94dvh;overflow-x:hidden;overflow-y:scroll;">
    <div class="row">
        <div class="col-12">
            <div class="w-100 d-flex mt-2">
                <h3 class="me-auto mt-1">รายการของพรีเมี่ยม</h3>
                <button type="button" class="btn btn-primary" onclick="window.location.href='v_addReward.php';"><i class="bi bi-plus-square"></i> เพิ่มรายการของพรีเมี่ยม</button>
            </div>
            <hr>
        </div>
        <div class="col-12">
            <div class="table-responsive w-100 p-1">
                <table id="rewardListTable" class="table table-bordered table-striped shadow-sm w-100">
                    <thead class="text-bg-primary">
                        <tr>
                            <td>ลำดับ</td>
                            <td style="width: 10%;">รูปภาพ</td>
                            <td style="width: 20%;">ชื่อ</td>
                            <td>ประเภท</td>
                            <td>หมวดหมู่</td>
                            <td>คะแนนที่ใช้</td>
                            <td>ระยะเวลา</td>
                            <td>แลกได้</td>
                            <td>จำนวน</td>
                            <td>ถูกแลก</td>
                            <td>Reserve</td>
                            <!-- <td>แก้ไขโดย</td> -->
                            <!-- <td>แก้ไขเมื่อ</td> -->
                            <td>สถานะ</td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody class="align-middle text-start">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
<?php
include_once 'v_sidebar_end.php';
include_once 'v_footer.php';
?>
<script>
$(function() {
    initRewardListTable();
    getRewardListDataFromAPI().then(() => {
        handleScriptLoad();
    });
})
</script>