<?php
/**
 * Authors: Alex Gusev <alex@flancer64.com>
 * Since: 2018
 */

namespace Flancer32\VsfAdapter\App;

/**
 * Main logger for this extension.
 */
class Logger
    extends \Magento\Framework\Logger\Monolog
{
    const FILENAME = 'vsf.log';
    const NAME = 'VSF';

    /** @var \Monolog\Handler\StreamHandler */
    private $hndlInMemory;

    public function __construct()
    {
        $handlers = $this->initHandlers();
        $processors = [];
        parent::__construct(static::NAME, $handlers, $processors);
    }

    public function getHandlerMemory(): \Monolog\Handler\StreamHandler
    {
        return $this->hndlInMemory;
    }

    private function initFormatter()
    {
        $dateFormat = "Y-m-d/H:i:s";
        $msgFormat = "%datetime%-%channel%.%level_name% - %message%\n";
        $result = new \Monolog\Formatter\LineFormatter($msgFormat, $dateFormat);
        return $result;
    }

    private function initHandlers()
    {
        $result = [];
        $formatter = $this->initFormatter();

        /* add file handler */
        $path = BP . '/var/log/' . static::FILENAME;
        $handler = new \Monolog\Handler\StreamHandler($path);
        $handler->setFormatter($formatter);
        $result[] = $handler;

        /* add memory handler */
        $path = 'php://memory';
        $this->hndlInMemory = new \Monolog\Handler\StreamHandler($path);
        $this->hndlInMemory->setFormatter($formatter);
        $result[] = $this->hndlInMemory;

        return $result;
    }
}
