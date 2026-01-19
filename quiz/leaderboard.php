<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$user = current_user();
$monthYear = current_month_year();
$pointsPerCorrect = (int)config("quiz.points_per_correct", 1);
$setId = null;
$stmt = db()->prepare(
  "SELECT id FROM quiz_question_sets WHERE month_year = ? AND is_active = 1"
);
$stmt->execute([$monthYear]);
$setId = $stmt->fetchColumn() ?: null;
$setItemsSql = "";
if ($setId) {
  $setItemsSql = "SELECT question_id
    FROM quiz_question_set_items
    WHERE set_id = ?
    ORDER BY position ASC, id ASC";
}
$pageTitle = "QuizTap - লিডারবোর্ড";
$pageTag = "চলতি মাসের লিডারবোর্ড";
$pageMeta = "শেষ আপডেট: " . date("g:i A");
$activeTab = "leaderboard";

$leaders = [];
$rank = 1;
$userScore = 0;
if ($setId) {
  $stmt = db()->prepare(
    "SELECT u.id, u.mobile, SUM(a.is_correct) * ? AS score, COUNT(a.id) AS total_attempts
     FROM quiz_attempts a
     INNER JOIN (" . $setItemsSql . ") s ON s.question_id = a.question_id
     INNER JOIN users u ON u.id = a.user_id
     WHERE a.month_year = ?
     GROUP BY a.user_id, u.mobile
     ORDER BY score DESC, u.id ASC
     LIMIT 5"
  );
  $stmt->execute([$pointsPerCorrect, $setId, $monthYear]);
  $leaders = $stmt->fetchAll();

  $stmt = db()->prepare(
    "SELECT SUM(a.is_correct) * ? AS score
     FROM quiz_attempts a
     INNER JOIN (" . $setItemsSql . ") s ON s.question_id = a.question_id
     WHERE a.user_id = ? AND a.month_year = ?"
  );
  $stmt->execute([$pointsPerCorrect, $setId, (int)$user["id"], $monthYear]);
  $userScore = (int)($stmt->fetchColumn() ?? 0);

  $stmt = db()->prepare(
    "SELECT COUNT(*) + 1
     FROM (
       SELECT a.user_id, SUM(a.is_correct) * ? AS score
       FROM quiz_attempts a
       INNER JOIN (" . $setItemsSql . ") s ON s.question_id = a.question_id
       WHERE a.month_year = ?
       GROUP BY a.user_id
       HAVING score > ?
     ) ranked"
  );
  $stmt->execute([$pointsPerCorrect, $setId, $monthYear, $userScore]);
  $rank = (int)$stmt->fetchColumn();
} else {
  $stmt = db()->prepare(
    "SELECT u.id, u.mobile, SUM(a.is_correct) * ? AS score, COUNT(a.id) AS total_attempts
     FROM quiz_attempts a
     INNER JOIN users u ON u.id = a.user_id
     WHERE a.month_year = ?
     GROUP BY a.user_id, u.mobile
     ORDER BY score DESC, u.id ASC
     LIMIT 5"
  );
  $stmt->execute([$pointsPerCorrect, $monthYear]);
  $leaders = $stmt->fetchAll();

  $stmt = db()->prepare(
    "SELECT SUM(is_correct) * ? FROM quiz_attempts WHERE user_id = ? AND month_year = ?"
  );
  $stmt->execute([$pointsPerCorrect, (int)$user["id"], $monthYear]);
  $userScore = (int)($stmt->fetchColumn() ?? 0);

  $stmt = db()->prepare(
    "SELECT COUNT(*) + 1
     FROM (
       SELECT user_id, SUM(is_correct) * ? AS score
       FROM quiz_attempts
       WHERE month_year = ?
       GROUP BY user_id
       HAVING score > ?
     ) ranked"
  );
  $stmt->execute([$pointsPerCorrect, $monthYear, $userScore]);
  $rank = (int)$stmt->fetchColumn();
}
$rankLabel = $rank <= 20 ? "টপ ২০" : "টপ ৫০";

require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4">
        <div class="col-lg-7 reveal">
          <div class="soft-card p-4">
            <h2 class="mb-3">টপ স্কোরার</h2>
            <?php if (!$leaders) { ?>
              <div class="text-muted">কোনো স্কোর নেই।</div>
            <?php } ?>
            <?php foreach ($leaders as $index => $leader) { ?>
              <div class="leaderboard-row mb-3">
                <div>
                  <div class="fw-semibold"><?php echo e($index + 1); ?>. <?php echo e($leader["mobile"]); ?></div>
                  <div class="text-muted small">মোট প্রশ্ন: <?php echo e((int)$leader["total_attempts"]); ?></div>
                </div>
                <div class="fw-semibold"><?php echo e((int)$leader["score"]); ?></div>
              </div>
            <?php } ?>
            <?php if (false) { ?>
            <div class="leaderboard-row mb-3">
              <div>
                <div class="fw-semibold">১. নাবিলা ইসলাম</div>
                <div class="text-muted small">মোট প্রশ্ন: 420</div>
              </div>
              <div class="fw-semibold">980</div>
            </div>
            <div class="leaderboard-row mb-3">
              <div>
                <div class="fw-semibold">২. আরিফ হাসান</div>
                <div class="text-muted small">মোট প্রশ্ন: 398</div>
              </div>
              <div class="fw-semibold">930</div>
            </div>
            <div class="leaderboard-row mb-3">
              <div>
                <div class="fw-semibold">৩. তানিম রায়হান</div>
                <div class="text-muted small">মোট প্রশ্ন: 382</div>
              </div>
              <div class="fw-semibold">910</div>
            </div>
            <div class="leaderboard-row mb-3">
              <div>
                <div class="fw-semibold">৪. সামিহা রহমান</div>
                <div class="text-muted small">মোট প্রশ্ন: 361</div>
              </div>
              <div class="fw-semibold">880</div>
            </div>
            <div class="leaderboard-row">
              <div>
                <div class="fw-semibold">৫. হাসান আলী</div>
                <div class="text-muted small">মোট প্রশ্ন: 340</div>
              </div>
              <div class="fw-semibold">860</div>
            </div>
            <?php } ?>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-1">
          <div class="glass-card p-4 mb-4">
            <h3 class="mb-3">আপনার অবস্থান</h3>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">র‍্যাঙ্ক</span>
              <span class="fw-semibold">#<?php echo e($rank); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">স্কোর</span>
              <span class="fw-semibold"><?php echo e((int)$userScore); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">যোগ্যতা</span>
              <span class="tag"><?php echo e($rankLabel); ?></span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">পুরস্কার লক্ষ্য</h3>
            <p class="text-muted mb-3">
              টপ ৫-এ প্রবেশ করলে মাসিক বোনাস জিতে নেওয়ার সুযোগ থাকবে।
            </p>
            <div class="list-row">
              <div>
                <div class="fw-semibold">পরবর্তী র‍্যাঙ্ক</div>
                <div class="text-muted small">আরও 53 পয়েন্ট দরকার</div>
              </div>
              <div class="fw-semibold">#10</div>
            </div>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
