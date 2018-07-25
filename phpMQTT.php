 <?php
require(“vendor/autoload.php”);
use \LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot;

require(“phpMQTT.php”);

$mqtt = new phpMQTT(“m10.cloudmqtt.com”, 18468, “phpMQTT Pub Example”); //เปลี่ยน www.yourmqttserver.com ไปที่ mqtt server ที่เราสมัครไว้นะครับ

$token = “Sz2pqA7iHWmE80uHtekGhSNIAd9RmCK1Kog+cOMRZrcl79OQQVY/btWxHuE3cMHaGfpVMxoWb9eQFKi+SzGrhsAA1lEJ0aOoRXCxg5M/9zVcmjDWZSeupX/zfpfnZPWizXiCiY431aWkV2c4e+s6ZQdB04t89/1O/w1cDnyilFU=”; //นำ token ที่มาจาก line developer account ของเรามาใส่ครับ

$httpClient = new CurlHTTPClient($token);
$bot = new LINEBot($httpClient, [‘channelSecret’ => $token]);
// webhook
$jsonStr = file_get_contents(‘php://input’);
$jsonObj = json_decode($jsonStr);
print_r($jsonStr);
foreach ($jsonObj->events as $event) {
if(‘message’ == $event->type){
// debug
//file_put_contents(“message.json”, json_encode($event));
$text = $event->message->text;

if (preg_match(“/สวัสดี/”, $text)) {
$text = “มีอะไรให้จ่าวิสรับใช้ครับ”;
}

if (preg_match(“/เปิดทีวี/”, $text)) {     //หากในแชตที่ส่งมามีคำว่า เปิดทีวี ก็ให้ส่ง mqtt ไปแจ้ง server เราครับ
if ($mqtt->connect()) {
$mqtt->publish(“/ESP/REMOTE”,”TV”); // ตัวอย่างคำสั่งเปิดทีวีที่จะส่งไปยัง mqtt server
$mqtt->close();
}
$text = “เปิดทีวีให้แล้วคร้าบบบบ”;
}
if (preg_match(“/ปิดทีวี/”, $text) and !preg_match(“/เปิดทีวี/”, $text)) {
if ($mqtt->connect()) {
$mqtt->publish(“/ESP/REMOTE”,”TV”);
$mqtt->close();
}
$text = “จ่าปิดทีวีให้แล้วนะครับ!!”;
}
$response = $bot->replyText($event->replyToken, $text); // ส่งคำ reply กลับไปยัง line application

}
}

?>
