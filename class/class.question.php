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
        return $arr;
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

            $sqlInsertCate = new SqlBuilder();
            $sqlInsertCate->SetTableName("tb_questioncategories");
            $questionId = 0;
            $questionIddt = 0;
            $ii = 0;
            $sqlObj = null;
            $sqlObj[$ii++] = new TField("questioncategories_compfunc", $_SESSION['userData']['staffcompfunc'], "string");
            $sqlObj[$ii++] = new TField("questioncategories_compfuncdep",  $_SESSION['userData']['staffcompfuncdep'], "string");
            $sqlObj[$ii++] = new TField("questioncategories_compfuncdepsec",  $_SESSION['userData']['staffcompfuncdepsec'], "string");
            $sqlObj[$ii++] = new TField("questioncategories_name", $info['mquestiontype_name'], "string");
            $sqlObj[$ii++] = new TField("questioncategories_active", '1', "string");
            $sqlObj[$ii++] = new TField("questioncategories_recspid", $info['par_userid'], "string");
            $sqlObj[$ii++] = new TField("questioncategories_recdatetime", $datetime, "string");

            $sqlInsertCate->SetField($sqlObj);
            $queryMquestiontype = $sqlInsertCate->InsertSql();

            if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {
                $questioncategories = $go_ncadb->ncaGetInsId("question");
                $info['mquestiontype'] = $questioncategories;
            } else {
                $go_ncadb->ncarollback("question");
                $data['fail'] = 1;
                $data['sql'] = $queryMquestiontype;
                return $data;
            }


            $sqlInsertGroup= new SqlBuilder();
            $sqlInsertGroup->SetTableName("tb_questiongroup");
            $ii = 0;
            $sqlObj = null;
            
            if($info['questiongroupcheck'] == 1 ){
                $arrmquestiongroup[0]['questiongroup_name'] = $info['questiongroup_name'];

            }else{
                $sql = "SELECT *  FROM tb_questiongroup WHERE questiongroup = '".$info['questiongroup']."'";
                $result = $go_ncadb->ncaretrieve($sql, "question");
                $arrmquestiongroup  = $result;
            }


            $sqlObj[$ii++] = new TField("questiongroup_name", $arrmquestiongroup[0]['questiongroup_name'], "string");
            $sqlObj[$ii++] = new TField("questiongroup_description", $arrmquestiongroup[0]['questiongroup_description'], "string");
            $sqlObj[$ii++] = new TField("questiongroup_questioncategories", $info['mquestiontype'] , "string");
            $sqlObj[$ii++] = new TField("questiongroup_recspid", $_SESSION['userData']['stf'], "string");
            $sqlObj[$ii++] = new TField("questiongroup_recdatetime", $datetime, "string");
            $sqlObj[$ii++] = new TField("questiongroup_active", "1", "string");

            $sqlInsertGroup->SetField($sqlObj);
            $queryGroupe = $sqlInsertGroup->InsertSql();

            if ($go_ncadb->ncaexec($queryGroupe, "question")) {
                $questionGroup = $go_ncadb->ncaGetInsId("question");
                $info['questiongroup'] = $questionGroup;
            } else {
                $go_ncadb->ncarollback("question");
                $data['fail'] = 1;
                $data['sql'] = $queryGroupe;
                return $data;
            }
            
        }else if($info['questiongroupcheck'] == 1 ){

            $sqlInsertGroup= new SqlBuilder();
            $sqlInsertGroup->SetTableName("tb_questiongroup");
            $ii = 0;
            $sqlObj = null;

            $arrmquestiongroup[0]['questiongroup_name'] = $info['questiongroup_name'];

            $sqlObj[$ii++] = new TField("questiongroup_name",  $arrmquestiongroup[0]['questiongroup_name'], "string");
            $sqlObj[$ii++] = new TField("questiongroup_description", $arrmquestiongroup[0]['questiongroup_description'], "string");
            $sqlObj[$ii++] = new TField("questiongroup_questioncategories", $info['mquestiontype'] , "string");
            $sqlObj[$ii++] = new TField("questiongroup_recspid", $_SESSION['userData']['stf'], "string");
            $sqlObj[$ii++] = new TField("questiongroup_recdatetime", $datetime, "string");
            $sqlObj[$ii++] = new TField("questiongroup_active", "1", "string");

            $sqlInsertGroup->SetField($sqlObj);
            $queryGroupe = $sqlInsertGroup->InsertSql();

            if ($go_ncadb->ncaexec($queryGroupe, "question")) {
                $questionGroup = $go_ncadb->ncaGetInsId("question");
                $info['questiongroup'] = $questionGroup;
            } else {
                $go_ncadb->ncarollback("question");
                $data['fail'] = 1;
                $data['sql'] = $queryGroupe;
                return $data;
            }

        }

        $sqlInsertQuestion = new SqlBuilder();
        $sqlInsertQuestion->SetTableName("tb_question");
        $questionId = 0;
        $questionIddt = 0;
        $ii = 0;
        $sqlObj = null;
        $sqlObj[$ii++] = new TField("question_name", $info['par_qname'], "string");
        $sqlObj[$ii++] = new TField("question_detail", $info['par_qdatail'], "string");
        $sqlObj[$ii++] = new TField("question_compfunc", $info['staffcompfunc'], "string");
        $sqlObj[$ii++] = new TField("question_compfuncdep", $info['staffcompfuncdep'], "string");
        $sqlObj[$ii++] = new TField("question_compfuncdepsec", $info['staffcompfuncdepsec'], "string");
        $sqlObj[$ii++] = new TField("question_questioncategories", $info['mquestiontype'], "string");
        $sqlObj[$ii++] = new TField("question_questioncategroup", $info['questiongroup'], "string");
        $sqlObj[$ii++] = new TField("question_questionmode", $info['questionmode'], "string");
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
                $sqlObjdt[$ii++] = new TField("questiondt_title", $value['datatext'], "string");
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
                    $sqlObjoption[$ii++] = new TField("questionoption_name", $value2, "string");
                    $sqlObjoption[$ii++] = new TField("questionoption_value", $value['dataoptionvalue'][$key2]);
                    $sqlObjoption[$ii++] = new TField("questionoption_images", ($value['optionimages'][$key2] ? "1" : "0"));
                    $sqlObjoption[$ii++] = new TField("questionoption_mistakelevel", ($value['optionmistakelevel'][$key2]));
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

    function addNewQuestionCustom($info, $maindata, $alldata, $catemode=0)
    {

        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');
        $data = array();

        if($catemode == 0){
            
            if ($info['mquestiontypecheck'] == 1) {

                $sqlInsertCate = new SqlBuilder();
                $sqlInsertCate->SetTableName("tb_questioncategories");
                $questionId = 0;
                $questionIddt = 0;
                $ii = 0;
                $sqlObj = null;
                $sqlObj[$ii++] = new TField("questioncategories_compfunc", $_SESSION['userData']['staffcompfunc'], "string");
                $sqlObj[$ii++] = new TField("questioncategories_compfuncdep",  $_SESSION['userData']['staffcompfuncdep'], "string");
                $sqlObj[$ii++] = new TField("questioncategories_compfuncdepsec",  $_SESSION['userData']['staffcompfuncdepsec'], "string");
                $sqlObj[$ii++] = new TField("questioncategories_name", $info['mquestiontype_name'], "string");
                $sqlObj[$ii++] = new TField("questioncategories_active", '1', "string");
                $sqlObj[$ii++] = new TField("questioncategories_recspid", $_SESSION['userData']['stf'], "string");
                $sqlObj[$ii++] = new TField("questioncategories_recspcode", $_SESSION['userData']['staffcd'], "string");
                $sqlObj[$ii++] = new TField("questioncategories_recanme", $_SESSION['userData']['userdspms'], "string");
                $sqlObj[$ii++] = new TField("questioncategories_recdatetime", $datetime, "string");

                $sqlInsertCate->SetField($sqlObj);
                $queryMquestiontype = $sqlInsertCate->InsertSql();

                if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {
                    $questioncategories = $go_ncadb->ncaGetInsId("question");
                    $info['mquestiontype'] = $questioncategories;
                } else {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryMquestiontype;
                    return $data;
                }


                $sqlInsertGroup = new SqlBuilder();
                $sqlInsertGroup->SetTableName("tb_questiongroup");
                $ii = 0;
                $sqlObj = null;

                if ($info['questiongroupcheck'] == 1) {
                    $arrmquestiongroup[0]['questiongroup_name'] = $info['questiongroup_name'];
                } else {
                    $sql = "SELECT *  FROM tb_questiongroup WHERE questiongroup = '" . $info['questiongroup'] . "'";
                    $result = $go_ncadb->ncaretrieve($sql, "question");
                    $arrmquestiongroup  = $result;
                }


                $sqlObj[$ii++] = new TField("questiongroup_name", $arrmquestiongroup[0]['questiongroup_name'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_description", $arrmquestiongroup[0]['questiongroup_description'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_questioncategories", $info['mquestiontype'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_recspid", $_SESSION['userData']['stf'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_recdatetime", $datetime, "string");
                $sqlObj[$ii++] = new TField("questiongroup_active", "1", "string");

                $sqlInsertGroup->SetField($sqlObj);
                $queryGroupe = $sqlInsertGroup->InsertSql();

                if ($go_ncadb->ncaexec($queryGroupe, "question")) {
                    $questionGroup = $go_ncadb->ncaGetInsId("question");
                    $info['questiongroup'] = $questionGroup;
                } else {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryGroupe;
                    return $data;
                }

            } else if ($info['questiongroupcheck'] == 1) {

                $sqlInsertGroup = new SqlBuilder();
                $sqlInsertGroup->SetTableName("tb_questiongroup");
                $ii = 0;
                $sqlObj = null;

                $arrmquestiongroup[0]['questiongroup_name'] = $info['questiongroup_name'];

                $sqlObj[$ii++] = new TField("questiongroup_name", $arrmquestiongroup[0]['questiongroup_name'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_description", $arrmquestiongroup[0]['questiongroup_description'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_questioncategories", $info['mquestiontype'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_recspid", $_SESSION['userData']['stf'], "string");
                $sqlObj[$ii++] = new TField("questiongroup_recdatetime", $datetime, "string");
                $sqlObj[$ii++] = new TField("questiongroup_active", "1", "string");

                $sqlInsertGroup->SetField($sqlObj);
                $queryGroupe = $sqlInsertGroup->InsertSql();

                if ($go_ncadb->ncaexec($queryGroupe, "question")) {
                    $questionGroup = $go_ncadb->ncaGetInsId("question");
                    $info['questiongroup'] = $questionGroup;
                } else {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryGroupe;
                    return $data;
                }
                
            }

            $sqlInsertQuestion = new SqlBuilder();
            $sqlInsertQuestion->SetTableName("tb_question");
            $questionId = 0;
            $questionIddt = 0;
            $ii = 0;
            $sqlObj = null;
            $sqlObj[$ii++] = new TField("question_name", $info['par_qname'], "string");
            $sqlObj[$ii++] = new TField("question_detail", $info['par_qdatail'], "string");
            $sqlObj[$ii++] = new TField("question_compfunc", $info['staffcompfunc'], "string");
            $sqlObj[$ii++] = new TField("question_compfuncdep", $info['staffcompfuncdep'], "string");
            $sqlObj[$ii++] = new TField("question_compfuncdepsec", $info['staffcompfuncdepsec'], "string");
            $sqlObj[$ii++] = new TField("question_questioncategories", $info['mquestiontype'], "string");
            $sqlObj[$ii++] = new TField("question_questioncategroup", $info['questiongroup'], "string");
            $sqlObj[$ii++] = new TField("question_questionmode", $info['questionmode'], "string");
            // $sqlObj[$ii++] = new TField("question_departmentid", $info['departmentid'], "string");
            // $sqlObj[$ii++] = new TField("question_offensegroupid", $info['offensegroupid'], "string");
            $sqlObj[$ii++] = new TField("question_active", '1', "string");

            if ($info['par_questioninfoid'] && $info['questioncopy'] == 0) {

                $sqlObj[$ii++] = new TField("question_modispid", $info['par_userid'], "string");
                $sqlObj[$ii++] = new TField("question_modidatetime", $datetime, "string");
                $sqlObj[$ii++] = new TField("question_modispcode", $_SESSION['userData']['staffcd'], "string");
                $sqlObj[$ii++] = new TField("question_modiname", $_SESSION['userData']['userdspms'], "string");

                $sqlInsertQuestion->SetField($sqlObj);
                $sqlInsertQuestion->SetWhereClause(" question = '" . $info['par_questioninfoid'] . "'");
                $queryQuestion = $sqlInsertQuestion->UpdateSql();

            } else {

                $sqlObj[$ii++] = new TField("question_recspid", $info['par_userid'], "string");
                $sqlObj[$ii++] = new TField("question_recdatetime", $datetime, "string");
                $sqlObj[$ii++] = new TField("question_recspcode", $_SESSION['userData']['staffcd'], "string");
                $sqlObj[$ii++] = new TField("question_recname", $_SESSION['userData']['userdspms'], "string");

                $sqlInsertQuestion->SetField($sqlObj);
                $queryQuestion = $sqlInsertQuestion->InsertSql();
            }

            if ($go_ncadb->ncaexec($queryQuestion, "question")) {
                if ($info['par_questioninfoid'] && $info['questioncopy'] == 0) {
                    $questionId = $info['par_questioninfoid'];
                } else {
                    $questionId = $go_ncadb->ncaGetInsId("question");
                }
            } else {
                $go_ncadb->ncarollback("question");
                $data['fail'] = 1;
                $data['sql'] = $queryQuestion;
                return $data;
            }

        }

        $array_insert  = array();
        $index = 1;
        foreach ($alldata as $key => $value) {

            if ($value['questiondtdeleted'] == "1") {

                $sqlInsertQuestiondt = new SqlBuilder();
                $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                $ii = 0;
                $sqlObjdt = null;

                $sqlObjdt[$ii++] = new TField("questiondt_active", 0, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modispid", $info['par_userid'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modispcode", $_SESSION['userData']['staffcd'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_modiname", $_SESSION['userData']['userdspms'], "string");

                $sqlInsertQuestiondt->SetField($sqlObjdt);
                $sqlInsertQuestiondt->SetWhereClause(" questiondt = '" . $value['questiondt'] . "'");
                $queryQuestiondtDel = $sqlInsertQuestiondt->UpdateSql();

                if (!$go_ncadb->ncaexec($queryQuestiondtDel, "question")) {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryQuestiondtDel;
                    return $data;
                }

            } else {

                $sqlInsertQuestiondt = new SqlBuilder();
                $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                $ii = 0;
                $sqlObjdt = null;
                $sqlObjdt[$ii++] = new TField("questiondt_question", $questionId, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_title",  $value['datatext'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_parent",  $array_insert['datadt'][$value['mainkey']], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_activities",  $value['dataactivities'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_offenseid",  $value['dataoffense'], "string");
                $order = "";
                if ($value['dataafteroption']) {
                    $textop = "option" . $value['mainkey'];
                    $order = str_replace($textop, "", $value['dataafteroption']);
                }
                $sqlObjdt[$ii++] = new TField("questiondt_after", ($value['datamain'] ? "" : ($order + 1)), "string");
                $sqlObjdt[$ii++] = new TField("questiondt_order", $index, "string");
                $sqlObjdt[$ii++] = new TField("questiondt_questiontype", $value['datainputtype'], "string");
                $sqlObjdt[$ii++] = new TField("questiondt_active", 1, "string");

                if ($value['questiondt'] > 0) {

                    if ($info['questioncopy'] > 0) {

                        $sqlObjdt[$ii++] = new TField("questiondt_recspid", $info['par_userid'], "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");

                        $sqlInsertQuestiondt->SetField($sqlObjdt);
                        $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();
                    } else {

                        $sqlObjdt[$ii++] = new TField("questiondt_modispid", $info['par_userid'], "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_modispcode", $_SESSION['userData']['staffcd'], "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_modiname", $_SESSION['userData']['userdspms'], "string");
                        $sqlInsertQuestiondt->SetField($sqlObjdt);
                        $sqlInsertQuestiondt->SetWhereClause(" questiondt = '" . $value['questiondt'] . "'");
                        $queryQuestiondt = $sqlInsertQuestiondt->UpdateSql();
                    }
                } else {

                    $sqlObjdt[$ii++] = new TField("questiondt_recspid", $info['par_userid'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_recspcode", $_SESSION['userData']['staffcd'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_recname", $_SESSION['userData']['userdspms'], "string");
                    $sqlInsertQuestiondt->SetField($sqlObjdt);
                    $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();
                }

                if ($go_ncadb->ncaexec($queryQuestiondt, "question")) {

                    if ($value['questiondt'] && $info['questioncopy'] == 0) {
                        $questionIddt = $value['questiondt'];
                        $array_insert['datadt'][$key] = $value['questiondt'];
                    } else {
                        $questionIddt = $go_ncadb->ncaGetInsId("question");
                        $array_insert['datadt'][$key] = $questionIddt;
                    }
                } else {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryQuestiondt;
                    return $data;
                }

                if($value['dataoption'] && !$value['questiondt']) {
                    
                    foreach ($value['dataoption'] as $key2 => $value2) {

                        $order = ($key2 + 1);
                        $sqlInsertQuestionoption = new SqlBuilder();
                        $sqlInsertQuestionoption->SetTableName("tb_questionoption");
                        $ii = 0;

                        $sqlObjoption = null;
                        $sqlObjoption[$ii++] = new TField("questionoption_question", $questionId, "string");
                        $sqlObjoption[$ii++] = new TField("questionoption_questiondt", $questionIddt, "string");
                        $sqlObjoption[$ii++] = new TField("questionoption_name", $value2, "string");
                        $sqlObjoption[$ii++] = new TField("questionoption_value", $value['dataoptionvalue'][$key2]);
                        $sqlObjoption[$ii++] = new TField("questionoption_images", ($value['optionimages'][$key2] ? "1" : "0"));
                        $sqlObjoption[$ii++] = new TField("questionoption_mistakelevel", ($value['optionmistakelevel'][$key2]));
                        $sqlObjoption[$ii++] = new TField("questionoption_order", $order, "string");
                        $sqlObjoption[$ii++] = new TField("questionoption_active", 1, "string");

                        if ($value['questionoption'][$key2] > 0  && $info['questioncopy'] == 0) {

                            $sqlObjoption[$ii++] = new TField("questionoption_modispid", $info['par_userid'], "string");
                            $sqlObjoption[$ii++] = new TField("questionoption_modidatetime", $datetime, "string");
                            $sqlObjoption[$ii++] = new TField("questionoption_modispcode", $_SESSION['userData']['staffcd'], "string");
                            $sqlObjoption[$ii++] = new TField("questionoption_modiname", $_SESSION['userData']['userdspms'], "string");

                            $sqlInsertQuestionoption->SetField($sqlObjoption);
                            $sqlInsertQuestionoption->SetWhereClause(" questionoption = '" . $value['questionoption'][$key2] . "'");
                            $queryQuestionoption = $sqlInsertQuestionoption->UpdateSql();
                        } else {

                            $sqlObjoption[$ii++] = new TField("questionoption_recspid", $info['par_userid'], "string");
                            $sqlObjoption[$ii++] = new TField("questionoption_recdatetime", $datetime, "string");
                            $sqlObjoption[$ii++] = new TField("questionoption_recspcode", $_SESSION['userData']['staffcd'], "string");
                            $sqlObjoption[$ii++] = new TField("questionoption_recname", $_SESSION['userData']['userdspms'], "string");

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
            }

            $index++;
        }

        if ($data['fail'] > 0) {
            return $data;
        } else {
            $data['success'] = 1;
            $data['sql'] = "";
            $data['questioninfoid'] = $questionId;

            return $data;
        }
    }
    function manageDataQuestion($alldata)
    {

        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');
        $data = array();

        $array_insert  = array();
        $index = 1;
        // echo "<pre>";
        // print_r($alldata);
        // die();
        foreach ($alldata as $k => $v) {

            foreach ($v as $key => $value) {

                $questionId = $value['questiondt'];

                if ($value['questiondtdeleted'] == "1") {

                    $sqlInsertQuestiondt = new SqlBuilder();
                    $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                    $ii = 0;
                    $sqlObjdt = null;

                    $sqlObjdt[$ii++] = new TField("questiondt_active", 0, "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_modispid", $_SESSION['userData']['stf'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_modispcode", $_SESSION['userData']['staffcd'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_modispcode", $_SESSION['userData']['userdspms'], "string");

                    $sqlInsertQuestiondt->SetField($sqlObjdt);
                    $sqlInsertQuestiondt->SetWhereClause(" questiondt = '" . $value['questiondt'] . "'");
                    $queryQuestiondtDel = $sqlInsertQuestiondt->UpdateSql();

                    if (!$go_ncadb->ncaexec($queryQuestiondtDel, "question")) {
                        $go_ncadb->ncarollback("question");
                        $data['fail'] = 1;
                        $data['sql'] = $queryQuestiondtDel;
                        return $data;
                    }

                } else {

                    $sqlInsertQuestiondt = new SqlBuilder();
                    $sqlInsertQuestiondt->SetTableName("tb_questiondt");
                    $ii = 0;
                    $sqlObjdt = null;
                    $sqlObjdt[$ii++] = new TField("questiondt_question", $k, "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_title",  $value['datatext'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_parent",  $array_insert['datadt'][$value['mainkey']], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_activities",  $value['dataactivities'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_offenseid",  $value['dataoffense'], "string");
                    $order = "";
                    if ($value['dataafteroption']) {
                        $textop = "option" . $value['mainkey'];
                        $order = str_replace($textop, "", $value['dataafteroption']);
                    }
                    $sqlObjdt[$ii++] = new TField("questiondt_after", ($value['datamain'] ? "" : ($order + 1)), "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_order", $index, "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_questiontype", $value['datainputtype'], "string");
                    $sqlObjdt[$ii++] = new TField("questiondt_active", 1, "string");

                    if ($value['questiondt'] > 0) {

                        if ($info['questioncopy'] > 0) {

                            $sqlObjdt[$ii++] = new TField("questiondt_recspid", $_SESSION['userData']['stf'], "string");
                            $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");
                            $sqlObjdt[$ii++] = new TField("questiondt_recspcode", $_SESSION['userData']['staffcd'], "string");
                            $sqlObjdt[$ii++] = new TField("questiondt_recname", $_SESSION['userData']['userdspms'], "string");

                            $sqlInsertQuestiondt->SetField($sqlObjdt);
                            $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();
                        } else {

                            $sqlObjdt[$ii++] = new TField("questiondt_modispid", $_SESSION['userData']['stf'], "string");
                            $sqlObjdt[$ii++] = new TField("questiondt_modidatetime", $datetime, "string");
                            $sqlObjdt[$ii++] = new TField("questiondt_modispcode", $_SESSION['userData']['staffcd'], "string");
                            $sqlObjdt[$ii++] = new TField("questiondt_modiname", $_SESSION['userData']['userdspms'], "string");
                            $sqlInsertQuestiondt->SetField($sqlObjdt);
                            $sqlInsertQuestiondt->SetWhereClause(" questiondt = '" . $value['questiondt'] . "'");
                            $queryQuestiondt = $sqlInsertQuestiondt->UpdateSql();
                        }

                    } else {

                        $sqlObjdt[$ii++] = new TField("questiondt_recspid", $_SESSION['userData']['stf'], "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_recdatetime", $datetime, "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_recspcode", $_SESSION['userData']['staffcd'], "string");
                        $sqlObjdt[$ii++] = new TField("questiondt_recname", $_SESSION['userData']['userdspms'], "string");
                        $sqlInsertQuestiondt->SetField($sqlObjdt);
                        $queryQuestiondt = $sqlInsertQuestiondt->InsertSql();
                    }

                    // echo "Sql Dt => ".$queryQuestiondt."<br>";

                    if ($go_ncadb->ncaexec($queryQuestiondt, "question")) {

                        if ($value['questiondt'] ) {
                            $questionIddt = $value['questiondt'];
                            $array_insert['datadt'][$key] = $value['questiondt'];
                        } else {

                            $questionIddt = $go_ncadb->ncaGetInsId("question");
                            $array_insert['datadt'][$key] = $questionIddt;

                            if($value['dataoption']){
                        
                                foreach ($value['dataoption'] as $key2 => $value2) {
        
                                    $order = ($key2 + 1);
                                    $sqlInsertQuestionoption = new SqlBuilder();
                                    $sqlInsertQuestionoption->SetTableName("tb_questionoption");
                                    $ii = 0;
        
                                    $sqlObjoption = null;
                                    $sqlObjoption[$ii++] = new TField("questionoption_question", $k, "string");
                                    $sqlObjoption[$ii++] = new TField("questionoption_questiondt", $questionIddt, "string");
                                    $sqlObjoption[$ii++] = new TField("questionoption_name", $value2, "string");
                                    $sqlObjoption[$ii++] = new TField("questionoption_value", $value['dataoptionvalue'][$key2]);
                                    $sqlObjoption[$ii++] = new TField("questionoption_images", ($value['optionimages'][$key2] ? "1" : "0"));
                                    $sqlObjoption[$ii++] = new TField("questionoption_mistakelevel", ($value['optionmistakelevel'][$key2]));
                                    $sqlObjoption[$ii++] = new TField("questionoption_order", $order, "string");
                                    $sqlObjoption[$ii++] = new TField("questionoption_active", 1, "string");
        
                                    if ($value['questionoption'][$key2] > 0) {
        
                                        $sqlObjoption[$ii++] = new TField("questionoption_modispid", $_SESSION['userData']['stf'], "string");
                                        $sqlObjoption[$ii++] = new TField("questionoption_modidatetime", $datetime, "string");
                                        $sqlObjoption[$ii++] = new TField("questionoption_modispcode", $_SESSION['userData']['staffcd'], "string");
                                        $sqlObjoption[$ii++] = new TField("questionoption_modiname", $_SESSION['userData']['userdspms'], "string");
        
                                        $sqlInsertQuestionoption->SetField($sqlObjoption);
                                        $sqlInsertQuestionoption->SetWhereClause(" questionoption = '" . $value['questionoption'][$key2] . "'");
                                        $queryQuestionoption = $sqlInsertQuestionoption->UpdateSql();

                                    } else {
        
                                        $sqlObjoption[$ii++] = new TField("questionoption_recspid", $_SESSION['userData']['stf'], "string");
                                        $sqlObjoption[$ii++] = new TField("questionoption_recdatetime", $datetime, "string");
                                        $sqlObjoption[$ii++] = new TField("questionoption_modispcode", $_SESSION['userData']['staffcd'], "string");
                                        $sqlObjoption[$ii++] = new TField("questionoption_modiname", $_SESSION['userData']['userdspms'], "string");
        
                                        $sqlInsertQuestionoption->SetField($sqlObjoption);
                                        $queryQuestionoption = $sqlInsertQuestionoption->InsertSql();
                                    }
        
                                    // echo "Sql option => ".$queryQuestiondt."<br>";
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

                        }

                    } else {
                        $go_ncadb->ncarollback("question");
                        $data['fail'] = 1;
                        $data['sql'] = $queryQuestiondt;
                        return $data;
                    }

                }

            }

            if($k){
                $sqlInsertQuestion = new SqlBuilder();
                $sqlInsertQuestion->SetTableName("tb_question");
                $questionId = 0;
                $questionIddt = 0;
                $ii = 0;
                $sqlObj = null;

                $sqlObj[$ii++] = new TField("question_modispid", $_SESSION['userData']['stf'], "string");
                $sqlObj[$ii++] = new TField("question_modidatetime", $datetime, "string");
                $sqlObj[$ii++] = new TField("question_modispcode", $_SESSION['userData']['staffcd'], "string");
                $sqlObj[$ii++] = new TField("question_modiname", $_SESSION['userData']['userdspms'], "string");

                $sqlInsertQuestion->SetField($sqlObj);
                $sqlInsertQuestion->SetWhereClause(" question = '" . $k . "'");
                $queryQuestion = $sqlInsertQuestion->UpdateSql();

                if ($go_ncadb->ncaexec($queryQuestion, "question")) {

                } else {
                    $go_ncadb->ncarollback("question");
                    $data['fail'] = 1;
                    $data['sql'] = $queryQuestion;
                    return $data;
                }
            }

            $index++;
        }

        if ($data['fail'] > 0) {
            return $data;
        } else {
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
                // $xx[$k] = iconv('tis-620', 'utf-8', $v);
                $xx[$k] = $v;
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
            $sql        = "SELECT * FROM tb_questiondt AS QDT LEFT JOIN tb_question AS Q ON (Q.question=questiondt_question) WHERE QDT.".$field." = '".$questiondt."' AND QDT.questiondt_active = '1' ";

            if($after > 0){
                $sql .= " AND questiondt_after = '".$after."'";
            }

            $dataOption = $go_ncadb->ncaretrieve($sql, "question");
            $data       = $dataOption;
            
            $sqlactivities = "SELECT * FROM tb_activities WHERE activities_active = 1";
            $arractivities = $go_ncadb->ncaretrieve($sqlactivities, "question");
            $arr_activities  = $arractivities;

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
                                        <input type="hidden" name="question['.$value['question'].']" value="'.$value['question'].'" />
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
                                            <select class="form-select-40 selectmistakeoption" name="questionactivities['.$value['questiondt'].']" name="questionactivities_'.$value['questiondt'].'" aria-label="isshowing">
                                                '. $htmlActivities.'
                                            </select>
                                            </div>
                                        </div>
                                        <div class="list-group nested-sortable">
                                            '.$this->getDataOption($value,$pid,$value,$questionArray).'
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
    function generateIsParentQuestionCustom($field,$questiondt,$after=0,$parentdeata=array(),$questionArray=array())
    {
        if(!$questiondt){
            return ;
        } else {

            $html = "";
            global $go_ncadb;
            $sql        = "SELECT * FROM tb_questiondt AS QDT LEFT JOIN tb_question AS Q ON (Q.question=questiondt_question) WHERE QDT.".$field." = '".$questiondt."' AND QDT.questiondt_active = '1' ";

            if($after > 0){
                $sql .= " AND questiondt_after = '".$after."'";
            }

            $dataOption = $go_ncadb->ncaretrieve($sql, "question");
            $data       = $dataOption;
            
            $sqlactivities = "SELECT * FROM tb_activities WHERE activities_active = 1";
            $arractivities = $go_ncadb->ncaretrieve($sqlactivities, "question");
            $arr_activities  = $arractivities;

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
                    $html .= ' 
                                <div class="bgcontentcolor col-md-6 content'.$value['questiondt'].'" id="'.$pid.'">
                                    <input type="hidden" name="mainname[]" value="'.$pid.'" />
                                    <input type="hidden" name="oldquestion[]" value="'.$value['questiondt'].'" />
                                    <div class="list-group-item nested-3 question mb-4" id="'.$value['questiondt'].'" data-id="'.$value['questiondt'].'">
                                        
                                        <!-- Hidden Zone -->
                                            <input type="hidden" name="question[]" value="'.$value['question'].'" />
                                            <input type="hidden" name="questionparent['.$value['question'].'][]" value="'.$value['questiondt'].'" />
                                            <input type="hidden" name="questiondt['.$value['questiondt'].']" value="'.$value['questiondt'].'" />
                                            <input type="hidden" name="questionid[]" value="'.$value['questiondt'].'" />
                                            <input type="hidden" name="questionname[]" value="'.$value['questiondt'].'" />
                                            <input type="hidden" name="datainputtype[]" value="'.$value['questiondt_questiontype'].'" />
                                            <input type="hidden" name="questionnameinput['.$value['questiondt'].']" value="'.$inputTypeName['questiontype_type'].'" />
                                            <input type="hidden" name="questionismain['.$value['questiondt'].']" value="'.($value['questiondt_parent'] > 0 ? '' : '1').'" />
                                            <input type="hidden" name="questionismainname['.$value['question'].']['.$value['questiondt'].']" value="'.$parentdeata['questiondt'].'" /> 
                                            <input type="hidden" name="questionnameinputafter['.$value['questiondt'].']" value="'.$parentdeata['questionoption_questiondt'].'" />
                                            <input type="hidden" name="questionnameinfrom['.$mainfrom.']" value="'.$mainfrom.'" />
                                            <!--<input type="hidden" name="questionactivities['.$value['questiondt'].']" value="'.$value['questiondt_activities'].'" />-->
                                            <input type="hidden" name="questionnameinputparent['.$parentdeata['questiondt'].']" value="'.$parentdeata['questiondt'].'" />
                                            <input type="hidden" name="questionnameinputafteroptoion['.$value['questiondt'].']" value="'.($value['questiondt_after'] ? 'option'.$parentdeata['questionoption_questiondt'].($parentdeata['questionoption_order'] - 1) : '' ).'" />
                                            <input type="hidden" id="questiondtdeleted_'.$value['questiondt'].'" name="questiondtdeleted['.$value['questiondt'].']" value="" />
                                        <!-- Hidden Zone -->

                                        

                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                คำถาม : <input class="form-control" type="text" name="questiontext['.$value['questiondt'].']" required="" value="'.$value['questiondt_title'].'" />
                                            </div>
                                        </div>
                                        <div class="list-group nested-sortable">
                                            '.$this->getDataOptionCustom($value,$pid,$value,$questionArray).'
                                        </div>
                                        <div class="form-group row">
                                            <div class="col-lg-12">
                                                <span class="btn btn-danger mt-3" id="delQuestion'.$value['questiondt'].'" style="/*position: absolute;*/ right: 8px; float: right;" onclick="deleteQuestionoption(`'.$value['questiondt'].'`,`'.$mainfrom.'`,`questionquestion'.$mainfrom.'`,`1`);">
                                                    ลบ
                                                </span>
                                            </div>
                                        </div>
                                    </div>';
                        $html .= '</div>
                    ';

                    if(count($data) == ($key +1) && count($data) > 0 && $parentdeata){
                        $html .= '<div class="col-lg-12"><span class="btn btn-primary mt-3" onclick="setQuestionmodal(`question`,`after`,`content'.$value['questiondt'].'`,`'.$questiondt.'`,`option'.$questiondt.($after-1).'` );"><i class="bi bi-file-plus"></i> เพิ่มคำถาม</span></div>';
                    }

                    $html .= '<input type="hidden" name="allquestionName[]" id="allquestionName_'.$value['questiondt'].'" value="'.$value['questiondt'].'" />';
                    $html .= '';
                    
                }
                       
            }

            return $html;

        }

    }


    function getDataOption($question,$pid,$dataParent,$questionArray)
    {
        global $go_ncadb;

        $sql        = "SELECT * FROM tb_questionoption WHERE questionoption_questiondt = '".$question['questiondt']."' ORDER BY questionoption_order ASC";
        $dataOption = $go_ncadb->ncaretrieve($sql, "question");
        $data       = $dataOption;

        $sqlOptionType  = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
        $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");
        $arr_OptionType = $arr_OptionType;
        $arrOptionType  = array();
        
        foreach ($arr_OptionType as $key => $value) {
            $arrOptionType[$value['questiontype']] = $value;
        }
        
        $html = "";
        $inputTypeName = $this->getInpustType("questiontype",$dataParent['questiondt_questiontype']);
        foreach ($data as $key => $value) {
            $order      = ($key + 1);
            $sql_parent = "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."' AND questiondt_active = 1";
            $dp         = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP      = $dp;
        
            $sqlmistakelevele  = "SELECT *  FROM tb_mistakelevel WHERE mistakelevel_active = '1'";
            $arr_mistakelevele = $go_ncadb->ncaretrieve($sqlmistakelevele, "question");
            $arrmistakelevele = $arr_mistakelevele;
            $htmlmistakelevele = '<option value="0">เลือกน้ำหนักความผิด</option>';
            if(count($arrmistakelevele) > 0){
                foreach ($arrmistakelevele as $keymis => $valuemis) {
                    $selected = "";
                    if($valuemis['mistakelevel_active'] == 1){
                        if($valuemis['mistakelevel'] == $value['questionoption_mistakelevel'] ){
                            $selected = "selected";
                        }
                        $htmlmistakelevele .= '<option value="'.$valuemis['mistakelevel'].'" '.$selected.'>'.$valuemis['mistakelevel_name'].' ('.$valuemis['mistakelevel_value'].') </option>';
                    }
                }
            }

            
            $mistakedisplay = " display: inline;";
            if($question['question_questionmode'] == 2){
                if($value['questionoption_value'] == 0){
                    $mistakedisplay = " display: inline;";
                }else{
                    $mistakedisplay = " display: none;";
                }
            }else{
                $mistakedisplay = " display: none;";
            }

            $html .= '  <div class="list-group-item nested-2 answer border-none ms-4" data-id="question'.$value['questionoption_questiondt'].$key.'" style >
                            <input type="hidden" name="questionoption_questiondt_'.$value['questionoption_questiondt'].'" value="'.($copy > 0 ? "" : $value['questionoption_questiondt']).'" />
                            <input type="hidden" name="'.$value['questionoption_questiondt'].$key.'" value="'.$inputTypeName['questiontype_type'].'" />

                            '.$inputTypeName['questiontype_name']." : ".$value['questionoption_order'].' 
                            <input class="form-control-40" type="text" name="option'.$value['questionoption_questiondt'].'[]" id="option'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_name'].'" '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'/>

                            คะเเนน : <input class="form-control-custom col-lg-1 changevaluemistake" type="number" name="optionvalue'.$value['questionoption_questiondt'].'[]" id="optionvalue'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_value'].'"  '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').' min="0" >

                            <div id="questionoption_mistakelevel'.$value['questionoption_questiondt'].$key.'" style="'. $mistakedisplay .'">
                                <select class="form-select-40" id="questionoption_mistakelevel_'.$value['questionoption_questiondt'].$key.'" name="questionoption_mistakelevel['.$value['questionoption_questiondt'].']['.$key.']" >
                                    '.$htmlmistakelevele.'
                                </select>
                            </div>

                            <label class="form-check-label" for="questionoption_images_'.$value['questionoption_questiondt'].$key.'">
                                ต้องการให้เเนบรูปหรือไม่ : 
                            </label>

                            <input class="form-check-input" type="checkbox" style="vertical-align: middle;" value="1" id="questionoption_images_'.$value['questionoption'].'" name="questionoption_images['.$value['questionoption_questiondt'].']['.$key.']" '.($value['questionoption_images'] == 1 ?  'checked=checked' : '' ).'>

                            <input type="hidden" name="optionid'.$value['questionoption_questiondt'].'[]" id="optionid'.$value['questionoption_questiondt'].'" value="'.$value['questionoption'].'" >
                            <div class="list-group-item nested-3 '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-5 mt-3 mb-3" data-id="'.$pid.'" >
                                '.$this->generateIsParentQuestion("questiondt_parent",$dataParent['questiondt'],$order,$value,$questionArray).'
                            </div>'


                            .(
                                $question['question_questionmode'] != 2 ?
                                (
                                    count($dataP) > 0 
                                    ? '<span class="btn btn-primary ms-3 hide" id="addquestionquestion'.$value['questionoption_questiondt'].$key.'" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questionoption_questiondt'].$key.'`,`'.$value['questionoption_questiondt'].'` ,`option'.$dataParent['questiondt'].$key.'`);" >
                                    สร้างคำถาม</span>' 
                                    : '<span class="btn btn-primary ms-3" id="addquestionquestion'.$value['questionoption_questiondt'].$key.'" style onclick="setQuestionmodal(`question`,`af`,`questionquestion'.$value['questionoption_questiondt'].$key.'`,`'.$value['questionoption_questiondt'].'` ,`option'.$dataParent['questiondt'].$key.'`);" >
                                        สร้างคำถาม</span>
                                    ' 
                                )
                                : ""
                            ).'
                        </div>
                    ';
        }
       
        return $html;
        
    }
    
    function getDataOptionCustom($question,$pid,$dataParent,$questionArray)
    {
        global $go_ncadb;

        $sql        = "SELECT * FROM tb_questionoption WHERE questionoption_questiondt = '".$question['questiondt']."' ORDER BY questionoption_order ASC";
        $dataOption = $go_ncadb->ncaretrieve($sql, "question");
        $data       = $dataOption;

        $sqlOptionType  = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
        $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");
        $arr_OptionType = $arr_OptionType;
        $arrOptionType  = array();
        
        foreach ($arr_OptionType as $key => $value) {
            $arrOptionType[$value['questiontype']] = $value;
        }
        
        $html = "";
        $inputTypeName = $this->getInpustType("questiontype",$dataParent['questiondt_questiontype']);
        foreach ($data as $key => $value) {
            $order      = ($key + 1);
            $sql_parent = "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."' AND questiondt_active = 1";
            $dp         = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP      = $dp;
        
            $sqlmistakelevele  = "SELECT *  FROM tb_mistakelevel WHERE mistakelevel_active = '1'";
            $arr_mistakelevele = $go_ncadb->ncaretrieve($sqlmistakelevele, "question");
            $arrmistakelevele = $arr_mistakelevele;
            $htmlmistakelevele = '<option value="0">เลือกน้ำหนักความผิด</option>';
            if(count($arrmistakelevele) > 0){
                foreach ($arrmistakelevele as $keymis => $valuemis) {
                    $selected = "";
                    if($valuemis['mistakelevel_active'] == 1){
                        if($valuemis['mistakelevel'] == $value['questionoption_mistakelevel'] ){
                            $selected = "selected";
                        }
                        $htmlmistakelevele .= '<option value="'.$valuemis['mistakelevel'].'" '.$selected.'>'.$valuemis['mistakelevel_name'].' ('.$valuemis['mistakelevel_value'].') </option>';
                    }
                }
            }

            
            $mistakedisplay = " display: inline;";
            if($question['question_questionmode'] == 2){
                if($value['questionoption_value'] == 0){
                    $mistakedisplay = " display: inline;";
                }else{
                    $mistakedisplay = " display: none;";
                }
            }else{
                $mistakedisplay = " display: none;";
            }

            $html .= '  <div class="nested-2 answer border-none" data-id="question'.$value['questionoption_questiondt'].$key.'" style >
                            <input type="hidden" name="questionoption_questiondt_'.$value['questionoption_questiondt'].'" value="'.($copy > 0 ? "" : $value['questionoption_questiondt']).'" />
                            <input type="hidden" name="'.$value['questionoption_questiondt'].$key.'" value="'.$inputTypeName['questiontype_type'].'" />

                            '.$inputTypeName['questiontype_name']." : ".$value['questionoption_order'].' 
                            <input class="form-control" type="text" name="option'.$value['questionoption_questiondt'].'[]" id="option'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_name'].'" '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'/>

                           
                        </div>
                    ';
        }
       
        return $html;
        
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
        $arr_OptionType = $arr_OptionType;
        
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
                $sqlObj[$ii++] = new TField("questioncategories_name", $value, "string");
            }
            $sqlObj[$ii++] = new TField("questioncategories_hidden", ($post['mtypename'][$key] > 0 ? "1" : "0"), "string");
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
            $generateMtype = $this->generateMtype($post['groupcurrent'],$post['mtstaffcompfunc']);
        }
        $data['html'] = $generateMtype['html'];
    

        return $data;

    }
    function updategroup($post){

        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');

        $data = array();
        $data['success'] = 0;
        $data['fail'] = 0;
        $data['html'] = "";

        foreach($post['editmnamechange'] as $key => $value){

            $sqlInsertMquestiontype = new SqlBuilder();
            $sqlInsertMquestiontype->SetTableName("tb_questiongroup");
            $ii = 0;
            $sqlObj = null;


            $sqlObj[$ii++] = new TField("questiongroup_hidden", ($post['groupenamecheck'][$key] > 0 ? "1" : "0"), "string");
            $sqlObj[$ii++] = new TField("questiongroup_modispid", $_SESSION['userData']['stf'], "string");
            $sqlObj[$ii++] = new TField("questiongroup_modidatetime", $datetime, "string");

            $sqlInsertMquestiontype->SetField($sqlObj);
            $sqlInsertMquestiontype->SetWhereClause(" questiongroup = '".$key."'");
            $queryMquestiontype = $sqlInsertMquestiontype->UpdateSql();
            
            if ($go_ncadb->ncaexec($queryMquestiontype, "question")) {
                $data['success'] = 1;
            } else {
                $go_ncadb->ncarollback("question");
                $data['fail'] = 1;
                $data['sql'][] = $queryMquestiontype;
                
                return $data;
            }
        }

        if($data['success'] == 1){

            $generateMtype = $this->generatequestiongroup($post['groupcurrentcate'],$post['groupcurrent']);
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
        $arrmquestiontype  = $arr_mquestiontype;
        $data['array'] = $arrmquestiontype;

        $html  = '<select class="form-select" name="mquestiontype" id="mquestiontype" onchange="changemquestiontype()">';
        $html .= '<option value="0">เลือกหมวด</option>';
        
        if(count($arrmquestiontype) > 0){
            foreach ($arrmquestiontype as $key => $value) {
                $selected = "";
                if($value['questioncategories_active'] == 1 && $value['questioncategories_hidden'] == 0){
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
                    if($mvalue['questioncategories_hidden'] == 1){
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
    function generatequestiongroup($cateid,$currentid){

        global $go_ncadb;
        $datetime = date('Y-m-d H:i:s');
        $data = array();
        $data['html'] = "";
        $data['htmlmodal'] = "";

        $sqlquestiongroup  = "SELECT * FROM tb_questiongroup WHERE questiongroup_questioncategories = '".$cateid."' AND questiongroup_active = 1 ";
        $arr_questiongroup = $go_ncadb->ncaretrieve($sqlquestiongroup, "question");
        $arrquestiongroup = $arr_questiongroup;

        $html  = '<select class="form-select" name="questiongroup" id="questiongroup" required="">';
        $html .= '<option value="0">เลือกกลุ่ม</option>';
        
        if(count($arrquestiongroup) > 0){
            foreach ($arrquestiongroup as $keyy => $valuee) {
                $selected = "";
                if($valuee['questiongroup_active'] == 1 && $valuee['questiongroup_hidden'] == 0){
                    if($valuee['questiongroup'] == $currentid){
                        $selected = "selected";
                    }
                    $html .= '<option value="'.$valuee['questiongroup'].'" '.$selected.'> '.$valuee['questiongroup_name'].' </option>';
                }
            }
        }

        $html .= '</select>';
        
        $data['html'] = $html;

        $htmlmodal = "";
        if(count($arrquestiongroup) > 0){
            foreach ($arrquestiongroup as $key => $value) {
                if($value['questiongroup_active'] == 1){
                    $isChecked = "";
                    if($value['questiongroup_hidden'] == 1){
                        $isChecked = "checked";
                    }
                    $htmlmodal .= ' <tr>
                                        <td>
                                        <span id="editmname_'.$value['questiongroup'].'" > '.$value['questiongroup_name'].'
                                        </span>
                                        <span id="inputmname_'.$value['questiongroup'].'" style="display: none;">
                                            <input type="text" id="mname_'.$value['questiongroup'].'" name="mname['.$value['questiongroup'].']" class="form-control" required="" value="'.$value['questiongroup_name'].'" style="width:88%; float:left;">
                                            <button onclick="editmname(`editmname_'.$value['questiongroup'].'`,`inputmname_'.$value['questiongroup'].'`,`cancel_'.$value['questiongroup'].'`,`'.$value['questiongroup'].'`)" type="button" class="btn btn-danger" style="float: right;">ยกเลิก</button>
                                        </span>
                                        <input type="hidden" name="editmnamechange['.$value['questiongroup'].']" id="editmnamechange_'.$value['questiongroup'].'" value="">
                                        </td>value
                                        <td align="center" style="vertical-align: middle;">
                                        <input class="form-check-input" type="checkbox" value="1" name="groupenamecheck['.$value['questiongroup'].']" id="mtypename5" '.$isChecked.'>
                                        </td>
                                    </tr>';
                }
            }
        }
        
        $data['sql'] = $sqlquestiongroup;
        $data['htmlmodal'] = $htmlmodal;

        return $data;

    }

    function curlPostNcaData($url, $data){  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function curlGetNcaData($url)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

    function getCompfuncData(){ // สายงาน
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getcompfunc";
        $exc = json_decode($this->curlGetNcaData($endpoint),true);
        return $exc;
    }

    function getDepartmentData($par_compfuncid=""){ // ฝ่าย
        if($par_compfuncid){
            $compfuncid = "&par_compfuncid=".$par_compfuncid;
        }
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getdepartment$compfuncid";
        $exc = json_decode($this->curlGetNcaData($endpoint),true);
        return $exc;
    }

    function getSectionData($par_departmentid=""){ // แผนก
        if($par_departmentid){
            $departmentid = "&par_departmentid=".$par_departmentid;
        }
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getsection$departmentid";
        $exc = json_decode($this->curlGetNcaData($endpoint),true);
        return $exc;
    }

    function getEmp($par_func,$par_dep,$par_sec){ // พนักงานในแผนกนั้น
        $par_ram = "?method=getemp&par_func=".$par_func."&par_dep=$par_dep"."&par_sec=$par_sec";
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php".$par_ram;
        $exc = json_decode($this->curlGetNcaData($endpoint),true);
        return $exc;
    }

    function getoutlet(){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getoutlet";
        $exc = json_decode($this->curlGetNcaData($endpoint),true);
        return $exc;
    }

    function getEmpCode($par_empcode){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
        $exc = json_decode($this->curlGetNcaData($endpoint),true);
        return $exc;
    }

    function getSectionById($id){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getsection&par_departmentid=";
        $exc = curlGetNca($endpoint);
        $sec_list = json_decode($exc, true);
        foreach ($sec_list["data"] as $key => $value) {
            if($value["section_id"] == $id){
                return $value;
            }
        }
    }

    function checkUserLogin($user,$pass){
        $endpoint = "http://61.91.248.21/nca_project/app_webservice/ncadriverdidntsleep.checkusr.json.php";
        $data = array('username' => $user, 'password' => $pass);
        $exc = json_decode($this->curlPostNcaData($endpoint,$data),true);
        return $exc;
    }

    function getDataQuestionidByCategory($category){

        global $go_ncadb;
        $_SESSION['userData']['staffcompfunc'];
        $sql_data = "SELECT Q.question, Q.question_name, QC.questioncategories_name FROM tb_question AS Q  LEFT JOIN tb_questioncategories AS QC ON (QC.questioncategories=Q.question_questioncategories) WHERE Q.question_questioncategories = '".$category."' ";
        $arr = $go_ncadb->ncaretrieve($sql_data, "question");
        
        return $arr;

    }

    function dateThai($strDate,$format="",$isThaiMonths=0)
	{
        if(!$strDate){
            return "-";
        }

        $thaiMonths = array (
            '01'  => 'มกราคม',
            '02'  => 'กุมภาพันธ์',
            '03'  => 'มีนาคม',
            '04'  => 'เมษายน',
            '05'  => 'พฤษภาคม',
            '06'  => 'มิถุนายน',
            '07'  => 'กรกฎาคม',
            '08'  => 'สิงหาคม',
            '09'  => 'กันยายน',
            '10' => 'ตุลาคม',
            '11' => 'พฤศจิกายน',
            '12' => 'ธันวาคม',
        );

        $strDate  = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $strDate)));
        $rtDate = "";
        if($format != ""){
            $rtDate = date($format, strtotime(str_replace('/', '-', $strDate)));
        }else{
            $strDay   = date("j",strtotime($strDate));
            $strYear  = date("Y",strtotime($strDate))+543;
            // $strMonth = ($isThaiMonths > 1 ?  $thaiMonths[date("n",strtotime($strDate)] : date("n",strtotime($strDate) ));
            if($isThaiMonths > 0){
                $strMonth = $thaiMonths[date('m',strtotime($strDate))];
                $rtDate = $strDay." ".$strMonth." ".$strYear;
            }else{
                $strMonth = date("m",strtotime($strDate));
                $rtDate = $strDay."/".$strMonth."/".$strYear;
            }
        }

		return $rtDate;
	}


}
