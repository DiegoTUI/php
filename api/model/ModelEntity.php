<?php
/**
 * Basic entity: collection of attributes.
 * (C) 2013 Tui Innovation.
 */

include_once 'model/ModelRequest.php';
include_once 'util/UtilLogging.php';

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
	 * Fill all the writable attributes with the contents of $REQUEST
	 */
	 function read_set_all()
	 {
		foreach($this->attributes as $attribute)
		{
			if ($attribute->writeable)
			{
				UtilLogging::getInstance()->debug("read_set_all - Trying to set attribute: " . $attribute->id);
				$attribute->read_set();
			}
		}
	 }
	 
	 /**
	 * Fill all the writable attributes with the contents of $xml
	 */
	 function read_set_all_from_xml($xml)
	 {
		//TODO: parse xml and fill in the attributes
	 }
	
	/**
	 * Get an ATLAS compliant XML representation of the entity
	 */
	 function get_xml()
	 {
		global $CONFIG;
		$xml = $CONFIG['xml_headers'][$this->name];		
		//TODO: produce an xml based on the entity's attributes
		$xml_json = $this->get_xml_json();
		
		$xml = $xml . $CONFIG['xml_footers'][$this->name];
		
		return $xml;
	 }
	 
	 /**
	 * Produce an intermmediata xml_json structure
	 */
	 function get_xml_json()
	 {
		$result = array();
		foreach($this->attributes as $attribute)
		{
			process_attribute ($attribute, $result);
		}
		
		return $result;
	 }
	 
	 function process_attribute ($attribute, &$result)
	 {
		$piece = $result;
		for ($i=0 ; $i<count($attribute->path); $i++)
		{
			$node_name = $attribute->path[i];
			if (!isset($piece[$node_name]))
				$piece[$node_name] = array();
			$piece = $piece[$node_name];
		}
		$piece[$attribute->name] = $attribute->value;
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

