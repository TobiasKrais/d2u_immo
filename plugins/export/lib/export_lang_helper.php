<?php
/**
 * Offers helper functions for language issues
 */
class export_lang_helper extends d2u_immo_lang_helper {
	/**
	 * @var array<string, string> Array with englisch replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_english = [
		'd2u_immo_export_linkedin_comment_text' => 'Check out our latest offer. For further information click on the picture.',
		'd2u_immo_export_linkedin_details' => 'More information can be found on our website.',
		'd2u_immo_export_linkedin_offers' => 'offers'
	];

	/**
	 * @var array<string, string> Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected array $replacements_german = [
		'd2u_immo_export_linkedin_comment_text' => 'Unsere aktuellen Angebote: Klicken Sie auf das Bild.',
		'd2u_immo_export_linkedin_details' => 'Mehr Infos finden Sie auf unserer Webseite.',
		'd2u_immo_export_linkedin_offers' => 'bietet an'
	];

	/**
	 * Factory method.
	 * @return used_machines_lang_helper Object
	 */
	public static function factory() {
		return new export_lang_helper();
	}
}