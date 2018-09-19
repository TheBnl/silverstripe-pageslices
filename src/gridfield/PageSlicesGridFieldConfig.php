<?php

namespace Broarm\PageSlices;

use Heyday\GridFieldVersionedOrderableRows\GridFieldVersionedOrderableRows;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\Forms\GridField\GridFieldVersionedState;
use SilverStripe\Versioned\VersionedGridFieldDetailForm;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;


/**
 * Class PageSlicesGridFieldConfig
 *
 * @package Broarm\PageSlices
 */
class PageSlicesGridFieldConfig extends GridFieldConfig
{

    /**
     * GridFieldConfig_PageSlices constructor.
     *
     * @param array  $availableClasses
     * @param int    $itemsPerPage
     * @param string $sortField
     */
    public function __construct($availableClasses = array(), $itemsPerPage = 999, $sortField = 'Sort')
    {
        parent::__construct();
        
        if (empty($availableClasses)) {
            $availableClasses = ClassInfo::subclassesFor(PageSlice::class);
            array_shift($availableClasses);
        }

        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent(new GridFieldTitleHeader());
        $this->addComponent(new GridFieldDataColumns());
        $this->addComponent(new GridFieldVersionedState());
        $this->addComponent(new GridFieldVersionedOrderableRows($sortField));
        $this->addComponent(new GridFieldDetailForm());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent($multiClassComponent = new GridFieldAddNewMultiClass());
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));

        $multiClassComponent->setClasses(self::translateAvailableClasses($availableClasses));
        $pagination->setThrowExceptionOnBadDataType(false);
    }


    /**
     * Translate the given array for a proper SINGULARNAME.
     *
     * @param $classes
     *
     * @return array
     */
    private static function translateAvailableClasses($classes)
    {
        $out = array();
        foreach ($classes as $class) {
            $out[$class] = $class::singleton()->getSliceType();
        }
        return $out;
    }
}
