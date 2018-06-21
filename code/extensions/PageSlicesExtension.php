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

    private static $default_slices = array();


    private static $has_many = array(
        'PageSlices' => 'Broarm\\Silverstripe\\PageSlices\\PageSlice.Parent'
    );

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->isValidClass() && $this->owner->exists()) {
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
    }
    
    /**
     * Get the slice controllers
     *
     * @return ArrayList
     */
    public function getSlices()
    {
        $controllers = ArrayList::create();

        if(
            $this->owner instanceof \VirtualPage
            && ($original = $this->owner->CopyContentFrom())
            && $original->hasExtension(self::class))
        {
            $slices = $original->PageSlices();
        } else {
            $slices = $this->owner->PageSlices();
        }

        if ($slices) {
            /** @var PageSlice $slice */
            foreach ($slices as $slice) {
                try {
                    $controller = $slice->getController();
                    $controller->init();
                    $controllers->push($controller);
                } catch (\Exception $e) {
                    user_error($e, E_ERROR);
                }
            }
            return $controllers;
        }

        return $controllers;
    }

    
    public function onAfterWrite()
    {
        if ($defaultSlices = Config::inst()->get($this->owner->class, 'default_slices')) {
            $slices = array_unique($defaultSlices);
        }
        
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
     * Make sure the default slices do not get added double on a duplicate action
     */
    public function onBeforeDuplicate()
    {
        Config::inst()->update($this->owner->class, 'default_slices', null);
    }


    /**
     * Loop over and copy the attached page slices
     *
     * @param \Page $page
     */
    public function onAfterDuplicate(\Page $page)
    {
        foreach ($this->owner->PageSlices() as $slice) {
            /** @var PageSlice $slice */
            $sliceCopy = $slice->duplicate(true);
            $page->PageSlices()->add($sliceCopy);
            $sliceCopy->publish('Stage', 'Live');
        }
    }


    /**
     * Clean up any child records after the page is deleted.
     * If a page is archived and a ID is reused (?) old slices
     * matching the parent ID can be added to the new page
     */
    public function onAfterDelete()
    {
        foreach ($this->owner->PageSlices() as $slice) {
            /** @var PageSlice $slice */
            $slice->deleteFromStage('Live');
            $slice->delete();
        }
        parent::onAfterDelete();
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
