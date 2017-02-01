<?php
/**
 * PageContentSlice.php
 *
 * @author Bram de Leeuw
 * Date: 19/12/16
 */


/**
 * PageContentSlice
 *
 * @method Page Parent
 */
class PageContentSlice extends PageSlice
{
    private static $db = array();

    private static $has_one = array();

    private static $slice_image = 'pageslices/images/TextSlice.png';

    private static $defaults = array(
        'Title' => 'Page content'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $notice = _t('PageContentSlice.ABOUT', 'This section holds the content of the parent page. To edit, simply edit the parent\'s content field');
        $fields->removeByName('Title');
        $fields->addFieldsToTab('Root.Main', array(
            LiteralField::create(
                'Notification',
                "<p class='message notice'>{$notice}</p>"
            )
        ));
        return $fields;
    }
}


class PageContentSlice_Controller extends PageSliceController
{
    private static $allowed_actions = array();

    public function init()
    {
        parent::init();
    }

    /**
     * Look for content slices that match any of the parents class ancestry
     * The slice name is composed of the class name + 'ContentSlice'
     *
     * @return HTMLText
     */
    public function getTemplate()
    {
        $sliceAncestry = explode(',', implode('ContentSlice,', array_reverse($this->Parent()->getClassAncestry())));
        array_pop($sliceAncestry);

        return $this->Parent()->renderWith($sliceAncestry, array('ID' => $this->ID));
    }
}