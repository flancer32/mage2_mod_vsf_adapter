<?php
/**
 * Register Magneto module on composer's 'install' event.
 *
 * User: Alex Gusev <alex@flancer64.com>
 * Since: 2020
 */

\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    \Flancer32\VsfAdapter\Config::MODULE, __DIR__);