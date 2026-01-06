<?php

namespace roelvanhintum\sentry;

use roelvanhintum\sentry\models\Settings;
use roelvanhintum\sentry\services\SentryService;

use Craft;
use craft\helpers\App;
use craft\web\Application as WebApplication;
use craft\base\Plugin as CraftPlugin;
use craft\events\ExceptionEvent;
use craft\events\TemplateEvent;
use craft\web\ErrorHandler;
use craft\web\View;
use Exception;
use Sentry;
use Sentry\State\Scope;

use yii\base\Event;

/**
 * Sentry craft cms plugin
 *
 * @property SentryService $sentry The sentry component
 * @method SentryService getSentry()      Returns the sentry component.
 */
class Plugin extends CraftPlugin
{
    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Plugin::$plugin
     *
     * @var Plugin
     */
    public static Plugin $plugin;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'sentry' => SentryService::class,
        ]);

        $app = Craft::$app;
        $info = $app->getInfo();
        $settings = $this->getSettings();

        if (!$this->isInstalled || !$settings->enabled) return;

        if (!$settings->clientDsn) {
            Craft::error('Failed to report exception due to missing client key (DSN)', $this->handle);
            return;
        }

        /**
         * Init Sentry
         */
        $options = [
            'dsn'                => $settings->clientDsn,
            'environment'        => App::env('CRAFT_ENVIRONMENT'),
            'release'            => $settings->release,
            'traces_sample_rate' => $settings->sampleRate,
        ];

        if (!Craft::$app->request->getIsConsoleRequest()) {
            // Prevent ExceptionListenerIntegration from loading.
            $options['integrations'] = static function (array $integrations) {
                return array_filter($integrations, static function (\Sentry\Integration\IntegrationInterface $integration): bool {
                    if ($integration instanceof \Sentry\Integration\ExceptionListenerIntegration) {
                        return false;
                    }

                    return true;
                });
            };
        }

        Sentry\init($options);

        /**
         * Setup user
         */
        Event::on(WebApplication::class, WebApplication::EVENT_INIT, function () use ($app, $settings) {
            $user = $app->getUser()->getIdentity();

            Sentry\configureScope(function (Scope $scope) use ($settings, $user) {
                if ($user && !$settings->anonymous) {
                    $scope->setUser([
                        'ID'       => $user->id,
                        'Username' => $user->username,
                        'Email'    => $user->email,
                        'Admin'    => $user->admin ? 'Yes' : 'No',
                    ]);
                }
            });
        });

        Sentry\configureScope(function (Scope $scope) use ($app, $info) {
            $scope->setExtra('App Type', 'Craft CMS');
            $scope->setExtra('App Name', App::env('CRAFT_APP_ID') ? App::env('CRAFT_APP_ID') : $app->name);
            $scope->setExtra('App Edition (licensed)', $app->getLicensedEditionName());
            $scope->setExtra('App Edition (running)', $app->getEditionName());
            $scope->setExtra('App Version', $info->version);
            $scope->setExtra('App Version (schema)', $info->schemaVersion);
            $scope->setExtra('PHP Version', phpversion());
        });

        /**
         * Init Sentry JS SDK (Front end)
         */
        if (Craft::$app->request->isSiteRequest && $settings->reportJsErrors) {
            if (!isset($settings->clientKey)) {
                throw new Exception('Failed to register Sentry browser client due to missing clientKey');
                return;
            } else {
                Event::on(
                    View::class,
                    View::EVENT_BEFORE_RENDER_TEMPLATE,
                    function (TemplateEvent $event) {
                        $settings = $this->getSettings();
                        $view = Craft::$app->getView();
                        $ignoreErrors = json_encode($settings->ignoreErrors);
                        $dataStorageLocationSubdomain = $settings->dataStorageLocation == 'EU' ? 'js-de' : 'js';

                        $view->registerScript("
                        // Configure sentryOnLoad before adding the Loader Script
                        window.sentryOnLoad = function () {
                            Sentry.init({
                            release: '$settings->release',
                            environment: '" . App::env('CRAFT_ENVIRONMENT') . "',
                            ignoreErrors: $ignoreErrors,
                            });
                        };", View::POS_END, $this->getScriptOptions());

                        $view->registerScript(
                            "",
                            View::POS_END,
                            array_merge([
                                'src' => "https://".$dataStorageLocationSubdomain.".sentry-cdn.com" ."/$settings->clientKey.min.js",
                                'crossorigin' => 'anonymous',
                            ], $this->getScriptOptions())
                        );
                    }
                );
            }
        }

        /**
         * Listen to exceptions
         */
        Event::on(
            ErrorHandler::class,
            ErrorHandler::EVENT_BEFORE_HANDLE_EXCEPTION,
            function (ExceptionEvent $event) {
                $this->sentry->handleException($event->exception);
            }
        );
    }

    public function getSentry(): SentryService
    {
        return $this->sentry;
    }

    private function getScriptOptions(): array
    {
        $options = [];

        if (class_exists('\roelvanhintum\contentsecuritypolicy\Plugin')) {
            $options['nonce'] = \roelvanhintum\contentsecuritypolicy\Plugin::$plugin->headers->registerNonce('script-src');
        }

        return $options;
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }
}
