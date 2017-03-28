<?php
/**
 * SupercoolTools plugin for Craft CMS 3.x
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2017 Supercool
 */

namespace supercool\supercooltools\widgets;

use supercool\supercooltools\SupercoolTools;
use supercool\supercooltools\assetbundles\supercooltools\SupercoolToolsAsset;

use Craft;
use craft\base\Widget;

/**
 * SupercoolTools Roll Your Own Widget
 *
 * @author    Supercool
 * @package   SupercoolTools
 * @since     1.0.0
 */
class RollYourOwn extends Widget
{

    // Public Properties
    // =========================================================================
    
    public $title = "Roll Your Own";
    public $template = "_dashboard";

    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('supercooltools', 'Roll Your Own');
    }

    /**
     * Returns the path to the widget’s SVG icon.
     *
     * @return string|null The path to the widget’s SVG icon
     */
    public static function iconPath()
    {
        return null;
    }

    /**
     * Returns the widget’s maximum colspan.
     *
     * @return int|null The widget’s maximum colspan, if it has one
     */
    public static function maxColspan()
    {
        return null;
    }

    // Public Methods
    // =========================================================================

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * Returns the component’s settings HTML.
     *
     * @return string|null
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate(
            'supercooltools/_components/widgets/rollyourown/settings',
            [
                'widget' => $this
            ]
        );
    }

    /**
     * Returns the widget's body HTML.
     *
     * @return string|false The widget’s body HTML, or `false` if the widget
     *                      should not be visible. (If you don’t want the widget
     *                      to be selectable in the first place, use {@link isSelectable()}.)
     */
    public function getBodyHtml()
    {   
        $oldMode = Craft::$app->getView()->getTemplateMode();
        Craft::$app->getView()->setTemplateMode('site');

        $output = Craft::$app->getView()->renderTemplate($this->template);

        Craft::$app->getView()->setTemplateMode($oldMode);

        return $output;
    }
}
