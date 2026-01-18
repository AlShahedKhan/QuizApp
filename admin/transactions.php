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

$stmt = db()->query(
  "SELECT t.id, t.user_id, t.type, t.amount, t.meta_json, t.status, t.created_at, u.mobile
   FROM transactions t
   JOIN users u ON u.id = t.user_id
   ORDER BY t.created_at DESC
   LIMIT 100"
);
$transactions = $stmt->fetchAll();

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
        <div class="d-flex gap-2">
          <button class="btn btn-outline-dark btn-sm" type="button">
            ফিল্টার
          </button>
          <button class="btn btn-primary btn-sm" type="button">
            এক্সপোর্ট
          </button>
        </div>
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
            <th>সময়</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$transactions) { ?>
            <tr>
              <td colspan="6" class="text-muted">কোনো লেনদেন নেই।</td>
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
            $amountLabel = ($txn["type"] === "quiz_deduct" || $txn["type"] === "withdraw")
              ? "-" . (int)$txn["amount"] . " TK"
              : "+" . (int)$txn["amount"] . " TK";
          ?>
            <tr>
              <td>TXN-<?php echo e($txn["id"]); ?></td>
              <td><?php echo e($txn["mobile"]); ?></td>
              <td><span class="badge <?php echo $badgeClass; ?>"><?php echo e($typeLabel); ?></span></td>
              <td><?php echo e($amountLabel); ?></td>
              <td class="text-muted small">
                <?php
                  $metaLabel = $meta["method"] ?? $meta["description"] ?? "";
                  if ($metaLabel === "" && $txn["type"] === "quiz_deduct") {
                    $metaLabel = "প্রশ্ন #" . ($meta["question_id"] ?? "");
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
                    <button class="btn btn-primary btn-sm" name="action" value="approve" type="submit">অ্যাপ্রুভ</button>
                    <button class="btn btn-outline-dark btn-sm" name="action" value="reject" type="submit">বাতিল</button>
                  </form>
                <?php } ?>
              </td>
              <td class="text-muted small"><?php echo e(format_time($txn["created_at"])); ?></td>
            </tr>
          <?php } ?>
          <?php if (false) { ?>
          <tr>
            <td>TXN-১২০৯৩</td>
            <td>Nabila Ahmed</td>
            <td><span class="badge bg-primary-subtle text-primary">ক্রয়</span></td>
            <td>+২০০ TK</td>
            <td class="text-muted small">বিকাশ ম্যানুয়াল</td>
            <td class="text-muted small">১১:৪২ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯২</td>
            <td>Arif Hasan</td>
            <td><span class="badge bg-warning-subtle text-warning">কুইজ</span></td>
            <td>-১ TK</td>
            <td class="text-muted small">প্রশ্ন #৩৪০</td>
            <td class="text-muted small">১১:৩৯ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯১</td>
            <td>Samiha Noor</td>
            <td><span class="badge bg-success-subtle text-success">রেফারেল</span></td>
            <td>+৫০ TK</td>
            <td class="text-muted small">রেফার্ড ০১৮০০-৭৭৪৪১১</td>
            <td class="text-muted small">১১:১০ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯০</td>
            <td>Tanvir Rahman</td>
            <td><span class="badge bg-info-subtle text-info">বোনাস</span></td>
            <td>+১০০ TK</td>
            <td class="text-muted small">সাইনআপ রিওয়ার্ড</td>
            <td class="text-muted small">১০:৫৮ এএম</td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
