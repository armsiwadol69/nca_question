<?
class curlManageData
{
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

    function getOutletByType($type = ""){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getoutlet";
        $exc = $this->curlGetNcaData($endpoint);
        $data = json_decode($exc, true);
        // print_r($data["data"]);
        $ar_rtn = array();
        foreach ($data["data"] as $key => $value) {
            if($value["outlet_typevar"] == $type){
                $ar_rtn[] = $value;
            }else if(empty($type)){
                $ar_rtn[] = $value;
            }
        }
        return json_encode($ar_rtn);
    }

    function getEmpCode($par_empcode){
        $endpoint = "http://61.91.248.20/ncaprj/nca_project/nca_project/leave/api/ncaorg.inc.service.php?method=getempcode&par_textsearch=$par_empcode";
        $exc = curlGetNca($endpoint);
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

    function getBusAllBusNumber(){

        $go_ncadb  = new ncadb();

        $sql = "SELECT
                busrecord.*
                FROM
                busrecord
                LEFT JOIN
                bus
                ON 
                bus.bus = busrecord.busrecord_bus
                WHERE
                busrecord.busrecord_status = 1 AND
                busrecord.busrecord_active = 1 AND
                bus.bus_active = 1
                GROUP BY busrecord.busrecord_number";

        $exc = $go_ncadb->ncaretrieve($sql, "icms");

        $excReformat = $this->TisToUtfArrayConverter($exc);

        $rtn_ar = array();

        foreach ($excReformat as $kk => $vv) {
            $rtn_ar[] = array(
                "busnumber" => $vv["busrecord_number"],
                "busrecord" => $vv["busrecord"],
            );
        }

        
        return $rtn_ar;
    }

        function TisToUtfArrayConverter($par_array)
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
}