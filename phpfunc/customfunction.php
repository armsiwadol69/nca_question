<?php

function convertArrayToUtf8($inputArray)
{
    if (is_array($inputArray)) {
        $resultArray = array();
        foreach ($inputArray as $key => $value) {
            $resultArray[$key] = ncaiconvutf8($value, 'en');
        }
        return $resultArray;
    } else {
        throw new InvalidArgumentException("Input must be an associative array.");
    }
}

function ncaArrayConverter($par_array)
{
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



function checkGiftImage($id, $img)
{
    if (!empty($img)) {
        $html = '<div class="swiper-slide">
                    <img src="../storage/' . $id . '/' . $img . '" />
                </div>';
    } else {
        $html = '<div class="swiper-slide">
                    <img src="../assets/images/noImg.png" />
                 </div>';
    }
    return $html;
}

function createDeleteImageButton($id, $order, $img)
{
    if ($order == '1') {
        return;
    }
    //echo "<button class=\"btn btn-sm btn-danger float-end\" onclick=\"callDeleteImage('','','');\"><i class=\"bi bi-trash\"></i></button>";
    if (!empty($img)) {
        $html = "<button type=\"button\" class=\"btn btn-sm btn-danger float-end\" onclick=\"callDeleteImage('$id','$order','$img');\"><i class=\"bi bi-trash\"></i></button>";
    } else {
        $html = "<button type=\"button\" class=\"btn btn-sm btn-danger disabled float-end\" disabled><i class=\"bi bi-trash\"></i></button>";
    }
    return $html;
}

function ArrayConverter($par_array)
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

function convertToThaiDate($date) {
    $timestamp = strtotime($date); // Convert the date to a timestamp

    // Define the Thai Buddhist Era (พ.ศ.) offset
    $thaiYearOffset = 543;

    $thaiYear = date('Y', $timestamp) + $thaiYearOffset; // Convert year to Thai Buddhist Era (พ.ศ.)
    
    // Define arrays for month names in Thai
    $thaiMonths = array(
        '01' => 'มกราคม',
        '02' => 'กุมภาพันธ์',
        '03' => 'มีนาคม',
        '04' => 'เมษายน',
        '05' => 'พฤษภาคม',
        '06' => 'มิถุนายน',
        '07' => 'กรกฎาคม',
        '08' => 'สิงหาคม',
        '09' => 'กันยายน',
        '10' => 'ตุลาคม',
        '11' => 'พฤศจิกายน',
        '12' => 'ธันวาคม',
    );

    $day = date('j', $timestamp);
    $month = $thaiMonths[date('m', $timestamp)];
    
    $formattedDate = $day . ' ' . $month . ' ' . $thaiYear;
    
    return $formattedDate;
}


function sortDates($dateString) {
    // Convert the date string to an array
    $dateStrings = explode(',', $dateString);

    // Sort the date strings using a custom comparison function
    usort($dateStrings, 'dateCompare');

    // Return the formatted string
    return implode(',', $dateStrings);
}

// Custom comparison function
function dateCompare($a, $b) {
    // Convert strings to DateTime objects for comparison
    $dateA = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $a))));
    $dateB = new DateTime(date('Y-m-d', strtotime(str_replace('/', '-', $b))));

    // Compare DateTime objects
    if ($dateA == $dateB) {
        return 0;
    }
    return ($dateA < $dateB) ? -1 : 1;
}

function converterUTF8($text)
{
    if (empty($text)) {
        return ;
    }
    
    return iconv('tis-620', 'utf-8', $text);

}
?>
