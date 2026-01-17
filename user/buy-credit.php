<?php
$pageTitle = "QuizTap - ক্রেডিট কিনুন";
$pageTag = "ক্রেডিট রেট: 1 TK = 1 ক্রেডিট";
$pageMeta = "ন্যূনতম ৫০ TK";
$activeTab = "buy-credit";
$tabs = [
  "play" => ["label" => "কুইজ খেলুন", "href" => "../quiz/play.php"],
  "buy-credit" => ["label" => "ক্রেডিট কিনুন", "href" => "#"],
  "transactions" => ["label" => "লেনদেন", "href" => "../user/transactions.php"],
  "account" => ["label" => "ড্যাশবোর্ড", "href" => "../user/dashboard.php"],
];
require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <section class="row g-4 align-items-stretch">
        <div class="col-lg-7 reveal">
          <div class="glass-card p-4 h-100">
            <h2 class="mb-3">ক্রেডিট টপ-আপ</h2>
            <form>
              <div class="mb-3">
                <label class="form-label" for="amount">পরিমাণ (TK)</label>
                <input
                  type="number"
                  class="form-control"
                  id="amount"
                  placeholder="50"
                  min="50"
                  step="10"
                />
                <div class="form-text text-muted">
                  সর্বনিম্ন ৫০ TK থেকে শুরু করুন।
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="method">পেমেন্ট মাধ্যম</label>
                <select class="form-select" id="method">
                  <option selected>বিকাশ</option>
                  <option>নগদ</option>
                  <option>ব্যাংক ট্রান্সফার</option>
                </select>
              </div>
              <div class="mb-4">
                <label class="form-label" for="trx">ট্রান্সেকশন আইডি</label>
                <input
                  type="text"
                  class="form-control"
                  id="trx"
                  placeholder="TXN-XXXXXX"
                />
              </div>
              <button class="btn btn-primary w-100" type="button">
                টপ-আপ অনুরোধ পাঠান
              </button>
            </form>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">সারসংক্ষেপ</h3>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">আপনি পাবেন</span>
              <span class="fw-semibold">২০০ ক্রেডিট</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">প্রসেসিং সময়</span>
              <span class="fw-semibold">৫-১৫ মিনিট</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">স্ট্যাটাস</span>
              <span class="tag">পেন্ডিং</span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">সহায়তা</h3>
            <p class="text-muted mb-3">
              পেমেন্ট সম্পন্ন হলে অ্যাডমিন যাচাই করবে এবং ক্রেডিট যুক্ত হবে।
              জরুরি হলে সাপোর্টে যোগাযোগ করুন।
            </p>
            <div class="list-row">
              <div>
                <div class="fw-semibold">সাপোর্ট হটলাইন</div>
                <div class="text-muted small">10AM - 10PM</div>
              </div>
              <div class="fw-semibold">01911-000000</div>
            </div>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
