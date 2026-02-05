<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - লেনদেন";
$pageTag = "লেজার";
$pageMeta = "আপডেট: " . date("g:i A");
$activeNav = "transactions";

if (is_post()) {
  require_csrf();
  $action = $_POST["action"] ?? "";
  $transactionId = (int)($_POST["transaction_id"] ?? 0);
  if ($transactionId && $action === "approve") {
    approve_purchase($transactionId);
  } elseif ($transactionId && $action === "reject") {
    reject_purchase($transactionId);
  }
  redirect("/admin/transactions.php");
}

$typeFilter = $_GET["type"] ?? "all";
$statusFilter = $_GET["status"] ?? "all";
$dateFrom = $_GET["from"] ?? "";
$dateTo = $_GET["to"] ?? "";
$sort = $_GET["sort"] ?? "newest";

$typeOptions = [
  "all" => "All",
  "quiz" => "Quiz",
  "purchase" => "Purchase",
  "referral" => "Referral",
  "bonus" => "Bonus",
  "withdraw" => "Withdraw",
];
$statusOptions = [
  "all" => "All",
  "completed" => "Completed",
  "pending" => "Pending",
  "approved" => "Approved",
  "canceled" => "Canceled",
];

if (!array_key_exists($typeFilter, $typeOptions)) {
  $typeFilter = "all";
}
if (!array_key_exists($statusFilter, $statusOptions)) {
  $statusFilter = "all";
}

$statusDb = $statusFilter === "canceled" ? "rejected" : $statusFilter;
$sortDir = $sort === "oldest" ? "ASC" : "DESC";

$baseWhere = [];
$baseParams = [];
if ($statusFilter !== "all") {
  $baseWhere[] = "t.status = ?";
  $baseParams[] = $statusDb;
}
if ($dateFrom !== "") {
  $baseWhere[] = "DATE(t.created_at) >= ?";
  $baseParams[] = $dateFrom;
}
if ($dateTo !== "") {
  $baseWhere[] = "DATE(t.created_at) <= ?";
  $baseParams[] = $dateTo;
}

$nonQuiz = [];
if ($typeFilter !== "quiz") {
  $nonQuizWhere = $baseWhere;
  $nonQuizParams = $baseParams;
  if ($typeFilter !== "all") {
    $typeMap = [
      "purchase" => "purchase",
      "referral" => "referral_credit",
      "bonus" => "bonus",
      "withdraw" => "withdraw",
    ];
    if (isset($typeMap[$typeFilter])) {
      $nonQuizWhere[] = "t.type = ?";
      $nonQuizParams[] = $typeMap[$typeFilter];
    }
  }
  $whereSql = $nonQuizWhere ? ("WHERE " . implode(" AND ", $nonQuizWhere) . " AND t.type != 'quiz_deduct'") : "WHERE t.type != 'quiz_deduct'";
  $sql = "SELECT t.id, t.user_id, t.type, t.amount, t.meta_json, t.status, t.created_at, u.mobile
          FROM transactions t
          JOIN users u ON u.id = t.user_id
          {$whereSql}
          ORDER BY t.created_at {$sortDir}
          LIMIT 100";
  $stmt = db()->prepare($sql);
  $stmt->execute($nonQuizParams);
  $nonQuiz = $stmt->fetchAll();
}

$quizRows = [];
if ($typeFilter === "all" || $typeFilter === "quiz") {
  $quizWhere = $baseWhere;
  $quizParams = $baseParams;
  $quizWhere[] = "t.type = 'quiz_deduct'";
  $whereSql = "WHERE " . implode(" AND ", $quizWhere);
  $sql = "SELECT MIN(t.id) AS id,
                 t.user_id,
                 'quiz_deduct' AS type,
                 SUM(t.amount) AS amount_sum,
                 COUNT(*) AS quiz_count,
                 MAX(t.status) AS status,
                 MAX(t.created_at) AS created_at,
                 GROUP_CONCAT(DATE_FORMAT(t.created_at, '%h:%i %p') ORDER BY t.created_at DESC SEPARATOR ', ') AS times_list,
                 u.mobile
          FROM transactions t
          JOIN users u ON u.id = t.user_id
          {$whereSql}
          GROUP BY t.user_id, DATE(t.created_at)
          ORDER BY created_at {$sortDir}
          LIMIT 100";
  $stmt = db()->prepare($sql);
  $stmt->execute($quizParams);
  $quizRows = $stmt->fetchAll();
}

$transactions = array_merge($nonQuiz, $quizRows);
usort($transactions, function ($a, $b) use ($sortDir) {
  $at = strtotime($a["created_at"]);
  $bt = strtotime($b["created_at"]);
  if ($at === $bt) {
    return 0;
  }
  if ($sortDir === "ASC") {
    return $at < $bt ? -1 : 1;
  }
  return $at > $bt ? -1 : 1;
});

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">লেনদেন লগ</h2>
          <p class="text-muted mb-0">
            বোনাস, ক্রয়, কুইজ কাটছাঁট, এবং রেফারেল রিওয়ার্ড ট্র্যাক করুন।
          </p>
        </div>
        <form class="d-flex flex-wrap gap-2" method="get">
          <select class="form-select form-select-sm" name="type">
            <?php foreach ($typeOptions as $value => $label) { ?>
              <option value="<?php echo e($value); ?>" <?php echo $typeFilter === $value ? "selected" : ""; ?>>
                <?php echo e($label); ?>
              </option>
            <?php } ?>
          </select>
          <select class="form-select form-select-sm" name="status">
            <?php foreach ($statusOptions as $value => $label) { ?>
              <option value="<?php echo e($value); ?>" <?php echo $statusFilter === $value ? "selected" : ""; ?>>
                <?php echo e($label); ?>
              </option>
            <?php } ?>
          </select>
          <input class="form-control form-control-sm" type="date" name="from" value="<?php echo e($dateFrom); ?>" />
          <input class="form-control form-control-sm" type="date" name="to" value="<?php echo e($dateTo); ?>" />
          <select class="form-select form-select-sm" name="sort">
            <option value="newest" <?php echo $sort === "newest" ? "selected" : ""; ?>>Newest</option>
            <option value="oldest" <?php echo $sort === "oldest" ? "selected" : ""; ?>>Oldest</option>
          </select>
          <button class="btn btn-outline-dark btn-sm" type="submit">Apply</button>
          <a class="btn btn-outline-dark btn-sm" href="/admin/transactions.php">Reset</a>
        </form>
      </div>
    </div>

    <div class="table-card reveal delay-1">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>আইডি</th>
            <th>ব্যবহারকারী</th>
            <th>ধরণ</th>
            <th>পরিমাণ</th>
            <th>মেটা</th>
            <th>স্ট্যাটাস</th>
            <th>তারিখ</th>
            <th>সময়</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$transactions) { ?>
            <tr>
              <td colspan="8" class="text-muted">কোনো লেনদেন নেই।</td>
            </tr>
          <?php } ?>
          <?php foreach ($transactions as $txn) {
            $meta = json_decode($txn["meta_json"] ?? "{}", true) ?: [];
            $typeLabel = [
              "bonus" => "বোনাস",
              "purchase" => "ক্রয়",
              "quiz_deduct" => "কুইজ",
              "referral_credit" => "রেফারেল",
              "withdraw" => "উইথড্র",
            ][$txn["type"]] ?? "লেনদেন";
            $badgeClass = [
              "bonus" => "bg-info-subtle text-info",
              "purchase" => "bg-primary-subtle text-primary",
              "quiz_deduct" => "bg-warning-subtle text-warning",
              "referral_credit" => "bg-success-subtle text-success",
              "withdraw" => "bg-danger-subtle text-danger",
            ][$txn["type"]] ?? "bg-secondary-subtle text-secondary";
            $amountBase = (int)($txn["amount_sum"] ?? $txn["amount"]);
            $amountLabel = ($txn["type"] === "quiz_deduct" || $txn["type"] === "withdraw")
              ? "-" . $amountBase . " TK"
              : "+" . $amountBase . " TK";
            $dateLabel = date("d/m/Y", strtotime($txn["created_at"]));
            $timeLabel = $txn["type"] === "quiz_deduct" && !empty($txn["times_list"])
              ? $txn["times_list"]
              : format_time($txn["created_at"]);
          ?>
            <tr>
              <td>TXN-<?php echo e($txn["id"]); ?></td>
              <td>
                <?php echo e($txn["mobile"]); ?>
                <div class="text-muted small">ID: <?php echo e((int)$txn["user_id"]); ?></div>
              </td>
              <td><span class="badge <?php echo $badgeClass; ?>"><?php echo e($typeLabel); ?></span></td>
              <td><?php echo e($amountLabel); ?></td>
              <td class="text-muted small">
                <?php
                  $metaLabel = $meta["method"] ?? $meta["description"] ?? "";
                  if ($txn["type"] === "quiz_deduct") {
                    $quizCount = (int)($txn["quiz_count"] ?? 0);
                    $metaLabel = "Quiz deduction";
                    if ($quizCount > 1) {
                      $metaLabel .= " (" . $quizCount . " times)";
                    }
                  } elseif ($metaLabel === "") {
                    $metaLabel = "-";
                  }
                  echo e($metaLabel);
                ?>
                <?php if (!empty($meta["trx_id"])) { ?>
                  • <?php echo e($meta["trx_id"]); ?>
                <?php } ?>
                <?php if ($txn["type"] === "purchase" && $txn["status"] === "pending") { ?>
                  <form method="post" class="mt-2 d-flex gap-2">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
                    <input type="hidden" name="transaction_id" value="<?php echo e((int)$txn["id"]); ?>" />
                    <button class="btn btn-primary btn-sm" name="action" value="approve" type="submit">Permit</button>
                    <button class="btn btn-outline-dark btn-sm" name="action" value="reject" type="submit">বাতিল</button>
                  </form>
                <?php } ?>
              </td>
              <td>
                <?php
                  $statusLabel = [
                    "pending" => ["পেন্ডিং", "bg-warning-subtle text-warning"],
                    "approved" => ["পারমিটেড", "bg-success-subtle text-success"],
                    "rejected" => ["বাতিল", "bg-danger-subtle text-danger"],
                    "completed" => ["সম্পন্ন", "bg-info-subtle text-info"],
                  ][$txn["status"]] ?? ["স্ট্যাটাস", "bg-secondary-subtle text-secondary"];
                ?>
                <span class="badge <?php echo $statusLabel[1]; ?>"><?php echo e($statusLabel[0]); ?></span>
              </td>
              <td class="text-muted small"><?php echo e($dateLabel); ?></td>
              <td class="text-muted small"><?php echo e($timeLabel); ?></td>
            </tr>
          <?php } ?>
          <?php if (false) { ?>
          <tr>
            <td>TXN-১২০৯৩</td>
            <td>Nabila Ahmed</td>
            <td><span class="badge bg-primary-subtle text-primary">ক্রয়</span></td>
            <td>+২০০ TK</td>
            <td class="text-muted small">বিকাশ ম্যানুয়াল</td>
            <td><span class="badge bg-success-subtle text-success">পারমিটেড</span></td>
            <td class="text-muted small">১১:৪২ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯২</td>
            <td>Arif Hasan</td>
            <td><span class="badge bg-warning-subtle text-warning">কুইজ</span></td>
            <td>-১ TK</td>
            <td class="text-muted small">প্রশ্ন #৩৪০</td>
            <td><span class="badge bg-info-subtle text-info">সম্পন্ন</span></td>
            <td class="text-muted small">১১:৩৯ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯১</td>
            <td>Samiha Noor</td>
            <td><span class="badge bg-success-subtle text-success">রেফারেল</span></td>
            <td>+৫০ TK</td>
            <td class="text-muted small">রেফার্ড ০১৮০০-৭৭৪৪১১</td>
            <td><span class="badge bg-info-subtle text-info">সম্পন্ন</span></td>
            <td class="text-muted small">১১:১০ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯০</td>
            <td>Tanvir Rahman</td>
            <td><span class="badge bg-info-subtle text-info">বোনাস</span></td>
            <td>+১০০ TK</td>
            <td class="text-muted small">সাইনআপ রিওয়ার্ড</td>
            <td><span class="badge bg-info-subtle text-info">সম্পন্ন</span></td>
            <td class="text-muted small">১০:৫৮ এএম</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>












