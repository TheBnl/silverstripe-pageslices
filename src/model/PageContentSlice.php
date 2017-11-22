<?php

use Broarm\PageSlices\PageSlice;
use Broarm\PageSlices\PageSliceController;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;

/**
 * Class PageContentSlice
 *
 * @package Broarm\PageSlices
 *
 * @method Page Parent
 */
class PageContentSlice extends PageSlice
{
    private static $has_one = [];

    private static $slice_image = 'pageslices/images/PageContentSlice.png';

    private static $defaults = [
        'Title' => 'Page content'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Title');

        $notice = _t(
            'PageContentSlice.ABOUT',
            'This section holds the content of the parent page. To edit, simply edit the parent\'s content field'
        );
        $fields->addFieldsToTab('Root.Main', [
            LiteralField::create(
                'Notification',
                "<p class='message notice'>{$notice}</p>"
            ),
            HtmlEditorField::create('Content', 'Content', $this->Parent()->Content)
        ]);

        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if ($this->isChanged('Content') && $this->Parent()->exists()) {
            $this->Parent()->Content = $this->getField('Content');
            $this->Parent()->write();
        }
    }
}

/**
 * Class PageContentSlice_Controller
 *
 * @package Broarm\PageSlices
 */
class PageContentSlice_Controller extends PageSliceController
{
    private static $allowed_actions = [];

    public function init()
    {
        parent::init();
    }

    /**
     * Look for content slices that match any of the parents class ancestry
     * The slice name is composed of the class name + 'ContentSlice'
     *
     * @return \SilverStripe\ORM\FieldType\DBHTMLText
     */
    public function getTemplate()
    {
        // Weird fix that appeared in SS 3.5.2
        if (in_array($this->Parent()->class, ['CMSPageEditController', 'CMSPageSettingsController'])) {
            return null;
        }

        $sliceAncestry = explode(',', implode('ContentSlice,', array_reverse($this->Parent()->getClassAncestry())));
        array_pop($sliceAncestry);

        return $this->Parent()->renderWith($sliceAncestry, ['Slice' => $this]);
    }
}
