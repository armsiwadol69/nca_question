<?
class answer extends question
{
    public $answerid;
    public $name;
    public $question = array();

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
        return $res;

    }

    function getAnswerdt() {
        global $go_ncadb;
        $sql = "SELECT 
                    * 
                FROM tb_answerdt AD
                    LEFT JOIN tb_questiondt AS QDT ON(QDT.questiondt=AD.answerdt_questiondt)
                    LEFT JOIN tb_questionoption AS QOP ON (QOP.questionoption_questiondt=QDT.questiondt)
                WHERE 
                    answerdt_answer = '".$this->answerid."'";
        
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
            $sql        = "SELECT * FROM tb_questiondt WHERE ".$field." = '".$questiondt."' AND questiondt_active = '1' ";
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
                $sqlOptionType = "SELECT * FROM tb_questiontype WHERE questiontype_active = 1 ";
                $arr_OptionType = $go_ncadb->ncaretrieve($sqlOptionType, "question");

                $html = "";

                foreach ($data as $key => $value) {

                    array_push($questionArray,$value['questiondt']);

                    $pid  = $this->generateRandomString(15);
                    $opid = $this->generateRandomString(15);
                    $inputTypeName = $this->getInpustType("questiontype",$value['questiondt_questiontype']);
                        $html .= "<h5 class='answerTitle my-3' id='questionTitle$questiondt' $isHidden>".$value['questiondt_title']."</h5>";
                        $html .= "<div class='answerBox' id='questionBox$questiondt' $isHidden>".$this->genareteViewOptions($value['questiondt'],$pid,$value,$questionArray, "")."</div>";
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

    function genareteViewOptions($question,$pid,$dataParent,$questionArray, $isHidden)
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
        foreach ($data as $key => $value) {
            $order = ($key + 1);
            $sql_parent = "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."'";
            $dp         = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP      = $this->ncaArrayConverter($dp);

            $html .= ' <div class="list-group-item answer border-none ms-5" data-id="question'.$value['questionoption_questiondt'].'"'.' style >'.' 
                            '.$this->createAnswerByType('optionid'.$value['questionoption_questiondt'],'optionid'.$value['questionoption_questiondt'].'',$dataParent['questiondt_questiontype'],$value['questionoption_order'],$value["questionoption_name"],$value["questionoption"]).
                            $this->createFileUploader($value['questionoption_images'],'optionid'.$value['questionoption_questiondt'],$value['questionoption_questiondt'].'',$dataParent['questiondt_questiontype'],$value['questionoption_order'],$value["questionoption_name"],$value["questionoption"]).'
                            <div class="list-group-item '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-3 mt-3 mb-3" data-id="'.$pid.'" >
                                '.$this->genareteViewAnswerFormData("questiondt_parent",$dataParent['questiondt'],$order,$value,$questionArray).'
                            </div>';

            $html .= "</div>";
        }
    
        return $html;
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

    function createAnswerByType($id,$name,$type, $order, $text, $dt){
        switch ($type) {
            case '1':
                return ' <input type="text" class="form-control" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="" aria-label="คำตอบ" aria-describedby="คำตอบ" required>';
            case '2':
                return '
                            <label for="inputId'.$dt.'">จำนวนที่พบ</label>
                            <input type="number" class="form-control form-control-inline" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="0" min="0" max="69" aria-label="คำตอบ" aria-describedby="คำตอบ" required>
                            <span>ครั้ง</span>
                       ';
            case '3':
                return ' <input type="date" class="form-control" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="" aria-label="คำตอบ" aria-describedby="คำตอบ">';
            case '4':
                return ' <input type="radio" class="form-check-input" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="'.$dt.'" aria-label="คำตอบ" aria-describedby="คำตอบ" required    >'." ".$order.".".' <label for="inputId'.$dt.'">'.$text.'</label>';
            case '5':
                return ' <input type="checkbox" class="form-check-input" id="inputId'.$dt.'" name="'.$name.'[]" placeholder="กรอกคำตอบ" value="'.$dt.'" aria-label="คำตอบ" aria-describedby="คำตอบ">'." ".$order.".".' <label for="inputId'.$dt.'">'.$text.'</label>';
            default:
                break;
        }
    }

    function createFileUploader($isRequire ,$dt ,$name ,$type, $order, $text, $id){
        if($isRequire == "1"){
            return '<input class="form-control form-control-sm my-2 file-upload-option" type="file" accept="image/png, image/gif, image/jpeg" id="fileUploadOptionId'.$id.'" name="fileUploadOption[]['.$id.']" multiple hidden>';
        }
    }

}