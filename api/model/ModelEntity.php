<?php
/**
 * Basic entity: collection of attributes.
 * (C) 2013 Tui Innovation.
 */

include_once 'model/ModelRequest.php';

/**
 * A basic entity: a collection of attributes with a name
 */
class ModelEntity
{
	var $name;
	var $attributes = array();

	/**
	 * Construct a basic entity with name of the entity and its attributes.
	 */
	function __construct($name, $attributes)
	{
		$this->name = $name;
		$this->add_attributes($attributes);
	}

	/**
	 * Add an array of attributes to the entity. Can be used to complement with a standard set of attributes.
	 */
	function add_attributes($attributes)
	{
		foreach ($attributes as $attribute)
		{
			$this->attributes[$attribute->id] = $attribute;
		}
	}
	
	/**
	 * Fill all the attributes with the contents of $REQUEST
	 */
	 function read_set_all()
	 {
		foreach($this->attributes as $attribute)
		{
			$attribute->read_set()
		}
	 }
	

	/**
	 * Get the attribute with the given id.
	 */
	function get_attribute($id)
	{
		if (!isset($this->attributes[$id]))
		{
			page_error('Unknown attribute ' . $id . ' for ' . $this->name);
		}
		return $this->attributes[$key];
	}

	/**
	 * Get the value of an attribute using its id.
	 */
	function get_value($id)
	{
		return $this->get_attribute($id)->$value;
	}

	/**
	 * Set the value for an attribute using its id.
	 */
	function set_value($id, $value)
	{
		$this->get_attribute($key)->value = $value;
	}
}

