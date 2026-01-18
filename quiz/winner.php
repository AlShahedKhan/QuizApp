<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$lastMonth = date("Y-m", strtotime("first day of last month"));
$displayMonth = date("F Y", strtotime("first day of last month"));

$stmt = db()->prepare(
  "SELECT w.score, w.prize_amount, u.mobile
   FROM monthly_winners w
   JOIN users u ON u.id = w.user_id
   WHERE w.month_year = ?
   LIMIT 1"
);
$stmt->execute([$lastMonth]);
$winner = $stmt->fetch();
$winnerName = $winner ? $winner["mobile"] : "TBD";
$winnerScore = $winner ? (int)$winner["score"] : 0;
$winnerPrize = $winner ? (int)$winner["prize_amount"] : 0;

$stmt = db()->prepare(
  "SELECT COUNT(DISTINCT user_id) FROM quiz_attempts WHERE month_year = ?"
);
$stmt->execute([$lastMonth]);
$participants = (int)$stmt->fetchColumn();

$stmt = db()->prepare(
  "SELECT COUNT(*) FROM users WHERE monthly_score >= ?"
);
$stmt->execute([(int)config("prize.min_score", 5000)]);
$eligible = (int)$stmt->fetchColumn();

$pageTitle = "QuizTap - উইনার";
$pageTag = "গত মাসের বিজয়ী";
$pageMeta = $displayMonth;
$activeTab = "winner";

require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch">
        <div class="col-lg-7 reveal">
          <div class="glass-card p-4 h-100">
            <h2 class="mb-3">চ্যাম্পিয়ন: <?php echo e($winnerName); ?></h2>
            <p class="text-muted mb-4">
              <?php if ($winner) { ?>
                গত মাসে সর্বোচ্চ <?php echo e($winnerScore); ?> পয়েন্ট সংগ্রহ করে বিজয়ী হয়েছেন।
                ধারাবাহিক পারফরম্যান্সে তিনি শীর্ষে ছিলেন।
              <?php } else { ?>
                গত মাসে কোনো বিজয়ী ঘোষণা হয়নি।
              <?php } ?>
            </p>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="soft-card p-3 h-100">
                  <div class="text-muted text-uppercase small">মোট পয়েন্ট</div>
              <div class="metric"><?php echo e($winnerScore); ?></div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="soft-card p-3 h-100">
                  <div class="text-muted text-uppercase small">পুরস্কার</div>
              <div class="metric"><?php echo e(format_tk($winnerPrize)); ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">মাসিক সারসংক্ষেপ</h3>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">মোট অংশগ্রহণকারী</span>
              <span class="fw-semibold"><?php echo e($participants); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">যোগ্য ইউজার</span>
              <span class="fw-semibold"><?php echo e($eligible); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">পুরস্কার বাজেট</span>
              <span class="fw-semibold"><?php echo e(format_tk((int)config("prize.max_prize", 30000))); ?></span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">নতুন মাসের লক্ষ্য</h3>
            <p class="text-muted mb-3">
              টপ ৫-এ থাকতে হলে মিনিমাম ৫,০০০ পয়েন্ট সংগ্রহ করুন।
            </p>
            <a class="btn btn-primary w-100" href="../quiz/play.php">
              কুইজ শুরু করুন
            </a>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
