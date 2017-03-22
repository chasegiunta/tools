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
use craft\fields\Categories;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Template;

/**
 * Disabled Lightswitch Field
 *
 * @author    Supercool
 * @package   SupercoolTools
 * @since     1.0.0
 */
class DisabledCategories extends Categories
{
    // Public Properties
    // =========================================================================


    // Static Methods
    // =========================================================================

    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('supercooltools', 'Categories (Disabled)');
    }

     // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->inputTemplate = 'supercooltools/_components/fields/disabledcategories/input';
    }


}
