<?php
require_once __DIR__ . "/../config/bootstrap.php";

unset($_SESSION["admin_id"]);
redirect("/admin/login.php");
