<?php

namespace Broarm\PageSlices;

use Heyday\GridFieldVersionedOrderableRows\GridFieldVersionedOrderableRows;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Dev\Deprecation;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;
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
class PageSlicesGridFieldConfig extends GridFieldConfig_RecordEditor
{

    /**
     * GridFieldConfig_PageSlices constructor.
     *
     * @param array  $availableClasses
     * @param int    $itemsPerPage
     * @param string $sortField
     */
    public function __construct($availableClasses = array(), $itemsPerPage = null, $sortField = 'Sort')
    {
        parent::__construct($itemsPerPage = null);
        $this->removeComponentsByType(new GridFieldAddNewButton());
        $this->addComponent(new GridFieldVersionedOrderableRows($sortField));
        $this->addComponent($multiClassComponent = new GridFieldAddNewMultiClass('buttons-before-left'));
        $multiClassComponent->setClasses($availableClasses);
    }
}
