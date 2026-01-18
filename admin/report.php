<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - মাসিক রিপোর্ট";
$pageTag = "মাসিক রিপোর্ট";
$pageMeta = date("F Y");
$activeNav = "report";

$minScore = (int)config("prize.min_score", 5000);
$perPoint = (int)config("prize.per_point", 5);
$maxPrize = (int)config("prize.max_prize", 30000);

$stmt = db()->prepare("SELECT COUNT(*) FROM users WHERE monthly_score >= ?");
$stmt->execute([$minScore]);
$eligibleCount = (int)$stmt->fetchColumn();

$stmt = db()->query(
  "SELECT id, mobile, monthly_score FROM users ORDER BY monthly_score DESC, id ASC LIMIT 5"
);
$topScores = $stmt->fetchAll();

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

        <section>
          <div class="row g-4 mb-4">
            <div class="col-md-6 reveal">
              <div class="soft-card p-4 h-100">
                <div class="text-muted text-uppercase small">যোগ্য খেলোয়াড়</div>
                <div class="metric"><?php echo e($eligibleCount); ?></div>
                <p class="text-muted mb-0">সর্বনিম্ন স্কোর: <?php echo e($minScore); ?></p>
              </div>
            </div>
            <div class="col-md-6 reveal delay-1">
              <div class="soft-card p-4 h-100">
                <div class="text-muted text-uppercase small">পুরস্কার তহবিল</div>
                <div class="metric"><?php echo e(format_tk($maxPrize)); ?></div>
                <p class="text-muted mb-0">সর্বোচ্চ <?php echo e(format_tk($maxPrize)); ?></p>
              </div>
            </div>
          </div>

          <div class="soft-card p-4 mb-4 reveal delay-2">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
              <div>
                <h2 class="mb-1">শীর্ষ ৫ স্কোরার</h2>
                <p class="text-muted mb-0">
                  মাসিক বিজয়ী যাচাইয়ের জন্য এই তালিকা ব্যবহার করুন।
                </p>
              </div>
              <button class="btn btn-primary btn-sm" type="button">
                বিজয়ী প্রকাশ করুন
              </button>
            </div>
          </div>

          <div class="table-card reveal delay-3">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>র‍্যাঙ্ক</th>
                  <th>ব্যবহারকারী</th>
                  <th>স্কোর</th>
                  <th>অনুমানিক পুরস্কার</th>
                  <th>স্ট্যাটাস</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!$topScores) { ?>
                  <tr>
                    <td colspan="5" class="text-muted">কোনো ডেটা নেই।</td>
                  </tr>
                <?php } ?>
                <?php foreach ($topScores as $index => $row) {
                  $score = (int)$row["monthly_score"];
                  $prize = min($maxPrize, $score * $perPoint);
                  $eligible = $score >= $minScore;
                ?>
                  <tr>
                    <td>#<?php echo e($index + 1); ?></td>
                    <td><?php echo e($row["mobile"]); ?></td>
                    <td><?php echo e($score); ?></td>
                    <td><?php echo e($eligible ? format_tk($prize) : "-"); ?></td>
                    <td>
                      <?php if ($eligible) { ?>
                        <span class="badge bg-success-subtle text-success">যোগ্য</span>
                      <?php } else { ?>
                        <span class="badge bg-secondary-subtle text-secondary">ন্যূনতমের নিচে</span>
                      <?php } ?>
                    </td>
                  </tr>
                <?php } ?>
                <?php if (false) { ?>
                <tr>
                  <td>#১</td>
                  <td>Nabila Ahmed</td>
                  <td>৬,১২০</td>
                  <td>৩০,০০০ TK</td>
                  <td><span class="badge bg-success-subtle text-success">যোগ্য</span></td>
                </tr>
                <tr>
                  <td>#২</td>
                  <td>Samiha Noor</td>
                  <td>৫,৮৮০</td>
                  <td>২৯,৪০০ TK</td>
                  <td><span class="badge bg-success-subtle text-success">যোগ্য</span></td>
                </tr>
                <tr>
                  <td>#৩</td>
                  <td>Tanvir Rahman</td>
                  <td>৫,৪৬০</td>
                  <td>২৭,৩০০ TK</td>
                  <td><span class="badge bg-success-subtle text-success">যোগ্য</span></td>
                </tr>
                <tr>
                  <td>#৪</td>
                  <td>Arif Hasan</td>
                  <td>৫,১২০</td>
                  <td>২৫,৬০০ TK</td>
                  <td><span class="badge bg-success-subtle text-success">যোগ্য</span></td>
                </tr>
                <tr>
                  <td>#৫</td>
                  <td>Sadia Rahman</td>
                  <td>৪,৯৮০</td>
                  <td>-</td>
                  <td><span class="badge bg-secondary-subtle text-secondary">ন্যূনতমের নিচে</span></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
