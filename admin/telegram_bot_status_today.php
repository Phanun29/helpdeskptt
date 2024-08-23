<?php
include "config.php";
date_default_timezone_set('Asia/Bangkok');

function sendTelegramMessage()
{
    global $conn;

    // Retrieve all stations
    $station_query = "SELECT station_id, station_type, station_name, chat_id FROM tbl_station";
    $station_result = $conn->query($station_query);

    while ($station_row = $station_result->fetch_assoc()) {
        $station_id = $station_row['station_id'];
        $station_name = $station_row['station_name'];
        $station_type = $station_row['station_type'];
        $chat_ids_string = $station_row['chat_id'];

        // Query to get the status counts for the current station
        $status_query = "SELECT status, COUNT(*) as count 
        FROM tbl_ticket
        WHERE station_id = '$station_id' 
        AND MONTH(ticket_open) = MONTH(CURDATE()) 
        AND YEAR(ticket_open) = YEAR(CURDATE())
        GROUP BY status";

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

        // Select the bot token based on station type
        $bot_telegram_query = "SELECT token FROM tbl_telegram_bot WHERE station_type = '$station_type'";
        $bot_result = $conn->query($bot_telegram_query);
        $bot_row = $bot_result->fetch_assoc();
        $botToken = $bot_row['token'];

        $chatIds = explode(',', $chat_ids_string);  // Split chat IDs into an array

        $currentDate = date('d-m-Y');
        $message = "Status for $station_type\n\n(Station ID: $station_id)  \n\n(Station name: $station_name) \n\nthis month at $currentDate:\n\n\n Open: $open \n On Hold: $onhold \n In Progress: $inprogress \n Pending Vendor: $pending_vendor \n Close: $close";

        $url = "https://api.telegram.org/bot$botToken/sendMessage";

        foreach ($chatIds as $chatId) {
            $data = [
                'chat_id' => trim($chatId),  // Trim any whitespace
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

            // Handle rate limiting by adding a short delay
            if (strpos($response, '"error_code":429') !== false) {
                $retryAfter = 1; // Shorter retry time
                if (preg_match('/"retry_after":(\d+)/', $response, $matches)) {
                    $retryAfter = (int)$matches[1];
                }
                echo "Rate limit hit. Retrying after $retryAfter seconds.\n";
                sleep($retryAfter); // Wait for the specified time before retrying
            } else {
                usleep(500000); // Shorter delay between successful requests (0.5 second)
            }
        }
    }
}

sendTelegramMessage();
