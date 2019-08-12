<?php
/**
 * Photo EXIF plugin for Craft CMS 3.x
 *
 * The plugin reads EXIF data from photos
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2019 NTH media
 */

namespace nthmedia\photoexif\twigextensions;

use nthmedia\photoexif\PhotoExif;

use Craft;

/**
 * @author    NTH media
 * @package   PhotoExif
 * @since     1.0.0
 */
class PhotoExifTwigExtension extends \Twig_Extension
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'PhotoExif';
    }

    /**
     * @inheritdoc
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('someFilter', [$this, 'someInternalFunction']),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('someFunction', [$this, 'someInternalFunction']),
        ];
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function someInternalFunction($text = null)
    {
        $result = $text . " in the way";

        return $result;
    }
}
