<?php
require_once __DIR__ . "/../config/bootstrap.php";

$errorMessage = "";
$infoMessage = "";
$pending = $_SESSION["signup_pending"] ?? null;

if (!$pending || empty($pending["mobile"]) || empty($pending["password_hash"])) {
  redirect("/auth/register.php");
}

$mobile = $pending["mobile"];
$referralCode = $pending["referral_code"] ?? "";

if (is_post()) {
  require_csrf();
  $action = $_POST["action"] ?? "verify";

  if ($action === "resend") {
    $stmt = db()->prepare(
      "SELECT id, last_sent_at FROM otp_requests WHERE mobile = ? AND used_at IS NULL ORDER BY created_at DESC LIMIT 1"
    );
    $stmt->execute([$mobile]);
    $row = $stmt->fetch();
    if ($row) {
      $lastSent = strtotime($row["last_sent_at"]);
      if ($lastSent && (time() - $lastSent) < 60) {
        $errorMessage = "OTP আবার পাঠাতে একটু অপেক্ষা করুন।";
      } else {
        $otpCode = (string)random_int(100000, 999999);
        $otpHash = password_hash($otpCode, PASSWORD_BCRYPT);
        $expiresAt = date("Y-m-d H:i:s", time() + ((int)config("sms.otp_expire_minutes", 5) * 60));
        db()->prepare(
          "UPDATE otp_requests SET code_hash = ?, attempts = 0, expires_at = ?, last_sent_at = NOW() WHERE id = ?"
        )->execute([$otpHash, $expiresAt, (int)$row["id"]]);

        $smsError = null;
        $smsText = "আপনার QuizTap OTP: {$otpCode}। ৫ মিনিটের মধ্যে ব্যবহার করুন।";
        if (!send_sms($mobile, $smsText, $smsError)) {
          $errorMessage = $smsError ?: "OTP পাঠানো যায়নি।";
        } else {
          $infoMessage = "OTP পাঠানো হয়েছে। মোবাইল দেখুন।";
        }
      }
    } else {
      $errorMessage = "OTP পাওয়া যায়নি। আবার সাইনআপ করুন।";
    }
  } else {
    $otpCode = trim($_POST["otp_code"] ?? "");
    if (!preg_match("/^\\d{6}$/", $otpCode)) {
      $errorMessage = "OTP ৬ ডিজিটের হতে হবে।";
    } else {
      $pdo = db();
      $stmt = $pdo->prepare(
        "SELECT * FROM otp_requests WHERE mobile = ? AND used_at IS NULL ORDER BY created_at DESC LIMIT 1"
      );
      $stmt->execute([$mobile]);
      $otpRow = $stmt->fetch();
      if (!$otpRow) {
        $errorMessage = "OTP পাওয়া যায়নি। আবার পাঠান।";
      } elseif (strtotime($otpRow["expires_at"]) < time()) {
        $errorMessage = "OTP মেয়াদ শেষ হয়েছে। আবার পাঠান।";
      } elseif ((int)$otpRow["attempts"] >= (int)config("sms.otp_max_attempts", 5)) {
        $errorMessage = "একাধিক ভুল চেষ্টা হয়েছে। আবার OTP পাঠান।";
      } elseif (!password_verify($otpCode, $otpRow["code_hash"])) {
        $pdo->prepare(
          "UPDATE otp_requests SET attempts = attempts + 1 WHERE id = ?"
        )->execute([(int)$otpRow["id"]]);
        $errorMessage = "OTP সঠিক নয়। আবার চেষ্টা করুন।";
      } else {
        $pdo->beginTransaction();
        try {
          $stmt = $pdo->prepare("SELECT id FROM users WHERE mobile = ?");
          $stmt->execute([$mobile]);
          if ($stmt->fetch()) {
            $pdo->rollBack();
            $errorMessage = "এই মোবাইল নম্বরটি ইতিমধ্যেই নিবন্ধিত।";
          } else {
            $referrerId = null;
            if ($referralCode !== "") {
              $stmt = $pdo->prepare("SELECT id FROM users WHERE referral_code = ?");
              $stmt->execute([$referralCode]);
              $referrerId = $stmt->fetchColumn() ?: null;
            }

            $refCode = generate_referral_code();
            $stmt = $pdo->prepare(
              "INSERT INTO users (mobile, password_hash, referral_code, referred_by_user_id, credits_balance, referral_balance, monthly_score, created_at)
               VALUES (?, ?, ?, ?, ?, 0, 0, NOW())"
            );
            $stmt->execute([
              $mobile,
              $pending["password_hash"],
              $refCode,
              $referrerId,
              (int)config("credits.signup_bonus", 100),
            ]);
            $userId = (int)$pdo->lastInsertId();

            if ($referrerId) {
              $reward = (int)config("credits.referral_reward", 50);
              $pdo->prepare(
                "INSERT INTO referrals (referrer_id, referred_user_id, bonus_used_at, first_purchase_at, reward_given, reward_amount, created_at)
                 VALUES (?, ?, NOW(), NOW(), 1, ?, NOW())"
              )->execute([
                $referrerId,
                $userId,
                $reward,
              ]);
              $pdo->prepare(
                "UPDATE users SET referral_balance = referral_balance + ? WHERE id = ?"
              )->execute([$reward, $referrerId]);
              create_transaction(
                (int)$referrerId,
                "referral_credit",
                $reward,
                ["source_user_id" => $userId],
                "completed"
              );
            }

            create_transaction(
              $userId,
              "bonus",
              (int)config("credits.signup_bonus", 100),
              ["source" => "signup"],
              "completed"
            );

            $pdo->prepare(
              "UPDATE otp_requests SET used_at = NOW() WHERE id = ?"
            )->execute([(int)$otpRow["id"]]);

            $pdo->commit();
            if ($referrerId) {
              setcookie(
                "referral_code",
                "",
                [
                  "expires" => time() - 3600,
                  "path" => "/",
                  "secure" => !empty($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off",
                  "httponly" => true,
                  "samesite" => "Lax",
                ]
              );
            }
            unset($_SESSION["signup_pending"]);
            session_regenerate_id(true);
            $_SESSION["user_id"] = $userId;
            redirect("/user/dashboard.php");
          }
        } catch (Throwable $e) {
          $pdo->rollBack();
          $errorMessage = "সাইনআপ সম্পন্ন করা যায়নি। আবার চেষ্টা করুন।";
        }
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
    <title>QuizTap - OTP যাচাই</title>
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
            <h1 class="promo-title">OTP যাচাই করে সাইনআপ সম্পন্ন করুন।</h1>
            <p class="text-white-50 mb-4">
              আপনার মোবাইলে ৬ ডিজিটের OTP পাঠানো হয়েছে।
            </p>
          </section>
        </div>
        <div class="col-lg-6 reveal delay-1">
          <section class="auth-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <h2 class="mb-1">OTP যাচাই</h2>
                <p class="text-muted mb-0">মোবাইল: <?php echo e($mobile); ?></p>
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
              <div class="text-success small mb-3"><?php echo e($infoMessage); ?></div>
            <?php } ?>

            <form method="post" novalidate data-auth-form data-otp-step="1">
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="action" value="verify" />
              <div class="mb-3">
                <label class="form-label" for="otpCode">OTP কোড</label>
                <input
                  id="otpCode"
                  name="otp_code"
                  type="text"
                  class="form-control"
                  placeholder="123456"
                  inputmode="numeric"
                  maxlength="6"
                  data-otp
                  required
                />
                <div class="invalid-feedback d-block" data-otp-error></div>
              </div>
              <button class="btn btn-primary w-100 mb-2" type="submit">
                OTP যাচাই করুন
              </button>
            </form>

            <form method="post">
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="action" value="resend" />
              <button class="btn btn-outline-dark w-100" type="submit">
                আবার OTP পাঠান
              </button>
            </form>
          </section>
        </div>
      </div>
    </main>
    <script src="../assets/js/auth.js"></script>
  </body>
</html>
