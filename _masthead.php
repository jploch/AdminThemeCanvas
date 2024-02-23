<?php namespace ProcessWire;
// define var $lang for use in tpl files
$pageEditLink = 'Edit';
$pageSettingsLink = 'Settings';
$pageViewLink = 'View';

if (isset($user->language->title)) {
if($user->language->title == 'Deutsch') {
$pageEditLink = 'Bearbeiten';
$pageSettingsLink = 'Einstellungen';
$pageViewLink = 'Ansehen';
}
  }


if(!defined("PROCESSWIRE")) die();

/** @var AdminThemeUikit $adminTheme */
/** @var User $user */
/** @var array $extras */
/** @var Paths $urls */
/** @var Config $config */
/** @var Notices $notices */

?>
<div id='pw-mastheads'>
  <header id='pw-masthead-mobile' class='pw-masthead uk-hidden uk-background-muted'>
    <div class='pw-container uk-container uk-container-expand<?php if(!$adminTheme->isLoggedIn) echo ' uk-text-center'; ?>'>
      <a href='<?php echo $adminTheme->isLoggedIn ? $config->urls->admin : $config->urls->root; ?>' class='pw-logo-link'>
        <?php echo $adminTheme->getLogo(); ?>
      </a>
    </div>
  </header>
  <header id='pw-masthead' class='pw-masthead uk-background-muted' data-pw-height='50'>
    <div class='pw-container uk-container uk-container-expand'>
      <nav class='uk-navbar uk-navbar-container uk-navbar-transparent' uk-navbar>
        <div class='uk-navbar-left'>

          <ul class='pw-tree-nav uk-navbar-nav'>
            <?php echo $adminTheme->renderPrimaryNavItems('first'); ?>
          </ul>

          <?php if($adminTheme->isLoggedIn): ?>
          <?php if ($page->process == 'ProcessPageEdit'): ?>
          <ul class='uk-navbar-nav pw-page-nav'>

            <li class="ProcessPageEditSettings">
              <a href="#" class="pw-dropdown-disabled">
                <i class="fa fa-gear pw-nav-icon"></i>
                <span>
                  <?php echo $pageSettingsLink; ?></span>
              </a>
            </li>
            <li class="ProcessPageEditView">
              <a href="#">
                <i class="fa fa-eye pw-nav-icon fa-fw">
                </i>
                <span>
                  <?php echo $pageViewLink; ?></span>
              </a>
            </li>
          </ul>
          <?php endif; ?>
          <?php endif; ?>
        </div>
        <?php if($adminTheme->isLoggedIn): ?>
        <div class="uk-navbar-right">
          <ul class='uk-navbar-nav pw-primary-nav'>
            <?php echo $adminTheme->renderPrimaryNavItems('notFirst'); ?>
          </ul>

          <?php include(__DIR__ . '/_search-form.php'); ?>

          <ul class='uk-navbar-nav pw-user-nav'>
            <li>
              <a id="tools-toggle" class="pw-dropdown-toggle" href="<?php echo $urls->admin; ?>profile/">
                <?php echo $adminTheme->renderUserNavLabel(); ?>
              </a>
              <ul class="pw-dropdown-menu user-dropdown" data-my="right top" data-at="right bottom" style="display: none;">
                <?php if($config->debug && $adminTheme->isSuperuser && strpos($adminTheme->layout, 'sidenav') === false): ?>
                <li>
                  <a href='#' onclick="$('#debug_toggle').click(); return false;">
                    <?php echo $adminTheme->renderNavIcon('bug') . __('Debug', __FILE__); ?>
                  </a>
                </li>
                <?php  endif; ?>
                <?php echo $adminTheme->renderUserNavItems(); ?>
              </ul>
            </li>
          </ul>

        </div>
        <?php endif; // loggedin ?>
      </nav>
    </div>
  </header>
  <?php 
	if(strpos($adminTheme->layout, 'sidenav') === false) {
		echo $adminTheme->renderNotices($notices);
		echo $adminTheme->renderExtraMarkup('masthead');
	}
	?>
</div>
