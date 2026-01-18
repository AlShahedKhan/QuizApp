<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_login();

$user = current_user();
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

$filter = $_GET["type"] ?? "all";
$allowed = ["all", "bonus", "purchase", "quiz", "referral", "withdraw"];
if (!in_array($filter, $allowed, true)) {
  $filter = "all";
}

$typeMap = [
  "bonus" => "bonus",
  "purchase" => "purchase",
  "quiz" => "quiz_deduct",
  "referral" => "referral_credit",
  "withdraw" => "withdraw",
];

$sql = "SELECT type, amount, meta_json, created_at, status
        FROM transactions
        WHERE user_id = ? AND status IN ('completed','approved')";
$params = [(int)$user["id"]];
if ($filter !== "all") {
  $sql .= " AND type = ?";
  $params[] = $typeMap[$filter];
}
$sql .= " ORDER BY created_at DESC LIMIT 200";
$stmt = db()->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

require __DIR__ . "/../views/partials/app-head.php";
require __DIR__ . "/../views/partials/app-header.php";
require __DIR__ . "/../views/partials/app-tabs.php";
?>

      <div class="soft-card p-4 reveal">
        <div class="d-flex flex-wrap gap-2 mb-3">
          <a class="btn btn-outline-dark btn-sm" href="?type=all">সব</a>
          <a class="btn btn-outline-dark btn-sm" href="?type=bonus">বোনাস</a>
          <a class="btn btn-outline-dark btn-sm" href="?type=purchase">ক্রয়</a>
          <a class="btn btn-outline-dark btn-sm" href="?type=quiz">কুইজ</a>
          <a class="btn btn-outline-dark btn-sm" href="?type=referral">রেফারেল</a>
          <a class="btn btn-outline-dark btn-sm" href="?type=withdraw">উইথড্র</a>
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
                <?php if (!$transactions) { ?>
                  <tr>
                    <td colspan="4" class="text-muted">কোনো লেনদেন নেই।</td>
                  </tr>
                <?php } ?>
                <?php foreach ($transactions as $txn) {
                  $meta = json_decode($txn["meta_json"] ?? "{}", true) ?: [];
                  $isDebit = $txn["type"] === "quiz_deduct" || $txn["type"] === "withdraw";
                  $amountLabel = ($isDebit ? "-" : "+") . (int)$txn["amount"] . " TK";
                  $amountClass = $isDebit ? "text-danger" : "text-success";
                  $typeLabel = [
                    "bonus" => "বোনাস",
                    "purchase" => "ক্রয়",
                    "quiz_deduct" => "কুইজ",
                    "referral_credit" => "রেফারেল",
                    "withdraw" => "উইথড্র",
                  ][$txn["type"]] ?? "লেনদেন";
                  $desc = $meta["description"] ?? "";
                  if ($desc === "" && $txn["type"] === "purchase") {
                    $desc = ($meta["method"] ?? "পেমেন্ট") . " • " . ($meta["trx_id"] ?? "");
                  }
                  if ($desc === "" && $txn["type"] === "quiz_deduct") {
                    $desc = "প্রশ্ন #" . ($meta["question_id"] ?? "");
                  }
                ?>
                  <tr>
                    <td><?php echo e(format_date($txn["created_at"])); ?></td>
                    <td><?php echo e($typeLabel); ?></td>
                  <td><?php echo e($desc !== "" ? $desc : "-"); ?></td>
                    <td class="text-end <?php echo $amountClass; ?> fw-semibold"><?php echo e($amountLabel); ?></td>
                  </tr>
                <?php } ?>
                <?php if (false) { ?>
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
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
<?php require __DIR__ . "/../views/partials/app-foot.php"; ?>
