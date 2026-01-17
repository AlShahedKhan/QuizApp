<?php
$activeTab = $activeTab ?? "";
$tabs = $tabs ?? [
  "play" => ["label" => "কুইজ খেলুন", "href" => "../quiz/play.php"],
  "account" => ["label" => "মাই অ্যাকাউন্ট", "href" => "../user/dashboard.php"],
  "referral" => ["label" => "রেফারেল", "href" => "../referral/referral.php"],
  "leaderboard" => ["label" => "লিডারবোর্ড", "href" => "../quiz/leaderboard.php"],
  "winner" => ["label" => "উইনার", "href" => "../quiz/winner.php"],
];
?>
<ul class="nav nav-pills app-tabs mb-4">
  <?php foreach ($tabs as $key => $tab) { ?>
    <li class="nav-item">
      <a
        class="nav-link pill-tab <?php echo $activeTab === $key ? "active" : ""; ?>"
        href="<?php echo htmlspecialchars($tab["href"], ENT_QUOTES, "UTF-8"); ?>"
      >
        <?php echo htmlspecialchars($tab["label"], ENT_QUOTES, "UTF-8"); ?>
      </a>
    </li>
  <?php } ?>
</ul>
