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
		$xml_json = $this->get_xml_json();
		
		$xml = $xml . $this->xmlfy_element($this->name, $xml_json);
		$xml = $xml . "</". $this->name . ">\n";
		
		$xml = $xml . $CONFIG['xml_footers'][$this->name];
		
		return $xml;
	 }
	 
	 /**
	 * Produce an intermmediate xml_json structure
	 */
	 function get_xml_json()
	 {
		$result = array();
		foreach($this->attributes as $attribute)
		{	
			$this->process_attribute ($attribute, $result);
		}
		
		return $result;
	 }
	 
	 /**
	 * Recursive function to produce the xml from the xml_json
	 */
	 function xmlfy_element($element, $value)
	 {
		static $result = "";
		$result = $result . "<" . $element;
		if (isset($value["attribute"]))
		{
			if (is_array($value["attribute"]))
			{
				foreach ($value["attribute"] as $key=>$att_value)
				{
					UtilLogging::getInstance()->debug("xmlfy_element - adding attribute for key: " . $key);
					$result = $result . " " . $key . "=\"" . $att_value . "\"";
					UtilLogging::getInstance()->debug("xmlfy_element - added attribute. Result: " . $result);
				}
			}
			unset($value["attribute"]);
		}
		$result = $result . ">\n";
		
		foreach ($value as $key=>$inner_value)
		{
			UtilLogging::getInstance()->debug("xmlfy_element - Processing key: " . $key);
			if (is_array($inner_value))
			{
				UtilLogging::getInstance()->debug("xmlfy_element - Inner value is an array. Calling xmlfy for " . $key);
				$this->xmlfy_element ($key, $inner_value);
				$result = $result . "</". $key . ">\n";
			}
			else
			{
				UtilLogging::getInstance()->debug("xmlfy_element - Inner value is a string. constructing xml for key: " . $key . " and value: " . $inner_value);
				$result = $result . "<" . $key . ">" . $inner_value . "</". $key . ">\n";
				UtilLogging::getInstance()->debug("xmlfy_element - result updated: " . $result);
			}
		}
	
		return $result;
	 }
	 
	 function process_attribute ($attribute, &$result)
	 {
		$piece = &$result;
		
		foreach ($attribute->path as $node_name)
		{
			UtilLogging::getInstance()->debug('Node name process_attribute: ' . $node_name . ' for attribute: ' . $attribute->id . " i: " . 0 . " and path: " . implode(", ", $attribute->path));
			if (!isset($piece[$node_name]))
				$piece[$node_name] = array();
			$piece = &$piece[$node_name];
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

