<?php
    $dir = rex_path::addonCache('d2u_immo');
    $files = array_diff(scandir($dir), ['..', '.']);
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
                    if(is_array($files) && count($files) > 0) {
                        echo '<ul>';
                        foreach(array_reverse($files) as $file) {
                            if(pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                                if('' === $show_file) {
                                    $show_file = $file;
                                }
                                echo '<li><a href="'. rex_url::currentBackendPage(['file' => $file])  .'"'. ($show_file === $file ? ' style="color:white"' : '') .'>'. $file .'</a></li>';
                            }
                        }
                        echo '</ul>';
                    }
                    else {
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
                        <?=  '<pre>'. $content .'</pre>'; ?>
                    </div>
                </fieldset>
            </div>
            <?php
        }
    ?>
</div>