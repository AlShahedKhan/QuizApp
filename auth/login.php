<!DOCTYPE html>
<html lang="bn">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - লগইন</title>
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
              বাংলার প্রথম কুইজ রিওয়ার্ড, একদম ঠিকভাবে
            </h1>
            <p class="text-white-50 mb-4">
              মাসিক চ্যালেঞ্জ খেলুন, ওয়ালেট ক্রেডিট অর্জন করুন, এবং প্রতিটি
              লেনদেন স্বচ্ছভাবে ট্র্যাক করুন।
            </p>
            <div class="promo-badges mb-4">
              <span class="badge-pill">সাইনআপ বোনাস</span>
              <span class="badge-pill">রেফারেল ক্রেডিট আনলক</span>
              <span class="badge-pill">মাসিক বিজয়ী</span>
            </div>
            <div class="promo-card">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="text-white-50 text-uppercase small">
                    সক্রিয় খেলোয়াড়
                  </div>
                  <div class="promo-stat">4,821</div>
                </div>
                <div class="text-end">
                  <div class="text-white-50 text-uppercase small">
                    সর্বোচ্চ স্কোর
                  </div>
                  <div class="promo-stat">980</div>
                </div>
              </div>
              <p class="promo-note mb-0">
                লিডারবোর্ড প্রতি মাসের ১ তারিখ স্বয়ংক্রিয়ভাবে রিসেট হয়।
              </p>
            </div>
          </section>
        </div>

        <div class="col-lg-6 reveal delay-1">
          <section class="auth-card">
            <div class="d-flex align-items-center justify-content-between mb-4">
              <div>
                <h2 class="mb-1">ফিরে আসায় স্বাগতম</h2>
                <p class="text-muted mb-0">
                  আপনার যাচাইকৃত মোবাইল নম্বর দিয়ে লগইন করুন।
                </p>
              </div>
              <span class="tag">নিরাপদ</span>
            </div>

            <div class="error-box mb-3" data-error-box role="alert"></div>

            <form data-auth-form novalidate>
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
                <div class="d-flex gap-2 align-items-center">
                  <input
                    id="password"
                    name="password"
                    type="password"
                    class="form-control"
                    placeholder="••••••••"
                    aria-describedby="passwordError"
                    minlength="6"
                    required
                    data-password
                  />
                  <button
                    type="button"
                    class="password-toggle"
                    data-toggle-password
                    aria-label="পাসওয়ার্ড দেখান"
                  >
                    দেখান
                  </button>
                </div>
                <div
                  class="invalid-feedback d-block"
                  id="passwordError"
                  data-password-error
                ></div>
              </div>

              <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    type="checkbox"
                    id="remember"
                  />
                  <label class="form-check-label" for="remember">
                    আমাকে মনে রাখুন
                  </label>
                </div>
                <a class="text-muted small" href="#">পাসওয়ার্ড ভুলে গেছেন?</a>
              </div>

              <button class="btn btn-primary w-100 mb-3" type="submit">
                ড্যাশবোর্ডে লগইন করুন
              </button>

              <div class="text-center text-muted small">
                নতুন ব্যবহারকারী?
                <a href="/auth/register.php" class="fw-semibold text-decoration-none"
                  >একটি অ্যাকাউন্ট তৈরি করুন</a
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
