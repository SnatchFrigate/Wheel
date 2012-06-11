<?php

/**
 * This file is part of the invent-the-wheel project
 * check out the license file ;)
 */

namespace Wheel;

/**
 * Base class for controllers, I should have named it better and will refactor
 * 
 * All controllers must extends this class so the service container is 
 * accessible
 *  
 * @author Daniel Chalk <snatchfrigate@gmail.com>
 */
abstract class Actions
{
  /**
   * @var Wheel\Front
   */
  protected $front;
  
  /**
   * @param Wheel\Service
   */
  public function __construct(Front $front)
  {
    $this->front = $front;
  }
  
  /**
   * get service
   * @return mixed
   */
  public function get($service_name)
  {
    return $this->front->getServices()->get($service_name);
  }
}
