<!DOCTYPE html>
<html lang="bn">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>QuizTap - Bangla Quiz Rewards</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="/assets/css/home.css" />
  </head>
  <body>
    <header class="site-header sticky-top">
      <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
          <a class="navbar-brand brand-mark" href="/">
            <span class="brand-dot"></span>
            <span>QuizTap</span>
          </a>
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#siteNav"
            aria-controls="siteNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="siteNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">
              <li class="nav-item">
                <a class="nav-link" href="#how">কিভাবে কাজ করে</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#packages">প্যাকেজ</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#winners">উইনার</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#referral">রেফারেল</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="/auth/login.php">লগইন</a>
              </li>
              <li class="nav-item">
                <a class="btn btn-primary btn-sm" href="/auth/register.php"
                  >সাইনআপ</a
                >
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

    <main>
      <section class="hero-section">
        <div class="container">
          <div class="row align-items-center g-4">
            <div class="col-lg-6 reveal">
              <div class="eyebrow">বাংলার প্রথম রিওয়ার্ডেড কুইজ</div>
              <h1 class="display-title mt-3">
                খেলুন কুইজ, জিতুন আকর্ষণীয় পুরস্কার
              </h1>
              <p class="lead text-muted">
                প্রতি মাসে নতুন কুইজ খেলুন, পয়েন্ট অর্জন করুন এবং জিতে নিন ক্যাশ
                রিওয়ার্ড। সবকিছু একদম স্বচ্ছ লেজারে ট্র্যাক করা যায়।
              </p>
              <div class="d-flex flex-wrap gap-3 mt-4">
                <a class="btn btn-primary btn-lg" href="/auth/register.php"
                  >এখনই শুরু করুন</a
                >
                <a class="btn btn-outline-dark btn-lg" href="#how"
                  >কিভাবে কাজ করে</a
                >
              </div>
              <div class="hero-badges mt-4">
                <span class="badge-pill">100 TK বোনাস ক্রেডিট!</span>
                <span class="badge-pill">মাসিক লিডারবোর্ড</span>
                <span class="badge-pill">রেফারেল ইনকাম</span>
              </div>
            </div>
            <div class="col-lg-6 reveal delay-1">
              <div class="hero-card">
                <div class="hero-card-top">
                  <div>
                    <div class="small text-uppercase text-muted">
                      Active players
                    </div>
                    <div class="hero-metric">4,821</div>
                  </div>
                  <div class="text-end">
                    <div class="small text-uppercase text-muted">Top score</div>
                    <div class="hero-metric">980</div>
                  </div>
                </div>
                <div class="hero-card-body">
                  <h3 class="mb-2">Bangla-first quiz arena</h3>
                  <p class="text-muted">
                    মাসের প্রথম দিন স্বয়ংক্রিয়ভাবে লিডারবোর্ড রিসেট হয়। আগের
                    মাসের বিজয়ীরা আলাদা করে দেখা যাবে।
                  </p>
                  <div class="hero-progress">
                    <div>
                      <span>এই মাসের লিডারবোর্ড</span>
                      <strong>84% পূরণ</strong>
                    </div>
                    <div class="progress">
                      <div class="progress-bar" style="width: 84%"></div>
                    </div>
                  </div>
                </div>
                <div class="hero-card-glow"></div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="features-section">
        <div class="container">
          <div class="section-title reveal">
            <h2>কেন QuizTap বেছে নেবেন?</h2>
            <p class="text-muted">
              রিওয়ার্ড, কুইজিং ও রেফারেল একসাথে একটি সুন্দর অভিজ্ঞতা।
            </p>
          </div>
          <div class="row g-4">
            <div class="col-md-6 col-lg-3 reveal delay-1">
              <div class="feature-card">
                <div class="icon-pill">
                  <i class="bi bi-gift"></i>
                </div>
                <h5>Signup Bonus</h5>
                <p>রেজিস্ট্রেশন করলেই 100 টাকার ক্রেডিট বোনাস!</p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal delay-2">
              <div class="feature-card">
                <div class="icon-pill">
                  <i class="bi bi-lightning-charge"></i>
                </div>
                <h5>Monthly Quiz</h5>
                <p>প্রতি মাসে নতুন কুইজ ও নতুন লিডারবোর্ড।</p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal delay-3">
              <div class="feature-card">
                <div class="icon-pill">
                  <i class="bi bi-cash-stack"></i>
                </div>
                <h5>Cash Rewards</h5>
                <p>সর্বোচ্চ 30,000 টাকা পর্যন্ত পুরস্কার।</p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal delay-4">
              <div class="feature-card">
                <div class="icon-pill">
                  <i class="bi bi-people"></i>
                </div>
                <h5>Referral Income</h5>
                <p>বন্ধু আনুন, 50 টাকা ক্রেডিট রিওয়ার্ড পান!</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="steps-section" id="how">
        <div class="container">
          <div class="section-title reveal">
            <h2>কিভাবে কাজ করে</h2>
            <p class="text-muted">
              চারটি সহজ ধাপে আপনার পুরস্কার নিশ্চিত করুন।
            </p>
          </div>
          <div class="row g-4">
            <div class="col-md-6 col-lg-3 reveal delay-1">
              <div class="step-card">
                <div class="step-number">1</div>
                <h5>সাইনআপ করুন</h5>
                <p>মোবাইল নম্বর দিয়ে দ্রুত সাইনআপ করুন।</p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal delay-2">
              <div class="step-card">
                <div class="step-number">2</div>
                <h5>বোনাস ব্যবহার করুন</h5>
                <p>বোনাস ও ক্রেডিট দিয়ে কুইজ খেলুন।</p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal delay-3">
              <div class="step-card">
                <div class="step-number">3</div>
                <h5>পয়েন্ট অর্জন করুন</h5>
                <p>সঠিক উত্তর দিয়ে পয়েন্ট বাড়ান।</p>
              </div>
            </div>
            <div class="col-md-6 col-lg-3 reveal delay-4">
              <div class="step-card">
                <div class="step-number">4</div>
                <h5>পুরস্কার জিতুন</h5>
                <p>টপ 5 এ থাকুন, রিওয়ার্ড নিন।</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="packages-section" id="packages">
        <div class="container">
          <div class="section-title reveal">
            <h2>ক্রেডিট প্যাকেজ</h2>
            <p class="text-muted">1 টাকা = 1 ক্রেডিট। প্রশ্নে খেলতে ব্যবহার হয়।</p>
          </div>
          <div class="row g-4 align-items-stretch">
            <div class="col-md-6 col-lg-4 reveal delay-1">
              <div class="price-card">
                <div class="price-header">
                  <h5>Starter Pack</h5>
                  <span class="price">50 TK</span>
                </div>
                <p class="text-muted">শুরু করার জন্য সেরা প্যাকেজ।</p>
                <ul class="list-unstyled">
                  <li>50 Credits</li>
                  <li>নতুন ইউজারদের জন্য</li>
                  <li>প্রতি প্রশ্নে 1 ক্রেডিট</li>
                </ul>
                <a class="btn btn-outline-dark w-100" href="/auth/login.php"
                  >Buy Credits</a
                >
              </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal delay-2">
              <div class="price-card highlight">
                <div class="popular-badge">Popular</div>
                <div class="price-header">
                  <h5>Popular Pack</h5>
                  <span class="price">200 TK</span>
                </div>
                <p class="text-muted">সবচেয়ে বেশি খেলা ইউজারদের পছন্দ।</p>
                <ul class="list-unstyled">
                  <li>200 Credits</li>
                  <li>স্ট্রিক বাড়ানোর জন্য</li>
                  <li>লিডারবোর্ডে দ্রুত উঠুন</li>
                </ul>
                <a class="btn btn-primary w-100" href="/auth/login.php"
                  >Buy Credits</a
                >
              </div>
            </div>
            <div class="col-md-6 col-lg-4 reveal delay-3">
              <div class="price-card">
                <div class="price-header">
                  <h5>Pro Pack</h5>
                  <span class="price">500 TK</span>
                </div>
                <p class="text-muted">ভারি ইউজারদের জন্য সর্বোচ্চ অ্যাকসেস।</p>
                <ul class="list-unstyled">
                  <li>500 Credits</li>
                  <li>প্রিমিয়াম রিওয়ার্ড ফোকাস</li>
                  <li>দ্রুত পয়েন্ট স্কেল</li>
                </ul>
                <a class="btn btn-outline-dark w-100" href="/auth/login.php"
                  >Buy Credits</a
                >
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="referral-section" id="referral">
        <div class="container">
          <div class="row g-4 align-items-center">
            <div class="col-lg-6 reveal">
              <div class="section-title text-start">
                <h2>রেফারেল ইনকাম সহজ করুন</h2>
                <p class="text-muted">
                  আপনার রেফারেল লিঙ্ক ব্যবহার করে কেউ বোনাস শেষ করে ক্রেডিট কিনলে
                  আপনি পাবেন 50 টাকার ক্রেডিট রেফারেল রিওয়ার্ড।
                </p>
              </div>
              <div class="referral-flow">
                <div>
                  <span>১. বন্ধু সাইনআপ করে</span>
                  <strong>বোনাস শেষ করে</strong>
                </div>
                <div>
                  <span>২. প্রথম পেমেন্ট</span>
                  <strong>কমপক্ষে 50 টাকার ক্রেডিট!</strong>
                </div>
                <div>
                  <span>৩. আপনি পান</span>
                  <strong>50 টাকার ক্রেডিট রিওয়ার্ড!</strong>
                </div>
              </div>
            </div>
            <div class="col-lg-6 reveal delay-1">
              <div class="referral-card">
                <div class="referral-header">
                  <div>
                    <h4 class="mb-1">Withdraw Methods</h4>
                    <p class="text-muted mb-0">বিকাশ বা নগদে উত্তোলন করুন।</p>
                  </div>
                  <i class="bi bi-wallet2"></i>
                </div>
                <div class="referral-list">
                  <div class="ref-item">
                    <span>bKash</span>
                    <span>Instant</span>
                  </div>
                  <div class="ref-item">
                    <span>Nogod</span>
                    <span>Same Day</span>
                  </div>
                </div>
                <a class="btn btn-outline-dark w-100" href="/auth/register.php"
                  >রেফারেল লিঙ্ক তৈরি করুন</a
                >
              </div>
            </div>
          </div>
        </div>
      </section>

      <section class="winner-section" id="winners">
        <div class="container">
          <div class="section-title reveal">
            <h2>গত মাসের উইনার</h2>
            <p class="text-muted">
              বিশ্বাস করুন, রিওয়ার্ড আসল। লিডারবোর্ডে উঠলে পুরস্কার নিশ্চিত।
            </p>
          </div>
          <div class="winner-card reveal delay-1">
            <div class="winner-badge">
              <i class="bi bi-trophy"></i>
            </div>
            <div>
              <h4>Farzana Ahmed</h4>
              <p class="text-muted mb-0">Score: 1,220 | Prize: 18,000 TK</p>
            </div>
            <div class="winner-month">December 2025</div>
          </div>
        </div>
      </section>

      <section class="cta-section">
        <div class="container">
          <div class="cta-card reveal">
            <div>
              <h2>আজই শুরু করুন, আপনার জয়ের গল্প লিখুন</h2>
              <p class="text-muted">
                মাত্র কয়েক মিনিটেই আপনার কুইজ অ্যাকাউন্ট চালু করুন।
              </p>
            </div>
            <div class="d-flex flex-wrap gap-3">
              <a class="btn btn-light btn-lg" href="/auth/register.php"
                >Signup Now</a
              >
              <a class="btn btn-outline-light btn-lg" href="/auth/login.php"
                >Login</a
              >
            </div>
          </div>
        </div>
      </section>
    </main>

    <footer class="site-footer">
      <div class="container">
        <div class="row g-4">
          <div class="col-md-6">
            <div class="brand-mark mb-2">
              <span class="brand-dot"></span>
              <span>QuizTap</span>
            </div>
            <p class="text-muted">
              Bangla-first quiz platform with rewards, referrals, and monthly
              winners.
            </p>
          </div>
          <div class="col-md-6">
            <div class="footer-links">
              <a href="#">About</a>
              <a href="#">Terms &amp; Conditions</a>
              <a href="#">Privacy Policy</a>
              <a href="#">Contact</a>
            </div>
          </div>
        </div>
        <div class="footer-bottom">
          <span>© 2026 QuizTap. All rights reserved.</span>
        </div>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/home.js"></script>
  </body>
</html>
