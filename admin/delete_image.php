<?php
// include "config.php"; // Include your database connection configuration

// if ($_SERVER['REQUEST_METHOD'] == 'POST') {
//     $image = $_POST['image'] ?? null;
//     $ticket_id = $_POST['ticket_id'] ?? null;

//     if ($image && $ticket_id) {
//         // Remove the image file
//         if (unlink($image)) {
//             // Update the database to remove the image path
//             $ticket_query = "SELECT issue_image FROM tbl_ticket WHERE id = ?";
//             $stmt_ticket = $conn->prepare($ticket_query);
//             $stmt_ticket->bind_param('i', $ticket_id);
//             $stmt_ticket->execute();
//             $ticket_result = $stmt_ticket->get_result();

//             if ($ticket_result->num_rows > 0) {
//                 $ticket = $ticket_result->fetch_assoc();
//                 $image_paths = explode(',', $ticket['issue_image']);
//                 $new_image_paths = array_filter($image_paths, function ($path) use ($image) {
//                     return $path != $image;
//                 });
//                 $updated_image_paths = implode(',', $new_image_paths);

//                 $update_query = "UPDATE tbl_ticket SET issue_image = ? WHERE id = ?";
//                 $stmt_update = $conn->prepare($update_query);
//                 $stmt_update->bind_param('si', $updated_image_paths, $ticket_id);
//                 $stmt_update->execute();
//                 $stmt_update->close();
//             }
//             $stmt_ticket->close();
//         }
//     }
//     $conn->close();
// }
