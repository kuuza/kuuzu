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

  class newsletter {
    var $show_choose_audience, $title, $content, $content_html;

    function __construct($title, $content, $content_html = null) {
      $this->show_choose_audience = false;
      $this->title = $title;
      $this->content = $content;
      $this->content_html = $content_html;
    }

    function choose_audience() {
      return false;
    }

    function confirm() {
      $KUUZU_Db = Registry::get('Db');

      $Qmail = $KUUZU_Db->get('customers', 'count(*) as count', ['customers_newsletter' => '1']);

      $confirm_string = '<table border="0" cellspacing="0" cellpadding="2">' . "\n" .
                        '  <tr>' . "\n" .
                        '    <td class="main"><font color="#ff0000"><strong>' . KUUZU::getDef('text_count_customers', ['count' => $Qmail->valueInt('count')]) . '</strong></font></td>' . "\n" .
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
                        '    <td class="smallText" align="right">' . HTML::button(KUUZU::getDef('image_send'), 'fa fa-envelope', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'] . '&action=confirm_send')) . HTML::button(KUUZU::getDef('image_cancel'), 'fa fa-close', KUUZU::link(FILENAME_NEWSLETTERS, 'page=' . $_GET['page'] . '&nID=' . $_GET['nID'])) . '</td>' . "\n" .
                        '  </tr>' . "\n" .
                        '</table>';

      return $confirm_string;
    }

    function send($newsletter_id) {
      $KUUZU_Db = Registry::get('Db');

      $newsletterEmail = new Mail();
      $newsletterEmail->setFrom(STORE_OWNER_EMAIL_ADDRESS, STORE_OWNER);
      $newsletterEmail->setSubject($this->title);

      if (!empty($this->content)) {
        $newsletterEmail->setBodyPlain($this->content);
      }

      if (!empty($this->content_html)) {
        $newsletterEmail->setBodyHTML($this->content_html);
      }

      $Qmail = $KUUZU_Db->get('customers', [
        'customers_firstname',
        'customers_lastname',
        'customers_email_address'
      ], [
        'customers_newsletter' => '1'
      ]);

      while ($Qmail->fetch()) {
        $newsletterEmail->clearTo();

        $newsletterEmail->addTo($Qmail->value('customers_email_address'), $Qmail->value('customers_firstname') . ' ' . $Qmail->value('customers_lastname'));

        $newsletterEmail->send();
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
