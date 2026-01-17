<!DOCTYPE html>
<html lang="bn">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - সাইনআপ</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="../assets/css/auth.css" />
  </head>
  <body>
    <main class="auth-shell">
      <div class="row g-4">
        <div class="col-lg-6 reveal">
          <section class="promo-panel h-100">
            <div class="brand-mark mb-4">
              <span class="brand-dot"></span>
              <span>QuizTap</span>
            </div>
            <h1 class="promo-title">
              নতুন অ্যাকাউন্ট খুলে আজই 100 TK বোনাস নিন!
            </h1>
            <p class="text-white-50 mb-4">
              সাইনআপ করুন, কুইজ খেলুন, আর আপনার রেফারেল লিংক শেয়ার করে অতিরিক্ত
              আয় করুন।
            </p>
            <div class="promo-badges mb-4">
              <span class="badge-pill">বোনাস ক্রেডিট 100 TK</span>
              <span class="badge-pill">রেফারেলে 50 TK পুরস্কার</span>
              <span class="badge-pill">মাসিক লিডারবোর্ড</span>
            </div>
            <div class="promo-card">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-white-50 text-uppercase small">
                    সাইনআপ সম্পন্ন
                  </div>
                  <div class="promo-stat">21,430 জন</div>
                </div>
                <div class="text-end">
                  <div class="text-white-50 text-uppercase small">
                    টপ স্কোর
                  </div>
                  <div class="promo-stat">1,280</div>
                </div>
              </div>
              <p class="promo-note mb-0">
                বোনাস ব্যবহার শেষ করে প্রথম ক্রেডিট কিনলেই রেফারেল রিওয়ার্ড
                পাওয়া যায়।
              </p>
            </div>
          </section>
        </div>

        <div class="col-lg-6 reveal delay-1">
          <section class="auth-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <h2 class="mb-1">সাইনআপ করুন</h2>
                <p class="text-muted mb-0">
                  আপনার মোবাইল নম্বর দিয়ে দ্রুত অ্যাকাউন্ট খুলুন।
                </p>
              </div>
              <span class="tag">নতুন</span>
            </div>

            <div class="error-box mb-3" data-error-box role="alert"></div>

            <form novalidate data-auth-form>
              <div class="mb-3">
                <label class="form-label" for="mobile">মোবাইল নম্বর</label>
                <input
                  id="mobile"
                  name="mobile"
                  type="tel"
                  class="form-control"
                  placeholder="01XXXXXXXXX"
                  inputmode="numeric"
                  pattern="^01[0-9]{9}$"
                  maxlength="11"
                  aria-describedby="mobileHelp mobileError"
                  required
                  data-mobile
                />
                <div id="mobileHelp" class="form-text text-muted">
                  ১১ ডিজিটের মোবাইল নম্বর লিখুন।
                </div>
                <div class="invalid-feedback" id="mobileError" data-mobile-error></div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="password">পাসওয়ার্ড</label>
                <input
                  id="password"
                  name="password"
                  type="password"
                  class="form-control"
                  placeholder="••••••••"
                  minlength="6"
                  aria-describedby="passwordError"
                  required
                  data-password
                />
                <div
                  class="invalid-feedback d-block"
                  id="passwordError"
                  data-password-error
                ></div>
              </div>
              <div class="mb-3">
                <label class="form-label" for="confirmPassword">
                  পাসওয়ার্ড আবার লিখুন
                </label>
                <input
                  id="confirmPassword"
                  name="confirmPassword"
                  type="password"
                  class="form-control"
                  placeholder="••••••••"
                  minlength="6"
                  required
                />
              </div>
              <div class="mb-4">
                <div class="border rounded-3 p-3 text-muted small">
                  রেফারেল কোড থাকলে এখানে দিন (ঐচ্ছিক)।
                </div>
              </div>
              <button class="btn btn-primary w-100 mb-3" type="submit">
                সাইনআপ সম্পন্ন করুন
              </button>
              <div class="text-center text-muted small">
                ইতিমধ্যে অ্যাকাউন্ট আছে?
                <a href="login.php" class="fw-semibold text-decoration-none"
                  >লগইন করুন</a
                >
              </div>
            </form>
          </section>
        </div>
      </div>
    </main>
    <script src="../assets/js/auth.js"></script>
  </body>
</html>
