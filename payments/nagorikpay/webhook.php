<?php
require_once __DIR__ . "/../../config/bootstrap.php";

header("Content-Type: application/json");

$raw = file_get_contents("php://input") ?: "";
$payload = [];
if ($raw !== "") {
  $decoded = json_decode($raw, true);
  if (is_array($decoded)) {
    $payload = $decoded;
  }
}

$transactionId = trim((string)($payload["transaction_id"] ?? $_GET["transaction_id"] ?? ""));
if ($transactionId === "") {
  http_response_code(400);
  echo json_encode(["ok" => false, "message" => "Missing transaction_id"]);
  exit;
}

$apiError = null;
$response = nagorikpay_verify_payment($transactionId, $apiError);
if (!$response) {
  http_response_code(400);
  echo json_encode(["ok" => false, "message" => $apiError ?: "Verify failed"]);
  exit;
}

$status = strtoupper((string)($response["status"] ?? ""));
$metadata = $response["metadata"] ?? [];
$reference = is_array($metadata) ? (string)($metadata["reference"] ?? "") : "";
if ($reference === "") {
  http_response_code(200);
  echo json_encode(["ok" => false, "message" => "Missing reference"]);
  exit;
}

$purchase = find_pending_purchase_by_reference($reference);
if (!$purchase) {
  http_response_code(200);
  echo json_encode(["ok" => true, "message" => "Already processed"]);
  exit;
}

$amount = (float)($response["amount"] ?? 0);
$expected = (float)$purchase["amount"];
if ($amount > 0 && abs($amount - $expected) > 0.01) {
  update_transaction_meta((int)$purchase["id"], [
    "payment_status" => $status,
    "gateway_transaction_id" => $transactionId,
    "amount_mismatch" => true,
  ]);
  http_response_code(200);
  echo json_encode(["ok" => false, "message" => "Amount mismatch"]);
  exit;
}

if ($status === "COMPLETED") {
  approve_purchase((int)$purchase["id"]);
  update_transaction_meta((int)$purchase["id"], [
    "payment_status" => $status,
    "gateway_transaction_id" => $transactionId,
    "payment_method" => $response["payment_method"] ?? "",
    "verified_at" => date("Y-m-d H:i:s"),
    "webhook" => true,
  ]);
  http_response_code(200);
  echo json_encode(["ok" => true]);
  exit;
}

update_transaction_meta((int)$purchase["id"], [
  "payment_status" => $status ?: "ERROR",
  "gateway_transaction_id" => $transactionId,
  "webhook" => true,
]);
http_response_code(200);
echo json_encode(["ok" => false, "message" => "Payment not completed"]);
