# Web-Based Quiz Application (Pure PHP) — Implementation Plan (QA → UI → Backend)

> Tech: **Pure PHP (no framework)**, MySQL, HTML5, CSS3, **Bootstrap 5**, jQuery  
> Goals: **Bangla support**, **mobile-first responsive UI**, clean code, security best practices, scalable structure.

---

## 0) Scope Summary (from client doc)
- User: signup/login with **mobile + password (bcrypt)**, unique mobile
- Credits: signup **100 TK bonus**, purchase credits (**1 TK = 1 credit**, min 50), spend credits to play quiz
- Referral: each user has referral URL; referrer gets **50 TK referral credit** only when:
  1) referred user uses their **100 TK bonus**
  2) then purchases credits
- Quiz: Bangla, monthly cycle reset, points for correct, leaderboard top 5, last month winner
- Wallet logs: full transaction ledger (bonus/purchase/referral/quiz/withdraw)
- Withdraw: referral credits withdrawable via **Bkash/Nogod** (not quiz winnings)
- Admin: manage users, questions, transactions, withdrawals, monthly top scorers, reset automation
- UI tabs: Quiz Play, My Account, Referral, Winner (+ Auth pages)

---

## 1) Milestones Delivery Strategy
### Milestone 1 — Project QA ($5)
Deliver:
- Final page list + navigation
- DB schema draft (tables + fields)
- Wireframe + **UI prototype (3 pages)**: Login/Signup, Dashboard, Quiz Play (responsive)
- Notes on referral condition + monthly reset logic

### Milestone 2 — HTML Design ($5)
Deliver:
- Full responsive UI (all required pages)
- Consistent components (navbar/tabs/cards/buttons/forms)
- Bangla-friendly typography
- Sample/dummy data views
- Admin UI base layouts

### Milestone 3–5 — Backend (PHP/MySQL), Payments, Final delivery
Proceed after UI approval.

---

## 2) Folder Structure (Pure PHP, MVC-ish but light)
/quiz-app
/public
index.php
.htaccess (if Apache)
/app
/config
config.php
db.php
/helpers
auth.php
csrf.php
validate.php
sanitize.php
time.php
/models
User.php
Transaction.php
Referral.php
Question.php
Attempt.php
Withdrawal.php
/controllers
AuthController.php
AccountController.php
QuizController.php
ReferralController.php
AdminController.php
/views
/partials
head.php
footer.php
tabs.php
alerts.php
/auth
login.php
register.php
/account
dashboard.php
buy_credit.php
transactions.php
withdraw.php
/quiz
play.php
leaderboard.php
/referral
index.php
/winner
index.php
/admin
login.php
dashboard.php
users.php
questions.php
transactions.php
withdrawals.php
/assets
/css
app.css
/js
app.js
/img
/storage
/logs
/sql
schema.sql
seed.sql


Notes:
- Keep `public/` as web root.
- All PHP includes use absolute paths.
- No framework, but structured code for maintainability.

---

## 3) UI/UX Plan (Mobile-first, attractive, simple)
### 3.1 Design System
- Bootstrap 5 + custom CSS for branding
- Use one Bangla-friendly font (system fallback is ok)
- Consistent spacing: cards, rounded corners, soft shadows
- Primary layout: tab-based navigation at bottom on mobile, top on desktop

### 3.2 Shared Components
- Top header: app name + current month + countdown chip
- Tabs navigation:
  - Quiz Play
  - My Account
  - Referral
  - Winner
- Reusable cards:
  - Credits card
  - Monthly score card
  - Referral earnings card
- Alerts/toasts for success/error
- Skeleton loader for quiz question

### 3.3 Required Pages (UI)
#### Auth
1) `Login` (mobile number + password)
2) `Signup` (mobile, password, optional referral code)

#### Main App
3) `My Account / Dashboard`
   - current credits
   - monthly score
   - referral earnings (separate)
   - buttons: Buy Credits, Withdraw Referral, Transactions

4) `Buy Credits`
   - amount input (min 50)
   - summary: “1 TK = 1 credit”
   - submit button (payment step later: manual/admin approve or gateway)

5) `Transactions`
   - filter tabs: All / Bonus / Purchase / Quiz / Referral / Withdraw
   - table list mobile-friendly

6) `Withdraw Referral`
   - method: bkash/nogod
   - number input
   - amount (<= referral balance)
   - status display

7) `Quiz Play`
   - question in Bangla
   - options (radio cards)
   - next button
   - show “Cost: 1 credit” badge
   - show credits remaining
   - feedback: correct/incorrect (optional)

8) `Leaderboard`
   - top 5 current month
   - user position highlight

9) `Winner`
   - last month winner
   - prize amount
   - month summary

#### Admin
10) `Admin Login`
11) `Admin Dashboard`
12) `Users Management` (edit credits, reset score, view profile)
13) `Questions CRUD` (Bangla fields)
14) `Transactions Ledger`
15) `Withdraw Requests` (approve/reject)
16) `Monthly Top Scorers Report`

---

## 4) Database Schema Plan (High level)
### 4.1 Core Tables
- `users`
  - id, mobile (unique), password_hash
  - referral_code (unique), referred_by_user_id (nullable)
  - credits_balance (int)
  - referral_balance (int)  // withdrawable only
  - monthly_score (int)
  - created_at

- `transactions`
  - id, user_id, type (bonus|purchase|quiz_deduct|referral_credit|withdraw)
  - amount (int), meta_json (json/text), created_at
  - index(user_id, type, created_at)

- `referrals`
  - id, referrer_id, referred_user_id
  - bonus_used_at (nullable)
  - first_purchase_at (nullable)
  - reward_given (tinyint)
  - reward_amount (int default 50)
  - created_at
  - unique(referred_user_id)  // one referrer per user

- `quiz_questions`
  - id, question_bn, option_a_bn, option_b_bn, option_c_bn, option_d_bn
  - correct_option (A/B/C/D)
  - is_active, created_at

- `quiz_attempts`
  - id, user_id, question_id
  - month_year (YYYY-MM)
  - is_correct (tinyint)
  - created_at
  - index(user_id, month_year)

- `withdrawals`
  - id, user_id
  - method (bkash|nogod), account_number
  - amount, status (pending|approved|rejected)
  - admin_note, created_at, updated_at

- `monthly_winners`
  - id, month_year, user_id, score, prize_amount, created_at
  - unique(month_year)

### 4.2 Monthly Reset
- Determine current month_year = `date('Y-m')`
- Store attempts and score per month via `month_year`
- On month change:
  - Calculate last month top scorer(s)
  - Insert into `monthly_winners`
  - Reset users.monthly_score = 0 (or compute score dynamically by attempts)

---

## 5) Business Logic Rules (Must Implement)
### 5.1 Credits
- Signup: +100 credits to credits_balance, log transaction type=bonus
- Purchase: credits_balance += amount, log transaction type=purchase
- Quiz play: each question costs `COST_PER_QUESTION` (default 1)
  - Deduct before attempt submission
  - Log transaction type=quiz_deduct

### 5.2 Referral Reward Condition
Reward 50 to referrer only when:
1) referred user has **used bonus** (bonus credits spent at least 100 total)
2) THEN user makes **first purchase** (>= 50)

Implementation approach:
- Track in `referrals`:
  - `bonus_used_at`: set when referred user's bonus fully consumed (or when total bonus-spend reaches 100)
  - `first_purchase_at`: set on first purchase
  - if both set and `reward_given=0` => give reward:
    - referrer.referral_balance += 50
    - transaction type=referral_credit for referrer
    - set reward_given=1

### 5.3 Withdrawals
- Only from `referral_balance`
- Create withdraw request => pending
- Admin approve:
  - referral_balance -= amount
  - transaction type=withdraw
  - status=approved

### 5.4 Scoring
- Correct answer => points add (configurable, default 1 per correct)
- Eligibility and prize cap logic in admin report:
  - min 5000 points to be eligible
  - max prize 30000 TK
  - example: payout = min(30000, score * 5) etc. (confirm exact formula with client if unclear)

---

## 6) Security & Best Practices Checklist
- Password hashing: `password_hash($pass, PASSWORD_BCRYPT)`; verify with `password_verify`
- SQL Injection: PDO prepared statements only
- XSS: escape output with `htmlspecialchars`
- CSRF tokens on all POST forms
- Server-side validation for all inputs
- Rate limit login attempts (basic: session-based)
- Use session regeneration on login
- Strict file permissions; logs outside public
- Use `utf8mb4` in DB and connection
- Validate mobile number format and uniqueness
- Admin routes protected by role check

---

## 7) HTML Design Implementation Tasks (Milestone 2)
### 7.1 Create UI Starter
- `assets/css/app.css`
- `assets/js/app.js`
- `views/partials/head.php` includes Bootstrap, app.css
- `views/partials/tabs.php` includes navigation

### 7.2 Build Pages (UI only)
- auth/login + auth/register
- account/dashboard
- account/buy_credit
- account/transactions
- account/withdraw
- quiz/play
- quiz/leaderboard
- referral/index
- winner/index
- admin/login + admin/dashboard + admin/users + admin/questions + admin/transactions + admin/withdrawals

### 7.3 Dummy Data
- Use placeholder numbers in cards
- Use sample leaderboard list
- Use example Bangla question/answers

### 7.4 Responsive Requirements
- Mobile: bottom tabs fixed; content padding bottom
- Desktop: tabs become top nav; 2-column dashboard cards
- Use Bootstrap grid and utilities

---

## 8) QA Plan (Before Backend)
- Confirm page flow matches client expectation
- Confirm Bangla rendering ok on all pages
- Confirm UI looks good on:
  - 360x640 (mobile)
  - 768x1024 (tablet)
  - 1366x768 (laptop)

Deliver UI preview screenshots or zip.

---

## 9) Backend Plan (After UI Approval)
1) DB schema create + seed admin user
2) Auth module (register/login/logout)
3) Account module (credits/transactions)
4) Purchase credits flow (placeholder/manual approval if no gateway)
5) Quiz flow (deduct credits, record attempt, score update)
6) Referral reward automation
7) Withdraw request + admin approve
8) Leaderboard + winner tab
9) Monthly reset job (cron or admin button)
10) Final hardening + documentation + deployment guide

---

## 10) Deliverables
- Full working app (deployment-ready)
- `/sql/schema.sql` + indexes
- Clean source code + comments
- README with setup steps
- Admin credentials (on delivery)

---
End of plan.
