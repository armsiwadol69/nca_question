<?php

class question
{
    public $id;
    public $name;
    public $question = array();

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
        $arr = array();
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
        $arr = array(0);
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

        /* 
            echo "<pre>";
            echo "addNewQuestion---------Start Prepare data--------<br>";
            echo "---------info--------<br>";
            print_r($info);
            echo "---------maindata--------<br>";
            print_r($maindata);
            
            echo "<pre>";
            echo "---------alldata--------<br>";
            print_r($alldata);
            die("//////////////////"); 
        */

        $sqlInsertQuestion = new SqlBuilder();
        $sqlInsertQuestion->SetTableName("tb_question");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;
        $sqlObj[$ii++] = new TField("question_name", iconv('utf-8', 'tis-620', $info['par_qname']), "string");
        $sqlObj[$ii++] = new TField("question_detail", iconv('utf-8', 'tis-620', $info['par_qdatail']), "string");
        $sqlObj[$ii++] = new TField("question_active", '1', "string");
        
        
        if($info['par_questioninfoid']){

            $sqlObj[$ii++] = new TField("question_modispid", $info['par_userid'], "string");
            $sqlObj[$ii++] = new TField("question_modidatetime", $datetime, "string");

            $sqlInsertQuestion->SetField($sqlObj);
            $sqlInsertQuestion->SetWhereClause(" question = '".$info['par_questioninfoid']."'");
            $queryQuestion = $sqlInsertQuestion->UpdateSql();

        }else{

            $sqlObj[$ii++] = new TField("question_recspid", $info['par_userid'], "string");
            $sqlObj[$ii++] = new TField("question_recdatetime", $datetime, "string");

            $sqlInsertQuestion->SetField($sqlObj);
            $queryQuestion = $sqlInsertQuestion->InsertSql();

        }

        // die("query => ".$queryQuestion);
        if ($go_ncadb->ncaexec($queryQuestion, "question")) {
            if($info['par_questioninfoid']){
                $questionId = $info['par_questioninfoid'];
            }else{
                $questionId = $go_ncadb->ncaGetInsId("question");
            }

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

            // $type = $this->getDataFromTable("questiontype","tb_questiontype","questiontype_name",$value['mainoptiontype']);
            $sqlInsertQuestiondt = new SqlBuilder();
            $sqlInsertQuestiondt->SetTableName("tb_questiondt");
            $ii = 0;
            $sqlObjdt = null;
            $sqlObjdt[$ii++] = new TField("questiondt_question", $questionId, "string");
            $sqlObjdt[$ii++] = new TField("questiondt_title", iconv('utf-8', 'tis-620', $value['datatext']), "string");
            // $sqlObjdt[$ii++] = new TField("questiondt_content", iconv('utf-8', 'tis-620', $value['datatext']), "string");
            $sqlObjdt[$ii++] = new TField("questiondt_require", 1, "string");
            $sqlObjdt[$ii++] = new TField("questiondt_parent",  $array_insert['datadt'][$value['mainkey']], "string");
            $order = "";
            if($value['dataafteroption']){
                $textop = "option".$value['mainkey'];
                $order = str_replace($textop,"",$value['dataafteroption']);
            }
            $sqlObjdt[$ii++] = new TField("questiondt_after", ($value['datamain'] ? "" : ($order + 1) ), "string");
            $sqlObjdt[$ii++] = new TField("questiondt_order", $index, "string");
            $sqlObjdt[$ii++] = new TField("questiondt_questiontype", $value['datainputtype'], "string");
            $sqlObjdt[$ii++] = new TField("questiondt_active", 1, "string");
            

            if($value['questiondt']){

                $sqlObjdt[$ii++] = new TField("questiondt_recspid", $info['par_userid'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");

                $sqlInsertQuestiondt->SetField($sqlObjdt);
                $sqlInsertQuestiondt->SetWhereClause(" questiondt = '".$value['questiondt']."'");
                $queryQuestiondt = $sqlInsertQuestiondt->UpdateSql();

            }else{

                $sqlObjdt[$ii++] = new TField("questiondt_modispid", $info['par_userid'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");

                $sqlInsertQuestiondt->SetField($sqlObjdt);
                $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();

            }

            if ($go_ncadb->ncaexec($queryQuestiondt, "question")) {
                $questionIddt = $go_ncadb->ncaGetInsId("question");
                if($value['questiondt']){
                    $questionIddt = 
                    $array_insert['datadt'][$key] = $value['questiondt'];
                }else{
                    $array_insert['datadt'][$key] = $questionIddt;
                }
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
                $sqlObjoption[$ii++] = new TField("questionoption_value", $value['dataoptionvalue'][$key2]);
                $sqlObjoption[$ii++] = new TField("questionoption_images", ($value['optionimages'][$value['questionoption'][$key2]] ? "1" : "0"));
                $sqlObjoption[$ii++] = new TField("questionoption_order", $order, "string");
                $sqlObjoption[$ii++] = new TField("questionoption_active", 1, "string");

                if($value['questionoption'][$key2] > 0){

                    $sqlObjoption[$ii++] = new TField("questionoption_modispid", $info['par_userid'], "string");
                    $sqlObjoption[$ii++] = new TField("questionoption_modidatetime", $datetime, "string");

                    $sqlInsertQuestionoption->SetField($sqlObjoption);
                    $sqlInsertQuestionoption->SetWhereClause(" questionoption = '".$value['questionoption'][$key2]."'");
                    $queryQuestionoption = $sqlInsertQuestionoption->UpdateSql();

                }else{
                            
                    $sqlObjoption[$ii++] = new TField("questionoption_recspid", $info['par_userid'], "string");
                    $sqlObjoption[$ii++] = new TField("questionoption_recdatetime", $datetime, "string");

                    $sqlInsertQuestionoption->SetField($sqlObjoption);
                    $queryQuestionoption = $sqlInsertQuestionoption->InsertSql();
                }
                    
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

            $index++;

        }

        $result_diff = array_diff($info['oldquestion'], $info['questionid']);
        if($result_diff){
            foreach ($result_diff as $key3 => $value3) {
                # code...
            
                $sqlInsertQuestiondt = new SqlBuilder();
                $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                $ii = 0;
                $sqlObjdt = null;

                $sqlObjdt[$ii++] = new TField("questiondt_active", 0, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_recspid", $info['par_userid'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");

                $sqlInsertQuestiondt->SetField($sqlObjdt);
                $sqlInsertQuestiondt->SetWhereClause(" questiondt = '". $value3."'");
                $queryQuestiondtDel = $sqlInsertQuestiondt->UpdateSql();

                if (!$go_ncadb->ncaexec($queryQuestiondtDel, "question")) {
                    $go_ncadb->ncarollback("question");
                    echo '<script>sessionStorage.setItem("curdStatus",0);window.location.href = "../view/list_question.php"</script>';
                    exit(0);
                }
            }
        }

        // echo '<script charset="" language="javascript">alert("'.iconv( 'UTF-8', 'TIS-620', "บันทึกสำเร็จ").'"); window.location.href = "../view/list_question.php";</script>';
        echo '<script charset="" language="javascript">alert("'.iconv( 'UTF-8', 'TIS-620', "บันทึกสำเร็จ").'"); window.location.href = "../view/addquestion.php?id='.$questionId.'";</script>';
        // echo '<script charset="" language="javascript">alert("'.iconv( 'UTF-8', 'TIS-620', "บันทึกสำเร็จ").'"); </script>';

    }

    function deleteQuestion($id,$user)
    {
        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');
        
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

    function deleteQuestiondt($id)
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

    public function ncaConverterText($text)
    {
        if (empty($text)) {
            return ;
        }
        return iconv('tis-620', 'utf-8', $text);
    }

    function generateIsParentQuestion($field,$questiondt,$after=0,$parentdeata=array(),$questionArray=array())
    {
        if(!$questiondt){
            return ;
        } else {

            $html = "";
            global $go_ncadb;
            $sql        = "SELECT * FROM tb_questiondt WHERE ".$field." = '".$questiondt."' AND questiondt_active = '1' ";
            if($after > 0){
                $sql .= " AND questiondt_after = '".$after."'";
            }

            $dataOption = $go_ncadb->ncaretrieve($sql, "question");
            $data       = $this->ncaArrayConverter($dataOption);
            

            if($data){
                $sqlOptionType = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
                $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");

                $html = "";

                foreach ($data as $key => $value) {

            
                    array_push($questionArray,$value['questiondt']);

                    $pid  = $this->generateRandomString(15);
                    $opid = $this->generateRandomString(15);
                    $inputTypeName = $this->getInpustType("questiontype",$value['questiondt_questiontype']);
                    $html .= '  <div class="bgcontentcolor content'.$value['questiondt'].'" id="'.$pid.'">
                                    <input type="hidden" name="mainname[]" value="'.$pid.'" />
                                    <input type="hidden" name="oldquestion[]" value="'.$value['questiondt'].'" />
                                    <div class="list-group-item nested-3 question" id="'.$value['questiondt'].'" data-id="'.$value['questiondt'].'">
                                        <span class="btn btn-primary" id="delQuestion" style="position: absolute; right: 8px;" onclick="if(confirm(`ยืนยันลบคำถามชุดนี้?`)) { deleteQuestion('.$value['questiondt'].'); $(`#'.$value['questiondt'].'`).remove(); }">
                                            ลบ
                                        </span>
                                        <input type="hidden" name="questiondt['.$value['questiondt'].']" value="'.$value['questiondt'].'" />
                                        <input type="hidden" name="questionid[]" value="'.$value['questiondt'].'" />
                                        <input type="hidden" name="questionname[]" value="'.$value['questiondt'].'" />
                                        <input type="hidden" name="datainputtype[]" value="'.$value['questiondt_questiontype'].'" />
                                        <input type="hidden" name="questionnameinput['.$value['questiondt'].']" value="'.$inputTypeName['questiontype_type'].'" />
                                        <input type="hidden" name="questionismain['.$value['questiondt'].']" value="'.($value['questiondt_parent'] > 0 ? '' : '1').'" />
                                        <input type="hidden" name="questionismainname['.$value['questiondt'].']" value="'.$parentdeata['questiondt'].'" /> 
                                        <input type="hidden" name="questionnameinputafter['.$value['questiondt'].']" value="'.$parentdeata['questionoption_questiondt'].'" />
                                        <input type="hidden" name="questionnameinputparent['.$parentdeata['questiondt'].']" value="'.$parentdeata['questiondt'].'" />
                                        <input type="hidden" name="questionnameinputafteroptoion['.$value['questiondt'].']" value="'.($value['questiondt_after'] ? 'option'.$parentdeata['questionoption_questiondt'].($parentdeata['questionoption_order'] - 1) : '' ).'" />
                                        <div class="col-lg-12">
                                            คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext['.$value['questiondt'].']" required="" value="'.$value['questiondt_title'].'" />
                                        </div>
                                        <div class="list-group nested-sortable">
                                            '.$this->getDataOption($value['questiondt'],$pid,$value,$questionArray).'
                                        </div>
                                    </div>';
                        $html .= '</div>
                    ';

                    if(count($data) == ($key +1) && count($data) > 0 && $parentdeata){
                        $html .= '<div class="col-lg-12"><span class="btn btn-primary mt-3" onclick="setQuestionmodal(`question`,`after`,`content'.$value['questiondt'].'`,`'.$questiondt.'`,`option'.$questiondt.($after-1).'` );"><i class="bi bi-file-plus"></i> เพิ่มคำถาม</span></div>';
                    }

                    $html .= '<input type="hidden" name="allquestionName[]" id="allquestionName_'.$value['questiondt'].'" value="'.$value['questiondt'].'" />';
                    
                }
                       
            }

            return $html;

        }

    }

    function getDataOption($question,$pid,$dataParent,$questionArray)
    {
        global $go_ncadb;

        $sql        = "SELECT * FROM tb_questionoption WHERE questionoption_questiondt = '".$question."' ORDER BY questionoption_order ASC";
        $dataOption = $go_ncadb->ncaretrieve($sql, "question");
        $data       = $this->ncaArrayConverter($dataOption);

        $sqlOptionType  = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
        $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");
        $arr_OptionType = $this->ncaArrayConverter($arr_OptionType);
        $arrOptionType  = array();
        
        foreach ($arr_OptionType as $key => $value) {
            $arrOptionType[$value['questiontype']] = $value;
        }
        
        $html = "";
            
        $inputTypeName = $this->getInpustType("questiontype",$dataParent['questiondt_questiontype']);
        foreach ($data as $key => $value) {
            $order = ($key + 1);
            $sql_parent= "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."'";
            $dp = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP       = $this->ncaArrayConverter($dp);
            
            $html .= '  <div class="list-group-item nested-2 answer border-none ms-5" data-id="question'.$value['questionoption_questiondt'].$key.'" style >
                            '.$inputTypeName['questiontype_name']." : ".$value['questionoption_order'].' 

                            <input type="hidden" name="questionoption_questiondt_'.$value['questionoption_questiondt'].'" value="'.$value['questionoption_questiondt'].'" />

                            <input type="hidden" name="'.$value['questionoption_questiondt'].$key.'" value="'.$inputTypeName['questiontype_type'].'" />
                            <input class="form-control-40 col-lg-4" type="text" name="option'.$value['questionoption_questiondt'].'[]" id="option'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_name'].'" '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'/>
                            คะเเนน : <input class="form-control-custom col-lg-1" type="number" name="optionvalue'.$value['questionoption_questiondt'].'[]" id="optionvalue'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_value'].'"  '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'>

                            
                            <label class="form-check-label" for="questionoption_images_'.$value['questionoption'].'">
                                ต้องการให้เเนบรูปหรือไม่ : 
                            </label>
                            <input class="form-check-input" type="checkbox" style="vertical-align: middle;" value="1" id="questionoption_images_'.$value['questionoption'].'" name="questionoption_images['.$value['questionoption_questiondt'].']['.$value['questionoption'].']" '.($value['questionoption_images'] == 1 ?  'checked=checked' : '' ).'>

                            <input type="hidden" name="optionid'.$value['questionoption_questiondt'].'[]" id="optionid'.$value['questionoption_questiondt'].'" value="'.$value['questionoption'].'" >
                            <div class="list-group-item nested-3 '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-3 mt-3 mb-3" data-id="'.$pid.'" >
                                '.$this->generateIsParentQuestion("questiondt_parent",$dataParent['questiondt'],$order,$value,$questionArray).'
                            </div>'
                            .(
                                count($dataP) > 0 
                                ? '' 
                                : '<span class="btn btn-primary ms-3" id="addquestionquestion'.$value['questionoption_questiondt'].$key.'" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questionoption_questiondt'].$key.'`,`'.$value['questionoption_questiondt'].'` ,`option'.$dataParent['questiondt'].'`);" >
                                    สร้างคำถาม<!--หลังจากคำตอบนี้--></span>
                                ' ).'
                        </div>';
        }
       

        return $html;
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

    function getInpustType($field="",$data="")
    {

        global $go_ncadb;
        $where = "";
        if(trim($field)){
            $where = " AND ".$field." = '".$data."'";
        }
        $sqlOptionType  = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ".$where;
        $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");
        $arr_OptionType = $this->ncaArrayConverter($arr_OptionType);
        
        if(trim($field) && trim($data)){
            return $arr_OptionType[0];
        }else{
            $arrOptionType  = array();
            foreach ($arr_OptionType as $key => $value) {
                $arrOptionType[$value['questiontype']] = $value;
            }
            return $arrOptionType;
        }
    }

    function deleteMainQuestion($id,$user)
    {
        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');
        
        $sqlInsertQuestionoption = new SqlBuilder();
        $sqlInsertQuestionoption->SetTableName("tb_question");
        $ii = 0;

        $sqlObjoption = null;
        $sqlObjoption[$ii++] = new TField("question_active", 0, "string");
        $sqlObjoption[$ii++] = new TField("question_modispid", $user, "string");
        $sqlObjoption[$ii++] = new TField("question_modidatetime", $datetime, "string");

        $sqlInsertQuestionoption->SetField($sqlObjoption);
        $sqlInsertQuestionoption->SetWhereClause(" question = '".$id."'");
        $queryQuestionoption = $sqlInsertQuestionoption->UpdateSql();

        
        $data = array();
        $data['id'] = $id;
        $data['success'] = 0;
        $data['fail'] = 0;

        if ($go_ncadb->ncaexec($queryQuestionoption, "question")) {
            $data['success'] = 1;
        } else {
            $data['fail'] = 1;
        }
        
        return $data;
        
    }

    /* function setArrayQuestionPush($id){

        if($id){
            array_push($this->$question,$id);
        }
        
    } */

}
