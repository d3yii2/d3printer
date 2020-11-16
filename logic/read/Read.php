<?php

namespace d3yii2\d3printer\logic\read;

use d3yii2\d3printer\logic\Connect;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use yii\base\Exception;

/**
 * Class Read
 * @package d3yii2\d3printer\logic\read
 */
class Read extends Connect
{
    /** @var DOMXPath */
    protected $xpath;
    
    /** @var DOMDocument */
    protected $dom;
    
    /**
     * D3PrinterRead constructor.
     * @throws Exception
     */
    public function __construct($url)
    {
        parent::__construct($url);
        
        $this->dom = new DOMDocument();
        
        $content = parent::connect();
        
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
