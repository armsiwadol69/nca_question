<?php

header("Content-Type: application/json");
$data  = array();
    $data['id']      = $id;
    $data['success'] = 1;
    $data['fail']    = 0;



echo json_encode($data);
