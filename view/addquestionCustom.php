<?php
session_start();
include_once 'v_head.php';
include_once 'v_sidebar_start.php';
require_once ("../class/class.question.php");
// ini_set('memory_limit', '2048M');

$go_ncadb = new ncadb();
$ncaquestion = new question($_GET['id']);

if (isset($_SERVER['HTTPS']) &&
    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
  $protocol = 'https://';
}
else {
  $protocol = 'http://';
}

$url_addlink = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if( strpos($url_addlink, "?id") !== false){
    $cuturl = explode("?id",$url_addlink);
    $url_addlink = $cuturl[0];
}

if($_GET['id']){

    $questioninfo = array();
    $questioninfo = $ncaquestion->getDataQuestion();
    $arr_parent = array();
    $htmlQuestion = "";
    $copy = 0;
    if($_GET['copy'] > 0){
        $copy = 1;
    }

    foreach($questioninfo AS $key => $val){

        if(!$val['questiondt_parent']){
            $htmlQuestion  .= $ncaquestion->generateIsParentQuestionCustom("questiondt",$val['questiondt'],0,$arr_parent);
        }
        
    }

    /* echo "<pre>";
    print_r($questioninfo);
 */
}

// Get questiontype
$sqlOptionType  = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
$arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");
// $arr_OptionType = $ncaquestion->ncaArrayConverter($arr_OptionType);

// Get หมวด
// $sqlmquestiontype  = "SELECT * FROM tb_questioncategories WHERE questioncategories_compfunc = '".$_SESSION['userData']['staffcompfunc']."' OR questioncategories_default = 1 AND questioncategories_active = 1 ";
$sqlmquestiontype  = "SELECT * FROM tb_questioncategories ";
$arrmquestiontype = $go_ncadb->ncaretrieve($sqlmquestiontype, "question");
// $arrmquestiontype  = $ncaquestion->ncaArrayConverter($arr_mquestiontype);

// Get กลุ่ม
$sqlquestiongroup  = "SELECT * FROM tb_questiongroup WHERE questiongroup_active = 1";
$arrquestiongroup = $go_ncadb->ncaretrieve($sqlquestiongroup, "question");
// $arrquestiongroup  = $ncaquestion->ncaArrayConverter($arr_questiongroup);

// Get ประเภทของคำถาม
$sqlquestionmode  = "SELECT * FROM tb_questionmode WHERE questionmode_active = 1";
$arrquestionmode = $go_ncadb->ncaretrieve($sqlquestionmode, "question");
// $arrquestionmode  = $ncaquestion->ncaArrayConverter($arr_questionmode);

// Get activities
$sqlactivities  = "SELECT * FROM tb_activities WHERE activities_active = 1";
$arr_activities  = $go_ncadb->ncaretrieve($sqlactivities, "question");
// $arr_activities = $ncaquestion->ncaArrayConverter($arractivities);

//
$sqlmistakelevele  = "SELECT *  FROM tb_mistakelevel WHERE mistakelevel_active = '1'";
$arrmistakelevele = $go_ncadb->ncaretrieve($sqlmistakelevele, "question");
// $arrmistakelevele  = $ncaquestion->ncaArrayConverter($arr_mistakelevele);


if($_GET['id'] > 0){
    $staffcompfunc    = ($questioninfo[0]['question_compfunc'] > 0 ? $questioninfo[0]['question_compfunc'] : $_SESSION['userData']['staffcompfunc']);
    $staffcompfuncdep = ($questioninfo[0]['question_compfuncdep'] > 0 ? $questioninfo[0]['question_compfuncdep'] : $_SESSION['userData']['staffcompfuncdep']);
    $mquestiontype    = $questioninfo[0]['question_questioncategories'];
    $questiongroup    = $questioninfo[0]['question_questioncategroup'];
    $questionmode     = $questioninfo[0]['question_questionmode'];
}else{
    $staffcompfunc    = $_SESSION['userData']['staffcompfunc'];
    $staffcompfuncdep = $_SESSION['userData']['staffcompfuncdep'];
    $mquestiontype    = "";
}

if($_GET['id'] > 0){
    if($copy > 0){ 
        $texttitle = " Copy คำชุดถามจาก : ".$questioninfo[0]['question_name']; 
    }else{
        $texttitle =  " แก้ไขชุดคำถาม";
    }
}else{
    $texttitle =  " เพิ่มข้อมูลชุดคำถามใหม่"; 
} 

?>

<form class="needs-validation" action="" enctype="multipart/form-data" method="POST" id="frm_submit" novalidate>

    <div class="row">

        <div class="col-lg-12">
        
            <div class="row">
                    
                <div class="row gy-3">
                    
                    <div class="col-lg-12 col-md-12">

                        <div class="col-lg-12">

                            <div class="w-100 d-flex mt-2">

                                <h4 class="me-auto">
                                    <?php echo $texttitle; ?>
                                
                                </h4>

                            </div>
                            
                            <hr>

                        </div>

                        <div class="col-lg-12 col-md-12 mt-2">

                            <div class="col-lg-12 col-md-12 mt-2">
                                <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-2">

                                        <label for="par_staffcompfunc" class="form-label">ฝ่าย <span class="text-danger">*</span></label>
                                        <select class="form-select" name="staffcompfunc" id="staffcompfunc" onchange="getDepartment();">
                                            <option value="0">เลือกสายงาน</option>
                                        </select>
                                    
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-2">

                                        <label for="par_staffcompfuncdep" class="form-label">ฝ่าย <span class="text-danger">*</span></label>
                                        <select class="form-select" name="staffcompfuncdep" id="staffcompfuncdep" onchange="getSection();">
                                            <option value="0">เลือกฝ่าย</option>
                                        </select>
                                    
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-2">

                                        <label for="par_staffcompfuncdepsec" class="form-label">แผนก <span class="text-danger">*</span></label>
                                        <select class="form-select" name="staffcompfuncdepsec" id="staffcompfuncdepsec" onchange="changestaffcompfuncdep()">
                                            <option value="0">เลือกแผนก</option>
                                        </select>
                                    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-12 col-md-12 mt-2">
                                
                                <div class="row">
                                    
                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-2">

                                        <label for="par_mquestiontype" class="form-label">หมวด <span class="text-danger">*</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input class="form-check-input" type="checkbox" value="1" name="mquestiontypecheck" id="mquestiontypecheck">
                                        <label class="form-check-label" for="mquestiontypecheck">
                                            ต้องการเพิ่มหมวดใหม่?
                                        </label>
                                        
                                        <span title="แก้ไข" onclick="editMquestiontype()" style="cursor: pointer;">
                                            <i class="bi bi-gear"></i> ซ่อน
                                        </span>
                                        
                                        <div id="mquestiontypeselect">
                                            <select class="form-select" name="mquestiontype" id="mquestiontype" onchange="changemquestiontype()" required>
                                                <option value="0">เลือกหมวด</option>
                                                <?php
                                                    
                                                    foreach ($arrmquestiontype as $key => $value) {
                                                        $selected = "";
                                                        /* if(
                                                            ($value['questioncategories_compfunc'] == $staffcompfunc && 
                                                            $value['questioncategories_compfuncdep'] == $staffcompfuncdep && 
                                                            $value['questioncategories_active'] == 1 &&
                                                            $value['questioncategories_hidden'] == 0) ||
                                                            ($value['questioncategories_default'] == 1)
                                                        ){ */
                                                        if($value['questioncategories_active'] == 1){

                                                            if($value['questioncategories'] == $mquestiontype){
                                                                $selected = "selected";
                                                            }
                                                            echo '<option value="'.$value['questioncategories'].'" '.$selected.'> '.$value['questioncategories_name'].' </option>';
                                                        }
                                                    }
                                                   
                                                ?>
                                            </select>
                                        </div>
                                        <div id="mquestiontypeinput" style="display: none;">
                                            <input type="text" id="mquestiontype_name" name="mquestiontype_name" class="form-control" value="" placeholder="เพิ่มประเภทใหม่">
                                        </div>
                                    </div>

                                    <!-- <div class="col-lg-6 col-md-6 col-sm-12 mt-2">

                                        <label for="par_mquestiongroup" class="form-label">กลุ่ม <span class="text-danger">*</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                        <input class="form-check-input" type="checkbox" value="1" name="questiongroupcheck" id="questiongroupcheck">
                                        <label class="form-check-label" for="questiongroupcheck">
                                            ต้องการเพิ่มกลุ่มใหม่?
                                        </label>
                                        <span title="แก้ไข" onclick="editquestiontGroup()" style="cursor: pointer;">

                                            <i class="bi bi-gear"></i> ซ่อน
                                        </span>
                                        <div id="mquestiongroupselect">
                                            <select class="form-select" name="questiongroup" id="questiongroup" required>
                                                <option value="0">เลือกกลุ่ม</option>
                                                <?php
                                                    if($_GET['id'] > 0){
                                                        foreach ($arrquestiongroup as $key => $value) {
                                                            if($value['questiongroup_questioncategories'] == $mquestiontype && $value['questiongroup_hidden'] == 0){
                                                                $selected = "";

                                                                if($value['questiongroup'] == $questiongroup){
                                                                    $selected = "selected";
                                                                }
                                                                echo '<option value="'.$value['questiongroup'].'" '.$selected.'> '.$value['questiongroup_name'].' </option>';
                                                            }
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div id="questiongroupinput" style="display: none;">
                                            <input type="text" id="questiongroup_name" name="questiongroup_name" class="form-control" value="" placeholder="เพิ่มกลุ่มใหม่">
                                        </div>
                                    </div> -->
                                    
                                </div>
                            </div>
                            
                           

                            <div class="col-lg-12 col-md-12 mt-2">

                                <label for="par_qname" class="form-label">ชื่อกลุ่มคำถาม <span class="text-danger">*</span></label>
                                <input type="text" id="par_qname" name="par_qname" class="form-control" required value="<?php echo $questioninfo[0]['question_name']; ?>">

                            </div>

                            <div class="col-lg-12 col-md-12 mt-2">

                                <label for="par_mquestionmode" class="form-label">ประเภท <span class="text-danger">*</span></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <!-- <input class="form-check-input" type="checkbox" value="1" name="mquestiontypecheck" id="mquestiontypecheck">
                                <label class="form-check-label" for="flexCheckDefault">
                                    ต้องการเพิ่มประเภทใหม่?
                                </label>
                                <span title="แก้ไข" onclick="editMquestiontype()" style="cursor: pointer;">
                                    <i class="bi bi-gear"></i> แก้ไข
                                </span> -->
                                <div id="mquestiontypeselect">
                                    <select class="form-select" name="questionmode" id="questionmode" required  <?php if($_GET['id'] > 0){ echo 'style="pointer-events: none;"'; } else{ echo ""; } ?> >
                                        <option value="0">เลือกประเภท</option>
                                        <?php
                                            foreach ($arrquestionmode as $key => $value) {
                                                $selected = "";
                                                if($value['questionmode'] == $questionmode){
                                                    $selected = "selected";
                                                }
                                                echo '<option value="'.$value['questionmode'].'" '.$selected.'> '.$value['questionmode_name'].' </option>';
                                                
                                            }
                                        ?>
                                    </select>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-lg-12 col-md-12">

                        <div class="col-lg-12">

                            <div class="w-100 d-flex mt-2">

                                <h4 class="me-auto mt-1">ส่วนคำถาม</h4>

                            </div>
                            <hr>

                        </div>

                        <div class="col-lg-12">

                            <div class="row">

                                <div class="col-lg-12 col-md-12">

                                    <div class="row">
                                        <? if(!$ncaquestion->id){ ?>
                                            <div class="col-lg-12 center">
                                                <span  class="btn btn-primary mb-3" id="mainQuestionStart" onclick="setQuestionmodal('question','mainQuestionStart');">
                                                    เริ่มสร้างคำถาม
                                                </span>
                                            </div>
                                        <? } ?>

                                        <div id="nestedQuestion" >
                                            <div class="nestedQuestionContent" class="list-group col">
                                                <? echo $htmlQuestion; ?>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 center">
                                            <span  class="btn btn-primary mt-3 mb-5 w-100" id="addQue" <? if($ncaquestion->id){ echo 'style="display: block;"'; }else{ echo 'style="display: none;"'; } ?> onclick="setQuestionmodal('question','mainQuestionContinue');">
                                                <i class="bi bi-file-plus"></i> เพิ่มคำถาม
                                            </span>
                                        </div>
                                        
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <input type="hidden"  class="form-control" name="questioninfoid" id="questioninfoid" value="<?php echo $_GET['id'];?>">
            <input type="hidden"  class="form-control" name="debug" id="debug" value="0">
            <input type="hidden"  class="form-control" name="questioncopy" id="questioncopy" value="<?php echo $copy?>">
            <input type="hidden"  class="form-control" name="mode" id="addQuestion" value="addQuestion">
            <input type="hidden"  class="form-control" name="par_userId" id="par_userId" value="<?php echo $_SESSION['userData']['stf']; ?>">
            <input type="hidden"  class="form-control" name="par_usernm" id="par_usernm" value="<?php echo $_SESSION['userData']['userdspms']; ?>">
            <button type="button" class="btn btn-primary w-100 mt-2" onclick="submitFrom()"><i class="bi bi-save"></i><?php if($copy > 0){ echo " บันทึกข้อมูลจากการ Copy "; }else{ echo " บันทึกข้อมูล"; } ?> </button>
        </div>

        <div class="col-lg-6 col-md-6 col-sm-6">
            <a href="list_question.php" class="btn btn-secondary w-100 mt-2"><i class="bi bi-back"></i> ย้อนกลับหน้ารายการ </a>
        </div>

    </div>
    
</form>

    <!-- Modal generate input box -->

    <div class="modal fade" id="generateinputbox" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">สร้างคำถาม</h1>
                    <span class="btn-close" data-bs-dismiss="modal" aria-label="Close"></span>
                </div>
                <div class="modal-body">

                    <!-- <form action="javascript:void(0);" id="frm_category"> -->

                        <div class="row gy-3">
                            
                            <div class="col-12">
                                <label class="form-label" for="option">คำถาม<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="optiontquestion" id="optiontquestion" required>
                            </div>

                            <!-- <div class="col-12">
                                <label class="form-label" for="par_icon">ลักษณะของการตรวจ<span class="text-danger">*</span></label>
                                <select class="form-select" name="activities" id="activities" aria-label="isshowing">
                                    <option value="0">เลือกลักษณะของการตรวจ</option>
                                    <?php    
                                        foreach ($arr_activities as $key => $value) {
                                            echo '<option value="'.$value['activities'].'">'.$value['activities_name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div> -->

                            <div class="col-12" id="normal_type">
                                <label class="form-label" for="par_icon">ประเภทคำตอบ<span class="text-danger">*</span></label>
                                <select class="form-select" name="optiontype" id="optiontype" aria-label="isshowing">
                                    <option value="0">เลือกประเภทคำตอบ</option>
                                    <?php    
                                        foreach ($arr_OptionType as $key => $value) {
                                            echo '<option value="'.$value['questiontype'].'">'.$value['questiontype_name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="option">จำนวน<span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="optiontnumber" id="optiontnumber" min="1" required>
                                
                                <input type="hidden" name="qmode" id="qmode" value="">
                                <input type="hidden" name="qtype" id="qtype" value="">
                                <input type="hidden" name="qinpname" id="qinpname" value="">
                                <input type="hidden" name="qinpclass" id="qinpclass" value="">
                                <input type="hidden" name="qafter" id="qafter" value="">
                                <input type="hidden" name="qafteroption" id="qafteroption" value="">
       
                            </div>
                            
                            <div class="col-12">
                                <span id="createOption" class="btn btn-primary w-100" >สร้าง</span>
                            </div>
                        </div>

                    <!-- </form> -->
                    
                </div>

            </div>

        </div>

    </div>

<!-- </main> -->

<!-- Modal setting mquestiontype START -->

<div class="modal fade" id="mquestiontypeSeting" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">แก้ไขประเภท</h1>
                <span class="btn-close" data-bs-dismiss="modal" aria-label="Close"></span>
            </div>
            <div class="modal-body">

                <form  action="" enctype="multipart/form-data" method="POST" id="frm_submit_mtype" novalidate>

                    <div class="row gy-3">
                        
                        <div class="col-12" style="max-height: 300px; overflow: auto;">
                            <!-- <label class="form-label" for="option">คำถาม<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="optiontquestion" id="optiontquestion" required> -->

                            <table id="rewardListTable" class="table table-bordered table-striped shadow-sm w-100">
                                <thead class="text-bg-primary" style="position: sticky; top: 0;">
                                    <tr>
                                        <td align="left">ชื่อ</td>
                                        <td align="center" style="width: 100px">ซ่อน</td>
                                    </tr>
                                </thead>
                                <tbody id="mt_tbody" style="border-top: none;">
                                    
                                <tbody>
                            </table>
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="mode" id="idupdatemtype" value="updatemtype">
                            <input type="hidden" name="mtstaffcompfunc" id="mtstaffcompfunc" value="<?php echo $staffcompfunc;?>">
                            <input type="hidden" name="mtstaffcompfuncdep" id="mtstaffcompfuncdep" value="<?php echo $staffcompfuncdep;?>">
                            <input type="hidden" name="mtcurrent" id="mtcurrent" value="<?php echo $mquestiontype;?>">
                            <span id="createOption" class="btn btn-primary w-100" onclick="submitFromMtype()"><i class="bi bi-save"></i> บันทึกข้อมูล</span>
                        </div>
                    </div>

                </form>
                
            </div>

        </div>

    </div>

</div>
    

<!-- Modal setting mquestiontype END -->

<!-- Modal setting questiongroup START -->

<div class="modal fade" id="questiongroupSetting" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" >
    <div class="modal-dialog modal-dialog-centered modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5">แก้ไขกลุ่ม</h1>
                <span class="btn-close" data-bs-dismiss="modal" aria-label="Close"></span>
            </div>
            <div class="modal-body">

                <form  action="" enctype="multipart/form-data" method="POST" id="frm_submit_group" novalidate>

                    <div class="row gy-3">
                        
                        <div class="col-12" style="max-height: 300px; overflow: auto;">
                            <!-- <label class="form-label" for="option">คำถาม<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="optiontquestion" id="optiontquestion" required> -->

                            <table id="rewardListTable" class="table table-bordered table-striped shadow-sm w-100">
                                <thead class="text-bg-primary" style="position: sticky; top: 0;">
                                    <tr>
                                        <td align="left">ชื่อ</td>
                                        <td align="center" style="width: 100px">ซ่อน</td>
                                    </tr>
                                </thead>
                                <tbody id="mt_tbody_group" style="border-top: none;">
   
                                <tbody>
                            </table>
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="mode" id="idupdategroup" value="updategroup">
                            <input type="hidden" name="groupcurrent" id="groupcurrent" value="<?php echo $questiongroup;?>">
                            <input type="hidden" name="groupcurrentcate" id="groupcurrentcate" value="<?php echo $mquestiontype;?>">
                            <span id="createOption" class="btn btn-primary w-100" onclick="submitFromGroup()"><i class="bi bi-save"></i> บันทึกข้อมูล</span>
                        </div>
                    </div>

                </form>
                
            </div>

        </div>

    </div>

</div>
    

<!-- Modal setting questiongroup END -->
<?php
include_once 'v_sidebar_end.php';
include_once 'v_footer.php';
?>
<script>
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            checkFileSizeAndExtension(this.id);
        });
    });

    var arr_OptionType = <?php echo json_encode($arr_OptionType); ?>;
    var arr_mquestiontype  = <?php echo json_encode($arrmquestiontype ); ?>;
    var arr_questiongroup  = <?php echo json_encode($arrquestiongroup ); ?>;
    var arr_activities  = <?php echo json_encode($arr_activities ); ?>;
    var arr_mistakelevele  = <?php echo json_encode($arrmistakelevele ); ?>;
    var questionId  = '<?php if($_GET['id'] > 0){ echo $_GET['id']; }else{ echo '0'; } ?>';
    var Iscopy  = '<?php if($_GET['copy'] > 0){ echo '1'; }else{ echo '0'; } ?>';
    var timeout = null;

    var arrmcompfunc = <?php echo json_encode($arrmcompfunc); ?>;
    var arrmcompfuncdep = <?php echo json_encode($arrmcompfuncdep); ?>;

    var arrmcompfunc = [];
    var arrmcompfunc_val = '<?php echo $questioninfo[0]['question_compfunc']; ?>';
    var arrmcompfuncdep = [];
    var arrmcompfuncdep_val = '<?php echo $questioninfo[0]['question_compfuncdep']; ?>';
    var arrmcompfuncdepsec = [];
    var arrmcompfuncdepsec_val = '<?php echo $questioninfo[0]['question_compfuncdepsec']; ?>';

    $(function() {

        $('.datepicker').datepicker({
            format: "dd/mm/yyyy",
            // todayBtn: "linked",
            clearBtn: false,
            multidate: false,
            language: "th",
            startDate : gStartDate,
            endDate : gEndDate,
            autoclose: true
        });
        
        handleScriptLoad();

        $("#optiontype").change(function(){
            if($("#questionmode").val() == 2){
                $("#optiontnumber").val("2")
                    $("#optiontnumber").attr("readonly",true);
            }else{
                if($(this).val()  < 4){
                    $("#optiontnumber").val("1")
                    $("#optiontnumber").attr("readonly",true);

                }else{
                    $("#optiontnumber").attr("readonly",false);
                    $("#optiontnumber").val("");
                }
            }
        });

        $("#createOption").click(function(){
            let data = [];  
            let optiontype            = $("#optiontype").val();
            let optiontquestion       = $("#optiontquestion").val();
            let optiontnumber         = $("#optiontnumber").val();
            let optiontqname          = $("#qinpname").val();
            let optiontqclass         = $("#qinpclass").val();
            // let optiontactivities     = $("#activities").val();
            let qmode                 = $("#qmode").val();
            let qtype                 = $("#qtype").val();
            let qafter                = $("#qafter").val();
            let qafteroption          = $("#qafteroption").val();
            data['optiontype']        = $("#optiontype").val();
            data['optiontquestion']   = $("#optiontquestion").val();
            data['optiontnumber']     = $("#optiontnumber").val();
            data['optiontqname']      = $("#qinpname").val();
            data['optiontqclass']     = $("#qinpclass").val();
            data['qmode']             = $("#qmode").val();
            data['qtype']             = $("#qtype").val();
            data['qafter']            = $("#qafter").val();
            data['qafteroption']      = $("#qafteroption").val();
            // data['optiontactivities'] = $("#activities").val();

            if(optiontquestion == ""){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุคำถาม ด้วยค่ะ");
                return false;
            }

            /* if(optiontactivities == "0"){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุลักษณะของการตรวจ ด้วยค่ะ");
                return false;
            } */

            if(optiontype == "0" ){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุประเภทคำตอบ ด้วยค่ะ");
                return false;
            }

            if(optiontype > 3 ){
                if(optiontnumber == "" || optiontnumber == 0){
                    fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุจำนวน ด้วยค่ะ");
                    return false;
                }
            } 

            if(optiontnumber == "" || optiontnumber == "0"){
                if(optiontnumber == "" || optiontnumber == 0){
                    fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุจำนวน ด้วยค่ะ");
                    return false;
                }
            }

            let html = '';
            if(qmode == 'question'){
                html = generateInput(data);

                if(optiontqname){
                    if(qtype == "after"){
                        $("."+optiontqname).append(html);
                    }else{
                        $("."+optiontqname).html(html);
                    }

                    $("."+optiontqname).removeClass('hide');

                }else{  
                    
                    if(qtype == "mainQuestionContinue"){
                        $(".nestedQuestionContent").append(html);
                    }else{
                        $(".nestedQuestionContent").html(html);
                    }
                }
                
            }else{

                html = createGroupquestion(data);
                $(".nestedQuestionContent").append(html);
            }
            
            if($("#questionmode").val() > 0){
                $("#questionmode").css('pointer-events','none'); 
            }

            $('#generateinputbox').modal('hide'); 

            if(qtype == 'mainQuestionStart'){
                $('#mainQuestionStart').hide(); 
                $('#addQue').show();
            }

            initSortable();

        })

        $('.question').each(function () {
            var hue = 'rgb(' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ')';
            $(this).css("background-color", hue);
        });

        $("#mquestiontypecheck").click(function(){
            let checked  = $(this).is(":checked");
            if(checked == true){
                $("#mquestiontypeselect").hide();
                $("#mquestiontype_name").prop('required',true);
                $("#mquestiontypeinput").show();
            }else{
                $("#mquestiontype_name").prop('required',false);
                $("#mquestiontypeselect").show();
                $("#mquestiontypeinput").hide();
                
            }
        })

        $("#questiongroupcheck").click(function(){
            let checked  = $(this).is(":checked");
            if(checked == true){
                $("#mquestiongroupselect").hide();
                $("#questiongroup_name").prop('required',true);
                $("#questiongroupinput").show();
            }else{
                $("#questiongroup_name").prop('required',false);
                $("#mquestiongroupselect").show();
                $("#questiongroupinput").hide();
                
            }
        })

        $("body").on("change", '.changevaluemistake', function(){
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                if($("#questionmode").val() == 2){
                    let attrid = $(this).attr("id"); 
                    attrid = attrid.replace("optionvalue", ""); 

                    if( $(this).val() == ""){
                        $(this).val(0);
                    }

                    if($(this).val() > 0 || $(this).val() == ""){
                        $("#questionoption_mistakelevel"+attrid).css("display","none");
                        $("#questionoption_mistakelevel_"+attrid).val(0);
                    }else{
                        
                        $("#questionoption_mistakelevel"+attrid).css("display","inline-block");
                        $("#questionoption_mistakelevel_"+attrid).val(0);
                    }
                }

            }, 300);
        });


        getCompfunc();
        // getDepartment();

    });

    function createGroupquestion(data){

        html += generateMainParentStart();
        html += generateInput(data);
        html += generateMainParentEnd();
        return html;
    }

    function generateMainParentStart(id){

        let html = `<div id="`+id+`" class="list-group-item nested-sortable setName" data-id="set2">`;
        html = `<div class="class="list-group-item content"></div>`;
        return html;

    }

    function generateMainParentEnd(){

        let html = `</div>`;
        return html;

    }

    function generateInput(data) {

        let type         = data['optiontype'];
        let opques       = data['optiontquestion'];
        let number       = data['optiontnumber'];
        let opname       = data['optiontqname'];  
        let opclass      = data['optiontqclass'];   
        var opactivities = data['optiontactivities'];   
        let opqmode      = data['qmode'];  
        let qtype        = data['qtype'];  
        let qafter       = data['qafter'];  
        let qafteroption = data['qafteroption'];  
        let inputType    = parseInt(parseInt(type) - 1);
        let html         = '';
        let questionmode = $("#questionmode").val();
        
        name = makeid(15);

        let inptype     = arr_OptionType[inputType]["questiontype_type"];
        let inptypename = arr_OptionType[inputType]["questiontype_name"];

        mainname = makeid(15);
        var bgColor = 'rgb(' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ')';
        html += `<div class="content`+name+`" id="`+mainname+`" >`;
        html += `   <input type="hidden" name="mainname[]" value="`+mainname+`" >`;
        html += `   <div class="list-group-item nested-3 question" id="`+name+`" data-id="`+name+`" style="background-color :`+bgColor+`">`;
        // html += `      <span class="btn btn-primary " id="delQuestion" style="position: absolute;right: 8px;" onclick="if(confirm('ยืนยันลบคำถามชุดนี้?')) { $('#`+name+`').remove(); }">ลบ</span>`;
        let listquest = $("input[name='questionnameinfrom["+qafter+"]']").length
        html += `      <span class="btn btn-primary " id="delQuestion`+name+`" style="position: absolute;right: 8px;" onclick="deleteQuestionoption('`+name+`','`+qafter+`','question`+name+`',0);">ลบ</span>`;
        // html += `   <input type="hidden" name="questionname[`+name+`]" value="`+name+`" >`;
        html += `       <input type="hidden" name="questionname[]" value="`+name+`" >`;
        html += `       <input type="hidden" name="datainputtype[]" value="`+type+`" >`;
        html += `       <input type="hidden" name="questionnameinput[`+name+`]" value="`+inptype+`" >`;
        html += `       <input type="hidden" name="questionismain[`+name+`]" value="`+(qafter ? "" : "1")+`" >`;
        html += `       <input type="hidden" name="questionismainname[`+name+`]" value="`+(qafter ? "" : name)+`" >`;
        html += `       <input type="hidden" name="questionnameinputafter[`+name+`]" value="`+qafter+`" >`;
        html += `       <input type="hidden" name="questionnameinfrom[`+qafter+`]" value="`+qafter+`" >`;
        html += `       <input type="hidden" name="questionactivities[`+name+`]" value="`+opactivities+`" >`;
        if(qafter){
            html += `       <input type="hidden" name="questionnameinputparent[`+qafter+`][]" value="`+name+`" >`;
        }
        html += `       <input type="hidden" name="questionnameinputafteroptoion[`+name+`]" value="`+qafteroption+`" >`;

        // html += `           <div class="col-lg-12">`;
        // html += `               คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext[`+name+`]" required="" value="`+opques+`">`;
        // html += `           </div>`;

        html += `<div class="form-group row">`;
        html += `    <div class="col-lg-6">`;
        html += `        คำถาม : <input class="form-control-50 col-lg-10" type="text" name="questiontext[`+name+`]" required="" value="`+opques+`">`;
        html += `    </div>`;
        html += `    <span style="float: left; width: unset; line-height: 37px;">เลือกลักษณะของการตรวจ : </span>`;
        html += `    <div class="col-sm-3">`;

        html += `    <select class="form-select-40" name="questionactivities[`+name+`] id="questionactivities_`+name+`" aria-label="isshowing" required>`;
        arr_activities.forEach(element => {
            let selected = "";
            if(opactivities == element.activities){
                selected = "selected";
            }
            html += `<option value="`+element.activities+`" `+selected+`> `+element.activities_name+` </option>`;
        });

        html += `    </select>`;
        html += `    </div>`;
        html += `</div>`;
        
        html += `           <div class="list-group nested-sortable">`;

        let htmlmistakelevele = `    <option value="0">เลือกน้ำหนักความผิด</option>`;         
        arr_mistakelevele.forEach(element => {
            htmlmistakelevele += `<option value="`+element.mistakelevel+`" >`+element.mistakelevel_shortname+`(`+element.mistakelevel_value+`) </option>`;
        });
        
        $order = 1
        for (let index = 0; index < number; index++) {

            html += `<div class="list-group-item nested-2 answer questionquestion border-none ms-4" data-id="`+name+index+`" style="">`;
            let formctr = '';
            $readonly = "";
            if(inputType  < 2 ){
                formctr = `form-control-50 col-lg-6`;
            }

            let optionname = makeid(3);

            let inp = `<input type="hidden" name="`+name+index+`" value="`+inptype+`" >`;
            inp += inptypename+` `+$order+` : <input class="form-control-40" type="text" name="option`+name+`[]" id="option`+name+index+`" `+(parseInt(inputType) < 3 ? `readonly="readonly" ` : ` required `)+`>`;
            if(parseInt(inputType) > 2){
                inp += ` คะเเนน : <input class="form-control-custom col-lg-1 changevaluemistake" type="number" name="optionvalue`+name+`[]" id="optionvalue`+name+index+`" >`;
            }

            inp += `<div id="questionoption_mistakelevel`+name+index+`" style="display: none;">`;
            inp += `                    <select class="form-select-40 ms-1 selectmistakeoption" id="questionoption_mistakelevel_`+name+index+`" name="questionoption_mistakelevel[`+name+`][`+index+`]" >`;
            inp +=                         htmlmistakelevele;
            inp += `                    </select>`;
            inp += `                </div>`;


            inp += `<label class="form-check-label" for="questionoption_images_`+optionname+`">`;
            inp += `&nbsp;ต้องการให้เเนบรูปหรือไม่ :&nbsp;`;
            inp += `</label>`;
            inp += `<input class="form-check-input" type="checkbox" style="vertical-align: middle;" value="1" id="questionoption_images_`+optionname+`" name="questionoption_images[`+name+`][`+index+`]">`;

            inp += `<input type="hidden" name="optionid`+name+index+`[]" id="optionid`+name+index+`" value="`+index+`" >`;

            inp += `<input type="hidden" name="questionoption[`+name+`][]" id="questionoption`+name+index+`" value="`+optionname+`" >`;

            inp += `<div class="list-group-item nested-3 hide question`+name+index+` `+(opques ? 'ms-3 mt-3 mb-3 ' : '')+`" >`;
            inp += `</div>`;
           
            if(questionmode != 2){
                inp += `<span class="btn btn-primary ms-3" id="addquestion`+name+index+`" style="" onclick="setQuestionmodal('question','af','question`+name+index+`','`+name+`' ,'option`+name+index+`');">สร้างคำถาม</span>`;
            }

            html += inp;
            html += `</div>`;

            $order++;
        }
                
        html += `       </div>`;
        html += `   </div>`;
        html += `</div>`;
       
        
        if(opname && qtype != 'after'){
            html += `<div class="col-lg-12">`;
                html += `<span class="btn btn-primary mt-3"  onclick="setQuestionmodal('question','after','content`+name+`','`+qafter+`','`+qafteroption+`' );"><i class="bi bi-file-plus"></i> เพิ่มคำถาม</span>`;
            html += `</div>`;
        }

        if(opname ){
            $("#add"+opname).hide();  
            $("#add2"+opname).show();  
        }

        return html;

    }

    function deleteQuestion(id){
        let html = '<input type="hidden" name="deleteQuestion[]" value="'+id+'" >';
        $("#deleteQuestionList").append(html);
    }

    function setQuestionmodal(qmode,qtype,qname,after,afteroption){

        if($("#questionmode").val() == 0){
            fireSwalOnErrorCustom("กรุณาเลือกประเภท ด้วยค่ะ");
            return false;
        }

        clearModal();
        if($("#questionmode").val() == 2){

            let html_questiontype = '<select class="form-select" name="optiontype" id="optiontype" aria-label="isshowing">';
            html_questiontype += ' <option value="0">เลือกประเภทคำตอบ</option>';

            arr_OptionType.forEach(element => {
                if(element.questiontype == 4){
                    html_questiontype += '<option value="'+element.questiontype+'" > '+element.questiontype_name+' </option>';
                }
            });
            html_questiontype += '</select>';

            $("#optiontype").html(html_questiontype);
        }else{

            let html_questiontype = '<select class="form-select" name="optiontype" id="optiontype" aria-label="isshowing">';
            html_questiontype += ' <option value="0">เลือกประเภทคำตอบ</option>';

            arr_OptionType.forEach(element => {
                html_questiontype += '<option value="'+element.questiontype+'" > '+element.questiontype_name+' </option>';
            });
            html_questiontype += '</select>';

            $("#optiontype").html(html_questiontype);
        }

        $('#qinpname').val(qname);
        $('#qmode').val(qmode); // question || parent
        $('#qtype').val(qtype);
        $('#qafter').val(after);
        $('#qafteroption').val(afteroption);
        $('#generateinputbox').modal('show');

    }

    function makeid(length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < length) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }
        return result;
    }

    function clearModal(){
        $("#optiontype").val(0);
        $("#activities").val(0);
        $("#optiontquestion").val('');
        $("#optiontnumber").val('');
        $("#qinpname").val('');
        $("#qinpclass").val('');
        $("#qmode").val('');
        $("#qtype").val('');
        $("#qafter").val('');
        $("#qafteroption").val('');
    }

    function submitFrom(){
        let validateSelect = true;
        let staffcompfunc = ($("#staffcompfunc").val() ? $("#staffcompfunc").val() : 0);
        if(staffcompfunc == "0"){
            fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุฝ่าย ด้วยค่ะ");
            return false;
        }
        
        let staffcompfuncdep = ($("#staffcompfuncdep").val() ? $("#staffcompfuncdep").val() : 0);
        if(staffcompfuncdep == "0"){
            fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุแผนก ด้วยค่ะ");
            return false;
        }

        let mquestiontype = ($("#mquestiontype").val() ? $("#mquestiontype").val() : 0);
        if(mquestiontype == "0"){
            
            if($("#mquestiontype_name").val() == ""){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุหมวด ด้วยค่ะ");
                return false;
            }
        }

        let questiongroup = ($("#questiongroup").val() ? $("#questiongroup").val() : 0);
        if(questiongroup == "0"){
            if($("#questiongroup_name").val() == ""){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุกลุ่ม ด้วยค่ะ");
                return false;
            }
        }
        
        if($("#questiongroupcheck").is(":checked")){
            let questionmode = ($("#questiongroup_name").val() == "" ? 0 : 1);
        }else{
            let questionmode = ($("#questionmode").val() ? $("#questionmode").val() : 0);
        }
        if(questionmode == "0"){
            fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุประเภท ด้วยค่ะ");
            return false;
        }

        let validate = validateForm();
        if(validate == true){

            if(parseInt($("#questionmode").val()) == 2){
                $(".changevaluemistake").each(function( index,element ) {
                    if($("#"+element.id).val() == '0'){

                        let inputid = element.id;
                        inputid = inputid.replace("optionvalue", "questionoption_mistakelevel_");
                        let selectMistake = $("#"+inputid).val();
                        
                        if(selectMistake == 0){
                            validateSelect = false;
                        }

                    }
                });
            }

            if(validateSelect == false){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุน้ำหนักความผิด ด้วยค่ะ");
                return  false;
            }
            
            var form = $("#frm_submit");
            var actionUrl = "../phpfunc/curdCustom.php";
            let questioninfoid = $("#questioninfoid").val();
            
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: form.serialize(), // serializes the form's elements.
                dataType: "JSON",
                beforeSend: function(){
                    fireSwalOnSubmit();
                },
                success: function(obj)
                {
                    
                    var res = obj.data;
                    if(res.success > 0){
                        alertSwalSuccess();
                        if(res.questioninfoid > 0){
                            setTimeout(() => {
                                    let url_replace = "<?php echo $url_addlink;?>?id="+res.questioninfoid;
                                    window.location.replace(url_replace);
                                }, 1000);
                        }else{
                            if(questioninfoid > 0){
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }else{
                                
                                setTimeout(() => {
                                    let url_replace = "<?php echo $url_addlink;?>?id="+res.questioninfoid;
                                    window.location.replace(url_replace);
                                }, 1000);
                            }
                        }
                        // swal.close()
                        // alertSwalSuccess();
                    }else{
                        fireSwalOnError("บันทึกข้อมูลไม่สำเร็จ <br>"+res.sql);
                    }
                }
            });

        }else{
            fireSwalOnError("กรุณาใส่ข้อมูลให้ครบ ด้วยค่ะ");
        }  

    }

    function validateForm(){
        var forms = document.querySelectorAll('.needs-validation');
        var validate = ""
        Array.prototype.slice.call(forms).forEach(function(form) {

            validate = form.checkValidity();
            if(validate == false){
                $("input:invalid").css("border","1px solid red");
                $("select:invalid").css("border","1px solid red");
                $("textarea:invalid").css("border","1px solid red");
            }
            
        });

        return validate;
        
    }

    function changemquestiontype(){

        var form = $("#frm_submit_mtype");
        var actionUrl = "../phpfunc/curdCustom.php";
        
        $.ajax({
            type: "POST",
            url: actionUrl,
            data: {
                mode: "questionGroupdata",
                questioncate : $("#mquestiontype").val(),
                current_group : $("#questiongroup").val(),
            },
            dataType: "JSON",
            success: function(obj)
            {
                var res = obj.data;
                $("#questiongroup").html(res.html);

            }
        });
        
    }   

    function editMquestiontype(){

        if($("#staffcompfuncdep").val() > 0){

            var form = $("#frm_submit_mtype");
            var actionUrl = "../phpfunc/curdCustom.php";
            
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: {
                    mode: "mquestiontypedata",
                    compfunc : $("#staffcompfunc").val(),
                    compfuncdep : $("#staffcompfuncdep").val(),
                    currentmquestiontype : $("#mquestiontype").val(),
                },
                dataType: "JSON",
                // beforeSend: function(){
                //     fireSwalOnSubmit();
                // },
                success: function(obj)
                {
                    var res = obj.data;
                    $("#mt_tbody").html(res.htmlmodal);

                }
            });

            $("#mquestiontypeSeting").modal("show");
        }else{
            fireSwalOnErrorCustom("กรุณาเลือกแผนก ด้วยค่ะ");
        }
    }

    function editquestiontGroup(){

        if($("#mquestiontype").val() > 0){

            var form = $("#frm_submit_mtype");
            var actionUrl = "../phpfunc/curdCustom.php";
            
            $.ajax({
                type: "POST",
                url: actionUrl,
                data: {
                    mode: "questionGroupdata",
                    questioncate : $("#mquestiontype").val(),
                    current_group : $("#questiongroup").val(),
                },
                dataType: "JSON",
                success: function(obj)
                {
                    var res = obj.data;
                    $("#mt_tbody_group").html(res.htmlmodal);

                }
            });

            $("#questiongroupSetting").modal("show");
        }else{
            fireSwalOnErrorCustom("กรุณาเลือกหมวด ด้วยค่ะ");
        }
    }

    function deleteQuestionoption(name,main,cont,type){
        Swal.fire({
			title: "คุณต้องการที่จะลบคำถามนี้หรือไม่?",
			//text: `รายการ : ${name}`,
			icon: "question",
			showCancelButton: true,
			confirmButtonText: "ใช่",
			cancelButtonText: "ไม่",
			confirmButtonColor: "#FE0000",
		}).then((result) => {
			if (result.isConfirmed == true) {
                if(questionId > 0 && Iscopy == 0){ deleteQuestion(name); }
				// $('#'+name).remove();
                if(type == 1){
				    $('#'+name).css("display","none");
                }else{
				    $('#'+name).remove();
                }
                $('#questiondtdeleted_'+name).val(1);
                let countquest = $("input[name='questionnameinfrom["+main+"]']").length
                if(countquest == 0){ $("."+cont).hide(); $("#addquestionquestion"+main).show(); }
			}
		});
    }

    function editmname(name1,name2,hiddent,id){
        $("#"+name1).show()
        $("#"+name2).hide()
        if(hiddent.indexOf('editmnamechange_') != -1){
            $("#"+hiddent).val("1");
        }else{
            $("#editmnamechange_"+id).val("");
        }
    }

    function submitFromMtype(){

        var form = $("#frm_submit_mtype").serialize();
        var actionUrl = "../phpfunc/curdCustom.php";
        var form = $("#frm_submit_mtype");
        var currentmquestiontype = $("#mquestiontype").val();

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(), // serializes the form's elements.
            dataType: "JSON",
            beforeSend: function(){
                fireSwalOnSubmit();
            },
            success: function(obj)
            {
                var res = obj.data;
                if(res.success > 0){

                    if($("#mquestiontype").val() == currentmquestiontype){
                       
                        setTimeout(() => {
                            $("#mquestiontype").val(currentmquestiontype);
                            console.log($("#mquestiontype").val() , currentmquestiontype);
                        }, 1200);
                        
                    }else{
                        $("#mquestiontype").val(0);
                        $("#questiongroup").html(`<select class="form-select" name="questiongroup" id="questiongroup" required=""><option value="0">เลือกกลุ่ม</option></select>`);
                    }

                    alertSwalSuccess();
                    
                    $("#mquestiontypeselect").html(res.html);
                    $("#mquestiontypeSeting").modal("hide");
                    
                    
                    setTimeout(() => {
                        swal.close();
                    }, 1200);

                }else{
                    fireSwalOnError("บันทึกข้อมูลไม่สำเร็จ <br>"+res.sql);
                }
            }
        });

    }

    function submitFromGroup(){

        var form = $("#frm_submit_group");
        var actionUrl = "../phpfunc/curdCustom.php";
        var questiongroup = $("#questiongroup").val();
        console.log("questiongroup",questiongroup);

        $.ajax({
            type: "POST",
            url: actionUrl,
            data: form.serialize(), // serializes the form's elements.
            dataType: "JSON",
            beforeSend: function(){
                fireSwalOnSubmit();
            },
            success: function(obj)
            {
                var res = obj.data;
                if(res.success > 0){

                    $("#questiongroup").val(questiongroup);

                    if($("#questiongroup").val() == questiongroup){
                        $("#questiongroup").val(questiongroup);
                    }else{
                        $("#questiongroup").val(0);
                    }

                    alertSwalSuccess();
                    $("#mquestiongroupselect").html(res.html);
                    $("#questiongroupSetting").modal("hide");
                    
                    setTimeout(() => {
                        swal.close();
                    }, 1200);

                }else{
                    fireSwalOnError("บันทึกข้อมูลไม่สำเร็จ <br>"+res.sql);
                }
            }
        });

    }

    function getRandomColor() {
        var letters = '0123456789ABCDEF';
        var color = '#';
        for (var i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
        }
        return color;
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
            success: function(data){
                let res = data;
                // console.log(res.respCode);
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
        let staffcompfunc = $("#staffcompfunc").val();
        let html = ``;
        let htmlselect = ``;
        console.log("arrmcompfunc", arrmcompfunc,arrmcompfunc_val);
        html = `<option value="0">เลือกสายงาน</option>`;
     
        arrmcompfunc.forEach(element => {
            if (arrmcompfunc.length > 0) {
                let selectted = "";
                if (arrmcompfunc_val == element.compfunc_id) {
                    selectted = "selected";
                }
                html += `<option value="` + element.compfunc_id + `" `+selectted+`>` + element.compfunc_name + `</option>`;
            }
        });

        $("#staffcompfunc").html(html);
        console.log(html); 

        if(arrmcompfunc_val){
            getDepartment();
        }

    }

    function getDepartment() {
        let compfunc = $("#staffcompfunc").val();
        console.log("par_compfuncid", compfunc);
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
                    console.log("getDepartment", data);
                    if (res.respCode == 1) {
                        let objData = res.data;
                        arrmcompfuncdep = objData;

                        changestaffcompfunc();
                    }

                },
                error: function(xhr, status, error) {
                    console.log("An error occurred: ", status, error);
                },
            });
        }
    }

    function changestaffcompfunc() {
        let html = ``;
        console.log("arrmcompfuncdep", arrmcompfuncdep);
        let staffcompfunc = $("#staffcompfunc").val();
        html += `<option value="0">เลือกฝ่าย</option>`;
        arrmcompfuncdep.forEach(element => {
            let selectted = "";
            if (arrmcompfuncdep_val == element.department_id) {
                selectted = "selected";
            }
            html += `<option value="` + element.department_id + `" `+selectted+`>` + element.department_name + `</option>`;

        });

        console.log(html);
        $("#staffcompfuncdep").html("");
        $("#staffcompfuncdep").html(html);
        
        console.log("arrmcompfunc_val",arrmcompfunc_val);
        if(arrmcompfuncdep_val){
            getSection();
        }

    }

    function getSection(id="") {
        
        let compfuncdep = (arrmcompfuncdep_val > 0 ? arrmcompfuncdep_val : $("#staffcompfuncdep").val());
        
        console.log("par_departmentid", compfuncdep);
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
                    console.log("getSection", data);
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
        let htmlselect = ``;
        console.log("changestaffcompfuncsec",id);

        html +=     `<option value="0">เลือกแผนก</option>`;

        arrmcompfuncdepsec.forEach(element => {
            let selectted = "";
            if (arrmcompfuncdepsec_val == element.section_id) {
                selectted = "selected";
            }
            html += `<option value="` + element.section_id + `" ` + selectted + `>` + element.section_name + `</option>`;
        });

        console.log(html);
        $("#staffcompfuncdepsec").html(html);
        

    }
    
</script>

<style>

    .list-group-item {
        background-color: unset;
    }

    .list-group-item.nested-3 {
        background-color: #FFFFFF;
    }

    .select2-container--bootstrap-5 .select2-dropdown .select2-results__options:not(.select2-results__options--nested) {
        max-height: 23rem !important;
    }

    input[type=text]:read-only,input[type=number]:read-only {
        background-color: #E7E9EB;
    }
    .cpointer{
        cursor: pointer;
    }

    input[type=checkbox] {
        font-size: 18px;
        margin: 0 auto;
    }
</style>
