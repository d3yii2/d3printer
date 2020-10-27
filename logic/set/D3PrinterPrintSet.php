<?php

namespace d3yii2\d3printer\logic\set;

class D3PrinterPrintSet extends D3PrinterSet
{
    /**
     * @return string
     */
    protected function getConnectionUrl(): string
    {
        return $this->accessSettings->getPrintSetupUrl();
    }
    
    /**
     * @return string[]
     */
    public function getSendAttributes(): array
    {
        return [
            'Copies' => '1',
            'WideA4' => 'EWS_NO',
            'A5FeedOrientation' => 'Portrait',
            'Courier' => 'Courier_Regular',
            'Orientation' => 'orient_Portrait',
            'Apply' => 'Apply',
        ];
    }
}
