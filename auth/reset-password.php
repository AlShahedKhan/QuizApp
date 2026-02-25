<?php
require_once __DIR__ . "/../config/bootstrap.php";

if (current_user()) {
  redirect("/user/dashboard.php");
}

$errorMessage = "";
$infoMessage = flash("auth_info");
$mobile = trim($_SESSION["password_reset_mobile"] ?? ($_GET["mobile"] ?? ""));
$otpMaxAttempts = (int)config("sms.otp_max_attempts", 5);

if (is_post()) {
  require_csrf();
  $action = $_POST["action"] ?? "reset";
  $mobile = trim($_POST["mobile"] ?? $mobile);

  if ($action === "resend") {
    if (!preg_match("/^01\\d{9}$/", $mobile)) {
      $errorMessage = "Please enter a valid 11-digit mobile number first.";
    } else {
      dispatch_password_reset_otp($mobile);
      $_SESSION["password_reset_mobile"] = $mobile;
      $infoMessage = "If this mobile number is registered, a new OTP has been sent.";
    }
  } else {
    $otpCode = trim($_POST["otp_code"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $confirmPassword = trim($_POST["confirm_password"] ?? "");

    if (!preg_match("/^01\\d{9}$/", $mobile)) {
      $errorMessage = "Please enter a valid 11-digit mobile number.";
    } elseif (!preg_match("/^\\d{6}$/", $otpCode)) {
      $errorMessage = "OTP must be exactly 6 digits.";
    } elseif (strlen($password) < 6) {
      $errorMessage = "Password must be at least 6 characters.";
    } elseif ($password !== $confirmPassword) {
      $errorMessage = "Password and confirm password do not match.";
    } else {
      $pdo = db();
      $genericOtpError = "Invalid OTP or expired request. Please resend OTP and try again.";

      $stmt = $pdo->prepare("SELECT id, password_hash FROM users WHERE mobile = ? LIMIT 1");
      $stmt->execute([$mobile]);
      $user = $stmt->fetch();

      if (!$user) {
        $errorMessage = $genericOtpError;
      } else {
        $stmt = $pdo->prepare(
          "SELECT * FROM password_reset_requests
           WHERE user_id = ? AND used_at IS NULL
           ORDER BY created_at DESC
           LIMIT 1"
        );
        $stmt->execute([(int)$user["id"]]);
        $request = $stmt->fetch();

        if (!$request) {
          $errorMessage = $genericOtpError;
        } elseif (strtotime($request["expires_at"]) < time()) {
          $errorMessage = $genericOtpError;
        } elseif ((int)$request["attempts"] >= $otpMaxAttempts) {
          $errorMessage = $genericOtpError;
        } elseif (!password_verify($otpCode, $request["code_hash"])) {
          $pdo->prepare(
            "UPDATE password_reset_requests SET attempts = attempts + 1 WHERE id = ?"
          )->execute([(int)$request["id"]]);
          $errorMessage = $genericOtpError;
        } elseif (password_verify($password, $user["password_hash"])) {
          $errorMessage = "New password must be different from your current password.";
        } else {
          $newHash = password_hash($password, PASSWORD_BCRYPT);
          $pdo->beginTransaction();
          try {
            $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?")->execute([
              $newHash,
              (int)$user["id"],
            ]);
            $pdo->prepare(
              "UPDATE password_reset_requests
               SET used_at = NOW()
               WHERE user_id = ? AND used_at IS NULL"
            )->execute([(int)$user["id"]]);
            $pdo->commit();

            unset($_SESSION["password_reset_mobile"]);
            flash("auth_info", "Password reset successful. Please log in with your new password.");
            redirect("/auth/login.php");
          } catch (Throwable $e) {
            $pdo->rollBack();
            $errorMessage = "Password reset failed. Please try again.";
          }
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - Reset Password</title>
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
            <h1 class="promo-title">Reset your password with OTP verification.</h1>
            <p class="text-white-50 mb-4">
              Enter the OTP sent to your mobile number and choose a strong new password.
            </p>
            <div class="promo-badges mb-4">
              <span class="badge-pill">6-digit OTP</span>
              <span class="badge-pill">Limited attempts</span>
              <span class="badge-pill">Instant reset</span>
            </div>
          </section>
        </div>

        <div class="col-lg-6 reveal delay-1">
          <section class="auth-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <h2 class="mb-1">Reset Password</h2>
                <p class="text-muted mb-0">Use OTP to set a new password.</p>
              </div>
              <span class="tag">OTP</span>
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
              <input type="hidden" name="action" value="reset" />

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
                  aria-describedby="mobileError"
                  required
                  data-mobile
                  value="<?php echo e($mobile); ?>"
                />
                <div class="invalid-feedback" id="mobileError" data-mobile-error></div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="otpCode">OTP Code</label>
                <input
                  id="otpCode"
                  name="otp_code"
                  type="text"
                  class="form-control"
                  placeholder="123456"
                  inputmode="numeric"
                  maxlength="6"
                  aria-describedby="otpError"
                  required
                  data-otp
                />
                <div class="invalid-feedback d-block" id="otpError" data-otp-error></div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="password">New Password</label>
                <input
                  id="password"
                  name="password"
                  type="password"
                  class="form-control"
                  placeholder="New password"
                  minlength="6"
                  aria-describedby="passwordError"
                  required
                  data-password
                />
                <div
                  class="invalid-feedback d-block"
                  id="passwordError"
                  data-password-error
                ></div>
              </div>

              <div class="mb-3">
                <label class="form-label" for="confirmPassword">Confirm Password</label>
                <input
                  id="confirmPassword"
                  name="confirm_password"
                  type="password"
                  class="form-control"
                  placeholder="Confirm new password"
                  minlength="6"
                  aria-describedby="confirmPasswordError"
                  required
                  data-password-confirm
                />
                <div
                  class="invalid-feedback d-block"
                  id="confirmPasswordError"
                  data-confirm-error
                ></div>
              </div>

              <button class="btn btn-primary w-100 mb-2" type="submit">
                Reset Password
              </button>
            </form>

            <form method="post" class="mt-2">
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="action" value="resend" />
              <input type="hidden" name="mobile" value="<?php echo e($mobile); ?>" />
              <button class="btn btn-outline-dark w-100 mb-3" type="submit">
                Resend OTP
              </button>
            </form>

            <div class="text-center text-muted small">
              Back to
              <a href="/auth/login.php" class="fw-semibold text-decoration-none">Login</a>
            </div>
          </section>
        </div>
      </div>
    </main>
    <script src="../assets/js/auth.js"></script>
  </body>
</html>
