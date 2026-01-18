<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - উইথড্র";
$pageTag = "উইথড্র অনুরোধ";
$stmt = db()->query("SELECT COUNT(*) FROM withdrawals WHERE status = 'pending'");
$pageMeta = "পেন্ডিং: " . (int)$stmt->fetchColumn();
$activeNav = "withdrawals";

if (is_post()) {
  require_csrf();
  $withdrawalId = (int)($_POST["withdrawal_id"] ?? 0);
  $action = $_POST["action"] ?? "";
  if ($withdrawalId && in_array($action, ["approve", "reject"], true)) {
    $pdo = db();
    $stmt = $pdo->prepare(
      "SELECT w.id, w.user_id, w.amount, w.status, u.referral_balance
       FROM withdrawals w
       JOIN users u ON u.id = w.user_id
       WHERE w.id = ?"
    );
    $stmt->execute([$withdrawalId]);
    $withdrawal = $stmt->fetch();
    if ($withdrawal && $withdrawal["status"] === "pending") {
      $pdo->beginTransaction();
      if ($action === "approve") {
        if ((int)$withdrawal["referral_balance"] < (int)$withdrawal["amount"]) {
          $pdo->rollBack();
          redirect("/admin/withdrawals.php");
        }
        $pdo->prepare(
          "UPDATE withdrawals SET status = 'approved', updated_at = NOW() WHERE id = ?"
        )->execute([$withdrawalId]);
        $pdo->prepare(
          "UPDATE users SET referral_balance = referral_balance - ? WHERE id = ?"
        )->execute([(int)$withdrawal["amount"], (int)$withdrawal["user_id"]]);
        create_transaction(
          (int)$withdrawal["user_id"],
          "withdraw",
          (int)$withdrawal["amount"],
          ["withdrawal_id" => $withdrawalId],
          "completed"
        );
      } else {
        $pdo->prepare(
          "UPDATE withdrawals SET status = 'rejected', updated_at = NOW() WHERE id = ?"
        )->execute([$withdrawalId]);
      }
      $pdo->commit();
    }
  }
  redirect("/admin/withdrawals.php");
}

$stmt = db()->query(
  "SELECT w.id, w.method, w.account_number, w.amount, w.status, u.mobile, u.referral_balance
   FROM withdrawals w
   JOIN users u ON u.id = w.user_id
   ORDER BY w.created_at DESC
   LIMIT 100"
);
$withdrawals = $stmt->fetchAll();

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">রেফারেল পেআউট অনুরোধ</h2>
          <p class="text-muted mb-0">
            অনুমোদনের আগে পেন্ডিং উইথড্র রিভিউ করুন।
          </p>
        </div>
        <button class="btn btn-outline-dark btn-sm" type="button">
          হিস্ট্রি দেখুন
        </button>
      </div>
    </div>

    <div class="table-card reveal delay-1">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>ব্যবহারকারী</th>
            <th>মাধ্যম</th>
            <th>অ্যাকাউন্ট</th>
            <th>পরিমাণ</th>
            <th>স্ট্যাটাস</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$withdrawals) { ?>
            <tr>
              <td colspan="6" class="text-muted">কোনো অনুরোধ নেই।</td>
            </tr>
          <?php } ?>
          <?php foreach ($withdrawals as $row) { ?>
            <tr>
              <td>
                <div class="fw-semibold"><?php echo e($row["mobile"]); ?></div>
                <div class="text-muted small">রেফারেল ব্যালেন্স: <?php echo e((int)$row["referral_balance"]); ?> TK</div>
              </td>
              <td><?php echo e($row["method"]); ?></td>
              <td><?php echo e($row["account_number"]); ?></td>
              <td><?php echo e((int)$row["amount"]); ?> TK</td>
              <td>
                <?php if ($row["status"] === "pending") { ?>
                  <span class="badge bg-warning-subtle text-warning">পেন্ডিং</span>
                <?php elseif ($row["status"] === "approved") { ?>
                  <span class="badge bg-success-subtle text-success">অনুমোদিত</span>
                <?php else { ?>
                  <span class="badge bg-danger-subtle text-danger">বাতিল</span>
                <?php } ?>
              </td>
              <td class="d-flex gap-2">
                <?php if ($row["status"] === "pending") { ?>
                  <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
                    <input type="hidden" name="withdrawal_id" value="<?php echo e((int)$row["id"]); ?>" />
                    <button class="btn btn-primary btn-sm" name="action" value="approve" type="submit">অনুমোদন</button>
                    <button class="btn btn-outline-dark btn-sm" name="action" value="reject" type="submit">বাতিল</button>
                  </form>
                <?php } else { ?>
                  <button class="btn btn-outline-dark btn-sm" type="button">দেখুন</button>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
          <?php if (false) { ?>
          <tr>
            <td>
              <div class="fw-semibold">Nabila Ahmed</div>
              <div class="text-muted small">রেফারেল ব্যালেন্স: ১২০ TK</div>
            </td>
            <td>বিকাশ</td>
            <td>০১৯০০-১২৩৪৫৬</td>
            <td>৮০ TK</td>
            <td><span class="badge bg-warning-subtle text-warning">পেন্ডিং</span></td>
            <td class="d-flex gap-2">
              <button class="btn btn-primary btn-sm" type="button">
                অনুমোদন
              </button>
              <button class="btn btn-outline-dark btn-sm" type="button">
                বাতিল
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Samiha Noor</div>
              <div class="text-muted small">রেফারেল ব্যালেন্স: ৩২০ TK</div>
            </td>
            <td>নগদ</td>
            <td>০১৭০০-৫৫৩২১০</td>
            <td>২০০ TK</td>
            <td><span class="badge bg-warning-subtle text-warning">পেন্ডিং</span></td>
            <td class="d-flex gap-2">
              <button class="btn btn-primary btn-sm" type="button">
                অনুমোদন
              </button>
              <button class="btn btn-outline-dark btn-sm" type="button">
                বাতিল
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Arif Hasan</div>
              <div class="text-muted small">রেফারেল ব্যালেন্স: ৫০ TK</div>
            </td>
            <td>বিকাশ</td>
            <td>০১৮০০-৭৭৪৪১১</td>
            <td>৫০ TK</td>
            <td><span class="badge bg-success-subtle text-success">অনুমোদিত</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                দেখুন
              </button>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
