<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 12.06.14
 * Time: 21:54
 */

namespace enko\dokuwiki\objectrepresentation;


class DokuWikiNameSpace extends DokuWikiNode
{

    /** @var \ArrayObject */
    public $nodes;

    /**
     * @param $path
     * @param null $parent
     */
    function __construct($path, $parent = null)
    {
        parent::__construct($path, $parent);
        $files = dir($path);

        $this->nodes = new \ArrayObject();

        while (($realfile = $files->read())) {
            $node = null;
            $file = $path . DIRECTORY_SEPARATOR . $realfile;
            if (is_dir($file)) {
                if (!(($realfile == '.') or ($realfile == '..'))) {
                    $node = new DokuWikiNameSpace($file, $this);
                }
            } else {
                $node = new DokuWikiPage($file, $this);
            }
            if ($node) {
                $this->nodes->append($node);
            }
        }
    }

    /**
     * @return string
     */
    public function toString()
    {
        $retval = '';
        foreach ($this->nodes as $node) {
            /** @var $node DokuWikiNode */
            if ($this->name == 'root') {
                $retval .= $node->toString() . "\n";
            } else {
                $retval .= $this->name . ":" . $node->toString() . "\n";
            }
        }
        return $retval;
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function hasChild($nodeName)
    {
        if ($this->nodes->count() > 0) {
            foreach ($this->nodes as $node) {
                /** @var DokuWikiNode $node */
                if (($node instanceof DokuWikiPage) && ($node->getName() == $nodeName)) {
                    return $node;
                }
            }
        }
        return null;
    }

}