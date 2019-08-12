<?php
/**
 * Photo EXIF plugin for Craft CMS 3.x
 *
 * The plugin reads EXIF data from photos
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2019 NTH media
 */

namespace nthmedia\photoexif\assetbundles\coordinatesfield;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    NTH media
 * @package   PhotoExif
 * @since     1.0.0
 */
class CoordinatesFieldAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@nthmedia/photoexif/assetbundles/coordinatesfield/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/Coordinates.js',
        ];

        $this->css = [
            'css/Coordinates.css',
        ];

        parent::init();
    }
}
