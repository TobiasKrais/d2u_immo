<?php

// Alter property table
\rex_sql_table::get(\rex::getTable('d2u_immo_properties'))
    ->ensureColumn(new \rex_sql_column('openimmo_anid', 'VARCHAR(32)'))
    ->alter();
