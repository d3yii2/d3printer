[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)

# Printer Monitoring"

## Features

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ composer require d3yii2/d3printer "*"
```

or add to composer require part 

```
"d3yii2/d3printer": "*"
```

## Printers define as compnents

For each type printer create own class
See components/D3PrinterGodex5000.php

## Labels
Labels create as object. Example:

```php
namespace components\printers;

use d3yii2\d3printer\components\G500LabelBase;
use d3yii2\d3printer\components\G500LabelInterface;
use Dbr\Ezpl\Builder;
use Dbr\Ezpl\Command\CommandPipe;
use Dbr\Ezpl\Command\Image\QRCode;

class G500ProductLabel extends G500LabelBase implements G500LabelInterface
{
    /** @var string */
    public $qrCode;

    /** @var $date */
    public $date;

    /** @var float */
    public $qnt;

    /** @var string */
    public $unitLabel;

    /** @var string */
    public $productName;

    public function createCommand(): Builder
    {
        return (new Builder(new CommandPipe()))
            ->resetMemory()
            ->setLabelHeight($this->labelHeight, 2)
            ->setLabelWidth($this->labelWidth)
            ->setDensity($this->density)
            ->copies($this->copies)
            ->labelStart()
            ->qrcode($this->createQrCode())
            ->text(14, 250, 10, 1, 1, 0, 0, $this->date)
            ->text(18, 250, 70, 1, 1, 0, 0, $this->qnt . ' ' . $this->unitLabel)
            ->text(10, 250, 200, 1, 1, 0, 0, $this->qrCode)
            ->text(14, 10, 260, 1, 1, 0, 0, $this->productName)
            ->labelEnd();
    }

    public function createQrCode(): QRCode
    {
        return (new QRCode($this->qrCode))
            ->setHorizontal(10)
            ->setVertical(10)
            ->setInputMode(QRCode::INPUT_MODE_MIXING)
            ->setType(QRCode::TYPE_ORIGINAL)
            ->setErrorLevel(QRCode::ERROR_CORRECTION_MEDIUM)
            ->setMaskingFactor(QRCode::MASKING_AUTO)
            ->setMultiple(10)
            ->setRotate(0);
    }
}
```

## Usage

```php 
require('../../../../autoload.php');
require('../../../../yiisoft/yii2/Yii.php');

use aluksne\app\components\printers\G500ProductLabel;
use d3yii2\d3printer\components\D3PrinterGodex5000;

$label = Yii::createObject([
    'class' => G500ProductLabel::class,
    'productName' => 'CLT60 C 3(20-20-20)V/V/5000/7000',
    'qrCode' => 'P100022342',
    'date' => '2021-09-20',
    'qnt' => 11.,
    'unitLabel' => 'M3',
]);

$printer = Yii::createObject([
    'class' => D3PrinterGodex5000::class,
    'printerIp' => '192.168.88.228',
]);

$printer->check();

echo $printer->getCheckResponseCode() . PHP_EOL;
echo $printer->getCheckResponseLabel() . PHP_EOL;

$printer->print($label);
```
## Health monitoring 
Define health component in app console config
```php
    'kaltePrinterHealth' => [
            'class' => '\d3yii2\d3printer\components\D3Printer',
            'printerCode' => 'officePrinter',
            'printerName' => 'Godex G500',
            'accessSettings' => [
                'home_url' => '<printer home URL>',
            ],
        ],
```
Read and save current state (files are located at [app path]/runtime/d3printer/[printerCode]
```php
     /usr/bin/php <sitepath>/yii d3printer/health-cron <printerCode>
```

Define printer state panels in app main config
```php
'panels' => [
    'printers' =>
        [
            [
                'route' => '/d3printer/info-panel/status',
                'params' => [
                    'printerComponent' => 'officePrinter',
                    'healthComponent' => 'officePrinterHealth',
                ]
            ],
            [
                'route' => '/d3printer/info-panel/status',
                'params' => [
                    'printerComponent' => 'homePrinter',
                    'healthComponent' => 'homePrinterHealth',
                ]
            ],
```
Show the state panel in app view 

```php
PanelWidget::widget(['name' => 'printers'])
```

## Examples
Get printer status, cartridge and drum by printer code
```php
 $printerComponent = D3Printer::getPrinterComponent('officePrinter');
 $deviceHealth = $printerComponent->deviceHealth();

 $status = $deviceHealth->getStatus();
 $cartridgeRemaining = $deviceHealth->getCartridgeRemaining();
 $drumRemaining = $deviceHealth->getDrumRemaining();
```

## Deamons for spool printer

### directory
/usr/lib/systemd/system

```ini
[Unit]
Description=Cewood Maza Pecapstrade printer spooler

[Service]
User=www-data
TimeoutStartSec=30
Restart=on-failure
Restart=always
RestartSec=5s
# MpPrinter - printer compnent name
ExecStart=php /home/chroot/websites/cewood/yii d3printer/ftp-print-daemon MpPrinter
StandardOutput=append:/home/chroot/websites/cewood/runtime/logs/mp/printer-spooler.log
StandardError=append:/home/chroot/websites/cewood/runtime/logs/mp/printer-spooler-error.log
                     /home/chroot/websites/cewood/runtime/logs/mp/
SyslogIdentifier=MpPrintDeamon

[Install]
WantedBy=multi-user.target

```

### install

systemctl daemon-reload 
systemctl enable cewood-mp-printer-spooler.service
systemctl start cewood-mp-printer-spooler.service

### diagnostic
journalctl -u cewood-mp-printer-spooler.service
check logfile /home/chroot/websites/cewood/runtime/logs/mp/printer-spooler-error.log
