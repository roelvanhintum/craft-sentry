<?php

namespace roelvanhintum\sentry\services;

use roelvanhintum\sentry\Plugin as SentryPlugin;

use Craft;
use craft\base\Component;

use Sentry;
use Twig\Error\RuntimeError as TwigRuntimeError;

class SentryService extends Component
{
    public function handleException($exception)
    {
        $plugin = SentryPlugin::$plugin;
        $settings = $plugin->getSettings();

        // If this is a Twig Runtime exception, use the previous one instead
        if ($exception instanceof TwigRuntimeError && ($previousException = $exception->getPrevious()) !== null) {
            $exception = $previousException;
        }

        $statusCode = $exception->statusCode ?? null;

        if (in_array($statusCode, $settings->excludedCodes)) {
            Craft::info('Exception status code excluded from being reported to Sentry.', $plugin->handle);
            return;
        }

        Sentry\configureScope(function (Sentry\State\Scope $scope) use ($statusCode) {
            $scope->setExtra('Status Code', $statusCode);
        });

        Sentry\captureException($exception);
    }
}
