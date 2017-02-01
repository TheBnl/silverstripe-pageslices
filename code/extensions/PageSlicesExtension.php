<?php

namespace Broarm\Silverstripe\PageSlices;

use ArrayList;
use Config;
use DataExtension;
use FieldList;
use GridField;
use LabelField;

/**
 * Class PageSlicesExtension
 *
 * @property \SiteTree|PageSlicesExtension $owner
 * @method \HasManyList PageSlices
 *
 * @package Broarm\Silverstripe\PageSlices
 */
class PageSlicesExtension extends DataExtension
{

    private static $has_many = array(
        'PageSlices' => 'Broarm\\Silverstripe\\PageSlices\\PageSlice.Parent'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $class = $this->owner->getClassName();
        $availableSlices = Config::inst()->get($class, 'available_slices');

        $pageSlicesGridFieldConfig = PageSlicesGridFieldConfig::create($availableSlices);

        $pageSlicesGridField = GridField::create(
            'PageSlices',
            _t('PageSlice.PLURALNAME', 'Page slices'),
            $this->owner->PageSlices(),
            $pageSlicesGridFieldConfig,
            $this
        );

        $pageSlicesLabelField = LabelField::create(
            'MembersLabel',
            _t('PageSlice.ABOUT', 'Add page sections to the page and rearrange them to alter the layout.')
        );

        $fields->addFieldsToTab('Root.PageSlices', array($pageSlicesLabelField, $pageSlicesGridField));
    }

    /**
     * Get the slice controllers
     *
     * @return ArrayList
     */
    public function getSlices()
    {
        $controllers = ArrayList::create();
        if ($slices = $this->owner->PageSlices()) {
            /** @var PageSlice $slice */
            foreach ($slices as $slice) {
                $controller = $slice->getController();
                $controller->init();
                $controllers->push($controller);
            }
            return $controllers;
        }

        return $controllers;
    }


    public function onAfterWrite()
    {
        $slices = array_unique(Config::inst()->get($this->owner->class, 'default_slices'));

        if ($this->owner->isValidClass() && $this->owner->hasNoSlices() && !empty($slices)) {
            foreach ($slices as $sliceClass) {
                /** @var PageSlice $slice */
                $slice = $sliceClass::create();
                $slice->write();
                $this->owner->PageSlices()->add($slice);
                $slice->publish('Stage', 'Live');
            }
        }

        parent::onAfterWrite();
    }


    /**
     * Check if the class is not in the exception list
     *
     * @return bool
     */
    public function isValidClass()
    {
        $invalidClassList = array_unique(PageSlice::config()->get('default_slices_exceptions'));
        return !in_array($this->owner->class, $invalidClassList);
    }


    /**
     * Check if the current obj has no page slices already created
     *
     * @return bool
     */
    public function hasNoSlices()
    {
        return $this->owner->PageSlices()->count() === 0;
    }
}
