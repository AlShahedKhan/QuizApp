<?php
require_once __DIR__ . "/../../config/bootstrap.php";
require_login();

$user = current_user();
$reference = trim($_GET["ref"] ?? "");

if ($reference !== "") {
  $purchase = find_pending_purchase_by_reference($reference, (int)$user["id"]);
  if ($purchase) {
    reject_purchase((int)$purchase["id"]);
    update_transaction_meta((int)$purchase["id"], [
      "payment_status" => "CANCELLED",
      "cancelled_at" => date("Y-m-d H:i:s"),
    ]);
  }
}

flash("purchase_error", "পেমেন্ট বাতিল করা হয়েছে।");
redirect("/user/buy-credit.php");
