<?php
date_default_timezone_set("Asia/Bangkok");
session_start();
$gb_notlogin = true;
require "../include.inc.php";
require "customfunction.php";

// $method = $_GET["method"];

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

$debug = 0;
if ($debug) {
    echo '<pre>';
    print_r($ar_prm);
    echo '</pre>';
}
header('Content-Type: application/json; charset=utf-8');
$apiCalling = new ncaapicalling();

switch ($ar_prm["method"]) {
    case "getQuestionList":
        echo $apiCalling->getQuestionList();
        break;
    // case "getItemList":
    //     echo $apiCalling->getItemList($ar_prm["par_id"], $ar_prm["offset"], $ar_prm["limit"], $ar_prm["draw"], $ar_prm["giftType"], $ar_prm["search"]);
    //     break;
    

}

class ncaapicalling
{
    //Thanks to p'JJ aka MASTER'JJ
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

    public function areDatesInDifferentWeeks(DateTime $date1, DateTime $date2)
    {
        // Set Sunday as the first day of the week
        $customWeekStart = 0;
    
        // Adjust the days based on the custom week start
        $dayDiff1 = ($date1->format('w') - $customWeekStart + 7) % 7;
        $dayDiff2 = ($date2->format('w') - $customWeekStart + 7) % 7;
    
        // Calculate the week number
        $weekNumber1 = floor(($date1->format('z') - $dayDiff1) / 7) + 1;
        $weekNumber2 = floor(($date2->format('z') - $dayDiff2) / 7) + 1;
    
        return ($date1->format('Y') != $date2->format('Y')) || ($weekNumber1 != $weekNumber2);
    }

    public function CheckExchangeCondition($frequency, $datetimenow, $lastTransaction, $type = "")
    {
        // Check if $lastTransaction is empty
        if (empty($lastTransaction)) {
            return true;
        }

        // Convert the date and time strings to DateTime objects
        $now = new DateTime($datetimenow);
        $last = new DateTime($lastTransaction);

        if ($type == "3") {
            return false;
        }

        // Check based on the specified frequency
        if ($frequency == "once") {
            return false;
        } elseif ($frequency == "daily") {
            // Check if both dates are not on the same day
            return $now->format('Y-m-d') !== $last->format('Y-m-d');
        } elseif ($frequency == "weekly") {
            // Check if both dates are not in the same week (considering ISO weeks)
            // return $now->format('oW') !== $last->format('oW');

            // Check if both dates are not in the same week (considering Sunday as the first day)
            // return $now->format('YW') !== $last->format('YW');
            return $this->areDatesInDifferentWeeks($now, $last);
        } elseif ($frequency == "monthly") {
            // Check if both dates are not in the same month
            return $now->format('Y-m') !== $last->format('Y-m');
        } elseif ($frequency == "freely") {
            return true;
        } else {
            return false;
        }
    }

    public function getQuestionList()
    {   
        $other = "อื่นๆ";
        global $go_ncadb;
        $sql = "SELECT * FROM tb_question WHERE question_active = '1' ";

        $result = $go_ncadb->ncaretrieve($sql, "question");
        $data = array();
        foreach ($result as $key => $value) {

            if($value['question_recspid'] > 0){
                $sql = "SELECT staff_dspnm FROM staff WHERE staff = '".$value['question_recspid']."' ";
                $res = $go_ncadb->ncaretrieve($sql, "icms");
                $value['question_recname'] = $res[0]['staff_dspnm'];
            }
            if($value['question_compfunc'] > 0){
                $sqlmcompfunc = "SELECT m_compfunc_name_th FROM m_compfunc WHERE m_compfunc = '".$value['question_compfunc']."' ";
                $arr_mcompfunc = $go_ncadb->ncaretrieve($sqlmcompfunc, "icms");
                $value['question_compfuncname'] = $arr_mcompfunc[0]['m_compfunc_name_th'];
            }
            if($value['question_compfuncdep'] > 0){
                $sqlmcompfuncdep = "SELECT m_compfuncdep_name_th FROM m_compfuncdep WHERE m_compfuncdep = '".$value['question_compfuncdep']."' ";
                $arr_mcompfuncdep = $go_ncadb->ncaretrieve($sqlmcompfuncdep, "icms");
                $value['question_compfuncdepname'] = $arr_mcompfuncdep[0]['m_compfuncdep_name_th'];
            }
            if($value['question_mquestiontype'] > 0){
                $sqlmquestiontype = "SELECT m_questiontype_name FROM m_questiontype WHERE m_questiontype = '".$value['question_mquestiontype']."' ";
                $arr_mquestiontype= $go_ncadb->ncaretrieve($sqlmquestiontype, "question");;
                $value['question_mquestiontypename'] = $arr_mquestiontype[0]['m_questiontype_name'];
            }
            $value['currrent_user'] = $_SESSION['userData']['stf'];
            $data[] = $value;
        }

        if (empty($data)) {
            return json_encode(array());
        }

        return json_encode($this->ncaArrayConverter($data));
        
    }

    // public function getRewardsListNcaWeb()
    // {
    //     global $go_ncadb;
    //     $resCode = "0";
    //     $msg = "Unsuccessfully";
    //     $today = new DateTime();
    //     $sevenDaysAgo = clone $today;
    //     $sevenDaysAgo->modify('-7 days');
    //     $sevenDaysAgo->setTime(0, 0, 0);
    //     $todayDateTime = date('Y-m-d');
    //     $formattedDate7daysAgo = $sevenDaysAgo->format('Y-m-d H:i:s');
    //     $other = iconv('utf-8','tis-620','อื่นๆ');
    //     $otheren = iconv('utf-8','tis-620','Other');

    //     $sql = "SELECT
    //     giftdetail AS id,
    //     giftdetail_name AS titleName,
    //     giftdetail_partnername AS partnername,
    //     giftdetail_nameen AS titleNameEn,
    //     giftdetail_partnernameen AS partnernameEn,
    //     giftdetail_type AS type,
    //     giftdetail_category AS category,
    //     giftcategory.giftcategory_name AS categoryName,
    //     giftcategory.giftcategory_nameen AS categoryNameEn,
    //     CASE WHEN giftdetail.giftdetail_category = 0 THEN '$other' ELSE giftcategory.giftcategory_name END AS categoryName,
    //     CASE WHEN giftdetail.giftdetail_category = 0 THEN '$otheren' ELSE giftcategory.giftcategory_nameen END AS categoryNameEn,
    //     giftdetail_point AS 'point',
    //     giftdetail_redeemfeq AS redeemfeq,
    //     giftdetail_startdate AS startdate,
    //     giftdetail_enddate AS enddate,
    //     giftdetail_image1 AS image1,
    //     giftdetail_ishot AS ishot,
    //     giftdetail_recdatetime AS 'datetime',
    //     (SELECT COUNT(giftitems_giftdetail) AS countRow1 FROM `giftitems` WHERE giftitems_giftdetail = giftdetail AND (giftitems_isused = 0 OR giftitems_isused = 3) AND giftitems_active = 1) AS total_items,
    //     (SELECT COUNT(giftitems_giftdetail) AS countRow2 FROM `giftitems` WHERE giftitems_giftdetail = giftdetail AND giftitems_active = 0) AS items_reserve,
    //     (SELECT COUNT(giftrecords) AS countRow3 FROM `giftrecords` WHERE giftrecords.giftrecords_giftdetail = giftdetail) AS items_used,
    //     (SELECT COUNT(giftrecords) AS countRow4 FROM `giftrecords` WHERE giftrecords.giftrecords_giftdetail = giftdetail AND giftrecords_datemaker >= '$formattedDate7daysAgo') AS redeemIn7days
    //     FROM giftdetail
    //     LEFT JOIN giftcategory ON giftcategory.giftcategory = giftdetail.giftdetail_category 
    //     WHERE '$todayDateTime' >= giftdetail_startdate AND '$todayDateTime' <= giftdetail_enddate
    //     AND giftdetail.giftdetail_active != '0'
    //     ORDER BY redeemIn7days DESC;";
    //     $result = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sql, "otsdev"));

    //     if ($result) {
    //         $resCode = "1";
    //         $msg = "Successfully";
    //     }
    //     $rtn = array(
    //         "resCode" => $resCode,
    //         "resMsg" => $msg,
    //         "rtn" => $result,
    //     );
    //     return json_encode($rtn);
    // }

    // public function getRewardDetailNcaWeb($par_id)
    // {
    //     global $go_ncadb;
    //     $other = iconv('utf-8','tis-620','อื่นๆ');
    //     $otherEn = "Other";

    //     $resCode = "0";
    //     $msg = "Unsuccessfully";
    //     $sql = "SELECT
    //     giftdetail.giftdetail AS id,
    //     giftdetail.giftdetail_gift AS gift_giftId,
    //     giftdetail.giftdetail_name AS `name`,
    //     giftdetail.giftdetail_partnername AS partnername,
    //     giftdetail.giftdetail_nameen AS `nameen`,
    //     giftdetail.giftdetail_partnernameen AS partnernameen,
    //     giftdetail.giftdetail_type AS 'type',
    //     giftdetail.giftdetail_category AS category,
    //     giftcategory.giftcategory_name AS categoryName,
    //     CASE WHEN giftdetail.giftdetail_category = 0 THEN '$other' ELSE giftcategory.giftcategory_name END AS categoryName,
    //     giftcategory.giftcategory_nameen AS categoryNameEN,
    //     CASE WHEN giftdetail.giftdetail_category = 0 THEN '$otherEn' ELSE giftcategory.giftcategory_nameen END AS categoryNameEN,
    //     giftdetail.giftdetail_point AS 'point',
    //     giftdetail.giftdetail_startdate AS startdate,
    //     giftdetail.giftdetail_enddate AS enddate,
    //     giftdetail.giftdetail_redeemfeq AS redeemfeq,
    //     giftdetail.giftdetail_quantity AS quantity,
    //     giftdetail.giftdetail_condition AS `condition`,
    //     giftdetail.giftdetail_conditionen AS `conditionen`,
    //     giftdetail.giftdetail_pickupcycel AS pickupcycel,
    //     giftdetail.giftdetail_image1 AS image1,
    //     giftdetail.giftdetail_image2 AS image2,
    //     giftdetail.giftdetail_image3 AS image3,
    //     giftdetail.giftdetail_image4 AS image4,
    //     giftdetail.giftdetail_image5 AS image5,
    //     giftdetail.giftdetail_active AS active,
    //     (SELECT COUNT(giftitems_giftdetail) AS countRow1 FROM `giftitems` WHERE giftitems_giftdetail = giftdetail AND (giftitems_isused = 0 OR giftitems_isused = 3) AND giftitems_active = 1) AS total_items,
    //     (SELECT COUNT(giftrecords) AS countRow1 FROM `giftrecords` WHERE giftrecords.giftrecords_giftdetail = giftdetail.giftdetail) AS items_used
    //     FROM giftdetail
    //     LEFT JOIN giftcategory ON giftdetail.giftdetail_category = giftcategory.giftcategory
    //     WHERE giftdetail.giftdetail = '$par_id' LIMIT 1";

    //     if ($rawData = $go_ncadb->ncaretrieve($sql, "otsdev")) {
    //         $wetData = $this->ncaArrayConverter($rawData);
    //         $alljson = $wetData;
    //         $resCode = "1";
    //         $msg = "Successfully";
    //     } else {
    //         $alljson = array();
    //     }

    //     $rtn = array(
    //         "resCode" => $resCode,
    //         "resMsg" => $msg,
    //         "rtn" => $alljson,
    //     );
    //     return json_encode($rtn);
    // }

    // public function getItemList($par_id, $offset, $limit, $draw, $gifttype, $search)
    // {
    //     $searchTis620 = iconv('utf-8', 'tis-620', $search);
    //     global $go_ncadb;
    //     $sql = "SELECT 	giftitems.*, giftrecords.giftrecords_code FROM giftitems LEFT JOIN giftrecords ON giftitems.giftitems = giftrecords.giftrecords_giftitems WHERE giftitems_giftdetail = '$par_id'
    //     AND (giftitems_code LIKE '%{$search}%'
    //     OR giftitems_modidspm LIKE '%{$searchTis620}%')
    //     ORDER BY giftitems ASC LIMIT $offset, $limit";

    //     if ($gifttype == "3") {
    //         $sqlCountAll = "SELECT
    //         COUNT(giftitems) AS totalItems,
    //         (SELECT COUNT(giftrecords) AS countRow1 FROM giftrecords WHERE giftrecords.giftrecords_giftdetail = '$par_id') AS items_used
    //         FROM giftitems WHERE giftitems_giftdetail = '$par_id' ORDER BY giftitems";
    //     } else {
    //         $sqlCountAll = "SELECT
    //                     COUNT(giftitems) AS totalItems,
    //                     (SELECT COUNT(giftitems) AS countRow1 FROM `giftitems` WHERE giftitems_giftdetail = '$par_id' AND giftitems_isused = 1) AS items_used,
    //                     (SELECT COUNT(giftitems) AS countRow2 FROM `giftitems` WHERE giftitems_giftdetail = '$par_id' AND giftitems_isused = 0) AS items_redeemable,
    //                     (SELECT COUNT(giftitems) AS countRow3 FROM `giftitems` WHERE giftitems_giftdetail = '$par_id' AND giftitems_active = 0) AS items_reserve
    //                     FROM giftitems WHERE giftitems_giftdetail = '$par_id' ORDER BY giftitems";
    //     }

    //     $recordsTotal = $go_ncadb->ncaretrieve($sql, "otsdev");
    //     $recordsArray = $this->ncaArrayConverter($recordsTotal);

    //     $countArray = $go_ncadb->ncaretrieve($sqlCountAll, "otsdev");
    //     $countArray = $this->ncaArrayConverter($countArray);

    //     if ($gifttype == "3") {
    //         $rtn = array(
    //             "resCode" => "1",
    //             "resMsg" => "Successfully",
    //             "draw" => $draw,
    //             "recordsTotal" => $countArray[0]["totalItems"],
    //             "recordsFiltered" => $countArray[0]["totalItems"],
    //             "redeemed" => $countArray[0]["items_used"],
    //             "giftitems_type" => $gifttype,
    //             "data" => $recordsArray,
    //         );
    //     } else {
    //         $rtn = array(
    //             "resCode" => "1",
    //             "resMsg" => "Successfully",
    //             "draw" => $draw,
    //             "recordsTotal" => $countArray[0]["totalItems"],
    //             "recordsFiltered" => $countArray[0]["totalItems"],
    //             "redeemable" => $countArray[0]["items_redeemable"],
    //             "redeemed" => $countArray[0]["items_used"],
    //             "reserve" => $countArray[0]["items_reserve"],
    //             "giftitems_type" => $gifttype,
    //             "data" => $recordsArray,
    //         );
    //     }
    //     return json_encode($rtn);
    // }

    // public function importCodeByCSV($csvFile, $par_id, $par_type, $par_userId, $par_usernm, $times)
    // {
    //     global $go_ncadb;
    //     $rowInsert = 0;
    //     $maxTimesToInsert = intval($times);
    //     if (isset($csvFile['tmp_name'])) {
    //         $csvFile = $csvFile['tmp_name'];
    //         $datetime = date('Y-m-d H:i:s');
    //         // Read the CSV file
    //         if (($handle = fopen($csvFile, "r")) !== false) {
    //             while (($data = fgetcsv($handle, 1000, ",")) !== false) {
    //                 $code = $data[0];
    //                 $exp = $data[1];
    //                 if (empty($code) || $rowInsert == $maxTimesToInsert) {
    //                     break;
    //                 }
    //                 // if($rowInsert == $timeToInsert){
    //                 //     break;
    //                 // }
    //                 $InsertSqlBuilder = new SqlBuilder();
    //                 $InsertSqlBuilder->setTableName("giftitems");
    //                 $ii = 0;
    //                 $sqlBuild[$ii++] = new TField("giftitems_code", iconv('utf-8', 'tis-620', $code), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_expires", iconv('utf-8', 'tis-620', $exp), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_giftdetail", iconv('utf-8', 'tis-620', $par_id), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_type", iconv('utf-8', 'tis-620', $par_type), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_recdspid", iconv('utf-8', 'tis-620', $par_userId), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_modidspid", iconv('utf-8', 'tis-620', $par_userId), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_recdsnm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_modidspm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_recdatetime", iconv('utf-8', 'tis-620', $datetime), "string");
    //                 $sqlBuild[$ii++] = new TField("giftitems_moddatetime", iconv('utf-8', 'tis-620', $datetime), "string");
    //                 $InsertSqlBuilder->setField($sqlBuild);
    //                 $query = $InsertSqlBuilder->InsertSql();
    //                 $result = $go_ncadb->ncaexec($query, "otsdev");
    //                 $rowInsert += 1;
    //             }
    //             fclose($handle);
    //             if ($result) {
    //                 $insertResult = array(
    //                     'resCode' => '1',
    //                     'datetime' => $datetime,
    //                     'resMsg' => 'Insertd Successfully',
    //                     'rowInsert' => $rowInsert,
    //                 );
    //             } else {
    //                 $insertResult = array(
    //                     'resCode' => '0',
    //                     'datetime' => $datetime,
    //                     'resMsg' => 'Unsuccessfully',
    //                     'rowInsert' => $rowInsert,
    //                 );
    //             }
    //         }
    //     } else {
    //         $insertResult = array(
    //             'resCode' => '0',
    //             'resMsg' => 'Can\'t read .csv file.',
    //         );
    //     }
    //     return json_encode($insertResult);
    // }

    // public function insertItemsAsBatch($par_id, $times, $autogen, $startofcode, $exp, $par_type, $par_userId, $par_usernm)
    // {
    //     global $go_ncadb;
    //     $datetime = date('Y-m-d H:i:s');
    //     $str_start = '';
    //     $rowInsert = 0;
    //     $result = false;
    //     for ($i = 0; $i < $times; $i++) {
    //         if ($autogen) {
    //             $str_start = $startofcode . date("YmdHis") . mt_rand(0, 100) . $rowInsert;
    //         }

    //         if ($par_type == '3') {
    //             $str_start = $startofcode;
    //         }
    //         // $sqlGiftObj[$ii++] = new TField("gift_name", iconv('utf-8', 'tis-620', $par_name), "string");
    //         $InsertSqlBuilder = new SqlBuilder();
    //         $InsertSqlBuilder->setTableName("giftitems");
    //         $ii = 0;
    //         $sqlBuild[$ii++] = new TField("giftitems_code", iconv('utf-8', 'tis-620', $str_start), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_expires", iconv('utf-8', 'tis-620', $exp), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_giftdetail", iconv('utf-8', 'tis-620', $par_id), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_type", iconv('utf-8', 'tis-620', $par_type), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_recdspid", iconv('utf-8', 'tis-620', $par_userId), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_modidspid", iconv('utf-8', 'tis-620', $par_userId), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_recdsnm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_modidspm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_recdatetime", iconv('utf-8', 'tis-620', $datetime), "string");
    //         $sqlBuild[$ii++] = new TField("giftitems_moddatetime", iconv('utf-8', 'tis-620', $datetime), "string");
    //         if ($par_type == '3') {
    //             $sqlBuild[$ii++] = new TField("giftitems_isused", iconv('utf-8', 'tis-620', '3'), "string");
    //         }
    //         $InsertSqlBuilder->setField($sqlBuild);
    //         $query = $InsertSqlBuilder->InsertSql();
    //         //echo $query;
    //         if (!$go_ncadb->ncaexec($query, "otsdev")) {
    //             $go_ncadb->ncarollback("otsdev");
    //             break;
    //         } else {
    //             $result = true;
    //         }
    //     }
    //     if ($result) {
    //         $insertResult = array(
    //             'resCode' => '1',
    //             'par_id' => $par_id,
    //             'datetime' => $datetime,
    //             'resMsg' => 'Insertd Successfully',
    //             'rowInsert' => $rowInsert,
    //         );
    //     } else {
    //         $insertResult = array(
    //             'resCode' => '0',
    //             'par_id' => $par_id,
    //             'datetime' => $datetime,
    //             'resMsg' => 'Unsuccessfully',
    //             'rowInsert' => $rowInsert,
    //         );
    //     }
    //     return json_encode($insertResult);
    // }

    // public function deleteItemsAsBatch($target)
    // {
    //     global $go_ncadb;
    //     $resCode = '0';
    //     $targetArray = json_decode($target, true);
    //     $targetArray = explode(',', $targetArray);
    //     $target = "('" . implode("','", $targetArray) . "')";

    //     $sqlBuilder = new SqlBuilder();
    //     $sqlBuilder->SetTableName("giftitems");
    //     $sqlBuilder->SetWhereClause("giftitems IN $target");
    //     $query = $sqlBuilder->DeleteSql();
    //     //echo $query;

    //     if ($go_ncadb->ncaexec($query, "otsdev")) {
    //         $result = true;
    //     } else {
    //         $go_ncadb->rollback();
    //         $result = false;
    //     }

    //     if ($result) {
    //         $resCode = '1';
    //         $resMsg = 'successfully';
    //     } else {
    //         $resCode = '0';
    //         $resMsg = 'unsuccessfully';
    //     }

    //     $deleteResult = array(
    //         'resCode' => $resCode,
    //         'resMsg' => $resMsg,
    //         'action' => 'delete',
    //         'target' => $targetArray,
    //     );

    //     return json_encode($deleteResult);
    // }

    // public function switchActiveItemsAsBatch($target, $userId, $usernm)
    // {
    //     global $go_ncadb;
    //     $resCode = '0';
    //     $datetime = date('Y-m-d H:i:s');
    //     $targetArray = json_decode($target, true);
    //     $targetArray = explode(',', $targetArray);
    //     $target = "('" . implode("','", $targetArray) . "')";
    //     $usernm = iconv('utf-8', 'tis-620', $usernm);
    //     $sql = "UPDATE giftitems
    //             SET giftitems_active = CASE
    //             WHEN giftitems_active = 0 THEN 1
    //             WHEN giftitems_active = 1 THEN 0
    //             ELSE giftitems_active END,
    //             giftitems_modidspid = '$userId',
    //             giftitems_modidspm = '$usernm',
    //             giftitems_moddatetime = '$datetime'
    //             WHERE giftitems IN $target;";
    //     //echo $sql;
    //     $result = $go_ncadb->ncaexec($sql, "otsdev");
    //     if ($result) {
    //         $resCode = '1';
    //         $resMsg = 'successfully';
    //     } else {
    //         $go_ncadb->rollback();
    //         $resCode = '0';
    //         $resMsg = 'unsuccessfully';
    //     }
    //     $updateResult = array(
    //         'resCode' => $resCode,
    //         'resMsg' => $resMsg,
    //         'action' => 'switch',
    //         'target' => $targetArray,
    //     );
    //     return json_encode($updateResult);
    // }

    // public function ncaGiftTransection($APIKey ,$par_rewardId, $par_pickupdate, $par_pickupOutletId, $par_pickupOutletName , $par_pickupOutletNameEN, $pickupCode, $par_userId, $par_usernm, $par_pointremaining)
    // {
    //     $debug = 0;
    //     $resCode = "0";
    //     $transectionResult = "0";
    //     $resMsg = "Unsuccessfully";
    //     global $go_ncadb;
    //     $date = date('Y-m-d H:i:s');

    //     if($APIKey != "bmNhZHZhbmNl"){
    //         $jsonRtn = array(
    //             "resCode" => "99",
    //             "resMsg" => "YOU DON'T HAVE PERMISION TO USE THIS API.",
    //         );
    //         return json_encode($jsonRtn);
    //     }

    //     $sqlGetGiftData = "SELECT giftdetail_redeemfeq, giftdetail_type, giftdetail_quantity, giftdetail_active, giftdetail_point FROM giftdetail WHERE giftdetail = $par_rewardId";
    //     $resultGiftData = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sqlGetGiftData, "otsdev"));
    //     $giftdetail_redeemfeq = $resultGiftData[0]["giftdetail_redeemfeq"];
    //     $giftdetail_quantity = $resultGiftData[0]["giftdetail_quantity"];
    //     $gifttype = $resultGiftData[0]["giftdetail_type"];

    //     if ($resultGiftData[0]["giftdetail_active"] == "0") {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "transectionResult" => "5",
    //             "resMsg" => "this item is not available for exchange. (gift not available)",
    //             "exchangeCondition" => $giftdetail_redeemfeq,
    //             "now" => $date,
    //             "latestTransactionDatetime" => "",
    //             "latestTransaction" => "",
    //             "data" => array(),
    //         );
    //         return json_encode($jsonRtn);
    //     }

    //     if ($gifttype == "3") {
    //         $sqlCountGiftRecords = "SELECT COUNT(giftrecords) AS recordsTotal FROM giftrecords WHERE giftrecords_giftdetail = $par_rewardId";
    //         $CountGiftRecords = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sqlCountGiftRecords, "otsdev"));
    //         if ($CountGiftRecords[0]["recordsTotal"] >= $giftdetail_quantity) {
    //             $jsonRtn = array(
    //                 "resCode" => "1",
    //                 "transectionResult" => "4",
    //                 "resMsg" => "this item is out of stock.",
    //                 "exchangeCondition" => $giftdetail_redeemfeq,
    //                 "now" => $date,
    //                 "latestTransactionDatetime" => "",
    //                 "latestTransaction" => "",
    //                 "data" => array(),
    //             );
    //             return json_encode($jsonRtn);
    //         }
    //     }

    //     $sqlGetUserGiftExchange = "SELECT * FROM gifthistory WHERE giftdetail = '$par_rewardId' AND giftrecords_user = '$par_userId' ORDER BY giftrecords_datemaker DESC LIMIT 1";
    //     $resultGift = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sqlGetUserGiftExchange, "otsdev"));
    //     $targetReusltGiftExchange = $resultGift[0];

    //     $canBeExchange = $this->CheckExchangeCondition($giftdetail_redeemfeq, $date, $targetReusltGiftExchange["giftrecords_datemaker"], $gifttype);
    //     if (!$canBeExchange) {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "transectionResult" => "2",
    //             "resMsg" => "User have already exchanged this item during this period ($giftdetail_redeemfeq)",
    //             "exchangeCondition" => $giftdetail_redeemfeq,
    //             "now" => $date,
    //             "latestTransactionDatetime" => $targetReusltGiftExchange["giftrecords_datemaker"],
    //             "latestTransaction" => $targetReusltGiftExchange,
    //             "data" => array(),
    //         );
    //         return json_encode($jsonRtn);
    //     }

    //     $getGift = "SELECT * FROM gitfitemslist WHERE giftId = '$par_rewardId' AND active = 1 AND isused != 1 LIMIT 1;";
    //     $resultGift = $this->ncaArrayConverter($go_ncadb->ncaretrieve($getGift, "otsdev"));

    //     if (empty($resultGift)) {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "transectionResult" => "4",
    //             "resMsg" => 'No more item left for exchange.',
    //             "data" => $resultGift,
    //         );
    //         return json_encode($jsonRtn);
    //     } else {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "transectionResult" => "1",
    //             "resMsg" => 'OK!',
    //             "data" => $resultGift,
    //         );
    //     }

    //     $updateSql = new SqlBuilder();
    //     $updateSql->SetTableName("gitfitemslist");
    //     $ii = 0;
    //     $o_obj = null;
    //     if ($gifttype != "3") {
    //         $o_obj[$ii++] = new TField("isused", '1', "string");
    //     } else {
    //         $o_obj[$ii++] = new TField("isused", '3', "string");
    //     }
    //     $updateTarget = $resultGift[0]["giftitemId"];
    //     $updateSql->SetWhereClause("giftitemId = $updateTarget");
    //     $updateSql->SetField($o_obj);
    //     $queryUpdate = $updateSql->UpdateSql();
    //     //echo $queryUpdate;
    //     if ($go_ncadb->ncaexec($queryUpdate, "otsdev")) {
    //         $updateUsedItem = true;
    //     } else {
    //         $updateUsedItem = false;
    //         $go_ncadb->rollback();
    //         $resCode = "1";
    //         $jsonRtn = array(
    //             "resCode" => $resCode,
    //             "transectionResult" => "3",
    //             "resMsg" => 'Errror occurred while updating item isused status.',
    //             "data" => $resultGift,
    //         );
    //     }

    //     if ($updateUsedItem) {
    //         $InsertSql = new SqlBuilder();
    //         $InsertSql->SetTableName("giftrecords");
    //         $ii = 0;
    //         $o_obj = null;

    //         $o_obj[$ii++] = new TField("giftrecords_giftdetail", $resultGift[0]["giftId"], "string");
    //         $o_obj[$ii++] = new TField("giftrecords_giftitems", $resultGift[0]["giftitemId"], "string");
    //         $o_obj[$ii++] = new TField("giftrecords_type", $resultGift[0]["giftitemType"], "string");
    //         if (empty($pickupCode)) {
    //             $o_obj[$ii++] = new TField("giftrecords_code", $resultGift[0]["giftitemCode"], "string");
    //         } else {
    //             $o_obj[$ii++] = new TField("giftrecords_code", $pickupCode.$resultGift[0]["giftitemId"], "string");
    //         }
    //         $o_obj[$ii++] = new TField("giftrecords_pickupdate", $par_pickupdate, "string");
    //         $o_obj[$ii++] = new TField("giftrecords_pickupoutlet", $par_pickupOutletId, "string");
    //         $o_obj[$ii++] = new TField("giftrecords_pickupoutletnm", iconv('utf-8', 'tis-620', $par_pickupOutletName), "string");
    //         $o_obj[$ii++] = new TField("giftrecords_pickupoutletnmen", iconv('utf-8', 'tis-620', $par_pickupOutletNameEN), "string");

    //         if ($resultGift[0]["giftitemType"] != "2") {
    //             $o_obj[$ii++] = new TField("giftrecords_iscomplete", "1", "string");
    //         } else {
    //             $o_obj[$ii++] = new TField("giftrecords_iscomplete", "0", "string");
    //         }

    //         $o_obj[$ii++] = new TField("giftrecords_user", $par_userId, "string");
    //         $o_obj[$ii++] = new TField("giftrecords_usernm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //         $o_obj[$ii++] = new TField("giftrecords_datemaker", $date, "string");
    //         $o_obj[$ii++] = new TField("giftrecords_point", $resultGiftData[0]["giftdetail_point"], "string");
    //         $o_obj[$ii++] = new TField("giftrecords_userpointremaining", $par_pointremaining, "string");
    //         $InsertSql->SetField($o_obj);
    //         $queryInsert = $InsertSql->InsertSql();
    //         if (!$go_ncadb->ncaexec($queryInsert, "otsdev")) {
    //             $go_ncadb->ncarollback("otsdev");
    //             $jsonRtn = array(
    //                 "resCode" => '1',
    //                 "transectionResult" => '3',
    //                 "resMsg" => 'Can\'t insert log to log table',
    //                 "data" => $resultGift,
    //             );
    //         } else {
    //             $recordId = $go_ncadb->ncaGetInsId("otsdev");
    //             $jsonRtn = array(
    //                 "resCode" => "1",
    //                 "transectionResult" => "1",
    //                 "resMsg" => 'Get item : OK, Update item : OK, Insert Exchange Log : OK',
    //                 "recordId" => strval($recordId),
    //                 "data" => $resultGift,
    //             );
    //         }
    //     }
    //     return json_encode($jsonRtn);
    // }

    // public function checkGiftCanBeRedeemForUser($par_rewardId, $par_userId)
    // {
    //     global $go_ncadb;
    //     $date = date('Y-m-d H:i:s');
    //     $sqlGetGiftData = "SELECT giftdetail_redeemfeq, giftdetail_type, giftdetail_quantity, giftdetail_active, giftdetail_point FROM giftdetail WHERE giftdetail = $par_rewardId";
    //     $resultGiftData = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sqlGetGiftData, "otsdev"));
    //     $giftdetail_redeemfeq = $resultGiftData[0]["giftdetail_redeemfeq"];
    //     $giftdetail_quantity = $resultGiftData[0]["giftdetail_quantity"];
    //     $gifttype = $resultGiftData[0]["giftdetail_type"];

    //     if ($resultGiftData[0]["giftdetail_active"] == "0") {
    //         $jsonRtn = array(
    //             "resCode" => "5",
    //             "resMsg" => "this item is not available for exchange. (gift not available)",
    //             "exchangeCondition" => $giftdetail_redeemfeq,
    //             "now" => $date,
    //             "latestTransactionDatetime" => "",
    //             "latestTransaction" => "",
    //             "data" => array(),
    //         );
    //         return json_encode($jsonRtn);
    //     }

    //     if ($gifttype == "3") {
    //         $sqlCountGiftRecords = "SELECT COUNT(giftrecords) AS recordsTotal FROM giftrecords WHERE giftrecords_giftdetail = $par_rewardId";
    //         $CountGiftRecords = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sqlCountGiftRecords, "otsdev"));
    //         if ($CountGiftRecords[0]["recordsTotal"] >= $giftdetail_quantity) {
    //             $jsonRtn = array(
    //                 "resCode" => "4",
    //                 "resMsg" => "this item is out of stock.",
    //                 "exchangeCondition" => $giftdetail_redeemfeq,
    //                 "now" => $date,
    //                 "latestTransactionDatetime" => "",
    //                 "latestTransaction" => "",
    //                 "data" => array(),
    //             );
    //             return json_encode($jsonRtn);
    //         }
    //     }

    //     $getGift = "SELECT giftId FROM gitfitemslist WHERE giftId = '$par_rewardId' AND active = 1 AND isused != 1 LIMIT 1;";
    //     $resultGift = $this->ncaArrayConverter($go_ncadb->ncaretrieve($getGift, "otsdev"));

    //     if (empty($resultGift)) {
    //         $jsonRtn = array(
    //             "resCode" => "4",
    //             "resMsg" => 'No more item left for exchange.',
    //             "data" => $resultGift,
    //         );
    //         return json_encode($jsonRtn);
    //     }

    //     $sqlGetUserGiftExchange = "SELECT * FROM gifthistory WHERE giftdetail = '$par_rewardId' AND giftrecords_user = '$par_userId' ORDER BY giftrecords_datemaker DESC LIMIT 1";
    //     $resultGift = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sqlGetUserGiftExchange, "otsdev"));
    //     $targetReusltGiftExchange = $resultGift[0];

    //     $canBeExchange = $this->CheckExchangeCondition($giftdetail_redeemfeq, $date, $targetReusltGiftExchange["giftrecords_datemaker"], $gifttype);
    //     if (!$canBeExchange) {
    //         $jsonRtn = array(
    //             "resCode" => "2",
    //             "resMsg" => "User have already exchanged this item during this period ($giftdetail_redeemfeq)",
    //             "exchangeCondition" => $giftdetail_redeemfeq,
    //             "now" => $date,
    //             "latestTransactionDatetime" => $targetReusltGiftExchange["giftrecords_datemaker"],
    //             "latestTransaction" => $targetReusltGiftExchange,
    //             "data" => array(),
    //         );

    //     } else {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "resMsg" => "User can exchange this item",
    //         );
    //     }

    //     if ($gifttype == "3") {
    //         $jsonRtn["resMsg"] = "this item is singel code (ONE FOR ALL) and this user aleredy redeemed for this item.";
    //     }
    //     return json_encode($jsonRtn);
    // }

    // public function getGiftTransectionHistory($offset, $limit, $draw, $search, $rewardId, $outletId, $iscomplete)
    // {   

    //     // let filterReward = 'all';
    //     // let filterOutlet = 'all';
    //     // let filterStatus = 'all';
    //     $usernmTarget = iconv('utf-8', 'tis-620', $search);
    //     global $go_ncadb;

    //     if($rewardId != "all"){
    //         $rewardFilter = "AND gifthistory.giftdetail = '$rewardId'";
    //         $rewardTotalsFilter = "AND gifthistory.giftdetail = '$rewardId'";
    //     }else{
    //         $rewardFilter = "";
    //         $rewardTotalsFilter = "";
    //     }
        
    //     if($outletId != "all"){
    //         $outletFilter = "AND gifthistory.giftrecords_pickupoutlet = '$outletId'";
    //         $outletTotalsFilter = "AND gifthistory.giftrecords_pickupoutlet = '$outletId'";
    //     }else{
    //         $outletFilter = "";
    //         $outletTotalsFilter = "";
    //     }

    //     if($iscomplete != "all"){
    //         $iscompleteFilter = "AND gifthistory.giftrecords_iscomplete = '$iscomplete'";
    //         $iscompleteTotalsFilter = "AND gifthistory.giftrecords_iscomplete = '$iscomplete'";
    //     }else{
    //         $iscompleteFilter = "";
    //         $iscompleteTotalsFilter = "";
    //     }


    //     $sql = "SELECT gifthistory.* FROM gifthistory WHERE
    //     (gifthistory.giftrecords_code LIKE '%$search%' OR
    //     gifthistory.giftrecords_usernm LIKE '%$usernmTarget%' OR
    //     gifthistory.giftrecords_user LIKE '%$search%') $rewardFilter $outletFilter $iscompleteFilter ORDER BY giftrecords_datemaker DESC LIMIT $offset, $limit";
    //     $sqlCountAll = "SELECT COUNT(gifthistory.giftrecords) AS totalItems FROM gifthistory WHERE gifthistory.giftrecords IS NOT NULL $rewardTotalsFilter $outletTotalsFilter $iscompleteTotalsFilter";
    //     $recordsTotal = $go_ncadb->ncaretrieve($sql, "otsdev");
    //     $recordsArray = $this->ncaArrayConverter($recordsTotal);

    //     $countArray = $go_ncadb->ncaretrieve($sqlCountAll, "otsdev");
    //     $countArray = $this->ncaArrayConverter($countArray);
    //     // echo $sql;
    //     // echo $sqlCountAll;
    //     // exit();
    //     $rtn = array(
    //         "resCode" => "1",
    //         "resMsg" => "Successfully",
    //         "draw" => $draw,
    //         "recordsTotal" => $countArray[0]["totalItems"],
    //         "recordsFiltered" => $countArray[0]["totalItems"],
    //         "data" => $recordsArray,
    //     );
    //     return json_encode($rtn);
    // }

    // public function getUserGiftTransectionHistory($par_id)
    // {
    //     global $go_ncadb;
    //     $sql = "SELECT
    //     gifthistory.giftrecords AS 'recordId',
    //     gifthistory.giftrecords_giftdetail AS 'giftId',
    //     gifthistory.giftrecords_giftitems AS 'itemId',
    //     gifthistory.giftrecords_type AS 'type',
    //     gifthistory.giftdetail_category AS 'category',
    //     gifthistory.giftrecords_code AS 'code',
    //     gifthistory.giftdetail_partnername AS 'partnerName',
    //     gifthistory.giftdetail_name AS 'name',
    //     gifthistory.giftitems_expires AS 'exp',
    //     gifthistory.giftdetail_point AS 'pointGiftUsed',
    //     gifthistory.giftrecords_point AS 'pointUsed',
    //     gifthistory.giftdetail AS 'giftId_gd',
    //     gifthistory.giftdetail_image1 AS 'image',
    //     gifthistory.giftrecords_pickupoutletnm AS 'outletName',
    //     gifthistory.giftrecords_pickupoutlet AS 'outletId',
    //     gifthistory.giftrecords_pickupdate AS 'pickupDate',
    //     gifthistory.giftrecords_datemaker AS 'transactionDatetime',
    //     gifthistory.giftrecords_pickupdatetime AS 'userpickupdatetime',
    //     gifthistory.giftrecords_iscomplete AS 'iscomplete',
    //     gifthistory.giftrecords_userpointremaining AS 'pointremaining'
    // FROM
    //     gifthistory
    // WHERE
    //     gifthistory.giftrecords_user = '$par_id' AND gifthistory.giftrecords_datemaker > NOW() - INTERVAL 1 YEAR
    // ORDER BY transactionDatetime DESC";

    //     $rawData = $go_ncadb->ncaretrieve($sql, "otsdev");
    //     $recordsArray = $this->ncaArrayConverter($rawData);

    //     $rtn = array(
    //         "resCode" => "1",
    //         "resMsg" => "Successfully",
    //         "data" => $recordsArray,
    //     );

    //     return json_encode($rtn);
    // }

    // public function deleteImage($par_id, $par_imageOrder, $par_imageName)
    // {
    //     global $go_ncadb;
    //     $path = '../storage/' . $par_id . '/';
    //     $o_bulid = new SqlBuilder();
    //     $o_bulid->SetTableName("giftdetail");
    //     $ii = 0;
    //     $o_obj = null;

    //     switch ($par_imageOrder) {
    //         case '1':
    //             $o_obj[$ii++] = new TField("giftdetail_image1", "", "string");
    //             break;
    //         case '2':
    //             $o_obj[$ii++] = new TField("giftdetail_image2", "", "string");
    //             break;
    //         case '3':
    //             $o_obj[$ii++] = new TField("giftdetail_image3", "", "string");
    //             break;
    //         case '4':
    //             $o_obj[$ii++] = new TField("giftdetail_image4", "", "string");
    //             break;
    //         case '5':
    //             $o_obj[$ii++] = new TField("giftdetail_image5", "", "string");
    //             break;
    //         default:
    //             return json_encode(array("resCode" => "0", "resMsg" => "Invalid Parameter"));
    //             break;
    //     }

    //     $o_bulid->SetField($o_obj);
    //     $o_bulid->SetWhereClause("giftdetail = $par_id");
    //     $sql = $o_bulid->UpdateSql();
    //     if ($go_ncadb->ncaexec($sql, "otsdev")) {
    //         $resCode = "1";
    //         $resMsg = "Image Delete Successfully";
    //         $image_path = $path . $par_imageName;
    //         if (file_exists($image_path)) {
    //             $isFileExists = true;
    //             unlink($image_path);
    //         } else {
    //             $isFileExists = false;
    //         }
    //     } else {
    //         $resCode = "0";
    //         $resMsg = "Failed to Delete Image";
    //         $image_path = '';
    //     }
    //     $rtnArray = array(
    //         "resCode" => $resCode,
    //         "resMsg" => $resMsg,
    //         "imagePath" => $image_path,
    //         "isFileExists" => $isFileExists,
    //     );
    //     return json_encode($rtnArray);

    // }

    // public function getPickUpDetail($pickupcode)
    // {
    //     global $go_ncadb;
    //     $sql = "SELECT
    //     gifthistory.* FROM gifthistory WHERE gifthistory.giftrecords_code = '$pickupcode' AND gifthistory.giftrecords_type = '2' LIMIT 1";
    //     $result = $this->ncaArrayConverter($go_ncadb->ncaretrieve($sql, "otsdev"));
    //     if (!empty($result)) {
    //         $rtnArray = array(
    //             "resCode" => "1",
    //             "resMsg" => "Successfully",
    //             "data" => $result,
    //         );
    //     } else {
    //         $rtnArray = array(
    //             "resCode" => "99",
    //             "resMsg" => "Not Found",
    //             "data" => $result,
    //         );
    //     }

    //     return json_encode($rtnArray);
    // }

    // public function pickupGift($par_pickupCode, $par_userId, $par_usernm)
    // {
    //     global $go_ncadb;
    //     $date = date('Y-m-d H:i:s');
    //     $o_bulid = new SqlBuilder();
    //     $o_bulid->SetTableName("giftrecords");
    //     $ii = 0;
    //     $o_obj = null;
    //     $o_obj[$ii++] = new TField("giftrecords_iscomplete", "1", "string");
    //     $o_obj[$ii++] = new TField("giftrecords_modidspid", $par_userId, "string");
    //     $o_obj[$ii++] = new TField("giftrecords_modidsnm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //     $o_obj[$ii++] = new TField("giftrecords_pickupdatetime", $date, "string");
    //     $o_bulid->SetField($o_obj);
    //     $o_bulid->SetWhereClause("giftrecords_code = '$par_pickupCode'");
    //     $sql = $o_bulid->UpdateSql();
    //     if ($go_ncadb->ncaexec($sql, "otsdev")) {
    //         $rtnArray = array(
    //             "resCode" => "1",
    //             "resMsg" => "Successfully",
    //         );
    //     } else {
    //         $rtnArray = array(
    //             "resCode" => "0",
    //             "resMsg" => "Unsuccessfully",
    //         );
    //     }
    //     return json_encode($rtnArray);
    // }

    // public function getOutletList()
    // {
    //     global $go_ncadb;
    //     $sql = "SELECT
    //     m_outlet.*
    //     FROM
    //     m_outlet
    //     WHERE
    //     m_outlet.m_outlet_active = '1' AND
    //     m_outlet.m_outlet_type IN ('nca') AND
    //     m_outlet.m_outlet NOT IN ('38','49','95','167','172','190','191')
    //     ORDER BY
    //     CONVERT(m_outlet_nameth USING tis620) ASC";

    //     $result = $go_ncadb->ncaretrieve($sql, "icms");

    //     return json_encode($this->ncaArrayConverter($result));
    // }

    // public function getGiftResultOnWeb($par_recordId, $APIKey)
    // {   
    //     if($APIKey != "bmNhZHZhbmNl"){
    //         $jsonRtn = array(
    //             "resCode" => "99",
    //             "resMsg" => "YOU DON'T HAVE PERMISION TO USE THIS API.",
    //         );
    //         return json_encode($jsonRtn);
    //     }

    //     global $go_ncadb;
    //     $sql = "SELECT
    //     gifthistory.giftrecords AS id,
    //     gifthistory.giftrecords_giftdetail AS giftId,
    //     gifthistory.giftrecords_giftitems AS itemId,
    //     gifthistory.giftrecords_type AS type,
    //     gifthistory.giftrecords_code AS `giftitemCode`,
    //     gifthistory.giftrecords_datemaker AS datemarker,
    //     gifthistory.giftrecords_usernm AS usernm,
    //     gifthistory.giftrecords_user AS userId,
    //     gifthistory.giftrecords_iscomplete AS iscomplete,
    //     gifthistory.giftrecords_pickupoutletnm AS pickupoutletName,
    //     gifthistory.giftrecords_pickupoutletnmen AS pickupoutletNameEN,
    //     gifthistory.giftrecords_pickupoutlet AS pickupoutletId,
    //     gifthistory.giftrecords_pickupdate AS pickupDate,
    //     gifthistory.giftdetail_name AS `name`,
    //     gifthistory.giftdetail_nameen AS `nameen`,
    //     gifthistory.giftdetail_partnername AS partnername,
    //     gifthistory.giftdetail_partnernameen AS partnernameen,
    //     gifthistory.giftitems_expires AS expDate,
    //     gifthistory.giftdetail_redeemfeq AS redeemfeq,
    //     gifthistory.giftdetail_point AS giftPoint,
    //     gifthistory.giftrecords_point AS pointUsed,
    //     gifthistory.giftdetail,
    //     gifthistory.giftitems_isused,
    //     gifthistory.giftdetail_category AS category,
    //     gifthistory.giftcategory_name AS categoryName,
    //     gifthistory.giftcategory_nameen AS categoryNameEN,
	//     gifthistory.giftdetail_image1 AS 'image'
    // FROM
    //     gifthistory
    // WHERE
    //     gifthistory.giftrecords = '$par_recordId'";

    //     if ($rawData = $go_ncadb->ncaretrieve($sql, "otsdev")) {
    //         $wetData = $this->ncaArrayConverter($rawData);
    //         $alljson = $wetData[0];
    //         $resCode = "1";
    //         $msg = "Successfully";
    //     } else {
    //         $resCode = "99";
    //         $msg = "record not found";
    //         $alljson = array();
    //     }

    //     $rtn = array(
    //         "resCode" => $resCode,
    //         "resMsg" => $msg,
    //         "rtn" => $alljson,
    //     );
    //     return json_encode($rtn);
    // }

    // public function getCategoryData($offset, $limit, $draw, $search)
    // {

    //     $usernmTarget = iconv('utf-8', 'tis-620', $search);
    //     global $go_ncadb;
    //     $sql = "SELECT giftcategory.*,
    //     (SELECT COUNT(*) FROM giftdetail WHERE giftdetail_category = giftcategory LIMIT 1) AS toatlItems
    //     FROM giftcategory WHERE giftcategory.giftcategory_name LIKE '%$search%' OR
    //     giftcategory.giftcategory_modidspnm LIKE '%$usernmTarget%'
    //     ORDER BY giftcategory.giftcategory_recdatetime ASC LIMIT $offset, $limit";
    //     $sqlCountAll = "SELECT COUNT(giftcategory.giftcategory) AS totalItems FROM giftcategory";
    //     $recordsTotal = $go_ncadb->ncaretrieve($sql, "otsdev");
    //     $recordsArray = $this->ncaArrayConverter($recordsTotal);

    //     $countArray = $go_ncadb->ncaretrieve($sqlCountAll, "otsdev");
    //     $countArray = $this->ncaArrayConverter($countArray);

    //     $rtn = array(
    //         "resCode" => "1",
    //         "resMsg" => "Successfully",
    //         "draw" => $draw,
    //         "recordsTotal" => $countArray[0]["totalItems"],
    //         "recordsFiltered" => $countArray[0]["totalItems"],
    //         "data" => $recordsArray,
    //     );
    //     return json_encode($rtn);
    // }

    // public function getCategoryListWeb()
    // {
    //     global $go_ncadb;
    //     $sql = "SELECT
    //     giftcategory.giftcategory, 
    //     giftcategory.giftcategory_name, 
    //     giftcategory.giftcategory_nameen, 
    //     giftcategory.giftcategory_icon
    //     FROM
    //     giftcategory WHERE giftcategory.giftcategory_active = 1";
    //     $recordsTotal = $go_ncadb->ncaretrieve($sql, "otsdev");
    //     $recordsArray = $this->ncaArrayConverter($recordsTotal);
    //     $rtn = array(
    //         "resCode" => "1",
    //         "resMsg" => "Successfully",
    //         "data" => $recordsArray,
    //     );
    //     return json_encode($rtn);
    // }

    // public function getCategorySpecificId($target)
    // {
    //     global $go_ncadb;
    //     $sql = "SELECT giftcategory.*
    //     FROM giftcategory WHERE giftcategory.giftcategory = $target";
    //     $sqlCountAll = "SELECT COUNT(giftcategory.giftcategory) AS totalItems FROM giftcategory";
    //     $records = $go_ncadb->ncaretrieve($sql, "otsdev");
    //     $recordsArray = $this->ncaArrayConverter($records);

    //     $rtn = array(
    //         "resCode" => "1",
    //         "resMsg" => "Successfully",
    //         "data" => $recordsArray,
    //     );
    //     return json_encode($rtn);
    // }

    // public function updateCategorySpecificId($par_target, $par_name, $par_nameen, $par_icon, $par_active, $par_userId, $par_usernm)
    // {
    //     global $go_ncadb;
    //     $date = date('Y-m-d H:i:s');
    //     $updateSql = new SqlBuilder();
    //     $updateSql->SetTableName("giftcategory");
    //     $ii = 0;
    //     $o_obj = null;

    //     $o_obj[$ii++] = new TField("giftcategory_name", iconv('utf-8', 'tis-620', $par_name), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_name", iconv('utf-8', 'tis-620', $par_name), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_name", iconv('utf-8', 'tis-620', $par_name), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_icon", $par_icon, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_active", $par_active, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_modidspid", $par_userId, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_modidspnm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_moddatetime", $date, "string");
    //     $updateSql->SetField($o_obj);
    //     $updateSql->SetWhereClause("giftcategory = $par_target");
    //     $queryUpdate = $updateSql->UpdateSql();

    //     $result = $go_ncadb->ncaexec($queryUpdate, "otsdev");
    //     if ($result) {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "resMsg" => "Successfully",
    //         );
    //     } else {
    //         $go_ncadb->rollback();
    //         $jsonRtn = array(
    //             "resCocd" => "0",
    //             "resMsg" => $result,
    //         );
    //     }

    //     return json_encode($jsonRtn);
    // }

    // public function newCategory($par_name, $par_nameen, $par_icon, $par_active, $par_userId, $par_usernm)
    // {
    //     global $go_ncadb;
    //     $date = date('Y-m-d H:i:s');
    //     $InsertSql = new SqlBuilder();
    //     $InsertSql->SetTableName("giftcategory");
    //     $ii = 0;
    //     $o_obj = null;

    //     $o_obj[$ii++] = new TField("giftcategory_name", iconv('utf-8', 'tis-620', $par_name), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_nameen", iconv('utf-8', 'tis-620', $par_nameen), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_icon", $par_icon, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_active", $par_active, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_recdspid", $par_userId, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_recdsnm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_recdatetime", $date, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_modidspid", $par_userId, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_modidspnm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_moddatetime", $date, "string");

    //     $InsertSql->SetField($o_obj);
    //     $queryInsert = $InsertSql->InsertSql();

    //     $result = $go_ncadb->ncaexec($queryInsert, "otsdev");
    //     if ($result) {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "resMsg" => "Successfully",
    //         );
    //     } else {
    //         $go_ncadb->rollback();
    //         $jsonRtn = array(
    //             "resCocd" => "0",
    //             "resMsg" => $result,
    //         );
    //     }

    //     return json_encode($jsonRtn);
    // }

    // public function switchCategoryActive($target, $userId, $usernm)
    // {
    //     global $go_ncadb;
    //     $resCode = '0';
    //     $datetime = date('Y-m-d H:i:s');
    //     $usernm = iconv('utf-8', 'tis-620', $usernm);
    //     $sql = "UPDATE giftcategory
    //             SET giftcategory_active = CASE
    //             WHEN giftcategory_active = 0 THEN 1
    //             WHEN giftcategory_active = 1 THEN 0
    //             ELSE giftcategory_active END,
    //             giftcategory_modidspid = '$userId',
    //             giftcategory_modidspnm = '$usernm',
    //             giftcategory_moddatetime = '$datetime'
    //             WHERE giftcategory = $target;";
    //     $result = $go_ncadb->ncaexec($sql, "otsdev");
    //     if ($result) {
    //         $resCode = '1';
    //         $resMsg = 'successfully';
    //     } else {
    //         $go_ncadb->rollback();
    //         $resCode = '0';
    //         $resMsg = 'unsuccessfully';
    //     }
    //     $updateResult = array(
    //         'resCode' => $resCode,
    //         'resMsg' => $resMsg,
    //         'action' => 'switch',
    //         'target' => $target
    //     );
    //     return json_encode($updateResult);
    // }

    // public function delCategory($target)
    // {
    //     global $go_ncadb;
    //     $resCode = '0';

    //     $sqlBuilder = new SqlBuilder();
    //     $sqlBuilder->SetTableName("giftcategory");
    //     $sqlBuilder->SetWhereClause("giftcategory = $target");
    //     $query = $sqlBuilder->DeleteSql();

    //     if ($go_ncadb->ncaexec($query, "otsdev")) {
    //         $result = true;
    //     } else {
    //         $go_ncadb->rollback();
    //         $result = false;
    //     }

    //     if ($result) {
    //         $resCode = '1';
    //         $resMsg = 'successfully';
    //     } else {
    //         $resCode = '0';
    //         $resMsg = 'unsuccessfully';
    //     }

    //     $deleteResult = array(
    //         'resCode' => $resCode,
    //         'resMsg' => $resMsg,
    //         'action' => 'delete',
    //         'target' => $target,
    //     );
    //     return json_encode($deleteResult);
    // }

    // public function editCategory($par_target,$par_name, $par_nameen, $par_icon, $par_active, $par_userId, $par_usernm)
    // {
    //     global $go_ncadb;
    //     $date = date('Y-m-d H:i:s');
    //     $updateSql = new SqlBuilder();
    //     $updateSql->SetTableName("giftcategory");
    //     $ii = 0;
    //     $o_obj = null;

    //     $o_obj[$ii++] = new TField("giftcategory_name", iconv('utf-8', 'tis-620', $par_name), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_nameen", iconv('utf-8', 'tis-620', $par_nameen), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_icon", $par_icon, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_active", $par_active, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_modidspid", $par_userId, "string");
    //     $o_obj[$ii++] = new TField("giftcategory_modidspnm", iconv('utf-8', 'tis-620', $par_usernm), "string");
    //     $o_obj[$ii++] = new TField("giftcategory_moddatetime", $date, "string");

    //     $updateSql->SetField($o_obj);
    //     $updateSql->SetWhereClause("giftcategory = $par_target");
    //     $queryUpdate = $updateSql->UpdateSql();

    //     $result = $go_ncadb->ncaexec($queryUpdate, "otsdev");
    //     if ($result) {
    //         $jsonRtn = array(
    //             "resCode" => "1",
    //             "resMsg" => "Successfully",
    //         );
    //     } else {
    //         $go_ncadb->rollback();
    //         $jsonRtn = array(
    //             "resCocd" => "0",
    //             "resMsg" => $result,
    //         );
    //     }
    //     return json_encode($jsonRtn);
    // }
}
