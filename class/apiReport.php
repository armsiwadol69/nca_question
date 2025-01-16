<?php
    header('Content-Type: text/html; charset=utf-8');
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

            if($type == "1"){
                //Staff
                $staffInfo = $this->getEmpCode($ref);

                $ar_staff = json_decode($staffInfo, true);

                $str_search = $ar_staff["data"][0]["empicms_id"];

            }else if($type == "2"){
                //Outlet
                
            }else if($type == "3"){
                //Bus

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
                            answer_recdatetime BETWEEN '$startDate' AND '$endDate' AND
                            answer_active = '1'";
            }

            echo $mainSql;
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
       
    }