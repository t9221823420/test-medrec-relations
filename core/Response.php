<?php

namespace core;

/**
 * Class Response
 * @package core
 */
class Response
{
    /** @var View */
    protected $_view;

    /**
     * Response constructor.
     * @param View|null $View
     */
    public function __construct(View $View = null)
    {
        $this->_view = $View;
    }

    /**
     *
     */
    public function send()
    {
        if ($this->_view instanceof View) {
            print $this->_view;
        }
    }
}
