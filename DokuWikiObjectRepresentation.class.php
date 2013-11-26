<?php
/**
 * Created by PhpStorm.
 * User: hana
 * Date: 12.11.13
 * Time: 22:09
 */

abstract class DokuWikiNode {
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

  /** @var  ArrayObject */
  protected $metadata;

  /** @var  ArrayObject */
  public $metadata_extractor;

  /** @var DokuWikiNameSpace */
  protected $parent = null;


  /**
   * @return string
   */
  public function getContent() {
    return $this->content;
  }

  /**
   * @param string $content
   * @return $this
   */
  public function setContent($content = '') {
    $this->content = $content;
    return $this;
  }

  /**
   * @param $key
   * @param $value
   */
  public function setMetaData($key,$value) {
    $this->metadata[$key] = $value;
  }

  /**
   * @param $key
   * @return mixed
   */
  public function getMetaData($key) {
    return $this->metadata[$key];
  }

  /**
   * @return mixed
   */
  public function getName() {
    return $this->name;
  }

  /**
   * @param $filename
   * @param null $parent
   */
  function __construct ($filename, $parent = null) {
    $this->filename = $filename;
    $this->parent = $parent;
    if (is_null ($parent) && is_dir ($filename)) {
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
  public function getFilename () {
    return $this->filename;
  }

  /**
   * @return string
   */
  public function toString () {
    return $this->name;
  }

  /**
   * @return string
   */
  public function getFullID() {
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
    return implode(':',$path);
  }

}

/**
 * Class DokuWikiNameSpace
 */
class DokuWikiNameSpace extends DokuWikiNode {

  /** @var \ArrayObject  */
  public $nodes;

  /**
   * @param $path
   * @param null $parent
   */
  function __construct ($path, $parent = null) {
    parent::__construct ($path, $parent);
    $files = dir ($path);

    $this->nodes = new ArrayObject();

    while (($realfile = $files->read ())) {
      $node = null;
      $file = $path . DIRECTORY_SEPARATOR . $realfile;
      if (is_dir ($file)) {
        if (!(($realfile == '.') or ($realfile == '..'))) {
          $node = new DokuWikiNameSpace($file, $this);
        }
      } else {
        $node = new DokuWikiPage($file, $this);
      }
      if ($node) {
        $this->nodes->append ($node);
      }
    }
  }

  /**
   * @return string
   */
  public function toString () {
    $retval = '';
    foreach ($this->nodes as $node) {
      if ($this->name == 'root') {
        $retval .= $node->toString() . "\n";
      } else {
        $retval .= $this->name . ":" . $node->toString() . "\n";
      }
    }
    return $retval;
  }

}

/**
 * Class DokuWikiPage
 */
class DokuWikiPage extends DokuWikiNode {
  /**
   * @param $filename
   * @param null $parent
   */
  public function __construct($filename, $parent = null) {
    parent::__construct($filename,$parent);
    $this->content = file_get_contents($this->filename);
    if (($this->name == 'start') && ($this->parent->name != 'root')) {
      $this->parent->content = $this->content;
    }
  }
}

/**
 * Class DokuWikiIterator
 */
class DokuWikiIterator {

  /**
   * @var DokuWikiNameSpace
   */
  private $root;

  /**
   * @param callable $callback
   */
  public function runMetadataExtractor(Callable $callback) {
    $this->all($callback);
  }

  /**
   * @param DokuWikiNameSpace $ns
   * @param callable $callback
   */
  private function _all(DokuWikiNameSpace $ns, Callable $callback) {
    $callback($ns);
    foreach($ns->nodes as $node) {
      /** $node DokuWikiNode */
      $callback($node);
      if ($node instanceof DokuWikiNameSpace) {
        $this->_all($node,$callback);
      }
    }
  }

  /**
   * @param callable $callback
   * @return $this
   */
  public function all(Callable $callback) {
    $this->_all($this->root,$callback);
    return $this;
  }

  /**
   *
   */
  public function __construct () {
    global $conf;
    $basedir = $conf['datadir'];

    $this->root = new DokuWikiNameSpace($basedir);
  }

  /**
   * @return string
   */
  public function toString () {
    return $this->root->toString();
  }
}