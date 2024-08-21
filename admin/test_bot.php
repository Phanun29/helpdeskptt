<?php


include "config.php";
$bot_telegram_query = "SELECT * FROM tbl_telegram_bot WHERE role = '1'";
$bot_result = $conn->query($bot_telegram_query);
$row = $bot_result->fetch_assoc();

$token = $row['token'];
$chat_id = $row['chat_id'];
$botToken =  $token;

echo "[".$chat_id."]";