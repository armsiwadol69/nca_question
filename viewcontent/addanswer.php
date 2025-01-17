<?php
include_once 'v_head.php';
// include_once 'v_sidebar_start.php';
require_once ("../class/class.question.php");

$go_ncadb = new ncadb();

$ncaquestion = new question($_GET['id']);


if($_GET['id']){
    $questioninfo = array();
    $questioninfo = $ncaquestion->getDataQuestion();
    $arr_parent = array();
    $htmlQuestion = "";
    foreach($questioninfo AS $key => $val){

        if(!$val['questiondt_parent']){
            $htmlQuestion  .= $ncaquestion->generateIsParentQuestion("questiondt",$val['questiondt'],0,$arr_parent);
        }
        
    }

}

$arrInput = array (
    "radio"    => "generateRadio",
    "text"     => "generatetext",
    "number"   => "generatenumber",
    "date"     => "generatedate",
    "checkbox" => "generatecheckbox",
);

$sqlOptionType = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
$arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");
$arr_OptionType = $ncaquestion->ncaArrayConverter($arr_OptionType);

?>

<div class="container-fluid" style="padding-right: 14px; padding-left: 14px;">

    <form class="needs-validation" action="../phpfunc/curd.php?mode=addQuestion" enctype="multipart/form-data" method="POST" id="frm" novalidate>

        <div class="row">
                        
            <div class="gy-3">
                
                <div class="col-lg-12 col-md-12">

                    <div class="col-lg-12">

                        <div class="w-100 d-flex mt-2">

                            <h4 class="me-auto mt-1">เพิ่มข้อมูลชุดคำถาม</h4>

                        </div>
                        <hr>

                    </div>

                    <div class="col-lg-12 col-md-12">

                        <div class="col-lg-12 col-md-12">

                            <label for="par_qname" class="form-label">ชื่อของชุดคำถาม<span class="text-danger">*</span></label>
                            <input type="text" id="par_qname" name="par_qname" class="form-control" required value="<?php echo $questioninfo[0]['question_name']; ?>">

                        </div>

                        <div class="col-lg-12">

                            <label for="par_qdatail" class="form-label">รายละเอียด<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="par_qdatail" name="par_qdatail" rows="5" required><?php echo $questioninfo[0]['question_detail']; ?></textarea>

                        </div>

                    </div>

                </div>

                <!-- <div class="col-lg-12 col-md-12"> -->
                <div class="row">

                    <div class="col-lg-12">

                        <div class="w-100 d-flex mt-2">

                            <h4 class="me-auto mt-1">ส่วนคำถาม</h4>

                        </div>

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

            <div class="col-lg-6 col-md-12">
                <input type="hidden"  class="form-control" name="questioninfoid" id="questioninfoid" value="<?php echo $_GET['id']; ?>">
                <input type="hidden"  class="form-control" name="par_userId" id="par_userId" value="<?php echo $_SESSION['userData']['stf']; ?>">
                <input type="hidden"  class="form-control" name="par_usernm" id="par_usernm" value="<?php echo $_SESSION['userData']['userdspms']; ?>">
                <button type="submit" class="btn btn-primary w-100 mt-5"><i class="bi bi-save"></i> บันทึกข้อมูล </button>
            </div>

            <div class="col-lg-6 col-md-12">
                <a href="list_question.php" class="btn btn-secondary w-100 mt-5"><i class="bi bi-back"></i> ย้อนกลับ </a>
            </div>

        </div>
        
    </form>
</div>

<!-- </main> -->
<?php
// include_once 'v_sidebar_end.php';
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
            if($(this).val()  < 4){
                $("#optiontnumber").val("1")
                $("#optiontnumber").attr("readonly",true);

            }else{
                $("#optiontnumber").attr("readonly",false);
                $("#optiontnumber").val("");
            }
        });

        $("#createOption").click(function(){

            let optiontype          = $("#optiontype").val();
            let optiontquestion     = $("#optiontquestion").val();
            let optiontnumber       = $("#optiontnumber").val();
            let optiontqname        = $("#qinpname").val();
            let optiontqclass       = $("#qinpclass").val();
            let qmode               = $("#qmode").val();
            let qtype               = $("#qtype").val();
            let qafter              = $("#qafter").val();
            let qafteroption        = $("#qafteroption").val();
            let data = [];
            data['optiontype']      = $("#optiontype").val();
            data['optiontquestion'] = $("#optiontquestion").val();
            data['optiontnumber']   = $("#optiontnumber").val();
            data['optiontqname']    = $("#qinpname").val();
            data['optiontqclass']   = $("#qinpclass").val();
            data['qmode']           = $("#qmode").val();
            data['qtype']           = $("#qtype").val();
            data['qafter']          = $("#qafter").val();
            data['qafteroption']    = $("#qafteroption").val();

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

            // console.log(html);
            $('#generateinputbox').modal('hide'); 

            if(qtype == 'mainQuestionStart'){
                $('#mainQuestionStart').hide(); 
                $('#addQue').show();
            }

            initSortable();
            // clearModal();
        })
    
    });

    (function() {
        'use strict'
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')
        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                console.log("form",form);
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                        alert("กรุณาใส่ข้อมูลให้ครบ ด้วยค่ะ");
                    } else {
                        fireSwalOnSubmit();
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()

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

    function generateInput(data) 
    {
        // console.log("generateInput",data);
        let type    = data['optiontype'];
        let opques  = data['optiontquestion'];
        let number  = data['optiontnumber'];
        let opname  = data['optiontqname'];  
        let opclass = data['optiontqclass'];   
        let opqmode = data['qmode'];  
        let qtype = data['qtype'];  
        let qafter = data['qafter'];  
        let qafteroption = data['qafteroption'];  

        let inputType = parseInt(parseInt(type) - 1);
        let html = '';
        name = makeid(15);
        
        let inptype = arr_OptionType[inputType]["questiontype_type"];
        let inptypename = arr_OptionType[inputType]["questiontype_name"];
        // console.log( "-->", inptype);

        mainname = makeid(15);
        var bgColor = 'rgb(' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ')';
        html += `<div class="content`+name+`" id="`+mainname+`" >`;
        html += `   <input type="hidden" name="mainname[]" value="`+mainname+`" >`;
        html += `   <div class="list-group-item nested-3 question ms-2" id="`+name+`" data-id="`+name+`" style="background-color :`+bgColor+`">`;
        html += `      <span class="btn btn-primary " id="delQuestion" style="position: absolute;right: 8px;" onclick="if(confirm('ยืนยันลบคำถามชุดนี้?')) { $('#`+name+`').remove(); }">ลบ</span>`;
        // html += `   <input type="hidden" name="questionname[`+name+`]" value="`+name+`" >`;
        html += `       <input type="hidden" name="questionname[]" value="`+name+`" >`;
        html += `       <input type="hidden" name="datainputtype[]" value="`+type+`" >`;
        html += `       <input type="hidden" name="questionnameinput[`+name+`]" value="`+inptype+`" >`;
        html += `       <input type="hidden" name="questionismain[`+name+`]" value="`+(qafter ? "" : "1")+`" >`;
        html += `       <input type="hidden" name="questionismainname[`+name+`]" value="`+(qafter ? "" : name)+`" >`;
        html += `       <input type="hidden" name="questionnameinputafter[`+name+`]" value="`+qafter+`" >`;
        if(qafter){
            html += `       <input type="hidden" name="questionnameinputparent[`+qafter+`][]" value="`+name+`" >`;
        }
        html += `       <input type="hidden" name="questionnameinputafteroptoion[`+name+`]" value="`+qafteroption+`" >`;
        html += `           <div class="col-lg-12">`;
        html += `               คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext[`+name+`]" required="" value="`+opques+`">`;
        html += `           </div>`;
        html += `           <div class="list-group nested-sortable">`;
                
        $order = 1
        for (let index = 0; index < number; index++) {

            html += `<div class="list-group-item nested-2 answer border-none ms-5" data-id="`+name+index+`" style="">`;
            let formctr = '';
            $readonly = "";
            if(inputType  < 2 ){
                formctr = `form-control-50 col-lg-6`;
            }

            let inp = `<input type="hidden" name="`+name+index+`" value="`+inptype+`" >`;
            inp += inptypename+` `+$order+` : <input class="form-control-40 col-lg-4" type="text" name="option`+name+`[]" id="option`+name+index+`" `+(parseInt(inputType) < 3 ? `readonly="readonly" ` : ` required `)+`>`;
            if(parseInt(inputType) > 2){
                inp += ` คะเเนน : <input class="form-control-custom col-lg-1" type="number" name="optionvalue`+name+`[]" id="optionvalue`+name+index+`" required="">`;
            }
            inp += `<div class="list-group-item nested-3 hide question`+name+index+` `+(opques ? 'ms-3 mt-3 mb-3 ' : '')+`" data-id="li8La">`;
            inp += `</div>`;
            inp += `<span class="btn btn-primary ms-3" id="addquestion`+name+index+`" style="" onclick="setQuestionmodal('question','af','question`+name+index+`','`+name+`' ,'option`+name+index+`');">สร้างคำถาม<!--หลังจากคำตอบนี้--></span>`;
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
            console.log("opname -->>>", opname);
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
        $("#optiontquestion").val('');
        $("#optiontnumber").val('');
        $("#qinpname").val('');
        $("#qinpclass").val('');
        $("#qmode").val('');
        $("#qtype").val('');
        $("#qafter").val('');
        $("#qafteroption").val('');
    }

    /* $(document).ready(function() {
        $('.question').each(function () {
            var hue = 'rgb(' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ',' + (Math.floor((256-199)*Math.random()) + 200) + ')';
            $(this).css("background-color", hue);
        });
    }); */

</script>
<style>
    .list-group-item {
        background-color: unset;
    }
    .list-group-item.nested-3 {
        background-color: #fff;
    }
</style>