<?php
    use D2U_Immo\ImportOpenImmo;

    $openimmoimport = new ImportOpenImmo();
    $import_file = (string) rex_request('import', 'string');

    // import requested file
    if ('' !== $import_file) {
        if ($openimmoimport->importZIP($import_file)) {
            echo rex_view::success(rex_i18n::msg('d2u_immo_import_import_success'));
        } else {
            echo rex_view::error(rex_i18n::msg('d2u_immo_import_import_error'));
        }
    }

    $zip_filenames = $openimmoimport->getZIPFiles();
    $log_file = '';
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
                    if (count($zip_filenames) > 0) {
                        echo '<p>'. rex_i18n::msg('d2u_immo_import_import_select') ,'</p>';
                        echo '<ul>';
                        foreach ($zip_filenames as $zip_file) {
                            if ('zip' === pathinfo($zip_file, PATHINFO_EXTENSION)) {
                                $filesize = filesize($openimmoimport->import_folder . $zip_file);
                                $filesize_mb = round($filesize / (1024 * 1024), 2);
                                echo '<li><a href="'. rex_url::currentBackendPage(['import' => $zip_file])  .'"'. ($import_file === $zip_file ? ' style="color:white"' : '') .'>'. $zip_file .' ('. $filesize_mb .' MB)</a></li>';
                            }
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>'. rex_i18n::msg('d2u_immo_import_import_none') .'</p>';
                    }
                ?>
            </div>
        </fieldset>
    </div>
    <?php
        if (file_exists($openimmoimport->log_file)) {
            $content = file_get_contents($openimmoimport->log_file);
            if (false !== $content) {
                ?>
                <p>&nbsp;</p>
                <div class="panel-body">
                    <fieldset>
                        <legend><?= rex_i18n::msg('d2u_immo_import_import_log') ?></legend>
                        <div class="panel-body-wrapper slide">
                            <?= '<pre>'. $content .'</pre>' ?>
                        </div>
                    </fieldset>
                </div>
                <?php
            }
        }
    ?>
</div>