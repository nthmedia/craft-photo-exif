<?php
/**
 * Photo EXIF plugin for Craft CMS 3.x
 *
 * The plugin reads EXIF data from photos
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2019 NTH media
 */

namespace nthmedia\photoexif;

use craft\base\Element;
use craft\elements\Asset;
use craft\events\RegisterElementTableAttributesEvent;
use nthmedia\photoexif\services\Metadata as MetadataService;
use nthmedia\photoexif\twigextensions\PhotoExifTwigExtension;
use nthmedia\photoexif\fields\Coordinates as CoordinatesField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Class PhotoExif
 *
 * @author    NTH media
 * @package   PhotoExif
 * @since     1.0.0
 *
 * @property  MetadataService $metadata
 */
class PhotoExif extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var PhotoExif
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

//        Craft::$app->view->registerTwigExtension(new PhotoExifTwigExtension());

        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = CoordinatesField::class;
            }
        );

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Event::on(
            Asset::class,
            Asset::EVENT_BEFORE_VALIDATE,
            function ($event) {
                if ($event->sender->kind === 'image') {
                    $imagePath = $event->sender->tempFilePath ?? $event->sender->getImageTransformSourcePath();
                    if (exif_imagetype($imagePath)) {
                        $exif = @exif_read_data($imagePath);

                        if (!$exif) {
                            return;
                        }

                        // Rewrite latitude and longitude from array
                        $latitude = $this->getGps($exif["GPSLatitude"], $exif['GPSLatitudeRef']);
                        $longitude = $this->getGps($exif["GPSLongitude"], $exif['GPSLongitudeRef']);

                        // Check which fields are defined in the field layout
                        $fieldLayout = $event->sender->getFieldLayout();
                        if ($fieldLayout) {
                            $fields = $fieldLayout->getFields();

                            // Find the 'Coordinates' fields
                            $fields = array_filter($fields, function ($field) {
                                return get_class($field) === 'nthmedia\photoexif\fields\Coordinates';
                            });

                            array_walk($fields, function ($field) use ($event, $latitude, $longitude) {
                                if ($event->sender->{$field['handle']} === null) {
                                    $event->sender->{$field['handle']} = $latitude . "," . $longitude;
                                }
                            }, $fields);
                        }
                    }
                }
            }
        );

        Craft::info(
            Craft::t(
                'photo-exif',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /*
     * Found in https://stackoverflow.com/a/2572991/9405801
     */
    protected function getGps($exifCoord, $hemi) {

        $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;

        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);

    }

    /*
     * Found in https://stackoverflow.com/a/2572991/9405801
     */
    protected function gps2Num($coordPart) {

        $parts = explode('/', $coordPart);

        if (count($parts) <= 0)
            return 0;

        if (count($parts) == 1)
            return $parts[0];

        return floatval($parts[0]) / floatval($parts[1]);
    }

}
