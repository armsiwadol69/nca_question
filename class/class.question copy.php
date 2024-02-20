<?php

class question
{
    public $id;
    public $name;

    function __construct($id=0) {
        if($id){
            $this->setQuestionId($id);
        }
    }

    function setQuestionId($id = 0) {
        $this->id = $id;
    }

    function getDataQuestion()
    {
        global $go_ncadb;

        $sql = "SELECT 
                    * 
                FROM tb_question AS Q
                LEFT JOIN tb_questiondt AS QDT 
                    ON(QDT.questiondt_question=Q.question)
                WHERE 
                    Q.question = '".$this->id."'
                ORDER BY QDT.questiondt ASC 
                ";
        $arr = $go_ncadb->ncaretrieve($sql, "question");
        return $this->ncaArrayConverter($arr);
    }

    function getDataFromTable($field="*",$tableName,$wherefield="",$data="",$orderfield="",$order="ASC")
    {
        global $go_ncadb;
        
        if(!trim($tableName)){
            return false;
        }
        $where = "";
        if(!trim($wherefield)){
            $where = $wherefield." = '".$data."'";
        }
        $worder = "";
        if(trim($orderfield)){
            $worder = " ORDER BY ".$orderfield." ".$order;
        }
        
        $sql = "SELECT ".$field." FROM ".$tableName." ".$where.$worder;
        $arr = $go_ncadb->ncaretrieve($sql, "question");
        return $arr;
    }


    function generateArrayQuestion($maindata,$alldata)
    {
        foreach ($alldata as $key => $value) {
            if($value){
                $data['mainkey'] = $key;
                $data['mainparent'] = $_POST['questionnameinputparent'][$key];
                $option = "option".$key;
                $data['mainoptiontype'] = $_POST['questionnameinput'][$key];
                $data['mainoption'] = $_POST[$option];
                array_push($mainQuestion,$data);
            }
        }
    
    }

    function addNewQuestion($info,$maindata,$alldata)
    {
        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');

        // echo "addNewQuestion---------Start Prepare data--------<br>";
        // echo "---------info--------<br>";
        // print_r($info);
        // echo "---------maindata--------<br>";
        // print_r($maindata);
        // echo "---------alldata--------<br>";
        // print_r($alldata);
        // die("//////////////////");

        $sqlInsertQuestion = new SqlBuilder();
        $sqlInsertQuestion->SetTableName("tb_question");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;
        $sqlObj[$ii++] = new TField("question_name", iconv('utf-8', 'tis-620', $info['par_qname']), "string");
        $sqlObj[$ii++] = new TField("question_detail", iconv('utf-8', 'tis-620', $info['par_qdatail']), "string");
        $sqlObj[$ii++] = new TField("question_active", '1', "string");
        $sqlObj[$ii++] = new TField("question_recspid", $info['par_userid'], "string");
        $sqlObj[$ii++] = new TField("question_recdatetime", $datetime, "string");
        
        $sqlInsertQuestion->SetField($sqlObj);
        $queryQuestion = $sqlInsertQuestion->InsertSql();

        
        
        // echo  "queryQuestioninfo => ".$queryQuestion."<br><br>";

        // die("query => ".$queryQuestion);
         if ($go_ncadb->ncaexec($queryQuestion, "question")) {
            $questionId = $go_ncadb->ncaGetInsId("question");

            // die("questionId".$questionId);
            // $firstStep = true;
        } else {
            $go_ncadb->ncarollback("question");
            echo '<script>sessionStorage.setItem("curdStatus",0);window.location.href = "../view/list_question.php"</script>';
            exit(0);
        }

        $array_insert  = array();
        $index = 1;
        foreach ($alldata as $key => $value) {

            $type = $this->getDataFromTable("questiontype","tb_questiontype","questiontype_name",$value['mainoptiontype']);
            $sqlInsertQuestiondt = new SqlBuilder();
            $sqlInsertQuestiondt->SetTableName("tb_questiondt");
            $ii = 0;
            $sqlObjdt = null;
            $sqlObjdt[$ii++] = new TField("questiondt_question", $questionId, "string");
            $sqlObjdt[$ii++] = new TField("questiondt_title", iconv('utf-8', 'tis-620', $value['maintext']), "string");
            $sqlObjdt[$ii++] = new TField("questiondt_content", iconv('utf-8', 'tis-620', $value['datatext']), "string");
            $sqlObjdt[$ii++] = new TField("questiondt_require", 1, "string");
            $sqlObjdt[$ii++] = new TField("questiondt_parent",  $array_insert['datadt'][$value['mainkey']], "string");
            $sqlObjdt[$ii++] = new TField("questiondt_after", ($value['datamain'] ? "" : (substr($value['dataafteroption'],11) + 1) ), "string");
            $sqlObjdt[$ii++] = new TField("questiondt_order", $index, "string");
            $sqlObjdt[$ii++] = new TField("questiondt_questiontype", $type[0]['questiontype'], "string");
            $sqlObjdt[$ii++] = new TField("questiondt_active", 1, "string");
            $sqlObjdt[$ii++] = new TField("questiondt_recspid", $info['par_userid'], "string");
            $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");
                        
            $sqlInsertQuestiondt->SetField($sqlObjdt);
            $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();
            

            // die($queryQuestiondt);
            // echo "queryQuestiondt => ".$queryQuestiondt."<br>";
            if ($go_ncadb->ncaexec($queryQuestiondt, "question")) {
                $questionIddt = $go_ncadb->ncaGetInsId("question");
                $array_insert['datadt'][$key] = $questionIddt;
                $firstStep = true;
            } else {
                $go_ncadb->ncarollback("question");
                echo '<script>sessionStorage.setItem("curdStatus",0);window.location.href = "../view/list_question.php"</script>';
                exit(0);
            }


            foreach ($value['dataoption'] as $key2 => $value2) {
                # code...2
                $order = ($key2+1);
                $sqlInsertQuestionoption = new SqlBuilder();
                $sqlInsertQuestionoption->SetTableName("tb_questionoption");
                $ii = 0;

                $sqlObjoption = null;
                $sqlObjoption[$ii++] = new TField("questionoption_question", $questionId, "string");
                $sqlObjoption[$ii++] = new TField("questionoption_questiondt", $questionIddt, "string");
                $sqlObjoption[$ii++] = new TField("questionoption_name", iconv('utf-8', 'tis-620', $value2), "string");
                $sqlObjoption[$ii++] = new TField("questionoption_order", $order, "string");
                $sqlObjoption[$ii++] = new TField("questionoption_active", 1, "string");
                $sqlObjoption[$ii++] = new TField("questionoption_recspid", $info['par_userid'], "string");
                $sqlObjoption[$ii++] = new TField("questionoption_recdatetime", $datetime, "string");
                            
                $sqlInsertQuestionoption->SetField($sqlObjoption);
                $queryQuestionoption = $sqlInsertQuestionoption->InsertSql();

                // echo "queryQuestionoption => ".$queryQuestionoption."<br>";
                if ($go_ncadb->ncaexec($queryQuestionoption, "question")) {
                    // $insertIdReward = $go_ncadb->ncaGetInsId("otsdev");
                    $questionIdoption = $go_ncadb->ncaGetInsId("question");
                    $firstStep = true;
                } else {
                    $go_ncadb->ncarollback("question");
                    echo '<script>sessionStorage.setItem("curdStatus",0);window.location.href = "../view/list_question.php"</script>';
                    exit(0);
                }

            }

            // echo "<br>";
            // echo "<br>";

            $index++;
        }

        // print_r($array_insert);
        // echo "---------End Prepare data--------addNewQuestion<br>";

        // echo '<script>sessionStorage.setItem("curdStatus",1);window.location.href = "../view/view/list_question.php";</script>';
        /* $text = $this->converterUTF8("บันทึกสำเร็จ");
        echo $text;
        die(); */
        echo '<script charset="UTF-8" language="javascript">alert("บันทึกสำเร็จ"); window.location.href = "../view/list_question.php";</script>';

    }

    function deleteQuestion($id)
    {
        global $go_ncadb;
        
        $sqlBuilder = new SqlBuilder();
        $sqlBuilder->SetTableName("tb_questionoption");
        $sqlBuilder->SetWhereClause("questionoption_question = $id");
        $query = $sqlBuilder->DeleteSql();

        if ($go_ncadb->ncaexec($query, "question")) {

            $sqlBuilder = new SqlBuilder();
            $sqlBuilder->SetTableName("tb_questiondt");
            $sqlBuilder->SetWhereClause("questiondt_question = $id");
            $query = $sqlBuilder->DeleteSql();
            if ($go_ncadb->ncaexec($query, "question")) {
                
                $sqlBuilder = new SqlBuilder();
                $sqlBuilder->SetTableName("tb_question");
                $sqlBuilder->SetWhereClause("question = $id");
                $query = $sqlBuilder->DeleteSql();
                if ($go_ncadb->ncaexec($query, "question")) {

                    echo '<script>alert("ลบสำเร็จ"); window.location.href = "../view/list_question.php"</script>';
                } else {
                    $go_ncadb->rollback();
                    echo '<script>window.location.href = "../view/list_question.php"</script>';
                }

            } else {
                $go_ncadb->rollback();
                echo '<script>window.location.href = "../view/list_question.php"</script>';
            }

        } else {
            $go_ncadb->rollback();
            echo '<script>window.location.href = "../view/list_question.php"</script>';
        }

    }


    public function ncaArrayConverter($par_array)
    {
        if (empty($par_array)) {
            return array();
        }
        $ar = array();
        foreach ($par_array as $key => $value) {
            $xx = array();
            foreach ($par_array[$key] as $k => $v) {
                if (is_int($k)) {
                    continue;
                }
                $xx[$k] = iconv('tis-620', 'utf-8', $v);
            }
            $ar[$key] = $xx;
        }
        return $ar;
    }

    function generateIsParentQuestion($field,$questiondt,$after=0)
    {
        if(!$questiondt){
            return ;
        } else {

            global $go_ncadb;
            $sql        = "SELECT * FROM tb_questiondt WHERE ".$field." = '".$questiondt."'";
            if($after > 0){
                $sql .= " AND questiondt_after = '".$after."'";
            }

            $dataOption = $go_ncadb->ncaretrieve($sql, "question");
            $data       = $this->ncaArrayConverter($dataOption);
            // $pid = $this->generateRandomString(5);
            if($data){
                $sqlOptionType = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
                $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");

                $html = "";
                echo "Count => ".count($data);
                // echo "<pre>";
                foreach ($data as $key => $value) {
                    // print_r($value);
                    $pid  = $this->generateRandomString(15);
                    $opid = $this->generateRandomString(15);
                    // $dataOption = $this->getDataOption($value['questiondt']);
                    // print_r($dataOption);

                    /* $html .= '<div class="contentquestion'.$value['questiondt'].'" id="'.$this->generateRandomString(5).'">
                            <input type="hidden" name="mainname[]" value="'.$this->generateRandomString(5).'" />
                                <div class="list-group-item nested-3 question ms-2" id="question'.$value['questiondt'].'" data-id="question'.$value['questiondt'].'" >
                                    <span class="btn btn-primary" id="delQuestion" style="position: absolute;right: 8px;" onclick="if(confirm(`ยืนยันลบคำถามชุดนี้?`)) { $(`#question'.$value['questiondt'].'`).remove(); }" >ลบ</span>
                                    <input type="hidden" name="questionname[]" value="question'.$value['questiondt'].'" />
                                    <input type="hidden" name="questionnameinput[question'.$value['questiondt'].']" value="radio" />
                                    <input type="hidden" name="questionismain[question'.$value['questiondt'].']" value="1" />
                                    <input type="hidden" name="questionismainname[question'.$value['questiondt'].']" value="question'.$value['questiondt'].'" />
                                    <input type="hidden" name="questionnameinputafter[question'.$value['questiondt'].']" value />
                                    <input type="hidden" name="questionnameinputafteroptoion[question'.$value['questiondt'].']" value />
                                    <div class="col-lg-12">
                                        คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext[question'.$value['questiondt'].']" required value="'.$value['questiondt_title'].'"/>
                                    </div>
                                    <div class="list-group nested-sortable">
                                        <div class="list-group-item nested-2 answer border-none ms-5" data-id="question'.$value['questiondt'].'0" style >
                                            <input type="hidden" name="question'.$value['questiondt'].'0" value="radio" />
                                            <input type="radio" name="question'.$value['questiondt'].'" class />
                                            <input class="form-control-50 col-lg-6" type="text" name="optionquestion'.$value['questiondt'].'[]" id="optionquestion'.$value['questiondt'].'0" required />
                                            <div class="list-group-item nested-3 hide questionquestion'.$value['questiondt'].'0 ms-3 mt-3 mb-3" data-id="li8La" ></div>
                                            <span class="btn btn-primary ms-3" id="addquestionquestion'.$value['questiondt'].'0" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questiondt'].'0`,`question'.$value['questiondt'].'` ,`optionquestion'.$value['questiondt'].'0`);" >สร้างคำถามหลังจากคำตอบนี้</span>
                                        </div>
                                        <div class="list-group-item nested-2 answer border-none ms-5" data-id="question'.$value['questiondt'].'1" style >
                                            <input type="hidden" name="question'.$value['questiondt'].'1" value="radio" />
                                            <input type="radio" name="question'.$value['questiondt'].'" class />
                                            <input class="form-control-50 col-lg-6" type="text" name="optionquestion'.$value['questiondt'].'[]" id="optionquestion'.$value['questiondt'].'1" required />
                                            <div class="list-group-item nested-3 hide questionquestion'.$value['questiondt'].'1 ms-3 mt-3 mb-3" data-id="li8La" ></div>
                                            <span class="btn btn-primary ms-3" id="addquestionquestion'.$value['questiondt'].'1" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questiondt'].'1`,`question'.$value['questiondt'].'` ,`optionquestion'.$value['questiondt'].'1`);" >สร้างคำถามหลังจากคำตอบนี้</span>
                                        </div>
                                    </div>
                                </div>
                            </div>'; */

                    /* $html.= '<div class="contentquestion'.$value['questiondt'].'" id="'.$pid.'">
                                <input type="hidden" name="mainname[]" value="'.$pid.'" />
                                <div class="list-group-item nested-3 question ms-2" id="question'.$value['questiondt'].'" data-id="question'.$value['questiondt'].'" >
                                    <span class="btn btn-primary" id="delQuestion" style="position: absolute;right: 8px;" onclick="if(confirm(`ยืนยันลบคำถามชุดนี้?`)) { $(`#question'.$value['questiondt'].'`).remove(); }" ><i class="bi bi-trash"></i></span>
                                    <input type="hidden" name="questionname[]" value="question'.$value['questiondt'].'" />
                                    <input type="hidden" name="questionnameinput[question'.$value['questiondt'].']" value="radio" />
                                    <input type="hidden" name="questionismain[question'.$value['questiondt'].']" value="1" />
                                    <input type="hidden" name="questionismainname[question'.$value['questiondt'].']" value="question'.$value['questiondt'].'" />
                                    <input type="hidden" name="questionnameinputafter[question'.$value['questiondt'].']" value />
                                    <input type="hidden" name="questionnameinputafteroptoion[question'.$value['questiondt'].']" value />
                                    <div class="col-lg-12">
                                        คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext[question'.$value['questiondt'].']" required value="'.$value['questiondt_title'].'"/>
                                    </div>
                                    <div class="list-group nested-sortable">
                                        '.$this->getDataOption($value['questiondt'],$pid,$value).'
                                    </div>
                                </div>
                            </div>';

                    if(count($data) == ($key +1)){
                        $html .= '<div class="col-lg-12"><span class="btn btn-primary mt-3" onclick="setQuestionmodal(`question`,`after`,`contentU1XpM`,`'.$pid.'`,`optioquestion'.$value['questiondt'].'` );"><i class="bi bi-file-plus"></i> เพิ่มคำถาม</span></div>';
                    } */

                 /*    $html .= '<div class="list-group-item nested-3 question ms-2" id="'.$value['questiondt'].'" data-id="'.$value['questiondt'].'">
                                <span class="btn btn-primary" id="delQuestion" style="position: absolute; right: 8px;" onclick="if(confirm(`ยืนยันลบคำถามชุดนี้?`)) { $(`#'.$value['questiondt'].'`).remove(); }">
                                    ลบ
                                </span> 
                                <input type="hidden" name="questionname[]" value="'.$value['questiondt'].'" />
                                <input type="hidden" name="questionnameinput['.$value['questiondt'].']" value="radio" /> 
                                <input type="hidden" name="questionismain['.$value['questiondt'].']" value="1" /> 
                                <input type="hidden" name="questionismainname['.$value['questiondt'].']" value="'.$value['questiondt'].'" />
                                <input type="hidden" name="questionnameinputafter['.$value['questiondt'].']" value="" /> 
                                <input type="hidden" name="questionnameinputafteroptoion['.$value['questiondt'].']" value="" />
                                <div class="col-lg-12">
                                    คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext['.$value['questiondt'].']" required="" value="asdasd" />
                                </div>
                                <div class="list-group nested-sortable">';
                    $html .= $this->getDataOption($value['questiondt'],$pid,$value);
                                    
                     $html .= ' </div>
                            </div>' ; */

                            /* <div class="col-lg-12">
                                                <span class="btn btn-primary mt-3" onclick="setQuestionmodal(`question`,`after`,`content'.$value['question'].$value['questiondt'].'`,`'.$value['questiondt'].'`,`option'.$value['questiondt'].'0` );"><i class="bi bi-file-plus"></i> เพิ่มคำถาม</span>
                                            </div> */


                    $html .= '  <div class="content'.$value['questiondt'].'" id="'.$pid.'">
                                    <input type="hidden" name="mainname[]" value="'.$pid.'" />
                                    <div class="list-group-item nested-3 question ms-2" id="'.$value['questiondt'].'" data-id="'.$value['questiondt'].'">
                                        <span class="btn btn-primary" id="delQuestion" style="position: absolute; right: 8px;" onclick="if(confirm(`ยืนยันลบคำถามชุดนี้?`)) { $(`#'.$value['questiondt'].'`).remove(); }">ลบ</span>
                                        <input type="hidden" name="questionname[]" value="'.$value['questiondt'].'" /> <input type="hidden" name="questionnameinput['.$value['questiondt'].']" value="radio" /> <input type="hidden" name="questionismain['.$value['questiondt'].']" value="1" />
                                        <input type="hidden" name="questionismainname['.$value['questiondt'].']" value="'.$value['questiondt'].'" /> <input type="hidden" name="questionnameinputafter['.$value['questiondt'].']" value="" />
                                        <input type="hidden" name="questionnameinputafteroptoion['.$value['questiondt'].']" value="" />
                                        <div class="col-lg-12">คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext['.$value['questiondt'].']" required="" value="aaa" /></div>
                                        <div class="list-group nested-sortable">
                                        '.$this->getDataOption($value['questiondt'],$pid,$value).'
                                        </div>
                                    </div>
                                </div>
                        ';

                        if(count($data) == ($key +1) && count($data) > 1){
                            $html .= '<div class="col-lg-12"><span class="btn btn-primary mt-3" onclick="setQuestionmodal(`question`,`after`,`content'.$value['questiondt'].'`,`'.$pid.'`,`optionquestion'.$questiondt.($after-1).'` );"><i class="bi bi-file-plus"></i> เพิ่มคำถาม</span></div>';
                        }

                    
                }
                
            }

            return $html;

        }

    }

    

    function getDataOption($question,$pid,$dataParent)
    {
        global $go_ncadb;

        $sql        = "SELECT * FROM tb_questionoption WHERE questionoption_questiondt = '".$question."'";
        $dataOption = $go_ncadb->ncaretrieve($sql, "question");
        $data       = $this->ncaArrayConverter($dataOption);
        $html = "";
        // echo "getDataOption"; print_r($dataParent);
        // echo $sql."<br>";

        // echo "<pre>";

        foreach ($data as $key => $value) {
            // print_r($value);
            $order = ($key + 1);

            $sql_parent= "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."'";
            $dp = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP       = $this->ncaArrayConverter($dp);
            // echo "sql_parent".$sql_parent;

            $html .= '<div class="list-group-item nested-2 answer border-none ms-5" data-id="question'.$value['questionoption_questiondt'].$key.'" style >
                        <input type="hidden" name="question'.$value['questionoption_questiondt'].$key.'" value="radio" />
                        <input type="radio" name="question'.$value['questionoption_questiondt'].'" class />
                        <input class="form-control-50 col-lg-6" type="text" name="optionquestion'.$value['questionoption_questiondt'].'[]" id="optionquestion'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_name'].'" required />
                        <div class="list-group-item nested-3 '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-3 mt-3 mb-3" data-id="'.$pid.'" >
                        '.$this->generateIsParentQuestion("questiondt_parent",$dataParent['questiondt'],$order).'
                        </div>'.(count($dataP) > 0 ? '' : '<span class="btn btn-primary ms-3" id="addquestionquestion'.$value['questionoption_questiondt'].$key.'" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questionoption_questiondt'].$key.'`,`question'.$value['questionoption_questiondt'].'` ,`optionquestion'.$value['questionoption_questiondt'].$key.'`);" >สร้างคำถามหลังจากคำตอบนี้</span>' ).'
                        
                    </div>';
        }
        /* foreach ($data as $key => $value) {
            // print_r($value);
            $order = ($key + 1);

            $sql_parent= "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."'";
            $dp = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP       = $this->ncaArrayConverter($dp);
            echo "<pre>";
            print_r($dataP);
            // echo "sql_parent".$sql_parent."<br>";

            $html .= '  <div class="list-group-item nested-2 answer border-none ms-5" data-id="'.$value['question'].$value['questiondt'].$key.'" style="">
                            <input type="hidden" name="'.$value['question'].$value['questiondt'].'" value="radio" />
                            <input type="radio" name="'.$value['question'].$value['questiondt'].'" class="" /> 
                            <input type="hidden" name="order'.$value['question'].$value['questiondt'].'" value="'.$key.'" class="" /> 
                            <input class="form-control-50 col-lg-6" type="text" name="option'.$value['question'].$value['questiondt'].'[]" id="option'.$value['question'].$value['questiondt'].$key.'" required="" />
                            '.$this->generateIsParentQuestion("questiondt_parent",$dataParent['questiondt'],$order).'
                            <div class="list-group-item nested-3 '.(count($dataP) > 0 ? "" : "hide" ).' question'.$value['question'].$value['questiondt'].$key.' ms-3 mt-3 mb-3" data-id="li8La"></div>'.(count($dataP) > 0 ? '' : '<span class="btn btn-primary ms-3" id="addquestion'.$value['question'].$value['questiondt'].$key.'" style="" onclick="setQuestionmodal(`question`,`af`,`question'.$value['question'].$value['questiondt'].$key.'`,`'.$value['question'].$value['questiondt'].'` ,`option'.$value['question'].$value['questiondt'].$key.'`);">สร้างคำถามหลังจากคำตอบนี้</span>') .'
                            
                        </div>';
            
        } */
        /* if(count($dataP) > 0 ){
             $html .= '<div class="col-lg-12"><span class="btn btn-primary mt-3" onclick="setQuestionmodal(`question`,`after`,`contentU1XpM`,`'.$pid.'`,`optioquestion'.$value['questiondt'].'` );"><i class="bi bi-file-plus"></i> เพิ่มคำถาม</span></div>';
        } */

        // $html .= '<div class="col-lg-12"><span class="btn btn-primary mt-3" onclick="setQuestionmodal(`question`,`after`,`contentU1XpM`,`SfmKt`,`optionSfmKt0` );">เพิ่มคำถาม</span></div>';

        return $html;
        // return ;
    }

    function generateParentQuestion($data,$parent)
    {

        global $go_ncadb;
        if(!is_array($data) || count($data) === 0){
            return;
        }

        $sqlOptionType = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
        $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");

        print_r($data);
        foreach ($data as $key => $value) {
           
        }

    }

    function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /* function getDataQuestiondt($question,$pid)
    {
        global $go_ncadb;

        $sql = "SELECT * FROM tb_questionoption WHERE questionoption_questiondt = '".$question."'";

        $dataOption = $go_ncadb->ncaretrieve($sql, "question");
        $data = $this->ncaArrayConverter($dataOption);

        $html = "";

        foreach ($data as $key => $value) {
            $html .= '<div class="list-group-item nested-2 answer border-none ms-5" data-id="question'.$value['questionoption_question'].$key.'" style >
                        <input type="hidden" name="question'.$value['questionoption_question'].$key.'" value="radio" />
                        <input type="radio" name="question'.$value['questionoption_question'].'" class />
                        <input class="form-control-50 col-lg-6" type="text" name="optionquestion'.$value['questionoption_question'].'[]" id="optionquestion'.$value['questionoption_question'].$key.'" value="'.$value['questionoption_name'].'" required />
                        <div class="list-group-item nested-3 hide questionquestion'.$value['questionoption_question'].$key.' ms-3 mt-3 mb-3" data-id="'.$pid.'" ></div>
                        <span class="btn btn-primary ms-3" id="addquestionquestion'.$value['questionoption_question'].$key.'" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questionoption_question'].$key.'`,`question'.$value['questionoption_question'].'` ,`optionquestion'.$value['questionoption_question'].$key.'`);" >สร้างคำถามหลังจากคำตอบนี้</span>
                    </div>';
        }

        return $html;
    } */

}
