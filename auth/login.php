<?php
require_once __DIR__ . "/../config/bootstrap.php";

$mobile = "";
$errorMessage = "";
$adminSessionActive = !empty($_SESSION["admin_id"]);

$redirectTarget = $_GET["redirect"] ?? $_POST["redirect"] ?? "/user/dashboard.php";
if (!is_string($redirectTarget) || $redirectTarget === "") {
  $redirectTarget = "/user/dashboard.php";
}
$parts = parse_url($redirectTarget);
if (
  $redirectTarget[0] !== "/" ||
  $parts === false ||
  isset($parts["scheme"]) ||
  isset($parts["host"]) ||
  strpos($redirectTarget, "\n") !== false ||
  strpos($redirectTarget, "\r") !== false
) {
  $redirectTarget = "/user/dashboard.php";
}

if (current_user()) {
  redirect($redirectTarget);
}

if ($adminSessionActive && !$errorMessage) {
  $errorMessage = "একই ব্রাউজারে অ্যাডমিন লগইন থাকলে ইউজার লগইন করা যাবে না। আগে অ্যাডমিন থেকে লগআউট করুন।";
}

if (is_post()) {
  require_csrf();
  if ($adminSessionActive) {
    $errorMessage = "একই ব্রাউজারে অ্যাডমিন লগইন থাকলে ইউজার লগইন করা যাবে না। আগে অ্যাডমিন থেকে লগআউট করুন।";
  } else {
  $mobile = trim($_POST["mobile"] ?? "");
  $password = trim($_POST["password"] ?? "");

  if (!preg_match("/^01\\d{9}$/", $mobile)) {
    $errorMessage = "সঠিক মোবাইল নম্বর দিন।";
  } elseif (strlen($password) < 6) {
    $errorMessage = "পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।";
  } else {
    $stmt = db()->prepare("SELECT id, password_hash FROM users WHERE mobile = ?");
    $stmt->execute([$mobile]);
    $user = $stmt->fetch();
    if (!$user || !password_verify($password, $user["password_hash"])) {
      $errorMessage = "মোবাইল নম্বর বা পাসওয়ার্ড সঠিক নয়।";
    } else {
      session_regenerate_id(true);
      $_SESSION["user_id"] = (int)$user["id"];
      redirect($redirectTarget);
    }
  }
  }
}
?>
<!DOCTYPE html>
<html lang="bn">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - লগইন</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/auth.css" />
  </head>
  <body>
    <main class="auth-shell">
      <div class="row g-4">
        <div class="col-lg-6 reveal">
          <section class="promo-panel h-100">
            <div class="brand-mark mb-4">
              <span class="brand-dot"></span>
              <span>QuizTap</span>
            </div>
            <h1 class="promo-title">
              প্রতিদিনের কুইজ, মাসিক লিডারবোর্ড, আর আকর্ষণীয় পুরস্কার।
            </h1>
            <p class="text-white-50 mb-4">
              নিজের দক্ষতা যাচাই করুন, ক্রেডিট ব্যবহার করে কুইজ খেলুন, আর রেফারেল
              থেকে আলাদা আয়ের সুযোগ পান।
            </p>
            <div class="promo-badges mb-4">
              <span class="badge-pill">সাইনআপে 100 TK বোনাস</span>
              <span class="badge-pill">সঠিক উত্তরে পয়েন্ট</span>
              <span class="badge-pill">রেফারেলে 50 TK</span>
            </div>
            <div class="promo-card">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-white-50 text-uppercase small">
                    আজকের কুইজার
                  </div>
                  <div class="promo-stat">4,821 জন</div>
                </div>
                <div class="text-end">
                  <div class="text-white-50 text-uppercase small">
                    চলতি মাস
                  </div>
                  <div class="promo-stat">সেপ্টেম্বর</div>
                </div>
              </div>
              <p class="promo-note mb-0">
                নিয়মিত খেলুন, স্ট্রিক ধরে রাখুন, আর টপ ৫-এ জায়গা নিন।
              </p>
            </div>
          </section>
        </div>

        <div class="col-lg-6 reveal delay-1">
          <section class="auth-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <h2 class="mb-1">লগইন করুন</h2>
                <p class="text-muted mb-0">
                  আপনার মোবাইল নম্বর এবং পাসওয়ার্ড দিয়ে প্রবেশ করুন।
                </p>
              </div>
              <span class="tag">নিরাপদ</span>
            </div>

            <div
              class="error-box mb-3 <?php echo $errorMessage ? "is-visible" : ""; ?>"
              data-error-box
              role="alert"
            >
              <?php echo e($errorMessage); ?>
            </div>

            <form method="post" data-auth-form novalidate>
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="redirect" value="<?php echo e($redirectTarget); ?>" />
              <div class="mb-3">
                <label class="form-label" for="mobile">মোবাইল নম্বর</label>
                <input
                  id="mobile"
                  name="mobile"
                  type="tel"
                  class="form-control"
                  placeholder="01XXXXXXXXX"
                  inputmode="numeric"
                  pattern="^01[0-9]{9}$"
                  maxlength="11"
                  aria-describedby="mobileHelp mobileError"
                  required
                  data-mobile
                  value="<?php echo e($mobile); ?>"
                />
                <div id="mobileHelp" class="form-text text-muted">
                  ১১ ডিজিটের মোবাইল নম্বর লিখুন।
                </div>
                <div class="invalid-feedback" id="mobileError" data-mobile-error></div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="password">পাসওয়ার্ড</label>
                <div class="d-flex gap-2 align-items-center">
                  <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control"
                    placeholder="••••••••"
                    aria-describedby="passwordError"
                    minlength="6"
                    required
                    data-password
                  />
                  <button
                    type="button"
                    class="password-toggle"
                    data-toggle-password
                    aria-label="পাসওয়ার্ড দেখান"
                  >
                    দেখান
                  </button>
                </div>
                <div
                  class="invalid-feedback d-block"
                  id="passwordError"
                  data-password-error
                ></div>
              </div>

              <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="remember"
                  />
                  <label class="form-check-label" for="remember">
                    লগইন মনে রাখুন
                  </label>
                </div>
                <a class="text-muted small" href="#">পাসওয়ার্ড ভুলে গেছেন?</a>
              </div>

              <button class="btn btn-primary w-100 mb-3" type="submit">
                লগইন করুন
              </button>

              <div class="text-center text-muted small">
                এখনো অ্যাকাউন্ট নেই?
                <a href="/auth/register.php" class="fw-semibold text-decoration-none"
                  >সাইনআপ করুন</a
                >
              </div>
            </form>
          </section>
        </div>
      </div>
    </main>

    <script src="../assets/js/auth.js"></script>
  </body>
</html>
