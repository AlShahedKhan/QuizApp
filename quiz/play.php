<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$user = current_user();
$costPerQuestion = (int)config("quiz.cost_per_question", 1);
$pointsPerCorrect = (int)config("quiz.points_per_correct", 1);
$monthYear = current_month_year();
$errorMessage = "";
$resultMessage = flash("quiz_result");
$submittedQuestionId = 0;

if (is_post()) {
  require_csrf();
  $questionId = (int)($_POST["question_id"] ?? 0);
  $submittedQuestionId = $questionId;
  $selected = trim($_POST["selected_option"] ?? "");

  if (!$questionId || !in_array($selected, ["A", "B", "C", "D"], true)) {
    $errorMessage = "উত্তর নির্বাচন করুন।";
  } elseif ((int)$user["credits_balance"] < $costPerQuestion) {
    $errorMessage = "আপনার পর্যাপ্ত ক্রেডিট নেই।";
  } else {
    $stmt = db()->prepare(
      "SELECT id, correct_option FROM quiz_questions WHERE id = ? AND is_active = 1"
    );
    $stmt->execute([$questionId]);
    $question = $stmt->fetch();
    if (!$question) {
      $errorMessage = "প্রশ্নটি আর সক্রিয় নেই।";
    } else {
      $stmt = db()->prepare(
        "SELECT id FROM quiz_attempts WHERE user_id = ? AND question_id = ? AND month_year = ?"
      );
      $stmt->execute([(int)$user["id"], $questionId, $monthYear]);
      if ($stmt->fetch()) {
        $errorMessage = "এই প্রশ্নটি আগে উত্তর দিয়েছেন।";
      } else {
        $pdo = db();
        $pdo->beginTransaction();
        try {
          $isCorrect = strtoupper($selected) === $question["correct_option"];
          $pdo->prepare(
            "INSERT INTO quiz_attempts (user_id, question_id, month_year, is_correct, created_at)
             VALUES (?, ?, ?, ?, NOW())"
          )->execute([
            (int)$user["id"],
            $questionId,
            $monthYear,
            $isCorrect ? 1 : 0,
          ]);
          $pdo->prepare(
            "UPDATE users SET credits_balance = credits_balance - ? WHERE id = ?"
          )->execute([$costPerQuestion, (int)$user["id"]]);
          create_transaction(
            (int)$user["id"],
            "quiz_deduct",
            $costPerQuestion,
            ["question_id" => $questionId],
            "completed"
          );
          if ($isCorrect) {
            $pdo->prepare(
              "UPDATE users SET monthly_score = monthly_score + ? WHERE id = ?"
            )->execute([$pointsPerCorrect, (int)$user["id"]]);
          }
          $pdo->commit();
          mark_bonus_used_if_needed((int)$user["id"]);
          flash("quiz_result", $isCorrect ? "সঠিক উত্তর!" : "ভুল উত্তর।");
          redirect("/quiz/play.php");
        } catch (Throwable $e) {
          $pdo->rollBack();
          $errorMessage = "উত্তর জমা দেওয়া যায়নি। আবার চেষ্টা করুন।";
        }
      }
    }
  }
}

$stmt = db()->prepare(
  "SELECT COUNT(*) FROM quiz_attempts WHERE user_id = ? AND month_year = ?"
);
$stmt->execute([(int)$user["id"], $monthYear]);
$attemptedCount = (int)$stmt->fetchColumn();

$stmt = db()->prepare(
  "SELECT COUNT(*) FROM quiz_attempts WHERE user_id = ? AND month_year = ? AND is_correct = 1"
);
$stmt->execute([(int)$user["id"], $monthYear]);
$correctCount = (int)$stmt->fetchColumn();

$stmt = db()->prepare("SELECT COUNT(*) FROM quiz_questions WHERE is_active = 1");
$stmt->execute();
$totalQuestions = (int)$stmt->fetchColumn();

$currentQuestion = null;
if ($errorMessage && $submittedQuestionId) {
  $stmt = db()->prepare("SELECT * FROM quiz_questions WHERE id = ? AND is_active = 1");
  $stmt->execute([$submittedQuestionId]);
  $currentQuestion = $stmt->fetch();
}
if (!$currentQuestion) {
  $stmt = db()->prepare(
    "SELECT q.*
     FROM quiz_questions q
     LEFT JOIN quiz_attempts a
       ON a.question_id = q.id AND a.user_id = ? AND a.month_year = ?
     WHERE q.is_active = 1 AND a.id IS NULL
     ORDER BY RAND()
     LIMIT 1"
  );
  $stmt->execute([(int)$user["id"], $monthYear]);
  $currentQuestion = $stmt->fetch();
  if (!$currentQuestion) {
    $stmt = db()->prepare("SELECT * FROM quiz_questions WHERE is_active = 1 ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $currentQuestion = $stmt->fetch();
  }
}

$stmt = db()->prepare(
  "SELECT mobile, monthly_score FROM users ORDER BY monthly_score DESC, id ASC LIMIT 5"
);
$stmt->execute();
$leaderboard = $stmt->fetchAll();

$pageTitle = "QuizTap - কুইজ খেলুন";
$pageTag = "প্রতি প্রশ্নে খরচ " . $costPerQuestion . " ক্রেডিট";
$pageMeta = "স্ট্রিক: " . $correctCount;
$activeTab = "play";

require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch mb-4">
        <div class="col-lg-8 reveal">
          <div class="glass-card p-4 h-100">
            <?php if ($errorMessage) { ?>
              <div class="text-danger small mb-3"><?php echo e($errorMessage); ?></div>
            <?php } ?>
            <?php if ($resultMessage) { ?>
              <div class="text-success small mb-3"><?php echo e($resultMessage); ?></div>
            <?php } ?>
            <?php if (!$currentQuestion) { ?>
              <div class="text-muted">এই মুহূর্তে কোনো প্রশ্ন নেই।</div>
            <?php } else { ?>
              <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
                <input type="hidden" name="question_id" value="<?php echo e((int)$currentQuestion["id"]); ?>" />
                <input type="hidden" name="selected_option" value="" data-selected-option />
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="tag">প্রশ্ন <?php echo e($attemptedCount + 1); ?> / <?php echo e($totalQuestions); ?></span>
                <h2 class="mt-2">
                  <?php echo e($currentQuestion["question_bn"] ?? ""); ?>
                </h2>
              </div>
              <div class="text-end">
                <div class="text-muted small">সময় বাকি</div>
                <div class="metric">০০:২৪</div>
              </div>
            </div>
            <div class="d-grid gap-3 mb-4">
              <div class="option-card" data-option data-option-value="A">ক) <?php echo e($currentQuestion["option_a_bn"] ?? ""); ?></div>
              <div class="option-card" data-option data-option-value="B">খ) <?php echo e($currentQuestion["option_b_bn"] ?? ""); ?></div>
              <div class="option-card" data-option data-option-value="C">গ) <?php echo e($currentQuestion["option_c_bn"] ?? ""); ?></div>
              <div class="option-card" data-option data-option-value="D">ঘ) <?php echo e($currentQuestion["option_d_bn"] ?? ""); ?></div>
            </div>
            <div class="d-flex flex-wrap gap-2">
              <a class="btn btn-outline-dark" href="/quiz/play.php">স্কিপ</a>
              <button class="btn btn-primary" type="submit">উত্তর জমা দিন</button>
            </div>
          </form>
          <?php } ?>
          </div>
        </div>
        <div class="col-lg-4 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">আপনার স্ট্যাটস</h3>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">ক্রেডিট বাকি</span>
              <span class="fw-semibold"><?php echo e(format_tk((int)$user["credits_balance"])); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">চলতি পয়েন্ট</span>
              <span class="fw-semibold"><?php echo e((int)$user["monthly_score"]); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">সঠিক উত্তর</span>
              <span class="fw-semibold"><?php echo e($correctCount); ?> / <?php echo e($attemptedCount); ?></span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">এই মাসের টপ ৫</h3>
            <?php if (!$leaderboard) { ?>
              <div class="text-muted">কোনো স্কোর নেই।</div>
            <?php } ?>
            <?php foreach ($leaderboard as $index => $entry) { ?>
              <div class="leaderboard-row mb-2">
                <span><?php echo e($index + 1); ?>. <?php echo e($entry["mobile"]); ?></span>
                <span class="fw-semibold"><?php echo e((int)$entry["monthly_score"]); ?></span>
              </div>
            <?php } ?>
            <?php if (false) { ?>
            <div class="leaderboard-row mb-2">
              <span>১. নাবিলা</span>
              <span class="fw-semibold">980</span>
            </div>
            <div class="leaderboard-row mb-2">
              <span>২. আরিফ</span>
              <span class="fw-semibold">930</span>
            </div>
            <div class="leaderboard-row mb-2">
              <span>৩. তানিম</span>
              <span class="fw-semibold">910</span>
            </div>
            <div class="leaderboard-row mb-2">
              <span>৪. সামিহা</span>
              <span class="fw-semibold">880</span>
            </div>
            <div class="leaderboard-row">
              <span>৫. হাসান</span>
              <span class="fw-semibold">860</span>
            </div>
            <?php } ?>
          </div>
        </div>
      </section>

      <section class="soft-card p-4 reveal delay-2">
        <div class="row g-4 align-items-center">
          <div class="col-md-8">
            <h3 class="mb-2">মাসিক উইনার স্পটলাইট</h3>
            <p class="text-muted mb-0">
              গত মাসে ফারজানা ১,২২০ পয়েন্ট নিয়ে শীর্ষে ছিলেন। টপ ৫-এ থাকলে
              বিশেষ বোনাস পাওয়া যাবে।
            </p>
          </div>
          <div class="col-md-4 text-md-end">
            <a class="btn btn-outline-dark" href="../quiz/leaderboard.php">
              পূর্ণ লিডারবোর্ড
            </a>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
