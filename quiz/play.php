<?php
$pageTitle = "QuizTap - কুইজ খেলুন";
$pageTag = "প্রতি প্রশ্নে খরচ ১ ক্রেডিট";
$pageMeta = "স্ট্রিক: ৩";
$activeTab = "play";
require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch mb-4">
        <div class="col-lg-8 reveal">
          <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="tag">প্রশ্ন ৪ / ১০</span>
                <h2 class="mt-2">
                  বাংলাদেশের দীর্ঘতম নদী কোনটি?
                </h2>
              </div>
              <div class="text-end">
                <div class="text-muted small">সময় বাকি</div>
                <div class="metric">০০:২৪</div>
              </div>
            </div>
            <div class="d-grid gap-3 mb-4">
              <div class="option-card" data-option>ক) মেঘনা</div>
              <div class="option-card" data-option>খ) যমুনা</div>
              <div class="option-card" data-option>গ) পদ্মা</div>
              <div class="option-card" data-option>ঘ) কর্ণফুলী</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-outline-dark" type="button">স্কিপ</button>
              <button class="btn btn-primary" type="button">উত্তর জমা দিন</button>
            </div>
          </div>
        </div>
        <div class="col-lg-4 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">আপনার স্ট্যাটস</h3>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">ক্রেডিট বাকি</span>
              <span class="fw-semibold">85 TK</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">চলতি পয়েন্ট</span>
              <span class="fw-semibold">120</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">সঠিক উত্তর</span>
              <span class="fw-semibold">৭ / ৯</span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">এই মাসের টপ ৫</h3>
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
