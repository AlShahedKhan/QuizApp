<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/styles.css" />
  </head>
  <body>
    <main class="shell">
      <header class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div class="brand-mark">
          <span class="brand-dot"></span>
          <span>QuizTap</span>
        </div>
        <ul class="nav nav-pills gap-2">
          <li class="nav-item">
            <a class="nav-link pill-tab" href="../quiz/play.php">Quiz Play</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pill-tab active" href="#">My Account</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pill-tab" href="../referral/referral.php">Referral</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pill-tab" href="../quiz/leaderboard.php">Winner</a>
          </li>
        </ul>
      </header>

      <section class="glass-card p-4 mb-4 reveal">
        <div class="row g-4">
          <div class="col-lg-4">
            <div class="soft-card p-4 h-100">
              <div class="text-muted text-uppercase small">Total Balance</div>
              <div class="metric">1,250 TK</div>
              <p class="text-muted mb-0">Includes bonus + purchased credits.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="soft-card p-4 h-100">
              <div class="text-muted text-uppercase small">Referral Wallet</div>
              <div class="metric">320 TK</div>
              <p class="text-muted mb-0">Withdrawable via Bkash/Nogod.</p>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="soft-card p-4 h-100">
              <div class="text-muted text-uppercase small">Monthly Rank</div>
              <div class="metric">#12</div>
              <p class="text-muted mb-0">43 pts away from Top 5.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="row g-4">
        <div class="col-lg-7 reveal delay-1">
          <div class="soft-card p-4 h-100">
            <h3 class="mb-3">Recent Transactions</h3>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="text-muted">Today</span>
              <span class="tag">Wallet Ledger</span>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
              <div>
                <div class="fw-semibold">Quiz entry fee</div>
                <div class="text-muted small">Bangla Monthly Quiz</div>
              </div>
              <div class="text-danger fw-semibold">-10 TK</div>
            </div>
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
              <div>
                <div class="fw-semibold">Referral unlocked</div>
                <div class="text-muted small">User: 01900XXXXXX</div>
              </div>
              <div class="text-success fw-semibold">+50 TK</div>
            </div>
            <div class="d-flex justify-content-between align-items-center py-2">
              <div>
                <div class="fw-semibold">Credit purchase</div>
                <div class="text-muted small">Bkash TXN: 9F56X</div>
              </div>
              <div class="text-success fw-semibold">+200 TK</div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 reveal delay-2">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">Quick Actions</h3>
            <div class="d-grid gap-2">
              <a class="btn btn-primary" href="../quiz/play.php">Start Quiz</a>
              <a class="btn btn-outline-dark" href="../user/buy-credit.php"
                >Buy Credits</a
              >
              <a class="btn btn-outline-dark" href="../user/withdraw.php"
                >Withdraw Referral</a
              >
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">Referral Snapshot</h3>
            <p class="text-muted">
              Share your link and unlock 50 TK after your friend uses the signup
              bonus and completes their first purchase.
            </p>
            <div class="d-flex align-items-center justify-content-between border rounded-3 p-3">
              <div>
                <div class="fw-semibold">quiztap.com/r/shahed22</div>
                <div class="text-muted small">12 friends joined</div>
              </div>
              <button class="btn btn-outline-dark btn-sm" type="button">
                Copy
              </button>
            </div>
          </div>
        </div>
      </section>
    </main>
    <script src="../assets/js/app.js"></script>
  </body>
</html>
