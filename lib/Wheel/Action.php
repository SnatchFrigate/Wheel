<?php

/**
 * This file is part of the invent-the-wheel project
 * check out the license file ;)
 */

namespace Wheel;
use Wheel\Services;

/**
 * Action
 * This represents and configures an action that has to take place within a 
 * route. check router.php to see how we are using these with our router
 * @author Daniel Chalk <snatchfrigate@gmail.com>
 */
class Action
{
  protected $class;
  protected $action;
  protected $params;

  public function __construct($class, $action, array $params = array())
  {
    $this->setClass($class)->setAction($action)->setParams($params);
  }

  public function getClass()
  {
    return $this->class;
  }

  public function setClass($class)
  {
    $this->class = $class;
    return $this;
  }

  public function getAction()
  {
    return $this->action;
  }

  public function setAction($action)
  {
    $this->action = $action;
    return $this;
  }

  public function getParams()
  {
    return $this->params;
  }

  public function setParams($params)
  {
    $this->params = $params;
    return $this;
  }

  /**
   * This will execute our controller and its action and return the response
   * @param Wheel\Services $services
   * @return mixed
   */
  public function execute(Services $services)
  {
    
    $controller_name = $this->getClass();
    $action_name = $this->getAction().'Action';
    
    //check if our controller even exists first
    if(!class_exists($controller_name))
    {
      throw new \Exception(sprintf('controller %s does not exist', $controller_name));
    }
    
    //now lets get reflective
    $reflector = new \ReflectionClass($controller_name);
    
    /* make sure our controller extends Wheel\Actions otherwise services will
     * not be available
     */
    if(!in_array('Wheel\\Actions', (array)$reflector->getParentClass()))
    {
      throw new \Exception(sprintf('controller %s doesnt extend Wheel\\Actions', $controller_name));
    }
    
    // Now we need to make sure our methods even exists
    if(!$reflector->hasMethod($action_name))
    {
      throw new \Exception(sprintf('action "%s" doesn\'t exists in %', $this->action, $this->class));
    }
    
    /* we now know our controller and method exists to we will first create a
     * new instance of our controller
     */ 
    $controller = $reflector->newInstanceArgs(array($services));
    
    /* I am now reflecting the method we will be using to make sure the correct
     * number of arguments are being used.
     */
    $action = new \ReflectionMethod($controller_name, $action_name);
    
    //get the required number of params
    $req_params = $action->getNumberOfRequiredParameters();
    
    //do we have enough params to satisfy our action
    if(count($this->getParams()) < $req_params)
    {
      throw new \Exception(sprintf('not enough argument for action %s:%s, you provided %d of %d', $this->class, $this->action, count($this->params), $req_params));
    }
    
    /* Time to call our action into action.
     * This action should return a response of some form, the response MUST be 
     * able to be echo'd, if you're returning an object a __toString() method
     * will do the trick.
     */
    return $action->invokeArgs($controller, $this->params);
  }

}
