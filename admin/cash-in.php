<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();
// 

$pageTitle = "QuizTap অ্যাডমিন - ক্যাশ ইন";
$pageTag = "ক্যাশ ইন";
$pageMeta = "আপডেট: " . date("g:i A");
$activeNav = "cash_in";

if (is_post()) {
  require_csrf();
  $action = $_POST["action"] ?? "";
  $transactionId = (int)($_POST["transaction_id"] ?? 0);
  if ($transactionId && $action === "approve") {
    approve_purchase($transactionId);
  } elseif ($transactionId && $action === "reject") {
    reject_purchase($transactionId);
  }
  redirect("/admin/cash-in.php");
}

$statusFilter = $_GET["status"] ?? "all";
$dateFrom = $_GET["from"] ?? "";
$dateTo = $_GET["to"] ?? "";
$sort = $_GET["sort"] ?? "newest";

$statusOptions = [
  "all" => "All",
  "completed" => "Completed",
  "pending" => "Pending",
  "approved" => "Approved",
  "canceled" => "Canceled",
];
if (!array_key_exists($statusFilter, $statusOptions)) {
  $statusFilter = "all";
}

$statusDb = $statusFilter === "canceled" ? "rejected" : $statusFilter;
$sortDir = $sort === "oldest" ? "ASC" : "DESC";

$where = ["t.type = 'purchase'"];
$params = [];
if ($statusFilter !== "all") {
  $where[] = "t.status = ?";
  $params[] = $statusDb;
}
if ($dateFrom !== "") {
  $where[] = "DATE(t.created_at) >= ?";
  $params[] = $dateFrom;
}
if ($dateTo !== "") {
  $where[] = "DATE(t.created_at) <= ?";
  $params[] = $dateTo;
}

$sql = "SELECT t.id, t.user_id, t.type, t.amount, t.meta_json, t.status, t.created_at, u.mobile
        FROM transactions t
        JOIN users u ON u.id = t.user_id
        WHERE " . implode(" AND ", $where) . "
        ORDER BY t.created_at {$sortDir}
        LIMIT 200";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$cashInRows = $stmt->fetchAll();

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">ক্যাশ ইন লগ</h2>
          <p class="text-muted mb-0">সকল Purchase/Cash-In লেনদেন এখানে দেখানো হচ্ছে।</p>
        </div>
        <form class="d-flex flex-wrap gap-2" method="get">
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
          <a class="btn btn-outline-dark btn-sm" href="/admin/cash-in.php">Reset</a>
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
            <th>সময়</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$cashInRows) { ?>
            <tr>
              <td colspan="8" class="text-muted">কোনো ক্যাশ ইন লেনদেন নেই।</td>
            </tr>
          <?php } ?>
          <?php foreach ($cashInRows as $txn) {
            $meta = json_decode($txn["meta_json"] ?? "{}", true) ?: [];
            $amountLabel = "+" . (int)$txn["amount"] . " TK";
            $dateLabel = date("d/m/Y", strtotime($txn["created_at"]));
            $timeLabel = format_time($txn["created_at"]);
            $statusLabel = [
              "pending" => ["পেন্ডিং", "bg-warning-subtle text-warning"],
              "approved" => ["পারমিটেড", "bg-success-subtle text-success"],
              "rejected" => ["বাতিল", "bg-danger-subtle text-danger"],
              "completed" => ["সম্পন্ন", "bg-info-subtle text-info"],
            ][$txn["status"]] ?? ["স্ট্যাটাস", "bg-secondary-subtle text-secondary"];
          ?>
            <tr>
              <td>TXN-<?php echo e($txn["id"]); ?></td>
              <td>
                <?php echo e($txn["mobile"]); ?>
                <div class="text-muted small">ID: <?php echo e((int)$txn["user_id"]); ?></div>
              </td>
              <td><span class="badge bg-primary-subtle text-primary">ক্রয়</span></td>
              <td><?php echo e($amountLabel); ?></td>
              <td class="text-muted small">
                <?php
                  $metaLabel = $meta["method"] ?? $meta["description"] ?? "-";
                  echo e($metaLabel);
                ?>
                <?php if (!empty($meta["trx_id"])) { ?>
                  • <?php echo e($meta["trx_id"]); ?>
                <?php } ?>
                <?php if ($txn["status"] === "pending") { ?>
                  <form method="post" class="mt-2 d-flex gap-2">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
                    <input type="hidden" name="transaction_id" value="<?php echo e((int)$txn["id"]); ?>" />
                    <button class="btn btn-primary btn-sm" name="action" value="approve" type="submit">Permit</button>
                    <button class="btn btn-outline-dark btn-sm" name="action" value="reject" type="submit">বাতিল</button>
                  </form>
                <?php } ?>
              </td>
              <td><span class="badge <?php echo $statusLabel[1]; ?>"><?php echo e($statusLabel[0]); ?></span></td>
              <td class="text-muted small"><?php echo e($dateLabel); ?></td>
              <td class="text-muted small"><?php echo e($timeLabel); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>

