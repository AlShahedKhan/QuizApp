<?php
$pageTitle = "QuizTap অ্যাডমিন - ব্যবহারকারী";
$pageTag = "ব্যবহারকারী ব্যবস্থাপনা";
$pageMeta = "শেষ সিঙ্ক: ১০ মিনিট আগে";
$activeNav = "users";
require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">সক্রিয় ব্যবহারকারী</h2>
          <p class="text-muted mb-0">
            ক্রেডিট, স্কোর, এবং রেফারেল স্ট্যাটাস রিভিউ করুন।
          </p>
        </div>
        <div class="d-flex gap-2">
          <button class="btn btn-outline-dark btn-sm" type="button">
            CSV এক্সপোর্ট
          </button>
          <button class="btn btn-primary btn-sm" type="button">
            ব্যবহারকারী যোগ
          </button>
        </div>
      </div>
    </div>

    <div class="table-card reveal delay-1">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>ব্যবহারকারী</th>
            <th>মোবাইল</th>
            <th>ক্রেডিট</th>
            <th>রেফারেল ব্যালেন্স</th>
            <th>মাসিক স্কোর</th>
            <th>স্ট্যাটাস</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="fw-semibold">Nabila Ahmed</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ০৩</div>
            </td>
            <td>০১৯০০-১২৩৪৫৬</td>
            <td>৮২০ TK</td>
            <td>১২০ TK</td>
            <td>১,২৪০</td>
            <td><span class="badge bg-success-subtle text-success">সক্রিয়</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Arif Hasan</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ০৮</div>
            </td>
            <td>০১৮০০-৭৭৪৪১১</td>
            <td>৯৫ TK</td>
            <td>০ TK</td>
            <td>২১০</td>
            <td><span class="badge bg-warning-subtle text-warning">কম ক্রেডিট</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Samiha Noor</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ১১</div>
            </td>
            <td>০১৭০০-৫৫৩২১০</td>
            <td>১,৫৪০ TK</td>
            <td>৩২০ TK</td>
            <td>১,৮৬০</td>
            <td><span class="badge bg-success-subtle text-success">সক্রিয়</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Tanvir Rahman</div>
              <div class="text-muted small">যোগ দিয়েছেন সেপ্টে ১৪</div>
            </td>
            <td>০১৬০০-৮৮৫৫৪৪</td>
            <td>৪৩০ TK</td>
            <td>৫০ TK</td>
            <td>৭৮০</td>
            <td><span class="badge bg-info-subtle text-info">বোনাস ব্যবহার</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
