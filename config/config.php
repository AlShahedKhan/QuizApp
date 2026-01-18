<?php
return [
  "db" => [
    "host" => getenv("DB_HOST") ?: "localhost",
    "name" => getenv("DB_NAME") ?: "quizapp",
    "user" => getenv("DB_USER") ?: "root",
    "pass" => getenv("DB_PASS") ?: "root",
    "charset" => "utf8mb4",
  ],
  "app" => [
    "base_url" => rtrim(getenv("APP_URL") ?: "", "/"),
    "timezone" => getenv("APP_TIMEZONE") ?: "Asia/Dhaka",
  ],
  "quiz" => [
    "cost_per_question" => 1,
    "points_per_correct" => 1,
  ],
  "credits" => [
    "signup_bonus" => 100,
    "min_purchase" => 50,
    "referral_reward" => 50,
    "min_withdraw" => 50,
  ],
  "prize" => [
    "min_score" => 5000,
    "per_point" => 5,
    "max_prize" => 30000,
  ],
];
