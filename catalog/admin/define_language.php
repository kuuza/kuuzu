<?php
/**
 * Kuuzu Cart
 *
 * REPLACE_WITH_COPYRIGHT_TEXT
 * REPLACE_WITH_LICENSE_TEXT
 */

    use Kuuzu\KU\Cache;
    use Kuuzu\KU\DateTime;
    use Kuuzu\KU\HTML;
    use Kuuzu\KU\KUUZU;

    require('includes/application_top.php');

    $action = (isset($_GET['action']) ? $_GET['action'] : '');

    if (tep_not_null($action)) {
        switch ($action) {
            case 'save':
        
            if (isset($_GET['content_group'])) $content_group = $_GET['content_group'];
            if (isset($_POST['definition_key'])) $definition_key = $_POST['definition_key'];
            if (isset($_POST['definition_value'])) $definition_value = $_POST['definition_value'];

            foreach ($definition_value as $id => $definition) {
                $sql_data_array = array(
                    'definition_value' => $definition
                );
                $KUUZU_Db->save('languages_definitions', $sql_data_array, ['id' => (int)$id]);
            }
            
            $group = str_replace("-", "/", $content_group) . '.txt';
            $file = KUUZU::getConfig('dir_root', 'Shop') . 'includes/languages/' . $KUUZU_Language->get('directory', $KUUZU_Language->get('code')) . '/' . $group;
            unlink($file);
            
            $languages_definitions_array = array_combine ($definition_key, $definition_value);
            foreach ($languages_definitions_array as $defKey => $defVal) {
              $data = $defKey . ' = ' . $defVal;
              file_put_contents($file, $data . PHP_EOL, FILE_APPEND | LOCK_EX);  
            }
            
            Cache::clear('languages-defs-' . $content_group . '-lang' . $KUUZU_Language->getId());
            
            KUUZU::redirect('define_language.php', 'content_group=' . $content_group . '&action=edit');
        }
    }
      $languages = tep_get_languages();

    require($kuuTemplate->getFile('template_top.php'));
    
    $heading_title = isset($_GET['content_group']) ? KUUZU::getDef('heading_title_2', ['content_group' => $_GET['content_group']]) : KUUZU::getDef('heading_title');
    ?>

    <h2><i class="fa fa-language"></i> <a href="<?= KUUZU::link('define_language.php'); ?>"><?= $heading_title; ?></a></h2>

    <?php

    if (isset($_GET['content_group'])) {
        echo HTML::form('define_language', KUUZU::link('define_language.php', 'content_group=' . $_GET['content_group'] . '&action=save', 'post', 'enctype="multipart/form-data"'));
     
     ?>   
        <ul class="nav nav-tabs">
            <?php
                for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                    echo '<li ' . ($i === 0 ? 'class="active"' : '') . '><a data-target="#section_general_content_' . $languages[$i]['directory'] . '" data-toggle="tab">' . $KUUZU_Language->getImage($languages[$i]['code']) . '&nbsp;' . $languages[$i]['name'] . '</a></li>';
                }
            ?>
        </ul>
        <div class="tab-content">
<?php

        for ($i=0, $n=sizeof($languages); $i<$n; $i++) {
                                       
            
?>
            <div id="section_general_content_<?php echo $languages[$i]['directory']; ?>" class="tab-pane <?= ($i === 0 ? 'active' : ''); ?>">
                <div class="panel panel-info kuuzu-panel">
                    <div class="panel-body">
                        <div class="container-fluid">
                            <div class="row">

                                <table class="kuuzu-table table table-bordered table-hover">
                                    
                                    <thead>
                                        <tr class="info">
                                            <th class="col-md-4"><?= KUUZU::getDef('table_heading_definition_key'); ?></th>
                                            <th class="col-md-8"><?= KUUZU::getDef('table_heading_definition_value'); ?></th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                        <?php
                                            $Qdefinitions = $KUUZU_Db->prepare('select id, definition_key, definition_value from :table_languages_definitions where content_group = :content_group and languages_id = :languages_id ');
                                            $Qdefinitions->bindValue(':content_group', $_GET['content_group']);
                                            $Qdefinitions->bindInt(':languages_id', $languages[$i]['id']);
                                            $Qdefinitions->execute();
                                            
                                            while ($Qdefinitions->fetch()) {
                                             ?>
                                                <tr>
                                                  <td><input type="hidden" name="definition_key[<?= $Qdefinitions->value('id'); ?>]" value="<?= htmlentities($Qdefinitions->value('definition_key')); ?>"><?= $Qdefinitions->value('definition_key'); ?></td>
                                                  <td><input type="text" class="form-control" name="definition_value[<?= $Qdefinitions->value('id'); ?>]" value="<?= htmlentities($Qdefinitions->value('definition_value')); ?>"></td>
                                                </tr>
                                            
                                             <?php
                                            }
                                        ?>  
                                    </tbody>
                                </table>   
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    <?php
        }
    
?>
                <div class="btn-group pull-right">
                  <?= HTML::button(KUUZU::getDef('image_back'), 'fa fa-chevron-left', KUUZU::link('define_language.php')); ?>
                  <?= HTML::button(KUUZU::getDef('image_save'), 'fa fa-save'); ?>
                </div>

        </div>
    </form>        
<?php
    }
    if ( empty($action) && !isset($_GET['content_group']) ) {
    ?>
        <table class="kuuzu-table table table-bordered table-hover">
            
            <thead>
                <tr class="info">
                    <th class="col-md-8"><?= KUUZU::getDef('table_heading_content_group_title'); ?></th>
                    <th class="col-md-4 action"><?= KUUZU::getDef('table_heading_content_group_action'); ?></th>
                </tr>
            </thead>
            
            <tbody>
                <?php
                    $Qcontent_group = $KUUZU_Db->prepare('select distinct content_group from :table_languages_definitions');
                    $Qcontent_group->execute();
                    
                    while ($Qcontent_group->fetch()) {
                     ?>
                        <tr>
                            <td><?= $Qcontent_group->value('content_group'); ?></td>
                            <td class="action"><a href="<?= KUUZU::link('define_language.php?content_group=' . $Qcontent_group->value('content_group') . '&action=edit'); ?>"><i class="fa fa-pencil" title="<?= KUUZU::getDef('image_edit'); ?>"></i></a></td>
                        </tr>
                     <?php
                    }
                ?>  
            </tbody>
        </table>  

    <?php
    }

    require($kuuTemplate->getFile('template_bottom.php'));
    require('includes/application_bottom.php');
?>
