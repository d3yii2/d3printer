<?php

namespace d3yii2\d3printer\logic;

use Yii;

class Mailer
{
    /**
     * @param string $content
     * @param $conf
     */
    public function send(string $content, $conf): void
    {
        if (YII_DEBUG) {
            // Save emails to runtime instead sending
            Yii::$app->mailer->useFileTransport = true;
        }
        
        Yii::$app->mailer
            ->compose()
            ->setFrom($conf['from'])
            ->setTo($conf['to'])
            ->setSubject($conf['subject'])
            ->setTextBody($content)
            ->send();
    }
}
