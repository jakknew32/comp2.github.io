<?php
// อนุญาตให้เรียกข้ามโดเมนได้ (CORS) และกำหนดประเภทเนื้อหาเป็น JSON
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// ใช้ API Key ฟรีของ TheSportsDB (ถ้ามีคีย์ส่วนตัวระดับ Patreon สามารถเปลี่ยนตรงนี้ได้)
$apiKey = '3'; 

$action = isset($_GET['action']) ? $_GET['action'] : '';

// ฟังก์ชันสำหรับยิง cURL ไปดึงข้อมูล
function fetchUrl($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // ถ้าดึงไม่ได้ ให้ส่ง JSON เปล่ากลับไปเพื่อไม่ให้เว็บพัง
    if (!$response) return json_encode(["events" => null, "teams" => null]);
    return $response;
}

switch ($action) {
    case 'live':
        // ดึงผลบอลสด (API v2 ของ TheSportsDB)
        $url = "https://www.thesportsdb.com/api/v2/json/{$apiKey}/livescore.php?s=Soccer";
        echo fetchUrl($url);
        break;

    case 'today':
        // ดึงโปรแกรมการแข่งขันวันนี้
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        $url = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/eventsday.php?d={$date}&s=Soccer";
        echo fetchUrl($url);
        break;

    case 'team':
        // ดึงข้อมูลโลโก้ทีม
        $teamId = isset($_GET['teamId']) ? $_GET['teamId'] : '';
        if (!$teamId) {
            echo json_encode(["teams" => null]);
            break;
        }
        $url = "https://www.thesportsdb.com/api/v1/json/{$apiKey}/lookupteam.php?id={$teamId}";
        echo fetchUrl($url);
        break;

    default:
        // ถ้า action ไม่ถูกต้อง
        echo json_encode(["error" => "Invalid action"]);
        break;
}
?>