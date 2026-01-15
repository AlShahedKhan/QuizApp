<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - Play Quiz</title>
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
            <a class="nav-link pill-tab active" href="#">Quiz Play</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pill-tab" href="../user/dashboard.php">My Account</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pill-tab" href="../referral/referral.php">Referral</a>
          </li>
          <li class="nav-item">
            <a class="nav-link pill-tab" href="../quiz/leaderboard.php">Winner</a>
          </li>
        </ul>
      </header>

      <section class="row g-4 align-items-stretch mb-4">
        <div class="col-lg-8 reveal">
          <div class="glass-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <span class="tag">Question 04 / 10</span>
                <h2 class="mt-2">Which river is the longest in Bangladesh?</h2>
              </div>
              <div class="text-end">
                <div class="text-muted small">Time left</div>
                <div class="metric">00:24</div>
              </div>
            </div>
            <div class="d-grid gap-3 mb-4">
              <div class="option-card" data-option>
                A. Meghna
              </div>
              <div class="option-card" data-option>
                B. Jamuna
              </div>
              <div class="option-card" data-option>
                C. Padma
              </div>
              <div class="option-card" data-option>
                D. Karnaphuli
              </div>
            </div>
            <div class="d-flex flex-wrap gap-2">
              <button class="btn btn-outline-dark" type="button">Skip</button>
              <button class="btn btn-primary" type="button">Submit Answer</button>
            </div>
          </div>
        </div>
        <div class="col-lg-4 reveal delay-1">
          <div class="soft-card p-4 mb-4">
            <h3 class="mb-3">Your Stats</h3>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">Credits left</span>
              <span class="fw-semibold">85 TK</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">Correct streak</span>
              <span class="fw-semibold">3</span>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">Current points</span>
              <span class="fw-semibold">120</span>
            </div>
          </div>
          <div class="soft-card p-4">
            <h3 class="mb-3">Top 5 This Month</h3>
            <div class="leaderboard-row mb-2">
              <span>1. Nabila</span>
              <span class="fw-semibold">980</span>
            </div>
            <div class="leaderboard-row mb-2">
              <span>2. Arif</span>
              <span class="fw-semibold">930</span>
            </div>
            <div class="leaderboard-row mb-2">
              <span>3. Tanim</span>
              <span class="fw-semibold">910</span>
            </div>
            <div class="leaderboard-row mb-2">
              <span>4. Samiha</span>
              <span class="fw-semibold">880</span>
            </div>
            <div class="leaderboard-row">
              <span>5. Hasan</span>
              <span class="fw-semibold">860</span>
            </div>
          </div>
        </div>
      </section>

      <section class="soft-card p-4 reveal delay-2">
        <div class="row g-4 align-items-center">
          <div class="col-md-8">
            <h3 class="mb-2">Monthly Winner Spotlight</h3>
            <p class="text-muted mb-0">
              Last month, Farzana took the crown with 1,220 points. Keep your
              streak alive to enter the Top 5 and earn special bonuses.
            </p>
          </div>
          <div class="col-md-4 text-md-end">
            <button class="btn btn-outline-dark" type="button">
              View full leaderboard
            </button>
          </div>
        </div>
      </section>
    </main>
    <script src="../assets/js/app.js"></script>
  </body>
</html>
