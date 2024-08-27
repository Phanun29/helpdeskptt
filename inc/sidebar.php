<?php

// Fetch user details including rules_id and permissions in one query
$user_id = $fetch_info['users_id']; //  user ID

$query_user = " SELECT u.*, r.list_station, r.list_ticket_status, r.list_user_status, r.list_user_rules  , r.list_ticket_assign, r.list_ticket_track , r.list_telegram_bot
                FROM tbl_users u 
                JOIN tbl_users_rules r 
                ON u.rules_id = r.rules_id 
                WHERE u.users_id = $user_id";

$result_user = $conn->query($query_user);

if ($result_user && $result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();

    $listStation = $user['list_station'];
    $listTicket = $user['list_ticket_status'];
    $listUsers = $user['list_user_status'];
    $listUsersRules = $user['list_user_rules'];
    $listTicketAssign = $user['list_ticket_assign'];
    $listTelegramBot = $user['list_telegram_bot'];
    $listTicketTrack = $user['list_ticket_track'];
} else {
    $_SESSION['error_message'] = "User not found or permission check failed.";
}

// Define the current page URL
$current_menu = basename($_SERVER['PHP_SELF']);
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-info ">
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <img src="../img/logo_ptt.png" class="img" alt="User Image" style="margin-left:10px;">
            <div class="info" style="padding-top: 12px;">
                <a href="index.php" class="d-block">PTT (CAMBODIA) Limited</a>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
         with font-awesome or any other icon font library -->
                <li class="nav-item menu-open">

                    <a href="index.php" <?php echo ($current_menu === 'index.php') ? 'class="nav-link active"' : 'class="nav-link"'; ?>>
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <?php
                if ($listTicket) : ?>
                    <li class="nav-item">
                        <a href="ticket.php" class="<?php echo ($current_menu === 'ticket.php' || $current_menu === 'add_ticket.php' || $current_menu === 'edit_ticket.php') ? 'nav-link active' : 'nav-link'; ?>">
                            <i class="nav-icon fa-solid fa-ticket"></i>
                            <p>Ticket</p>
                        </a>

                    </li>
                <?php
                endif;
                if ($listStation) : ?>
                    <li class="nav-item">

                        <a href="station.php" class="<?php echo ($current_menu === 'station.php'  || $current_menu === 'add_station.php' || $current_menu === 'edit_station.php') ? 'nav-link active' : 'nav-link'; ?>">
                            <i class="nav-icon fa-solid fa-gas-pump"></i>
                            <p>
                                Station
                            </p>
                        </a>
                    </li>
                <?php
                endif;
                if ($listUsers) : ?>
                    <li class="nav-item">

                        <a href="users.php" class="<?php echo ($current_menu === 'users.php' || $current_menu === 'add_users.php' || $current_menu === 'edit_users.php') ? 'nav-link active' : 'nav-link'; ?>">
                            <i class="nav-icon fa-solid fa-users"></i>
                            <p>
                                Users
                            </p>
                        </a>
                    </li>
                <?php
                endif;
                if ($listUsersRules) : ?>
                    <li class="nav-item">

                        <a href="users_rules.php" class="<?php echo ($current_menu === 'users_rules.php' || $current_menu === 'add_users_rules.php' || $current_menu === 'edit_users_rules.php') ? 'nav-link active' : 'nav-link'; ?>">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Users Rules
                            </p>
                        </a>
                    </li>
                <?php
                endif;
                if ($listTicketTrack) :
                ?>
                    <li class="nav-item">
                        <a href="track.php" class="<?php echo ($current_menu === 'track.php') ? 'nav-link active' :
                                                        'nav-link'; ?>">
                            <i class="nav-icon fa fa-search" aria-hidden="true"></i>
                            <p>Ticket Track</p>
                        </a>
                    </li>
                <?php
                endif;
                if ($listTelegramBot) :
                ?>
                    <li class="nav-item">
                        <a href="telegram_bot.php" class="<?php echo ($current_menu === 'telegram_bot.php' || $current_menu == 'edit_telegram_bot.php' || $current_menu == "add_telegram_bot.php") ? 'nav-link active' : 'nav-link'; ?>">

                            <i class=" nav-icon fas fa-robot  " aria-hidden="true"></i>
                            <p>Telegram Bot</p>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">

                    <a href="report.php" <?php echo ($current_menu === 'report.php') ? 'class="nav-link active"' : 'class="nav-link"'; ?>>
                        <i class="nav-icon fa fa-file"></i>
                        <p>
                            Report
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-toggle="modal" data-target="#logoutModal">
                        <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                        <p>
                            Logout
                        </p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

<!-- Logout Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to logout?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="../logout.php" class="btn btn-danger">Logout</a>
            </div>
        </div>
    </div>
</div>