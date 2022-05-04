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

use Craft;
use craft\base\ElementInterface;

use craft\base\Field;
use craft\helpers\Html;
use craft\helpers\Json;
use nthmedia\photoexif\PhotoExif;
use yii\db\Schema;

/**
 * @author    NTH media
 * @package   PhotoExif
 * @since     1.0.0
 */
class Coordinates extends Field implements \craft\base\PreviewableFieldInterface
{
    // Public Properties
    // =========================================================================

    public $coordinates = null;

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function hasContentColumn(): bool
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
                'message' => Craft::t('photo-exif', 'Coordinates are incorrectly formatted. (Correct: 12.34,56.78)'),
            ], // https://stackoverflow.com/a/18690202/9405801
            ['default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): array|string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function normalizeValue(mixed $value, ?\craft\base\ElementInterface $element = null): mixed
    {
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function serializeValue(mixed $value, ?\craft\base\ElementInterface $element = null): mixed
    {
        // Replace spaces in the string
        $value = str_replace(' ', '', $value);
        return parent::serializeValue($value, $element);
    }

    /**
     * @inheritdoc
     */
    public function getInputHtml(mixed $value, ?\craft\base\ElementInterface $element = null): string
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
    public function getTableAttributeHtml(mixed $value, ElementInterface $element): string
    {
        if ($value) {
            $value = preg_replace_callback(
                '/^([-+]?([1-8]?\d(\.\d+))?|90(\.0+)?),\s*([-+]?(180(\.0+)?|((1[0-7]\d)|([1-9]?\d))(\.\d+))?)$/',
                function($matches) {
                    $roundedCoordinates = number_format((float) $matches[1], 4, '.', '');
                    $roundedCoordinates .= ', ' . number_format((float) $matches[5], 4, '.', '');

                    return Html::a(
                        $roundedCoordinates,
                        'https://www.google.com/maps?q=' . $matches[0],
                        ['target' => '_blank']
                    );
                }, $value);

            return $value;
        }
        return '';
    }
}
