<?php
/**
 * Attributes in an entity.
 * (C) 2013 Tui Innovation.
 */
 
 include_once 'util/UtilKernel.php';
 include_once 'util/UtilLogging.php';

/**
 * An attribute in an entity. The creator can specify if it is mandatory,
 * visible and if it can be updated (writeable).
 * The path is used as a way to reach sub-objects:
 * position.location refers to an attribute called location
 * inside another called position.
 * Square brackets can be used to indicate an array and the position within it:
 * opening[0].from_time refers to the from_time attribute inside the first
 * set of opening hours. Type can be "element" or "attribute" (for xml conversion)
 */
class Attribute
{
	var $id;
	var $name;
	var $type;
	var $path;
	var $value;
	var $mandatory = false;
	var $writeable = true;
	var $visible = true;

	/**
	 * Construct a new attribute.
	 * The path is a dot-separated list of objects: position.latitude.
	 */
	function __construct($name, $dot_path = null, $type = null, $value = null)
	{
		$this->id = $name;
		$this->name = $name;
		$this->path = array();
		if ($dot_path != null)
		{
			$this->id = str_replace(".", "_", $dot_path) . '_' . $name;
			$this->set_path($dot_path);
		}
		
		$this->type = $type;
		if ($type === null)	//"Autocalculate" the type based on the name
		{
			$this->type = 'attribute';
			if (startsWithUpper($this->name))
				$this->type = 'element';
			if (contains($this->name, "List"))
				$this->type = 'list';
		}
		if (equals($this->type, "attribute"))
			array_push($this->path, $this->type);
			
		if ($value != null)
			$this->writeable = false;
			
		$this->value = $value;
		
		UtilLogging::getInstance()->debug("Attribute constructed - id: " . $this->id . " - type: " . $this->type . " - path: " . implode(", ", $this->path));
	}

	/**
	 * Set the path dot-separated.
	 */
	function set_path($dot_path)
	{
		$this->path = explode('.', $dot_path);
		return $this;
	}

	/**
	 * Read a value from the request; if present, set into the given object.
	 */
	function read_set()
	{
		global $REQUEST;
		if (!$this->writeable)
		{
			page_error('Read only attribute ' . $this->name);
		}
		$this->value = $REQUEST->read($this->id, $this->mandatory);
		
		UtilLogging::getInstance()->debug("read_set - Attribute: ". $this->id . " - value set: " . $this->value);
	}
	
	/**
	 * Read a value from a simpleXML object, set into the given object.
	 */
	 function read_set_simplexml ($simplexml)
	 {
	 }
}

/**
 * A mandatory attribute. By default it will be also visible and writeable.
 */
class Mandatory extends Attribute
{
	function __construct($name, $dot_path = null, $type = null, $value = null)
	{
		parent::__construct($name, $dot_path, $type, $value);
		$this->mandatory = true;
		$this->writeable = true;
		$this->visible = true;
		
		if ($value != null)
			$this->writeable = false;
	}
}

/**
 * An optional attribute. By default it will also be visible.
 */
class Optional extends Attribute
{
	function __construct($name, $dot_path = null, $type = null, $value = null)
	{
		parent::__construct($name, $dot_path, $type, $value);
		$this->writeable = true;
		$this->visible = true;
		
		if ($value != null)
			$this->writeable = false;
	}
}

/**
 * A visible attribute. It cannot be written to directly.
 */
class Visible extends Attribute
{
	function __construct($name, $dot_path = null, $type = null, $value = null)
	{
		parent::__construct($name, $dot_path, $type, $value);
		$this->visible = true;
	}
}

/**
 * An opaque attribute. Can be written to, but never returned.
 */
class Opaque extends Attribute
{
	function __construct($name)
	{
		parent::__construct($name);
		$this->writeable = true;
	}
}


