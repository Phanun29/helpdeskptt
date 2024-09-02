<?php
// Include the database configuration file
include "config.php";

// Set the timezone to Bangkok
date_default_timezone_set('Asia/Bangkok');

// Autoload PhpSpreadsheet library
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Main function to send Telegram messages and generate Excel reports
function sendTelegramMessageAndGenerateExcel()
{
    // Access the global database connection variable
    global $conn;

    // Calculate the previous month and year
    $previousMonth = date('m', strtotime('first day of last month'));
    $previousYear = date('Y', strtotime('first day of last month'));

    // Query to fetch station information
    $station_query = "SELECT station_id, station_type, station_name, telegram_chat_id FROM tbl_station";
    $station_result = $conn->query($station_query);

    // Directory where Excel files will be saved
    $directory = 'excel_files/';

    // Check if the directory exists, if not, create it
    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    // Loop through each station to generate reports and send them via Telegram
    while ($station_row = $station_result->fetch_assoc()) {
        $station_id = $station_row['station_id'];
        $station_name = $station_row['station_name'];
        $station_type = $station_row['station_type'];
        $chat_ids_string = $station_row['telegram_chat_id'];

        // Query to fetch ticket data for the station for the previous month
        $ticket_query = "SELECT t.*
            FROM tbl_ticket t
            WHERE t.station_id = '$station_id' 
            AND MONTH(t.ticket_open) = '$previousMonth' 
            AND YEAR(t.ticket_open) = '$previousYear'
            GROUP BY t.ticket_id
            ORDER BY t.ticket_open ASC";

        $ticket_result = $conn->query($ticket_query);

        // Create a new Excel spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set column headers in the Excel sheet
        $headers = [
            'A1' => 'Ticket ID',
            'B1' => 'Station ID',
            'C1' => 'Station Name',
            'D1' => 'Station Type',
            'E1' => 'Province',
            'F1' => 'Issue Description',
            'G1' => 'Issue Type',
            'H1' => 'SLA Category',
            'I1' => 'Status',
            'J1' => 'Ticket Open',
            'K1' => 'Ticket on Hold',
            'L1' => 'Ticket In Progress',
            'M1' => 'Ticket Pending Vendor',
            'N1' => 'Ticket Close',
            'O1' => 'Ticket Time',
            'P1' => 'Comment'
        ];

        // Loop through the headers and set them in the sheet
        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Populate the Excel sheet with ticket data
        $rowNum = 2;
        while ($ticket_row = $ticket_result->fetch_assoc()) {
            $sheet->setCellValue('A' . $rowNum, $ticket_row['ticket_id']);
            $sheet->setCellValue('B' . $rowNum, $ticket_row['station_id']);
            $sheet->setCellValue('C' . $rowNum, $ticket_row['station_name']);
            $sheet->setCellValue('D' . $rowNum, $ticket_row['station_type']);
            $sheet->setCellValue('E' . $rowNum, $ticket_row['province']);
            $sheet->setCellValue('F' . $rowNum, $ticket_row['issue_description']);
            $sheet->setCellValue('G' . $rowNum, $ticket_row['issue_type']);
            $sheet->setCellValue('H' . $rowNum, $ticket_row['SLA_category']);
            $sheet->setCellValue('I' . $rowNum, $ticket_row['status']);
            $sheet->setCellValue('J' . $rowNum, $ticket_row['ticket_open']);
            $sheet->setCellValue('K' . $rowNum, $ticket_row['ticket_on_hold']);
            $sheet->setCellValue('L' . $rowNum, $ticket_row['ticket_in_progress']);
            $sheet->setCellValue('M' . $rowNum, $ticket_row['ticket_pending_vendor']);
            $sheet->setCellValue('N' . $rowNum, $ticket_row['ticket_close']);
            $sheet->setCellValue('O' . $rowNum, $ticket_row['ticket_time']);
            $sheet->setCellValue('P' . $rowNum, $ticket_row['comment']);
            $rowNum++;
        }

        // Define the filename and path for the Excel file
        $filename = 'Station_id_' . $station_id . '_Status_' . $previousMonth . '_' . $previousYear . '.xlsx';
        $filePath = $directory . $filename;

        // Save the Excel file to the specified path
        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save($filePath);
            echo "File saved successfully at $filePath\n";
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            echo 'Error saving file: ', $e->getMessage(), "\n";
            continue;
        }

        // Query to count the number of tickets by status for the station
        $status_query = "SELECT status, COUNT(*) as count 
            FROM tbl_ticket
            WHERE station_id = '$station_id' 
              AND MONTH(ticket_open) = '$previousMonth' 
              AND YEAR(ticket_open) = '$previousYear'
            GROUP BY status";

        // Initialize status counts
        $status_counts = [
            'Open' => 0,
            'On Hold' => 0,
            'In Progress' => 0,
            'Pending Vendor' => 0,
            'Close' => 0
        ];

        // Execute the status query and update the status counts
        $result_status = $conn->query($status_query);
        while ($row = $result_status->fetch_assoc()) {
            $status_counts[$row['status']] = $row['count'];
        }

        // Extract individual status counts
        $open = $status_counts['Open'];
        $onhold = $status_counts['On Hold'];
        $inprogress = $status_counts['In Progress'];
        $pending_vendor = $status_counts['Pending Vendor'];
        $close = $status_counts['Close'];

        // Fetch the Telegram bot token for the current station type
        $bot_telegram_query = "SELECT token FROM tbl_telegram_bot WHERE station_type = '$station_type'";
        $bot_result = $conn->query($bot_telegram_query);
        $bot_row = $bot_result->fetch_assoc();
        $botToken = $bot_row['token'];

        // Split the chat IDs into an array
        $chatIds = explode(',', $chat_ids_string);

        // Compose the message to be sent via Telegram
        $currentDate = date('d-m-Y');
        $message = "Status for $station_type\n\n(Station ID: $station_id)\n\n(Station name: $station_name)\n\nfor the month of $previousMonth-$previousYear:\n\n\n Open: $open \n On Hold: $onhold \n In Progress: $inprogress \n Pending Vendor: $pending_vendor \n Close: $close";

        // Telegram API URL for sending documents
        $url = "https://api.telegram.org/bot$botToken/sendDocument";

        // Loop through each chat ID to send the Excel file
        foreach ($chatIds as $chatId) {
            $data = [
                'chat_id' => trim($chatId),
                'caption' => $message,
                'document' => new CURLFile($filePath)
            ];

            // Set cURL options for sending the file
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

            echo "File sent to chat ID: $chatId\n";
            echo "Response: $response\n";

            // Delete the file from the server if the response indicates success or rate limit error
            if (strpos($response, '"ok":true') !== false || strpos($response, '"error_code":429') !== false) {
                unlink($filePath);
                echo "File deleted from server: $filePath\n";
            }

            // Handle rate limiting by retrying the request after the specified wait time
            if (strpos($response, '"error_code":429') !== false) {
                $retryAfter = 1;
                if (preg_match('/"retry_after":(\d+)/', $response, $matches)) {
                    $retryAfter = (int)$matches[1];
                }
                echo "Rate limit hit. Waiting $retryAfter seconds before retrying...\n";
                sleep($retryAfter);
                $response = curl_exec($ch);
                echo "Response after retry: $response\n";
            }
        }
    }

    // Close the database connection
    $conn->close();
}

// Call the function to execute the script
sendTelegramMessageAndGenerateExcel();
