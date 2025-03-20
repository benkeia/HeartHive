<?php
session_start();

if (isset($_POST['association_id'])) {
    $_SESSION['association_id'] = $_POST['association_id'];
}