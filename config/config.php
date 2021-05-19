<?php
/* Database connection data */
$user = "root";
$password = "1234qwer";
$dbname = "nestedset";
$host = "localhost";

/* jsonResponseStructure */
$jsonResponseStructure = array(
    'nodes' => array(
                  'node_id'         => '',
                  'name'            => '',
                  'children_count'  => '',
              ),
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