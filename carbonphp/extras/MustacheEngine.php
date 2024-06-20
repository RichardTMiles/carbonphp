<?php

namespace CarbonPHP\Extras;

use CarbonPHP\Error\ThrowableHandler;
use Mustache_Engine;
use Throwable;

class MustacheEngine extends Mustache_Engine
{
    public function __construct(array $options = [])
    {
        try {

            ThrowableHandler::stop();

            $oldLevel =ThrowableHandler::$level;

            ThrowableHandler::$level ^= (E_NOTICE | E_WARNING | E_DEPRECATED);

            error_reporting(ThrowableHandler::$level);

            // @note if the Mustache_Engine fails, then tries to re-load itsself the message will say "Class "Mustache_Engine" not found"
            // This is very deceptive as the class is found, but failing to be loaded via compile time error. E_DEPRECATED is the first sign
            parent::__construct($options);

            ThrowableHandler::$level = $oldLevel;

            ThrowableHandler::start();

        } catch (Throwable $e) {

            sortDump(['Mustache_Engine __construct Failed', $e->getMessage(), $e->getTraceAsString()]);

            exit(11);

        }
    }

    public function render($template, $context = array())
    {
        try {

            ThrowableHandler::stop();

            $oldLevel =ThrowableHandler::$level;

            ThrowableHandler::$level ^= (E_NOTICE | E_WARNING | E_DEPRECATED);

            error_reporting(ThrowableHandler::$level);

            // @note if the Mustache_Engine fails, then tries to re-load itsself the message will say "Class "Mustache_Engine" not found"
            // This is very deceptive as the class is found, but failing to be loaded via compile time error. E_DEPRECATED is the first sign
            $result = parent::render($template, $context);

            ThrowableHandler::$level = $oldLevel;

            error_reporting(ThrowableHandler::$level);

            ThrowableHandler::start();

            return $result;

        } catch (Throwable $e) {

            sortDump(['Mustache_Engine Render Failed', $e->getMessage(), $context]);

            exit(11);

        }
    }
}

