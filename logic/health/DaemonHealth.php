<?php

namespace d3yii2\d3printer\logic\health;

/**
 * Class DaemonHealth
 * @package d3yii2\d3printer\logic\health
 */
class DaemonHealth extends Health
{
    public $linuxDaemonName;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_FAILED = 'failed';
    public const STATUS_UNKNOW = 'unknow';

    private $rawStatus;

    public function init()
    {
        parent::init();

        $this->logger->addInfo(
            sprintf(
                'Daemon Health. Printer: %s (%s). Daemon name: %s',
                $this->printerName,
                $this->printerCode,
                $this->linuxDaemonName
            )
        );
    }

    public function getStatus(): string
    {
        return self::STATUS_ACTIVE;
        $command = sprintf('systemctl status %s', $this->linuxDaemonName);
        $this->logger->addInfo($command);
        $output = shell_exec($command);
        $this->rawStatus = $output;
        $status = '-';
        if (preg_match('/Active:\s+(.*?)\s+\(/', $output, $matches)) {
            $status = $matches[1];
            if (in_array($status, [
                self::STATUS_ACTIVE,
                self::STATUS_INACTIVE,
                self::STATUS_FAILED,
            ])) {
                return $status;
            };
        }
        $this->logger->addError(
            sprintf(
                'Cannot parse daemon status value: %s. Printer: %s (%s). Daemon name: "%s". Command: "%s"',
                $status,
                $this->printerName,
                $this->printerCode,
                $this->linuxDaemonName,
                $command
            )
        );

        return self::STATUS_UNKNOW;
    }

    public function statusOk(): bool
    {
        if($this->getStatus() === self::STATUS_ACTIVE) {
            return true;
        }

        $status = $this->getStatus();
        $statusOutput = $status !== DaemonHealth::STATUS_UNKNOW ? $status : sprintf('%s (%s)', $status, $this->getRawStatus());

        $this->logger->addError('Daemon looks down! Status: "' . $statusOutput . '"');

        return false;
    }

    public function getRawStatus(): ?string
    {
        return $this->rawStatus;
    }
}
