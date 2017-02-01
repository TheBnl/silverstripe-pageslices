<?php
/**
 * PageSliceController.php
 *
 * @author Bram de Leeuw
 * Date: 7/10/16
 */


/**
 * PageSliceController
 */
class PageSliceController extends Controller
{

    /**
     * @var PageSlice
     */
    protected $slice;


    /**
     * @var array
     */
    private static $allowed_actions = array();


    /**
     * @param PageSlice $slice
     */
    public function __construct($slice = null)
    {
        if ($slice) {
            $this->slice = $slice;
            $this->failover = $slice;
        }

        parent::__construct();
    }


    /**
     * Trigger the on after init here because we don't have a request handler on the page slice controller
     */
    public function init()
    {
        parent::init();
        $this->extend('onAfterInit');
    }


    /**
     * @param string $action
     *
     * @return string
     */
    public function Link($action = null)
    {
        $id = ($this->slice) ? $this->slice->ID : null;
        $segment = Controller::join_links('slice', $id, $action);

        if ($page = Director::get_current_page()) {
            return $page->Link($segment);
        }

        return Controller::curr()->Link($segment);
    }


    /**
     * Get the parent Controller
     *
     * @return Controller
     */
    public function Parent()
    {
        return Controller::curr();
    }


    /**
     * @return PageSlice
     */
    public function getSlice()
    {
        return $this->slice;
    }


    /**
     * Return the rendered template
     *
     * @return HTMLText
     */
    public function getTemplate()
    {
        return $this->renderWith($this->getClassName());
    }
}