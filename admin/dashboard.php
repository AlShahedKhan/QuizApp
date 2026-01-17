<?php
$pageTitle = "QuizTap অ্যাডমিন - ড্যাশবোর্ড";
$pageTag = "সেপ্টেম্বর ২০২৪";
$pageMeta = "রিসেট বাকি ১২ দিন";
$activeNav = "dashboard";
require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="row g-4 mb-4">
      <div class="col-md-6 col-xl-3 reveal">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">মোট ব্যবহারকারী</div>
          <div class="metric">২,৮৪০</div>
          <p class="text-muted mb-0">এই মাসে +১২০ নতুন</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3 reveal delay-1">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">সক্রিয় খেলোয়াড়</div>
          <div class="metric">১,৩৯২</div>
          <p class="text-muted mb-0">গত ৭ দিনে খেলেছে</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3 reveal delay-2">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">পেন্ডিং উইথড্র</div>
          <div class="metric">৬</div>
          <p class="text-muted mb-0">রিভিউ দরকার</p>
        </div>
      </div>
      <div class="col-md-6 col-xl-3 reveal delay-3">
        <div class="soft-card p-4 h-100">
          <div class="text-muted text-uppercase small">প্রশ্ন ভাণ্ডার</div>
          <div class="metric">৩১২</div>
          <p class="text-muted mb-0">এই মাসে ১২৪ সক্রিয়</p>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-7 reveal delay-1">
        <div class="table-card">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>সাম্প্রতিক কার্যক্রম</th>
                <th>ধরণ</th>
                <th>পরিমাণ</th>
                <th>সময়</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>
                  <div class="fw-semibold">Nabila Ahmed</div>
                  <div class="text-muted small">TXN-১২০৯৩</div>
                </td>
                <td>ক্রয়</td>
                <td>+২০০ TK</td>
                <td class="text-muted small">১১:৪২ এএম</td>
              </tr>
              <tr>
                <td>
                  <div class="fw-semibold">Samiha Noor</div>
                  <div class="text-muted small">TXN-১২০৯১</div>
                </td>
                <td>রেফারেল</td>
                <td>+৫০ TK</td>
                <td class="text-muted small">১১:১০ এএম</td>
              </tr>
              <tr>
                <td>
                  <div class="fw-semibold">Tanvir Rahman</div>
                  <div class="text-muted small">TXN-১২০৯০</div>
                </td>
                <td>বোনাস</td>
                <td>+১০০ TK</td>
                <td class="text-muted small">১০:৫৮ এএম</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="col-lg-5 reveal delay-2">
        <div class="soft-card p-4 mb-4">
          <h3 class="mb-3">পেন্ডিং উইথড্র</h3>
          <div class="list-row mb-3">
            <div>
              <div class="fw-semibold">Nabila Ahmed</div>
              <div class="text-muted small">বিকাশ • ৮০ TK</div>
            </div>
            <span class="tag">পেন্ডিং</span>
          </div>
          <div class="list-row">
            <div>
              <div class="fw-semibold">Samiha Noor</div>
              <div class="text-muted small">নগদ • ২০০ TK</div>
            </div>
            <span class="tag">পেন্ডিং</span>
          </div>
        </div>
        <div class="soft-card p-4">
          <h3 class="mb-3">শীর্ষ স্কোরার প্রিভিউ</h3>
          <div class="leaderboard-row mb-2">
            <span>১. Nabila Ahmed</span>
            <span class="fw-semibold">১,২৪০</span>
          </div>
          <div class="leaderboard-row mb-2">
            <span>২. Samiha Noor</span>
            <span class="fw-semibold">১,১৮০</span>
          </div>
          <div class="leaderboard-row">
            <span>৩. Tanvir Rahman</span>
            <span class="fw-semibold">১,০১০</span>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
