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

try {
    $dbh = new PDO('mysql:host=localhost;dbname=hittest', 'root', 'root');

    $query = "SELECT a.id, ad.content, ad.lang, ad.status
    FROM articles a
    INNER JOIN authors au ON a.author_id=au.id
    INNER JOIN articles_data ad ON ad.pid = a.id
    WHERE ad.lang = :language
    ORDER BY date_added DESC
    LIMIT 5";

    if (!$stm = $dbh->prepare($query)) {
        throw new Exception('error in PDO::prepare()');
    }

    if (!$stm->execute(array(':language' => $language))) {
        throw new Exception('error in PDO::execute()');
    }

    $resultData = array();

    while ($row = $stm->fetch()) {
        $row['content'] = json_decode($row['content']);
        $row['status'] = $statuses[$row['status']];
        $resultData[] = $row;
    }

} catch (Exception $e) {
    print 'Failed to connect to database: '. $e->getMessage();
    die();
}

print json_encode($resultData);
