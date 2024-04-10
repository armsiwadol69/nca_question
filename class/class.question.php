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
        /* echo "<pre>";
        print_r($info);
        print_r($alldata);
        die(); */
       
        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');
        $data = array();

        if($info['mquestiontypecheck'] == 1 ){

            $sqlInsertMquestiontype = new SqlBuilder();
            $sqlInsertMquestiontype->SetTableName("tb_questioncategories");
            $questionId = 0;
            $questionIddt = 0;
            $ii = 0;
            $sqlObj = null;
            $sqlObj[$ii++] = new TField("questioncategories_compfunc", iconv('utf-8', 'tis-620', $info['staffcompfunc']), "string");
            $sqlObj[$ii++] = new TField("questioncategories_compfuncdep", iconv('utf-8', 'tis-620', $info['staffcompfuncdep']), "string");
            $sqlObj[$ii++] = new TField("questioncategories_name", iconv('utf-8', 'tis-620', $info['mquestiontype_name']), "string");
            $sqlObj[$ii++] = new TField("questioncategories_active", '1', "string");
            $sqlObj[$ii++] = new TField("questioncategories_recspid", $info['par_userid'], "string");
            $sqlObj[$ii++] = new TField("questioncategories_recdatetime", $datetime, "string");

            $sqlInsertMquestiontype->SetField($sqlObj);
            $queryMquestiontype = $sqlInsertMquestiontype->InsertSql();

            if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {
                $questioncategories = $go_ncadb->ncaGetInsId("question");
                $info['mquestiontype'] = $questioncategories;
            } else {
                $go_ncadb->ncarollback("question");
                $data['fail'] = 1;
                $data['sql'] = $queryMquestiontype;
                return $data;
            }
        }

        $sqlInsertQuestion = new SqlBuilder();
        $sqlInsertQuestion->SetTableName("tb_question");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;
        $sqlObj[$ii++] = new TField("question_name", iconv('utf-8', 'tis-620', $info['par_qname']), "string");
        $sqlObj[$ii++] = new TField("question_detail", iconv('utf-8', 'tis-620', $info['par_qdatail']), "string");
        $sqlObj[$ii++] = new TField("question_compfunc", iconv('utf-8', 'tis-620', $info['staffcompfunc']), "string");
        $sqlObj[$ii++] = new TField("question_compfuncdep", iconv('utf-8', 'tis-620', $info['staffcompfuncdep']), "string");
        $sqlObj[$ii++] = new TField("question_questioncategories", iconv('utf-8', 'tis-620', $info['mquestiontype']), "string");
        $sqlObj[$ii++] = new TField("question_questioncategroup", iconv('utf-8', 'tis-620', $info['questiongroup']), "string");
        $sqlObj[$ii++] = new TField("question_questionmode", iconv('utf-8', 'tis-620', $info['questionmode']), "string");
        $sqlObj[$ii++] = new TField("question_active", '1', "string");
        
        
        if($info['par_questioninfoid'] && $info['questioncopy'] == 0){

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

        if ($go_ncadb->ncaexec($queryQuestion, "question")) {
            if($info['par_questioninfoid'] && $info['questioncopy'] == 0){
                $questionId = $info['par_questioninfoid'];
            }else{
                $questionId = $go_ncadb->ncaGetInsId("question");
            }

        } else {
            $go_ncadb->ncarollback("question");
            $data['fail'] = 1;
            $data['sql'] = $queryQuestion;
            return $data;
        }

        $array_insert  = array();
        $index = 1;
        foreach ($alldata as $key => $value) {
            if($value['questiondtdeleted'] == "1"){

                $sqlInsertQuestiondt = new SqlBuilder();
                $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                $ii = 0;
                $sqlObjdt = null;

                $sqlObjdt[$ii++] = new TField("questiondt_active", 0, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modispid", $info['par_userid'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");

                $sqlInsertQuestiondt->SetField($sqlObjdt);
                $sqlInsertQuestiondt->SetWhereClause(" questiondt = '". $value['questiondt'] ."'");
                $queryQuestiondtDel = $sqlInsertQuestiondt->UpdateSql();

                if (!$go_ncadb->ncaexec($queryQuestiondtDel, "question")) {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryQuestiondtDel;
                    return $data;
                }

            }else{
                $sqlInsertQuestiondt = new SqlBuilder();
                $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                $ii = 0;
                $sqlObjdt = null;
                $sqlObjdt[$ii++] = new TField("questiondt_question", $questionId, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_title", iconv('utf-8', 'tis-620', $value['datatext']), "string");
                $sqlObjdt[$ii++] = new TField("questiondt_parent",  $array_insert['datadt'][$value['mainkey']], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_activities",  $value['dataactivities'], "string");
                $order = "";
                if($value['dataafteroption']){
                    $textop = "option".$value['mainkey'];
                    $order = str_replace($textop,"",$value['dataafteroption']);
                }
                $sqlObjdt[$ii++] = new TField("questiondt_after", ($value['datamain'] ? "" : ($order + 1) ), "string");
                $sqlObjdt[$ii++] = new TField("questiondt_order", $index, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_questiontype", $value['datainputtype'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_active", 1, "string");

                if($value['questiondt'] > 0){

                    if($info['questioncopy'] > 0){

                        $sqlObjdt[$ii++] = new TField("questiondt_recspid", $info['par_userid'], "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");

                        $sqlInsertQuestiondt->SetField($sqlObjdt);
                        $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();

                    }else{

                        $sqlObjdt[$ii++] = new TField("questiondt_modispid", $info['par_userid'], "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");
                        $sqlInsertQuestiondt->SetField($sqlObjdt);
                        $sqlInsertQuestiondt->SetWhereClause(" questiondt = '".$value['questiondt']."'");
                        $queryQuestiondt = $sqlInsertQuestiondt->UpdateSql();

                    }

                }else{

                    $sqlObjdt[$ii++] = new TField("questiondt_recspid", $info['par_userid'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");
                    $sqlInsertQuestiondt->SetField($sqlObjdt);
                    $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();

                }

                if ($go_ncadb->ncaexec($queryQuestiondt, "question")) {

                    if($value['questiondt'] && $info['questioncopy'] == 0){
                        $questionIddt = $value['questiondt'];
                        $array_insert['datadt'][$key] = $value['questiondt'];
                    }else{
                        $questionIddt = $go_ncadb->ncaGetInsId("question");
                        $array_insert['datadt'][$key] = $questionIddt;
                    }

                } else {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryQuestiondt;
                    return $data;
                }

                foreach ($value['dataoption'] as $key2 => $value2) {

                    $order = ($key2+1);
                    $sqlInsertQuestionoption = new SqlBuilder();
                    $sqlInsertQuestionoption->SetTableName("tb_questionoption");
                    $ii = 0;

                    $sqlObjoption = null;
                    $sqlObjoption[$ii++] = new TField("questionoption_question", $questionId, "string");
                    $sqlObjoption[$ii++] = new TField("questionoption_questiondt", $questionIddt, "string");
                    $sqlObjoption[$ii++] = new TField("questionoption_name", iconv('utf-8', 'tis-620', $value2), "string");
                    $sqlObjoption[$ii++] = new TField("questionoption_value", $value['dataoptionvalue'][$key2]);
                    $sqlObjoption[$ii++] = new TField("questionoption_images", ($value['optionimages'][$key2] ? "1" : "0"));
                    $sqlObjoption[$ii++] = new TField("questionoption_order", $order, "string");
                    $sqlObjoption[$ii++] = new TField("questionoption_active", 1, "string");

                    if($value['questionoption'][$key2] > 0  && $info['questioncopy'] == 0){

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
                        $questionIdoption = $go_ncadb->ncaGetInsId("question");

                    } else {
                        $go_ncadb->ncarollback("question");
                        $data['fail'] = 1;
                        $data['sql'] = $queryQuestionoption;
                        return $data;
                    }
                }

            }

            $index++;

        }

        /* $result_diff = array_diff($info['oldquestion'], $info['questionid']);
        if($result_diff){
            foreach ($result_diff as $key3 => $value3) {

                $sqlInsertQuestiondt = new SqlBuilder();
                $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                $ii = 0;
                $sqlObjdt = null;

                $sqlObjdt[$ii++] = new TField("questiondt_active", 0, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modispid", $info['par_userid'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");

                $sqlInsertQuestiondt->SetField($sqlObjdt);
                $sqlInsertQuestiondt->SetWhereClause(" questiondt = '". $value3."'");
                $queryQuestiondtDel = $sqlInsertQuestiondt->UpdateSql();

                if (!$go_ncadb->ncaexec($queryQuestiondtDel, "question")) {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryQuestionoption;
                    return $data;
                }
            }
        } */

        if($data['fail'] > 0){
            return $data;
        }else{
            $data['success'] = 1;
            $data['sql'] = "";
            $data['questioninfoid'] = $questionId;

            return $data;
        }

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
            
            $sqlactivities = "SELECT * FROM tb_activities WHERE activities_active = 1";
            $arractivities = $go_ncadb->ncaretrieve($sqlactivities, "question");
            $arr_activities  = $this->ncaArrayConverter($arractivities);

            if($data){

                $html = "";
                foreach ($data as $key => $value) {

                    array_push($questionArray,$value['questiondt']);
                    $mainfrom = $parentdeata['questionoption_questiondt'].($parentdeata['questionoption_order'] - 1);
                    $pid  = $this->generateRandomString(15);
                    $inputTypeName = $this->getInpustType("questiontype",$value['questiondt_questiontype']);

                    $htmlActivities = "";
                    foreach ($arr_activities as $k_activities => $v_activities ){
                        $selected = "";
                        if($v_activities['activities'] == $value['questiondt_activities']){
                            $selected = "selected";
                        }
                        $htmlActivities .=  '<option value="'.$v_activities['activities'].'" '.$selected.'> '.$v_activities['activities_name'].' </option>';
                    }
                    $html .= '  <div class="bgcontentcolor content'.$value['questiondt'].'" id="'.$pid.'">
                                    <input type="hidden" name="mainname[]" value="'.$pid.'" />
                                    <input type="hidden" name="oldquestion[]" value="'.$value['questiondt'].'" />
                                    <div class="list-group-item nested-3 question" id="'.$value['questiondt'].'" data-id="'.$value['questiondt'].'">
                                        <span class="btn btn-primary" id="delQuestion'.$value['questiondt'].'" style="position: absolute; right: 8px;" onclick="deleteQuestionoption(`'.$value['questiondt'].'`,`'.$mainfrom.'`,`questionquestion'.$mainfrom.'`,`1`);">
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
                                        <input type="hidden" name="questionnameinfrom['.$mainfrom.']" value="'.$mainfrom.'" />
                                        <!--<input type="hidden" name="questionactivities['.$value['questiondt'].']" value="'.$value['questiondt_activities'].'" />-->
                                        <input type="hidden" name="questionnameinputparent['.$parentdeata['questiondt'].']" value="'.$parentdeata['questiondt'].'" />
                                        <input type="hidden" name="questionnameinputafteroptoion['.$value['questiondt'].']" value="'.($value['questiondt_after'] ? 'option'.$parentdeata['questionoption_questiondt'].($parentdeata['questionoption_order'] - 1) : '' ).'" />
                                        <input type="hidden" id="questiondtdeleted_'.$value['questiondt'].'" name="questiondtdeleted['.$value['questiondt'].']" value="" />
                                        <!--<div class="col-lg-12">
                                            คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext['.$value['questiondt'].']" required="" value="'.$value['questiondt_title'].'" />
                                        </div>-->
                                       
                                        <div class="form-group row">
                                            <div class="col-lg-6">
                                                คำถาม : <input class="form-control-50 col-lg-10" type="text" name="questiontext['.$value['questiondt'].']" required="" value="'.$value['questiondt_title'].'" />
                                            </div>
                                            <span style="float: left; width: unset; line-height: 35px;">เลือกลักษณะของการตรวจ : </span>
                                            <div class="col-sm-3">
                                            <select class="form-select" name="questionactivities['.$value['questiondt'].']" name="questionactivities_'.$value['questiondt'].'" aria-label="isshowing">
                                                '. $htmlActivities.'
                                            </select>
                                            </div>
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
            $sql_parent= "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."' AND questiondt_active = 1";
            $dp = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP       = $this->ncaArrayConverter($dp);
            
            $html .= '  <div class="list-group-item nested-2 answer border-none ms-4" data-id="question'.$value['questionoption_questiondt'].$key.'" style >
                            '.$inputTypeName['questiontype_name']." : ".$value['questionoption_order'].' 
                            <input type="hidden" name="questionoption_questiondt_'.$value['questionoption_questiondt'].'" value="'.($copy > 0 ? "" : $value['questionoption_questiondt']).'" />
                            <input type="hidden" name="'.$value['questionoption_questiondt'].$key.'" value="'.$inputTypeName['questiontype_type'].'" />
                            <input class="form-control-40" type="text" name="option'.$value['questionoption_questiondt'].'[]" id="option'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_name'].'" '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'/>
                            คะเเนน : <input class="form-control-custom col-lg-1" type="number" name="optionvalue'.$value['questionoption_questiondt'].'[]" id="optionvalue'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_value'].'"  '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'>

                            <label class="form-check-label" for="questionoption_images_'.$value['questionoption'].'">
                                ต้องการให้เเนบรูปหรือไม่ : 
                            </label>
                            <input class="form-check-input" type="checkbox" style="vertical-align: middle;" value="1" id="questionoption_images_'.$value['questionoption'].'" name="questionoption_images['.$value['questionoption_questiondt'].']['.$key.']" '.($value['questionoption_images'] == 1 ?  'checked=checked' : '' ).'>

                            <input type="hidden" name="optionid'.$value['questionoption_questiondt'].'[]" id="optionid'.$value['questionoption_questiondt'].'" value="'.$value['questionoption'].'" >
                            <div class="list-group-item nested-3 '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-5 mt-3 mb-3" data-id="'.$pid.'" >
                                '.$this->generateIsParentQuestion("questiondt_parent",$dataParent['questiondt'],$order,$value,$questionArray).'
                            </div>'
                            .(
                                count($dataP) > 0 
                                ? '<span class="btn btn-primary ms-3 hide" id="addquestionquestion'.$value['questionoption_questiondt'].$key.'" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questionoption_questiondt'].$key.'`,`'.$value['questionoption_questiondt'].'` ,`option'.$dataParent['questiondt'].$key.'`);" >
                                สร้างคำถาม</span>
                            ' 
                                : '<span class="btn btn-primary ms-3" id="addquestionquestion'.$value['questionoption_questiondt'].$key.'" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questionoption_questiondt'].$key.'`,`'.$value['questionoption_questiondt'].'` ,`option'.$dataParent['questiondt'].$key.'`);" >
                                    สร้างคำถาม</span>
                                ' 
                            ).'
                        </div>
                    ';
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

    function updateMtype($post){

        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');

        $data = array();
        $data['success'] = 0;
        $data['fail'] = 0;
        $data['html'] = "";

        foreach($post['mname'] as $key => $value){

            $sqlInsertMquestiontype = new SqlBuilder();
            $sqlInsertMquestiontype->SetTableName("tb_questioncategories");
            $ii = 0;
            $sqlObj = null;

            if($post['editmnamechange'][$key] > 0){
                $sqlObj[$ii++] = new TField("questioncategories_name", iconv('utf-8', 'tis-620', $value), "string");
            }
            $sqlObj[$ii++] = new TField("questioncategories_active", ($post['mtypename'][$key] > 0 ? "0" : "1"), "string");
            $sqlObj[$ii++] = new TField("questioncategories_modispid", $_SESSION['userData']['stf'], "string");
            $sqlObj[$ii++] = new TField("questioncategories_modidatetime", $datetime, "string");

            $sqlInsertMquestiontype->SetField($sqlObj);
            $sqlInsertMquestiontype->SetWhereClause(" questioncategories = '".$key."'");
            $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();
            
            if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {
                $data['success'] = 1;
            } else {
                $go_ncadb->ncarollback("question");
                $data['fail'] = 1;
                $data['sql'] = $queryMquestiontype;
                
                return $data;
            }
        }

        if($data['success'] == 1){
            $generateMtype = $this->generateMtype($post['mtcurrent'],$post['mtstaffcompfunc']);
        }
        $data['html'] = $generateMtype['html'];
    

        return $data;

    }


    function generateMtype($currentid=0,$staffcompfunc){

        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');
        $data = array();
        $data['array'] = array();
        $data['html'] = "";
        $data['htmlmodal'] = "";

        $mstaffcompfunc = ($staffcompfunc > 0 ? $staffcompfunc : $_SESSION['userData']['staffcompfunc']);

        $sqlmquestiontype  = "SELECT * FROM tb_questioncategories WHERE questioncategories_compfunc = '".$mstaffcompfunc."' OR questioncategories_default = 1";
        $arr_mquestiontype = $go_ncadb->ncaretrieve($sqlmquestiontype, "question");
        $arrmquestiontype  = $this->ncaArrayConverter($arr_mquestiontype);
        $data['array'] = $arrmquestiontype;

        $html  = '<select class="form-select" name="mquestiontype" id="mquestiontype">';
        $html .= '<option >เลือกประเภทเรื่อง</option>';
        
        if(count($arrmquestiontype) > 0){
            foreach ($arrmquestiontype as $key => $value) {
                $selected = "";
                if($value['questioncategories_active'] == 1){
                    if($value['questioncategories'] == $currentid && $value['questioncategories_active'] > 0){
                        $selected = "selected";
                    }
                    $html .= '<option value="'.$value['questioncategories'].'" '.$selected.'> '.$value['questioncategories_name'].' </option>';
                }
            }
        }

        $html .= '</select>';
        
        $data['html'] = $html;


        $htmlmodal = "";
        if(count($arrmquestiontype) > 0){
            foreach ($arrmquestiontype as $mkey => $mvalue) {
                if($mvalue['questioncategories_default'] == 0){
                    $isChecked = "";
                    if($mvalue['questioncategories_active'] == 0){
                        $isChecked = "checked";
                    }
                    /* $htmlmodal .= ' <tr>
                                        <td>
                                        <span id="editmname_'.$mvalue['questioncategories'].'" onclick="editmname(`inputmname_'.$mvalue['questioncategories'].'`,`editmname_'.$mvalue['questioncategories'].'`,`editmnamechange_'.$mvalue['questioncategories'].'`,`'.$mvalue['questioncategories'].'`)" > '.$mvalue['questioncategories_name'].' <i class="bi bi-pencil-square cpointer" style="float: right;">แก้ไข</i>
                                        </span>
                                        <span id="inputmname_'.$mvalue['questioncategories'].'" style="display: none;">
                                            <input type="text" id="mname_'.$mvalue['questioncategories'].'" name="mname['.$mvalue['questioncategories'].']" class="form-control" required="" value="'.$mvalue['questioncategories_name'].'" style="width:88%; float:left;">
                                            <button onclick="editmname(`editmname_'.$mvalue['questioncategories'].'`,`inputmname_'.$mvalue['questioncategories'].'`,`cancel_'.$mvalue['questioncategories'].'`,`'.$mvalue['questioncategories'].'`)" type="button" class="btn btn-danger" style="float: right;">ยกเลิก</button>
                                        </span>
                                        <input type="hidden" name="editmnamechange['.$mvalue['questioncategories'].']" id="editmnamechange_'.$mvalue['questioncategories'].'" value="">
                                        </td>
                                        <td align="center" style="vertical-align: middle;">
                                        <input class="form-check-input" type="checkbox" value="1" name="mtypename['.$mvalue['questioncategories'].']" id="mtypename5" '.$isChecked.'>
                                        </td>
                                    </tr>'; */
                    $htmlmodal .= ' <tr>
                                        <td>
                                        <span id="editmname_'.$mvalue['questioncategories'].'" > '.$mvalue['questioncategories_name'].'
                                        </span>
                                        <span id="inputmname_'.$mvalue['questioncategories'].'" style="display: none;">
                                            <input type="text" id="mname_'.$mvalue['questioncategories'].'" name="mname['.$mvalue['questioncategories'].']" class="form-control" required="" value="'.$mvalue['questioncategories_name'].'" style="width:88%; float:left;">
                                            <button onclick="editmname(`editmname_'.$mvalue['questioncategories'].'`,`inputmname_'.$mvalue['questioncategories'].'`,`cancel_'.$mvalue['questioncategories'].'`,`'.$mvalue['questioncategories'].'`)" type="button" class="btn btn-danger" style="float: right;">ยกเลิก</button>
                                        </span>
                                        <input type="hidden" name="editmnamechange['.$mvalue['questioncategories'].']" id="editmnamechange_'.$mvalue['questioncategories'].'" value="">
                                        </td>
                                        <td align="center" style="vertical-align: middle;">
                                        <input class="form-check-input" type="checkbox" value="1" name="mtypename['.$mvalue['questioncategories'].']" id="mtypename5" '.$isChecked.'>
                                        </td>
                                    </tr>';
                }
            }
        }
        
        $data['htmlmodal'] = $htmlmodal;

        return $data;

    }

}
