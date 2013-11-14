<?php
/**
 * Techworker\Nano
 *
 * Copyright (c) 2013, Benjamin Ansbach <benjaminansbach@googlemail.com>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Benjamin Ansbach nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    Techworker
 * @subpackage Nano
 * @author     Benjamin Ansbach <benjaminansbach@googlemail.com>
 * @copyright  2013 Benjamin Ansbach <benjaminansbach@googlemail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.techworker.de/
 */
namespace Techworker;

/**
 * This is a small helper function which can parse a string with placeholders identified by curly brackets
 * (eg {name}). It does support hierarchies by dividing the placeholders with a dot (eg. {name.firstname}).
 *
 * Ported from the original
 * @https://github.com/trix/nano/ Thx!
 *
 * @param string $template The string template to work on.
 * @param array  $data     The data for the replacement process.
 *
 * @return string
 */
class Nano
{
    /**
     * Template to parse and fill with data.
     *
     * @var string
     */
    private $_template;

    /**
     * Database for all placeholders inside of the template.
     *
     * @var array|object
     */
    private $_data;

    /**
     * Default replacement value.
     * @var string
     */
    private $_def = "";

    /**
     * The compilation result is cached, this is the cache database.
     *
     * @var array
     */
    private $_cache = array();

    /**
     * Creates a new instance of the Nano class.
     *
     * @param string $template The template.
     * @param array $data The database.
     * @param string $def The default replacement value.
     */
    public function __construct($template = "", $data = array(), $def = "")
    {
        $this->_template = $template;
        $this->_data = $data;
        $this->_def = $def;
    }

    /**
     * Static helper to provide a short template method without manual instantiation of the nano class.
     *
     * @param string $template The template to parse and fill.
     * @param array|object $data The data basis.
     * @param string $default Default replacement value.
     * @return string
     */
    public static function tpl($template, $data, $default = "")
    {
        return (new Nano($template, $data))->def($default)->compile();
    }

    /**
     * Sets the template for the next compiling process.
     *
     * @param string $template The template string.
     * @return $this
     */
    public function template($template)
    {
        $this->_template = $template;
        return $this;
    }

    /**
     * Sets the data for the compile process. You can either send a complete array or object opr you can fill up
     * the database step by step by providing an additional key.
     *
     * If you omit the key value and the $value parameter is an object, the database will be overwritten!
     *
     * @param mixed $value The value to add.
     * @param null|string $key The key under which the value is added.
     * @return $this
     */
    public function data($value, $key = null)
    {
        // key given? Append to data array.
        if(!is_null($key))
        {
            // cleanup
            if(!is_array($this->_data)) {
                $this->_data = array();
            }

            $this->_data[$key] = $value;
            // fast exit
            return $this;
        }

        // given data is an array, merge with the actual array
        if(is_array($value)) {
            $this->_data = array_merge($this->_data, $value);
        }

        // replace db
        if(is_object($value)) {
            $this->_data = $value;
        }

        return $this;
    }

    /**
     * Magic call method to provide a "default" method instead of the "def". Usage looks nicer for me.
     *
     * @param string $name The name of the function to call.
     * @param array $arguments The arguments of the call.
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        // check args
        if(!isset($arguments[0]) || !is_string($arguments[0])) {
            throw new \InvalidArgumentException("Missing argument 1 for Nano::default() method call.");
        }

        // check method name
        if($name === "default") {
            // call internal default method
            return $this->def($arguments[0]);
        }

        // we dont have this method.
        throw new \BadMethodCallException(
            Nano::tpl("Unknown method call {name} or fucked up arguments", array('name' => $name))
        );
    }

    /**
     * Sets the default vreplacement value.
     *
     * @param string $def The replacement value.
     * @return $this
     */
    public function def($def)
    {
        $this->_def = $def;
        return $this;
    }

    /**
     * Compiles the template with the current data and returns it.
     *
     * @return string
     */
    public function compile()
    {
        // check cache
        $unique = $this->_unique();
        if(isset($this->_cache[$unique])) {
            return $this->_cache[$unique];
        }

        // the callback of the preg_replace_callback call (just for the readability).
        $callback = function($matches) {
            return $this->_getValue($matches[1]);
        };

        // replace placeholders
        return($this->_cache[$unique] = preg_replace_callback("/\{([\w\.\|%:]*)\}/", $callback, $this->_template));
    }

    /**
     * Returns the compiled string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->compile();
    }

    /**
     * Gets the value from the given data variable identified by the given key.
     *
     * @param String $key The key.
     * @param array $data The database.
     * @return string
     */
    private function _getValue($key)
    {
        $keys = explode(":", $key);
        $value = $this->_data;

        $key = $modifier = false;
        while(count($keys) > 0)
        {
            $key = array_shift($keys);
            if(strpos($key, "|") !== false) {
                list($key, $modifier) = explode("|", $key);
            }

            try
            {
                $value = $this->_access($value, $key);
            }
            catch(Exception $ex) { return $this->_def; }
        }

        return $modifier ? sprintf($modifier, strval($value)) : strval($value);
    }

    /**
     * Detects the type of the current value and return the appropriate "subvalue". If the value is neither
     * an array nor an object OR the key does not exist, the function will be return the given default value.
     *
     * @param object|array $value The value to check.
     * @param string $key The key to check in the $value.
     * @return mixed
     */
    private function _access($value, $key)
    {
        switch(gettype($value))
        {
            case "array":
                if(!isset($value[$key])) {
                    break;
                }

                return $value[$key];

            case "object":
                if(!isset($value->{$key})) {
                    break;
                }

                return $value->{$key};
        }

        throw new Exception(self::tpl("Key {key} not found", array('key' => $key)));
    }


    /**
     * Gets a unique identifier for the current template + data.
     *
     * @return string
     */
    private function _unique()
    {
        return md5($this->_template . $this->_def . serialize($this->_data));
    }
}