<?php

declare(strict_types=1);

namespace d3yii2\d3printer\components;

use Zebra\Client;
use Zebra\CommunicationException;

final class ZebraClient extends Client
{
    public function sendAndRead(string $zpl, $length = 1024): string
    {
        $this->send($zpl);
        $response = @socket_read($this->socket,$length);

        if ($response === false) {
            $error = $this->getLastError();
            $this->disconnect();

            throw new CommunicationException($error['message'], $error['code']);
        }

        $this->disconnect();

        return $response;
    }
}
