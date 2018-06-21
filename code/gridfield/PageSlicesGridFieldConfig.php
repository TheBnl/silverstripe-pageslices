<?php

namespace Broarm\Silverstripe\PageSlices;

use ClassInfo;
use GridFieldAddNewMultiClass;
use GridFieldConfig;
use GridFieldDataColumns;
use GridFieldEditButton;
use GridFieldPaginator;
use GridFieldTitleHeader;
use GridFieldToolbarHeader;
use Heyday\VersionedDataObjects\VersionedDataObjectDetailsForm;
use Heyday\VersionedDataObjects\VersionedGridFieldOrderableRows;

/**
 * Class PageSlicesGridFieldConfig
 *
 * @package Broarm\Silverstripe\PageSlices
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
            $availableClasses = ClassInfo::subclassesFor('Broarm\\Silverstripe\\PageSlices\\PageSlice');
            array_shift($availableClasses);
        }

        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent(new GridFieldTitleHeader());
        $this->addComponent(new GridFieldDataColumns());
        $this->addComponent(new VersionedDataObjectDetailsForm());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new PageSlicesVersionedGridFieldDeleteAction());
        $this->addComponent(new VersionedGridFieldOrderableRows($sortField));
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
