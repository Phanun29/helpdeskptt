<?php

include "config.php";
date_default_timezone_set('Asia/Bangkok');

function sendTelegramMessage()
{
  global $conn;

  // Query to get the status counts
  $status_query = "SELECT status, COUNT(*) as count FROM tbl_ticket GROUP BY status";
  $status_counts = [
    'Open' => 0,
    'On Hold' => 0,
    'In Progress' => 0,
    'Pending Vendor' => 0,
    'Close' => 0
  ];
  $result_status = $conn->query($status_query);
  while ($row = $result_status->fetch_assoc()) {
    $status_counts[$row['status']] = $row['count'];
  }

  $open = $status_counts['Open'];
  $onhold = $status_counts['On Hold'];
  $inprogress = $status_counts['In Progress'];
  $pending_vendor = $status_counts['Pending Vendor'];
  $close = $status_counts['Close'];

  $bot_telegram_query = "SELECT * FROM tbl_telegram_bot WHERE role = '1'";
  $bot_result = $conn->query($bot_telegram_query);
  $row = $bot_result->fetch_assoc();

  $token = $row['token'];

  $botToken =  $token;

  $chat_ids_string = $row['chat_id'];
  $botToken = $token;

  // Split chat IDs into an array
  $chatIds = explode(',', $chat_ids_string);

  $currentDate = date('d-m-Y H:i:s');
  $message = "Status all at $currentDate:\n Open: $open \n On Hold: $onhold \n In Progress: $inprogress \n Pending Vendor: $pending_vendor \n Close: $close";

  $url = "https://api.telegram.org/bot$botToken/sendMessage";

  foreach ($chatIds as $chatId) {
    $data = [
      'chat_id' => $chatId,
      'text' => $message
    ];

    $options = [
      CURLOPT_URL => $url,
      CURLOPT_POST => true,
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_RETURNTRANSFER => true
    ];

    $ch = curl_init();
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    curl_close($ch);

    echo "Message sent to chat ID: $chatId\n";
    echo "Response: $response\n";
  }
}

sendTelegramMessage();
