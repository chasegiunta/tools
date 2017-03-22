<?php
/**
 * SupercoolTools plugin for Craft CMS 3.x
 *
 * SupercoolTools
 *
 * @link      http://supercooldesign.co.uk
 * @copyright Copyright (c) 2017 Supercool
 */

namespace supercool\supercooltools;

use supercool\supercooltools\fields\AuthorInstructions as AuthorInstructionsField;
use supercool\supercooltools\fields\DefaultNumber as DefaultNumberField;
use supercool\supercooltools\fields\DisabledLightswitch as DisabledLightswitchField;
use supercool\supercooltools\fields\DisabledPlainText as DisabledPlainTextField;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\services\Fields;
use craft\events\RegisterComponentTypesEvent;

use yii\base\Event;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://craftcms.com/docs/plugins/introduction
 *
 * @author    Naveed
 * @package   Tester
 * @since     1.0.0
 *
 */
class SupercoolTools extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * SupercoolTools::$plugin
     *
     * @var SupercoolTools
     */
    public static $plugin;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * SupercoolTools::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        // Register our fields
        Event::on(
            Fields::className(),
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function (RegisterComponentTypesEvent $event) {
                $event->types[] = AuthorInstructionsField::class;
                $event->types[] = DefaultNumberField::class;
                $event->types[] = DisabledLightswitchField::class;
                $event->types[] = DisabledPlainTextField::class;
            }
        );

        // Do something after we're installed
        Event::on(
            Plugins::className(),
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function (PluginEvent $event) {
                if ($event->plugin === $this) {
                    // We were just installed
                }
            }
        );

/**
 * Logging in Craft involves using one of the following methods:
 *
 * Craft::trace(): record a message to trace how a piece of code runs. This is mainly for development use.
 * Craft::info(): record a message that conveys some useful information.
 * Craft::warning(): record a warning message that indicates something unexpected has happened.
 * Craft::error(): record a fatal error that should be investigated as soon as possible.
 *
 * Unless `devMode` is on, only Craft::warning() & Craft::error() will log to `craft/storage/logs/web.log`
 *
 * It's recommended that you pass in the magic constant `__METHOD__` as the second parameter, which sets
 * the category to the method (prefixed with the fully qualified class name) where the constant appears.
 *
 * To enable the Yii debug toolbar, go to your user account in the AdminCP and check the
 * [] Show the debug toolbar on the front end & [] Show the debug toolbar on the Control Panel
 *
 * http://www.yiiframework.com/doc-2.0/guide-runtime-logging.html
 */
        Craft::info(Craft::t('supercooltools', '{name} plugin loaded', ['name' => $this->name]), __METHOD__);
    }

    // Protected Methods
    // =========================================================================

}
