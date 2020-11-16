<?php

namespace d3yii2\d3printer\logic\set;

/**
 * Class SetEnergy
 * @package d3yii2\d3printer\logic\set
 */
class SetEnergy extends Set
{
    /**
     * @return string[]
     */
    public function getSendAttributes(): array
    {
        return [
            'ShutDown_timer_changed' => 'no',
            'AutoOff_timer_changed' => 'yes',
            'aoao_active_off_supported' => '1',
            'AutoOff' => 'EWS_AO_15Min',
            'ShutDown' => 'EWS_SD_4Hours',
            'delayShutDown' => 'on',
            'Apply' => 'Apply',
        ];
    }
    
    /**
     * @return string
     */
    protected function getConnectionUrl(): string
    {
        return $this->accessSettings->getEnergySetupUrl();
    }
}
