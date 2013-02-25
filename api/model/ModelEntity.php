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
	private static $xml = "";

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
		self::$xml = "";
		self::$xml = $CONFIG['xml_headers'][$this->name + "_noSOAP"];	
		
		$xml_json = $this->get_xml_json();		
		$this->xmlfy_element($this->name, $xml_json);
		self::$xml = self::$xml . "</". $this->name . ">\n";
		
		self::$xml = self::$xml . $CONFIG['xml_footers'][$this->name + "_noSOAP"];
		
		return self::$xml;
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
	 function xmlfy_element($element, $body)
	 {
		if ($element !== null) //dont do this if its a list
		{
			self::$xml = self::$xml . "<" . $element;
			//attributes
			if (isset($body["attribute"]))
			{
				foreach ($body["attribute"] as $key=>$value)
				{
					self::$xml = self::$xml . " " . $key . "=\"" . $value . "\"";
				}
			unset($body["attribute"]);
			}
			self::$xml = self::$xml . ">";
			//value
			if (isset($body["Value"]))
			{
				self::$xml = self::$xml . $body["Value"];
				unset($body["Value"]);
			}
		}
		//elements
		foreach ($body as $key=>$value)
		{
			if (is_array($value))
			{
				if (!isAssociative($value))	//it's a list
				{
					self::$xml = self::$xml . "<". $key . ">\n";
					for ($i = 0; $i < count($value); $i++)
						$this->xmlfy_element (null, $value[$i]);
					self::$xml = self::$xml . "</". $key . ">\n";
				}
				else //it's not a list
				{
					$this->xmlfy_element ($key, $value);
					self::$xml = self::$xml . "</". $key . ">\n";
				}
			}
			else
			{
				self::$xml = self::$xml . "<" . $key . ">" . $value . "</". $key . ">\n";
			}
		}
	 }
	 
	 function process_attribute ($attribute, &$result)
	 {
		$piece = &$result;
		
		if ($attribute->value === null) return;
		
		foreach ($attribute->path as $node_name)
		{
			UtilLogging::getInstance()->debug('Node name process_attribute: ' . $node_name . ' for attribute: ' . $attribute->id . " i: " . 0 . " and path: " . implode(", ", $attribute->path));
			if (!isset($piece[$node_name]))
				$piece[$node_name] = array();
			$piece = &$piece[$node_name];
		}
		if (equals($attribute->type, "list"))
		{
			UtilLogging::getInstance()->debug("process_attribute: the attribute: " . $attribute->id . " is a list. About to json_decode: " . $attribute->value);
			$piece[$attribute->name] = json_decode($attribute->value, TRUE);
		}
		else
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
		return $this->attributes[$id];
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
		$this->get_attribute($id)->value = $value;
	}
}

