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
  "sms" => [
    "endpoint" => "https://api.sms.net.bd/sendsms",
    "api_key" => getenv("SMS_API_KEY") ?: "664wzp0FT7R5y2CER5ck5495PTP0U7Cmj0S1DM8d",
    "otp_expire_minutes" => 5,
    "otp_max_attempts" => 5,
  ],
  "nagorikpay" => [
    "api_key" => getenv("NAGORIKPAY_API_KEY") ?: "jjAyHFZAjspgOQfp8JAnL1AasTWriJ6NbH8yR3hxUqf6u8EhtB",
    // "api_key" => getenv("NAGORIKPAY_API_KEY") ?: "0OgDtlxZdHZhjkyQjdxNciAixzAWL2gAQ2gvVIAZ3ycyIupsIN",
    "create_url" => "https://secure-pay.nagorikpay.com/api/payment/create",
    "verify_url" => "https://secure-pay.nagorikpay.com/api/payment/verify",
  ],
];
