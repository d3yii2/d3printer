<?php

namespace d3yii2\d3printer\logic\set;

use d3yii2\d3printer\logic\settings\EnergySettings;

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
        $units = [
            EnergySettings::HOUR => 'Hours',
            EnergySettings::HOURS => 'Hours',
            EnergySettings::MINUTE => 'Min',
            EnergySettings::MINUTES => 'Min',
        ];
        
        return [
            'ShutDown_timer_changed' => 'yes',
            'AutoOff_timer_changed' => 'yes',
            'aoao_active_off_supported' => '1',
            'AutoOff' => 'EWS_AO_' . EnergySettings::DEFAULT_SLEEP_AFTER_VALUE . $units[EnergySettings::DEFAULT_SLEEP_AFTER_UNIT],
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
