<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
use d3yii2\d3printer\components\Mailer;
use d3yii2\d3printer\components\ZebraPrinter;
use Exception;
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
        if (!Yii::$app->has($printerComponent) || !Yii::$app->has($mailerComponent)) {
            throw new Exception(
                sprintf('Missing Printer: `%s` or Mailer: `%s` component.', $printerComponent, $mailerComponent)
            );
        }

        /** @var ZebraPrinter $printer */
        $printer = Yii::$app->get($printerComponent);

        /** @var Mailer $mailer */
        $mailer = Yii::$app->get($mailerComponent);

        $errors = $printer->collectErrors();
        if (count($errors) > 0) {
            if ($printer->isChangedErrors($errors)) {
                $mailer->send($this->getErrorMessage($printer, $errors));
            }
            $printer->saveErrors($errors);
        }

        return ExitCode::OK;
    }

    private function getErrorMessage(ZebraPrinter $printer, array $errors): string
    {
        return sprintf(
            'Device Health. Printer: %s (%s). Errors: %s',
            $printer->printerName,
            $printer->printerCode,
            implode(PHP_EOL, $errors)
        );
    }
}

