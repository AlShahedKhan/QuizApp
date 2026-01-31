<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - ব্যবহারকারী এডিট";
$pageTag = "ব্যবহারকারী এডিট";
$pageMeta = "আপডেট";
$activeNav = "users";

$userId = (int)($_GET["id"] ?? 0);
$errorMessage = "";
$successMessage = flash("user_success");

if (!$userId) {
  redirect("/admin/users.php");
}

$stmt = db()->prepare("SELECT id, mobile, credits_balance, referral_balance, monthly_score FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();
if (!$user) {
  redirect("/admin/users.php");
}

if (is_post()) {
  require_csrf();
  $credits = (int)($_POST["credits_balance"] ?? $user["credits_balance"]);
  $referral = (int)($_POST["referral_balance"] ?? $user["referral_balance"]);
  $monthly = (int)($_POST["monthly_score"] ?? $user["monthly_score"]);

  if ($credits < 0 || $referral < 0 || $monthly < 0) {
    $errorMessage = "সংখ্যা নেগেটিভ হতে পারবে না।";
  } else {
    $stmt = db()->prepare(
      "UPDATE users SET credits_balance = ?, referral_balance = ?, monthly_score = ? WHERE id = ?"
    );
    $stmt->execute([$credits, $referral, $monthly, $userId]);
    flash("user_success", "ব্যবহারকারী আপডেট হয়েছে।");
    redirect("/admin/users.php");
  }
}

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 reveal">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h2 class="mb-1">ব্যবহারকারী এডিট</h2>
          <p class="text-muted mb-0"><?php echo e($user["mobile"]); ?></p>
        </div>
        <a class="btn btn-outline-dark btn-sm" href="/admin/users.php">ফিরে যান</a>
      </div>
      <?php if ($errorMessage) { ?>
        <div class="text-danger small mb-3"><?php echo e($errorMessage); ?></div>
      <?php } ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <div class="mb-3">
          <label class="form-label" for="credits_balance">ক্রেডিট</label>
          <input
            type="number"
            class="form-control"
            id="credits_balance"
            name="credits_balance"
            min="0"
            value="<?php echo e((int)$user["credits_balance"]); ?>"
          />
        </div>
        <div class="mb-3">
          <label class="form-label" for="referral_balance">রেফারেল ব্যালেন্স</label>
          <input
            type="number"
            class="form-control"
            id="referral_balance"
            name="referral_balance"
            min="0"
            value="<?php echo e((int)$user["referral_balance"]); ?>"
          />
        </div>
        <div class="mb-3">
          <label class="form-label" for="monthly_score">মাসিক স্কোর</label>
          <input
            type="number"
            class="form-control"
            id="monthly_score"
            name="monthly_score"
            min="0"
            value="<?php echo e((int)$user["monthly_score"]); ?>"
          />
        </div>
        <button class="btn btn-primary" type="submit">আপডেট করুন</button>
      </form>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
