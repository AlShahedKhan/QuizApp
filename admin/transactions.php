<?php
$pageTitle = "QuizTap অ্যাডমিন - লেনদেন";
$pageTag = "লেজার";
$pageMeta = "আজ: ১২৮ এন্ট্রি";
$activeNav = "transactions";
require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">লেনদেন লগ</h2>
          <p class="text-muted mb-0">
            বোনাস, ক্রয়, কুইজ কাটছাঁট, এবং রেফারেল রিওয়ার্ড ট্র্যাক করুন।
          </p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-dark btn-sm" type="button">
            ফিল্টার
          </button>
          <button class="btn btn-primary btn-sm" type="button">
            এক্সপোর্ট
          </button>
        </div>
      </div>
    </div>

    <div class="table-card reveal delay-1">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>আইডি</th>
            <th>ব্যবহারকারী</th>
            <th>ধরণ</th>
            <th>পরিমাণ</th>
            <th>মেটা</th>
            <th>সময়</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>TXN-১২০৯৩</td>
            <td>Nabila Ahmed</td>
            <td><span class="badge bg-primary-subtle text-primary">ক্রয়</span></td>
            <td>+২০০ TK</td>
            <td class="text-muted small">বিকাশ ম্যানুয়াল</td>
            <td class="text-muted small">১১:৪২ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯২</td>
            <td>Arif Hasan</td>
            <td><span class="badge bg-warning-subtle text-warning">কুইজ</span></td>
            <td>-১ TK</td>
            <td class="text-muted small">প্রশ্ন #৩৪০</td>
            <td class="text-muted small">১১:৩৯ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯১</td>
            <td>Samiha Noor</td>
            <td><span class="badge bg-success-subtle text-success">রেফারেল</span></td>
            <td>+৫০ TK</td>
            <td class="text-muted small">রেফার্ড ০১৮০০-৭৭৪৪১১</td>
            <td class="text-muted small">১১:১০ এএম</td>
          </tr>
          <tr>
            <td>TXN-১২০৯০</td>
            <td>Tanvir Rahman</td>
            <td><span class="badge bg-info-subtle text-info">বোনাস</span></td>
            <td>+১০০ TK</td>
            <td class="text-muted small">সাইনআপ রিওয়ার্ড</td>
            <td class="text-muted small">১০:৫৮ এএম</td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
