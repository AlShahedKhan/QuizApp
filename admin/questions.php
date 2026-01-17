<?php
$pageTitle = "QuizTap অ্যাডমিন - প্রশ্ন";
$pageTag = "প্রশ্ন ব্যাংক";
$pageMeta = "সক্রিয়: ১২৪";
$activeNav = "questions";
require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">কুইজ প্রশ্ন পরিচালনা করুন</h2>
          <p class="text-muted mb-0">
            প্রতি মাসে সহজ, মাঝারি, এবং কঠিন প্রশ্নের ভালো মিশ্রণ রাখুন।
          </p>
        </div>
        <button class="btn btn-primary btn-sm" type="button">
          নতুন প্রশ্ন যোগ
        </button>
      </div>
    </div>

    <div class="table-card reveal delay-1">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>প্রশ্ন</th>
            <th>সঠিক উত্তর</th>
            <th>কঠিনতা</th>
            <th>স্ট্যাটাস</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <div class="fw-semibold">
                বাংলাদেশের দীর্ঘতম নদী কোনটি?
              </div>
              <div class="text-muted small">ক্যাটাগরি: ভূগোল</div>
            </td>
            <td>মেঘনা</td>
            <td>মধ্যম</td>
            <td><span class="badge bg-success-subtle text-success">সক্রিয়</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">
                "আমার সোনার বাংলা" কে লিখেছেন?
              </div>
              <div class="text-muted small">ক্যাটাগরি: সাহিত্য</div>
            </td>
            <td>রবীন্দ্রনাথ ঠাকুর</td>
            <td>সহজ</td>
            <td><span class="badge bg-success-subtle text-success">সক্রিয়</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">
                বাংলাদেশ স্বাধীনতা লাভ করে কোন সালে?
              </div>
              <div class="text-muted small">ক্যাটাগরি: ইতিহাস</div>
            </td>
            <td>১৯৭১</td>
            <td>সহজ</td>
            <td><span class="badge bg-warning-subtle text-warning">খসড়া</span></td>
            <td>
              <button class="btn btn-outline-dark btn-sm" type="button">
                এডিট
              </button>
            </td>
          </tr>
          <tr>
            <td>
              <div class="fw-semibold">
                সবচেয়ে বেশি জনসংখ্যা কোন বিভাগে?
              </div>
              <div class="text-muted small">ক্যাটাগরি: পরিসংখ্যান</div>
            </td>
            <td>ঢাকা</td>
            <td>কঠিন</td>
            <td><span class="badge bg-secondary-subtle text-secondary">আর্কাইভড</span></td>
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
