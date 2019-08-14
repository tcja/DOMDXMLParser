<?php

namespace Tcja;

/**
 * DOMDXMLParser
 *
 * DOMDXMLParser is a class that can handle multiple CRUD tasks with XML files using The DOMDocument class
 *
 * @license MIT License
 * @author  Trim C.
 *
 * @version 1.1.15
 */
class DOMDXMLParser
{
    /**
	 *
	 * @var bool $layoutStyle	XML style layout
	 * */
    protected $layoutStyle = false;
    /**
	 *
	 * @var string $DOMPath	XML file path
	 * */
    protected $DOMPath;
    /**
	 *
	 * @var object $DOM	DOMDocument
	 * */
    protected $DOM;
    /**
	 *
	 * @var mixed $nodeData	Collected node data
	 * */
    protected $nodeData;

    /**
	 * Initialize and prepare the DOM to be handled from the XML file
	 *
	 * @param 	string			$path		The path to the XML file
	 * @return	void
	 **/
	public function __construct($path)
    {
        $this->DOMPath = $this->setDOMPath($path);
		$this->DOM = $this->setDOM();
		$this->DOM->preserveWhiteSpace = false;
		$this->DOM->formatOutput = true;
        $this->DOM->load($this->DOMPath);
    }
    /**
	 * Check a node existance based on its value or one of its attribute/value pair
	 *
	 * @param 	string			$selector		Any node value or attribute/value pair
	 * @return	bool						    Return false if no corresponding data found, if yes return true
	 **/
	public function checkNode(...$selector)
	{
		if (count($selector) === 2) {
			$target = $this->XPathQuery('//*[@' . $selector[0] . '="' . $selector[1] . '"]');
		} else {
			$target = $this->XPathQuery('//*[text()="' . $selector[0] . '"]');
		}

        return ($target) ? true : false;
    }
    /**
	 * Select a node based on its value or one of its attribute/value pair
	 *
	 * @param 	mixed			$selector		Any node value or attribute/value pair
	 * @return	object							If no corresponding data found : set "nodeData" property to false,
	 * 											if yes : set the corresponding node data to "nodeData" property, return $this in any case
	 **/
	public function pickNode(...$selector)
	{
		if (count($selector) === 2) {
			$target = $this->XPathQuery('//*[@' . $selector[0] . '="' . $selector[1] . '"]');
		} else {
			$target = $this->XPathQuery('//*[text()="' . $selector[0] . '"]');
			if (!$target) {
				$target = $this->DOM->getElementsByTagName($selector[0]);
			}
		}

		($target->length) ? $this->nodeData = $target : $this->nodeData = false;

		return $this;
    }
    /**
	 * Collect corresponding data (attributes/values and node value) from the specified node, chain with $this->pickNode('foo')->fetchData() for example
	 *
	 * @param 	mixed			$selector		If an attribute or tag is set, collect all the values which match only the attribute or tag
	 * @return	mixed							Store an array of [attribute => value] in $this->nodeData property if data was found and return $this, put  $this->nodeData to false if no match,
     *                                          return $this in any case
	 **/
	public function fetchData($selector = null)
	{
        if ($this->nodeData) {
            if (empty($selector)) {
                foreach ($this->nodeData as $node) {
                    if ($node->childNodes->length <= 1) {
                        if ($this->checkLayoutStyle()) {
                            foreach ($node->attributes as $value) {
                                $names[] = $value->name;
                                $values[] = $value->value;
                            }
                            $names[] = 'nodeValue';
                            $values[] = $node->nodeValue;
                        } else {
                            foreach ($node->parentNode->childNodes as $childNodes) {
                                $names[] = $childNodes->tagName;
                                $values[] = $childNodes->nodeValue;
                            }
                        }
                    } else {
                        foreach ($node->childNodes as $childNodes) {
                            $names[] = $childNodes->tagName;
                            $values[] = $childNodes->nodeValue;
                        }
                    }
                    $array[] = array_combine($names, $values);
                }

                $this->nodeData = (count($array) == 1) ? array_merge($array[0]) : $array;

                return $this;
            } else {
                if ($this->nodeData->length && $this->nodeData->item(0)->getAttribute($selector)) {
                    foreach ($this->nodeData as $node) {
                        $names[] = $selector;
                        $values[] = $node->getAttribute($selector);
                        $array[] = array_combine($names, $values);
                    }

                    $this->nodeData = $array;
                } else {
                    foreach ($this->nodeData as $node) {
                        foreach ($node->childNodes as $childNodes) {
                            if ((!empty($childNodes->tagName) && $childNodes->tagName == $selector) || $selector == 'nodeValue') {
                                $names[] = ($selector == 'nodeValue') ? 'nodeValue' : $childNodes->tagName;
                                $values[] = $childNodes->nodeValue;
                            }
                        }
                        $array[] = array_combine($names, $values);
                    }
                }

                $this->nodeData = (count($array) == 1) ? array_merge($array[0]) : $array;

                return $this;
            }
        }

        $this->nodeData = false;

        return $this;
    }
    /**
	 * Sort a multi-dimentional array by a node attribute, chain with $this->pickNode('foo')->sortBy('bar')->toArray() for example
	 *
	 * @param 	string			$attr		Any node attribute
	 * @param 	bool			$DESC		If set to true: sort the array in descending order
	 * @return	object						Return $this
	 **/
    public function sortBy($attr, $DESC = false)
	{
		if (!$DESC) {
			usort($this->nodeData, function($a, $b) use ($attr) {
				return strtolower($a[$attr]) <=> strtolower($b[$attr]);
			});
		} else {
			usort($this->nodeData, function($a, $b) use ($attr) {
				return strtolower($b[$attr]) <=> strtolower($a[$attr]);
			});
		}

		return $this;
    }
    /**
	 * Return the array of the fetched node data, chain with $this->pickNode('foo')->toArray() for example
	 *
	 * @return	mixed   Return the array of the fetched node data or false if no match
	 **/
    public function toArray()
	{
		if ($this->nodeData) {
			if (!empty($this->nodeData[0]) && is_array($this->nodeData[0]) && count($this->nodeData[0]) === 1) {
				foreach ($this->nodeData as $key => $value) {
					$array[] = $value[key($value)];
				}

				return $array;
			} else {
				return $this->nodeData;
			}
		}

		return false;
    }
    /**
	 * Perform an xpath query
	 *
	 * @param 	string			$query		Xpath query
	 * @return	mixed						Return object of the result of the query if it matched something, return false if no match
	 **/
    protected function XPathQuery($query)
	{
        $xpath = new \DOMXpath($this->DOM);
		$result = $xpath->query($query);

		return ($result->length > 0) ? $result : false;
    }
    /**
	 * Compare an attribute value with the previous one in the same node, chain with $this->pickNode('foo', 'bar')->compareTo('baz', 'qux') for example
	 *
	 * @param 	string			$attr		Attribute to compare to
	 * @param 	string			$value		Attribute's value to compare to
	 * @return	bool						Return true if the attribute/value parameters matched the previous attribute/value in the same node, return false if no match
	 **/
	public function compareTo($attr, $value)
	{
		if ($this->nodeData && !empty($this->nodeData->item(0)->getAttribute($attr))) {
			return ($this->nodeData->item(0)->getAttribute($attr) == $value) ? true : false;
		} else {
			foreach ($this->nodeData->item(0)->parentNode->childNodes as $node) {
				if ($node->nodeValue == $value) {
					return true;
				}
			}
		}

		return false;
    }
    /**
	 * Change single or multiple attribute value, chain with $this->pickNode('foo', 'bar')->changeData(['foo' => 'baz', 'qux' => 'quux']) for example
	 *
	 * @param 	array			$data			Array of [attribute => value] to change or just [...]->changeData('attribute', 'value') to change/add a single attribute/value data
	 * @return	bool							Return true on change success or false if failed
	 **/
	public function changeData(...$data)
	{
		if ($this->checkLayoutStyle()) {
			if (is_array($data[0])) {
				if (!empty($this->nodeData->item(0)->attributes->item(0))) {
					foreach ($this->nodeData as $node) {
						foreach ($data[0] as $attr => $value) {
							if ($attr == 'CDATA') {
								(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createCDATASection($value), $node->firstChild) : $node->appendChild($this->DOM->createCDATASection($value));
							} elseif ($attr == 'textNode') {
								(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createTextNode($value), $node->firstChild) : $node->appendChild($this->DOM->createTextNode($value));
							} else {
								$node->setAttribute($attr, $value);
							}
						}
					}
				} else {
					foreach ($data[0] as $attr => $value) {
						$query = $this->XPathQuery('//' . $attr);
						foreach ($query as $node) {
							if ($node->firstChild->nodeName == '#cdata-section') {
								(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createCDATASection($value), $node->firstChild) : $node->appendChild($this->DOM->createCDATASection($value));
							} elseif ($node->firstChild->nodeName == '#text') {
								(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createTextNode($value), $node->firstChild) : $node->appendChild($this->DOM->createTextNode($value));
							}
						}
					}
				}
			} else {
				if ($data[0] == 'CDATA') {
					$this->setValue($data[1]);
				} elseif ($data[0] == 'textNode') {
					$this->setTextValue($data[1]);
				} else {
					$this->nodeData->item(0)->setAttribute($data[0], $data[1]);
				}
			}
		} else {
			if (is_array($data[0])) {
				foreach ($this->nodeData->item(0)->parentNode->childNodes as $node) {
					foreach ($data[0] as $attr => $value) {
                        if (!$this->XPathQuery('//' . $attr)) {
                            $newNode = $this->DOM->createElement($attr, $value);
                            $node->parentNode->appendChild($newNode);
                            continue;
                        }
						if ($node->tagName == $attr && $node->firstChild->nodeName == '#cdata-section') {
							(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createCDATASection($value), $node->firstChild) : $node->appendChild($this->DOM->createCDATASection($value));
						} elseif ($node->tagName == $attr && $node->firstChild->nodeName == '#text') {
							(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createTextNode($value), $node->firstChild) : $node->appendChild($this->DOM->createTextNode($value));
						}
					}
				}
			} else {
				foreach ($this->nodeData->item(0)->parentNode->childNodes as $node) {
					if ($node->tagName == $data[0] && $node->firstChild->nodeName == '#cdata-section') {
						(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createCDATASection($data[1]), $node->firstChild) : $node->appendChild($this->DOM->createCDATASection($data[1]));
					} elseif ($node->tagName == $data[0] && $node->firstChild->nodeName == '#text') {
						(!empty($node->firstChild)) ? $node->replaceChild($this->DOM->createTextNode($data[1]), $node->firstChild) : $node->appendChild($this->DOM->createTextNode($data[1]));
					}
				}
			}
		}

		return ($this->DOM->save($this->DOMPath)) ? true : false;
    }
    /**
	 * Add a new node with attribute/value pair
	 *
	 * @param 	string			$node			Name of the new node to add
	 * @param 	array			$data			Array of [attribute => value] to add in the new node
	 * @param 	bool			$CDATA			If set to true, will write all non-attribute nodes values using CDATA
	 * @return	bool							Return true on change success or false if failed
	 **/
	public function addNode($node, $data, $CDATA = false)
	{
        if (!$this->getTotalItems()) {
            $newNode = $this->DOM->createElement($node);
            if (!$this->layoutStyle) {
                foreach ($data as $attr => $value) {
                    if ($attr != 'CDATA' && $attr != 'textNode') {
                        $newNode->setAttribute($attr, $value);
                    }
                }
                if (!empty($data['CDATA'])) {
                    $newNode->appendChild($this->DOM->createCDATASection($data['CDATA']));
                } elseif (!empty($data['textNode'])) {
                    $newNode->appendChild($this->DOM->createTextNode($data['textNode']));
                }
            } else {
                foreach ($data as $attr => $value) {
                    $innerNode = $this->DOM->createElement($attr);
                    $innerNode->appendChild(($CDATA) ? $this->DOM->createCDATASection($value) : $this->DOM->createTextNode($value));
                    $newNode->appendChild($innerNode);
                }
            }
            $this->DOM->documentElement->appendChild($newNode);
        } elseif ($this->getTotalItems()) {
            $newNode = $this->DOM->createElement($node);
            if ($this->checkLayoutStyle()) {
                foreach ($data as $attr => $value) {
                    if ($attr != 'CDATA' && $attr != 'textNode') {
                        $newNode->setAttribute($attr, $value);
                    }
                }
                if (!empty($data['CDATA'])) {
                    $newNode->appendChild($this->DOM->createCDATASection($data['CDATA']));
                } elseif (!empty($data['textNode'])) {
                    $newNode->appendChild($this->DOM->createTextNode($data['textNode']));
                }
            } else {
                foreach ($data as $attr => $value) {
                    $innerNode = $this->DOM->createElement($attr);
                    $innerNode->appendChild(($CDATA) ? $this->DOM->createCDATASection($value) : $this->DOM->createTextNode($value));
                    $newNode->appendChild($innerNode);
                }
            }
            $this->DOM->documentElement->appendChild($newNode);
        }

		return ($this->DOM->save($this->DOMPath)) ? true : false;
    }
    /**
	 * Remove a specific node, chain with $this->pickNode('foo', 'bar')->remove() for example
	 *
	 * @return	bool		Return true on remove success or false if failed
	 **/
	public function remove()
	{
		if ($this->checkLayoutStyle()) {
			$this->nodeData->item(0)->parentNode->removeChild($this->nodeData->item(0));
		} else {
			$this->nodeData->item(0)->parentNode->parentNode->removeChild($this->nodeData->item(0)->parentNode);
		}

		return ($this->DOM->save($this->DOMPath)) ? true : false;
    }
    /**
	 * Check the current DOM layout style
	 *
	 * @return	bool	Return true if layout style is the default one (attribute/value pair) or false if it is the node -> value pair
	 **/
	public function checkLayoutStyle()
	{
        return ($this->DOM->childNodes->item(0)->childNodes->item(0)->attributes->length) ? true : false;
    }
    /**
	 * Set the DOM XML file path
	 *
	 * @return	string	Return the path
	 **/
	public function setDOMPath($path)
	{
		return $path;
    }
    /**
	 * Set the layout style to single node -> value pair
	 *
     * @param 		string			$style		If set to true : set the layout style to single node -> value pair
	 * @return	    void
	 **/
	public function setLayoutStyle($style)
	{
		$this->layoutStyle = $style;
    }
    /**
	 * Initialize DOMDocument class
	 *
	 * @param 		string			$version		XML file version, defaults to 1.0
	 * @param 		string			$encoding		XML file encoding, defaults to UTF-8
	 * @return		object							Return an instance of DOMDocument class
	 **/
	public function setDOM($version = '1.0', $encoding = 'UTF-8')
	{
		return new \DOMDocument($version, $encoding);
	}
	/**
	 * Set the CDATA value of the current node, chain with $this->pickNode('foo')->setValue('bar) for example
	 *
	 * @param 	string			$value		The value of the node to set
	 * @return	bool						Return true on change success or false if failed
	 **/
	public function setValue($value)
	{
		if (!empty($this->nodeData->item(0)->firstChild)) {
			$this->nodeData->item(0)->replaceChild($this->DOM->createCDATASection($value), $this->nodeData->item(0)->firstChild);
		} else {
			$this->nodeData->item(0)->appendChild($this->DOM->createCDATASection($value));
		}

		return ($this->DOM->save($this->DOMPath)) ? true : false;
	}
	/**
	 * Set the TEXT value of the current node, chain with $this->pickNode('foo')->setTextValue('bar) for example
	 *
	 * @param 	string			$value		The value of the node to set
	 * @return	bool						Return true on change success or false if failed
	 **/
	public function setTextValue($value)
	{
		if (!empty($this->nodeData->item(0)->firstChild)) {
			$this->nodeData->item(0)->replaceChild($this->DOM->createTextNode($value), $this->nodeData->item(0)->firstChild);
		} else {
			$this->nodeData->item(0)->appendChild($this->DOM->createTextNode($value));
		}

		return ($this->DOM->save($this->DOMPath)) ? true : false;
	}
	/**
	 * Get an attribute value corresponding to the current node, chain with $this->pickNode('foo', 'bar')->getAttr('baz') for example
	 *
	 * @param 	string			$attr		Attribute to get the value from
	 * @return	mixed						Return the attribute value if found in the node, return false if no match
	 **/
	public function getAttr($attr)
	{
		return ($this->nodeData) ? $this->nodeData->item(0)->getAttribute($attr) : false;
	}
	/**
	 * Get the value of the current node, chain with $this->pickNode('foo')->getValue() for example
	 *
	 * @param 	string			$tag		If tag name is set, will get the value of the corresponding tag in the node
	 * @return	mixed						Return the attribute value if found in the node, return false if no match
	 **/
	public function getValue($tag = null)
	{
		if (!empty($tag)) {
			foreach ($this->nodeData->item(0)->parentNode->childNodes as $node) {
				if ($node->tagName == $tag) {
					return $node->nodeValue;
				}
			}
		} else {
			return ($this->nodeData) ? $this->nodeData->item(0)->nodeValue : false;
		}

		return false;
	}
	/**
	 * Get the highest value of a node name or attribute value from all nodes, chain with $this->pickNode('foo')->getHighestValue('bar') for example
	 *
	 * @param 	string			$selector		Any node name or attribute/value pair
	 * @return	mixed						    Return the highest value or return false if no match
	 **/
	public function getHighestValue($selector)
	{
        return ($this->nodeData && !empty($selector)) ? max($this->fetchData($selector)->toArray()) : false;
	}
	/**
	 * Get total number of items in the current DOM
	 *
	 * @return	mixed   Return the total number or return 0 if no item found
	 **/
	public function getTotalItems()
	{
        return ($this->DOM && !empty($this->DOM->childNodes->item(0)->childNodes->item(0)->childNodes)) ? $this->DOM->childNodes->item(0)->childNodes->length : 0;
	}
    /**
	 *
	 * @return	string
	 **/
	public function getDOMPath()
	{
		return $this->DOMPath;
	}
    /**
	 *
	 * @return	object
	 **/
	public function getDOM()
	{
		return $this->DOM;
    }
}
