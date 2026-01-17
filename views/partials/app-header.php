<?php
$pageTag = $pageTag ?? "";
$pageMeta = $pageMeta ?? "";
?>
<header class="page-header">
  <div class="brand-mark">
    <span class="brand-dot"></span>
    <span>QuizTap</span>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <?php if ($pageTag !== "") { ?>
      <span class="tag"><?php echo htmlspecialchars($pageTag, ENT_QUOTES, "UTF-8"); ?></span>
    <?php } ?>
    <?php if ($pageMeta !== "") { ?>
      <span class="meta-chip"><?php echo htmlspecialchars($pageMeta, ENT_QUOTES, "UTF-8"); ?></span>
    <?php } ?>
  </div>
</header>
