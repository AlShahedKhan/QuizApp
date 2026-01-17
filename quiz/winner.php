<?php
$pageTitle = "QuizTap - উইনার";
$pageTag = "গত মাসের বিজয়ী";
$pageMeta = "আগস্ট ২০২৪";
$activeTab = "winner";
require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch">
        <div class="col-lg-7 reveal">
          <div class="glass-card p-4 h-100">
            <h2 class="mb-3">চ্যাম্পিয়ন: ফারজানা ইসলাম</h2>
            <p class="text-muted mb-4">
              গত মাসে সর্বোচ্চ ১,২২০ পয়েন্ট সংগ্রহ করে বিজয়ী হয়েছেন। ধারাবাহিক
              ১২ দিনের স্ট্রিক ছিল তাঁর সেরা অর্জন।
            </p>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="soft-card p-3 h-100">
                  <div class="text-muted text-uppercase small">মোট পয়েন্ট</div>
                  <div class="metric">1,220</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="soft-card p-3 h-100">
                  <div class="text-muted text-uppercase small">পুরস্কার</div>
                  <div class="metric">5,000 TK</div>
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
              <span class="fw-semibold">4,980</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">যোগ্য ইউজার</span>
              <span class="fw-semibold">620</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">পুরস্কার বাজেট</span>
              <span class="fw-semibold">30,000 TK</span>
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
