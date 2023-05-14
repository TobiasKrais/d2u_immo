<?php
    use D2U_Immo\ImportOpenImmo;
    $openimmoimport = new ImportOpenImmo();
    $zip_filenames = $openimmoimport->getZIPFiles();
    $import_file = (string) rex_request('import', 'string');
?>

<div class="panel panel-edit">
    <header class="panel-heading">
        <div class="panel-title"><?= rex_i18n::msg('d2u_immo_import') ?></div>
    </header>
    <div class="panel-body">
        <fieldset>
            <legend><?= rex_i18n::msg('d2u_immo_import_import_list') ?></legend>
            <div class="panel-body-wrapper slide">
                <?php
                    if(count($zip_filenames) > 0) {
                        echo '<p>'. rex_i18n::msg('d2u_immo_import_import_select') ,'</p>';
                        echo '<ul>';
                        foreach($zip_filenames as $zip_file) {
                            if(pathinfo($zip_file, PATHINFO_EXTENSION) === 'zip') {
                                echo '<li><a href="'. rex_url::currentBackendPage(['import' => $zip_file])  .'"'. ($import_file === $zip_file ? ' style="color:white"' : '') .'>'. $zip_file .'</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                    else {
                        echo '<p>'. rex_i18n::msg('d2u_immo_import_import_none') .'</p>';
                    }
                ?>
            </div>
        </fieldset>
    </div>
    <?php
/* 
        $content = file_get_contents(rex_path::addonCache('d2u_immo', $import_file));
        if ('' !== $content) {
            ?>
            <p>&nbsp;</p>
            <div class="panel-body">
                <fieldset>
                    <legend><?= $import_file ?></legend>
                    <div class="panel-body-wrapper slide">
                        <?=  '<pre>'. $content .'</pre>'; ?>
                    </div>
                </fieldset>
            </div>
            <?php
        }
*/
    ?>
</div>