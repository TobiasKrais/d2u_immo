<?php
/**
 * @api
 * Offers helper functions for language issues.
 */
class export_lang_helper extends d2u_immo_lang_helper
{
    /**
     * @var array<string,string> Array with englisch replacements. Key is the wildcard,
     * value the replacement.
     */
    public $replacements_english = [
        'd2u_immo_export_linkedin_comment_text' => '',
        'd2u_immo_export_linkedin_details' => '',
        'd2u_immo_export_linkedin_offers' => '',
    ];
    
    /**
     * Factory method.
     * @return export_lang_helper Object
     */
    public static function factory()
    {
        return new self();
    }
}