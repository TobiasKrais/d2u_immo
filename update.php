<?php
// Update language replacements
d2u_immo_lang_helper::factory()->install();

// Update modules
if(class_exists(D2UModuleManager) && class_exists(D2UImmoModules)) {
	$d2u_module_manager = new D2UModuleManager(D2UImmoModules::getD2UImmoModules(), "", "d2u_immo");
	$d2u_module_manager->autoupdate();
}