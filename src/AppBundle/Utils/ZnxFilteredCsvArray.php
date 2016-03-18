<?php

namespace AppBundle\Utils;

class ZnxFilteredCsvArray extends \FilterIterator
{
    private $csvFilter = array();

    public function __construct( \ArrayIterator $csvIterator , array $filter )
    {
        parent::__construct($csvIterator);
        foreach($filter as $term){
            $csvfilter[] = $term->getPending()->getLabel();
        }
        $this->csvFilter = array_flip($csvfilter);

    }

    public function accept()
    {
        $product = $this->getInnerIterator()->current();

        if(isset($this->csvFilter[$product['MerchantProductCategoryPath']]) == true) {
            return false;
        }
        return true;
    }
}