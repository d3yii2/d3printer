<?php

declare(strict_types=1);

namespace d3yii2\d3printer\components;

use Exception;
use Yii;
use yii\base\InvalidConfigException;
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
    private ?string $host;
    private ?int $port;
    private ?string $printerName = null;

    /**
     * Create an instance.
     *
     * @param string $host
     * @param int $port
     * @param string|null $printerName
     * @throws InvalidConfigException
     */
    public function __construct(
        string $host,
        int $port = 9100,
        string $printerName = null
    )
    {
        if (!$printerName) {
            throw new InvalidConfigException('Printer name cannot be empty');
        }
        $this->host = $host;
        $this->port = $port;
        $this->printerName = $printerName;
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
     * @throws InvalidConfigException
     */
    public static function printer(string $host, int $port = 9100): self
    {
        return new self($host, $port);
    }

    /**
     * Connect to printer.
     *
     * @throws CommunicationException if the connection fails.
     */
    protected function connect(): void
    {
        $this->socket = self::socketConnectTimeout($this->host, $this->port, 500);
    }

    /**
     * Close connection to printer.
     */
    public function disconnect(): void
    {
        if ($this->socket) {
            @socket_close($this->socket);
        }
    }

    /**
     * Send ZPL data to printer.
     *
     * @param string $zpl
     * @throws CommunicationException if writing to the socket fails.
     */
    public function send(string $zpl): void
    {
        if (!$this->socket) {
            $this->connect();
        }
        if (false === @socket_write($this->socket, $zpl . chr(13))) {
            $error = $this->getLastError();
            throw new CommunicationException($error['message'], $error['code']);
        }
    }

    public function sendAndRead(string $zpl, $length = 1024): string
    {
        if (!$this->socket) {
            $this->connect();
        }
        $this->send($zpl);
        $response = @socket_read($this->socket, $length);
        if ($response === false) {
            $error = $this->getLastError();
            $this->disconnect();

            throw new CommunicationException($error['message'], $error['code']);
        }
//        $this->disconnect();
        return $response;
    }

    private static function socketConnectTimeout(string $host, int $port, int $timeout=100){

        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

        /**
         * Set the send and receive timeouts super low so that socket_connect
         * will return to us quickly. We then loop and check the real timeout
         * and check the socket error to decide if it's not connected yet or not.
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
        if (!$this->socket) {
            $this->connect();
        }
        $code = socket_last_error($this->socket);
        $message = socket_strerror($code);
        return compact('code', 'message');
    }

    /**
     */
    public function processStatus(): PrinterStatus
    {
        $maxRetryCount = 3;
        $retry = 0;
        while ($retry < $maxRetryCount) {
            $retry++;
            try {
                $response = $this->readStatus();
                $printerStatus = new GodexPrinterStatus($this->printerName, $response);
                break;
            } catch (CommunicationException $exception) {
                sleep(3);
                if ($maxRetryCount === $retry) {
                    $printerStatus = new GodexPrinterStatus($this->printerName);
                    $printerStatus->setCanNotConnect();
                    break;
                }
            } catch (Exception $exception) {
                Yii::error($exception->getMessage() . PHP_EOL . $exception->getTraceAsString());
                sleep(3);
                if ($maxRetryCount >= $retry) {
                    $printerStatus = new GodexPrinterStatus($this->printerName);
                    $printerStatus->setOtherError($exception->getMessage());
                    break;
                }
            }
        }
        if (!isset($printerStatus)) {
            $printerStatus = new GodexPrinterStatus($this->printerName);
            $printerStatus->setOtherError('Mistiska');
        }
        return $printerStatus;
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

    public function print(string $content): bool
    {
        if (!$this->isPrinterReady()) {
            return false;
        }
        /** @todo jāpārbaua, varbūt atgriez kaut ko, ja nevar izdrukāt */
        $this->send($content);
        return true;
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
