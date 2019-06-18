<?php

namespace Broarm\PageSlices;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\LabelField;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\HasManyList;
use SilverStripe\Versioned\Versioned;


/**
 * Class PageSlicesExtension
 *
 * @property \SilverStripe\CMS\Model\SiteTree|PageSlicesExtension $owner
 * @method HasManyList PageSlices
 *
 * @package Broarm\PageSlices
 */
class PageSlicesExtension extends DataExtension
{
    private static $default_slices = array();

    private static $has_many = array(
        'PageSlices' => 'Broarm\\PageSlices\\PageSlice.Parent'
    );

    public function updateCMSFields(FieldList $fields)
    {
        if ($this->isValidClass() && $this->owner->exists()) {
            $availableSlices = $this->owner->getAvailableSlices();
            $pageSlicesGridFieldConfig = PageSlicesGridFieldConfig::create($availableSlices);

            $pageSlicesGridField = GridField::create(
                'PageSlices',
                _t('PageSlice.PLURALNAME', 'Page slices'),
                $this->owner->PageSlices(),
                $pageSlicesGridFieldConfig
            );

            $pageSlicesGridField->setDescription(_t(
                'PageSlice.ABOUT', 'Add page sections to the page and rearrange them to alter the layout.'
            ));

            $fields->addFieldsToTab('Root.PageSlices', array($pageSlicesGridField));
        }
    }

    public function getAvailableSlices()
    {
        $class = $this->owner->getClassName();
        $availableSlices = Config::inst()->get($class, 'available_slices');
        if (empty($availableClasses)) {
            $availableClasses = ClassInfo::subclassesFor(PageSlice::class);
            array_shift($availableClasses);
        }

        return array_map(function ($class) {
            return $class::singleton()->getSliceType();
        }, $availableClasses);
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
        parent::onAfterWrite();
        if ($defaultSlices = $this->owner->config()->get('default_slices')) {
            $slices = array_unique($defaultSlices);
        }

        if ($this->owner->isValidClass() && $this->owner->hasNoSlices() && !empty($slices)) {
            foreach ($slices as $sliceClass) {
                /** @var PageSlice $slice */
                $slice = $sliceClass::create();
                $slice->write();
                $this->owner->PageSlices()->add($slice);
                $slice->publishRecursive();
            }
        }
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
            $sliceCopy->publishRecursive();
        }
    }

    /**
     * Check if the class is not in the exception list
     *
     * @return bool
     */
    public function isValidClass()
    {
        $invalidClassList = array_unique(PageSlice::config()->get('default_slices_exceptions'));
        return !in_array($this->owner->getClassName(), $invalidClassList);
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
