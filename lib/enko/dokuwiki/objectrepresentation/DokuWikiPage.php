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
    public function __construct($filename, $parent = null,$loadChangesets = false, \DateTime $maxChangeSetAge = null)
    {
        parent::__construct($filename, $parent,$loadChangesets,$maxChangeSetAge);
        $this->content = file_get_contents($this->filename);
        if (($this->name == 'start') && ($this->parent->name != 'root')) {
            $this->parent->content = $this->content;
        }
        $metadata = p_get_metadata($this->getFullID());
        foreach ($metadata as $key => $value) {
            $this->setMetaData($key, $value);
        }
        $this->ChangeLog = new \ArrayObject();
        if ($this->loadChangesets) {
            // extract changelog
            $file = metaFN($this->getFullID(), '.changes');
            if (file_exists($file)) {
                $changelog_entries = explode("\n", file_get_contents($file));
                foreach ($changelog_entries as $raw_entry) {
                    $entry = parseChangelogLine($raw_entry);
                    if ((!is_null($this->maxChangeSetAge)) && ($this->maxChangeSetAge->format('U') > $entry['date']))
                        continue;
                    $changelog = new DokuWikiChangeset($entry['date'], $entry['extra'], $entry['id'], $entry['ip'], $entry['sum'], $entry['type'], $entry['user']);
                    $this->ChangeLog->append($changelog);
                }
            }
        }

    }
}