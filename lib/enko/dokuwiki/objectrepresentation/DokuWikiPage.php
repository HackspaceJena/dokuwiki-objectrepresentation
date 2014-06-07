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
    public $ChangeLog;

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
        // extract changelog
        $this->ChangeLog = new \ArrayObject();
        $file = metaFN($this->getFullID(), '.changes');
        if (file_exists($file)) {
            $changelog_entries = explode("\n", file_get_contents($file));
            foreach ($changelog_entries as $raw_entry) {
                $entry = parseChangelogLine($raw_entry);
                $changelog = new DokuWikiChangeset($entry['date'], $entry['extra'], $entry['id'], $entry['ip'], $entry['sum'], $entry['type'], $entry['user']);
                $this->ChangeLog->append($changelog);
            }
        }

    }
}