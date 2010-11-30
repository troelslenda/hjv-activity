<?php
// $Id: node.tpl.php,v 1.5 2007/10/11 09:51:29 goba Exp $
?>
<div id="node-<?php print $node->nid; ?>" class="node<?php if ($sticky) { print ' sticky'; } ?><?php if (!$status) { print ' node-unpublished'; } ?>">
<div class="smallbox floatright">
  <h2>Opdatering:</h2>
  <div class="badge lastupdate_act" title="<?php print t('Aktivitetsinformation blev opdateret for !time siden',array('!time' => $lastupdate_activity ));?>"><?php print $lastupdate_activity; ?></div>
  <div class="badge lastupdate_part" title="<?php print t('Deltagerinformation blev opdateret for !time siden',array('!time' => $lastupdate_participant ));?>"><?php print $lastupdate_participant; ?></div>
  <div class="updatelink"><?php print $updatelink?></div>
</div>
<?php if($signuplink): ?>
  <div class="signup-via-hjv.dk"><?php print $signuplink; ?></div>
<?php endif;?>


<?php print $picture ?>

<?php if ($page == 0): ?>
  <h2><a href="<?php print $node_url ?>" title="<?php print $title ?>"><?php print $title ?></a></h2>
<?php endif; ?>

  <!--<?php if ($submitted): ?>
    <span class="submitted"><?php print $submitted; ?></span>
  <?php endif; ?>-->

  <div class="content clear-block">
    <?php print $content ?>
    <div class="members clear-block"><?php print $members;?></div>
  </div>

  <div class="clear-block">
    <div class="meta">
    <?php if ($taxonomy): ?>
      <div class="terms"><?php print $terms ?></div>
    <?php endif;?>
    </div>

    <?php if ($links): ?>
      <div class="links"><?php print $links; ?></div>
    <?php endif; ?>
  </div>
  
  <h2>Sidste opdatering af aktiviteten</h2>
  <div>Aktivitet: <?php print $lastupdate_activity?></div>
  <div>Deltagere: <?php print $lastupdate_participant?></div>

</div>
