<?php
$pageTitle = "QuizTap - লেনদেন";
$pageTag = "লেনদেন হিস্ট্রি";
$pageMeta = "শেষ ৩০ দিন";
$activeTab = "transactions";
$tabs = [
  "play" => ["label" => "কুইজ খেলুন", "href" => "../quiz/play.php"],
  "transactions" => ["label" => "লেনদেন", "href" => "#"],
  "buy-credit" => ["label" => "ক্রেডিট কিনুন", "href" => "../user/buy-credit.php"],
  "account" => ["label" => "ড্যাশবোর্ড", "href" => "../user/dashboard.php"],
];
require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <div class="soft-card p-4 reveal">
        <div class="d-flex flex-wrap gap-2 mb-3">
          <button class="btn btn-outline-dark btn-sm">সব</button>
          <button class="btn btn-outline-dark btn-sm">বোনাস</button>
          <button class="btn btn-outline-dark btn-sm">ক্রয়</button>
          <button class="btn btn-outline-dark btn-sm">কুইজ</button>
          <button class="btn btn-outline-dark btn-sm">রেফারেল</button>
          <button class="btn btn-outline-dark btn-sm">উইথড্র</button>
        </div>
        <div class="table-card">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>তারিখ</th>
                  <th>ধরণ</th>
                  <th>বিবরণ</th>
                  <th class="text-end">পরিমাণ</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>১২ সেপ্টেম্বর, ২০২৪</td>
                  <td>ক্রয়</td>
                  <td>বিকাশ টপ-আপ • TXN-9F56X</td>
                  <td class="text-end text-success fw-semibold">+200 TK</td>
                </tr>
                <tr>
                  <td>১১ সেপ্টেম্বর, ২০২৪</td>
                  <td>কুইজ</td>
                  <td>সাধারণ জ্ঞান • প্রশ্ন ৩</td>
                  <td class="text-end text-danger fw-semibold">-1 TK</td>
                </tr>
                <tr>
                  <td>১০ সেপ্টেম্বর, ২০২৪</td>
                  <td>রেফারেল</td>
                  <td>রেফার্ড ইউজার 01900XXXXXX</td>
                  <td class="text-end text-success fw-semibold">+50 TK</td>
                </tr>
                <tr>
                  <td>৮ সেপ্টেম্বর, ২০২৪</td>
                  <td>বোনাস</td>
                  <td>সাইনআপ বোনাস</td>
                  <td class="text-end text-success fw-semibold">+100 TK</td>
                </tr>
                <tr>
                  <td>৪ সেপ্টেম্বর, ২০২৪</td>
                  <td>উইথড্র</td>
                  <td>বিকাশ উইথড্র রিকোয়েস্ট</td>
                  <td class="text-end text-danger fw-semibold">-150 TK</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
