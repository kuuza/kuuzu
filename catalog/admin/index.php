<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\Apps;
  use Kuuzu\KU\HTML;
  use Kuuzu\KU\KUUZU;

  require('includes/application_top.php');

  if (KUUZU::hasSitePage()) {
    if (KUUZU::isRPC() === false) {
        $page_file = KUUZU::getSitePageFile();

        if (empty($page_file) || !is_file($page_file)) {
          $page_file = KUUZU::getConfig('dir_root', 'Shop') . 'includes/error_documents/404.php';
        }

        if (KUUZU::useSiteTemplateWithPageFile()) {
          include($kuuTemplate->getFile('template_top.php'));
        }

        include($page_file);

        if (KUUZU::useSiteTemplateWithPageFile()) {
          include($kuuTemplate->getFile('template_bottom.php'));
        }
    }

    goto main_sub3;
  }

  require($kuuTemplate->getFile('template_top.php'));
?>

<h2><i class="fa fa-home"></i> <a href="<?= KUUZU::link(FILENAME_DEFAULT); ?>"><?= STORE_NAME; ?></a></h2>

<?php
  if (defined('MODULE_ADMIN_DASHBOARD_INSTALLED') && tep_not_null(MODULE_ADMIN_DASHBOARD_INSTALLED)) {
    $adm_array = explode(';', MODULE_ADMIN_DASHBOARD_INSTALLED);

    $col = 0;

    foreach ($adm_array as $adm) {
      if (strpos($adm, '\\') !== false) {
        $class = Apps::getModuleClass($adm, 'AdminDashboard');
      } else {
        $class = substr($adm, 0, strrpos($adm, '.'));

        if ( !class_exists($class) ) {
          $KUUZU_Language->loadDefinitions('modules/dashboard/' . pathinfo($adm, PATHINFO_FILENAME));

          include('includes/modules/dashboard/' . $class . '.php');
        }
      }

      $ad = new $class();

      if ($ad->isEnabled()) {
        $col += 1;

        if ($col === 1) {
          echo '<div class="row">';
        }

        echo '<div class="col-md-6">' . $ad->getOutput() . '</div>';

        if ($col === 2) {
          $col = 0;

          echo '</div>';
        }
      }
    }

    if ($col === 1) {
      echo '</div>';
    }
  }

  require($kuuTemplate->getFile('template_bottom.php'));

  main_sub3: // Sites and Apps skip to here

  require('includes/application_bottom.php');
?>
