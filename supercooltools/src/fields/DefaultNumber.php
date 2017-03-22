<?php
/**
 * SupercoolTools plugin for Craft CMS 3.x
 *
 * SupercoolTools
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2017 Supercool
 */

namespace supercool\supercooltools\fields;

use supercool\supercooltools\SupercoolTools as SupercoolToolsPlugin;
use supercool\supercooltools\assetbundles\supercooltools\SupercoolToolsAsset;

use Craft;
use craft\base\ElementInterface;
use craft\fields\Number;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use craft\helpers\Template;
use craft\i18n\Locale;

/**
 * Default Number Field
 *
 * @author    Supercool
 * @package   SupercoolTools
 * @since     1.0.0
 */
class DefaultNumber extends Number
{
    // Public Properties
    // =========================================================================
    
    /**
     * @var int|float The minimum allowed number
     */
    public $min = 0;

    /**
     * @var int|float|null The maximum allowed number
     */
    public $max;

    /**
     * @var int The number of digits allowed after the decimal point
     */
    public $decimals = 0;

    /**
     * @var int|null The size of the field
     */
    public $size;

    /**
     * @var int
     */
    public $default;

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('supercooltools', 'Number (Default)');
    }

    // Public Methods
    // =========================================================================
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // Normalize $default
        if ($this->default !== null && empty($this->default)) {
            $this->default = null;
        }
    }

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules = array_merge($rules, [
            ['default', 'required'],
            ['default', 'number'],
        ]);

        if ( $this->min )
        {
            $rules[] = [['default'],
                            'compare',
                            'compareAttribute' => 'min',
                            'operator' => '>='
                        ];
        }

        if ( $this->max )
        {
            $rules[] = [['default'],
                            'compare',
                            'compareAttribute' => 'max',
                            'operator' => '<='
                        ];
        }

        return $rules;
    }

    /**
     * Returns the column type that this field should get within the content table.
     */
    public function getContentColumnType(): string
    {
        return Db::getNumericalColumnType($this->min, $this->max, $this->decimals);
    }

    /**
     * Normalizes the field’s value for use.
     *
     * @param mixed                 $value   The raw field value
     * @param ElementInterface|null $element The element the field is associated with, if there is one
     *
     * @return mixed The prepared field value
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {
        // Is this a post request?
        $request = Craft::$app->getRequest();

        if (!$request->getIsConsoleRequest() && $request->getIsPost() && $this->required) {
            // Normalize the number and make it look like this is what was posted
            if ($value !== '') {
                $value = Localization::normalizeNumber($value);
            }
        }

        return $value;
    }

    /**
     * Modifies an element query.
     *
     * @param ElementQueryInterface $query The element query
     * @param mixed                 $value The value that was set on this field’s corresponding [[ElementCriteriaModel]] param,
     *                                     if any.
     *
     * @return null|false `false` in the event that the method is sure that no elements are going to be found.
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        return parent::serializeValue($value, $element);
    }

    /**
     * Returns the component’s settings HTML.
     *
     * The same principles also apply if you’re including your JavaScript code with
     * [[\craft\web\View::registerJs()]].
     *
     * @return string|null
     */
    public function getSettingsHtml()
    {
        $numberTemplate = parent::getSettingsHtml();
        // Render the settings template
        $defaultTemplate = Craft::$app->getView()->renderTemplate(
            'supercooltools/_components/fields/defaultnumber/settings',
            [
                'field' => $this,
            ]
        );

        return $numberTemplate . $defaultTemplate;
    }

    /**
     * Returns the field’s input HTML.
     *
     * @param mixed                 $value           The field’s value. This will either be the [[normalizeValue() normalized value]],
     *                                               raw POST data (i.e. if there was a validation error), or null
     * @param ElementInterface|null $element         The element the field is associated with, if there is one
     *
     * @return string The input HTML.
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {

        if ($value == null && empty($value)) 
        {
            $value = $this->default;
        }

        $decimals = $this->decimals;

        // If decimals is 0 (or null, empty for whatever reason), don't run this
        if ($decimals) {
            $decimalSeparator = Craft::$app->getLocale()->getNumberSymbol(Locale::SYMBOL_DECIMAL_SEPARATOR);
            $value = number_format($value, $decimals, $decimalSeparator, '');
        }

        // Register assets
        Craft::$app->getView()->registerAssetBundle(SupercoolToolsAsset::class);

        // Render the input template
        return Craft::$app->getView()->renderTemplate('_includes/forms/text', [
            'name' => $this->handle,
            'value' => $value,
            'size' => $this->size
        ]);

    }

}
