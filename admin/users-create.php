<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - ব্যবহারকারী যোগ";
$pageTag = "ব্যবহারকারী যোগ";
$pageMeta = "নতুন";
$activeNav = "users";

$errorMessage = "";
$successMessage = flash("user_success");
$mobile = "";
$password = "";

if (is_post()) {
  require_csrf();
  $mobile = trim($_POST["mobile"] ?? "");
  $password = trim($_POST["password"] ?? "");

  if (!preg_match("/^01\\d{9}$/", $mobile)) {
    $errorMessage = "সঠিক মোবাইল নম্বর দিন।";
  } elseif (strlen($password) < 6) {
    $errorMessage = "পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।";
  } else {
    $stmt = db()->prepare("SELECT id FROM users WHERE mobile = ?");
    $stmt->execute([$mobile]);
    if ($stmt->fetch()) {
      $errorMessage = "এই মোবাইল নম্বর ইতিমধ্যে আছে।";
    } else {
      $stmt = db()->prepare(
        "INSERT INTO users (mobile, password_hash, referral_code, credits_balance, referral_balance, monthly_score, created_at)
         VALUES (?, ?, ?, 0, 0, 0, NOW())"
      );
      $stmt->execute([
        $mobile,
        password_hash($password, PASSWORD_BCRYPT),
        generate_referral_code(),
      ]);
      flash("user_success", "ব্যবহারকারী যোগ হয়েছে।");
      redirect("/admin/users.php");
    }
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
          <h2 class="mb-1">নতুন ব্যবহারকারী</h2>
          <p class="text-muted mb-0">মোবাইল ও পাসওয়ার্ড দিয়ে নতুন ব্যবহারকারী যোগ করুন।</p>
        </div>
        <a class="btn btn-outline-dark btn-sm" href="/admin/users.php">ফিরে যান</a>
      </div>
      <?php if ($errorMessage) { ?>
        <div class="text-danger small mb-3"><?php echo e($errorMessage); ?></div>
      <?php } ?>
      <form method="post">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <div class="mb-3">
          <label class="form-label" for="mobile">মোবাইল</label>
          <input
            type="tel"
            class="form-control"
            id="mobile"
            name="mobile"
            placeholder="01XXXXXXXXX"
            value="<?php echo e($mobile); ?>"
          />
        </div>
        <div class="mb-3">
          <label class="form-label" for="password">পাসওয়ার্ড</label>
          <input
            type="password"
            class="form-control"
            id="password"
            name="password"
            placeholder="******"
            value="<?php echo e($password); ?>"
          />
        </div>
        <button class="btn btn-primary" type="submit">সংরক্ষণ করুন</button>
      </form>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
