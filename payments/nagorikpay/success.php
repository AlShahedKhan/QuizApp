<?php
require_once __DIR__ . "/../../config/bootstrap.php";
require_login();

$user = current_user();
$transactionId = trim($_GET["transaction_id"] ?? "");
$reference = trim($_GET["ref"] ?? "");

if ($transactionId === "" || $reference === "") {
  flash("purchase_error", "পেমেন্ট তথ্য পাওয়া যায়নি।");
  redirect("/user/buy-credit.php");
}

$purchase = find_pending_purchase_by_reference($reference, (int)$user["id"]);
if (!$purchase) {
  flash("purchase_error", "পেমেন্ট অনুরোধ খুঁজে পাওয়া যায়নি।");
  redirect("/user/buy-credit.php");
}

$apiError = null;
$response = nagorikpay_verify_payment($transactionId, $apiError);
if (!$response) {
  flash("purchase_error", $apiError ?: "পেমেন্ট যাচাই করা যায়নি।");
  redirect("/user/buy-credit.php");
}

$status = strtoupper((string)($response["status"] ?? ""));
$amount = (float)($response["amount"] ?? 0);
$expected = (float)$purchase["amount"];
if ($amount > 0 && abs($amount - $expected) > 0.01) {
  update_transaction_meta((int)$purchase["id"], [
    "payment_status" => $status,
    "gateway_transaction_id" => $transactionId,
    "amount_mismatch" => true,
  ]);
  flash("purchase_error", "পেমেন্ট পরিমাণ মেলেনি।");
  redirect("/user/buy-credit.php");
}

if ($status === "COMPLETED") {
  approve_purchase((int)$purchase["id"]);
  update_transaction_meta((int)$purchase["id"], [
    "payment_status" => $status,
    "gateway_transaction_id" => $transactionId,
    "payment_method" => $response["payment_method"] ?? "",
    "verified_at" => date("Y-m-d H:i:s"),
  ]);
  flash("purchase_success", "পেমেন্ট সফল হয়েছে। ক্রেডিট যোগ করা হয়েছে।");
  redirect("/user/transactions.php");
}

if ($status === "PENDING") {
  update_transaction_meta((int)$purchase["id"], [
    "payment_status" => $status,
    "gateway_transaction_id" => $transactionId,
  ]);
  flash("purchase_pending", "পেমেন্ট এখনো সম্পূর্ণ হয়নি। পরে আবার দেখুন।");
  redirect("/user/transactions.php");
}

update_transaction_meta((int)$purchase["id"], [
  "payment_status" => $status ?: "ERROR",
  "gateway_transaction_id" => $transactionId,
]);
flash("purchase_error", "পেমেন্ট সফল হয়নি।");
redirect("/user/buy-credit.php");
