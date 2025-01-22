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

    protected $convertEncoding = true;

    /**
     * D3PrinterRead constructor.
     * @throws Exception
     */
    public function __construct($url)
    {
        parent::__construct($url);

        libxml_use_internal_errors(true);

        $this->dom = new DOMDocument();
        $this->dom->encoding = 'UTF-8'; // output UTF-8

        $content = parent::connect();

        if ($this->convertEncoding) {
            $content = mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8');
        }

        $content = preg_replace('/<(\d)/i', '&lt;$1', $content );

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

    public function getSanitizedValue(DOMNode $node)
    {
        return preg_replace("/†|\r\n|\r\n\r\n|\r\r|\n\n| +/", '', $node->nodeValue);
    }
}
