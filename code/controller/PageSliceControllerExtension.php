<?php

namespace Broarm\Silverstripe\PageSlices;

use Extension;

/**
 * Class PageSliceControllerExtension
 *
 * @package Broarm\Silverstripe\PageSlices
 */
class PageSliceControllerExtension extends Extension
{

    /**
     * @var array
     */
    private static $allowed_actions = array(
        'handleSlice'
    );


    /**
     * Handle the slice
     *
     * @return bool|PageSliceController
     */
    public function handleSlice()
    {
        if (!$id = $this->owner->getRequest()->param('ID')) {
            return false;
        }

        $sliceRelations = array();
        if (!$hasManyRelations = $this->owner->data()->hasMany()) {
            return false;
        }

        foreach ($hasManyRelations as $relationName => $relationClass) {
            if ($relationClass == 'Broarm\\Silverstripe\\PageSlices\\PageSlice' || is_subclass_of($relationClass, 'Broarm\\Silverstripe\\PageSlices\\PageSlice')) {
                $sliceRelations[] = $relationName;
            }
        }

        $slice = null;
        foreach ($sliceRelations as $sliceRelation) {
            if ($slice) {
                break;
            }
            /** @var PageSlice $slice */
            $slice = $this->owner->data()->$sliceRelation()->find('ID', $id);
        }

        if (!$slice) {
            user_error('No slice found', E_USER_ERROR);
        }

        return $slice->getController();
    }
}
