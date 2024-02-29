<?php
header('Content-type: application/json;');
date_default_timezone_set('Asia/Tehran');

function convert_numbers_to_english($text){
    $persian_numbers = array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $english_numbers = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
    return str_replace($persian_numbers, $english_numbers, $text);
}
function remove_spaces($text){
    return str_replace(array(' ', chr(0xC2).chr(0xA0)), '', $text);
}

$curl = curl_init('https://www.bahesab.ir/time/today/');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($curl);
curl_close($curl);


$dom = new DOMDocument();
@$dom->loadHTML($response);

$li_elements = $dom->getElementsByTagName('li');
foreach ($li_elements as $li) {
    $class_attribute = $li->getAttribute('class');
    $text = $li->textContent;
    if ($class_attribute === 'date-1') {
        $solar = preg_replace('/\s*\([^)]*\)\s*/', '', $text);
    }
    if ($class_attribute === 'date-11') {
        $solar_number = remove_spaces(convert_numbers_to_english($text));
        $parts = explode('/', $solar_number);
        $solar_number = sprintf('%04d/%02d/%02d', $parts[2], $parts[1], $parts[0]);
    }
    if ($class_attribute === 'date-2') {
        $ad = remove_spaces(preg_replace('/\s*\([^)]*\)\s*/', '', $text));
    }
    if ($class_attribute === 'date-22') {
        $ad_number = remove_spaces($text);
        $parts = explode('-', $ad_number);
        $ad_number = sprintf('%04d/%02d/%02d', $parts[2], $parts[1], $parts[0]);
    }
    if ($class_attribute === 'date-3') {
        $Lunar = preg_replace('/\s*\([^)]*\)\s*/', '', $text);
    }
    if ($class_attribute === 'date-33') {
        $Lunar_number = remove_spaces(convert_numbers_to_english($text));
        $parts = explode('/', $Lunar_number);
        $Lunar_number = sprintf('%04d/%02d/%02d', $parts[2], $parts[1], $parts[0]);
    }
}

echo json_encode([
    'status' => 200,
    'dev' => ajcode,
    'result' => [
        'time' => date('H:i:s'),
        'hijri' => $solar,
        'hijri-number' => $solar_number,
        'miladi' => $ad,
        'miladi-number' => $ad_number,
        'shamsi' => $Lunar,
        'shamsi-number' => $Lunar_number,
    ]
], 448);

?>
