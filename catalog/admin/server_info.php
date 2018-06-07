<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

use Kuuzu\KU\HTML;
use Kuuzu\KU\HTTP;
use Kuuzu\KU\KUUZU;

require('includes/application_top.php');

$KUUZU_Language->loadDefinitions('server_info');

$info = tep_get_system_information();
$server = parse_url(KUUZU::getConfig('http_server'));

$action = (isset($_GET['action']) ? $_GET['action'] : '');

switch ($action) {
    case 'getPhpInfo':
        phpinfo();
        exit;
        break;

    case 'submit':
        $response = HTTP::getResponse([
            'url' => 'https://kuuzu.org/index.php?RPC&Website&Index&SaveUserServerInfo&v=2',
            'parameters' => [
                'info' => json_encode($info)
            ]
        ]);

        if ($response != 'OK') {
            $KUUZU_MessageStack->add(KUUZU::getDef('error_info_submit'), 'error');
        } else {
            $KUUZU_MessageStack->add(KUUZU::getDef('success_info_submit'), 'success');
        }

        KUUZU::redirect('server_info.php');
        break;

    case 'save':
        $info_file = 'server_info-' . date('YmdHis') . '.txt';

        header('Content-type: text/plain');
        header('Content-disposition: attachment; filename=' . $info_file);

        echo tep_format_system_info_array($info);

        exit;
        break;
}

require($kuuTemplate->getFile('template_top.php'));

if (!isset($_GET['action'])) {
?>

<div class="pull-right">
  <?= HTML::button(KUUZU::getDef('image_export'), 'fa fa-upload', KUUZU::link('server_info.php', 'action=export'), null, 'btn-info'); ?>
  <?= HTML::button(KUUZU::getDef('button_php_info'), 'fa fa-info-circle', KUUZU::link('server_info.php', 'action=getPhpInfo'), ['newwindow' => true], 'btn-info'); ?>
</div>

<?php
}
?>

<h2><i class="fa fa-tasks"></i> <a href="<?= KUUZU::link('server_info.php'); ?>"><?= KUUZU::getDef('heading_title'); ?></a></h2>

<?php
if ($action == 'export') {
?>

<p>
  <?=
    KUUZU::getDef('text_export_intro', [
        'button_submit_to_kuuzu' => KUUZU::getDef('button_submit_to_kuuzu'),
        'button_save' => KUUZU::getDef('image_save')
    ]);
  ?>
</p>

<p>
  <?= HTML::textareaField('server_settings', '100', '15', tep_format_system_info_array($info), 'readonly', false); ?>
</p>

<p>
  <?= HTML::button(KUUZU::getDef('button_submit_to_kuuzu'), 'fa fa-upload', KUUZU::link('server_info.php', 'action=submit'), null, 'btn-info') . '&nbsp;' . HTML::button(KUUZU::getDef('image_save'), 'fa fa-save', KUUZU::link('server_info.php', 'action=save'), null, 'btn-info'); ?>
</p>

<?php
} else {
?>

<table class="table table-hover">
  <tbody>
    <tr>
      <td><strong><?= KUUZU::getDef('title_kuuzu_version'); ?></strong></td>
      <td><?= KUUZU::getVersion(); ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_http_server'); ?></strong></td>
      <td><?= $info['system']['http_server']; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_php_version'); ?></strong></td>
      <td><?= $info['php']['version'] . ' (' . KUUZU::getDef('title_zend_version') . ' ' . $info['php']['zend'] . ')'; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_server_host'); ?></strong></td>
      <td><?= $server['host'] . ' (' . gethostbyname($server['host']) . ')'; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_server_os'); ?></strong></td>
      <td><?= $info['system']['os'] . ' ' . $info['system']['kernel']; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_server_date'); ?></strong></td>
      <td><?= $info['system']['date']; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_server_up_time'); ?></strong></td>
      <td><?= $info['system']['uptime']; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_database_host'); ?></strong></td>
      <td><?= KUUZU::getConfig('db_server') . ' (' . gethostbyname(KUUZU::getConfig('db_server')) . ')'; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_database'); ?></strong></td>
      <td><?= 'MySQL ' . $info['mysql']['version']; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_database_date'); ?></strong></td>
      <td><?= $info['mysql']['date']; ?></td>
    </tr>
    <tr>
      <td><strong><?= KUUZU::getDef('title_database_name'); ?></strong></td>
      <td><?= KUUZU::getConfig('db_database'); ?></td>
    </tr>
  </tbody>
</table>

<?php
}

require($kuuTemplate->getFile('template_bottom.php'));
require('includes/application_bottom.php');
?>
