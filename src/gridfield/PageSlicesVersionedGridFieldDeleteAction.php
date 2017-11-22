<?php

namespace Broarm\PageSlices;

use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\ORM\ValidationException;

/**
 * Class PageSlicesVersionedGridFieldDeleteAction
 *
 * Extend the delete action with a versioned delete
 * This class is temporarily included in the boilerplate until
 *
 * @package Broarm\PageSlices
 */
class PageSlicesVersionedGridFieldDeleteAction extends GridFieldDeleteAction
{
    /**
     * Delete the object form both live and stage
     *
     * @param GridField $gridField
     * @param string    $actionName
     * @param mixed     $arguments
     * @param array     $data
     *
     * @throws ValidationException
     */
    public function handleAction(GridField $gridField, $actionName, $arguments, $data)
    {
        if ($item = $gridField->getList()->byID($arguments['RecordID'])) {
            /** @var \SilverStripe\ORM\DataObject $item */
            if (!$item->canDelete()) {
                throw new ValidationException(_t(
                    'GridFieldAction_Delete.DeletePermissionsFailure',
                    "No delete permissions"
                ), 0);
            }
            $item->deleteFromStage('Live');
            $item->delete();
        }
    }
}
