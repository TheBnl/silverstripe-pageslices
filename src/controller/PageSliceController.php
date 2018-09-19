<?php

namespace Broarm\PageSlices;

use Psr\SimpleCache\CacheInterface;
use SilverStripe\Control\Controller;
use SilverStripe\Control\Director;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\Versioned\Versioned;
use SilverStripe\Core\ClassInfo;


/**
 * Class PageSliceController
 *
 * @package Broarm\PageSlices
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
     * Turn the caching feature on/off
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
            Versioned::get_reading_mode()
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
        if (!$this->useCaching()) {
            $result = $this->renderTemplate();
        } else {
            $cache = Injector::inst()->get(CacheInterface::class . '.PageSlices');
            if (!$cache->has($this->getCacheKey())) {
                $result = $this->renderTemplate();
                $cache->set($this->getCacheKey(), $result);
            } else {
                $result = $cache->get($this->getCacheKey());
            }
        }

        return $result;
    }

    public function renderTemplate()
    {
        return $this->renderWith(array_reverse(ClassInfo::ancestry($this->getClassName())));
    }
}