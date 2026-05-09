<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// แนะนำให้สมัครสมาชิกที่ thesportsdb.com เพื่อรับ API Key ส่วนตัว (ราคาไม่แพง)
// หากใช้ '3' จะเป็น Key สาธารณะสำหรับทดสอบ ซึ่งอาจจะดึงข้อมูลได้ไม่ครบทุกรายการ
$API_KEY = '3'; 
$BASE = "https://www.thesportsdb.com/api/v1/json/{$API_KEY}";

$action = $_GET['action'] ?? 'today';
$league = $_GET['league'] ?? '';
$date = $_GET['date'] ?? date('Y-m-d');
$teamId = $_GET['teamId'] ?? '';

function fetchUrl($url) {
    $ctx = stream_context_create([
        'http' => [
            'timeout' => 15, // เพิ่ม timeout
            'header' => "User-Agent: FootLiveApp/1.0\r\n"
        ]
    ]);
    $res = @file_get_contents($url, false, $ctx);
    if ($res === false) return null;
    return json_decode($res, true);
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
    // กรณีใช้ Free Key (123 หรือ 3) บางครั้ง API จะไม่ยอมให้ loop ดึงหลายลีกพร้อมกัน
    // แนะนำให้ดึงทีละลีก หรือใช้ API สำหรับดึงตารางรวมถ้ามีสิทธิ์
    foreach ($leagues as $l) {
        $url = "{$BASE}/eventsday.php?d={$date}&id={$l['id']}";
        $data = fetchUrl($url);
        
        if (!empty($data['events'])) {
            foreach ($data['events'] as $e) {
                $e['strLeague'] = $e['strLeague'] ?? $l['name'];
                $all[] = $e;
            }
        }
        // หน่วงเวลาเล็กน้อยเพื่อป้องกันการโดนเตะออกจากเซิร์ฟเวอร์
        usleep(300000); 
    }
    
    // เรียงตามเวลาแข่ง
    usort($all, function($a, $b) {
        $timeA = $a['strTime'] ?? '23:59:00';
        $timeB = $b['strTime'] ?? '23:59:00';
        return strcmp($timeA, $timeB);
    });
    
    echo json_encode(['events' => $all]);

} elseif ($action === 'team') {
    $data = fetchUrl("{$BASE}/lookupteam.php?id={$teamId}");
    echo json_encode($data ?? ['teams' => []]);

} else {
    echo json_encode(['error' => 'unknown action']);
}