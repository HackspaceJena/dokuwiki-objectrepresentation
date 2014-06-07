<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 12.06.14
 * Time: 21:53
 */

namespace enko\dokuwiki\objectrepresentation;

abstract class DokuWikiNode
{
    /** @var  String */
    protected $filename;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $content;

    /** @var  \ArrayObject */
    protected $metadata;

    /** @var  \ArrayObject */
    public $metadata_extractor;

    /** @var DokuWikiNameSpace */
    protected $parent = null;


    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return $this
     */
    public function setContent($content = '')
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setMetaData($key, $value)
    {
        $this->metadata[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getMetaData($key)
    {
        return $this->metadata[$key];
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $filename
     * @param null $parent
     */
    function __construct($filename, $parent = null)
    {
        $this->filename = $filename;
        $this->parent = $parent;
        $this->metadata = new \ArrayObject();
        if (is_null($parent) && is_dir($filename)) {
            $this->name = 'root';
        } else {
            $parts = pathinfo($filename);
            if (is_dir($filename)) {
                $this->name = $parts['basename'];
            } else {
                $this->name = $parts['filename'];
            }
        }
    }

    /**
     * @return String
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getFullID()
    {
        $path = array();
        $node = $this;
        while ($parent = $node->parent) {
            if ($parent->name != 'root') {
                $path[] = $parent->name;
            }
            $node = $parent;
        }
        $path = array_reverse($path);
        if ($this->name != 'root') {
            $path[] = $this->name;
        }
        return implode(':', $path);
    }

}