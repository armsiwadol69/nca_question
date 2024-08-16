<?php
require_once '../class/class.question.php';
class NcaStatReportGenerator {

    function __construct($id=0) {

    }

    function generateSummaryReport($startDate, $endDate) {
        
    global $go_ncadb;
    $sql = "SELECT
            SADT.statanswerdt_questiondt,
            SADT.statanswerdt_question,
            Q.question_compfuncdepsec AS 'depsec',
            QDT.questiondt_title AS 'title',
            SUM( SADT.statanswerdt_count ) AS 'COUNT' 
            FROM tb_statanswerdt AS SADT
            LEFT JOIN tb_questiondt AS QDT ON QDT.questiondt = SADT.statanswerdt_questiondt
            LEFT JOIN tb_question AS Q ON Q.question = SADT.statanswerdt_question 
            WHERE
            SADT.statanswerdt_date BETWEEN '$startDate' AND '$endDate'
            GROUP BY
            SADT.statanswerdt_questiondt,
            SADT.statanswerdt_question
            ORDER BY COUNT DESC;";

        $arr = $go_ncadb->ncaretrieve($sql, "question");

        return json_encode($this->ArrayKeyRemover($arr));
    }

    function generateDepSecReport($depSec, $startDate, $endDate) {
        #a.k.a Department
        
        global $go_ncadb;
        $sql = "SELECT
                SADT.statanswerdt_questiondt,
                SADT.statanswerdt_question,
                Q.question_compfuncdepsec AS 'depsec',
                QDT.questiondt_title AS 'title',
                SUM( SADT.statanswerdt_count ) AS 'COUNT' 
                FROM tb_statanswerdt AS SADT
                LEFT JOIN tb_questiondt AS QDT ON QDT.questiondt = SADT.statanswerdt_questiondt
                LEFT JOIN tb_question AS Q ON Q.question = SADT.statanswerdt_question 
                WHERE
                Q.question_compfuncdepsec = '$depSec'
                AND
                SADT.statanswerdt_date BETWEEN '$startDate' AND '$endDate'
                GROUP BY
                SADT.statanswerdt_questiondt,
                SADT.statanswerdt_question
                ORDER BY COUNT DESC;";
    
            $arr = $go_ncadb->ncaretrieve($sql, "question");
    
         

            $cl_question = new question();

            $sectionList = $cl_question->getSectionData();
            $ar_department = $sectionList["data"];


            $result = $this->ArrayKeyRemover($arr);

            for ($i=0; $i < count($result); $i++) { 
               foreach ($ar_department as $key => $value) {
                    if($value["section_id"] ==  $result[$i]["depsec"]){
                        $result[$i]["depsec_name"] = $value["section_name"];
                    }
               }
            }
        


            return json_encode($result);
    }

    function generateTopStatOfDepSecReport($startDate, $endDate) {
        global $go_ncadb;
        $sql = "
                SELECT
                SADT.statanswerdt_questiondt,
                SADT.statanswerdt_question,
                Q.question_compfuncdepsec AS depsec,
                QDT.questiondt_title AS title,
                SUM(SADT.statanswerdt_count) AS COUNT
                FROM
                    tb_statanswerdt AS SADT
                    LEFT JOIN tb_questiondt AS QDT ON QDT.questiondt = SADT.statanswerdt_questiondt
                    LEFT JOIN tb_question AS Q ON Q.question = SADT.statanswerdt_question
                WHERE
                    statanswerdt_date BETWEEN '$startDate' AND '$endDate'
                GROUP BY
                    SADT.statanswerdt_questiondt,
                    SADT.statanswerdt_question,
                    Q.question_compfuncdepsec,
                    QDT.questiondt_title
                ORDER BY COUNT DESC;
        ";

        $arr = $go_ncadb->ncaretrieve($sql, "question");
    
        $results = $this->ArrayKeyRemover($arr);


        // Process results to group by depsec
        $groupedResults = array();

        $cl_question = new question();

        $sectionList = $cl_question->getSectionData();
        $ar_department = $sectionList["data"];

       

        foreach ($results as $row) {
            $depsec = $row['depsec'];
            unset($row['depsec']);

            foreach ($ar_department  as $rk => $vk) {
                if($vk["section_id"] == $depsec){
                     $funcId = $vk["section_funcid"];
                     $depId = $vk["section_depid"];
                     $sectionName = $vk["section_name"];
                     break;
                }
            }
            
            if (!isset($groupedResults[$depsec])) {
                $groupedResults[$depsec] = array(
                    'section_id' => $depsec,
                    'section_funcid' => $funcId,
                    'section_depid' => $depId,
                    'section_name' => $sectionName,
                    'most_happening' => array()
                );
            }
            
            $groupedResults[$depsec]['most_happening'][] = $row;
            
        }

        foreach ($groupedResults as $depsec => $data) {
            $groupedResults[$depsec]['most_happening'] = array_slice($data['most_happening'], 0, 4);
        }

        // Convert to JSON
        $jsonOutput = json_encode(array_values($groupedResults));

        echo $jsonOutput;
    }

    public function generateCountSumByDepSec($startDate, $endDate){
        global $go_ncadb;

        $sql = "SELECT
            -- SADT.statanswerdt_questiondt,
            -- SADT.statanswerdt_question,
            Q.question_compfuncdepsec AS 'depsec',
            -- QDT.questiondt_title AS 'title',
            SUM( SADT.statanswerdt_count ) AS 'COUNT' 
            FROM tb_statanswerdt AS SADT
            LEFT JOIN tb_questiondt AS QDT ON QDT.questiondt = SADT.statanswerdt_questiondt
            LEFT JOIN tb_question AS Q ON Q.question = SADT.statanswerdt_question 
            WHERE
            SADT.statanswerdt_date BETWEEN '$startDate' AND '$endDate'
            GROUP BY
            Q.question_compfuncdepsec
            ORDER BY COUNT DESC;";

        $arr = $go_ncadb->ncaretrieve($sql, "question");

        $cmd = new question();

        $ar_department = $cmd->getSectionData();
        $ar_department = $ar_department["data"];

        for ($i=0; $i < count($arr); $i++) { 
           foreach ($ar_department as $key => $value) {
                if($value["section_id"] ==  $arr[$i]["depsec"]){
                    $arr[$i]["depsec_name"] = $value["section_name"];
                }
           }
        }

        return json_encode($this->ArrayKeyRemover($arr));
    }


    public function ArrayKeyRemover($par_array){
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

    
}