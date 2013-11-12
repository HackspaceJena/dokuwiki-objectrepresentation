<?php
/**
 * Created by PhpStorm.
 * User: hana
 * Date: 12.11.13
 * Time: 22:09
 */

class DokuWikiNode {

}

class DokuWikiNameSpace extends DokuWikiNode {

}

class DokuWikiIterator {

  private  $nodes = array();

  function __construct () {
    global $conf;
    $basedir = $conf['datadir'] . DIRECTORY_SEPARATOR;


  }
}