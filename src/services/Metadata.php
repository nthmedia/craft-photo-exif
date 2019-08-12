<?php
/**
 * Photo EXIF plugin for Craft CMS 3.x
 *
 * The plugin reads EXIF data from photos
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2019 NTH media
 */

namespace nthmedia\photoexif\services;

use nthmedia\photoexif\PhotoExif;

use Craft;
use craft\base\Component;

/**
 * @author    NTH media
 * @package   PhotoExif
 * @since     1.0.0
 */
class Metadata extends Component
{
    // Public Methods
    // =========================================================================

    /*
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';

        return $result;
    }
}
