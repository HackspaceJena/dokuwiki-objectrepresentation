<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 12.06.14
 * Time: 21:55
 */

namespace enko\dokuwiki\objectrepresentation;


class DokuWikiPage extends DokuWikiNode
{
    /**
     * @param $filename
     * @param null $parent
     */
    public function __construct($filename, $parent = null)
    {
        parent::__construct($filename, $parent);
        $this->content = file_get_contents($this->filename);
        if (($this->name == 'start') && ($this->parent->name != 'root')) {
            $this->parent->content = $this->content;
        }
        $metadata = p_get_metadata($this->getFullID());
        foreach ($metadata as $key => $value) {
            $this->setMetaData($key, $value);
        }

    }
}