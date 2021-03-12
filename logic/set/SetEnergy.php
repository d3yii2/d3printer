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
            'ShutDown_timer_changed' => 'yes',
            'AutoOff_timer_changed' => 'yes',
            'aoao_active_off_supported' => '1',
            'AutoOff' => 'EWS_AO_1Hour',
            'ShutDown' => 'EWS_SD_8Hours',
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
