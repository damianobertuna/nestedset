<?php
/* Database connection data */
$user = "root";
$password = "1234qwer";
$dbname = "nestedset";
$host = "localhost";

/* jsonResponseStructure */
$jsonResponseStructure = array(
    'rootNodesNumber' => 0,
    'nodes' => array(),
    'error' => ''
);

/* Error dictionary */
$errorDictionary = array(
    1 => 'Invalid node id',
    2 => 'Missing mandatory params',
    3 => 'Invalid page number request',
    4 => 'Invalid page size requested',
    5 => 'Database connection error'
);