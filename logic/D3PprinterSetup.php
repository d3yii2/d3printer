<?php

namespace d3yii2\d3printer\logic;

use Yii;
use yii\web\HttpException;

/**
 * Class D3Pprinter
 * @package d3yii2\d3printer\logic
 */
class D3PprinterSetup extends D3PprinterHealth
{
    protected $config;
    
    /**
     * D3Pprinter constructor.
     */
    public function __construct()
    {
        $this->config = new D3PprinterReadConfiguration();
    }
}
