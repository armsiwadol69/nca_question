<?php
    header('Content-Type: application/json; charset=utf-8');
    date_default_timezone_set("Asia/Bangkok");
    $gb_notlogin = true;

    require_once "../include.inc.php";

    if (is_array($_GET)) {
        foreach ($_GET as $k => $v) {
            $ar_prm[$k] = $v;
        }
    }
    if (is_array($_POST)) {
        foreach ($_POST as $k => $v) {
            $ar_prm[$k] = $v;
        }
    }

    $go_ncadb = new ncadb();

    $reportGenerator = new ReportGenerator();

    $method = $ar_prm["method"];

    switch ($method) {
        case 'getReport':
            echo $reportGenerator->getReport($ar_prm["type"],$ar_prm["ref"],$ar_prm["startDate"],$ar_prm["endDate"]);
            break;
        default:
            echo json_encode(array("resCode" => "99", "resMsg" => "No Method was specified"));
            break;
    }

    class ReportGenerator {

        public function getReport($type, $ref, $startDate, $endDate) {

            global $go_ncadb;

            $referance = array();

            $str_search = "";

            $refObj = array();

            if($type == "1"){
                //Staff
                $staffInfo = $this->getEmpCode($ref);

                $ar_staff = json_decode($staffInfo, true);

                $refObj = $ar_staff["data"][0];

                $str_search = $ar_staff["data"][0]["empicms_id"];

            }else if($type == "2"){
                //Outlet

                $str_search = $ref;
                
            }else if($type == "3"){

                $str_search = $ref;

            }

            if(!$str_search){

                $ar_rtn = array(
                    "resCode" => "0",
                    "resMsg" => "Staff, Outlet or bus data not found",
                );

                return json_encode($ar_rtn);
            }

            $mainSql = "";

            if($type == "1"){

                $mainSql = "SELECT
                            answer
                            FROM
                            tb_answer
                            WHERE
                            answer_type = '$type' AND
                            answer_staff = '$str_search' AND
                            answer_recdate BETWEEN '$startDate' AND '$endDate' AND
                            answer_active = '1'";

            }else if($type =="2"){

                $mainSql = "SELECT
                            answer
                            FROM
                            tb_answer
                            WHERE
                            answer_type = '$type' AND
                            answer_outlet = '$str_search' AND
                            answer_recdate BETWEEN '$startDate' AND '$endDate' AND
                            answer_active = '1'";

            }else if($type =="3"){

                $mainSql = "SELECT
                answer
                FROM
                tb_answer
                WHERE
                answer_type = '$type' AND
                answer_busref = '$str_search' AND
                answer_recdate BETWEEN '$startDate' AND '$endDate' AND
                answer_active = '1'";

            }

            $excAnswer = $go_ncadb->ncaretrieve($mainSql, "question");

            $answerWithKey = $this->ncaArrayConverter($excAnswer);

            if(!$answerWithKey){
                $ar_rtn = array(
                    "resCode" => "1",
                    "resMsg" => "this ref has no record",
                    "ref" => $refObj,
                    "type" => $type,
                    "point" => 0,
                    "data" => array(),
                    "allmistake" => array()
                );

                return json_encode($ar_rtn);

            }

            $ar_answer = array();

            foreach ($answerWithKey as $kk => $vv) {
                $ar_answer[$kk] = $vv["answer"];
            }

            $sqlIn = $this->arrayToSqlInClause($ar_answer);    
            
            $sqlAnswerDt = "SELECT
                            tb_answerdt.*,
                            tb_question.question_name,
                            tb_questiondt.questiondt,
                            tb_questiondt.questiondt_title,
                            tb_questionoption.questionoption_name,
                            tb_questionoption.questionoption_mistakelevel,
                            tb_mistakelevel.mistakelevel_shortname,
                            tb_mistakelevel.mistakelevel_name,
                            tb_mistakelevel.mistakelevel_value AS 'weight'
                            FROM
                            tb_answerdt 
                            LEFT JOIN tb_question ON tb_answerdt.answerdt_question = tb_question.question
                            LEFT JOIN tb_questiondt ON tb_answerdt.answerdt_questiondt = tb_questiondt.questiondt
                            LEFT JOIN tb_questionoption ON tb_answerdt.answerdt_optionid = tb_questionoption.questionoption
                            LEFT JOIN tb_mistakelevel ON tb_questionoption.questionoption_mistakelevel = tb_mistakelevel.mistakelevel                            
                            WHERE
                            answerdt_answer $sqlIn
                            AND answerdt_value = '0' 
                            ORDER BY
                            answerdt DESC";
            
            $excAnswerDt = $go_ncadb->ncaretrieve($sqlAnswerDt, "question");

            $answerDtWithKey = $this->ncaArrayConverter($excAnswerDt);

            $result = array();

            $grouped = array();

            $totalPoint = 0;
            
            foreach ($answerDtWithKey as $item) {

                $questiondt = $item['questiondt'];
            
                if (!isset($grouped[$questiondt])) {
                    $grouped[$questiondt] = array(
                        "question_name" => $item["question_name"],
                        "questiondt" => $item["questiondt"],
                        "questiondt_title" => $item["questiondt_title"],
                        "questionoption_name" => $item["questionoption_name"],
                        "questionoption_mistakelevel" => $item["questionoption_mistakelevel"],
                        "mistakelevel_shortname" => $item["mistakelevel_shortname"],
                        "mistakelevel_name" => $item["mistakelevel_name"],
                        "weight" => $item["weight"],
                        "count" => 0
                    );
                }
                
                $totalPoint += $item["weight"];

                $grouped[$questiondt]['count']++;

            }
            
            $result = array_values($grouped);

            //To be use in usort
            function compare_by_count($a, $b) {
                if ($a['count'] == $b['count']) {
                    return 0;
                }
                return ($a['count'] < $b['count']) ? 1 : -1;
            }
            
            usort($result, 'compare_by_count');

            if(empty($result)){
                $ar_rtn = array(
                    "resCode" => "2",
                    "resMsg" => "Record is clean",
                    "ref" => $refObj,
                    "type" => $type,
                    "point" => $totalPoint,
                    "data" => $result,
                    "allmistake" => $answerDtWithKey
                );
            }else{
                $ar_rtn = array(
                    "resCode" => "1",
                    "resMsg" => "Successfully",
                    "ref" => $refObj,
                    "type" => $type,
                    "point" => $totalPoint,
                    "data" => $result,
                    "allmistake" => $answerDtWithKey
                );
            }
            
            return json_encode($ar_rtn);

        }


        private function getEmp($par_func,$par_dep,$par_sec){ // แผนก
            $par_ram = "?method=getemp&par_func=".$par_func."&par_dep=$par_dep"."&par_sec=$par_sec";
            $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php".$par_ram;
            $exc = $this->curlGetNca($endpoint);
            return $exc;
        }
    
        private function getEmpCode($par_empcode){
            $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
            $exc = $this->curlGetNca($endpoint);
            return $exc;
        }
    
        private function getEmpCodeEX($par_empcode){
            $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
            $exc = $this->curlGetNca($endpoint);
            return $exc;
        }
    
        private function curlPostNca($url, $data){  
            //echo var_dump($data);
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            //$data = json_encode($data);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }
    
        private function curlGetNca($url)
        {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        }

        public function ncaArrayConverter($par_array){
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

                    $xx[$k] = $v;
                }
                $ar[$key] = $xx;
            }
            return $ar;
        }

        function arrayToSqlInClause($array) {

            $escapedValues = array();
        
            foreach ($array as $value) {
                $escapedValues[] = "'" . addslashes($value) . "'";
            }
        
            return "IN (" . implode(", ", $escapedValues) . ")";
        }
       
    }