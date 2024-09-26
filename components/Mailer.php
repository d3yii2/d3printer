<?php

declare(strict_types=1);

namespace d3yii2\d3printer\components;

use d3yii2\d3printer\logic\Mailer as BaseMailer;

final class Mailer extends BaseMailer
{
  public $from;
  public $to;
  public $subject;

  public function send(string $content, $conf = []): void
  {
      parent::send($content, [
        'from' => $this->from,
        'to' => $this->to,
        'subject' => $this->subject,
      ]);
  }
}
