<?php

namespace bazilio\yii\newrelic\handlers;

use yii\web\Application;
use yii\web\View;

class WebHandler extends BaseHandler
{
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        if ($this->newrelic->enableEndUser) {
            $app->on(
                Application::EVENT_BEFORE_ACTION,
                function () use ($app, &$agent) {
                    $app->controller->view->registerJs(
                        $this->getAgent()->getBrowserTimingHeader(false),
                        View::POS_HEAD,
                        'newrelic-head'
                    );

                    $app->controller->view->registerJs(
                        $this->getAgent()->getBrowserTimingFooter(false),
                        View::POS_END,
                        'newrelic-end'
                    );
                }
            );
        }

        $app->on(
            Application::EVENT_AFTER_ACTION,
            function () use ($app) {
                foreach ($app->controller->actionParams as $key => $value) {
                    $this->getAgent()->addCustomParameter($key, var_export($value, true));
                }
            }
        );
    }

}