<?php

namespace d3yii2\d3printer\controllers;

use d3system\commands\D3CommandController;
use d3yii2\d3printer\components\Mailer;
use d3yii2\d3printer\components\ZebraPrinter;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\console\ExitCode;

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
     * @param object $printerComponent like d3yii2\d3printer\components\ZebraPrinter
     * @param object $mailerComponent like d3yii2\d3printer\components\Mailer
     * @return int
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function actionIndex(string $printerComponent, string $mailerComponent = null): int
    {
        if (!Yii::$app->has($printerComponent)) {
            throw new InvalidConfigException(
                sprintf('Missing Printer: `%s` component.', $printerComponent)
            );
        }

        /** @var ZebraPrinter $printer */
        $printer = Yii::$app->get($printerComponent);

        /** @var Mailer $mailer */
        if ($mailerComponent) {
            if (!Yii::$app->has($mailerComponent)) {
                throw new InvalidConfigException(
                    sprintf('Missing Mailer: `%s` component.', $mailerComponent)
                );
            }

            $mailer = Yii::$app->get($mailerComponent);
        } else {
            $mailer = null;
        }
        $printerStatus = $printer->processStatus();
        if (!$printerStatus->isReady()) {
            $this->out('Status: ' . $printerStatus->getActualStatusReport());
            if ($mailer && $printerStatus->isChangesInErrors()) {
                $printerStatus->saveStatus();
                $this->out('Send errors to ' . implode('; ', $mailer->to));
                $mailer->send(
                    $printer->printerName,
                    $this->getErrorMessage(
                        $printer,
                        $printerStatus->getActualStatusReport()
                    )
                );
            }
        }
        $printerStatus->saveStatus();
        return ExitCode::OK;
    }

    private function getErrorMessage(ZebraPrinter $printer, string $errors): string
    {
        return Yii::t(
            'd3printer',
            'Device Health. Printer: {printerName} ({printerCode}). Errors: {errors}',
            [
                'printerName' => $printer->printerName,
                'printerCode' => $printer->printerCode,
                'errors' => $errors
            ]
        );
    }
}
