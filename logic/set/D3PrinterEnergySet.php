<?php

namespace d3yii2\d3printer\logic\set;

/**
 * Class D3PrinterEnergySet
 * @package d3yii2\d3printer\logic\set
 */
class D3PrinterEnergySet extends D3PrinterSet
{
    /**
     * @return string
     */
    protected function getConnectionUrl(): string
    {
        return $this->accessSettings->getEnergySetupUrl();
    }
    
    /**
     * @return string[]
     */
    public function getSendAttributes(): array
    {
        return [
            'ShutDown_timer_changed' => 'no',
            'AutoOff_timer_changed' => 'no',
            'aoao_active_off_supported' => '1',
            'AutoOff' => 'EWS_AO_15Min',
            'ShutDown' => 'EWS_SD_4Hours',
            'delayShutDown' => 'on',
            'Apply' => 'Apply',
        ];
    }
}
