<?php

namespace d3yii2\d3printer\logic\read;

use d3yii2\d3printer\logic\D3Printer;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use yii\base\Exception;

/**
 * Class D3PrinterRead
 * @package d3yii2\d3printer\logic
 */
class D3PrinterRead extends D3Printer
{
    /** @var DOMXPath */
    protected $xpath;
    
    /** @var DOMDocument */
    protected $dom;
    
    /**
     * D3PrinterRead constructor.
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->dom = new DOMDocument();
        $this->init();
    }
    
    /**
     * Fill the DOM object with content returned from printer page response
     * @throws Exception
     */
    public function init(): void
    {
        $content = parent::connect($this->getConnectionUrl());
        
        libxml_use_internal_errors(true);
        
        if (false === $this->dom->loadHTML($content)) {
            throw new Exception('Cannot load HTML into DOMDocument');
        }
        
        libxml_clear_errors();
        
        $this->xpath = new DOMXPath($this->dom);
    }
    
    /**
     * Extract specific part of HTML via DOMXpath
     * @param string $expr
     * @param DOMNode|null $contextNode
     * @return DOMNodeList
     * @throws Exception
     */
    public function parse(string $expr, ?DOMNode $contextNode = null): DOMNodeList
    {
        if (false === $nodeList = $this->xpath->query($expr, $contextNode)) {
            throw new Exception('Cannot parse content or context node invalid');
        }
        
        return $nodeList;
    }
    
    /**
     * @param DOMNode $node
     * @return string
     */
    public function getParsedHtml(DOMNode $node): string
    {
        $dom = new DOMDocument();
        $import = $dom->importNode($node, true);
        return $dom->saveHTML();
    }
}
