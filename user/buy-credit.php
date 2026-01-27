<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$user = current_user();
$minPurchase = (int)config("credits.min_purchase", 50);
$packages = [
  "starter" => ["name" => "Starter Pack", "amount" => 50, "credits" => 50],
  "popular" => ["name" => "Popular Pack", "amount" => 200, "credits" => 200],
  "pro" => ["name" => "Pro Pack", "amount" => 500, "credits" => 500],
];
$packageKey = $_GET["package"] ?? $_POST["package"] ?? "";
$package = null;
if (is_string($packageKey) && isset($packages[$packageKey])) {
  $package = $packages[$packageKey];
}
$amountValue = $package ? max($minPurchase, (int)$package["amount"]) : "";
$pageTitle = "QuizTap - ক্রেডিট কিনুন";
$pageTag = "ক্রেডিট রেট: 1 TK = 1 ক্রেডিট";
$pageMeta = "ন্যূনতম " . $minPurchase . " TK";
$activeTab = "buy-credit";
$tabs = [
  "play" => ["label" => "কুইজ খেলুন", "href" => "../quiz/play.php"],
  "buy-credit" => ["label" => "ক্রেডিট কিনুন", "href" => "#"],
  "transactions" => ["label" => "লেনদেন", "href" => "../user/transactions.php"],
  "account" => ["label" => "ড্যাশবোর্ড", "href" => "../user/dashboard.php"],
];

$errorMessage = "";
$successMessage = flash("purchase_success");
$pendingMessage = flash("purchase_pending");
$purchaseError = flash("purchase_error");

if (is_post()) {
  require_csrf();
  $amount = (int)($_POST["amount"] ?? 0);

  if ($amount < $minPurchase) {
    $errorMessage = "ন্যূনতম পরিমাণ " . $minPurchase . " TK।";
  } else {
    $baseUrl = request_base_url();
    if ($baseUrl === "") {
      $errorMessage = "অ্যাপের বেস URL সেট করা নেই।";
    } else {
      $reference = "NP" . strtoupper(bin2hex(random_bytes(4)));
      $transactionId = create_transaction(
        (int)$user["id"],
        "purchase",
        $amount,
        ["gateway" => "nagorikpay", "reference" => $reference],
        "pending"
      );

      $payload = [
        "cus_name" => "QuizTap User " . $user["mobile"],
        "cus_email" => "user" . (int)$user["id"] . "@quiztap.local",
        "cus_phone" => (string)$user["mobile"],
        "amount" => (string)$amount,
        "success_url" => $baseUrl . "/payments/nagorikpay/success.php?ref=" . urlencode($reference),
        "cancel_url" => $baseUrl . "/payments/nagorikpay/cancel.php?ref=" . urlencode($reference),
        "webhook_url" => $baseUrl . "/payments/nagorikpay/webhook.php",
        "metadata" => [
          "reference" => $reference,
          "user_id" => (int)$user["id"],
          "mobile" => (string)$user["mobile"],
        ],
        "meta_data" => [
          "reference" => $reference,
          "user_id" => (int)$user["id"],
          "mobile" => (string)$user["mobile"],
        ],
      ];

      $apiError = null;
      $response = nagorikpay_create_payment($payload, $apiError);
      $status = is_array($response) ? ($response["status"] ?? null) : null;
      $message = is_array($response) ? (string)($response["message"] ?? "") : "";
      $paymentUrl = is_array($response) ? (string)($response["payment_url"] ?? "") : "";
      $isOk = ($status === true || $status === "TRUE" || $status === "true" || $status === "SUCCESS" || $status === "success");

      update_transaction_meta($transactionId, [
        "gateway_status" => $status,
        "gateway_message" => $message,
        "payment_url" => $paymentUrl,
      ]);

      if ($paymentUrl !== "") {
        redirect($paymentUrl);
      }

      if (!$response || (!$isOk && $apiError)) {
        reject_purchase($transactionId);
      }
      $errorMessage = $apiError ?: ($message !== "" ? $message : "পেমেন্ট শুরু করা যায়নি।");
    }
  }
}

require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch">
        <div class="col-lg-7 reveal">
          <div class="glass-card p-4 h-100">
            <h2 class="mb-3">ক্রেডিট টপ-আপ</h2>
            <form method="post">
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <?php if ($package) { ?>
                <input type="hidden" name="package" value="<?php echo e($packageKey); ?>" />
              <?php } ?>
              <?php if ($errorMessage) { ?>
                <div class="text-danger small mb-3"><?php echo e($errorMessage); ?></div>
              <?php } ?>
              <?php if ($successMessage) { ?>
                <div class="text-success small mb-3"><?php echo e($successMessage); ?></div>
              <?php } ?>
              <?php if ($pendingMessage) { ?>
                <div class="text-warning small mb-3"><?php echo e($pendingMessage); ?></div>
              <?php } ?>
              <?php if ($purchaseError) { ?>
                <div class="text-danger small mb-3"><?php echo e($purchaseError); ?></div>
              <?php } ?>
              <div class="mb-3">
                <label class="form-label" for="amount">পরিমাণ (TK)</label>
                <input
                  type="number"
                  class="form-control"
                  id="amount"
                  name="amount"
                  placeholder="50"
                  min="<?php echo e($minPurchase); ?>"
                  step="10"
                  value="<?php echo e($amountValue); ?>"
                />
                <div class="form-text text-muted">
                  সর্বনিম্ন <?php echo e($minPurchase); ?> TK থেকে শুরু করুন।
                </div>
              </div>
              <button class="btn btn-primary w-100" type="submit">
                Nagorikpay দিয়ে পেমেন্ট করুন
              </button>
            </form>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">সারসংক্ষেপ</h3>
            <?php if ($package) { ?>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">প্যাকেজ</span>
                <span class="fw-semibold"><?php echo e($package["name"]); ?></span>
              </div>
            <?php } ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">আপনি পাবেন</span>
              <span class="fw-semibold">
                <?php echo e(number_format($package ? (int)$package["credits"] : $minPurchase)); ?> ক্রেডিট
              </span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">প্রসেসিং সময়</span>
              <span class="fw-semibold">তাৎক্ষণিক</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">স্ট্যাটাস</span>
              <span class="tag">পেন্ডিং</span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">সহায়তা</h3>
            <p class="text-muted mb-3">
              আপনাকে Nagorikpay পেমেন্ট পেজে পাঠানো হবে। পেমেন্ট সফল হলে
              স্বয়ংক্রিয়ভাবে ক্রেডিট যুক্ত হবে।
            </p>
            <div class="list-row">
              <div>
                <div class="fw-semibold">সাপোর্ট হটলাইন</div>
                <div class="text-muted small">10AM - 10PM</div>
              </div>
              <div class="fw-semibold">01911-000000</div>
            </div>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
