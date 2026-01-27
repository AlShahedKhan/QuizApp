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
$bkashNumber = (string)config("payments.bkash_number", "01XXXXXXXXX");

if (is_post()) {
  require_csrf();
  $amount = (int)($_POST["amount"] ?? 0);
  $method = "বিকাশ";
  $trxId = trim($_POST["trx_id"] ?? "");

  if ($amount < $minPurchase) {
    $errorMessage = "ন্যূনতম পরিমাণ " . $minPurchase . " TK।";
  } elseif ($method !== "বিকাশ") {
    $errorMessage = "শুধু বিকাশ পেমেন্ট গ্রহণ করা হয়।";
  } elseif ($trxId === "") {
    $errorMessage = "ট্রান্সেকশন আইডি দিন।";
  } elseif (!preg_match("/^[A-Za-z0-9\\-_.]{6,30}$/", $trxId)) {
    $errorMessage = "ট্রান্সেকশন আইডি সঠিক নয়।";
  } else {
    create_transaction(
      (int)$user["id"],
      "purchase",
      $amount,
      ["method" => $method, "trx_id" => $trxId],
      "pending"
    );
    flash("purchase_success", "আপনার টপ-আপ অনুরোধ পাঠানো হয়েছে।");
    $redirectUrl = "/user/buy-credit.php";
    if ($package) {
      $redirectUrl .= "?package=" . urlencode($packageKey);
    }
    redirect($redirectUrl);
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
            <div data-bkash-source>
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
                  placeholder="50"
                  min="<?php echo e($minPurchase); ?>"
                  step="10"
                  value="<?php echo e($amountValue); ?>"
                />
                <div class="form-text text-muted">
                  সর্বনিম্ন <?php echo e($minPurchase); ?> TK থেকে শুরু করুন।
                </div>
              </div>
              <button
                class="btn btn-primary w-100"
                type="button"
                data-bs-toggle="modal"
                data-bs-target="#bkashModal"
              >
                Pay with bKash
              </button>
            </div>
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
              <span class="fw-semibold">৫-১৫ মিনিট</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">স্ট্যাটাস</span>
              <span class="tag">পেন্ডিং</span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">সহায়তা</h3>
            <p class="text-muted mb-3">
              পেমেন্ট সম্পন্ন হলে অ্যাডমিন যাচাই করবে এবং ক্রেডিট যুক্ত হবে।
              জরুরি হলে সাপোর্টে যোগাযোগ করুন।
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
      <div
        class="modal fade"
        id="bkashModal"
        tabindex="-1"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-dialog-centered">
          <form method="post" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
            <?php if ($package) { ?>
              <input type="hidden" name="package" value="<?php echo e($packageKey); ?>" />
            <?php } ?>
            <input type="hidden" name="method" value="বিকাশ" />
            <input type="hidden" name="amount" value="<?php echo e($amountValue); ?>" data-bkash-amount-input />
            <div class="modal-header">
              <h5 class="modal-title">ট্রানজেকশন আইডি দিন</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <label class="form-label" for="trx">ট্রান্সেকশন আইডি</label>
                <input
                  type="text"
                  class="form-control"
                  id="trx"
                  name="trx_id"
                  placeholder="TXN-XXXXXX"
                  required
                />
              </div>
              <div class="soft-card p-3">
                <div class="fw-semibold mb-2">bKash নির্দেশনা</div>
                <div class="d-flex align-items-center gap-2 mb-2">
                  <span class="text-muted small">প্রাপক নম্বর:</span>
                  <span id="bkashNumber" class="fw-semibold"><?php echo e($bkashNumber); ?></span>
                  <button class="btn btn-outline-dark btn-sm" type="button" data-copy="bkashNumber">Copy</button>
                </div>
                <ul class="text-muted small mb-0">
                  <li>*247# ডায়াল করে BKash অ্যাপে যান</li>
                  <li>Send Money নির্বাচন করুন</li>
                  <li>প্রাপক নম্বর হিসেবে উপরোক্ত নম্বরটি দিন</li>
                  <li>টাকার পরিমাণ: ৳<span data-bkash-amount>0</span></li>
                  <li>রেফারেন্স: <?php echo e($user["mobile"]); ?></li>
                  <li>ট্রান্সেকশন আইডি দিয়ে Verify চাপুন</li>
                </ul>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-primary w-100" type="submit">
                Verify
              </button>
            </div>
          </form>
        </div>
      </div>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
