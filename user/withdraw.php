<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$user = current_user();
$minWithdraw = (int)config("credits.min_withdraw", 50);
$pageTitle = "QuizTap - রেফারেল উইথড্র";
$pageTag = "রেফারেল ব্যালেন্স: " . format_tk((int)$user["referral_balance"]);
$pageMeta = "উইথড্র শুধুই রেফারেল থেকে";
$activeTab = "withdraw";
$tabs = [
  "play" => ["label" => "কুইজ খেলুন", "href" => "../quiz/play.php"],
  "withdraw" => ["label" => "উইথড্র", "href" => "#"],
  "transactions" => ["label" => "লেনদেন", "href" => "../user/transactions.php"],
  "account" => ["label" => "ড্যাশবোর্ড", "href" => "../user/dashboard.php"],
];

$errorMessage = "";
$successMessage = flash("withdraw_success");

if (is_post()) {
  require_csrf();
  $method = trim($_POST["method"] ?? "");
  $account = trim($_POST["account"] ?? "");
  $amount = (int)($_POST["amount"] ?? 0);

  if ($method === "" || $account === "") {
    $errorMessage = "সব তথ্য দিন।";
  } elseif (!preg_match("/^01\\d{9}$/", $account)) {
    $errorMessage = "সঠিক মোবাইল নম্বর দিন।";
  } elseif ($amount < $minWithdraw) {
    $errorMessage = "ন্যূনতম উইথড্র " . $minWithdraw . " TK।";
  } elseif ($amount > (int)$user["referral_balance"]) {
    $errorMessage = "আপনার রেফারেল ব্যালেন্সে পর্যাপ্ত টাকা নেই।";
  } else {
    $stmt = db()->prepare(
      "INSERT INTO withdrawals (user_id, method, account_number, amount, status, created_at, updated_at)
       VALUES (?, ?, ?, ?, 'pending', NOW(), NOW())"
    );
    $stmt->execute([(int)$user["id"], $method, $account, $amount]);
    flash("withdraw_success", "আপনার উইথড্র অনুরোধ পাঠানো হয়েছে।");
    redirect("/user/withdraw.php");
  }
}

$stmt = db()->prepare(
  "SELECT method, account_number, amount, status, created_at
   FROM withdrawals
   WHERE user_id = ?
   ORDER BY created_at DESC
   LIMIT 2"
);
$stmt->execute([(int)$user["id"]]);
$recentWithdrawals = $stmt->fetchAll();

require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch">
        <div class="col-lg-7 reveal">
          <div class="glass-card p-4 h-100">
            <h2 class="mb-3">উইথড্র অনুরোধ</h2>
            <form method="post">
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <?php if ($errorMessage) { ?>
                <div class="text-danger small mb-3"><?php echo e($errorMessage); ?></div>
              <?php } ?>
              <?php if ($successMessage) { ?>
                <div class="text-success small mb-3"><?php echo e($successMessage); ?></div>
              <?php } ?>
              <div class="mb-3">
                <label class="form-label" for="method">পেমেন্ট মাধ্যম</label>
                <select class="form-select" id="method" name="method">
                  <option value="বিকাশ" selected>বিকাশ</option>
                  <option value="নগদ">নগদ</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label" for="account">মোবাইল নম্বর</label>
                <input
                  type="tel"
                  class="form-control"
                  id="account"
                  name="account"
                  placeholder="01XXXXXXXXX"
                />
              </div>
              <div class="mb-4">
                <label class="form-label" for="amount">উইথড্র পরিমাণ</label>
                <input
                  type="number"
                  class="form-control"
                  id="amount"
                  name="amount"
                  placeholder="100"
                  min="<?php echo e($minWithdraw); ?>"
                  max="<?php echo e((int)$user["referral_balance"]); ?>"
                  step="10"
                />
                <div class="form-text text-muted">
                  সর্বোচ্চ <?php echo e((int)$user["referral_balance"]); ?> TK পর্যন্ত অনুরোধ করতে পারবেন।
                </div>
              </div>
              <button class="btn btn-primary w-100" type="submit">
                উইথড্র রিকোয়েস্ট পাঠান
              </button>
            </form>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">সর্বশেষ স্ট্যাটাস</h3>
            <?php if (!$recentWithdrawals) { ?>
              <div class="text-muted">এখনো কোনো উইথড্র নেই।</div>
            <?php } ?>
            <?php foreach ($recentWithdrawals as $withdrawal) {
              $statusMap = [
                "pending" => "পেন্ডিং",
                "approved" => "অনুমোদিত",
                "rejected" => "বাতিল",
              ];
              $statusLabel = $statusMap[$withdrawal["status"]] ?? $withdrawal["status"];
            ?>
              <div class="list-row mb-3">
                <div>
                  <div class="fw-semibold"><?php echo e($withdrawal["method"]); ?> উইথড্র</div>
                  <div class="text-muted small">রিকোয়েস্ট: <?php echo e(format_date($withdrawal["created_at"])); ?></div>
                </div>
                <span class="tag"><?php echo e($statusLabel); ?></span>
              </div>
            <?php } ?>
            <?php if (false) { ?>
            <div class="list-row mb-3">
              <div>
                <div class="fw-semibold">বিকাশ উইথড্র</div>
                <div class="text-muted small">রিকোয়েস্ট: ১০ সেপ্টেম্বর</div>
              </div>
              <span class="tag">পেন্ডিং</span>
            </div>
            <div class="list-row">
              <div>
                <div class="fw-semibold">নগদ উইথড্র</div>
                <div class="text-muted small">রিকোয়েস্ট: ২ সেপ্টেম্বর</div>
              </div>
              <span class="tag">অনুমোদিত</span>
            </div>
            <?php } ?>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">গুরুত্বপূর্ণ</h3>
            <p class="text-muted mb-0">
              কুইজ থেকে অর্জিত ক্রেডিট উইথড্র করা যাবে না। কেবল রেফারেল
              ব্যালেন্স উইথড্র করা যাবে।
            </p>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
