<?php
include_once 'v_head.php';
include_once 'v_sidebar_start.php';

// $go_ncadb = new ncadb();
// // die("----");
// $sql = "SELECT * FROM tb_questiondt WHERE questiondt_question = 1 ";

// $getQuestion = $go_ncadb->ncaretrieve($sql, "question");
// $questionData = ArrayConverter($getQuestion);

// echo "<PRE>";
// print_r($questionData);
$arrInput = array(
    "radio"    => "generateRadio",
    "text"     => "generatetext",
    "number"   => "generatenumber",
    "date"     => "generatedate",
    "checkbox" => "generatecheckbox",
);

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
                <form class="needs-validation" action="../phpfunc/curd.php?method=addNewReward" enctype="multipart/form-data" method="POST" id="frm" novalidate>
                    <div class="row gy-3">
                        <div class="col-lg-12 col-md-12">
                            <label for="par_name" class="form-label">ชื่อของชุดคำถาม<span class="text-danger">*</span></label>
                            <input type="text" id="par_name" name="par_name" class="form-control" required>
                        </div>
                        <div class="col-lg-12">
                            <label for="par_conditionen" class="form-label">รายละเอียด<span class="text-danger">*</span></label>
                            <textarea class="form-control" id="par_conditionen" name="par_conditionen" rows="5" required></textarea>
                        </div>
                    <!-- </div>
                    
                    <div class="row gy-3 content-question"> -->

                        <div class="col-lg-12">
                            <div class="w-100 d-flex mt-2">
                                <h4 class="me-auto mt-1">คำถาม</h4>
                            </div>
                        </div>
                        <hr>
                        <div class="col-lg-12">

                            <div class="row">

                                <div class="col-lg-12 col-md-12">
                                    <!-- Start question -->
     
                                    <div id="nestedQuestion" class="list-group col">
                                        <div class="list-group-item">
                                            asdasdasdasdasdasdasdasksj;lmlkjm.ml;k'm'sak;m'jmksad
                                            <div class="list-group-item nested-sortable setName" data-id="set1">
                                                <input type="radio" id="javascript" name="answerp1" value="1">
                                                ชุดคำถามที่ 1
                                                <div class="p1question">
                                                    <div class="list-group-item nested-2 question" data-id="s1q1">คุณชอบถนนเส้นไหน
                                                        <div class="list-group nested-sortable">
                                                            <div class="list-group-item nested-1 answer border-none" data-id="s1q1a1">
                                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                                                รัชดา
                                                            </div>
                                                            <div class="list-group-item nested-1 answer border-none" data-id="s1q1a2">
                                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                                                ทองหล่อ
                                                            </div>
                                                            <div class="list-group-item nested-1 answer border-none" data-id="s1q1a3">
                                                                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">
                                                                พี่ตู๋ว่าไงผมก็ว่างั้น
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item nested-3 question" data-id="s1q2">คุณมีเมียกี่คน?
                                                        <div class="list-group nested-sortable">
                                                            <div class="list-group-item nested-2 answer border-none" data-id="s1q2a1">
                                                                <input type="radio" id="javascript" name="family" value="1">
                                                                1 คน
                                                            </div>
                                                            <div class="list-group-item nested-2 answer border-none" data-id="s1q2a1">
                                                                <input type="radio" id="javascript" name="family" value="2">
                                                                ระหว่าง 2 ถึง 9 คน
                                                            </div>
                                                            <div class="list-group-item nested-2 answer border-none" data-id="s1q2a3">
                                                                <input type="radio" id="javascript" name="family" value="3">
                                                                มากกว่า 9 คน
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item question" data-id="s1q3">ที่หน้าบ้าน ร้านอาหารร้านใดอร่อยที่สุด?
                                                        <div class="list-group nested-sortable">
                                                            <div class="list-group-item nested-3 answer border-none" data-id="s1q3a1">
                                                                <input type="text" id="par_name" name="par_name" class="form-control" required="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item question" data-id="s1q3">ซื้อรถให้เด็กกี่คัน
                                                        <div class="list-group nested-sortable">
                                                            <div class="list-group-item nested-3 answer border-none" data-id="s1q3a1">
                                                                <input type="number" id="par_name" name="par_name" class="form-control" required="">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="list-group-item question" data-id="s1q3">ไปหาเด็กล่าสุดวันไหน
                                                        <div class="list-group nested-sortable">
                                                            <div class="list-group-item nested-3 answer border-none" data-id="s1q3a1">
                                                            <!-- <input type="date" id="par_startdate" name="par_startdate" class="form-control" onchange="setMinDate();setGobalStartDate(this.value);" required> -->
                                                            <input type="text" id="par_startdate" name="par_startdate" class="form-control datepicker" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12 right">
                                                        <button id="get-nested-btn" class="btn btn-primary mt-3">Add Question</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-group-item nested-sortable setName" data-id="set2">
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
                                                        <div class="list-group-item nested-4 answer border-none" id="testinput" data-id="s2q1a4">
                                                        
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <hr>

                                    <button id="get-nested-btn" class="btn btn-primary w-100">I WILL HAVE ORDER!</button>

                                    <hr>

                                    <div class="col my-5">
                                        <h6>Nested-Sort</h6>
                                        <!-- <p id="Nested-Sort"></p> -->
                                        <pre id="Nested-Sort">

                                        </pre>

                                    </div>
                                    
                                    <hr>

                                    <div class="col my-5">
                                        <h4>Create Nested Sortable Element By JSON</h4>
                                        <div id="placeholder-sortable"></div>
                                        <button class="btn btn-info w-100" onclick="createSortableByJson();">CREATE</button>
                                        <p class="text-danger my-2">Please Gen JSON from above before click this button!</p>
                                    </div>

                                    <!-- End question -->

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Modal generate input box -->

    <div class="modal fade" id="generateinputbox" tabindex="-1" aria-labelledby="createBatch" aria-hidden="true" onsubmit="submitCategory();" ;>
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">เพิ่มหมวดหมู่ใหม่</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:void(0);" id="frm_category">
                        <div class="row gy-3">
                            <div class="col-12">
                                <label class="form-label" for="par_categoryName">ชื่อหมวดหมู่<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="par_categoryName" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="par_categoryName">ชื่อหมวดหมู่ (EN)<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="par_categoryNameEN" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="par_icon">ไอคอน<span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="par_icon" required>
                                <div id="par_icon_help" class="form-text">
                                    You can find icon from <a href="https://fontawesome.com/search?o=r&m=free" target="framename">HERE.</a> Set RGB(166,166,166)
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="par_icon">แสดงหมวดหมู่บนหน้าเว็บ NCA หรือไม่<span class="text-danger">*</span></label>
                                <select class="form-select" name="par_caregoryActive" aria-label="isshowing">
                                    <option value="0" selected>ซ่อน</option>
                                    <option value="1">แสดง</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <input type="hidden" class="form-control" name="method" value="newCategory">
                                <input type="hidden" class="form-control" name="par_userId" id="par_userId" value="<?php echo $_SESSION['userData']['stf']; ?>">
                                <input type="hidden" class="form-control" name="par_usernm" id="par_usernm" value="<?php echo $_SESSION['userData']['userdspms']; ?>">
                                <div class="my-2 text-danger" id="message2"></div>
                                <button type="submit" id="insertBatchBtn" class="btn btn-primary w-100">เพิ่ม</button>
                            </div>
                        </div>
                    </form>
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
        initSortable();
    
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

    function generateMainParent(){

        let html = `<div class="list-group-item nested-sortable setName" data-id="set2">`;
        return html;

    }

    function footerDiv(){

        let html = `</div>`;
        return html;

    }

    function generateInput( type, name, checked, classname, data, value, text ) {

        if(!type){
            return '';
        }

        let html = '<input type="'+type+'" name="'+name+'"';
        if ( checked ) {
            html += ' checked="checked"';
        }

        if(classname){
            html += ' class="'+classname+'"';
        }

        if(data){
            html += ' data-id="'+data+'"';
        }

        if(value){
            html += ' valuee="'+value+'"';
        }

        html += '/>';

        if(text){
            html += ' '+text;
        }

        return html;

    }

    let input = generateInput("radio","aaa","","aaaa","aaaa","aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa","ทดสอบ");
    $("#testinput").html(input);
    console.log("createRadioElement => ",input);
</script>