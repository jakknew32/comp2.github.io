<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$API_KEY = '123';
$BASE = "https://www.thesportsdb.com/api/v1/json/{$API_KEY}";

$action = $_GET['action'] ?? 'today';
$league = $_GET['league'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$teamId = $_GET['teamId'] ?? '';

function fetchUrl($url) {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 10,
            'header' => "User-Agent: Mozilla/5.0\r\n"
        ]
    ]);
    $res = @file_get_contents($url, false, $ctx);
    return $res ? json_decode($res, true) : null;
}

$leagues = [
    ['id' => '4328', 'name' => 'English Premier League'],
    ['id' => '4480', 'name' => 'UEFA Champions League'],
    ['id' => '4335', 'name' => 'Spanish La Liga'],
    ['id' => '4331', 'name' => 'German Bundesliga'],
    ['id' => '4332', 'name' => 'Italian Serie A'],
    ['id' => '4334', 'name' => 'French Ligue 1'],
    ['id' => '4390', 'name' => 'Thai Premier League'],
    ['id' => '4346', 'name' => 'Japanese J1 League'],
    ['id' => '4356', 'name' => 'Australian A-League'],
];

if ($action === 'live') {
    $data = fetchUrl("{$BASE}/livescore.php?s=Soccer");
    echo json_encode($data ?? ['events' => []]);

} elseif ($action === 'today') {
    $all = [];
    foreach ($leagues as $l) {
        $data = fetchUrl("{$BASE}/eventsday.php?d={$date}&id={$l['id']}");
        if (!empty($data['events'])) {
            foreach ($data['events'] as $e) {
                $e['strLeague'] = $e['strLeague'] ?? $l['name'];
                $all[] = $e;
            }
        }
        usleep(200000); // 200ms delay ป้องกัน rate limit
    }
    usort($all, fn($a,$b) => strcmp(($a['strTime']??''), ($b['strTime']??'')));
    echo json_encode(['events' => $all]);

} elseif ($action === 'team') {
    $data = fetchUrl("{$BASE}/lookupteam.php?id={$teamId}");
    echo json_encode($data ?? ['teams' => []]);

} else {
    echo json_encode(['error' => 'unknown action']);
}
