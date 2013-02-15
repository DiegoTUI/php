<?php
/**
 * Attributes in an entity.
 * (C) 2013 Tui Innovation.
 */
 
 include_once 'util/UtilKernel.php';

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
	var $name;
	var $type;
	var $path;
	var $mandatory = false;
	var $writeable = true;
	var $visible = true;

	/**
	 * Construct a new attribute.
	 * The path is a dot-separated list of objects: position.latitude.
	 */
	function __construct($name, $type = "element", $dot_path = null)
	{
		$this->name = $name;
		$this->path = array($name);
		if ($dot_path != null)
		{
			$this->set_path($dot_path);
		}
		
		$this->type = $type;
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
	function read_set(&$object)
	{
		global $REQUEST;
		if (!$this->writeable)
		{
			page_error('Read only attribute ' . $this->name);
		}
		$value = $REQUEST->read($this->name, false);
		$this->set($object, $value);
	}

	/**
	 * 
	 * Flatten the value from an object and set into the flattened object.
	 * The value is set as its name, so as to avoid nested attributes.
	 */
	function flatten($object, &$flattened)
	{
		if (!$this->visible)
		{
			return;
		}
		$value = $this->get($object);
		if ($value === null)
		{
			return;
		}
		$flattened[$this->name] = $value;
	}

	/**
	 * Filter the value from an object and set into the filtered object.
	 */
	function filter($object, &$filtered)
	{
		if (!$this->visible)
		{
			return;
		}
		$value = $this->get_inner($object);
		if ($value === null)
		{
			return;
		}
		$this->set_inner($filtered, $value);
	}

	/**
	 * Get the value of an attribute from an object.
	 */
	function get($object)
	{
		$value = $this->get_inner($object);
		
		return $value);
	}

	/**
	 * Set the value for an attribute into an object.
	 */
	function set(&$object, $value)
	{
		if ($value == null && $this->mandatory)
		{
			page_error('Parameter "' . $this->name . '" is mandatory');
		}
		$this->set_inner($object, $value);
	}

	/**
	 * Get the inner value of an object, untranslated.
	 */
	function get_inner($object)
	{
		return $this->inner($this->path, $object, function($inner, $attr_name)
		{
			if (!isset($inner[$attr_name]))
			{
				return null;
			}
			return $inner[$attr_name];
		});
	}

	/**
	 * Set the value into an inner attribute, unconverted.
	 */
	function set_inner(&$object, $value)
	{
		$this->inner($this->path, $object, function(&$inner, $attr_name) use ($value)
		{
			$inner[$attr_name] = $value;
		});
	}

	/**
	 * Locate the place referred to by the path (as an array of fragments)
	 * and perform the given operation.
	 */
	function inner($path, &$object, $operation)
	{
		$piece = array_shift($path);
		if (count($path) == 0)
		{
			return $operation($object, $piece);
		}
		$pos = null;
		if (contains($piece, '[') && contains($piece, ']'))
		{
			$bits = explode('[', $piece);
			$piece = $bits[0];
			$pos = intval($bits[1]);
		}
		if (!isset($object[$piece]))
		{
			$object[$piece] = array();
		}
		if ($pos === null)
		{
			if (!is_array($object[$piece]))
			{
				return null;
			}
			return $this->inner($path, $object[$piece], $operation);
		}
		return $this->inner_array($path, $object[$piece], $pos, $operation);
	}

	/**
	 * Perform the given operation given that $linear is truly a linear array.
	 */
	function inner_array($path, &$linear, $pos, $operation)
	{
		while (count($linear) < $pos + 1)
		{
			array_push($linear, array());
		}
		return $this->inner($path, $linear[$pos], $operation);
	}
}

/**
 * A mandatory attribute. By default it will be also visible and writeable.
 */
class Mandatory extends Attribute
{
	function __construct($name, $type = "element", $dot_path = null)
	{
		parent::__construct($name, $type, $dot_path);
		$this->mandatory = true;
		$this->writeable = true;
		$this->visible = true;
	}
}

/**
 * An optional attribute. By default it will also be visible.
 */
class Optional extends Attribute
{
	function __construct($name, $type = "element", $dot_path = null)
	{
		parent::__construct($name, $type, $dot_path);
		$this->writeable = true;
		$this->visible = true;
	}
}

/**
 * A visible attribute. It cannot be written to directly.
 */
class Visible extends Attribute
{
	function __construct($name, $type = "element", $dot_path = null)
	{
		parent::__construct($name, $type, $dot_path);
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


