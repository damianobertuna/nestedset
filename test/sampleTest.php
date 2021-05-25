<?php

$exampleRequests = array(
"Wrong node_id"                             => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=dd&language=italian",
"Wrong node_id (empty node_id)"             => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=&language=italian",
"Wrong language string"                     => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5&language=italiand",
"Wrong language string (empty language)"    => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5&language=italiand",
"Missing mandatory params (language)"       => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5",
"Missing mandatory params (node_id)"        => $_SERVER["HTTP_HOST"]."/nestedset/api.php?language=italian",
"Invalid page number"                       => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5&language=italian&search_keyword=&page_num=123&page_size=123",
"Invalid page size (not a number)"          => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5&language=italian&search_keyword=&page_num=2&page_size=ddd",
"Correct data provided"                     => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5&language=italian&search_keyword=&page_num=&page_size=",
"Search keyword provided"                   => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5&language=italian&search_keyword=supp&page_num=&page_size=",
"Correct pagination provided"               => $_SERVER["HTTP_HOST"]."/nestedset/api.php?node_id=5&language=italian&search_keyword=&page_num=1&page_size=4"
);

foreach ($exampleRequests as $requestName => $url) {
    $result = curl($url);
    echo "<strong>Test name</strong>: ".$requestName."<br />";
    echo "<strong>Url called</strong>: ".$url."<br />";
    echo "<strong>Result</strong>: ".$result."<br /><br /><br />";
}

function curl($url) {
    $curlSES=curl_init(); 
    curl_setopt($curlSES,CURLOPT_URL,$url);
    curl_setopt($curlSES,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($curlSES,CURLOPT_HEADER, false); 
    $result=curl_exec($curlSES);
    curl_close($curlSES);
    return $result;
}