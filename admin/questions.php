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
$importMessage = flash("import_success");
$importError = flash("import_error");

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
      if ($addToSet) {
        $stmt = $pdo->prepare("SELECT id FROM quiz_question_sets WHERE month_year = ?");
        $stmt->execute([$monthYear]);
        $setId = (int)$stmt->fetchColumn();
        if ($setId) {
          $checkStmt = $pdo->prepare(
            "SELECT q.id
             FROM quiz_questions q
             INNER JOIN quiz_question_set_items s ON s.question_id = q.id
             WHERE s.set_id = ? AND q.question_bn = ? AND q.option_a_bn = ? AND q.option_b_bn = ? AND q.option_c_bn = ? AND q.option_d_bn = ?
             LIMIT 1"
          );
          $checkStmt->execute([$setId, $question, $optionA, $optionB, $optionC, $optionD]);
          if ($checkStmt->fetch()) {
            $errorMessage = "Same question already exists for this month.";
          }
        }
      }
      if ($errorMessage !== "") {
        // duplicate found
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

  if ($action === "import_questions") {
    if (!isset($_FILES["import_file"]) || !is_uploaded_file($_FILES["import_file"]["tmp_name"])) {
      flash("import_error", "ফাইল আপলোড করুন।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }

    $fileName = (string)($_FILES["import_file"]["name"] ?? "");
    $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    if (!in_array($extension, ["csv", "txt", "xlsx"], true)) {
      flash("import_error", "শুধু CSV অথবা XLSX ফাইল আপলোড করুন।");
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }

    $addToSet = isset($_POST["import_add_to_set"]);
    if ($addToSet === false) {
      $stmt = $pdo->prepare("SELECT id FROM quiz_question_sets WHERE month_year = ?");
      $stmt->execute([$monthYear]);
      $addToSet = !(bool)$stmt->fetchColumn();
    }

    $filePath = $_FILES["import_file"]["tmp_name"];
    $header = [];
    $rows = [];
    $delimiters = [",", "\t", ";"];
    $delimiterUsed = ",";
    $best = 0;

    if ($extension === "xlsx") {
      if (!class_exists("\\PhpOffice\\PhpSpreadsheet\\IOFactory")) {
        flash("import_error", "XLSX ইম্পোর্ট চালু করতে PhpSpreadsheet প্রয়োজন।");
        redirect("/admin/questions.php?month=" . urlencode($monthYear));
      }
      $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($filePath);
      $sheet = $spreadsheet->getActiveSheet();
      $sheetRows = $sheet->toArray(null, true, true, false);
      if (!$sheetRows || count($sheetRows) === 0) {
        flash("import_error", "ফাইল ফাঁকা।");
        redirect("/admin/questions.php?month=" . urlencode($monthYear));
      }
      $header = array_shift($sheetRows);
      $rows = $sheetRows;
    } else {
      $handle = fopen($filePath, "r");
      if ($handle === false) {
        flash("import_error", "ফাইল পড়া যাচ্ছে না।");
        redirect("/admin/questions.php?month=" . urlencode($monthYear));
      }
      $firstLine = fgets($handle);
      if ($firstLine === false) {
        fclose($handle);
        flash("import_error", "ফাইল ফাঁকা।");
        redirect("/admin/questions.php?month=" . urlencode($monthYear));
      }
      foreach ($delimiters as $delim) {
        $parsed = str_getcsv(trim($firstLine), $delim);
        if (count($parsed) > $best) {
          $best = count($parsed);
          $header = $parsed;
          $delimiterUsed = $delim;
        }
      }
      while (($row = fgetcsv($handle, 0, $delimiterUsed)) !== false) {
        $rows[] = $row;
      }
      fclose($handle);
    }

    $header = array_map(function ($item) {
      $text = trim((string)$item);
      $text = preg_replace("/^\\xEF\\xBB\\xBF/", "", $text);
      $text = strtolower($text);
      $text = preg_replace("/\\s+/", "_", $text);
      return $text;
    }, $header);
    $aliases = [
      "question" => "question_bn",
      "question_text" => "question_bn",
      "option_a" => "option_a_bn",
      "option_b" => "option_b_bn",
      "option_c" => "option_c_bn",
      "option_d" => "option_d_bn",
      "correct" => "correct_option",
    ];
    $normalizedHeader = [];
    foreach ($header as $col) {
      $normalizedHeader[] = $aliases[$col] ?? $col;
    }
    $header = $normalizedHeader;
    $indexMap = array_flip($header);

    $required = ["question_bn", "option_a_bn", "option_b_bn", "option_c_bn", "option_d_bn", "correct_option"];
    $missing = array_filter($required, fn($col) => !array_key_exists($col, $indexMap));
    if ($missing) {
      flash("import_error", "ফাইল ফরম্যাট ঠিক নয়। হেডার দরকার: " . implode(", ", $required));
      redirect("/admin/questions.php?month=" . urlencode($monthYear));
    }

    $pdo->beginTransaction();
    $inserted = 0;
    $skipped = 0;
    $duplicates = 0;
    $setId = 0;
    $position = 0;

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
          "SELECT COALESCE(MAX(position), 0) FROM quiz_question_set_items WHERE set_id = ?"
        );
        $stmt->execute([$setId]);
        $position = (int)$stmt->fetchColumn();
      }
    }

    $insertStmt = $pdo->prepare(
      "INSERT INTO quiz_questions
       (question_bn, option_a_bn, option_b_bn, option_c_bn, option_d_bn, correct_option, is_active, created_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())"
    );
    $setStmt = $pdo->prepare(
      "INSERT IGNORE INTO quiz_question_set_items (set_id, question_id, position, created_at)
       VALUES (?, ?, ?, NOW())"
    );
    $checkStmt = $pdo->prepare(
      "SELECT q.id
       FROM quiz_questions q
       INNER JOIN quiz_question_set_items s ON s.question_id = q.id
       WHERE s.set_id = ? AND q.question_bn = ? AND q.option_a_bn = ? AND q.option_b_bn = ? AND q.option_c_bn = ? AND q.option_d_bn = ?
       LIMIT 1"
    );

    foreach ($rows as $row) {
      if (!is_array($row)) {
        $skipped++;
        continue;
      }
      if (count($row) < count($header)) {
        $row = array_pad($row, count($header), "");
      }
      $question = trim((string)($row[$indexMap["question_bn"]] ?? ""));
      $optionA = trim((string)($row[$indexMap["option_a_bn"]] ?? ""));
      $optionB = trim((string)($row[$indexMap["option_b_bn"]] ?? ""));
      $optionC = trim((string)($row[$indexMap["option_c_bn"]] ?? ""));
      $optionD = trim((string)($row[$indexMap["option_d_bn"]] ?? ""));
      $correct = strtoupper(trim((string)($row[$indexMap["correct_option"]] ?? "")));
      $isActiveVal = 1;
      if (array_key_exists("is_active", $indexMap)) {
        $rawActive = trim((string)($row[$indexMap["is_active"]] ?? ""));
        $isActiveVal = ($rawActive === "0" || strtolower($rawActive) === "false") ? 0 : 1;
      }

      if ($question === "" && $optionA === "" && $optionB === "" && $optionC === "" && $optionD === "") {
        $skipped++;
        continue;
      }
      if ($question === "" || $optionA === "" || $optionB === "" || $optionC === "" || $optionD === "") {
        $skipped++;
        continue;
      }
      if (!in_array($correct, ["A", "B", "C", "D"], true)) {
        $skipped++;
        continue;
      }

      if ($addToSet && $setId) {
        $checkStmt->execute([$setId, $question, $optionA, $optionB, $optionC, $optionD]);
        if ($checkStmt->fetch()) {
          $duplicates++;
          continue;
        }
      }

      $insertStmt->execute([
        $question,
        $optionA,
        $optionB,
        $optionC,
        $optionD,
        $correct,
        $isActiveVal,
      ]);
      $questionId = (int)$pdo->lastInsertId();
      if ($addToSet && $setId && $questionId) {
        $position += 1;
        $setStmt->execute([$setId, $questionId, $position]);
      }
      $inserted++;
    }
    $pdo->commit();
    $message = "Import completed. Added: {$inserted}, skipped: {$skipped}";
    if ($duplicates > 0) {
      $message .= ", duplicates: {$duplicates}";
    }
    flash("import_success", $message);
    redirect("/admin/questions.php?month=" . urlencode($monthYear));
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
$questionsPage = max(1, (int)($_GET["page"] ?? 1));
$questionsLimit = 10;
$questionsOffset = ($questionsPage - 1) * $questionsLimit;
$questionsTotal = 0;
$questionsTotalPages = 1;
if ($currentSet) {
  $countStmt = $pdo->prepare(
    "SELECT COUNT(*)
     FROM quiz_question_set_items s
     INNER JOIN quiz_questions q ON q.id = s.question_id
     WHERE s.set_id = ?"
  );
  $countStmt->execute([(int)$currentSet["id"]]);
  $questionsTotal = (int)$countStmt->fetchColumn();
  $questionsTotalPages = max(1, (int)ceil($questionsTotal / $questionsLimit));
  if ($questionsPage > $questionsTotalPages) {
    $questionsPage = $questionsTotalPages;
    $questionsOffset = ($questionsPage - 1) * $questionsLimit;
  }
  $stmt = $pdo->prepare(
    "SELECT s.id AS item_id, s.position, q.*
     FROM quiz_question_set_items s
     INNER JOIN quiz_questions q ON q.id = s.question_id
     WHERE s.set_id = ?
     ORDER BY s.position ASC, q.id ASC
     LIMIT ? OFFSET ?"
  );
  $stmt->execute([(int)$currentSet["id"], $questionsLimit, $questionsOffset]);
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

    <div class="soft-card p-4 mb-4 reveal delay-2">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h3 class="mb-1">এক্সেল/CSV থেকে ইম্পোর্ট</h3>
          <p class="text-muted mb-0">
            হেডার ফরম্যাট: question_bn, option_a_bn, option_b_bn, option_c_bn, option_d_bn, correct_option, is_active
          </p>
        </div>
      </div>
      <?php if ($importMessage) { ?>
        <div class="text-success small mt-3"><?php echo e($importMessage); ?></div>
      <?php } ?>
      <?php if ($importError) { ?>
        <div class="text-danger small mt-3"><?php echo e($importError); ?></div>
      <?php } ?>
      <form class="mt-3" method="post" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
        <input type="hidden" name="action" value="import_questions" />
        <div class="row g-3 align-items-center">
          <div class="col-lg-7">
            <label class="form-label" for="import_file">CSV ফাইল</label>
            <input
              class="form-control"
              type="file"
              id="import_file"
              name="import_file"
              accept=".csv,.txt,.xlsx"
              required
            />
            <div class="form-text text-muted">
              এক্সেল (XLSX) বা CSV ফাইল আপলোড করুন।
            </div>
          </div>
          <div class="col-lg-3">
            <div class="form-check d-flex align-items-center gap-2 h-100">
              <input class="form-check-input mt-0" type="checkbox" id="import_add_to_set" name="import_add_to_set" checked />
              <label class="form-check-label mb-0" for="import_add_to_set">এই মাসের সেটে যুক্ত করুন</label>
            </div>
          </div>
          <div class="col-lg-2">
            <button class="btn btn-primary w-100" type="submit">ইম্পোর্ট করুন</button>
          </div>
        </div>
      </form>
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
                      <button
                        class="btn btn-outline-dark btn-sm"
                        type="button"
                        data-bs-toggle="modal"
                        data-bs-target="#questionModal<?php echo e((int)$item["id"]); ?>"
                        title="View"
                        aria-label="View"
                      >
                        <i class="bi bi-eye"></i>
                      </button>
                      <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
                        <input type="hidden" name="action" value="toggle_question" />
                        <input type="hidden" name="question_id" value="<?php echo e((int)$item["id"]); ?>" />
                        <button
                          class="btn btn-outline-dark btn-sm"
                          type="submit"
                          title="<?php echo (int)$item["is_active"] === 1 ? "Deactivate" : "Activate"; ?>"
                          aria-label="<?php echo (int)$item["is_active"] === 1 ? "Deactivate" : "Activate"; ?>"
                        >
                          <?php if ((int)$item["is_active"] === 1) { ?>
                            <i class="bi bi-toggle-on"></i>
                          <?php } else { ?>
                            <i class="bi bi-toggle-off"></i>
                          <?php } ?>
                        </button>
                      </form>
                      <a
                        class="btn btn-outline-dark btn-sm"
                        href="/admin/questions.php?month=<?php echo e($monthYear); ?>&edit=<?php echo e((int)$item["id"]); ?>"
                        title="Edit"
                        aria-label="Edit"
                      >
                        <i class="bi bi-pencil-square"></i>
                      </a>
                      <form method="post">
                        <input type="hidden" name="csrf_token" value="<?php echo e(csrf_token()); ?>" />
                        <input type="hidden" name="action" value="remove_from_set" />
                        <input type="hidden" name="item_id" value="<?php echo e((int)$item["item_id"]); ?>" />
                        <button class="btn btn-outline-dark btn-sm" type="submit" title="Remove" aria-label="Remove">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </td>
                  </tr>
                  <div
                    class="modal fade"
                    id="questionModal<?php echo e((int)$item["id"]); ?>"
                    tabindex="-1"
                    aria-hidden="true"
                  >
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                      <div class="modal-content">
                        <div class="modal-header">
                          <h5 class="modal-title">প্রশ্ন বিস্তারিত</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="fw-semibold mb-2"><?php echo e($item["question_bn"]); ?></div>
                          <div class="row g-2">
                            <div class="col-md-6">
                              <div class="soft-card p-2">
                                ক) <?php echo e($item["option_a_bn"]); ?>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="soft-card p-2">
                                খ) <?php echo e($item["option_b_bn"]); ?>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="soft-card p-2">
                                গ) <?php echo e($item["option_c_bn"]); ?>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="soft-card p-2">
                                ঘ) <?php echo e($item["option_d_bn"]); ?>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="modal-footer">
                          <span class="text-muted small me-auto">সঠিক উত্তর: <?php echo e($item["correct_option"]); ?></span>
                          <button class="btn btn-outline-dark" type="button" data-bs-dismiss="modal">বন্ধ করুন</button>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php } ?>
              <?php } ?>
            </tbody>
          </table>
          <?php if ($questionsTotalPages > 1) { ?>
            <nav class="mt-3">
              <ul class="pagination pagination-sm mb-0">
                <?php
                  $base = "/admin/questions.php?month=" . urlencode($monthYear) . "&page=";
                  $prev = max(1, $questionsPage - 1);
                  $next = min($questionsTotalPages, $questionsPage + 1);
                ?>
                <li class="page-item <?php echo $questionsPage <= 1 ? "disabled" : ""; ?>">
                  <a class="page-link" href="<?php echo $base . $prev; ?>">&laquo;</a>
                </li>
                <?php for ($p = 1; $p <= $questionsTotalPages; $p++) { ?>
                  <li class="page-item <?php echo $p === $questionsPage ? "active" : ""; ?>">
                    <a class="page-link" href="<?php echo $base . $p; ?>"><?php echo $p; ?></a>
                  </li>
                <?php } ?>
                <li class="page-item <?php echo $questionsPage >= $questionsTotalPages ? "disabled" : ""; ?>">
                  <a class="page-link" href="<?php echo $base . $next; ?>">&raquo;</a>
                </li>
              </ul>
            </nav>
          <?php } ?>
        </div>
      </div>
    </div>
  </section>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>






