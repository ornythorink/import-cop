<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeedCSV
 *
 * @ORM\Table(name="feedcsv")
 * @ORM\Entity(repositoryClass="AppBundle\Entity\FeedCSVRepository")
 */
class FeedCSV
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;


    /**
     * @var integer
     *
     * @ORM\Column(name="id_store_api", type="integer")
     */
    private $idStoreApi;

    /**
     * @return int
     */
    public function getIdStoreApi()
    {
        return $this->idStoreApi;
    }

    /**
     * @param int $idStoreApi
     */
    public function setIdStoreApi($idStoreApi)
    {
        $this->idStoreApi = $idStoreApi;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=3)
     */
    private $source;    

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2)
     */
    private $locale;  
    
    /**
     * @var string
     *
     * @ORM\Column(name="feed", type="text")
     */
    private $feed;

    /**
     * @var string
     *
     * @ORM\Column(name="flagbatched", type="string", length=1)
     */
    private $flagbatched;

    /**
     * @var string
     *
     * @ORM\Column(name="active", type="string", length=1)
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="broken", type="string", length=1)
     */
    private $broken;

    /**
     * @return string
     */
    public function getSitename()
    {
        return $this->sitename;
    }

    /**
     * @param string $sitename
     */
    public function setSitename($sitename)
    {
        $this->sitename = $sitename;
    }


    /**
     * @var string
     *
     * @ORM\Column(name="sitename", type="string", length=255)
     */
    private $sitename;

    /**
     * @var string
     *
     * @ORM\Column(name="siteslug", type="string", length=255)
     */
    private $siteslug;

    /**
     * @return string
     */
    public function getSiteslug()
    {
        return $this->siteslug;
    }

    /**
     * @param string $siteslug
     */
    public function setSiteslug($siteslug)
    {
        $this->siteslug = $siteslug;
    }

    /**
     * @return string
     */
    public function getSiteurl()
    {
        return $this->siteurl;
    }

    /**
     * @param string $siteurl
     */
    public function setSiteurl($siteurl)
    {
        $this->siteurl = $siteurl;
    }

    /**
     * @return string
     */
    public function getLogostore()
    {
        return $this->logostore;
    }

    /**
     * @param string $logostore
     */
    public function setLogostore($logostore)
    {
        $this->logostore = $logostore;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="siteurl", type="text")
     */
    private $siteurl;

    /**
     * @var string
     *
     * @ORM\Column(name="logostore", type="string", length=255)
     */
    private $logostore;

    /**
     * set site_id
     *
     * @return integer 
     */
    public function setSiteId($siteId)
    {
        $this->site_id = $siteId;

        return $this;
    }

    /**
     * Set feed
     *
     * @param string $feed
     * @return FeedCSV
     */
    public function setFeed($feed)
    {
        $this->feed = $feed;

        return $this;
    }

    /**
     * Get feed
     *
     * @return string 
     */
    public function getFeed()
    {
        return $this->feed;
    }

    /**
     * Set flagbatched
     *
     * @param string $flagbatched
     * @return FeedCSV
     */
    public function setFlagbatched($flagbatched)
    {
        $this->flagbatched = $flagbatched;

        return $this;
    }

    /**
     * Get flagbatched
     *
     * @return string 
     */
    public function getFlagbatched()
    {
        return $this->flagbatched;
    }

    /**
     * Set active
     *
     * @param string $active
     * @return FeedCSV
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return string 
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set broken
     *
     * @param string $broken
     * @return FeedCSV
     */
    public function setBroken($broken)
    {
        $this->broken = $broken;

        return $this;
    }

    /**
     * Get broken
     *
     * @return string 
     */
    public function getBroken()
    {
        return $this->broken;
    }

    /**
     * Set source
     *
     * @param string $source
     * @return FeedCSV
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get Source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }
    
    /**
     * Set Locale
     *
     * @param string $locale
     * @return FeedCSV
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get broken
     *
     * @return string 
     */
    public function getLocale()
    {
        return $this->locale;
    }
    
    
    
    
    
    
    
}
