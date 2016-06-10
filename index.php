<?php 
function curlGetPostJson($url, $post = array())
{
    $url = trim($url);
    if (is_array($post) && count($post)) {
        $data = json_encode($post);
    } else {
        $data = $post;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data))
    );

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

$hashtag = 'obamavietnam';
$page = 1;  
$url = 'http://127.0.0.1:9200/muabannhanh/product/_search?pretty=1';

$request = '#' . $hashtag;
$content = 1;
$limit = 2;
$_query = '
    "query": {
       "bool": {
           "should": [
               { "match_phrase": { "description": "'. $request .'" } }
           ]
       }
    },';
$queryString = '
{
    "query": {
        "filtered": {
            '. $_query .'
            "filter" : {
                "bool": {
                    "must": [
                        '. $filter .'
                        {"term" : {"status_value": "1"} },
                        {"term" : {"is_shown": "1"} }
                    ]
                }
            }
        }
    },
    "sort" : [
        { "time_updated_at" : {"order" : "desc"} }
    ],
    "size": '. $limit .',
    "from": '. ($limit * $page - $limit) .'
}';

$r = curlGetPostJson($url, $queryString);
echo $r;

$response = json_decode($r, true);
