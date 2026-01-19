<?php
require_once __DIR__ . "/../config/bootstrap.php";
require_admin();

$pageTitle = "QuizTap অ্যাডমিন - প্রশ্ন";
$pageTag = "প্রশ্ন ব্যাংক";
$monthYear = trim((string)($_GET["month"] ?? current_month_year()));
if ($monthYear === "" || !preg_match("/^\\d{4}-\\d{2}$/", $monthYear)) {
  $monthYear = current_month_year();
}

$pageMeta = "মাস: " . $monthYear;
$activeNav = "questions";

$errorMessage = "";
$successMessage = flash("question_success");

function load_question(PDO $pdo, int $id): ?array
{
  $stmt = $pdo->prepare("SELECT * FROM quiz_questions WHERE id = ?");
  $stmt->execute([$id]);
  $row = $stmt->fetch();
  return $row ?: null;
}

$pdo = db();

if (is_post()) {
  require_csrf();
  $action = $_POST["action"] ?? "";

  if ($action === "create_set") {
    $title = trim($_POST["title"] ?? "");
    $timeLimit = (int)($_POST["time_limit_seconds"] ?? 30);
    $questionLimit = (int)($_POST["questions_per_quiz"] ?? 10);
    if ($timeLimit < 10) {
      $timeLimit = 10;
    }
    if ($questionLimit < 1) {
      $questionLimit = 1;
    }
    if ($title === "") {
      $title = $monthYear . " প্রশ্ন সেট";
    }
    $stmt = $pdo->prepare("SELECT id FROM quiz_question_sets WHERE month_year = ?");
    $stmt->execute([$monthYear]);
    $existing = $stmt->fetchColumn();
    if ($existing) {
      $errorMessage = "এই মাসের জন্য সেট ইতিমধ্যে আছে।";
    } else {
      $stmt = $pdo->prepare(
        "INSERT INTO quiz_question_sets (month_year, title, time_limit_seconds, questions_per_quiz, is_active, created_at)
         VALUES (?, ?, ?, ?, 1, NOW())"
      );
      $stmt->execute([$monthYear, $title, $timeLimit, $questionLimit]);
      flash("question_success", "মাসিক প্রশ্ন সেট তৈরি হয়েছে।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }
  }

  if ($action === "update_set") {
    $setId = (int)($_POST["set_id"] ?? 0);
    $title = trim($_POST["title"] ?? "");
    $timeLimit = (int)($_POST["time_limit_seconds"] ?? 30);
    $questionLimit = (int)($_POST["questions_per_quiz"] ?? 10);
    if ($timeLimit < 10) {
      $timeLimit = 10;
    }
    if ($questionLimit < 1) {
      $questionLimit = 1;
    }
    if (!$setId) {
      $errorMessage = "সেট খুঁজে পাওয়া যায়নি।";
    } else {
      if ($title === "") {
        $title = $monthYear . " প্রশ্ন সেট";
      }
      $stmt = $pdo->prepare(
        "UPDATE quiz_question_sets SET title = ?, time_limit_seconds = ?, questions_per_quiz = ? WHERE id = ?"
      );
      $stmt->execute([$title, $timeLimit, $questionLimit, $setId]);
      flash("question_success", "সেট আপডেট হয়েছে।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }
  }

  if ($action === "create_question") {
    $question = trim($_POST["question_bn"] ?? "");
    $optionA = trim($_POST["option_a_bn"] ?? "");
    $optionB = trim($_POST["option_b_bn"] ?? "");
    $optionC = trim($_POST["option_c_bn"] ?? "");
    $optionD = trim($_POST["option_d_bn"] ?? "");
    $correct = trim($_POST["correct_option"] ?? "");
    $isActive = isset($_POST["is_active"]) ? 1 : 0;
    $addToSet = isset($_POST["add_to_set"]);
    if ($addToSet === false) {
      $stmt = $pdo->prepare("SELECT id FROM quiz_question_sets WHERE month_year = ?");
      $stmt->execute([$monthYear]);
      $addToSet = !(bool)$stmt->fetchColumn();
    }

    if ($question === "" || $optionA === "" || $optionB === "" || $optionC === "" || $optionD === "") {
      $errorMessage = "সব প্রশ্ন ও অপশন পূরণ করুন।";
    } elseif (!in_array($correct, ["A", "B", "C", "D"], true)) {
      $errorMessage = "সঠিক উত্তর নির্বাচন করুন।";
    } else {
      $stmt = $pdo->prepare(
        "INSERT INTO quiz_questions
         (question_bn, option_a_bn, option_b_bn, option_c_bn, option_d_bn, correct_option, is_active, created_at)
         VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
      );
      $stmt->execute([$question, $optionA, $optionB, $optionC, $optionD, $correct, $isActive]);
      $questionId = (int)$pdo->lastInsertId();
      if ($addToSet) {
        $stmt = $pdo->prepare("SELECT id FROM quiz_question_sets WHERE month_year = ?");
        $stmt->execute([$monthYear]);
        $setId = (int)$stmt->fetchColumn();
        if (!$setId) {
          $defaultTitle = $monthYear . " প্রশ্ন সেট";
          $stmt = $pdo->prepare(
            "INSERT INTO quiz_question_sets (month_year, title, time_limit_seconds, questions_per_quiz, is_active, created_at)
             VALUES (?, ?, 30, 10, 1, NOW())"
          );
          $stmt->execute([$monthYear, $defaultTitle]);
          $setId = (int)$pdo->lastInsertId();
        }
        if ($setId) {
          $stmt = $pdo->prepare(
            "SELECT COALESCE(MAX(position), 0) + 1 FROM quiz_question_set_items WHERE set_id = ?"
          );
          $stmt->execute([$setId]);
          $position = (int)$stmt->fetchColumn();
          $stmt = $pdo->prepare(
            "INSERT IGNORE INTO quiz_question_set_items (set_id, question_id, position, created_at)
             VALUES (?, ?, ?, NOW())"
          );
          $stmt->execute([$setId, $questionId, $position]);
        }
      }
      flash("question_success", "প্রশ্ন সংরক্ষণ হয়েছে।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }
  }

  if ($action === "update_question") {
    $questionId = (int)($_POST["question_id"] ?? 0);
    $question = trim($_POST["question_bn"] ?? "");
    $optionA = trim($_POST["option_a_bn"] ?? "");
    $optionB = trim($_POST["option_b_bn"] ?? "");
    $optionC = trim($_POST["option_c_bn"] ?? "");
    $optionD = trim($_POST["option_d_bn"] ?? "");
    $correct = trim($_POST["correct_option"] ?? "");
    $isActive = isset($_POST["is_active"]) ? 1 : 0;

    if (!$questionId) {
      $errorMessage = "প্রশ্ন খুঁজে পাওয়া যায়নি।";
    } elseif ($question === "" || $optionA === "" || $optionB === "" || $optionC === "" || $optionD === "") {
      $errorMessage = "সব প্রশ্ন ও অপশন পূরণ করুন।";
    } elseif (!in_array($correct, ["A", "B", "C", "D"], true)) {
      $errorMessage = "সঠিক উত্তর নির্বাচন করুন।";
    } else {
      $stmt = $pdo->prepare(
        "UPDATE quiz_questions
         SET question_bn = ?, option_a_bn = ?, option_b_bn = ?, option_c_bn = ?, option_d_bn = ?,
             correct_option = ?, is_active = ?
         WHERE id = ?"
      );
      $stmt->execute([
        $question,
        $optionA,
        $optionB,
        $optionC,
        $optionD,
        $correct,
        $isActive,
        $questionId,
      ]);
      flash("question_success", "প্রশ্ন আপডেট হয়েছে।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }
  }

  if ($action === "toggle_question") {
    $questionId = (int)($_POST["question_id"] ?? 0);
    if ($questionId) {
      $stmt = $pdo->prepare("UPDATE quiz_questions SET is_active = 1 - is_active WHERE id = ?");
      $stmt->execute([$questionId]);
      flash("question_success", "স্ট্যাটাস আপডেট হয়েছে।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }
  }

  if ($action === "add_to_set") {
    $questionId = (int)($_POST["question_id"] ?? 0);
    $setId = (int)($_POST["set_id"] ?? 0);
    if (!$setId || !$questionId) {
      $errorMessage = "সেট বা প্রশ্ন পাওয়া যায়নি।";
    } else {
      $stmt = $pdo->prepare(
        "SELECT COALESCE(MAX(position), 0) + 1 FROM quiz_question_set_items WHERE set_id = ?"
      );
      $stmt->execute([$setId]);
      $position = (int)$stmt->fetchColumn();
      $stmt = $pdo->prepare(
        "INSERT IGNORE INTO quiz_question_set_items (set_id, question_id, position, created_at)
         VALUES (?, ?, ?, NOW())"
      );
      $stmt->execute([$setId, $questionId, $position]);
      flash("question_success", "প্রশ্ন সেটে যোগ হয়েছে।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }
  }

  if ($action === "remove_from_set") {
    $itemId = (int)($_POST["item_id"] ?? 0);
    if ($itemId) {
      $stmt = $pdo->prepare("DELETE FROM quiz_question_set_items WHERE id = ?");
      $stmt->execute([$itemId]);
      flash("question_success", "প্রশ্ন সেট থেকে সরানো হয়েছে।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }
  }
}

$stmt = $pdo->prepare("SELECT * FROM quiz_question_sets ORDER BY month_year DESC");
$stmt->execute();
$sets = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM quiz_question_sets WHERE month_year = ?");
$stmt->execute([$monthYear]);
$currentSet = $stmt->fetch();

$editId = (int)($_GET["edit"] ?? 0);
$editQuestion = $editId ? load_question($pdo, $editId) : null;

$monthQuestions = [];
if ($currentSet) {
  $stmt = $pdo->prepare(
    "SELECT s.id AS item_id, s.position, q.*
     FROM quiz_question_set_items s
     INNER JOIN quiz_questions q ON q.id = s.question_id
     WHERE s.set_id = ?
     ORDER BY s.position ASC, q.id ASC"
  );
  $stmt->execute([(int)$currentSet["id"]]);
  $monthQuestions = $stmt->fetchAll();
}

require __DIR__ . "/../views/partials/admin-head.php";
require __DIR__ . "/../views/partials/admin-header.php";
?>

<div class="admin-shell">
  <?php require __DIR__ . "/../views/partials/admin-nav.php"; ?>

  <section>
    <div class="soft-card p-4 mb-4 reveal">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h2 class="mb-1">মাসিক প্রশ্ন সেট</h2>
          <p class="text-muted mb-0">প্রতি মাসে আলাদা প্রশ্ন তালিকা পরিচালনা করুন।</p>
        </div>
        <form class="d-flex flex-wrap gap-2" method="get">
          <input type="month" class="form-control" name="month" value="<?php echo e($monthYear); ?>" />
          <button class="btn btn-outline-dark btn-sm" type="submit">লোড</button>
        </form>
      </div>
      <?php if ($successMessage) { ?>
        <div class="text-success small mt-3"><?php echo e($successMessage); ?></div>
      <?php } ?>
      <?php if ($errorMessage) { ?>
        <div class="text-danger small mt-3"><?php echo e($errorMessage); ?></div>
      <?php } ?>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-lg-5 reveal">
        <div class="soft-card p-4 h-100">
          <h3 class="mb-3">বর্তমান সেট</h3>
          <?php if ($currentSet) { ?>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">সেট টাইটেল</span>
              <span class="fw-semibold"><?php echo e($currentSet["title"]); ?></span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">প্রশ্ন সংখ্যা</span>
              <span class="fw-semibold"><?php echo e((int)$currentSet["questions_per_quiz"]); ?> টি</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">সময় সীমা</span>
              <span class="fw-semibold"><?php echo e((int)$currentSet["time_limit_seconds"]); ?> সেকেন্ড</span>
            </div>
            <div class="text-muted small">মাস: <?php echo e($monthYear); ?></div>
            <form class="mt-3" method="post">
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="action" value="update_set" />
              <input type="hidden" name="set_id" value="<?php echo e((int)$currentSet["id"]); ?>" />
              <div class="mb-3">
                <label class="form-label" for="title">সেটের নাম</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo e($currentSet["title"]); ?>" />
              </div>
              <div class="mb-3">
                <label class="form-label" for="time_limit_seconds">সময় সীমা (সেকেন্ড)</label>
                <input
                  type="number"
                  class="form-control"
                  id="time_limit_seconds"
                  name="time_limit_seconds"
                  min="10"
                  value="<?php echo e((int)$currentSet["time_limit_seconds"]); ?>"
                />
              </div>
              <div class="mb-3">
                <label class="form-label" for="questions_per_quiz">প্রতি কুইজে প্রশ্ন সংখ্যা</label>
                <input
                  type="number"
                  class="form-control"
                  id="questions_per_quiz"
                  name="questions_per_quiz"
                  min="1"
                  value="<?php echo e((int)$currentSet["questions_per_quiz"]); ?>"
                />
              </div>
              <button class="btn btn-primary btn-sm" type="submit">আপডেট করুন</button>
            </form>
          <?php } else { ?>
            <div class="text-muted mb-3">এই মাসের জন্য কোনো সেট নেই।</div>
            <form method="post">
              <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
              <input type="hidden" name="action" value="create_set" />
              <div class="mb-3">
                <label class="form-label" for="title">সেটের নাম</label>
                <input type="text" class="form-control" id="title" name="title" placeholder="<?php echo e($monthYear); ?> প্রশ্ন সেট" />
              </div>
              <div class="mb-3">
                <label class="form-label" for="time_limit_seconds">সময় সীমা (সেকেন্ড)</label>
                <input type="number" class="form-control" id="time_limit_seconds" name="time_limit_seconds" min="10" value="30" />
              </div>
              <div class="mb-3">
                <label class="form-label" for="questions_per_quiz">প্রতি কুইজে প্রশ্ন সংখ্যা</label>
                <input type="number" class="form-control" id="questions_per_quiz" name="questions_per_quiz" min="1" value="10" />
              </div>
              <button class="btn btn-primary btn-sm" type="submit">মাসিক সেট তৈরি করুন</button>
            </form>
          <?php } ?>
        </div>
      </div>
      <div class="col-lg-7 reveal delay-1">
        <div class="soft-card p-4 h-100">
          <h3 class="mb-3"><?php echo $editQuestion ? "প্রশ্ন আপডেট করুন" : "নতুন প্রশ্ন যোগ করুন"; ?></h3>
          <form method="post">
            <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
            <input type="hidden" name="action" value="<?php echo $editQuestion ? "update_question" : "create_question"; ?>" />
            <?php if ($editQuestion) { ?>
              <input type="hidden" name="question_id" value="<?php echo e((int)$editQuestion["id"]); ?>" />
            <?php } ?>
            <div class="mb-3">
              <label class="form-label" for="question_bn">প্রশ্ন (বাংলা)</label>
              <textarea class="form-control" id="question_bn" name="question_bn" rows="2"><?php echo e($editQuestion["question_bn"] ?? ""); ?></textarea>
            </div>
            <div class="row g-2">
              <div class="col-md-6">
                <label class="form-label" for="option_a_bn">অপশন A</label>
                <input class="form-control" id="option_a_bn" name="option_a_bn" value="<?php echo e($editQuestion["option_a_bn"] ?? ""); ?>" />
              </div>
              <div class="col-md-6">
                <label class="form-label" for="option_b_bn">অপশন B</label>
                <input class="form-control" id="option_b_bn" name="option_b_bn" value="<?php echo e($editQuestion["option_b_bn"] ?? ""); ?>" />
              </div>
              <div class="col-md-6">
                <label class="form-label" for="option_c_bn">অপশন C</label>
                <input class="form-control" id="option_c_bn" name="option_c_bn" value="<?php echo e($editQuestion["option_c_bn"] ?? ""); ?>" />
              </div>
              <div class="col-md-6">
                <label class="form-label" for="option_d_bn">অপশন D</label>
                <input class="form-control" id="option_d_bn" name="option_d_bn" value="<?php echo e($editQuestion["option_d_bn"] ?? ""); ?>" />
              </div>
            </div>
            <div class="row g-2 mt-2 align-items-end">
              <div class="col-md-6">
                <label class="form-label" for="correct_option">সঠিক উত্তর</label>
                <select class="form-select" id="correct_option" name="correct_option">
                  <?php foreach (["A", "B", "C", "D"] as $opt) { ?>
                    <option value="<?php echo e($opt); ?>" <?php echo ($editQuestion && $editQuestion["correct_option"] === $opt) ? "selected" : ""; ?>>
                      <?php echo e($opt); ?>
                    </option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-md-6">
                <div class="form-check mt-4">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo ($editQuestion ? (int)$editQuestion["is_active"] === 1 : true) ? "checked" : ""; ?> />
                  <label class="form-check-label" for="is_active">সক্রিয় রাখুন</label>
                </div>
              </div>
            </div>
            <?php if (!$editQuestion) { ?>
              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" id="add_to_set" name="add_to_set" checked />
                <label class="form-check-label" for="add_to_set">
                  এই মাসের সেটে যুক্ত করুন
                  <?php if (!$currentSet) { ?>
                    <span class="text-muted small"> (না থাকলে সেট অটো-তৈরি হবে)</span>
                  <?php } ?>
                </label>
              </div>
            <?php } ?>
            <button class="btn btn-primary mt-3" type="submit">
              <?php echo $editQuestion ? "আপডেট করুন" : "সংরক্ষণ করুন"; ?>
            </button>
            <?php if ($editQuestion) { ?>
              <a class="btn btn-outline-dark mt-3" href="/admin/questions.php?month=<?php echo e($monthYear); ?>">নতুন প্রশ্ন</a>
            <?php } ?>
          </form>
        </div>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-12 reveal">
        <div class="table-card">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>এই মাসের প্রশ্ন</th>
                <th>স্ট্যাটাস</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$currentSet) { ?>
                <tr>
                  <td colspan="3" class="text-muted">সেট তৈরি করলে প্রশ্ন যোগ করতে পারবেন।</td>
                </tr>
              <?php } elseif (!$monthQuestions) { ?>
                <tr>
                  <td colspan="3" class="text-muted">এই মাসের সেটে এখনো প্রশ্ন নেই।</td>
                </tr>
              <?php } else { ?>
                <?php foreach ($monthQuestions as $item) { ?>
                  <tr>
                    <td>
                      <div class="fw-semibold"><?php echo e($item["question_bn"]); ?></div>
                      <div class="text-muted small">#<?php echo e((int)$item["id"]); ?></div>
                    </td>
                    <td>
                      <?php if ((int)$item["is_active"] === 1) { ?>
                        <span class="badge bg-success-subtle text-success">সক্রিয়</span>
                      <?php } else { ?>
                        <span class="badge bg-secondary-subtle text-secondary">নিষ্ক্রিয়</span>
                      <?php } ?>
                    </td>
                    <td class="d-flex gap-2">
                      <a class="btn btn-outline-dark btn-sm" href="/admin/questions.php?month=<?php echo e($monthYear); ?>&edit=<?php echo e((int)$item["id"]); ?>">এডিট</a>
                      <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
                        <input type="hidden" name="action" value="remove_from_set" />
                        <input type="hidden" name="item_id" value="<?php echo e((int)$item["item_id"]); ?>" />
                        <button class="btn btn-outline-dark btn-sm" type="submit">সরান</button>
                      </form>
                    </td>
                  </tr>
                <?php } ?>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
