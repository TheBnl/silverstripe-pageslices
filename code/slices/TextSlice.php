<?php
/**
 * TextSlice.php
 *
 * @author Bram de Leeuw
 * Date: 03/10/16
 */


/**
 * TextSlice
 */
class TextSlice extends PageSlice
{

    private static $db = array(
        'Content' => 'HTMLText'
    );

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $contentField = new HtmlEditorField('Content', 'Content');

        $fields->addFieldsToTab('Root.Main', array(
            $contentField
        ));

        $this->extend('updateCMSFields', $fields);
        return $fields;
    }
}