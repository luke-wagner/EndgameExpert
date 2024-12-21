<?php

if (isset($_GET['fetch-data']) && $_GET['fetch-data'] === 'true') {
    include 'views/loading_view.php';
} else {
    include 'views/stats_view.php';
}