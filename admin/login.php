<?php
$pageTitle = "QuizTap অ্যাডমিন - লগইন";
require __DIR__ . "/../views/partials/admin-head.php";
?>
<div class="row justify-content-center">
  <div class="col-lg-6 col-xl-5">
    <section class="glass-card p-4 reveal">
      <div class="d-flex align-items-center gap-2 mb-3">
        <span class="brand-dot"></span>
        <div class="brand-mark">QuizTap অ্যাডমিন</div>
      </div>
      <h2 class="mb-2">লগইন</h2>
      <p class="text-muted mb-4">
        ব্যবহারকারী, কুইজ, এবং পেআউট পরিচালনায় অ্যাডমিন ক্রেডেনশিয়াল ব্যবহার করুন।
      </p>
      <form>
        <div class="mb-3">
          <label class="form-label" for="adminId">অ্যাডমিন আইডি</label>
          <input
            id="adminId"
            type="text"
            class="form-control"
            placeholder="admin@quiztap"
          />
        </div>
        <div class="mb-3">
          <label class="form-label" for="password">পাসওয়ার্ড</label>
          <input
            id="password"
            type="password"
            class="form-control"
            placeholder="পাসওয়ার্ড লিখুন"
          />
        </div>
        <div class="d-flex align-items-center justify-content-between mb-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="remember" />
            <label class="form-check-label" for="remember">
              লগইন রাখা
            </label>
          </div>
          <a class="text-muted small" href="#">পাসওয়ার্ড ভুলে গেছেন?</a>
        </div>
        <button class="btn btn-primary w-100" type="submit">
          লগইন
        </button>
      </form>
    </section>
  </div>
</div>
<?php require __DIR__ . "/../views/partials/admin-foot.php"; ?>
