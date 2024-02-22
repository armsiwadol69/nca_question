<?
class questionview
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

    function genareteViewFormData($field,$questiondt,$after=0,$parentdeata=array(),$questionArray=array())
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
                    // $html .= '  <div class="bgcontentcolor content'.$value['questiondt'].'" id="'.$pid.'">
                    //                 <input type="hidden" name="mainname[]" value="'.$pid.'" />
                    //                 <input type="hidden" name="oldquestion[]" value="'.$value['questiondt'].'" />
                    //                 <div class="list-group-item nested-3 question" id="'.$value['questiondt'].'" data-id="'.$value['questiondt'].'">
                    //                     <input type="hidden" name="questiondt['.$value['questiondt'].']" value="'.$value['questiondt'].'" />
                    //                     <input type="hidden" name="questionid[]" value="'.$value['questiondt'].'" />
                    //                     <input type="hidden" name="questionname[]" value="'.$value['questiondt'].'" />
                    //                     <input type="hidden" name="datainputtype[]" value="'.$value['questiondt_questiontype'].'" />
                    //                     <input type="hidden" name="questionnameinput['.$value['questiondt'].']" value="'.$inputTypeName['questiontype_type'].'" />
                    //                     <input type="hidden" name="questionismain['.$value['questiondt'].']" value="'.($value['questiondt_parent'] > 0 ? '' : '1').'" />
                    //                     <input type="hidden" name="questionismainname['.$value['questiondt'].']" value="'.$parentdeata['questiondt'].'" /> 
                    //                     <input type="hidden" name="questionnameinputafter['.$value['questiondt'].']" value="'.$parentdeata['questionoption_questiondt'].'" />
                    //                     <input type="hidden" name="questionnameinputparent['.$parentdeata['questiondt'].']" value="'.$parentdeata['questiondt'].'" />
                    //                     <input type="hidden" name="questionnameinputafteroptoion['.$value['questiondt'].']" value="'.($value['questiondt_after'] ? 'option'.$parentdeata['questionoption_questiondt'].($parentdeata['questionoption_order'] - 1) : '' ).'" />
                    //                     <div class="col-lg-12">
                    //                         คำถาม : <input class="form-control-50 col-lg-5" type="text" name="questiontext['.$value['questiondt'].']" required="" value="'.$value['questiondt_title'].'" />
                    //                     </div>
                    //                     <div class="list-group">
                    //                         '.$this->genareteViewOptions($value['questiondt'],$pid,$value,$questionArray).'
                    //                     </div>
                    //                 </div>';
                        // $html .='<div '.$isHidden.'>';
                        $html .= "<h4 class='answerTitle' id='questionTitle$questiondt' $isHidden>".$value['questiondt_title']."</h4>";
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
        // $isHidden = "";
        $html = "<div>";
            
        $inputTypeName = $this->getInpustType("questiontype",$dataParent['questiondt_questiontype']);
        foreach ($data as $key => $value) {
            $order = ($key + 1);
            $sql_parent= "SELECT * FROM tb_questiondt WHERE questiondt_parent = '".$dataParent['questiondt']."' AND questiondt_after = '".$order."'";
            $dp = $go_ncadb->ncaretrieve($sql_parent, "question");
            $dataP       = $this->ncaArrayConverter($dp);
            
            // $html .= '  <div class="list-group-item nested-2 answer border-none ms-5" data-id="question'.$value['questionoption_questiondt'].$key.'" style >
            //                 '.$inputTypeName['questiontype_name']." : ".$value['questionoption_order'].' 

            //                 <input type="hidden" name="questionoption_questiondt_'.$value['questionoption_questiondt'].'" value="'.$value['questionoption_questiondt'].'" />

            //                 <input type="hidden" name="'.$value['questionoption_questiondt'].$key.'" value="'.$inputTypeName['questiontype_type'].'" />
            //                 <input class="form-control-40 col-lg-4" type="text" name="option'.$value['questionoption_questiondt'].'[]" id="option'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_name'].'" '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'/>
            //                 คะเเนน : <input class="form-control-custom col-lg-1" type="number" name="optionvalue'.$value['questionoption_questiondt'].'[]" id="optionvalue'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_value'].'"  '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'>

                            
            //                 <label class="form-check-label" for="questionoption_images_'.$value['questionoption'].'">
            //                     ต้องการให้เเนบรูปหรือไม่ : 
            //                 </label>
            //                 <input class="form-check-input" type="checkbox" style="vertical-align: middle;" value="1" id="questionoption_images_'.$value['questionoption'].'" name="questionoption_images['.$value['questionoption_questiondt'].']['.$value['questionoption'].']" '.($value['questionoption_images'] == 1 ?  'checked=checked' : '' ).'>

            //                 <input type="hidden" name="optionid'.$value['questionoption_questiondt'].'[]" id="optionid'.$value['questionoption_questiondt'].'" value="'.$value['questionoption'].'" >
            //                 <div class="list-group-item nested-3 '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-3 mt-3 mb-3" data-id="'.$pid.'" >
            //                     '.$this->genareteViewFormData("questiondt_parent",$dataParent['questiondt'],$order,$value,$questionArray).'
            //                 </div>'
            //                 .'
            //             </div>';

            #OVER

            // <input type="hidden" name="optionid'.$value['questionoption_questiondt'].'[]" id="optionid'.$value['questionoption_questiondt'].'" value="'.$value['questionoption'].'" >
            // <input class="form-control-40 col-lg-4" type="text" name="option'.$value['questionoption_questiondt'].'[]" id="option'.$value['questionoption_questiondt'].$key.'" value="'.$value['questionoption_name'].'" '.($dataParent['questiondt_questiontype'] > 3 ? 'required' : 'readonly="readonly"').'/>
            // <input type="hidden" name="questionoption_questiondt_'.$value['questionoption_questiondt'].'" value="'.$value['questionoption_questiondt'].'" />
            // <input type="hidden" name="'.$value['questionoption_questiondt'].$key.'" value="'.$inputTypeName['questiontype_type'].'" />

            $html .= ' <div class="list-group-item answer border-none ms-5" data-id="question'.$value['questionoption_questiondt'].'"'.' style >'.' 
                            '.$this->createAnswerByType('optionid'.$value['questionoption_questiondt'],'optionid'.$value['questionoption_questiondt'].'',$dataParent['questiondt_questiontype'],$value['questionoption_order'],$value["questionoption_name"],$value["questionoption"]).
                            $this->createFileUploader($value['questionoption_images'],'optionid'.$value['questionoption_questiondt'],$value['questionoption_questiondt'].'',$dataParent['questiondt_questiontype'],$value['questionoption_order'],$value["questionoption_name"],$value["questionoption"]).'
                            <div class="list-group-item '.(count($dataP) > 0 ? "" : "hide" ).' questionquestion'.$value['questionoption_questiondt'].$key.' ms-3 mt-3 mb-3" data-id="'.$pid.'" >
                                '.$this->genareteViewFormData("questiondt_parent",$dataParent['questiondt'],$order,$value,$questionArray).'
                            </div>';

            $html .= "</div>";
        }
    

        return $html;
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

    function createAnswerByType($id,$name,$type, $order, $text, $dt){
        switch ($type) {
            case '1':
                return ' <input type="text" class="form-control" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="" aria-label="คำตอบ" aria-describedby="คำตอบ">';
            case '2':
                return ' <input type="number" class="form-control" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="" aria-label="คำตอบ" aria-describedby="คำตอบ">';
            case '3':
                return ' <input type="date" class="form-control" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="" aria-label="คำตอบ" aria-describedby="คำตอบ">';
            case '4':
                return ' <label  for="inputId'.$dt.'">'.'<input type="radio" class="form-check-input" id="inputId'.$dt.'" name="'.$name.'" placeholder="กรอกคำตอบ" value="'.$dt.'" aria-label="คำตอบ" aria-describedby="คำตอบ" required    >'." ".$order.".".$text."</label>";
            case '5':
                return ' <label  for="inputId'.$dt.'">'.'<input type="checkbox" class="form-check-input" id="inputId'.$dt.'" name="'.$name.'[]" placeholder="กรอกคำตอบ" value="'.$dt.'" aria-label="คำตอบ" aria-describedby="คำตอบ">'." ".$order.".".$text."</label>";
            default:
                break;
        }
    }

    function createFileUploader($isRequire ,$dt ,$name ,$type, $order, $text, $id){
        if($isRequire == "1"){
            // <label fname="fileUploadOptionId'.$name.'" class="form-label" hidden>แนบไฟล์</label>
            return '<input class="form-control form-control-sm my-2" type="file" id="fileUploadOptionId'.$id.'" name="fileUploadOption[]['.$id.']" multiple>';
        }
    }
    

}