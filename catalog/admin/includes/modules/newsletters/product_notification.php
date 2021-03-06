<?php
/**
  * Kuuzu Cart
  *
  * REPLACE_WITH_COPYRIGHT_TEXT
  * REPLACE_WITH_LICENSE_TEXT
  */

  use Kuuzu\KU\HTML;
  use Kuuzu\KU\Mail;
  use Kuuzu\KU\KUUZU;
  use Kuuzu\KU\Registry;

  class product_notification {
    var $show_choose_audience, $title, $content, $content_html;

    function __construct($title, $content, $content_html = null) {
      $this->show_choose_audience = true;
      $this->title = $title;
      $this->content = $content;
      $this->content_html = $content_html;
    }

    function choose_audience() {
      $KUUZU_Db = Registry::get('Db');
      $KUUZU_Language = Registry::get('Language');

      $products_array = [];

      $Qproducts = $KUUZU_Db->get([
        'products p',
        'products_description pd'
      ], [
        'pd.products_id',
        'pd.products_name'
      ], [
        'pd.language_id' => $KUUZU_Language->getId(),
        'pd.products_id' => [
          'rel' => 'p.products_id'
        ],
        'p.products_status' => '1'
      ], 'pd.products_name');

      while ($Qproducts->fetch()) {
        $products_array[] = [
          'id' => $Qproducts->valueInt('products_id'),
          'text' => $Qproducts->value('products_name')
        ];
      }

$choose_audience_string = '<script type="text/javascript"><!--
function mover(move) {
  if (move == \'remove\') {
    for (x=0; x<(document.notifications.products.length); x++) {
      if (document.notifications.products.options[x].selected) {
        with(document.notifications.elements[\'chosen[]\']) {
          options[options.length] = new Option(document.notifications.products.options[x].text,document.notifications.products.options[x].value);
        }
        document.notifications.products.options[x] = null;
        x = -1;
      }
    }
  }
  if (move == \'add\') {
    for (x=0; x<(document.notifications.elements[\'chosen[]\'].length); x++) {
      if (document.notifications.elements[\'chosen[]\'].options[x].selected) {
        with(document.notifications.products) {
          options[options.length] = new Option(document.notifications.elements[\'chosen[]\'].options[x].text,document.notifications.elements[\'chosen[]\'].options[x].value);
        }
        document.notifications.elements[\'chosen[]\'].options[x] = null;
        x = -1;
      }
    }
  }
  return true;
}

function selectAll(FormName, SelectBox) {
  temp = "document." + FormName + ".elements[\'" + SelectBox + "\']";
  Source = eval(temp);

  for (x=0; x<(Source.length); x++) {
    Source.options[x].selected = "true";
  }

  if (x<1) {
    alert(\'' . KUUZU::getDef('js_please_select_products') . '\');
    return false;
  } else {
    return true;
  }
}
//--></script>';

      $global_button = HTML::button(KUUZU::getDef('button_global'), 'fa fa-globe', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm&global=true'));

      $cancel_button = HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID']));

      $choose_audience_string .= '<form name="notifications" action="' . KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm') . '" method="post" onsubmit="return selectAll(\'notifications\', \'chosen[]\')"><table border="0" width="100%" cellspacing="0" cellpadding="2">' . "\n" .
                                 '  <tr>' . "\n" .
                                 '    <td align="center" class="smallText"><strong>' . KUUZU::getDef('text_products') . '</strong><br />' . HTML::selectField('products', $products_array, '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '    <td align="center" class="smallText">&nbsp;<br />' . $global_button . '<br /><br /><br /><input type="button" value="' . KUUZU::getDef('button_select') . '" style="width: 8em;" onClick="mover(\'remove\');"><br /><br /><input type="button" value="' . KUUZU::getDef('button_unselect') . '" style="width: 8em;" onClick="mover(\'add\');"><br /><br /><br />' . HTML::button(KUUZU::getDef('image_send'), 'fa fa-envelope') . '<br /><br />' . $cancel_button . '</td>' . "\n" .
                                 '    <td align="center" class="smallText"><strong>' . KUUZU::getDef('text_selected_products') . '</strong><br />' . HTML::selectField('chosen[]', array(), '', 'size="20" style="width: 20em;" multiple') . '</td>' . "\n" .
                                 '  </tr>' . "\n" .
                                 '</table></form>';

      return $choose_audience_string;
    }

    function confirm() {
      $KUUZU_Db = Registry::get('Db');

      $audience = array();

      if (isset($_GET['global']) && ($_GET['global'] == 'true')) {
        $Qproducts = $KUUZU_Db->get('products_notifications', 'distinct customers_id');

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = '1';
        }

        $Qcustomers = $KUUZU_Db->get('customers_info', 'customers_info_id', ['global_product_notifications' => '1']);

        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_info_id')] = '1';
        }
      } else {
        $chosen = [];

        foreach ($_POST['chosen'] as $id) {
          if (is_numeric($id) && !in_array($id, $chosen)) {
            $chosen[] = $id;
          }
        }

        $ids = array_map(function($k) {
          return ':products_id_' . $k;
        }, array_keys($chosen));

        $Qproducts = $KUUZU_Db->prepare('select distinct customers_id from :table_products_notifications where products_id in (' . implode(', ', $ids) . ')');

        foreach ($chosen as $k => $v) {
          $Qproducts->bindInt(':products_id_' . $k, $v);
        }

        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = '1';
        }

        $Qcustomers = $KUUZU_Db->get('customers_info', 'customers_info_id', ['global_product_notifications' => '1']);

        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_info_id')] = '1';
        }
      }

      $confirm_string =  HTML::form('confirm', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm_send')) . "\n" .
                        '<table border="0" cellspacing="0" cellpadding="2">' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><font color="#ff0000"><strong>' . KUUZU::getDef('text_count_customers', ['audience' => sizeof($audience)]) . '</strong></font></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>&nbsp;</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><strong>' . $this->title . '</strong></td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>&nbsp;</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main">' . "\n" .
                        '      <ul class="nav nav-tabs" role="tablist">' . "\n" .
                        '        <li role="presentation" class="active"><a href="#html_preview" aria-controls="html_preview" role="tab" data-toggle="tab">' . KUUZU::getDef('email_type_html') . '</a></li>' . "\n" .
                        '        <li role="presentation"><a href="#plain_preview" aria-controls="plain_preview" role="tab" data-toggle="tab">' . KUUZU::getDef('email_type_plain') . '</a></li>' . "\n" .
                        '      </ul>' . "\n" .
                        '      <div class="tab-content">' . "\n" .
                        '        <div role="tabpanel" class="tab-pane active" id="html_preview">' . "\n" .
                        '          <iframe id="emailHtmlPreviewContent" style="width: 100%; height: 400px; border: 0;"></iframe>' . "\n" .
                        '          <script id="emailHtmlPreview" type="x-tmpl-mustache">' . "\n" .
                        '            ' . HTML::outputProtected($this->content_html) . "\n" .
                        '          </script>' . "\n" .
                        '          <script>' . "\n" .
                        '            $(function() {' . "\n" .
                        '              var content = $(\'<div />\').html($(\'#emailHtmlPreview\').html()).text();' . "\n" .
                        '              $(\'#emailHtmlPreviewContent\').contents().find(\'html\').html(content);' . "\n" .
                        '            });' . "\n" .
                        '          </script>' . "\n" .
                        '        </div>' . "\n" .
                        '        <div role="tabpanel" class="tab-pane" id="plain_preview">' . "\n" .
                        '          ' . nl2br(HTML::outputProtected($this->content)) . "\n" .
                        '        </div>' . "\n" .
                        '      </div>' . "\n" .
                        '    </td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td>&nbsp;</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="smallText" align="right">';
      if (sizeof($audience) > 0) {
        if (isset($_GET['global']) && ($_GET['global'] == 'true')) {
          $confirm_string .= HTML::hiddenField('global', 'true');
        } else {
          for ($i = 0, $n = sizeof($chosen); $i < $n; $i++) {
            $confirm_string .= HTML::hiddenField('chosen[]', $chosen[$i]);
          }
        }
        $confirm_string .= HTML::button(KUUZU::getDef('image_send'), 'fa fa-envelope');
      }
      $confirm_string .= HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=send')) . '</td>' . "\n" .
                         '  </tr>' . "\n" .
                         '</table>' . "\n" .
                         '</form>';

      return $confirm_string;
    }

    function send($newsletter_id) {
      $KUUZU_Db = Registry::get('Db');

      $audience = array();

      if (isset($_POST['global']) && ($_POST['global'] == 'true')) {
        $Qproducts = $KUUZU_Db->get([
          'customers c',
          'products_notifications pn'
        ], [
          'distinct pn.customers_id',
          'c.customers_firstname',
          'c.customers_lastname',
          'c.customers_email_address'
        ], [
          'c.customers_id' => [
            'rel' => 'pn.customers_id'
          ]
        ]);

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = [
            'firstname' => $Qproducts->value('customers_firstname'),
            'lastname' => $Qproducts->value('customers_lastname'),
            'email_address' => $Qproducts->value('customers_email_address')
          ];
        }

        $Qcustomers = $KUUZU_Db->get([
          'customers c',
          'customers_info ci'
        ], [
          'c.customers_id',
          'c.customers_firstname',
          'c.customers_lastname',
          'c.customers_email_address'
        ], [
          'c.customers_id' => [
            'rel' => 'ci.customers_info_id'
          ],
          'ci.global_product_notifications' => '1'
        ]);

        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_id')] = [
            'firstname' => $Qcustomers->value('customers_firstname'),
            'lastname' => $Qcustomers->value('customers_lastname'),
            'email_address' => $Qcustomers->value('customers_email_address')
          ];
        }
      } else {
        $chosen = [];

        foreach ($_POST['chosen'] as $id) {
          if (is_numeric($id) && !in_array($id, $chosen)) {
            $chosen[] = $id;
          }
        }

        $ids = array_map(function($k) {
          return ':products_id_' . $k;
        }, array_keys($chosen));

        $Qproducts = $KUUZU_Db->prepare('select distinct pn.customers_id, c.customers_firstname, c.customers_lastname, c.customers_email_address from :table_customers c, :table_products_notifications pn where c.customers_id = pn.customers_id and pn.products_id in (' . implode(', ', $ids) . ')');

        foreach ($chosen as $k => $v) {
          $Qproducts->bindInt(':products_id_' . $k, $v);
        }

        $Qproducts->execute();

        while ($Qproducts->fetch()) {
          $audience[$Qproducts->valueInt('customers_id')] = [
            'firstname' => $Qproducts->value('customers_firstname'),
            'lastname' => $Qproducts->value('customers_lastname'),
            'email_address' => $Qproducts->value('customers_email_address')
          ];
        }

        $Qcustomers = $KUUZU_Db->get([
          'customers c',
          'customers_info ci'
        ], [
          'c.customers_id',
          'c.customers_firstname',
          'c.customers_lastname',
          'c.customers_email_address'
        ], [
          'c.customers_id' => [
            'rel' => 'ci.customers_info_id'
          ],
          'ci.global_product_notifications' => '1'
        ]);

        while ($Qcustomers->fetch()) {
          $audience[$Qcustomers->valueInt('customers_id')] = [
            'firstname' => $Qcustomers->value('customers_firstname'),
            'lastname' => $Qcustomers->value('customers_lastname'),
            'email_address' => $Qcustomers->value('customers_email_address')
          ];
        }
      }

      $notificationEmail = new Mail();
      $notificationEmail->setFrom(STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER);
      $notificationEmail->setSubject($this->title);

      if (!empty($this->content)) {
        $notificationEmail->setBodyPlain($this->content);
      }

      if (!empty($this->content_html)) {
        $notificationEmail->setBodyHTML($this->content_html);
      }

      foreach ( $audience as $key => $value ) {
        $notificationEmail->clearTo();

        $notificationEmail->addTo($value['email_address'], $value['firstname'] . ' ' . $value['lastname']);

        $notificationEmail->send();
      }

      $KUUZU_Db->save('newsletters', [
        'date_sent' => 'now()',
        'status' => '1'
      ], [
        'newsletters_id' => (int)$newsletter_id
      ]);
    }
  }
?>
