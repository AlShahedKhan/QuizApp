<?php
$activeNav = $activeNav ?? "";
$navItems = [
  "dashboard" => ["ড্যাশবোর্ড", "dashboard.php"],
  "users" => ["ব্যবহারকারী", "users.php"],
  "questions" => ["প্রশ্ন", "questions.php"],
  "transactions" => ["লেনদেন", "transactions.php"],
  "withdrawals" => ["উইথড্র", "withdrawals.php"],
  "report" => ["মাসিক রিপোর্ট", "report.php"],
];
?>
<nav class="admin-nav">
  <div class="text-uppercase small text-muted mb-3">নেভিগেশন</div>
  <div class="nav flex-column gap-1">
    <?php foreach ($navItems as $key => $navItem) { ?>
      <a
        class="nav-link <?php echo $activeNav === $key ? "active" : ""; ?>"
        href="<?php echo htmlspecialchars($navItem[1], ENT_QUOTES, "UTF-8"); ?>"
      >
        <?php echo htmlspecialchars($navItem[0], ENT_QUOTES, "UTF-8"); ?>
      </a>
    <?php } ?>
    <a class="nav-link text-danger" href="/admin/logout.php">লগআউট</a>
  </div>
</nav>
