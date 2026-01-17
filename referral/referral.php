<?php
$pageTitle = "QuizTap - রেফারেল";
$pageTag = "রেফারেল রিওয়ার্ড: ৫০ TK";
$pageMeta = "শর্ত পূরণ হলেই ক্রেডিট";
$activeTab = "referral";
require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch">
        <div class="col-lg-7 reveal">
          <div class="glass-card p-4 h-100">
            <h2 class="mb-3">আপনার রেফারেল লিংক</h2>
            <p class="text-muted">
              বন্ধু বা পরিচিতদের সাথে শেয়ার করুন। তারা বোনাস ব্যবহার করে প্রথম
              ক্রেডিট কেনা সম্পন্ন করলে আপনি রিওয়ার্ড পাবেন।
            </p>
            <div class="d-flex flex-wrap align-items-center justify-content-between border rounded-3 p-3 mb-4">
              <div>
                <div class="fw-semibold" id="referralLink">
                  quiztap.com/r/shahed22
                </div>
                <div class="text-muted small">এক ক্লিকে কপি করুন</div>
              </div>
              <button class="btn btn-outline-dark btn-sm" type="button" data-copy="referralLink">
                কপি
              </button>
            </div>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="soft-card p-3 h-100">
                  <div class="text-muted text-uppercase small">মোট রেফারাল</div>
                  <div class="metric">12</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="soft-card p-3 h-100">
                  <div class="text-muted text-uppercase small">যোগ্য রেফারাল</div>
                  <div class="metric">5</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">রিওয়ার্ড শর্ত</h3>
            <div class="list-row mb-3">
              <div>
                <div class="fw-semibold">১) বোনাস খরচ সম্পূর্ণ</div>
                <div class="text-muted small">১০০ TK বোনাস শেষ হলে</div>
              </div>
              <span class="tag">সম্পন্ন</span>
            </div>
            <div class="list-row">
              <div>
                <div class="fw-semibold">২) প্রথম ক্রেডিট কেনা</div>
                <div class="text-muted small">মিনিমাম ৫০ TK</div>
              </div>
              <span class="tag">বাকি</span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">সাম্প্রতিক রেফারাল</h3>
            <div class="list-row mb-2">
              <div>
                <div class="fw-semibold">01900XXXXXX</div>
                <div class="text-muted small">সাইনআপ: ৫ সেপ্টেম্বর</div>
              </div>
              <span class="tag">বোনাস ব্যবহার</span>
            </div>
            <div class="list-row">
              <div>
                <div class="fw-semibold">01800XXXXXX</div>
                <div class="text-muted small">সাইনআপ: ৩ সেপ্টেম্বর</div>
              </div>
              <span class="tag">ক্রয় সম্পন্ন</span>
            </div>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
