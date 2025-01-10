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
}