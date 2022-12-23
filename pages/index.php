<?php
echo rex_view::title(rex_i18n::msg('d2u_immo'));

if (rex_config::get('d2u_helper', 'article_id_privacy_policy', 0) == 0) {
	print rex_view::warning(rex_i18n::msg('d2u_helper_gdpr_warning'));
}

rex_be_controller::includeCurrentPageSubPath();