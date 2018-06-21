<?php

namespace Broarm\Silverstripe\PageSlices;

use ClassInfo;
use Controller;
use Director;
use Versioned;

/**
 * Class PageSliceController
 * @mixin \LoopSlice
 *
 * @package Broarm\Silverstripe\PageSlices
 */
class PageSliceController extends Controller
{
    /**
     * @var PageSlice
     */
    protected $slice;

    /**
     * Overwrite this setting on your subclass
     * to disable caching on a per slice basis
     *
     * @var boolean
     */
    protected $useCaching = true;

    /**
     * Turn the caching feature on
     *
     * @var boolean
     */
    private static $enable_cache = false;

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
     * Temp Disable the log_last_visited, this can cause a lot of duplicate requests to the database.
     */
    public function init()
    {
        $logVisits = \Config::inst()->get('Member', 'log_last_visited');
        \Config::inst()->update('Member', 'log_last_visited', false);
        parent::init();
        $this->extend('onAfterInit');
        \Config::inst()->update('Member', 'log_last_visited', $logVisits);
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

        if (($page = Director::get_current_page()) && !($page instanceof PageSliceController)) {
            return $page->Link($segment);
        }

        if ($controller = $this->getParentController()) {
            return $controller->Link($segment);
        }

        return $segment;
    }

    /**
     * Find a non PageSlice controller
     *
     * @return Controller|false
     */
    public function getParentController()
    {
        foreach(Controller::$controller_stack as $controller) {
            if (!($controller instanceof PageSliceController)) {
                return $controller;
            }
        }

        return false;
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
     * Check if the caching featured is turned on and enabled for this slice
     *
     * @return bool
     */
    public function useCaching()
    {
        return $this->useCaching && self::config()->get('enable_cache');
    }

    /**
     * The Cache key with basis properties
     * Extend this on your subclass for more specific properties
     *
     * @return string
     */
    public function getCacheKey()
    {
        $cacheKey = implode('_', array(
            $this->ID,
            strtotime($this->LastEdited),
            strtotime($this->Parent()->LastEdited),
            Versioned::current_stage()
        ));

        $this->extend('updateCacheKey', $cacheKey);
        return $cacheKey;
    }

    /**
     * Return the rendered template
     *
     * @return \HTMLText
     */
    public function getTemplate()
    {
        if ($this->useCaching()) {
            // Lookup the list in the cache
            $cache = \SS_Cache::factory('page_slice');
            if (!($result = unserialize($cache->load($this->getCacheKey())))) {
                $result = $this->renderTemplate();
                $cache->save(serialize($result), $this->getCacheKey());
            }
        } else {
            $result = $this->renderTemplate();
        }

        return $result;
    }

    public function renderTemplate()
    {
        return $this->renderWith(array_reverse(ClassInfo::ancestry($this->getClassName())));
    }
}