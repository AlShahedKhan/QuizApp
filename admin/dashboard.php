<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - ড্যাশবোর্ড";
$pageTag = date("F Y");
$pageMeta = "রিসেট বাকি " . ((int)date("t") - (int)date("j")) . " দিন";
$activeNav = "dashboard";
$activityPage = max(1, (int)($_GET["page"] ?? 1));
$activityLimit = 10;
$activityOffset = ($activityPage - 1) * $activityLimit;
$activityDays = 30;

$stmt = db()->query("SELECT COUNT(*) FROM users");
$totalUsers = (int)$stmt->fetchColumn();

$stmt = db()->query(
  "SELECT COUNT(DISTINCT user_id) FROM quiz_attempts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
);
$activeUsers = (int)$stmt->fetchColumn();

$stmt = db()->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'");
$pendingWithdrawals = (int)$stmt->fetchColumn();

$stmt = db()->query("SELECT COUNT(*) FROM quiz_questions WHERE is_active = 1");
$activeQuestions = (int)$stmt->fetchColumn();

$stmt = db()->prepare(
  "SELECT COUNT(*)
   FROM transactions t
   WHERE t.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)"
);
$stmt->execute([$activityDays]);
$activityTotal = (int)$stmt->fetchColumn();
$activityPages = max(1, (int)ceil($activityTotal / $activityLimit));
$activityPage = min($activityPage, $activityPages);
$activityOffset = ($activityPage - 1) * $activityLimit;

$stmt = db()->prepare(
  "SELECT t.id, t.type, t.amount, t.created_at, u.mobile
   FROM transactions t
   JOIN users u ON u.id = t.user_id
   WHERE t.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)
   ORDER BY t.created_at DESC
   LIMIT ? OFFSET ?"
);
$stmt->execute([$activityDays, $activityLimit, $activityOffset]);
$recentTransactions = $stmt->fetchAll();

$stmt = db()->query(
  "SELECT w.id, w.method, w.amount, u.mobile
   FROM withdrawals w
   JOIN users u ON u.id = w.user_id
   WHERE w.status = 'pending'
   ORDER BY w.created_at DESC
   LIMIT 2"
);
$pendingList = $stmt->fetchAll();

$stmt = db()->query(
  "SELECT mobile, monthly_score FROM users ORDER BY monthly_score DESC, id ASC LIMIT 3"
);
$topScores = $stmt->fetchAll();

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="row g-4 mb-4">
      <div class="col-md-6 col-xl-3 reveal">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">মোট ব্যবহারকারী</div>
          <div class="metric"><?php echo e($totalUsers); ?></div>
          <p class="text-muted mb-0">মোট ব্যবহারকারী</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3 reveal delay-1">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">সক্রিয় খেলোয়াড়</div>
          <div class="metric"><?php echo e($activeUsers); ?></div>
          <p class="text-muted mb-0">গত ৭ দিনে খেলেছে</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3 reveal delay-2">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">পেন্ডিং উইথড্র</div>
          <div class="metric"><?php echo e($pendingWithdrawals); ?></div>
          <p class="text-muted mb-0">রিভিউ দরকার</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3 reveal delay-3">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">প্রশ্ন ভাণ্ডার</div>
          <div class="metric"><?php echo e($activeQuestions); ?></div>
          <p class="text-muted mb-0">সক্রিয় প্রশ্ন</p>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-7 reveal delay-1">
        <div class="table-card">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>সাম্প্রতিক কার্যক্রম</th>
                <th>ধরণ</th>
                <th>পরিমাণ</th>
                <th>সময়</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$recentTransactions) { ?>
                <tr>
                  <td colspan="4" class="text-muted">কোনো লেনদেন নেই।</td>
                </tr>
              <?php } ?>
              <?php foreach ($recentTransactions as $txn) {
                $typeLabel = [
                  "bonus" => "বোনাস",
                  "purchase" => "ক্রয়",
                  "quiz_deduct" => "কুইজ",
                  "referral_credit" => "রেফারেল",
                  "withdraw" => "উইথড্র",
                ][$txn["type"]] ?? "লেনদেন";
                $isDebit = $txn["type"] === "quiz_deduct" || $txn["type"] === "withdraw";
                $amountLabel = ($isDebit ? "-" : "+") . (int)$txn["amount"] . " TK";
              ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?php echo e($txn["mobile"]); ?></div>
                    <div class="text-muted small">#<?php echo e($txn["id"]); ?></div>
                  </td>
                  <td><?php echo e($typeLabel); ?></td>
                  <td><?php echo e($amountLabel); ?></td>
                  <td class="text-muted small">
                    <?php echo e(format_date($txn["created_at"])); ?><br />
                    <?php echo e(format_time($txn["created_at"])); ?>
                  </td>
                </tr>
              <?php } ?>
              <?php if (false) { ?>
              <tr>
                <td>
                  <div class="fw-semibold">Nabila Ahmed</div>
                  <div class="text-muted small">TXN-১২০৯৩</div>
                </td>
                <td>ক্রয়</td>
                <td>+২০০ TK</td>
                <td class="text-muted small">১১:৪২ এএম</td>
              </tr>
              <tr>
                <td>
                  <div class="fw-semibold">Samiha Noor</div>
                  <div class="text-muted small">TXN-১২০৯১</div>
                </td>
                <td>রেফারেল</td>
                <td>+৫০ TK</td>
                <td class="text-muted small">১১:১০ এএম</td>
              </tr>
              <tr>
                <td>
                  <div class="fw-semibold">Tanvir Rahman</div>
                  <div class="text-muted small">TXN-১২০৯০</div>
                </td>
                <td>বোনাস</td>
                <td>+১০০ TK</td>
                <td class="text-muted small">১০:৫৮ এএম</td>
              </tr>
              <?php } ?>
            </tbody>
          </table>
          <?php if ($activityPages > 1) { ?>
            <div class="d-flex justify-content-between align-items-center px-3 pb-3">
              <div class="text-muted small">
                পেজ <?php echo e($activityPage); ?> / <?php echo e($activityPages); ?> • মোট <?php echo e($activityTotal); ?> টি
              </div>
              <div class="btn-group">
                <a
                  class="btn btn-outline-dark btn-sm <?php echo $activityPage <= 1 ? "disabled" : ""; ?>"
                  href="/admin/dashboard.php?page=<?php echo e(max(1, $activityPage - 1)); ?>"
                >
                  আগের
                </a>
                <a
                  class="btn btn-outline-dark btn-sm <?php echo $activityPage >= $activityPages ? "disabled" : ""; ?>"
                  href="/admin/dashboard.php?page=<?php echo e(min($activityPages, $activityPage + 1)); ?>"
                >
                  পরের
                </a>
              </div>
            </div>
          <?php } ?>
        </div>
      </div>
      <div class="col-lg-5 reveal delay-2">
        <div class="soft-card p-4 mb-4">
          <h3 class="mb-3">পেন্ডিং উইথড্র</h3>
          <?php if (!$pendingList) { ?>
            <div class="text-muted">কোনো পেন্ডিং নেই।</div>
          <?php } ?>
          <?php foreach ($pendingList as $item) { ?>
            <div class="list-row mb-3">
              <div>
                <div class="fw-semibold"><?php echo e($item["mobile"]); ?></div>
                <div class="text-muted small"><?php echo e($item["method"]); ?> • <?php echo e((int)$item["amount"]); ?> TK</div>
              </div>
              <span class="tag">পেন্ডিং</span>
            </div>
          <?php } ?>
          <?php if (false) { ?>
          <div class="list-row mb-3">
            <div>
              <div class="fw-semibold">Nabila Ahmed</div>
              <div class="text-muted small">বিকাশ • ৮০ TK</div>
            </div>
            <span class="tag">পেন্ডিং</span>
          </div>
          <div class="list-row">
            <div>
              <div class="fw-semibold">Samiha Noor</div>
              <div class="text-muted small">নগদ • ২০০ TK</div>
            </div>
            <span class="tag">পেন্ডিং</span>
          </div>
          <?php } ?>
        </div>
        <div class="soft-card p-4">
          <h3 class="mb-3">শীর্ষ স্কোরার প্রিভিউ</h3>
          <?php if (!$topScores) { ?>
            <div class="text-muted">কোনো স্কোর নেই।</div>
          <?php } ?>
          <?php foreach ($topScores as $index => $entry) { ?>
            <div class="leaderboard-row mb-2">
              <span><?php echo e($index + 1); ?>. <?php echo e($entry["mobile"]); ?></span>
              <span class="fw-semibold"><?php echo e((int)$entry["monthly_score"]); ?></span>
            </div>
          <?php } ?>
          <?php if (false) { ?>
          <div class="leaderboard-row mb-2">
            <span>১. Nabila Ahmed</span>
            <span class="fw-semibold">১,২৪০</span>
          </div>
          <div class="leaderboard-row mb-2">
            <span>২. Samiha Noor</span>
            <span class="fw-semibold">১,১৮০</span>
          </div>
          <div class="leaderboard-row">
            <span>৩. Tanvir Rahman</span>
            <span class="fw-semibold">১,০১০</span>
          </div>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
