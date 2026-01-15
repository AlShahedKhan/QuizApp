<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - Login</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/styles.css" />
  </head>
  <body>
    <main class="shell">
      <div class="auth-grid">
        <section class="auth-hero reveal">
          <div class="d-flex align-items-center gap-2 mb-4">
            <span class="brand-dot"></span>
            <span class="fw-semibold">QuizTap</span>
          </div>
          <h1 class="display-title mb-3">Bangla-first quiz rewards, done right.</h1>
          <p class="text-white-50 mb-4">
            Play monthly challenges, earn wallet credits, and track every
            transaction with full transparency.
          </p>
          <div class="d-flex flex-wrap gap-2 mb-4">
            <span class="tag">100 TK signup bonus</span>
            <span class="tag">Referral credit unlock</span>
            <span class="tag">Monthly winners</span>
          </div>
          <div class="glass-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <div>
                <div class="text-white-50 text-uppercase small">Active players</div>
                <div class="metric text-white">4,821</div>
              </div>
              <div class="text-end">
                <div class="text-white-50 text-uppercase small">Top score</div>
                <div class="metric text-white">980</div>
              </div>
            </div>
            <p class="text-white-50 mb-0">
              Monthly leaderboard resets automatically on the first of every
              month.
            </p>
          </div>
        </section>

        <section class="soft-card auth-form reveal delay-1">
          <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
              <h2 class="mb-1">Welcome back</h2>
              <p class="text-muted mb-0">Login with your verified mobile number.</p>
            </div>
            <span class="tag">Secure</span>
          </div>
          <form>
            <div class="mb-3">
              <label class="form-label">Mobile Number</label>
              <input
                type="tel"
                class="form-control"
                placeholder="01XXXXXXXXX"
              />
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" placeholder="••••••••" />
            </div>
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" />
                <label class="form-check-label" for="remember">Remember me</label>
              </div>
              <a class="text-muted small" href="#">Forgot password?</a>
            </div>
            <button class="btn btn-primary w-100 mb-3" type="button">
              Login to Dashboard
            </button>
            <div class="text-center text-muted small">
              New here?
              <a href="register.php" class="fw-semibold text-decoration-none"
                >Create an account</a
              >
            </div>
          </form>
        </section>
      </div>
    </main>
    <script src="../assets/js/app.js"></script>
  </body>
</html>
