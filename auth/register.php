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
              আজই ১০০ টাকা বোনাস ক্রেডিট দিয়ে শুরু করুন!
            </h1>
            <p class="text-white-50 mb-4">
              আপনার অ্যাকাউন্ট তৈরি করুন, বোনাস ক্রেডিট নিন, আর সঠিক উত্তরে
              লিডারবোর্ডে এগিয়ে যান।
            </p>
            <div class="promo-badges mb-4">
              <span class="badge-pill">সাইনআপে ১০০ TK বোনাস ক্রেডিট</span>
              <span class="badge-pill">রেফারেল রিওয়ার্ড ৫০ TK বোনাস ক্রেডিট</span>
              <span class="badge-pill">বাংলা-ফার্স্ট কুইজ</span>
            </div>
            <div class="promo-card">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-white-50 text-uppercase small">
                    মাসিক পুল
                  </div>
                  <div class="promo-stat">৩০,০০০ টাকা</div>
                </div>
                <div class="text-end">
                  <div class="text-white-50 text-uppercase small">
                    টপ ৫ জয়
                  </div>
                  <div class="promo-stat">নিশ্চিত</div>
                </div>
              </div>
              <p class="promo-note mb-0">
                আপনার বন্ধু প্রথম কেনাকাটা শেষ করলে রেফারেল ক্রেডিট উত্তোলনযোগ্য
                হয়।
              </p>
            </div>
          </section>
        </div>

        <div class="col-lg-6 reveal delay-1">
          <section class="auth-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <h2 class="mb-1">নতুন অ্যাকাউন্ট তৈরি করুন</h2>
                <p class="text-muted mb-0">
                  রিওয়ার্ড পেতে একটি বৈধ মোবাইল নম্বর ব্যবহার করুন।
                </p>
              </div>
              <span class="tag">সাইনআপ</span>
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
                  ১১ ডিজিটের নম্বর লিখুন।
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
                  পাসওয়ার্ড নিশ্চিত করুন
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
                  রেফারেল লিঙ্ক শনাক্ত হয়েছে (স্বয়ংক্রিয়ভাবে প্রযোজ্য হয়েছে)।
                </div>
              </div>
              <button class="btn btn-primary w-100 mb-3" type="submit">
                অ্যাকাউন্ট তৈরি করুন
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
