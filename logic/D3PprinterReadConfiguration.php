<?php

namespace d3yii2\d3printer\logic;

use yii\base\Exception;

/**
 * Class D3PprinterReadConfiguration
 * @package d3yii2\d3printer\logic
 */
class D3PprinterReadConfiguration extends D3PprinterRead
{
    // Paper Settings
    public const DEEFAULT_PAPER_SIZE = 'paper_size';
    public const DEEFAULT_PAPER_TYPE = 'paper_type';
    public const TRAY1_SIZE = 'tray1_size';
    public const TRAY1_TYPE = 'tray1_type';
    public const PAPER_OUT_ACTION = 'paper_out_action';
    public const MANUAL_FEED = 'manual_feed';
    public const DUPLEX = 'duplex';
    public const BIND = 'bind';
    
    // Print settings
    public const AUTO_CONTINUE = 'auto_continue';
    public const NUMBER_OF_COPIES = 'number_of_copies';
    public const COURIER_FONT = 'courier_font';
    public const ORIENTATION = 'orientation';
    public const MONOCHROME_RET = 'monochrome_ret';
    public const WIDE_A4 = 'wide_a4';
    public const A5_FEED_ORIENTATION = 'a5_feed_orientation';
    public const MONOCHROME_RESOLUTION = 'monochrome_resolution';
    public const MONOCHROME_BITS_PER_PIXEL = 'monochrome_bits_per_pixel';
    public const ECONOMODE = 'economode';
    public const PRINT_DENSITY = 'print_density';
    public const IO_TIMEOUT = 'io_timeout';
    public const JAM_RECOVERY = 'jam_recovery';
    public const PERSONALITY = 'personality';
    public const PRINT_PS_ERRORS = 'print_ps_errors';
    
    // Energy Settings
    public const SLEEP_AFTER = 'sleep_after';
    public const SHUT_DOWN_AFTER = 'shut_down_after';
    public const DELAY_SHUT_DOWN = 'delay_shut_down';
    
    /**
     * @return string
     */
    public function getPrinterPageUrl(): string
    {
        return 'http://cewood.weberp.loc/printer/HomeDeviceConfig.html';
    }
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function getPrintSettings(): ?array
    {
        $settingsKeys = [
            0 => self::AUTO_CONTINUE,
            1 => self::NUMBER_OF_COPIES,
            2 => self::COURIER_FONT,
            3 => self::ORIENTATION,
            4 => self::MONOCHROME_RET,
            5 => self::WIDE_A4,
            6 => self::A5_FEED_ORIENTATION,
            7 => self::MONOCHROME_RESOLUTION,
            8 => self::MONOCHROME_BITS_PER_PIXEL,
            9 => self::ECONOMODE,
            10 => self::PRINT_DENSITY,
            11 => self::IO_TIMEOUT,
            12 => self::JAM_RECOVERY,
            13 => self::PERSONALITY,
            14 => self::PRINT_PS_ERRORS,
        ];
        
        $settings = [];
        
        $settingsNodes = $this->parse("(//td[@class='rightContentPane']//table[@class='mainContentArea'])[5]/tr/td[2]");
        
        if (0 === count($settingsNodes)) {
            echo 'Cannot parse print settings nodes';
            return null;
        }
        
        foreach ($settingsNodes as $i => $node) {
            $settings[$settingsKeys[$i]] = trim($node->nodeValue);
        }
        
        return $settings;
    }
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function getPaperSettings(): ?array
    {
        $settingsKeys = [
            0 => self::DEEFAULT_PAPER_SIZE,
            1 => self::DEEFAULT_PAPER_TYPE,
            2 => self::TRAY1_SIZE,
            3 => self::TRAY1_TYPE,
            4 => self::PAPER_OUT_ACTION,
            5 => self::MANUAL_FEED,
            6 => self::DUPLEX,
            7 => self::BIND,
        ];
        
        $settings = [];
        
        $settingsNodes = $this->parse("(//td[@class='rightContentPane']//table[@class='mainContentArea'])[4]/tr/td[2]");
        
        if (0 === count($settingsNodes)) {
            echo 'Cannot parse paaper settings nodes';
            return null;
        }
        
        foreach ($settingsNodes as $i => $node) {
            $settings[$settingsKeys[$i]] = trim($node->nodeValue);
        }
        
        return $settings;
    }
    
    /**
     * @return string|null
     * @throws Exception
     */
    public function getEnergySettings(): ?array
    {
        $settingsKeys = [
            0 => self::SLEEP_AFTER,
            1 => self::SHUT_DOWN_AFTER,
            2 => self::DELAY_SHUT_DOWN,
        ];
        
        $settings = [];
        
        $settingsNodes = $this->parse("(//td[@class='rightContentPane']//table[@class='mainContentArea'])[7]/tr/td[2]");
        
        if (0 === count($settingsNodes)) {
            echo 'Cannot parse energy settings nodes';
            return null;
        }
        
        foreach ($settingsNodes as $i => $node) {
            $settings[$settingsKeys[$i]] = trim($node->nodeValue);
        }
        
        return $settings;
    }
}