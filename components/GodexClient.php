<?php

declare(strict_types=1);

namespace d3yii2\d3printer\components;


use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use Zebra\CommunicationException;

final class GodexClient
{
    /**
     * The endpoint.
     *
     * @var resource
     */
    protected $socket;

    private const STATUS_READY_CODE = '00';

    private const ERROR_HEALTH_LIST = [
        [
            'code' => self::STATUS_READY_CODE,
            'label' => 'Ready',
        ],
        [
            'code' => '01',
            'label' => 'Media Empty or Media Jam',
        ],
        [
            'code' => '02',
            'label' => 'Media Empty or Media Jam',
        ],
        [
            'code' => '03',
            'label' => 'Ribbon Empty',
        ],
        [
            'code' => '04',
            'label' => 'Printhead Up ( Open )',
        ],
        [
            'code' => '05',
            'label' => 'Rewinder Full',
        ],
        [
            'code' => '06',
            'label' => 'File System Full',
        ],
        [
            'code' => '07',
            'label' => 'Filename Not Found',
        ],
        [
            'code' => '8',
            'label' => 'Duplicate Name',
        ],
        [
            'code' => '09',
            'label' => 'Syntax error',
        ],
        [
            'code' => '10',
            'label' => 'Cutter JAM',
        ],
        [
            'code' => '11',
            'label' => 'Extended Memory Not Found',
        ],
        [
            'code' => '20',
            'label' => 'Pause',
        ],
        [
            'code' => '21',
            'label' => 'In Setting Mode',
        ],
        [
            'code' => '22',
            'label' => 'In Keyboard Mode',
        ],
        [
            'code' => '50',
            'label' => 'Printer is Printing',
        ],
        [
            'code' => '60',
            'label' => 'Data in Process',
        ],
    ];

    /**
     * Create an instance.
     *
     * @param string $host
     * @param int $port
     */
    public function __construct(string $host, int $port = 9100)
    {
        $this->connect($host, $port);
    }

    /**
     * Destroy an instance.
     */
    public function __destruct()
    {
        $this->disconnect();
    }

    /**
     * Create an instance statically.
     *
     * @param string $host
     * @param int $port
     * @return self
     */
    public static function printer(string $host, int $port = 9100): self
    {
        return new self($host, $port);
    }

    /**
     * Connect to printer.
     *
     * @param string $host
     * @param int $port
     * @throws CommunicationException if the connection fails.
     */
    protected function connect(string $host, int $port): void
    {
        $this->socket = self::socketConnectTimeout($host, $port, 500);
    }

    /**
     * Close connection to printer.
     */
    public function disconnect(): void
    {
        @socket_close($this->socket);
    }

    /**
     * Send ZPL data to printer.
     *
     * @param string $zpl
     * @throws CommunicationException if writing to the socket fails.
     */
    public function send(string $zpl): void
    {
        if (false === @socket_write($this->socket, $zpl . chr(13))) {
            $error = $this->getLastError();
            throw new CommunicationException($error['message'], $error['code']);
        }
    }

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

    private static function socketConnectTimeout(string $host, int $port, int $timeout=100){

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        /**
         * Set the send and receive timeouts super low so that socket_connect
         * will return to us quickly. We then loop and check the real timeout
         * and check the socket error to decide if its conected yet or not.
         */
        $connect_timeval = [
            "sec"=>0,
            "usec" => 100
        ];
        socket_set_option(
            $socket,
            SOL_SOCKET,
            SO_SNDTIMEO,
            $connect_timeval
        );
        socket_set_option(
            $socket,
            SOL_SOCKET,
            SO_RCVTIMEO,
            $connect_timeval
        );

        $now = microtime(true);

        /**
         * Loop calling socket_connect. As long as the error is 115 (in progress)
         * or 114 (already called) and our timeout has not been reached, keep
         * trying.
         */
        do{
            socket_clear_error($socket);
            $socket_connected = @socket_connect($socket, $host, $port);
            $err = socket_last_error($socket);
            $elapsed = (microtime(true) - $now) * 1000;
        }
        while (($err === 115 || $err === 114) && $elapsed < $timeout);

        /**
         * For some reason, socket_connect can return true even when it is
         * not connected. Make sure it returned true the last error is zero
         */
        $socket_connected = $socket_connected && $err === 0;

        if($socket_connected){

            /**
             * Set keep alive on so the other side does not drop us
             */
            socket_set_option($socket, SOL_SOCKET, SO_KEEPALIVE, 1);

            /**
             * set the real send/receive timeouts here now that we are connected
             */
            $timeval = [
                'sec' => 0,
            ];
            if($timeout >= 1000){
                $ts_seconds = $timeout / 1000;
                $timeval["sec"] = floor($ts_seconds);
                $timeval["usec"] = ($ts_seconds - $timeval["sec"]) * 1000000;
            } else {
                $timeval["usec"] = $timeout * 1000;
            }
            socket_set_option(
                $socket,
                SOL_SOCKET,
                SO_SNDTIMEO,
                $timeval
            );
            socket_set_option(
                $socket,
                SOL_SOCKET,
                SO_RCVTIMEO,
                $timeval
            );

        } else {
            $elapsed = round($elapsed, 4);
            if ($err !== 0 && $err !== 114 && $err !== 115) {
                $message = "Failed to connect to $host:$port. ($err: ".socket_strerror($err)."; after {$elapsed}ms)";
            } else {
                $message = "Failed to connect to $host:$port. (timed out after {$elapsed}ms)";
            }
            throw new CommunicationException($message, $err);
        }
        return $socket;
    }

    /**
     * Get the last socket error.
     *
     * @return array
     */
    protected function getLastError(): array
    {
        $code = socket_last_error($this->socket);
        $message = socket_strerror($code);
        return compact('code', 'message');
    }

    /**
     */
    public function collectErrors(): array
    {
        $maxRetryCount = 3;
        $retry = 0;
        while ($retry < $maxRetryCount) {
            $retry++;
            try {
                $response = $this->readStatus();
                return self::fetchErrors($response);
            } catch (CommunicationException $exception) {
                sleep(3);
                if ($maxRetryCount === $retry) {
                    return ['Cannot connect'];
                }
            } catch (Exception $exception) {
                Yii::error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
                sleep(3);
                if ($maxRetryCount >= $retry) {
                    return [$exception->getMessage()];
                }
            }
        }
        return [];
    }

    private static function fetchErrors(string $response): array
    {
        $list = ArrayHelper::map(self::ERROR_HEALTH_LIST, 'code', 'label');
        $response = $list[$response] ?? 'Undefined code - ' . $response;
        return [$response];
    }

    public function isPrinterReady(): bool
    {
        try {
            return $this->readStatus() === self::STATUS_READY_CODE;
        } catch (CommunicationException $exception) {
            return false;
        } catch (Exception $exception) {
            Yii::error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
        }
        return false;
    }

    /**
     * @return string
     */
    private function readStatus(): string
    {
        /** Note: Before using this command, the “^XSET,IMMEDIATE”
         * (Set immediate response on/off)
         * command should be turned on.
         */
        $this->send('^XSET,IMMEDIATE,1');
        return trim($this->sendAndRead('~S,CHECK'));
    }
}
