<div class="row">
	<div class="col-xs-12">
		<br>
		<?php
        // Gruppen
        $query = 'SELECT category_id, name  '.
                'FROM '. \rex::getTablePrefix() .'d2u_immo_categories_lang '.
                'WHERE clang_id = '. rex_clang::getCurrentId() .' '.
                'ORDER BY name';
        $result = rex_sql::factory();
        $result->setQuery($query);
        $num_rows = $result->getRows();

        $categories = [];
        for ($i = 0; $i < $num_rows; ++$i) {
            $categories[$result->getValue('category_id')] = $result->getValue('name');
            $result->next();
        }
        echo '<p>Welche Kategorie soll angezeigt werden?<br /></p>';
        $select_category = new rex_select();
        $select_category->setName('REX_INPUT_VALUE[1]');
        $select_category->setSize(1);
        $select_category->setAttribute('class', 'form-control');
        $select_category->addOption('Alle', 0);

        // Daten
        foreach ($categories as $category_id => $name) {
          $select_category->addOption($name, $category_id);
        }

        // Vorselektierung
        $select_category->setSelected('REX_VALUE[1]');

        echo $select_category->show();
        ?>
		<br>
		Alle weiteren Einstellungen können im <a href="index.php?page=d2u_immo">
				D2U Immo Addon</a> vorgenommen werden.
	</div>
</div>