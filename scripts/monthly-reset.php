<?php
require_once __DIR__ . "/../config/bootstrap.php";

$isLastDay = date("j") === date("t");
if (!$isLastDay) {
  exit("Not the last day of the month. No reset performed.\n");
}

$targetMonth = date("Y-m");

$stmt = db()->prepare("SELECT id FROM monthly_resets WHERE month_year = ?");
$stmt->execute([$targetMonth]);
if ($stmt->fetch()) {
  exit("Monthly reset already completed for {$targetMonth}.\n");
}

$stmt = db()->prepare(
  "SELECT user_id, SUM(is_correct) AS corrects
   FROM quiz_attempts
   WHERE month_year = ?
   GROUP BY user_id
   ORDER BY corrects DESC, user_id ASC
   LIMIT 1"
);
$stmt->execute([$targetMonth]);
$top = $stmt->fetch();

$pointsPerCorrect = (int)config("quiz.points_per_correct", 1);
$minScore = (int)config("prize.min_score", 5000);
$perPoint = (int)config("prize.per_point", 5);
$maxPrize = (int)config("prize.max_prize", 30000);

$pdo = db();
$pdo->beginTransaction();
try {
  if ($top) {
    $score = (int)$top["corrects"] * $pointsPerCorrect;
    $prize = $score >= $minScore ? min($maxPrize, $score * $perPoint) : 0;
    $stmt = $pdo->prepare(
      "INSERT INTO monthly_winners (month_year, user_id, score, prize_amount, created_at)
       VALUES (?, ?, ?, ?, NOW())"
    );
    $stmt->execute([$targetMonth, (int)$top["user_id"], $score, $prize]);
  }

  $pdo->exec("UPDATE users SET monthly_score = 0");

  $stmt = $pdo->prepare(
    "INSERT INTO monthly_resets (month_year, created_at) VALUES (?, NOW())"
  );
  $stmt->execute([$targetMonth]);

  $pdo->commit();
  exit("Monthly reset completed for {$targetMonth}.\n");
} catch (Throwable $e) {
  $pdo->rollBack();
  throw $e;
}
