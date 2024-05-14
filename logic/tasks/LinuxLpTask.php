<?php


namespace d3yii2\d3printer\logic\tasks;

use d3system\exceptions\D3TaskException;


class LinuxLpTask extends PrinterTask
{
    public function connect(): void
    {
    }

    public function authorize(): void
    {
    }

    public function disconnect(): void
    {
    }

    /**
     * @param string $filePath
     * @param int $tryTimes try put file times
     * @param int $usleep sleep in microseconds between try. default 0.5 second
     * @throws D3TaskException
     */
    public function putFile(string $filePath, int $tryTimes = 5, int $usleep = 1000000): void
    {
        usleep($usleep);
        $tryCounter = 1;
        while ($tryCounter <= $tryTimes) {

            if (@exec(
                'lp -d ' . $this->printerName . ' "' . $filePath . '"',
                $output,
                $result_code
            )) {
                return;
            }
            /**
             * @todo jāpieliek kļūdu pastrāde pēc $output un $result_code
             */
            $tryCounter ++;
            usleep($usleep);
        }

        throw new D3TaskException('Can not ftp_put! ' . PHP_EOL
            . 'file: ' . $filePath . PHP_EOL
            //. implode(PHP_EOL, $errors)
        );
    }
}