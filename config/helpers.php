<?php
function e($value): string
{
  return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function is_post(): bool
{
  return ($_SERVER["REQUEST_METHOD"] ?? "") === "POST";
}

function url_path(string $path): string
{
  $base = config("app.base_url");
  if ($base !== "") {
    return $base . "/" . ltrim($path, "/");
  }
  return $path;
}

function request_base_url(): string
{
  $base = config("app.base_url");
  if ($base !== "") {
    return $base;
  }
  $host = $_SERVER["HTTP_HOST"] ?? "";
  if ($host === "") {
    return "";
  }
  $secure = !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
  $scheme = $secure ? "https" : "http";
  return $scheme . "://" . $host;
}

function redirect(string $path): void
{
  header("Location: " . url_path($path));
  exit;
}

function csrf_token(): string
{
  if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
  }
  return $_SESSION["csrf_token"];
}

function verify_csrf(?string $token): bool
{
  return isset($_SESSION["csrf_token"]) &&
    is_string($token) &&
    hash_equals($_SESSION["csrf_token"], $token);
}

function require_csrf(): void
{
  if (!verify_csrf($_POST["csrf_token"] ?? null)) {
    http_response_code(400);
    exit("Invalid request.");
  }
}

function flash(string $key, ?string $message = null): ?string
{
  if ($message === null) {
    if (!isset($_SESSION["flash"][$key])) {
      return null;
    }
    $value = $_SESSION["flash"][$key];
    unset($_SESSION["flash"][$key]);
    return $value;
  }

  $_SESSION["flash"][$key] = $message;
  return null;
}

function current_user(): ?array
{
  static $cached = null;
  $userId = $_SESSION["user_id"] ?? null;
  if (!$userId) {
    return null;
  }
  if ($cached && (int)$cached["id"] === (int)$userId) {
    return $cached;
  }
  $stmt = db()->prepare("SELECT * FROM users WHERE id = ?");
  $stmt->execute([$userId]);
  $user = $stmt->fetch();
  $cached = $user ?: null;
  return $cached;
}

function current_admin(): ?array
{
  static $cached = null;
  $adminId = $_SESSION["admin_id"] ?? null;
  if (!$adminId) {
    return null;
  }
  if ($cached && (int)$cached["id"] === (int)$adminId) {
    return $cached;
  }
  $stmt = db()->prepare("SELECT * FROM admins WHERE id = ?");
  $stmt->execute([$adminId]);
  $admin = $stmt->fetch();
  $cached = $admin ?: null;
  return $cached;
}

function require_login(): void
{
  if (!current_user()) {
    $requestUri = $_SERVER["REQUEST_URI"] ?? "/user/dashboard.php";
    if (!is_string($requestUri) || $requestUri === "") {
      $requestUri = "/user/dashboard.php";
    }
    $target = "/auth/login.php?redirect=" . urlencode($requestUri);
    redirect($target);
  }
}

function require_admin(): void
{
  if (!current_admin()) {
    redirect("/admin/login.php");
  }
}

function format_tk(int $amount): string
{
  return number_format($amount) . " TK";
}

function format_date(string $datetime): string
{
  return date("j M, Y", strtotime($datetime));
}

function format_time(string $datetime): string
{
  return date("g:i A", strtotime($datetime));
}

function current_month_year(): string
{
  return date("Y-m");
}

function generate_referral_code(): string
{
  $pdo = db();
  do {
    $code = strtoupper(bin2hex(random_bytes(4)));
    $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
    $stmt->execute([$code]);
  } while ($stmt->fetch());
  return $code;
}

function referral_link(string $code): string
{
  $base = request_base_url();
  $path = "/auth/register.php?ref=" . urlencode($code);
  if ($base !== "") {
    return $base . $path;
  }
  return $path;
}

function send_sms(string $to, string $message, ?string &$error = null): bool
{
  $endpoint = config("sms.endpoint");
  $apiKey = config("sms.api_key");
  if ($endpoint === "" || $apiKey === "") {
    $error = "SMS কনফিগারেশন সেট করা নেই।";
    return false;
  }

  $payload = json_encode([
    "api_key" => $apiKey,
    "msg" => $message,
    "to" => $to,
  ], JSON_UNESCAPED_UNICODE);

  $ch = curl_init($endpoint);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 10,
  ]);
  $response = curl_exec($ch);
  $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($response === false) {
    $error = curl_error($ch);
    return false;
  }

  $data = json_decode($response, true);
  if ($httpCode >= 200 && $httpCode < 300 && isset($data["error"]) && (string)$data["error"] === "0") {
    return true;
  }
  $error = $data["msg"] ?? "SMS পাঠানো যায়নি।";
  return false;
}

function nagorikpay_request(string $url, array $payload, ?string &$error = null): ?array
{
  $apiKey = config("nagorikpay.api_key");
  if ($apiKey === "") {
    $error = "Nagorikpay API key সেট করা নেই।";
    return null;
  }

  $body = json_encode($payload, JSON_UNESCAPED_UNICODE);
  $ch = curl_init($url);
  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
      "API-KEY: " . $apiKey,
      "Content-Type: application/json",
    ],
    CURLOPT_POSTFIELDS => $body,
    CURLOPT_TIMEOUT => 20,
  ]);
  $response = curl_exec($ch);
  $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
  if ($response === false) {
    $error = curl_error($ch);
    return null;
  }

  $data = json_decode($response, true);
  if ($httpCode < 200 || $httpCode >= 300 || !is_array($data)) {
    $error = "Nagorikpay রেসপন্স পাওয়া যায়নি।";
    return null;
  }
  return $data;
}

function nagorikpay_create_payment(array $payload, ?string &$error = null): ?array
{
  $url = config("nagorikpay.create_url");
  if ($url === "") {
    $error = "Nagorikpay create URL সেট করা নেই।";
    return null;
  }
  return nagorikpay_request($url, $payload, $error);
}

function nagorikpay_verify_payment(string $transactionId, ?string &$error = null): ?array
{
  $url = config("nagorikpay.verify_url");
  if ($url === "") {
    $error = "Nagorikpay verify URL সেট করা নেই।";
    return null;
  }
  return nagorikpay_request($url, ["transaction_id" => $transactionId], $error);
}

function create_transaction(
  int $userId,
  string $type,
  int $amount,
  array $meta = [],
  string $status = "completed"
): int {
  $stmt = db()->prepare(
    "INSERT INTO transactions (user_id, type, amount, meta_json, status, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())"
  );
  $stmt->execute([
    $userId,
    $type,
    $amount,
    json_encode($meta, JSON_UNESCAPED_UNICODE),
    $status,
  ]);
  return (int)db()->lastInsertId();
}

function update_transaction_meta(int $transactionId, array $meta): void
{
  $stmt = db()->prepare("SELECT meta_json FROM transactions WHERE id = ?");
  $stmt->execute([$transactionId]);
  $current = $stmt->fetchColumn();
  $currentMeta = [];
  if (is_string($current) && $current !== "") {
    $decoded = json_decode($current, true);
    if (is_array($decoded)) {
      $currentMeta = $decoded;
    }
  }
  $merged = array_merge($currentMeta, $meta);
  db()->prepare(
    "UPDATE transactions SET meta_json = ? WHERE id = ?"
  )->execute([json_encode($merged, JSON_UNESCAPED_UNICODE), $transactionId]);
}

function find_pending_purchase_by_reference(string $reference, ?int $userId = null): ?array
{
  $pattern = '%"reference":"' . addslashes($reference) . '"%';
  if ($userId) {
    $stmt = db()->prepare(
      "SELECT id, user_id, amount, status FROM transactions
       WHERE type = 'purchase' AND status = 'pending' AND user_id = ? AND meta_json LIKE ?
       ORDER BY id DESC
       LIMIT 1"
    );
    $stmt->execute([$userId, $pattern]);
  } else {
    $stmt = db()->prepare(
      "SELECT id, user_id, amount, status FROM transactions
       WHERE type = 'purchase' AND status = 'pending' AND meta_json LIKE ?
       ORDER BY id DESC
       LIMIT 1"
    );
    $stmt->execute([$pattern]);
  }
  $txn = $stmt->fetch();
  return $txn ?: null;
}

function mark_bonus_used_if_needed(int $userId): void
{
  $stmt = db()->prepare(
    "SELECT id, bonus_used_at, first_purchase_at, reward_given, referrer_id
     FROM referrals WHERE referred_user_id = ?"
  );
  $stmt->execute([$userId]);
  $referral = $stmt->fetch();
  if (!$referral) {
    return;
  }

  if ($referral["bonus_used_at"] === null) {
    $stmt = db()->prepare(
      "SELECT COALESCE(SUM(amount), 0) AS spent
       FROM transactions
       WHERE user_id = ? AND type = 'quiz_deduct' AND status = 'completed'"
    );
    $stmt->execute([$userId]);
    $spent = (int)$stmt->fetchColumn();
    if ($spent >= (int)config("credits.signup_bonus", 100)) {
      db()->prepare(
        "UPDATE referrals SET bonus_used_at = NOW() WHERE id = ?"
      )->execute([$referral["id"]]);
      $referral["bonus_used_at"] = date("Y-m-d H:i:s");
    }
  }

  if ($referral["bonus_used_at"] && $referral["first_purchase_at"] && !$referral["reward_given"]) {
    $reward = (int)config("credits.referral_reward", 50);
    $pdo = db();
    $pdo->beginTransaction();
    $pdo->prepare(
      "UPDATE users SET referral_balance = referral_balance + ? WHERE id = ?"
    )->execute([$reward, $referral["referrer_id"]]);
    create_transaction(
      (int)$referral["referrer_id"],
      "referral_credit",
      $reward,
      ["source_user_id" => $userId],
      "completed"
    );
    $pdo->prepare(
      "UPDATE referrals SET reward_given = 1 WHERE id = ?"
    )->execute([$referral["id"]]);
    $pdo->commit();
  }
}

function mark_first_purchase(int $userId): void
{
  $stmt = db()->prepare(
    "SELECT id, first_purchase_at FROM referrals WHERE referred_user_id = ?"
  );
  $stmt->execute([$userId]);
  $referral = $stmt->fetch();
  if (!$referral || $referral["first_purchase_at"] !== null) {
    return;
  }
  db()->prepare(
    "UPDATE referrals SET first_purchase_at = NOW() WHERE id = ?"
  )->execute([$referral["id"]]);
  mark_bonus_used_if_needed($userId);
}

function approve_purchase(int $transactionId): void
{
  $pdo = db();
  $stmt = $pdo->prepare(
    "SELECT id, user_id, amount, status FROM transactions WHERE id = ? AND type = 'purchase'"
  );
  $stmt->execute([$transactionId]);
  $txn = $stmt->fetch();
  if (!$txn || $txn["status"] !== "pending") {
    return;
  }
  $pdo->beginTransaction();
  $pdo->prepare(
    "UPDATE transactions SET status = 'approved' WHERE id = ?"
  )->execute([$transactionId]);
  $pdo->prepare(
    "UPDATE users SET credits_balance = credits_balance + ? WHERE id = ?"
  )->execute([(int)$txn["amount"], (int)$txn["user_id"]]);
  $pdo->commit();
  mark_first_purchase((int)$txn["user_id"]);
}

function reject_purchase(int $transactionId): void
{
  db()->prepare(
    "UPDATE transactions SET status = 'rejected' WHERE id = ? AND type = 'purchase' AND status = 'pending'"
  )->execute([$transactionId]);
}
