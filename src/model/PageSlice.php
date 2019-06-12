<?php

namespace Broarm\PageSlices;

use Exception;
use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Core\Manifest\ModuleResourceLoader;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;
use SilverStripe\View\Parsers\URLSegmentFilter;

/**
 * Class PageSlice
 * @mixin Versioned
 *
 * @package Broarm\PageSlices
 *
 * @property string Title
 * @property string SliceID
 *
 * @method \Page Parent
 */
class PageSlice extends DataObject
{
    private static $table_name = 'PageSlice';
    
    private static $db = [
        'Title' => 'Varchar(255)',
        'SliceID' => 'Varchar(255)',
        'Sort' => 'Int'
    ];

    private static $default_sort = 'Sort ASC';

    private static $has_one = [
        'Parent' => 'Page'
    ];

    private static $summary_fields = [
        'getSliceImage' => 'Type',
        'getSliceType' => 'Type Name',
        'Title' => 'Title'
    ];

    private static $translate = [
        'Title'
    ];

    private static $slice_image = 'bramdeleeuw/silverstripe-pageslices:images/PageSlice.png';

    /**
     * @var PageSliceController
     */
    protected $controller;

    /**
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = FieldList::create(TabSet::create('Root', $mainTab = Tab::create('Main')));

        $titleField = TextField::create('Title', 'Title');

        $fields->addFieldsToTab('Root.Main', array($titleField));
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function onBeforeWrite()
    {
        if(!$this->exists()){
            $sort = $this->Parent()->PageSlices()->max('Sort');
            $this->Sort = $sort++;
        }
        $this->createSliceID();
        parent::onBeforeWrite();
    }

    /**
     * If this slice holds has_many content
     * on duplicate copy the content over
     *
     * @param PageSlice $slice
     */
    public function onAfterDuplicate(PageSlice $slice)
    {
        // Check if there are relations set
        // Loop over each set relation
        // Copy all items in the relation over to the new object
        if ($hasManyRelations = $slice->data()->hasMany()) {
            foreach ($hasManyRelations as $relation => $class) {
                foreach ($slice->$relation() as $object) {
                    /** @var DataObject $object */
                    $copy = $object->duplicate(true);
                    $this->$relation()->add($copy);
                }
            }
        }
    }

    /**
     * Return the translated ClassName
     *
     * @return string
     */
    public function getSliceType()
    {
        $singularName = explode('\\', $this->i18n_singular_name());
        return end($singularName);
    }

    /**
     * Return a nice css name
     *
     * @return string
     */
    public function getCSSName()
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', ClassInfo::shortName($this)));
    }

    /**
     * Create a readable ID based on the slice title
     */
    private function createSliceID()
    {
        $urlFilter = URLSegmentFilter::create();
        if ($sliceID = $urlFilter->filter($this->getField('Title'))) {
            if (!$this->Parent()->PageSlices()->filter(array('ID:not' => $this->ID, 'SliceID' => $sliceID))->exists()) {
                $this->setField('SliceID', $sliceID);
            } else {
                $this->setField('SliceID', "$sliceID-{$this->ID}");
            }
        }
    }

    /**
     * Return the path to the section image
     *
     * @return string
     */
    public function getSliceImage()
    {
        $image = ModuleResourceLoader::resourceURL(self::config()->get('slice_image'));
        return LiteralField::create(
            'SliceImage',
            "<img src='$image' title='{$this->getSliceType()}' alt='{$this->getSliceType()}' width='125'>"
        );
    }

    /**
     * @throws Exception
     *
     * @return PageSliceController
     */
    public function getController()
    {
        if ($this->controller) {
            return $this->controller;
        }

        $controllerClass = null;
        foreach (array_reverse(ClassInfo::ancestry($this->getClassName())) as $sliceClass) {
            $controllerClass = "{$sliceClass}_Controller";
            if (class_exists($controllerClass)) {
                break;
            }

            $controllerClass = "{$sliceClass}Controller";
            if (class_exists($controllerClass)) {
                break;
            }
        }

        if (!class_exists($controllerClass)) {
            throw new Exception("Could not find controller class for {$this->getClassName()}");
        }

        $this->controller = Injector::inst()->create($controllerClass, $this);

        return $this->controller;
    }

    /**
     * Remove the add new button from the utility list
     * Because of the multi class, add new would create a new base class that should not be used
     * (Could be replaced with an add new multi class button)
     *
     * @return mixed
     */
    public function getBetterButtonsUtils()
    {
        $fields = parent::getBetterButtonsUtils();
        $fields->removeByName('action_doNew');
        return $fields;
    }

    public function canView($member = null)
    {
        return $this->Parent()->canView($member);
    }

    public function canEdit($member = null)
    {
        return $this->Parent()->canEdit($member);
    }

    public function canDelete($member = null)
    {
        return $this->Parent()->canDelete($member);
    }

    public function canCreate($member = null, $context = [])
    {
        return $this->Parent()->canCreate($member);
    }
}
