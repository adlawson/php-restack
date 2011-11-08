<?php

namespace Restack\Exception;

/**
 * Exception thrown when a circular
 * dependency is detected
 * 
 * @category  Restack
 * @package   Restack\Exception
 */
class CircularDependencyException extends \LogicException implements \Restack\Exception
{
    
}