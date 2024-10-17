<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
use d3yii2\d3printer\components\Mailer;
use d3yii2\d3printer\components\ZebraPrinter;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\ExitCode;
use function count;

/**
 * Class HealthZebraCronController
 * @package d3yii2\d3printer\controllers
 */
class UniversalHealthController extends D3CommandController
{
    /**
     * check printer by printerComponent and on problems by mailerComponent send emails
     * to console controllerMap add:
     * 'printer-health' => 'd3yii2\d3printer\controllers\UniversalHealthController'
     * run: php yii d3printer/universal-health/index printerBig printerMailer
     *
     *
     * @param string $printerComponent like d3yii2\d3printer\components\ZebraPrinter
     * @param string $mailerComponent like d3yii2\d3printer\components\Mailer
     * @return int
     * @throws InvalidConfigException
     * @throws Exception
     */
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
            $this->out('Errors: ' . implode('; ', $errors));
            if ($printer->isChangedErrors($errors)) {
                $this->out('Send errors to ' . implode('; ', $mailer->to));
                $mailer->send($printer->printerName, $this->getErrorMessage($printer, $errors));
            }
        }
        $printer->saveErrors($errors);
        return ExitCode::OK;
    }

    private function getErrorMessage(ZebraPrinter $printer, array $errors): string
    {
        return Yii::t(
            'd3printer',
            'Device Health. Printer: {printerName} ({printerCode}). Errors: {errors}',
            [
                'printerName' => $printer->printerName,
                'printerCode' => $printer->printerCode,
                'errors' => implode(PHP_EOL, $errors)
            ]
        );
    }
}
