<?php
/**
 * Created by PhpStorm.
 * User: tim
 * Date: 12.06.14
 * Time: 22:49
 */

namespace enko\dokuwiki\objectrepresentation;


class DokuWikiChangeset {
    private $date;
    private $ip;
    private $type;
    private $id;
    private $user;
    private $sum;
    private $extra;
    private $content;
    private $page;

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return mixed
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * @return mixed
     */
    public function getSum()
    {
        return $this->sum;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    public function getPage() {
        return $this->page;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    function __construct($date,$extra, $id, $ip, $sum, $type, $user, DokuWikiPage $page)
    {
        $this->date = new \DateTime();
        $this->date->setTimestamp($date);
        $this->extra = $extra;
        $this->id = $id;
        $this->ip = $ip;
        $this->sum = $sum;
        $this->type = $type;
        $this->user = $user;
        $this->content = rawWiki($id,$this->date->format('U'));
        $this->page = $page;
    }


} 