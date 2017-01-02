<?php
/**
 * GridFieldConfig_PageSlices.php
 *
 * @author Bram de Leeuw
 * Date: 03/10/16
 */

/**
 * Class GridFieldConfig_PageSlices
 */
class GridFieldConfig_PageSlices extends GridFieldConfig
{

    /**
     * GridFieldConfig_PageSlices constructor.
     *
     * @param array $availableClasses
     * @param int $itemsPerPage
     * @param string $sortField
     */
    public function __construct($availableClasses = array(), $itemsPerPage = 999, $sortField = 'Sort')
    {
        parent::__construct();
        if (empty($availableClasses)) {
            $availableClasses = ClassInfo::subclassesFor('PageSlice');
            array_shift($availableClasses);
        }

        $this->addComponent(new GridFieldToolbarHeader());
        $this->addComponent(new GridFieldTitleHeader());
        $this->addComponent(new GridFieldDataColumns());
        $this->addComponent(new VersionedDataObjectDetailsForm());
        $this->addComponent(new VersionedGridFieldDeleteAction());
        $this->addComponent(new GridFieldEditButton());
        $this->addComponent(new VersionedGridFieldOrderableRows($sortField));
        $this->addComponent($multiClassComponent = new GridFieldAddNewMultiClass());
        $this->addComponent($pagination = new GridFieldPaginator($itemsPerPage));

        $multiClassComponent->setClasses(self::translate_available_classes($availableClasses));
        $pagination->setThrowExceptionOnBadDataType(false);
    }


    /**
     * Translate the given array for a proper SINGULARNAME.
     *
     * @param $classes
     * @return array
     */
    private static function translate_available_classes($classes) {
        $out = array();
        foreach ($classes as $class) {
            $out[$class] = _t("$class.SINGULARNAME", "$class");
        }
        return $out;
    }
}