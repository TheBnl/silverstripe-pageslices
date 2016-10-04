<?php
/**
 * PageSlicesPage.php
 *
 * @author Bram de Leeuw
 * Date: 03/10/16
 */


/**
 * PageSlicesPage
 */
class PageSlicesPage extends Page
{

    private static $has_many = array(
        'PageSlices' => 'PageSlice'
    );

    /**
     * Set the available slices
     *
     * @config
     */
    private static $available_slices = array();

    public function getCMSFields()
    {
        // Use beforeUpdateCMSFields so fluent can add a label and icon
        $this->beforeUpdateCMSFields(function (FieldList $fields) {
            $pageSlicesGridFieldConfig = new GridFieldConfig_PageSlices(self::config()->get('available_slices'));

            $pageSlicesGridField = new GridField('PageSlices', _t('PageSlice.PLURALNAME', 'Page slices'), $this->PageSlices(), $pageSlicesGridFieldConfig, $this);
            $pageSlicesLabelField = new LabelField('MembersLabel', _t('PageSlice.ABOUT', 'Add page sections to the page and rearrange them to alter the layout.'));

            $fields->addFieldsToTab('Root.Main', array($pageSlicesLabelField, $pageSlicesGridField), 'Content');
        });

        $fields = parent::getCMSFields();
        $fields->removeByName(array(
            'Content'
        ));
        return $fields;
    }
}


/**
 * Class PageSlicesPage_controller
 *
 * @property PageSlicesPage dataRecord
 * @method PageSlicesPage data
 */
class PageSlicesPage_controller extends Page_Controller
{
    private static $allowed_actions = array();

    public function init()
    {
        parent::init();
    }
}