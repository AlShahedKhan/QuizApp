<?php
$pageTitle = "QuizTap অ্যাডমিন - উইথড্র";
$pageTag = "উইথড্র অনুরোধ";
$pageMeta = "পেন্ডিং: ৬";
$activeNav = "withdrawals";
require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">রেফারেল পেআউট অনুরোধ</h2>
          <p class="text-muted mb-0">
            অনুমোদনের আগে পেন্ডিং উইথড্র রিভিউ করুন।
          </p>
        </div>
        <button class="btn btn-outline-dark btn-sm" type="button">
          হিস্ট্রি দেখুন
        </button>
      </div>
    </div>

    <div class="table-card reveal delay-1">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>ব্যবহারকারী</th>
            <th>মাধ্যম</th>
            <th>অ্যাকাউন্ট</th>
            <th>পরিমাণ</th>
            <th>স্ট্যাটাস</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="fw-semibold">Nabila Ahmed</div>
              <div class="text-muted small">রেফারেল ব্যালেন্স: ১২০ TK</div>
            </td>
            <td>বিকাশ</td>
            <td>০১৯০০-১২৩৪৫৬</td>
            <td>৮০ TK</td>
            <td><span class="badge bg-warning-subtle text-warning">পেন্ডিং</span></td>
            <td class="d-flex gap-2">
              <button class="btn btn-primary btn-sm" type="button">
                অনুমোদন
              </button>
              <button class="btn btn-outline-dark btn-sm" type="button">
                বাতিল
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Samiha Noor</div>
              <div class="text-muted small">রেফারেল ব্যালেন্স: ৩২০ TK</div>
            </td>
            <td>নগদ</td>
            <td>০১৭০০-৫৫৩২১০</td>
            <td>২০০ TK</td>
            <td><span class="badge bg-warning-subtle text-warning">পেন্ডিং</span></td>
            <td class="d-flex gap-2">
              <button class="btn btn-primary btn-sm" type="button">
                অনুমোদন
              </button>
              <button class="btn btn-outline-dark btn-sm" type="button">
                বাতিল
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">Arif Hasan</div>
              <div class="text-muted small">রেফারেল ব্যালেন্স: ৫০ TK</div>
            </td>
            <td>বিকাশ</td>
            <td>০১৮০০-৭৭৪৪১১</td>
            <td>৫০ TK</td>
            <td><span class="badge bg-success-subtle text-success">অনুমোদিত</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                দেখুন
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
