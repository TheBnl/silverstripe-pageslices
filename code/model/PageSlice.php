<?php
/**
 * PageSlice.php
 *
 * @author Bram de Leeuw
 * Date: 19/07/16
 */


/**
 * PageSlice
 */
class PageSlice extends DataObject
{
    private static $db = array(
        'Title' => 'Varchar(255)',
        'Sort' => 'Int'
    );

    private static $default_sort = 'Sort ASC';

    private static $has_one = array(
        'Parent' => 'SiteTree'
    );

    private static $summary_fields = array(
        'getSliceImage' => 'Type',
        'getSliceType' => 'Type Name',
        'Title' => 'Title'
    );

    private static $translate = array(
        'Title'
    );

    private static $slice_image = 'pageslices/images/PageSlice.png';

    /**
     * @var PageSliceController
     */
    protected $controller;

    public function getCMSFields()
    {
        $fields = new FieldList(new TabSet('Root', $mainTab = new Tab('Main')));

        $titleField = new TextField('Title', 'Title');

        $fields->addFieldsToTab('Root.Main', array($titleField));
        $this->extend('updateCMSFields', $fields);
        return $fields;
    }


    /**
     * Return the translated ClassName
     *
     * @return string
     */
    public function getSliceType()
    {
        $key = "{$this->getClassName()}.SINGULARNAME";
        return _t($key, $this->getClassName());
    }


    /**
     * Return a nice css name
     *
     * @return string
     */
    public function getCSSName()
    {
        return strtolower(preg_replace('/([a-zA-Z])(?=[A-Z])/', '$1-', $this->getClassName()));
    }


    /**
     * Return the rendered template
     *
     * @return HTMLText
     */
    public function getTemplate()
    {
        return $this->renderWith($this->getClassName());
    }


    /**
     * Return the path to the section image
     *
     * @return string
     */
    public function getSliceImage()
    {
        $image = self::config()->get('slice_image');
        return new LiteralField('SliceImage', "<img src='$image' title='{$this->getSliceType()}' alt='{$this->getSliceType()}' width='125' height='75'>");
    }


    /**
     * @throws Exception
     *
     * @return PageSliceController
     */
    public function getController()
    {
        if ($this->controller) return $this->controller;

        foreach (array_reverse(ClassInfo::ancestry($this->class)) as $sliceClass) {
            $controllerClass = "{$sliceClass}_Controller";
            if (class_exists($controllerClass)) break;

            $controllerClass = "{$sliceClass}Controller";
            if (class_exists($controllerClass)) break;

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

    public function canCreate($member = null)
    {
        return $this->Parent()->canCreate($member);
    }
}