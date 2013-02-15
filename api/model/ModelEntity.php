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
			$this->attributes[$attribute->name] = $attribute;
		}
	}

	/**
	 * Transform an object, return a copy with all attributes in the first level.
	 * Suitable for showing or editing.
	 */
	function flatten($object)
	{
		$copy = array();
		foreach ($this->attribute as $attribute)
		{
			$attribute->flatten($object, $copy);
		}
		return $copy;
	}

	/**
	 * Filter an object, return a copy with all non-visible objects removed.
	 */
	function filter($object)
	{
		$copy = array();
		foreach ($this->attributes as $attribute)
		{
			$attribute->filter($object, $copy);
		}
		return $copy;
	}

	/**
	 * Get the attribute with the given key.
	 */
	function get_attribute($key)
	{
		if (!isset($this->attributes[$key]))
		{
			page_error('Unknown attribute ' . $key . ' for ' . $this->name);
		}
		return $this->attributes[$key];
	}

	/**
	 * Get the value of an attribute from an object by key.
	 */
	function get_value($object, $key)
	{
		return $this->get_attribute($key)->get($object);
	}

	/**
	 * Set the value for an attribute into an object.
	 */
	function set_value(&$object, $key, $value)
	{
		$this->get_attribute($key)->set($object, $value);
	}

	/**
	 * Create a new object with a random id, read contents from the request.
	 * Returns the new object.
	 */
	function create_new()
	{
		global $AUTHORIZATION;
		$id = $this->read_id(false);
		if (isset($id))
		{
			page_error('New ' . $this->name . ' cannot accept external primary key');
		}
		$object = $this->get_skeleton();
		foreach ($this->attributes as $key => $attribute)
		{
			if ($attribute->mandatory)
			{
				$attribute->read_set($object);
			}
		}
		$object['created'] = $AUTHORIZATION->get_audit_info();
		return $object;
	}

	/**
	 * Modify an existing object with all values read from the request.
	 */
	function modify_existing(&$object)
	{
		global $REQUEST;
		foreach($REQUEST->get_keys() as $key)
		{
			$this->get_attribute($key)->read_set($object);
		}
	}

	/**
	 * Get a list attribute with the given key. Shows an error if not a list.
	 */
	function get_list_attribute($key)
	{
		$list_attribute = $this->get_attribute($key);
		if (!($list_attribute instanceof BasicList))
		{
			page_error('Attribute ' . $key . ' is not a list');
		}
		return $list_attribute;
	}
	/**
	 * Check that a value is in a list attribute of an object.
	 * Returns true if the value is in the list.
	 * If the attribute is not a list shows an error.
	 */
	function check_in_list($object, $key, $value)
	{
		$list_attribute = $this->get_list_attribute($key);
		return $list_attribute->check($value, $object);
	}

	/**
	 * Add a value to a list attribute in an object.
	 */
	function add_in_list(&$object, $key, $value)
	{
		$list_attribute = $this->get_list_attribute($key);
		return $list_attribute->add($value, $object);
	}

	/**
	 * Remove a value from a list attribute in an object.
	 */
	function remove_in_list(&$object, $key, $value)
	{
		$list_attribute = $this->get_list_attribute($key);
		return $list_attribute->remove($value, $object);
	}

	/**
	 * Filter an element in a list using the entity definition for list elements.
	 * Returns a filtered copy of the element.
	 */
	function filter_in_list($key, $value)
	{
		$list_attribute = $this->get_list_attribute($key);
		return $list_attribute->filter_element($value);
	}

	/**
	 * Get the primary key from the parameter or from the request, look for it in a list attribute.
	 * Returns the value from the list with the primary key, or null if not found.
	 */
	function find_in_list($object, $key, $id = null)
	{
		$list_attribute = $this->get_list_attribute($key);
		if ($id == null)
		{
			$id = $list_attribute->read_id();
		}
		return $list_attribute->find_id($id, $object);
	}

	/**
	 * Get an object with just the primary key of a list element.
	 */
	function get_skeleton_in_list($key, $value)
	{
		$list_attribute = $this->get_list_attribute($key);
		return $list_attribute->entity->get_skeleton($value);
	}

	/**
	 * Set the content as JSON.
	 */
	function set_json_content()
	{
	    //header('Content-type: application/json');
		header('Content-type: text/plain');
	}

	/**
	 * Show an object as JSON, do not exit afterwards.
	 */
	function show($object)
	{
		$result = $this->flatten($object);
		$this->show_json($result);
	}

	/**
	 * Show all elements in an object (or a cursor) as JSON, do not exit afterwards.
	 * A cursor is especially marked (true by default).
	 * The resulting JSON object will show total number of elements,
	 * count of elements returned, and an array with the values themselves.
	 */
	function show_all($object, $cursor = true)
	{
		if ($object === null)
		{
			page_error('Empty array');
		}
		$values = array();
		foreach ($object as $attribute)
		{
			$values[] = $this->flatten($attribute);
		}
		if ($cursor)
		{
			$total = $object->count();
			$count = count($values);
			$info = $object->info();
			$offset = $info['skip'];
		}
		else
		{
			$total = count($values);
			$count = count($values);
			$offset = 0;
		}
		$this->show_values($total, $count, $offset, $values);
	}

	/**
	 * Show a number of values, including: total, count, offset and values.
	 */
	function show_values($total, $count, $offset, $values)
	{
		$result = array(
			'total' => $total,
			'count' => $count,
			'offset' => $offset,
			'values' => $values,
		);
		$this->show_json($result);
	}

	/**
	 * Show an object as JSON.
	 */
	function show_json($result)
	{
		$this->set_json_content();

		$now = date(DATE_RFC822);
		header('Cache-Control: public, no-store, max-age=0');
		header('Expires: '.$now);
		header('Last-Modified: '.$now);
		header('Vary: *');

		print json_encode($result);
		monitor('OK', $result);
	}

	/**
	 * Show a list in an object.
	 */
	function show_list($object, $key)
	{
		$list_attribute = $this->get_list_attribute($key);
		$list_attribute->show($object);
	}
}

