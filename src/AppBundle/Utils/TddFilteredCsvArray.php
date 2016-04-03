<?php

namespace AppBundle\Utils;

class TddFilteredCsvArray
{
    private $csvFilter = array();
    private $iterator;

    public function __construct( array $iterator)
    {
        $this->iterator = $iterator;
    }

    public function setBlackList(array $filter ){
        foreach($filter as $term){
            $this->csvFilter[] = $term;
        }
    }

    public function getIterator()
    {
        $iterator = array();
        foreach($this->iterator as $item)
        {
            /* @todo remplacer par une cnstante de source  */
            if( !in_array($item['merchantCategoryName'], $this->csvFilter) )
            {
                $iterator[] = $item;
            }
        }

        return $iterator;
    }
}


