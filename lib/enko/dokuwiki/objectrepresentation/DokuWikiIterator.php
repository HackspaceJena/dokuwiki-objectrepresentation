<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 12.06.14
 * Time: 21:56
 */

namespace enko\dokuwiki\objectrepresentation;


class DokuWikiIterator
{
    /**
     * @var DokuWikiNameSpace
     */
    private $root;

    /**
     * @param callable $callback
     */
    public function runMetadataExtractor(Callable $callback)
    {
        $this->all($callback);
    }

    /**
     * @param DokuWikiNameSpace $ns
     * @param callable $callback
     */
    private function _all(DokuWikiNameSpace $ns, Callable $callback)
    {
        $callback($ns);
        foreach ($ns->nodes as $node) {
            /** $node DokuWikiNode */
            if ($node instanceof DokuWikiPage) {
                $callback($node);
            }
            if ($node instanceof DokuWikiNameSpace) {
                $this->_all($node, $callback);
            }
        }
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function all(Callable $callback)
    {
        $this->_all($this->root, $callback);
        return $this;
    }

    /**
     *
     */
    public function __construct()
    {
        global $conf;
        $basedir = $conf['datadir'];

        $this->root = new DokuWikiNameSpace($basedir);
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->root->toString();
    }

    public function getRoot()
    {
        return $this->root;
    }
}