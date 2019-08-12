<?php
/**
 * Photo EXIF plugin for Craft CMS 3.x
 *
 * The plugin reads EXIF data from photos
 *
 * @link      https://nthmedia.nl
 * @copyright Copyright (c) 2019 NTH media
 */

namespace nthmedia\photoexif\fields;

use nthmedia\photoexif\PhotoExif;
use nthmedia\photoexif\assetbundles\coordinatesfield\CoordinatesFieldAsset;

use Craft;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;

/**
 * @author    NTH media
 * @package   PhotoExif
 * @since     1.0.0
 */
class Coordinates extends Field
{
    // Public Properties
    // =========================================================================

    public $coordinates = null;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function hasContentColumn (): bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('photo-exif', 'Coordinates');
    }

    // Public Methods
    // =========================================================================

    public function getElementValidationRules(): array
    {
        return [
            ['string'],
            [
                'match',
                'pattern' => '/^[-+]?([1-8]?\d(\.\d+)?|90(\.0+)?),\s*[-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+)?)$/',
                'message' => Craft::t('photo-exif', 'Coordinates are incorrectly formatted. (Correct: 12.34,56.78)')
            ], // https://stackoverflow.com/a/18690202/9405801
            ['default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        // Replace spaces in the string
        $value = str_replace(' ', '', $value);
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {
        // Get our id and namespace
        $id = Craft::$app->getView()->formatInputId($this->handle);
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);

        // Variables to pass down to our field JavaScript to let it namespace properly
        $jsonVars = [
            'id' => $id,
            'name' => $this->handle,
            'namespace' => $namespacedId,
            'prefix' => Craft::$app->getView()->namespaceInputId(''),
            ];

        $jsonVars = Json::encode($jsonVars);
        Craft::$app->getView()->registerJs("$('#{$namespacedId}-field').PhotoExifCoordinates(" . $jsonVars . ");");

        // Render the input template
        return Craft::$app->getView()->renderTemplate(
            'photo-exif/_components/fields/Coordinates_input',
            [
                'name' => $this->handle,
                'value' => $value,
                'field' => $this,
                'id' => $id,
                'namespacedId' => $namespacedId,
            ]
        );
    }


    /**
     * @inheritdoc
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        return 'AA';
    }

}
