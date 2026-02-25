<?php
require_once __DIR__ . "/../config/bootstrap.php";

if (current_user()) {
  redirect("/user/dashboard.php");
}

$mobile = "";
$errorMessage = "";
$infoMessage = flash("auth_info");

if (is_post()) {
  require_csrf();
  $mobile = trim($_POST["mobile"] ?? "");

  if (!preg_match("/^01\\d{9}$/", $mobile)) {
    $errorMessage = "Please enter a valid 11-digit mobile number (01XXXXXXXXX).";
  } else {
    dispatch_password_reset_otp($mobile);
    $_SESSION["password_reset_mobile"] = $mobile;
    flash("auth_info", "If this mobile number is registered, an OTP has been sent.");
    redirect("/auth/reset-password.php");
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - Forgot Password</title>
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
            <h1 class="promo-title">Recover your account access securely.</h1>
            <p class="text-white-50 mb-4">
              Enter your registered mobile number. If it exists, we will send a one-time OTP to reset your password.
            </p>
            <div class="promo-badges mb-4">
              <span class="badge-pill">OTP protected</span>
              <span class="badge-pill">Time-limited code</span>
              <span class="badge-pill">Secure password reset</span>
            </div>
          </section>
        </div>

        <div class="col-lg-6 reveal delay-1">
          <section class="auth-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <h2 class="mb-1">Forgot Password</h2>
                <p class="text-muted mb-0">We will send OTP to your mobile number.</p>
              </div>
              <span class="tag">Recover</span>
            </div>

            <div
              class="error-box mb-3 <?php echo $errorMessage ? "is-visible" : ""; ?>"
              data-error-box
              role="alert"
            >
              <?php echo e($errorMessage); ?>
            </div>
            <?php if ($infoMessage) { ?>
              <div class="alert alert-success py-2 mb-3"><?php echo e($infoMessage); ?></div>
            <?php } ?>

            <form method="post" novalidate data-auth-form>
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <div class="mb-3">
                <label class="form-label" for="mobile">Mobile Number</label>
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
                  Use the same mobile number you used during registration.
                </div>
                <div class="invalid-feedback" id="mobileError" data-mobile-error></div>
              </div>

              <button class="btn btn-primary w-100 mb-3" type="submit">
                Send OTP
              </button>

              <div class="text-center text-muted small">
                Remembered your password?
                <a href="/auth/login.php" class="fw-semibold text-decoration-none">Back to Login</a>
              </div>
            </form>
          </section>
        </div>
      </div>
    </main>
    <script src="../assets/js/auth.js"></script>
  </body>
</html>
