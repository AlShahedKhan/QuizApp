<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$user = current_user();
$monthLabel = date("F Y");
$pageTitle = "QuizTap - ড্যাশবোর্ড";
$pageTag = "চলতি মাস: " . $monthLabel;
$pageMeta = "রিসেট বাকি " . ((int)date("t") - (int)date("j")) . " দিন";
$activeTab = "account";

$stmt = db()->prepare("SELECT COUNT(*) + 1 FROM users WHERE monthly_score > ?");
$stmt->execute([(int)$user["monthly_score"]]);
$rank = (int)$stmt->fetchColumn();

$stmt = db()->prepare(
  "SELECT type, amount, meta_json, created_at
   FROM transactions
   WHERE user_id = ? AND status IN ('completed','approved')
   ORDER BY created_at DESC
   LIMIT 3"
);
$stmt->execute([(int)$user["id"]]);
$recentTransactions = $stmt->fetchAll();

require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="glass-card p-4 mb-4 reveal">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="soft-card p-4 h-100">
              <div class="text-muted text-uppercase small">বর্তমান ক্রেডিট</div>
              <div class="metric"><?php echo e(format_tk((int)$user["credits_balance"])); ?></div>
              <p class="text-muted mb-0">প্রতি প্রশ্নে ১ ক্রেডিট খরচ হবে।</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="soft-card p-4 h-100">
              <div class="text-muted text-uppercase small">রেফারেল ব্যালেন্স</div>
              <div class="metric"><?php echo e(format_tk((int)$user["referral_balance"])); ?></div>
              <p class="text-muted mb-0">
                শুধুমাত্র রেফারেল ব্যালেন্স থেকে উইথড্র করা যাবে।
              </p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="soft-card p-4 h-100">
              <div class="text-muted text-uppercase small">চলতি মাসের র‍্যাঙ্ক</div>
              <div class="metric">#<?php echo e($rank); ?></div>
              <p class="text-muted mb-0">এই মাসে <?php echo e((int)$user["monthly_score"]); ?> পয়েন্ট সংগ্রহ করেছেন।</p>
            </div>
          </div>
        </div>
      </section>

      <section class="row g-4">
        <div class="col-lg-7 reveal delay-1">
          <div class="soft-card p-4 h-100">
            <h3 class="mb-3">সাম্প্রতিক লেনদেন</h3>
            <?php if (!$recentTransactions) { ?>
              <div class="text-muted">এখনো কোনো লেনদেন নেই।</div>
            <?php } ?>
            <?php foreach ($recentTransactions as $txn) {
              $meta = json_decode($txn["meta_json"] ?? "{}", true) ?: [];
              $isDebit = $txn["type"] === "quiz_deduct" || $txn["type"] === "withdraw";
              $amountLabel = ($isDebit ? "-" : "+") . (int)$txn["amount"] . " TK";
              $amountClass = $isDebit ? "text-danger" : "text-success";
              $typeLabel = [
                "bonus" => "বোনাস",
                "purchase" => "ক্রেডিট কেনা হয়েছে",
                "quiz_deduct" => "কুইজ ক্রেডিট কর্তন",
                "referral_credit" => "রেফারেল বোনাস",
                "withdraw" => "উইথড্র",
              ][$txn["type"]] ?? "লেনদেন";
              $desc = $meta["description"] ?? "";
              if ($desc === "" && $txn["type"] === "purchase") {
                $desc = ($meta["method"] ?? "পেমেন্ট") . " • " . ($meta["trx_id"] ?? "");
              }
              if ($desc === "" && $txn["type"] === "quiz_deduct") {
                $desc = "প্রশ্ন #" . ($meta["question_id"] ?? "");
              }
            ?>
              <div class="list-row mb-3">
                <div>
                  <div class="fw-semibold"><?php echo e($typeLabel); ?></div>
                  <div class="text-muted small"><?php echo e($desc ?: format_date($txn["created_at"])); ?></div>
                </div>
                <div class="<?php echo $amountClass; ?> fw-semibold"><?php echo e($amountLabel); ?></div>
              </div>
            <?php } ?>
            <?php if (false) { ?>
            <div class="list-row mb-3">
              <div>
                <div class="fw-semibold">কুইজ ক্রেডিট কর্তন</div>
                <div class="text-muted small">প্রশ্ন: সাধারণ জ্ঞান • ৫ মিনিট আগে</div>
              </div>
              <div class="text-danger fw-semibold">-1 TK</div>
            </div>
            <div class="list-row mb-3">
              <div>
                <div class="fw-semibold">রেফারেল বোনাস</div>
                <div class="text-muted small">রেফার্ড ইউজার: 01900XXXXXX</div>
              </div>
              <div class="text-success fw-semibold">+50 TK</div>
            </div>
            <div class="list-row">
              <div>
                <div class="fw-semibold">ক্রেডিট কেনা হয়েছে</div>
                <div class="text-muted small">ট্রানজেকশন ID: TXN-9F56X</div>
              </div>
              <div class="text-success fw-semibold">+200 TK</div>
            </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-2">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">দ্রুত কাজ</h3>
            <div class="d-grid gap-2">
              <a class="btn btn-primary" href="../quiz/play.php">কুইজ শুরু করুন</a>
              <a class="btn btn-outline-dark" href="../user/buy-credit.php"
                >ক্রেডিট কিনুন</a
              >
              <a class="btn btn-outline-dark" href="../user/withdraw.php"
                >রেফারেল উইথড্র</a
              >
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">আপনার রেফারেল লিংক</h3>
            <p class="text-muted">
              বন্ধুদের শেয়ার করুন। তারা বোনাস ব্যবহার ও প্রথম ক্রেডিট কেনা
              সম্পন্ন করলে আপনি ৫০ TK পাবেন।
            </p>
            <div class="d-flex flex-wrap align-items-center justify-content-between border rounded-3 p-3">
              <div>
                <div class="fw-semibold" id="referralLink">
                  <?php echo e(referral_link($user["referral_code"])); ?>
                </div>
                <div class="text-muted small">লিংক কপি করে পাঠিয়ে দিন</div>
              </div>
              <button class="btn btn-outline-dark btn-sm" type="button" data-copy="referralLink">
                কপি
              </button>
            </div>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
