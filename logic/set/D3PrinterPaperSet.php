<?php

namespace d3yii2\d3printer\logic\set;

/**
 * Class D3PrinterPaperSet
 * @package d3yii2\d3printer\logic\set
 */
class D3PrinterPaperSet extends D3PrinterSet
{
    /**
     * @return string
     */
    protected function getConnectionUrl(): string
    {
        return $this->accessSettings->getPaperSetupUrl();
    }
    
    /**
     * @return string[]
     */
    public function getSendAttributes(): array
    {
        return [
            'okSet' => 'Apply',
            'sizePromptSupported' => 'no',
            'DefaultPaperSize' => '14',
            'DefaultPaperType' => '27',
            'ManualFeed' => 'EWS_OFF',
            'SizePrompt' => 'EWS_OFF',
            'Duplex' => 'EWS_OFF',
            'Tray1Size' => '16',
            'Tray1Type' => '1',
            'PaperOutHandling' => 'EWS_OFF',
        ];
    }
}
