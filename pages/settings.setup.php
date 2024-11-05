<?php
/*
 * Modules
 */
$d2u_module_manager = new \TobiasKrais\D2UHelper\ModuleManager(D2UImmoModules::getModules(), 'modules/', 'd2u_immo');

// \TobiasKrais\D2UHelper\ModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if ('' !== $d2u_module_id) {
    $d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// \TobiasKrais\D2UHelper\ModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Template</h2>
<p>Beispielseiten</p>
<ul>
	<li>Immobilien Addon: <a href="https://www.immobiliengaiser.de/" target="_blank">
		ImmobilienGaiser</a>.</li>
</ul>
<p>Im D2U Helper Addon kann das "Header Pic Template - 2 Columns" installiert werden. In
	diesem Template sind alle nötigen Anpassungen integriert.</p>