<?php

namespace AppBundle\Utils;



class TddFilteredCsvArray extends \FilterIterator
{
    private $csvFilter = array();

    public function setBlackList(array $filter ){
        foreach($filter as $term){
            $this->csvFilter[] = $term;
        }
    }

    public function accept()
    {
        $product = $this->getInnerIterator()->current();
        return !in_array($product['merchantCategoryName'], $this->csvFilter);
    }

    public function current() {
        return $this->getInnerIterator()->current();
    }

}