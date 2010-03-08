<?php
/**
  * This file is a symfony plugin and part of the PromoteLabs.com architecture.
  * (c) PromoteLabs <info@promotelabs.com>
  *
  * For the full copyright and license information, please view the LICENSE
  * file that was distributed with this source code.
  */

/**
  * A Symfony form widget which displays a hierarchical set of choices.
  *
  * @package PromoteLabs
  * @author Chris LeBlanc <chris@webPragmatist.com>
  */

class plWidgetFormDoctrineChoiceHierarchy extends sfWidgetFormChoice
{
  /**
    * @see sfWidget
    */
  public function __construct($options = array(), $attributes = array())
  {
    $options['choices'] = array();

    parent::__construct($options, $attributes);
  }

  /**
    * Constructor.
    *
    * Available options:
  *
    *  * model:           The model class (required)
    *  * add_empty:       Whether to add a first empty value or not (false by default)
    *                     If the option is not a Boolean, the value will be used as the text value
    *  * method:          The method to use to display object values (__toString by default)
    *  * key_method:      The method to use to display the object keys (getPrimaryKey by default)
    *  * base_query:      The base_query for nested set
    *  * multiple:        true if the select tag must allow multiple selections
    *  * level_character: The character used to represent a level (- by default)
    *
    * @see sfWidgetFormSelect
    */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('add_empty', false);
    $this->addOption('method', '__toString');
    $this->addOption('key_method', 'getPrimaryKey');
    $this->addOption('base_query', null);
    $this->addOption('multiple', false);
    $this->addOption('level_character', '&nbsp;&nbsp;');

    parent::configure($options, $attributes);
  }

  /**
    * Returns the choices associated to the model.
    *
    * @return array An array of choices
    */
  public function getChoices()
  {
    $choices = array();
    if (false !== $this->getOption('add_empty'))
    {
      $choices[''] = true === $this->getOption('add_empty') ? '' : $this->getOption('add_empty');
    }

    $treeObject = Doctrine_Core::getTable($this->getOption('model'))->getTree();
    if (null !== $this->getOption('query')) $treeObject->setBaseQuery($this->getOption('query'));
    $tree = $treeObject->fetchTree();      

    $method = $this->getOption('method');
    $keyMethod = $this->getOption('key_method');

    foreach ($tree as $node)
    {
      $choices[$node->$keyMethod()] = str_repeat($this->getOption('level_character'), $node->getLevel()) . $node->$method();
    }

    return $choices;
  }
}