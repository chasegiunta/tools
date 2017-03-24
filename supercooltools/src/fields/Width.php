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
use supercool\supercooltools\fields\data\WidthData;

use Craft;
use craft\base\ElementInterface;
use craft\fields\Dropdown;
use craft\helpers\Db;
use yii\db\Schema;
use craft\helpers\Json;
use craft\helpers\Template;
use craft\helpers\ArrayHelper;

/**
 * Width Field
 *
 * @author    Supercool
 * @package   SupercoolTools
 * @since     1.0.0
 */
class Width extends Dropdown
{

    // Static Methods
    // =========================================================================
    
    /**
     * Returns the display name of this class.
     *
     * @return string The display name of this class.
     */
    public static function displayName(): string
    {
        return Craft::t('supercooltools', 'Width');
    }

    // Public Methods
    // =========================================================================
    
    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {

        $options = $this->translatedOptions();

        if (!$options)
        {
            // Give it a default row
            $options = array(array('label' => '', 'value' => ''));
        }

        return Craft::$app->getView()->renderTemplateMacro('_includes/forms', 'editableTableField', array(
            array(
                'label'        => $this->optionsSettingLabel(),
                'instructions' => Craft::t('supercooltools', 'Define the available options.'),
                'id'           => 'options',
                'name'         => 'options',
                'addRowLabel'  => Craft::t('supercooltools', 'Add an option'),
                'cols'         => array(
                    'widthValue' => array(
                        'heading'      => Craft::t('supercooltools', 'Width Value'),
                        'type'         => 'singleline',
                        'class'        => 'code'
                    ),
                    'widthDefault' => array(
                        'heading'      => Craft::t('supercooltools', 'Width Default?'),
                        'type'         => 'checkbox',
                        'class'        => 'thin'
                    ),

                    'leftValue' => array(
                        'heading'      => Craft::t('supercooltools', 'Left Value'),
                        'type'         => 'singleline',
                        'class'        => 'code'
                    ),
                    'leftDefault' => array(
                        'heading'      => Craft::t('supercooltools', 'Left Default?'),
                        'type'         => 'checkbox',
                        'class'        => 'thin'
                    ),

                    'rightValue' => array(
                        'heading'      => Craft::t('supercooltools', 'Right Value'),
                        'type'         => 'singleline',
                        'class'        => 'code'
                    ),
                    'rightDefault' => array(
                        'heading'      => Craft::t('supercooltools', 'Right Default?'),
                        'type'         => 'checkbox',
                        'class'        => 'thin'
                    ),
                ),
                'rows' => $options
            )
        ));
    }


    /**
     * @inheritdoc
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {

        if( $value == null ) {
            $options = $this->translatedOptions();
            $data = new WidthData();
            $value = $data->setData($options, $value);
        }

        // Come up with an ID value for 'foo'
        $id = Craft::$app->getView()->formatInputId($this->handle);
     
        // Figure out what that ID is going to be namespaced into
        $namespacedId = Craft::$app->getView()->namespaceInputId($id);
                

        return Craft::$app->getView()->renderTemplate( 'supercooltools/_components/fields/width/input', array(
            'name' => $this->handle,
            'value' => $value,
            'namespaceId' => $namespacedId
        ));

    }


    public function normalizeValue($value, ElementInterface $element = null)
    {
        $options = $this->translatedOptions();
        $data = new WidthData();
        $value = $data->setData($options, $value);
        return $value;
    }

    /**
     * Value we are going to save into the database
     */
    public function serializeValue($value, ElementInterface $element = null)
    {
        $value = Json::encode($value);
        return $value;
    }

    /**
     * @inheritdoc
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @inheritdoc
     */
    public function getElementValidationRules(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function translatedOptions(): array
    {
        $translatedOptions = [];

        foreach ($this->options as $option) {
            $translatedOptions[] = [
                'widthValue' => $option['widthValue'],
                'widthDefault' => $option['widthDefault'],
                'leftValue' => $option['leftValue'],
                'leftDefault' => $option['leftDefault'],
                'rightValue' => $option['rightValue'],
                'rightDefault' => $option['rightDefault'],
            ];
        }

        return $translatedOptions;
    }

}
