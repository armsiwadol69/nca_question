<?php
include_once 'v_head.php';
include_once 'v_sidebar_start.php';

$go_ncadb = new ncadb();
// // die("----");
// $sql = "SELECT * FROM tb_questiondt WHERE questiondt_question = 1 ";

// $getQuestion = $go_ncadb->ncaretrieve($sql, "question");
// $questionData = ArrayConverter($getQuestion);

// echo "<PRE>";
// print_r($questionData);
$arrInput = array (
    "radio"    => "generateRadio",
    "text"     => "generatetext",
    "number"   => "generatenumber",
    "date"     => "generatedate",
    "checkbox" => "generatecheckbox",
);

// $go_ncadb = new ncadb();
// die("----");
$sqlOptionType = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";

$arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");

// $arrOptionType = ArrayConverter($arr_OptionType);

// echo "<PRE>";
// print_r($arr_OptionType);

?>
<!-- <main class="main-content" style="height: 94dvh;overflow-x:hidden;overflow-y:auto;"> -->
    <div class="row">

        <div class="col-lg-12">
            <div class="w-100 d-flex mt-2">
                <h4 class="me-auto mt-1">เพิ่มข้อมูลชุดคำถาม</h4>
            </div>
            <hr>
        </div>

        <div class="col-lg-12">

            <div class="row">
                <!-- <form class="needs-validation" action="../phpfunc/curd.php?method=addNewReward" enctype="multipart/form-data" method="POST" id="frm" novalidate> -->
                <form class="needs-validation"  enctype="multipart/form-data" method="POST" id="frm" >
                    
                    <div class="row gy-3">
                            
                        <div class="col-lg-12 col-md-12">
                            <div class="col-lg-12 col-md-12">
                                <label for="par_name" class="form-label">ชื่อของชุดคำถาม<span class="text-danger">*</span></label>
                                <input type="text" id="par_name" name="par_name" class="form-control" required>
                            </div>
                            <div class="col-lg-12">
                                <label for="par_conditionen" class="form-label">รายละเอียด<span class="text-danger">*</span></label>
                                <textarea class="form-control" id="par_conditionen" name="par_conditionen" rows="5" required></textarea>
                            </div>
                        </div>

                        <div class="col-lg-12">
                            <div class="w-100 d-flex mt-2">
                                <h4 class="me-auto mt-1">ส่วนคำถาม</h4>
                            </div>
                        </div>

                        <hr>

                        <div class="col-lg-12">

                            <div class="row">

                                <div class="col-lg-12 col-md-12">
                                    <!-- Start question -->
                                    <div class="col-lg-12 center">
                                        <span  class="btn btn-primary mb-3" onclick="setQuestionmodal('question','s'); $(this).hide(); $('#addQue').show()">เริ่มสร้างคำถาม</span>
                                    </div>

                                    <div id="nestedQuestion" class="list-group col">
                                        
                                    </div>
                                    <div class="col-lg-12 center">
                                        <span  class="btn btn-primary mt-3" id="addQue" style="display: none;" onclick="setQuestionmodal('question','p');">เพิ่ม</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal generate input box -->

    <div class="modal fade" id="generateinputbox" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">สร้างคำตอบ</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- <form action="javascript:void(0);" id="frm_category"> -->
                        <div class="row gy-3">
                            
                            <div class="col-12">
                                <label class="form-label" for="par_icon">ประเถทคำตอบ<span class="text-danger">*</span></label>
                                <select class="form-select" name="optiontype" id="optiontype" aria-label="isshowing">
                                    <option value="0">เลือกประเถทคำตอบ</option>
                                    <?php    
                                        foreach ($arr_OptionType as $key => $value) {
                                            echo '<option value="'.$value['questiontype'].'">'.$value['questiontype_name'].'</option>';
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="option">จำนวน<span class="text-danger">*</span></label>
                                <input class="form-control" type="number" name="optiontnumber" id="optiontnumber" required>
                                <input type="hidden" name="qinpname" id="qinpname">
                                <input type="hidden" name="qinpclass" id="qinpclass">
                                <input type="hidden" name="qmode" id="qmode">
                                <input type="hidden" name="qtype" id="qtype">
                            </div>
        
                            <div class="col-12">
                                <button id="createOption" class="btn btn-primary w-100" >สร้าง</button>
                            </div>
                        </div>
                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>
<!-- </main> -->
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
    // console.log("arr_OptionType",arr_OptionType);

    $(function() {
        // $('#par_pickupcycel').multiDatesPicker({
        //     dateFormat: 'dd/mm/yy'
        // }); 
        
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

        // intiCreatePanle();
        // initSortable();

        /* $('#frm_category').submit(function(e){
            e.preventDefault();
            // var title = this.title; //get the input named "title" of the form
            // alert(title.value); //alerts the value of that input
            console.log("data",$(this).par_categoryName);
            console.log("data",$(this));
        }); */

        $("#createOption").click(function(){

            let optiontype = $("#optiontype").val();
            let optiontnumber = $("#optiontnumber").val();
            let optiontqname = $("#qinpname").val();
            let optiontqclass = $("#qinpclass").val();
            let qmode = $("#qmode").val();

            // alert(optiontype + ' --- ' +optiontnumber);
            // console.log("generate",generateInput(optiontype,optiontnumber,optiontqclass));

            let html = '';
            if(qmode != 'question'){
                html = generateInput(optiontype,optiontnumber,optiontqclass);
            }else{
                html = createGroupquestion(optiontype,optiontnumber,optiontqclass);
            }
            // let contenthtml = $(".questionP1").html() ;
            if(qmode == 's'){
                $("#nestedQuestion").html(html);
            }else{
                $("#nestedQuestion").append(html);
            }
            console.log(html);
            $('#generateinputbox').modal('hide'); 
            initSortable();
        })
    
    });

    (function() {
        'use strict'
        // Fetch all the forms we want to apply custom Bootstrap validation styles to
        var forms = document.querySelectorAll('.needs-validation')

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms)
            .forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    } else {
                        fireSwalOnSubmit();
                    }

                    form.classList.add('was-validated')
                }, false)
            })
    })()

    // function generateRadio( name, checked , classname) {
    //     var radioInput;
    //     try {
    //         var radioHtml = '<input type="radio" name="';
    //         if ( checked ) {
    //             radioHtml += ' checked="checked"';
    //         }
    //         if(classname){
    //             radioHtml += ' class="'+classname+'"';
    //         }

    //         radioHtml += '/>';
    //         //radioInput = document.createElement(radioHtml);
    //         return radioHtml;
    //     } catch( err ) {
    //         /* radioInput = document.createElement('input');
    //         radioInput.setAttribute('type', 'radio');
    //         radioInput.setAttribute('name', name);
    //         if ( checked ) {
    //             radioInput.setAttribute('checked', 'checked');
    //         } */
    //         console.log("Error",err);
    //     }

    //     return radioInput;
    // }

    /* let html = `<div class="list-group-item nested-sortable setName" data-id="set2">
        <input type="radio" id="javascript" name="answerp1" value="2">
        ชุดคำถามที่ 2
        <div class="list-group-item nested-sortable question" data-id="s2q1">
            รถยนต์คันหนึ่งวิ่งด้วยอัตราเร็วเฉลี่ย 80 กิโลเมตรต่อชั่วโมง จากเมือง A ไปยังเมือง B ที่อยู่ห่างกัน 200 กิโลเมตร ถ้าออกเดินทางเวลา 06.00 น. จะถึงปลายทางเวลาเท่าใด
            <div class="list-group nested-sortable">
                <div class="list-group-item nested-4 answer border-none" data-id="s2q1a1">
                    <input type="radio" id="javascript" name="fav_language" value="07.30 น.">    
                    07.30 น.
                </div>
                <div class="list-group-item nested-4 answer border-none" data-id="s2q1a2">
                    <input type="radio" id="javascript" name="fav_language" value=" 08.00 น.">
                    08.00 น.
                </div>
                <div class="list-group-item nested-4 answer border-none" data-id="s2q1a3">
                    <input type="radio" id="javascript" name="fav_language" value=" 08.30 น.">
                    08.30 น.
                </div>
                <div class="list-group-item nested-4 answer border-none" data-id="s2q1a4">
                    <input type="radio" id="javascript" name="fav_language" value=" 09.00 น.">
                    09.00 น.
                </div>
            </div>
        </div>
    </div>`; */

    function createGroupquestion(qclass, qname){
        let html = "";
        let optiontype = $("#optiontype").val();
        let optiontnumber = $("#optiontnumber").val();
        let optiontqname = $("#qinpname").val();
        let optiontqclass = $("#qinpclass").val();


        // function setQuestionmodal(qclass, qname){


        // alert(optiontype + ' --- ' +optiontnumber);
        // console.log("generate",generateInput(optiontype,optiontnumber,optiontqclass));
        
        html += generateMainParentStart();
        html += generateInput(optiontype,optiontnumber,optiontqclass);
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

    function generateInput( type, number, classname, gtype) {

        let inputType = parseInt(parseInt(type) - 1);
        let html = '';
        name = makeid(5);

        // html += generateMainParentStart(name);
        // for (let index = 0; index < number; index++) {
                

        //     html += '<div class="list-group-item nested-2 answer border-none" data-id="s1q2a1">';

        //     html += '<input type="'+arr_OptionType[(type-1)]['questiontype_type']+'" name="'+name+'" id"'+name+index+'" ';

        //     /* if ( checked ) {

        //         html += ' checked="checked"';

        //     } */

        //     /* if(classname){
        //         html += ' class="'+classname+'"';
        //     } */

        //     if(data){
        //         html += ' data-id="'+data+'"';
        //     }

        //     if(value){
        //         html += ' valuee="'+value+'"';
        //     }

        //     html += '/>';

        //     if(text){
        //         html += ' '+text;
                
        //     }

        //     html += '</div>';
                
        // }
        
        // html += generateMainParentEnd();
        // console.log(html);
        // html = "";
        // console.log("number", number)
        
        let inptype = arr_OptionType[inputType]["questiontype_type"];
        console.log( "-->", inptype);

        html += `<div class="list-group-item nested-3 question" data-id="`+name+`">
                    <div class="col-lg-12">
                        คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questionname" required="">
                    </div>
                    <div class="list-group nested-sortable">`;
                    
                for (let index = 0; index < number; index++) {

                    html += `<div class="list-group-item nested-2 answer border-none" data-id="`+name+index+`" style="">`;
                    let formctr = '';
                    if(inputType  < 2 ){
                        formctr = `form-control`;
                    }

                    let inp = `<input type="hidden" name="`+name+index+`" value="`+inptype+`" >`;
                    inp += `<input type="`+inptype+`" name="`+name+`" class="`+formctr+`" >`;

                    if(inputType > 1){
                        inp += ` <input class="form-control-50 col-lg-5" type="text" name="option`+name+`[]" id="option`+name+index+`" required="">`;
                    }

                    inp += `<div class="list-group-item nested-3 question`+name+`" data-id="li8La"></div>`;
                    inp += `<button  class="btn btn-primary" id="addQue" style="" onclick="setQuestionmodal('question','af','question`+name+`');">เพิ่ม</button>`;
                    html += inp;

                        /* html += `<div class="list-group-item nested-sortable setName" data-id="set2">
                            
                            `+input+` 

                            <!-- <div class="list-group-item nested-sortable question" data-id="s2q1" draggable="false" style="">
                                รถยนต์คันหนึ่งวิ่งด้วยอัตราเร็วเฉลี่ย 80 กิโลเมตรต่อชั่วโมง จากเมือง A ไปยังเมือง B ที่อยู่ห่างกัน 200 กิโลเมตร ถ้าออกเดินทางเวลา 06.00 น. จะถึงปลายทางเวลาเท่าใด
                                <div class="list-group nested-sortable">
                                    
                                    <div class="list-group-item nested-4 answer border-none" data-id="s2q1a1" draggable="false" style="">
                                        <input type="radio" id="javascript" name="fav_language" value="07.30 น.">    
                                        07.30 น.
                                    </div><div class="list-group-item nested-4 answer border-none" data-id="s2q1a2" draggable="false" style="">
                                        <input type="radio" id="javascript" name="fav_language" value=" 08.00 น.">
                                        08.00 น.
                                    </div><div class="list-group-item nested-4 answer border-none" data-id="s2q1a3" draggable="false" style="">
                                        <input type="radio" id="javascript" name="fav_language" value=" 08.30 น.">
                                        08.30 น.
                                    </div><div class="list-group-item nested-4 answer border-none" data-id="s2q1a4" draggable="false" style="">
                                        <input type="radio" id="javascript" name="fav_language" value=" 09.00 น.">
                                        09.00 น.
                                    </div>
                                    <div class="list-group-item nested-4 answer border-none" id="testinput" data-id="s2q1a4">
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-lg-12 right">
                                <!-- <button class="btn btn-primary mt-3" onclick="setQuestionmodal('questionP1', 'questionP1');">สร้างคำตอบ</button> -->
                                <button class="btn btn-primary mt-3" onclick="setQuestionmodal('questionP1', 'questionP1');">สร้างคำถาม</button>
                            </div>
                        </div>`; */
                            
                    html += `</div>`;
                }

        html += `   </div>
                </div>`;

        // let contenthtml = $(".questionP1").html() ;
        // // $(".questionP1").html(contenthtml+html);
        // $(".questionP1").html(contenthtml+html);
        // initSortable();
        return html;

    }

    function generateQuestionHead(){
        let html = `<div class="list-group-item" id="questionP1">
                        <div class="col-12 mb-2">
                            <label class="form-label" for="par_categoryName">
                                คำถาม<span class="text-danger">*</span>
                            </label>
                            <input class="form-control" type="text" name="par_categoryName" required="">

                            <div class="questionP1 mt-3" id="">

                            </div>
                        </div>
                        <div class="col-lg-12 right">
                            <button class="btn btn-primary mt-3" onclick="setQuestionmodal('questionP1', 'questionP1');">สร้างคำตอบ</button>
                        </div>
                    </div>`;
        return html;
    }

    function generateQuestionFooter(){
        let html = `<div class="list-group-item" id="questionP1">
                        <div class="col-12 mb-2">
                            <label class="form-label" for="par_categoryName">คำถาม<span class="text-danger">*</span></label>
                            <input class="form-control" type="text" name="par_categoryName" required="">

                            <div class="questionP1 mt-3" id="">

                            </div>
                        </div>
                        <div class="col-lg-12 right">
                            <button class="btn btn-primary mt-3" onclick="setQuestionmodal('questionP1', 'questionP1');">สร้างคำตอบ</button>
                        </div>
                    </div>`;
        return html;
    }

    function setQuestionmodal(qmode,qtype,qname,qclass){
        $('#qinpname').val(qname);
        $('#qinpclass').val(qclass);
        $('#qmode').val(qmode);
        $('#qtype').val(qtype);
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

    // let input = generateInput("radio","aaa","","aaaa","aaaa","aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa","ทดสอบ");
    // $("#testinput").html(input);
    // console.log("createRadioElement => ",input);

</script>