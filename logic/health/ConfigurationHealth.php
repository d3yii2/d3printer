<?php

namespace d3yii2\d3printer\logic\health;

use d3yii2\d3printer\logic\read\ReadConfiguration;
use d3yii2\d3printer\logic\set\SetEnergy;
use d3yii2\d3printer\logic\set\SetPaper;
use d3yii2\d3printer\logic\set\SetPrint;
use d3yii2\d3printer\logic\settings\EnergySettings;
use d3yii2\d3printer\logic\settings\PaperSettings;
use d3yii2\d3printer\logic\settings\PrintSettings;
use GuzzleHttp\Exception\GuzzleException;
use yii\base\Exception;

/**
 * Class ConfigurationHealth
 * @package d3yii2\d3printer\logic\health
 */
class ConfigurationHealth extends Health
{
    protected $configuration;
    
    public function init()
    {
        parent::init();
        $this->device = new ReadConfiguration($this->accessSettings['configuration_info_url']);
        $this->logger->addInfo('Configuration Health');
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function paperSizeOk(): bool
    {
        $settings = $this->device->paperSettings();
        
        if (!$settings) {
            $this->logger->addError('Cannot parse paper settings');
        }
        
        if (PaperSettings::DEFAULT_PAPER_SIZE !== $settings['tray1_size']) {
            $this->logger->addInfo("Paper settings don't match: " . PaperSettings::DEFAULT_PAPER_SIZE . ' | ' . $settings['paper_size']);
            return false;
        }
        
        $this->logger->addInfo('Paper OK (' . $settings['tray1_size'] . ')');
        
        return true;
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function energySleepOk(): bool
    {
        $settings = $this->device->energySettings();
        
        if (!$settings) {
            $this->logger->addError('Cannot parse energy settings');
        }
        
        //@FIXME
        // Atbilde ir formātā: x Minute[s]. Ja uzdots stundās, salīdzināšana nestrādās 
        $printerSleepData = explode(' ', $settings['sleep_after']);
        
        if (EnergySettings::DEFAULT_SLEEP_AFTER !== $printerSleepData[0]) {
            $this->logger->addInfo("Energy sleep setting don't match: " . EnergySettings::DEFAULT_SLEEP_AFTER . ' | ' . $printerSleepData[0]);
            return false;
        }
        
        $this->logger->addInfo('Energy OK (Sleep after ' . $settings['sleep_after'] . ' minutes)');
        return true;
    }
    
    /**
     * @return bool
     * @throws Exception
     */
    public function printOrientationOk(): bool
    {
        $settings = $this->device->printSettings();
        
        if (!$settings) {
            $this->logger->addError('Cannot parse print settings');
        }
        
        if (PrintSettings::DEFAULT_ORIENTATION !== $settings['orientation']) {
            $this->logger->addInfo("Orientation setting don't match: " . PrintSettings::DEFAULT_ORIENTATION . ' | ' . $settings['orientation']);
            return false;
        }
        
        $this->logger->addInfo('Print orientation OK (' . $settings['orientation'] . ')');
        return true;
    }
    
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function updatePaperConfig(): void
    {
        $paper = new SetPaper();
        if ($paper->update($this->accessSettings['paper_setup_url'])) {
            $this->logger->addInfo('Paper configuration updated to: ' . PHP_EOL . print_r($paper->getSentData(),
                    true));
        }
    }
    
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function updateEnergyConfig(): void
    {
        $energy = new SetEnergy();
        if ($energy->update($this->accessSettings['energy_setup_url'])) {
            $this->logger->addInfo('Energy configuration updated to: ' . PHP_EOL . print_r($energy->getSentData(),
                    true));
        }
    }
    
    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function updatePrintConfig(): void
    {
        $print = new SetPrint();
        if ($print->update($this->accessSettings['print_setup_url'])) {
            $this->logger->addInfo('Print configuration updated to: ' . PHP_EOL . print_r($print->getSentData(),
                    true));
        }
    }
}
