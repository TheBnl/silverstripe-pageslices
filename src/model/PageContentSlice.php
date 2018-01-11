<?php

namespace Broarm\PageSlices;

use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\Forms\LiteralField;
use \SilverStripe\Core\ClassInfo;

/**
 * Class PageContentSlice
 *
 * @package Broarm\PageSlices
 *
 * @method Page Parent
 */
class PageContentSlice extends PageSlice
{
    private static $table_name = 'PageContentSlice';

    private static $slice_image = 'resources/bramdeleeuw/silverstripe-pageslices/images/PageContentSlice.png';

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

    public function getTemplate()
    {
        // Place the full ancestry in the current namespace so templates are to be placed in a coherent place
        $nameSpace = __NAMESPACE__;
        $sliceAncestry = array_map(function ($item) use ($nameSpace) {
            $name = ClassInfo::shortName($item);
            return "{$nameSpace}\\{$name}ContentSlice";
        }, array_reverse($this->Parent()->getClassAncestry()));

        return $this->Parent()->renderWith($sliceAncestry, ['Slice' => $this]);
    }
}
