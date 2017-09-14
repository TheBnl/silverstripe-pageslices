<?php

namespace Broarm\Silverstripe\PageSlices;

use ClassInfo;
use Controller;
use Director;
use Versioned;

/**
 * Class PageSliceController
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
     * @var array
     */
    private static $allowed_actions = array();

    /**
     * @var boolean
     */
    private static $enable_cache = false;


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

    public function getCacheKey()
    {
        $cacheKey = implode('_', array(
            $this->ID,
            strtotime($this->LastEdited),
            Versioned::current_stage()
        ));

        $this->extend('updateCacheKey', $cacheKey);
        return $cacheKey;
    }

    /**
     * Return the rendered template
     * todo add more advanced caching
     *
     * @return \HTMLText
     */
    public function getTemplate()
    {
        if (self::config()->get('enable_cache')) {
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