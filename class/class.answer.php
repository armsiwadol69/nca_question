<?
class answer extends question
{
    public $answerid;
    public $name;
    public $question = array();
    public $questionData = array();

    function __construct($answerid=0,$questionid=0) {
        if($answerid){
            $this->setAnswerId($answerid);
        }
        if($questionid){
            $this->setQuestionId($questionid);
        }
    }

    function setAnswerId($id = 0) {
        $this->answerid = $id;
    }

    function getAnswerId() {
        return $this->answerid;
    }

    function getDataAnswer() {
        global $go_ncadb;

        $sql = "SELECT * FROM tb_answer WHERE answer= '".$this->answerid."'";
        $res = $go_ncadb->ncaretrieve($sql, "question");
        $this->questionData = $res[0];
        return $res;

    }

    function getAnswerdt() {
        global $go_ncadb;
        $sql = "SELECT 
                    * 
                FROM tb_answerdt AD
                    LEFT JOIN tb_questiondt AS QDT ON(QDT.questiondt=AD.answerdt_questiondt)
                    -- LEFT JOIN tb_questionoption AS QOP ON (QOP.questionoption_questiondt=QDT.questiondt)
                WHERE 
                    answerdt_answer = '".$this->answerid."' ORDER BY questiondt_order ASC";
        $res = $go_ncadb->ncaretrieve($sql, "question");
        return $res;
    }

    function genareteViewAnswerFormData($field,$questiondt,$after=0,$parentdeata=array(),$questionArray=array())
    {   
        if(!$questiondt){
            return ;
        } else {

            $html = "";
            global $go_ncadb;
            $sql        = "SELECT * FROM tb_answerdt AS ADT LEFT JOIN tb_questiondt AS QDT  ON (QDT.questiondt=ADT.answerdt_questiondt) WHERE ".$field." = '".$questiondt."' AND questiondt_active = '1' ";

            if($after > 0){
                $sql .= " AND questiondt_after = '".$after."'";
                $isHidden = "hidden";
                $isRequire = "required";
                $isDisabled = "disabled"; 
            }else{
                $isHidden = "";
                $isRequire = "";
                $isDisabled = "";
            }

            $dataOption = $go_ncadb->ncaretrieve($sql, "question");
            $data       = $this->ncaArrayConverter($dataOption);
            
            if($data){

                $html = "";

                foreach ($data as $key => $value) {

                    array_push($questionArray,$value['questiondt']);

                    $pid  = $this->generateRandomString(15);
                    // $inputTypeName = $this->getInpustType("questiontype",$value['questiondt_questiontype']);
                        $html .= "<h5 class='answerTitle my-1' id='questionTitle$questiondt' $isHidden>".$value['questiondt_title']."</h5>";
                        $html .= "<div class='answerBox' id='questionBox$questiondt' $isHidden>".$this->genareteViewOptionsAnswer($value['questiondt'],$pid,$value,$questionArray, "")."</div>";
                    // $html .='<div>';
                    $html .= '<input type="hidden" name="allquestionName[]" id="allquestionName_'.$value['questiondt'].'" value="'.$value['questiondt'].'" />';
                    $html .= '</div>';
                    
                }
                    
            }
            if($after == "0"){
                // $html .= "<hr>";
            }
            
            return $html;

        }

    }

    function genareteViewOptionsAnswer($question,$pid,$dataParent,$questionArray, $isHidden)
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

        $html = "<div>";
            
        $inputTypeName = $this->getInpustType("questiontype",$dataParent['questiondt_questiontype']);

        // echo "<pre>----------------------------****----------------1111-------------------";
        // print_r($dataParent);
        // print_r($this->questionData);
        // echo "</pre>---------------------------****----------------11111-------------------";
        // echo "---------------------------****-----------------------------------<br>";
        
        foreach ($data as $key => $value) {
            $order = ($key + 1);
            $sql_parent = "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."'";
            $dp         = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP      = $this->ncaArrayConverter($dp);

            /* echo "<pre>";
            print_r($value);
            echo "</pre>"; */
            $result ="";
            if($dataParent['answerdt_order'] == $value['questionoption_order']){

                switch ($dataParent['answerdt_questiontype']) {
                    case '1':
                        $result = $dataParent['answerdt_value'];
                        break;
                    case '2':
                        $result = $dataParent['answerdt_value'];
                        break;
                    case '3':
                        $result = $this->dateThai($dataParent['answerdt_value']);
                        // $result = $dataParent['answerdt_value'];
                        break;
                    case '4':
                        $result = "checked";
                        break;
                    case '5':
                        $result = "checked";
                        break;
                    default:
                        $result = "";
                        break;
                }
               
            }

            $offenseName = "";
            if($this->questionData['answer_questionmode'] == "2"){
                $datamistakelevel = $this->getDataMistakelevelByAnswerOption($value['questionoption']);
                if($datamistakelevel['mistakelevel'] > 0){
                    $offenseName = " <span class='text-danger'>( ความผิด : ".$datamistakelevel['mistakelevel_name']." )</span>";
                }
            }

            // echo "result ".$value['questionoption_order']." : ".$result."<br>";

            $html .= ' <div class="list-group-item answer border-none ms-2" data-id="question'.$value['questionoption_questiondt'].'"'.' style >'.' 
                            '.$this->createAnswerByType('optionid'.$value['questionoption_questiondt'],'optionid'.$value['questionoption_questiondt'].'',$dataParent['questiondt_questiontype'],$value['questionoption_order'],$value["questionoption_name"],$value["questionoption"],$result).$offenseName.
                            $this->createFileUploader($value['questionoption_images'],'optionid'.$value['questionoption_questiondt'],$value['questionoption_questiondt'].'',$dataParent['questiondt_questiontype'],$value['questionoption_order'],$value["questionoption_name"],$value["questionoption"]).'
                            <div class="list-group-item '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-3 mt-3 mb-3" data-id="'.$pid.'" >
                                '.$this->genareteViewAnswerFormData("questiondt_parent",$dataParent['questiondt'],$order,$value,$questionArray).'
                            </div>';

            $html .= "</div>";
        }

        // echo "---------------------------****-----------------------------------<br>";
    
        return $html."<hr>";
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


    function createAnswerByType($id,$name,$type, $order, $text, $dt, $result=""){
        switch ($type) {
            case '1':
                return ' <input type="text" class="form-control" id="inputId'.$dt.'" name="'.$name.'" value="'.$result.'" aria-label="คำตอบ" aria-describedby="คำตอบ" required>';
            case '2':
                return '
                            <label for="inputId'.$dt.'">จำนวนที่พบ</label>
                            <input type="number" class="form-control form-control-inline" id="inputId'.$dt.'" name="'.$name.'"  value="'.$result.'" min="0" max="69" aria-label="คำตอบ" aria-describedby="คำตอบ" required>
                            <span>ครั้ง</span>
                       ';
            case '3':
                return ' <input type="text" class="form-control" id="inputId'.$dt.'" name="'.$name.'" value="'.$result.'" aria-label="คำตอบ" aria-describedby="คำตอบ">';
            case '4':
                return ' <input type="radio" class="form-check-input" id="inputId'.$dt.'" name="'.$name.'" aria-label="คำตอบ" aria-describedby="คำตอบ" required '.$result.' disable>'." ".$order.".".' <label for="inputId'.$dt.'">'.$text.'</label>';
            case '5':
                return ' <input type="checkbox" class="form-check-input" id="inputId'.$dt.'" name="'.$name.'[]"  value="'.$dt.'" aria-label="คำตอบ" aria-describedby="คำตอบ" '.$result.' disable>'." ".$order.".".' <label for="inputId'.$dt.'">'.$text.'</label>';
            default:
                break;
        }
    }

    function createFileUploader($isRequire ,$dt ,$name ,$type, $order, $text, $id){
        if($isRequire == "1"){
            return '<input class="form-control form-control-sm my-2 file-upload-option" type="file" accept="image/png, image/gif, image/jpeg" id="fileUploadOptionId'.$id.'" name="fileUploadOption[]['.$id.']" multiple hidden>';
        }
    }

    function getDataMistakelevelByAnswerOption($answerdt_optionid){
        global $go_ncadb;
        $sql = "SELECT ML.*
                FROM tb_questionoption AS QOP 
                    LEFT JOIN tb_mistakelevel AS ML ON (ML.mistakelevel = QOP.questionoption_mistakelevel)
                WHERE QOP.questionoption = '".$answerdt_optionid."' AND QOP.questionoption_mistakelevel > 0";
        // echo $sql;
        $result = $go_ncadb->ncaretrieve($sql, "question");
        return $result[0];
    }

}