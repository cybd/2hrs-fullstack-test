<?php
// statuses: red - yellow - green - red

$vars = explode('/', $_SERVER['REQUEST_URI']);

if (!empty($vars[2])) {
    $language = $vars[2];
} else {
    $language = 'en';
}

$statuses = [
    '0' => 'images/icons/eye-red.gif',
    '1' => 'images/icons/eye-yellow.gif',
    '2' => 'images/icons/eye-green.gif',
];

header('Content-type: application/json');

$mysqli = new mysqli("localhost", "root", "root", "hittest");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$query = "SELECT a.id, ad.content, ad.lang, ad.status
FROM articles a
INNER JOIN authors au ON a.author_id=au.id
INNER JOIN articles_data ad ON ad.pid = a.id
WHERE ad.lang = '$language'
ORDER BY date_added DESC
LIMIT 5";

$resultData = [];
$res = $mysqli->query($query);
while ($row = $res->fetch_assoc()) {
    $row['content'] = json_decode($row['content']);
    $row['status'] = $statuses[$row['status']];
    $resultData[] = $row;
}

print json_encode($resultData);
