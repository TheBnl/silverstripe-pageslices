<?php

/**
 * ImageSlice
 *
 * @author Bram de Leeuw
 * Date: 03/10/16
 *
 * @property int SliceHeight
 *
 * @method Image Image
 */
class ImageSlice extends PageSlice
{

    private static $db = array(
        'SliceHeight' => 'Int'
    );

    private static $has_one = array(
        'Image' => 'Image'
    );

    private static $defaults = array(
        'SliceHeight' => 450
    );

    private static $slice_image = 'pageslices/images/ImageSlice.png';

    private static $site_width = 1600;

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $numberField = new NumericField('SliceHeight', 'SliceHeight');

        $uploadField = new UploadField('Image', 'Image');
        $uploadField->setAllowedMaxFileNumber(1);
        $uploadField->setFolderName($this->uploadFolder());
        $uploadField->getValidator()->setAllowedExtensions(array('png', 'gif', 'jpg'));

        $fields->addFieldsToTab('Root.Main', array(
            $numberField,
            $uploadField
        ));

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }

    public function ImageFill() {
        if ($image = $this->Image()) {
            $method = class_exists('FocusPointImage') ? 'FocusFill' : 'Fill';
            return $image->$method(self::config()->get('site_width'), $this->getField('SliceHeight'));
        }

        return null;
    }

    /**
     * Generate a upload folder path
     *
     * @return string
     */
    private function uploadFolder()
    {
        $folder = 'page-slices';
        if ($parent = $this->Parent()) $folder .= "/{$parent->URLSegment}/";

        return $folder;
    }
}