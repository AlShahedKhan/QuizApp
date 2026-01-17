<?php
$pageTitle = "QuizTap - রেফারেল উইথড্র";
$pageTag = "রেফারেল ব্যালেন্স: 320 TK";
$pageMeta = "উইথড্র শুধুই রেফারেল থেকে";
$activeTab = "withdraw";
$tabs = [
  "play" => ["label" => "কুইজ খেলুন", "href" => "../quiz/play.php"],
  "withdraw" => ["label" => "উইথড্র", "href" => "#"],
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
            <h2 class="mb-3">উইথড্র অনুরোধ</h2>
            <form>
              <div class="mb-3">
                <label class="form-label" for="method">পেমেন্ট মাধ্যম</label>
                <select class="form-select" id="method">
                  <option selected>বিকাশ</option>
                  <option>নগদ</option>
                </select>
              </div>
              <div class="mb-3">
                <label class="form-label" for="account">মোবাইল নম্বর</label>
                <input
                  type="tel"
                  class="form-control"
                  id="account"
                  placeholder="01XXXXXXXXX"
                />
              </div>
              <div class="mb-4">
                <label class="form-label" for="amount">উইথড্র পরিমাণ</label>
                <input
                  type="number"
                  class="form-control"
                  id="amount"
                  placeholder="100"
                  min="50"
                  step="10"
                />
                <div class="form-text text-muted">
                  সর্বোচ্চ 320 TK পর্যন্ত অনুরোধ করতে পারবেন।
                </div>
              </div>
              <button class="btn btn-primary w-100" type="button">
                উইথড্র রিকোয়েস্ট পাঠান
              </button>
            </form>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">সর্বশেষ স্ট্যাটাস</h3>
            <div class="list-row mb-3">
              <div>
                <div class="fw-semibold">বিকাশ উইথড্র</div>
                <div class="text-muted small">রিকোয়েস্ট: ১০ সেপ্টেম্বর</div>
              </div>
              <span class="tag">পেন্ডিং</span>
            </div>
            <div class="list-row">
              <div>
                <div class="fw-semibold">নগদ উইথড্র</div>
                <div class="text-muted small">রিকোয়েস্ট: ২ সেপ্টেম্বর</div>
              </div>
              <span class="tag">অনুমোদিত</span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">গুরুত্বপূর্ণ</h3>
            <p class="text-muted mb-0">
              কুইজ থেকে অর্জিত ক্রেডিট উইথড্র করা যাবে না। কেবল রেফারেল
              ব্যালেন্স উইথড্র করা যাবে।
            </p>
          </div>
        </div>
      </section>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
