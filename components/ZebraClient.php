<?php

declare(strict_types=1);

namespace d3yii2\d3printer\components;

use Zebra\Client;
use Zebra\CommunicationException;

final class ZebraClient extends Client
{
    public const ERROR_HEALTH_LIST = [
        [
            'code' => '030',
            'label' => 'communication settings',
            'show' => false,
        ],
        [
            'code' => '1',
            'label' => 'paper out',
            'show' => true,
        ],
        [
            'code' => '1',
            'label' => 'pause active',
            'show' => true,
        ],
        [
            'code' => '1231',
            'label' => 'label length (value in number of dots)',
            'show' => false,
        ],
        [
            'code' => '000',
            'label' => 'number of formats in receive buffer',
            'show' => false,
        ],
        [
            'code' => '1',
            'label' => 'buffer full',
            'show' => false,
        ],
        [
            'code' => '1',
            'label' => 'diagnostic mode active',
            'show' => false,
        ],
        [
            'code' => '1',
            'label' => 'partial format',
            'show' => false,
        ],
        [
            'code' => '000',
            'label' => 'unused',
            'show' => false,
        ],
        [
            'code' => '1',
            'label' => 'corrupt RAM (configuration data lost)',
            'show' => true,
        ],
        [
            'code' => '1',
            'label' => 'under temperature',
            'show' => true,
        ],
        [
            'code' => '1',
            'label' => 'over temperature',
            'show' => true,
        ],
    ];

    public function sendAndRead(string $zpl, $length = 1024): string
    {
        $this->send($zpl);
        $response = @socket_read($this->socket, $length);

        if ($response === false) {
            $error = $this->getLastError();
            $this->disconnect();

            throw new CommunicationException($error['message'], $error['code']);
        }

        $this->disconnect();

        return $response;
    }
}
