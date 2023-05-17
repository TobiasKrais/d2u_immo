<?php
// show log viewer
$dir = rex_path::addonCache('d2u_immo');
$files = scandir($dir);
if (is_array($files)) {
    $files = array_diff($files, ['..', '.']);
}
$show_file = (string) rex_request('file', 'string');
?>
<div class="panel panel-edit">
    <header class="panel-heading">
        <div class="panel-title"><?= rex_i18n::msg('d2u_immo_import_log_title') ?></div>
    </header>
    <div class="panel-body">
        <fieldset>
            <legend><?= rex_i18n::msg('d2u_immo_import_log_list') ?></legend>
            <div class="panel-body-wrapper slide">
                <?php
                    if (is_array($files) && count($files) > 0) {
                        echo '<p>'. rex_i18n::msg('d2u_immo_import_log_details') .'</p>';
                        echo '<ul>';
                        foreach (array_reverse($files) as $file) {
                            if ('log' === pathinfo($file, PATHINFO_EXTENSION)) {
                                if ('' === $show_file) {
                                    $show_file = $file;
                                }
                                $logfile_info = pathinfo($file);
                                $zip_file = $logfile_info['filename'] .'.zip';
                                echo '<li>';
                                echo '<a href="'. rex_url::currentBackendPage(['file' => $file])  .'"'. ($show_file === $file ? ' style="color:white"' : '') .'>'. $file .'</a>';
                                if (file_exists(rex_path::addonCache('d2u_immo', $zip_file))) {
                                    $filesize = filesize(rex_path::addonCache('d2u_immo', $zip_file));
                                    $filesize_mb = round($filesize / (1024 * 1024), 2);
                                    echo ' - <a href="'. rex_url::currentBackendPage(['download_file' => $zip_file])  .'">OpenImmo ZIP ('. $filesize_mb .' MB)</a>';
                                }
                                echo '</li>';
                            }
                        }
                        echo '</ul>';
                    } else {
                        echo '<p>'. rex_i18n::msg('d2u_immo_import_log_none') .'</p>';
                    }
                ?>
            </div>
        </fieldset>
    </div>
    <?php
        $content = file_get_contents(rex_path::addonCache('d2u_immo', $show_file));
        if (false !== $content) {
            ?>
            <p>&nbsp;</p>
            <div class="panel-body">
                <fieldset>
                    <legend><?= $show_file ?></legend>
                    <div class="panel-body-wrapper slide">
                        <?= '<pre>'. $content .'</pre>' ?>
                    </div>
                </fieldset>
            </div>
            <?php
        }
    ?>
</div>