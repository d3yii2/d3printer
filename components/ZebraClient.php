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
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'paper out',
            'check' => true,
        ],
        [
            'code' => '1',
            'label' => 'pause active',
            'check' => true,
        ],
        [
            'code' => '1231',
            'label' => 'label length (value in number of dots)',
            'check' => false,
        ],
        [
            'code' => '000',
            'label' => 'number of formats in receive buffer',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'buffer full',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'diagnostic mode active',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'partial format',
            'check' => false,
        ],
        [
            'code' => '000',
            'label' => 'unused',
            'check' => false,
        ],
        [
            'code' => '1',
            'label' => 'corrupt RAM (configuration data lost)',
            'check' => true,
        ],
        [
            'code' => '1',
            'label' => 'under temperature',
            'check' => true,
        ],
        [
            'code' => '1',
            'label' => 'over temperature',
            'check' => true,
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
