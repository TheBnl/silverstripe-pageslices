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
class PageSlicesExtension extends DataExtension
{

    private static $has_many = array(
         'PageSlices' => 'PageSlice.Parent'
    );

    public function updateCMSFields(FieldList $fields)
    {
        $class = $this->owner->getClassName();
        $availableSlices = Config::inst()->get($class, 'available_slices');

        $pageSlicesGridFieldConfig = new GridFieldConfig_PageSlices($availableSlices);
        $pageSlicesGridField = new GridField('PageSlices', _t('PageSlice.PLURALNAME', 'Page slices'), $this->owner->PageSlices(), $pageSlicesGridFieldConfig, $this);
        $pageSlicesLabelField = new LabelField('MembersLabel', _t('PageSlice.ABOUT', 'Add page sections to the page and rearrange them to alter the layout.'));

        $fields->addFieldsToTab('Root.PageSlices', array($pageSlicesLabelField, $pageSlicesGridField));
    }

    /**
     * Get the slice controllers
     *
     * @return ArrayList
     */
    public function getSlices() {
        $controllers = new ArrayList();
        if ($slices = $this->owner->PageSlices()) {
            foreach ($slices as $slice) {
                $controller = $slice->getController();
                $controller->init();
                $controllers->push($controller);
            }
            return $controllers;
        }

        return $controllers;
    }
}
