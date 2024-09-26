<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
use d3yii2\d3printer\components\Mailer;
use d3yii2\d3printer\components\ZebraPrinter;
use Throwable;
use Yii;
use yii\console\ExitCode;
use function count;

/**
 * Class HealthZebraCronController
 * @package d3yii2\d3printer\controllers
 */
class HealthZebraCronController extends D3CommandController
{
    public function actionIndex(string $printerComponent, string $mailerComponent): int
    {
        try {
            if (!Yii::$app->has($printerComponent) || !Yii::$app->has($mailerComponent)) {
                throw new \Exception(sprintf(
                    'Missing Printer: `%s` or Mailer: `%s` component.',
                    $printerComponent,
                    $mailerComponent
                ));
            }

            /** @var ZebraPrinter $printer */
            $printer = Yii::$app->get($printerComponent);

            /** @var Mailer $mailer */
            $mailer = Yii::$app->get($mailerComponent);

            $errors = $printer->collectErrors();
            if (count($errors) > 0) {
                if ($printer->isChangedErrors($errors)) {
                    $mailer->send(implode(PHP_EOL, $errors));
                }
                $printer->saveErrors($errors);
            }

            return ExitCode::OK;
        } catch (Throwable $exception) {
            echo $exception->getMessage();
            Yii::error($exception->getMessage(), 'd3printer-health-check-error');
        }
    }
}

