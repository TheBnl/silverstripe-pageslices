<?php
/**
 * PageSlicesExtension.php
 *
 * @author Bram de Leeuw
 * Date: 03/10/16
 */


/**
 * PageSlicesExtension
 *
 * @property SiteTree|PageSlicesExtension $owner
 * @method HasManyList PageSlices
 */
class PageSlicesExtension extends DataExtension
{

    private static $has_many = array(
        'PageSlices' => 'PageSlice.Parent'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $class = $this->owner->getClassName();
        $availableSlices = Config::inst()->get($class, 'available_slices');

        $pageSlicesGridFieldConfig = GridFieldConfig_PageSlices::create($availableSlices);

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
        $slices = Config::inst()->get($this->owner->class, 'default_slices');
        if (is_array($slices)) {
            $slices = array_unique($slices);
        } else {
            $slices = null;
        }

        if (!empty($slices) && $this->owner->isValidClass() && $this->owner->hasNoSlices()) {
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
     * @param Page $page
     */
    public function onAfterDuplicate(Page $page)
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
