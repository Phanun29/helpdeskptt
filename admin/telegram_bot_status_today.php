<?php
include "config.php";
date_default_timezone_set('Asia/Bangkok');

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

function sendTelegramMessageAndGenerateExcel()
{
    global $conn;

    $previousMonth = date('m', strtotime('first day of last month'));
    $previousYear = date('Y', strtotime('first day of last month'));

    $station_query = "SELECT station_id, station_type, station_name, telegram_chat_id FROM tbl_station";
    $station_result = $conn->query($station_query);

    $directory = 'C:/xampp/htdocs/helpdeskptt/excel_files/';

    if (!file_exists($directory)) {
        mkdir($directory, 0777, true);
    }

    while ($station_row = $station_result->fetch_assoc()) {
        $station_id = $station_row['station_id'];
        $station_name = $station_row['station_name'];
        $station_type = $station_row['station_type'];
        $chat_ids_string = $station_row['telegram_chat_id'];

        // Refined SQL query
        $ticket_query = "SELECT t.*
            
            FROM tbl_ticket t
            WHERE t.station_id = '$station_id' 
            AND MONTH(t.ticket_open) = '$previousMonth' 
            AND YEAR(t.ticket_open) = '$previousYear'
            GROUP BY t.ticket_id
            ORDER BY t.ticket_open ASC";

        $ticket_result = $conn->query($ticket_query);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

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

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

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

        $filename = 'Station_id_' . $station_id . '_Status_' . $previousMonth . '_' . $previousYear . '.xlsx';
        $filePath = $directory . $filename;

        $writer = new Xlsx($spreadsheet);

        try {
            $writer->save($filePath);
            echo "File saved successfully at $filePath\n";
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            echo 'Error saving file: ', $e->getMessage(), "\n";
            continue;
        }

        $status_query = "SELECT status, COUNT(*) as count 
            FROM tbl_ticket
            WHERE station_id = '$station_id' 
              AND MONTH(ticket_open) = '$previousMonth' 
              AND YEAR(ticket_open) = '$previousYear'
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

        $bot_telegram_query = "SELECT token FROM tbl_telegram_bot WHERE station_type = '$station_type'";
        $bot_result = $conn->query($bot_telegram_query);
        $bot_row = $bot_result->fetch_assoc();
        $botToken = $bot_row['token'];

        $chatIds = explode(',', $chat_ids_string);

        $currentDate = date('d-m-Y');
        $message = "Status for $station_type\n\n(Station ID: $station_id)\n\n(Station name: $station_name)\n\nfor the month of $previousMonth-$previousYear:\n\n\n Open: $open \n On Hold: $onhold \n In Progress: $inprogress \n Pending Vendor: $pending_vendor \n Close: $close";

        $url = "https://api.telegram.org/bot$botToken/sendDocument";

        foreach ($chatIds as $chatId) {
            $data = [
                'chat_id' => trim($chatId),
                'caption' => $message,
                'document' => new CURLFile($filePath)
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

            echo "File sent to chat ID: $chatId\n";
            echo "Response: $response\n";

            if (strpos($response, '"ok":true') !== false || strpos($response, '"error_code":429') !== false) {

                unlink($filePath);
                echo "File deleted from server: $filePath\n";
            }

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
    $conn->close();
}

sendTelegramMessageAndGenerateExcel();
