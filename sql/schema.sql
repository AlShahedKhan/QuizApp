CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mobile VARCHAR(20) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  referral_code VARCHAR(20) NOT NULL UNIQUE,
  referred_by_user_id INT NULL,
  credits_balance INT NOT NULL DEFAULT 0,
  referral_balance INT NOT NULL DEFAULT 0,
  monthly_score INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_users_referred_by
    FOREIGN KEY (referred_by_user_id) REFERENCES users(id)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  type ENUM('bonus','purchase','quiz_deduct','referral_credit','withdraw') NOT NULL,
  amount INT NOT NULL,
  meta_json TEXT NULL,
  status ENUM('pending','approved','rejected','completed') NOT NULL DEFAULT 'completed',
  created_at DATETIME NOT NULL,
  INDEX idx_transactions_user_type_created (user_id, type, created_at),
  CONSTRAINT fk_transactions_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE referrals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  referrer_id INT NOT NULL,
  referred_user_id INT NOT NULL UNIQUE,
  bonus_used_at DATETIME NULL,
  first_purchase_at DATETIME NULL,
  reward_given TINYINT(1) NOT NULL DEFAULT 0,
  reward_amount INT NOT NULL DEFAULT 50,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_referrals_referrer
    FOREIGN KEY (referrer_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_referrals_referred
    FOREIGN KEY (referred_user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_questions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  question_bn TEXT NOT NULL,
  option_a_bn TEXT NOT NULL,
  option_b_bn TEXT NOT NULL,
  option_c_bn TEXT NOT NULL,
  option_d_bn TEXT NOT NULL,
  correct_option ENUM('A','B','C','D') NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_question_sets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  month_year CHAR(7) NOT NULL UNIQUE,
  title VARCHAR(120) NOT NULL,
  time_limit_seconds INT NOT NULL DEFAULT 30,
  questions_per_quiz INT NOT NULL DEFAULT 10,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_question_set_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  set_id INT NOT NULL,
  question_id INT NOT NULL,
  position INT NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  UNIQUE KEY uniq_set_question (set_id, question_id),
  INDEX idx_set_items_set (set_id, position),
  CONSTRAINT fk_set_items_set
    FOREIGN KEY (set_id) REFERENCES quiz_question_sets(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_set_items_question
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE quiz_attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  question_id INT NOT NULL,
  month_year CHAR(7) NOT NULL,
  is_correct TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  INDEX idx_attempts_user_month (user_id, month_year),
  CONSTRAINT fk_attempts_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_attempts_question
    FOREIGN KEY (question_id) REFERENCES quiz_questions(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE withdrawals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  method ENUM('বিকাশ','নগদ') NOT NULL,
  account_number VARCHAR(20) NOT NULL,
  amount INT NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_note TEXT NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  CONSTRAINT fk_withdrawals_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE monthly_winners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  month_year CHAR(7) NOT NULL UNIQUE,
  user_id INT NOT NULL,
  score INT NOT NULL,
  prize_amount INT NOT NULL,
  created_at DATETIME NOT NULL,
  CONSTRAINT fk_monthly_winners_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE monthly_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  month_year CHAR(7) NOT NULL UNIQUE,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
