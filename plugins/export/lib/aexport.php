<?php

namespace D2U_Immo;

use rex_i18n;
use rex_media;
use rex_media_manager;
use rex_url;
use rex_view;

/**
 * Defines methods each export provider has to implement.
 */
abstract class AExport
{
    /** @var ExportedProperty[] that need to be exported */
    protected $export_properties = [];

    /** @var Provider export provider object */
    protected $provider;

    /**
     * Constructor. Initializes variables.
     * @param Provider $provider Export Provider
     */
    public function __construct($provider)
    {
        $this->provider = $provider;
        $this->export_properties = ExportedProperty::getAll($this->provider);
    }

    /**
     * Converts HTML formatted string to string with new lines. Following HTML
     * tags are converted to new lines: </p>, <br>, </h1>, </h2>, </h3>, </h4>,
     * </h5>, </h6>, </li>.
     * @param string $html HTML string
     * @return string Converted string
     */
    protected static function convertToExportString($html)
    {
        $html = str_replace('<br>', PHP_EOL, $html);
        $html = str_replace('</p>', '</p>'.PHP_EOL, $html);
        $html = str_replace('</h1>', '</h1>'.PHP_EOL, $html);
        $html = str_replace('</h2>', '</h2>'.PHP_EOL, $html);
        $html = str_replace('</h3>', '</h3>'.PHP_EOL, $html);
        $html = str_replace('</h4>', '</h4>'.PHP_EOL, $html);
        $html = str_replace('</h5>', '</h5>'.PHP_EOL, $html);
        $html = str_replace('</h6>', '</h6>'.PHP_EOL, $html);
        $html = str_replace('</li>', '</li>'.PHP_EOL, $html);
        $html = html_entity_decode($html);
        return strip_tags($html);
    }

    /**
     * Export properties that are added to export list for the provider.
     * @return string error message - if no errors occured, empty string is returned
     */
    abstract public function export();

    /**
     * Creates the scaled image if it does not already exist.
     * @param string $pic Picture filename
     * @return string Cached picture filename
     */
    protected function preparePicture($pic)
    {
        $media = rex_media::get($pic);
        if ($media instanceof rex_media && $media->getSize() < 3145728) {
            $media_manager = rex_media_manager::create($this->provider->media_manager_type, $pic);
            if (file_exists($media_manager->getCacheFilename())) {
                // Cached file if successfull
                return $media_manager->getCacheFilename();
            }

            // Normal media file if not successfull
            return rex_url::media($pic);

        }

        echo rex_view::warning($pic .': '. rex_i18n::msg('d2u_immo_export_image_too_large'));
        return rex_url::media($pic);

    }

    /**
     * Save export results.
     */
    protected function saveExportedProperties(): void
    {
        foreach ($this->export_properties as $exported_property) {
            if ('add' === $exported_property->export_action || 'update' === $exported_property->export_action) {
                $exported_property->export_timestamp = date('Y-m-d H:i:s');
                $exported_property->export_action = '';
                $exported_property->save();
            } elseif ('delete' === $exported_property->export_action) {
                $exported_property->delete();
            }
        }
    }
}
