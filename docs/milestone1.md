# Milestone 1 - Project QA Deliverables

## 1) Final Page List + Navigation

### Public/Auth
- `auth/login.php` - Login with mobile + password
- `auth/register.php` - Signup with mobile + password + referral code
- `auth/logout.php` - End session

### User (Authenticated)
- `user/dashboard.php` - Wallet summary, rank, recent transactions
- `quiz/play.php` - Play quiz + answer flow
- `quiz/leaderboard.php` - Current top 5 + last month winner
- `referral/referral.php` - Referral link + invited list + referral wallet
- `user/buy-credit.php` - Purchase credits (min 50)
- `user/transactions.php` - Full wallet ledger
- `user/withdraw.php` - Withdraw referral credits (Bkash/Nogod)

### Admin
- `admin/login.php` - Admin auth
- `admin/users.php` - User list + search + status
- `admin/questions.php` - Question CRUD
- `admin/transactions.php` - Wallet ledger overview
- `admin/withdrawals.php` - Withdrawal approvals

### Navigation Rules
- Primary tabs: Quiz Play, My Account, Referral, Winner
- User actions: Buy Credits, Withdraw, View Transactions
- Auth gating: all user pages require login; public only for login/register

---

## 2) DB Schema Draft (Tables + Key Fields)

### `users`
- `id` (PK), `mobile` (unique), `password_hash`, `referral_code` (unique)
- `referred_by_user_id` (FK users.id, nullable)
- `status` (active/suspended), `created_at`

### `wallet_ledger`
- `id` (PK), `user_id` (FK), `type` (bonus/purchase/referral/quiz/withdraw)
- `amount` (signed), `balance_after`, `metadata_json`, `created_at`

### `wallet_balances`
- `user_id` (PK FK), `bonus_balance`, `paid_balance`, `referral_balance`, `updated_at`

### `credit_purchases`
- `id` (PK), `user_id` (FK), `amount`, `payment_channel`, `payment_ref`, `status`
- `created_at`

### `referral_events`
- `id` (PK), `referrer_user_id` (FK), `referred_user_id` (FK)
- `bonus_used_at`, `first_purchase_at`, `rewarded_at`, `status`

### `quiz_months`
- `id` (PK), `month_key` (YYYY-MM), `starts_at`, `ends_at`, `status`

### `quiz_questions`
- `id` (PK), `question_text`, `options_json`, `correct_index`
- `difficulty`, `is_active`, `created_at`

### `quiz_attempts`
- `id` (PK), `user_id` (FK), `quiz_month_id` (FK), `score`, `started_at`, `ended_at`

### `quiz_answers`
- `id` (PK), `attempt_id` (FK), `question_id` (FK)
- `selected_index`, `is_correct`, `created_at`

### `monthly_leaderboard`
- `id` (PK), `quiz_month_id` (FK), `user_id` (FK), `score`, `rank`

### `withdrawals`
- `id` (PK), `user_id` (FK), `amount`, `channel` (Bkash/Nogod)
- `account_number`, `status` (pending/approved/paid/rejected), `created_at`

### `admin_users`
- `id` (PK), `email`, `password_hash`, `role`, `created_at`

---

## 3) Wireframe + UI Prototype (3 Pages)

Pages implemented as clickable prototypes:
- `auth/login.php`
- `user/dashboard.php`
- `quiz/play.php`

Design notes:
- Mobile-first layout with Bootstrap 5 grid
- Expressive typography (Playfair Display + Space Grotesk)
- Gradient atmosphere + glass cards for premium feel
- Staggered reveals for primary panels

---

## 4) Referral Condition + Monthly Reset Logic

### Referral Reward Condition
1) Referred user signs up and receives 100 TK bonus
2) Referred user spends the bonus (quiz entries reduce bonus balance)
3) Referred user completes first purchase (min 50 TK)
4) On purchase success, referrer receives 50 TK referral credit

### Monthly Reset Logic
- Create `quiz_months` record per calendar month (YYYY-MM)
- On new month:
  - Set previous month to `closed`
  - Snapshot Top 5 into `monthly_leaderboard`
  - Reset monthly scores/attempt counters for new month
  - Display last month winner from `monthly_leaderboard`
