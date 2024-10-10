<?php

declare(strict_types=1);

namespace d3yii2\d3printer\components;

use Yii;
use yii\base\Component;

final class Mailer extends Component
{
  public ?string $from = null;
  public ?array $to = null;
  public ?string $subject = 'System "{systemName}",Problems with the "{name}" {deviceName}';
  public ?string $messageTranslation = 'd3printer';

  public function send(
      string $printerComponentName,
      string $body,
      string $deviceName = 'printer'
  ): void
  {
//      if (YII_DEBUG) {
//          // Save emails to runtime instead sending
//          Yii::$app->mailer->useFileTransport = true;
//      }
      Yii::$app->mailer->useFileTransport = false;
      $subject = Yii::t(
          $this->messageTranslation,
          $this->subject,
          [
              'systemName' => Yii::$app->name,
              'name' => $printerComponentName,
              'deviceName' => $deviceName
          ]
      );
      Yii::$app->mailer
          ->compose()
          ->setFrom($this->from)
          ->setTo($this->to)
          ->setSubject($subject)
          ->setTextBody($body)
          ->send();
  }
}
