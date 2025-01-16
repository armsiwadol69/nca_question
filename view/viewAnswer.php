<?php
session_start();
include_once 'v_head.php';
include_once 'v_sidebar_start.php';
// require_once ("../class/class.question.php");
// require_once ("../class/class.answer.php");
// ini_set('memory_limit', '2048M');

if(!$_GET['answerid'] && !$_GET['id']){
   
    echo "<script> alert('ไม่พบข้อมูล');  window.close(); window.opener.focus();</script>";
    
}

$go_ncadb = new ncadb();

$ncaanswer = new answer($_GET['answerid'],$_GET['id']);
$datanswer = $ncaanswer->getDataAnswer();
$datanswerdt = $ncaanswer->getAnswerdt();

$arrCompfunc = array();
$arrcompfunc = $ncaanswer->getCompfuncData();
$compfuncname = "";
if($arrcompfunc['respCode'] == "1"){
    $compfun = $arrcompfunc['data'];
    foreach ($compfun as $key1 => $value1) {
        if($datanswer[0]['answer_compfunc'] == $value1['compfunc_id']){
            $compfuncname = $value1['compfunc_name'];
        }
    }
}

$compfuncdepname = "";
$arrCompfuncdep = array();
$arrcompfuncdep = $ncaanswer->getDepartmentData($datanswer[0]['answer_compfunc']);
if($arrcompfuncdep['respCode'] == "1"){
    $compfuncdep = $arrcompfuncdep['data'];
    
    foreach ($compfuncdep as $key1 => $value1) {
        if($datanswer[0]['answer_compfuncdep'] == $value1['department_id']){
            $compfuncdepname  = $value1['department_name'];
        }
    }
}

$compfuncdepsecname = "";
$arrCompfuncdepsec = array();
$arrcompfuncdepsec = $ncaanswer->getSectionData($datanswer[0]['answer_compfuncdep']);
if($arrcompfuncdepsec['respCode'] == "1"){
    $compfuncdepsec = $arrcompfuncdepsec['data'];
    foreach ($compfuncdepsec as $key1 => $value1) {
        if($datanswer[0]['answer_compfuncdepsec'] == $value1['section_id']){
            $compfuncdepsecname  = $value1['section_name'];
        }
    }
}

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

/* echo "<pre>";
print_r($ncaanswer);
echo "</pre>";
 */
if($_GET['id']){
    $questioninfo = array();
    $questioninfo = $ncaanswer->getDataQuestion();
    $arr_parent = array();
    $htmlQuestion = "";
    $formId = $questioninfo[0]["question"];
    $formName = $questioninfo[0]["question_name"];
    $formDes = $questioninfo[0]["question_detail"];
    foreach($questioninfo AS $key => $val){
        if(!$val['questiondt_parent']){
            $htmlQuestion  .= $ncaanswer->genareteViewAnswerFormData("questiondt",$val['questiondt'],0,$arr_parent);
        }
    }
}

if($_GET['id'] > 0){
    $staffcompfunc    = ($questioninfo[0]['question_compfunc'] > 0 ? $questioninfo[0]['question_compfunc'] : $_SESSION['userData']['staffcompfunc']);
    $staffcompfuncdep = ($questioninfo[0]['question_compfuncdep'] > 0 ? $questioninfo[0]['question_compfuncdep'] : $_SESSION['userData']['staffcompfuncdep']);
    $mquestiontype    = $questioninfo[0]['question_questioncategories'];
    $questiongroup    = $questioninfo[0]['question_questioncategroup'];
    $questionmode     = $questioninfo[0]['question_questionmode'];
}else{
    $staffcompfunc       = $_SESSION['userData']['staffcompfunc'];
    $staffcompfuncdep    = $_SESSION['userData']['staffcompfuncdep'];
    $staffcompfuncdepsec = $_SESSION['userData']['staffcompfuncdepsec'];
    $mquestiontype       = "";
}


$sqlmquestiontype  = "  SELECT * FROM tb_questioncategories WHERE questioncategories = '".$mquestiontype."' ";
// $sqlmquestiontype  = "SELECT * FROM tb_questioncategories ";
$arrmquestiontype = $go_ncadb->ncaretrieve($sqlmquestiontype, "question");
// $arrmquestiontype  = $ncaanswer->ncaArrayConverter($arr_mquestiontype);

$sqlOptionType  = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
$arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");

// Get กลุ่ม
$sqlquestiongroup  = "SELECT * FROM tb_questiongroup WHERE questiongroup = '".$questiongroup."'";
$arr_questiongroup = $go_ncadb->ncaretrieve($sqlquestiongroup, "question");
$arrquestiongroup  = $ncaanswer->ncaArrayConverter($arr_questiongroup);

// Get ประเภทของคำถาม
$sqlquestionmode  = "SELECT * FROM tb_questionmode WHERE questionmode = '".$questionmode."'";
$arr_questionmode = $go_ncadb->ncaretrieve($sqlquestionmode, "question");
$arrquestionmode  = $ncaanswer->ncaArrayConverter($arr_questionmode);

// Get activities
// $sqlactivities  = "SELECT * FROM tb_activities WHERE activities_active = 1";
// $arractivities  = $go_ncadb->ncaretrieve($sqlactivities, "question");
// $arr_activities = $ncaanswer->ncaArrayConverter($arractivities);

//
// $sqlmistakelevele  = "SELECT *  FROM tb_mistakelevel WHERE mistakelevel_active = '1'";
// $arr_mistakelevele = $go_ncadb->ncaretrieve($sqlmistakelevele, "question");
// $arrmistakelevele  = $ncaanswer->ncaArrayConverter($arr_mistakelevele);



$texttitle =  "ผลการตอบคําถาม"; 

?>

<!-- <form class="needs-validation" action="../phpfunc/curd.php?mode=addQuestion" enctype="multipart/form-data" method="POST" id="frm_submiะ" novalidate> -->
<form class="needs-validation" action="" enctype="multipart/form-data" method="POST" id="frm_submit" novalidate>

    <div class="col-lg-12 col-md-12">

        <div class="row">

            <div class="col-lg-6">

                <div class="row border rounded-2"> 

                    <div class="col-lg-12 col-md-12 mt-2">

                        <div class="col-lg-12 col-md-12 mt-2">

                            <div class="col-lg-12 col-md-12 mt-2">

                                <div class="row">

                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-2">

                                        <label for="par_staffcompfunc" class="form-label">สายงาน : <?php echo $compfuncname; ?></label>
                                    
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-2">

                                        <label for="par_staffcompfuncdep" class="form-label">ฝ่าย : <?php echo $compfuncdepname; ?></label>
                                    
                                    </div>

                                    <div class="col-lg-12 col-md-12 col-sm-12 mt-2">

                                        <label for="par_staffcompfuncdepsec" class="form-label">แผนก : <?php echo $compfuncdepsecname; ?></label>
                                    
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 mt-2">
                                
                                <div class="row">
                                    
                                    <div class="col-lg-6 col-md-6 col-sm-12 mt-2">

                                        <label for="par_mquestiontype" class="form-label">หมวด : <?php echo $arrmquestiontype[0]['questioncategories_name']; ?> </label>

                                </div>

                            </div>

                            <div class="col-lg-12 col-md-12 mt-2">

                                <label for="par_qname" class="form-label">ประเภท : <?php echo $arrquestionmode[0]['questionmode_name']; ?></span></label>

                            </div>


                            <div class="col-lg-12 col-md-12 mt-2">

                                <label for="par_qname" class="form-label">ชื่อของชุดคำถาม : <?php echo $questioninfo[0]['question_name']; ?></label>

                            </div>

                            <div class="col-lg-12 mt-2 ">

                                <label for="par_qdatail" class="form-label">รายละเอียด : <?php echo $questioninfo[0]['question_detail']; ?></label>

                            </div>

                        </div>
                    
                    </div>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="row  border rounded-2">  

                    <div class="col-md-12 col-lg-12">

                        <?php echo $htmlQuestion; ?>

                    </div>

                </div>

            </div>

        </div>

    </div>
    
</form>

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

    var arrmcompfunc = [];
    var arrmcompfunc_val = '';
    var arrmcompfuncdep = [];
    var arrmcompfuncdep_val = '';
    var arrmcompfuncdepsec = [];
    var arrmcompfuncdepsec_val = '';

    <?php if(!$_GET['id'] && !$_GET['cateid']){ ?>

    arrmcompfunc_val       = '<?php echo $staffcompfunc; ?>';
    arrmcompfuncdep_val    = '<?php echo $staffcompfuncdep; ?>';
    arrmcompfuncdepsec_val = '<?php echo $staffcompfuncdepsec; ?>';

    <?php }else if($_GET['id'] && !$_GET['cateid']){ ?>

    arrmcompfunc_val = '<?php echo $questioninfo[0]['question_compfunc']; ?>';
    arrmcompfuncdep_val = '<?php echo $questioninfo[0]['question_compfuncdep']; ?>';
    arrmcompfuncdepsec_val = '<?php echo $questioninfo[0]['question_compfuncdepsec']; ?>';

    <?php } ?>

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
            let optiontactivities     = $("#activities").val();
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
            data['optiontactivities'] = $("#activities").val();

            if(optiontquestion == ""){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุคำถาม ด้วยค่ะ");
                return false;
            }

            if(optiontactivities == "0"){
                fireSwalOnErrorCustom("สร้างคำถามไม่สำเร็จ","กรุณาระบุลักษณะของการตรวจ ด้วยค่ะ");
                return false;
            }

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
            var actionUrl = "../phpfunc/curd.php";
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

    function changestaffcompfunc(){
        let staffcompfunc = $("#staffcompfunc").val();
        let html = ``; 
        console.log(arrmcompfuncdep);
        html += `<select class="form-select" name="staffcompfuncdep" id="staffcompfuncdep" onchange="changestaffcompfuncdep()">
                        <option value="0">เลือกแผนก</option>`;
       
        arrmcompfuncdep.forEach(element => {
            if(staffcompfunc > 0){
                if(element.m_compfuncdep_compfunc == staffcompfunc){
                    html += `<option value="` + element.m_compfuncdep + `">` + element.m_compfuncdep_name_th + `</option>`;
                }
            }/*else{
                html += `<option value="` + element.m_compfuncdep + `">` + element.m_compfuncdep_name_th + `</option>`;
            }*/
        });
        html += `</select>`;

        $("#staffcompfuncdep").html(html);

    }    
    
    function changestaffcompfuncdep(){
        let compfunc = $("#staffcompfunc").val();
        let compfuncdep = $("#staffcompfuncdep").val();
        let html_mquestiontype = '<select class="form-select" name="mquestiontype" id="mquestiontype" onchange="changemquestiontype()" required>';
        html_mquestiontype += '<option value="0">เลือกหมวด</option>';
        arr_mquestiontype.forEach(element => {
            if(compfunc == element.questioncategories_compfunc && compfuncdep == element.questioncategories_compfuncdep && element.questioncategories_active == 1 || element.questioncategories_default == 1 ){
                html_mquestiontype += '<option value="'+element.questioncategories+'" > '+element.questioncategories_name+' </option>';
            }
        });
        html_mquestiontype += '</select>';

        $("#mquestiontype").html(html_mquestiontype);
        
    }   

    function changemquestiontype(){

        var form = $("#frm_submit_mtype");
        var actionUrl = "../phpfunc/curd.php";
        
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
            var actionUrl = "../phpfunc/curd.php";
            
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
            var actionUrl = "../phpfunc/curd.php";
            
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
        var actionUrl = "../phpfunc/curd.php";
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
        var actionUrl = "../phpfunc/curd.php";
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
                if (arrmcompfunc_val == element.compfunc_id) {
                    selectted = "selected";
                }
                html += `<option value="` + element.compfunc_id + `" `+selectted+`>` + element.compfunc_name + `</option>`;
            }
        });

        $("#staffcompfunc").html(html);

        if(arrmcompfunc_val){
            getDepartment();
        }

    }

    function getDepartment() {
        let compfunc = $("#staffcompfunc").val();
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

                        changestaffcompfunc();
                        getMquestiontype();
                    }

                },
                error: function(xhr, status, error) {
                    console.log("An error occurred: ", status, error);
                },
            });
        }
    }

    function getMquestiontype(){

        if($("#staffcompfunc").val() > 0){

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
                    $("#mquestiontypeselect").html(obj.data.html);

                }
            });

        }
    }

    function changestaffcompfunc() {
        let html = ``;
        let staffcompfunc = $("#staffcompfunc").val();
        html += `<option value="0">เลือกฝ่าย</option>`;
        arrmcompfuncdep.forEach(element => {
            let selectted = "";
            if (arrmcompfuncdep_val == element.department_id) {
                selectted = "selected";
            }
            html += `<option value="` + element.department_id + `" `+selectted+`>` + element.department_name + `</option>`;

        });

        $("#staffcompfuncdep").html("");
        $("#staffcompfuncdep").html(html);
        
        if(arrmcompfuncdep_val){
            getSection();
        }

    }

    function getSection(id="") {
        
        let compfuncdep = $("#staffcompfuncdep").val();
        
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
                        getMquestiontype();
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
        html +=     `<option value="0">เลือกแผนก</option>`;

        arrmcompfuncdepsec.forEach(element => {
            let selectted = "";
            if (arrmcompfuncdepsec_val == element.section_id) {
                selectted = "selected";
            }
            html += `<option value="` + element.section_id + `" ` + selectted + `>` + element.section_name + `</option>`;
        });

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
