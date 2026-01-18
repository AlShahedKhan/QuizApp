<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$user = current_user();
$monthYear = current_month_year();
$pageTitle = "QuizTap - লিডারবোর্ড";
$pageTag = "চলতি মাসের লিডারবোর্ড";
$pageMeta = "শেষ আপডেট: " . date("g:i A");
$activeTab = "leaderboard";

$stmt = db()->prepare(
  "SELECT u.id, u.mobile, u.monthly_score,
          (SELECT COUNT(*) FROM quiz_attempts a WHERE a.user_id = u.id AND a.month_year = ?) AS total_attempts
   FROM users u
   ORDER BY u.monthly_score DESC, u.id ASC
   LIMIT 5"
);
$stmt->execute([$monthYear]);
$leaders = $stmt->fetchAll();

$stmt = db()->prepare("SELECT COUNT(*) + 1 FROM users WHERE monthly_score > ?");
$stmt->execute([(int)$user["monthly_score"]]);
$rank = (int)$stmt->fetchColumn();
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
                <div class="fw-semibold"><?php echo e((int)$leader["monthly_score"]); ?></div>
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
              <span class="fw-semibold"><?php echo e((int)$user["monthly_score"]); ?></span>
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
