<?php
/**
 * Yii Application Config
 *
 * Edit this file at your own risk!
 *
 * The array returned by this file will get merged with
 * vendor/craftcms/cms/src/config/app.php and app.[web|console].php, when
 * Craft's bootstrap script is defining the configuration for the entire
 * application.
 *
 * You can define custom modules and system components, and even override the
 * built-in system components.
 *
 * If you want to modify the application config for *only* web requests or
 * *only* console requests, create an app.web.php or app.console.php file in
 * your config/ folder, alongside this one.
 */

use craft\helpers\App;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use NewRelic\Monolog\Enricher\Formatter;
use NewRelic\Monolog\Enricher\Processor;

return [
    'modules' => [
        'demos' => \modules\demos\Module::class,
        'spoke' => \modules\Module::class,
    ],
    'bootstrap' => ['spoke', 'demos'],
    'components' => [
        'log' => [
            'class' => yii\log\Dispatcher::class,
            'targets' => [
                function () {
                    $logger = new \Monolog\Logger('app');
                    $logger->pushHandler(
                        (new \Monolog\Handler\StreamHandler('php://stderr', \Monolog\Logger::WARNING))
                            ->setFormatter(new Formatter())
                    );

                    if (\Craft::$app->getConfig()->getGeneral()->devMode) {
                        $logger->pushHandler(
                            (new \Monolog\Handler\StreamHandler('php://stdout', \Monolog\Logger::INFO))
                            ->setFormatter(new Formatter())
                        );
                    }

                    $logger->pushProcessor(new Processor());

                    return \Craft::createObject([
                        'class' => \samdark\log\PsrTarget::class,
                        'except' => ['yii\web\HttpException:40*'],
                        'logVars' => [],
                        'logger' => $logger,
                        'addTimestampToContext' => true,
                    ]);
                }
            ],
        ]
    ]
];
