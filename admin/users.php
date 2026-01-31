<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - ব্যবহারকারী";
$pageTag = "ব্যবহারকারী ব্যবস্থাপনা";
$pageMeta = "শেষ সিঙ্ক: " . date("g:i A");
$activeNav = "users";

$action = $_GET["action"] ?? $_POST["action"] ?? "";

if ($action === "export") {
  $stmt = db()->query(
    "SELECT id, mobile, credits_balance, referral_balance, monthly_score, created_at
     FROM users
     ORDER BY created_at DESC"
  );
  $rows = $stmt->fetchAll();
  header("Content-Type: text/csv; charset=UTF-8");
  header("Content-Disposition: attachment; filename=users.csv");
  $out = fopen("php://output", "w");
  fputcsv($out, ["id", "mobile", "credits_balance", "referral_balance", "monthly_score", "created_at"]);
  foreach ($rows as $row) {
    fputcsv($out, [
      $row["id"],
      $row["mobile"],
      $row["credits_balance"],
      $row["referral_balance"],
      $row["monthly_score"],
      $row["created_at"],
    ]);
  }
  fclose($out);
  exit;
}

$page = max(1, (int)($_GET["page"] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$stmt = db()->query("SELECT COUNT(*) FROM users");
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $limit));
$page = min($page, $pages);
$offset = ($page - 1) * $limit;

$stmt = db()->prepare(
  "SELECT id, mobile, credits_balance, referral_balance, monthly_score, created_at
   FROM users
   ORDER BY created_at DESC
   LIMIT ? OFFSET ?"
);
$stmt->execute([$limit, $offset]);
$users = $stmt->fetchAll();

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">সক্রিয় ব্যবহারকারী</h2>
          <p class="text-muted mb-0">
            ক্রেডিট, স্কোর, এবং রেফারেল স্ট্যাটাস রিভিউ করুন।
          </p>
        </div>
        <div class="d-flex gap-2">
          <a class="btn btn-outline-dark btn-sm" href="/admin/users.php?action=export">
            CSV এক্সপোর্ট
          </a>
          <a class="btn btn-primary btn-sm" href="/admin/users-create.php">
            ব্যবহারকারী যোগ
          </a>
        </div>
      </div>
    </div>

    <div class="table-card reveal delay-1">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>ব্যবহারকারী</th>
            <th>মোবাইল</th>
            <th>ক্রেডিট</th>
            <th>রেফারেল ব্যালেন্স</th>
            <th>মাসিক স্কোর</th>
            <th>স্ট্যাটাস</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$users) { ?>
            <tr>
              <td colspan="7" class="text-muted">কোনো ব্যবহারকারী নেই।</td>
            </tr>
          <?php } ?>
          <?php foreach ($users as $row) { ?>
            <tr>
              <td>
                <div class="fw-semibold"><?php echo e($row["mobile"]); ?></div>
                <div class="text-muted small">যোগ দিয়েছেন <?php echo e(format_date($row["created_at"])); ?></div>
              </td>
              <td><?php echo e($row["mobile"]); ?></td>
              <td><?php echo e((int)$row["credits_balance"]); ?> TK</td>
              <td><?php echo e((int)$row["referral_balance"]); ?> TK</td>
              <td><?php echo e((int)$row["monthly_score"]); ?></td>
              <td><span class="badge bg-success-subtle text-success">সক্রিয়</span></td>
              <td>
                <a class="btn btn-outline-dark btn-sm" href="/admin/users-edit.php?id=<?php echo e((int)$row["id"]); ?>">
                  এডিট
                </a>
              </td>
            </tr>
          <?php } ?>
          <?php if (false) { ?>
          <tr>
            <td>
              <div class="fw-semibold">Nabila Ahmed</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ০৩</div>
            </td>
            <td>০১৯০০-১২৩৪৫৬</td>
            <td>৮২০ TK</td>
            <td>১২০ TK</td>
            <td>১,২৪০</td>
            <td><span class="badge bg-success-subtle text-success">সক্রিয়</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Arif Hasan</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ০৮</div>
            </td>
            <td>০১৮০০-৭৭৪৪১১</td>
            <td>৯৫ TK</td>
            <td>০ TK</td>
            <td>২১০</td>
            <td><span class="badge bg-warning-subtle text-warning">কম ক্রেডিট</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Samiha Noor</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ১১</div>
            </td>
            <td>০১৭০০-৫৫৩২১০</td>
            <td>১,৫৪০ TK</td>
            <td>৩২০ TK</td>
            <td>১,৮৬০</td>
            <td><span class="badge bg-success-subtle text-success">সক্রিয়</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Tanvir Rahman</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ১৪</div>
            </td>
            <td>০১৬০০-৮৮৫৫৪৪</td>
            <td>৪৩০ TK</td>
            <td>৫০ TK</td>
            <td>৭৮০</td>
            <td><span class="badge bg-info-subtle text-info">বোনাস ব্যবহার</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php if ($pages > 1) { ?>
        <div class="d-flex justify-content-between align-items-center px-3 pb-3">
          <div class="text-muted small">
            পেজ <?php echo e($page); ?> / <?php echo e($pages); ?> • মোট <?php echo e($total); ?> জন
          </div>
          <div class="btn-group">
            <a
              class="btn btn-outline-dark btn-sm <?php echo $page <= 1 ? "disabled" : ""; ?>"
              href="/admin/users.php?page=<?php echo e(max(1, $page - 1)); ?>"
            >
              আগের
            </a>
            <a
              class="btn btn-outline-dark btn-sm <?php echo $page >= $pages ? "disabled" : ""; ?>"
              href="/admin/users.php?page=<?php echo e(min($pages, $page + 1)); ?>"
            >
              পরের
            </a>
          </div>
        </div>
      <?php } ?>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
