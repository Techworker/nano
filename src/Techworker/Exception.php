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
 * Simple, small and fast String templating functionality ported from the original https://github.com/trix/nano.
 *
 * @package    Techworker
 * @see        https://github.com/trix/nano.
 * @subpackage Nano
 * @author     Benjamin Ansbach <benjaminansbach@googlemail.com>
 * @copyright  2013 Benjamin Ansbach <benjaminansbach@googlemail.com>
 * @license    http://www.opensource.org/licenses/BSD-3-Clause  The BSD 3-Clause License
 * @link       http://www.techworker.de/
 */
class Nano
{
    // new implementation
    /*
     *
new Nano("Hallo {name}")->data("name", "Ben")->compile();
new Nano()->template("Hallo {name}")->data("name", "Ben")->compile();
new Nano()->template("Hallo {name}")->data("name", "Ben")->compile($data);

     */
    public function __construct($template = "", $data = array())
    {

    }

    public function data()
    {

    }

    public function __toString()
    {
        return $this->compile();
    }

    /**
     * Parses the given template and tries to apply the given values (accessible via ArrayAccess)
     * to the placeholders.
     *
     * @param String $template The template to parse and fill.
     * @param array|object $data The data basis.
     * @return String
     */
    public static function tpl($template, $data, $default = "")
    {
        // the callback of the preg_replace_callback call (just for the readability).
        $callback = function($matches) use($data, $default) {
            return self::_getValue($matches[1], $data, $default);
        };

        // replace placeholders
        return preg_replace_callback("/\{([\w\.\|%:]*)\}/", $callback, $template);
    }

    /**
     * Gets the value from the given data array identified by the iven key.
     * To provide deeper structures
     *
     * @param String $key The key.
     * @param array $data The database.
     * @return string
     */
    private static function _getValue($key, $data, $default)
    {
        $keys = explode(":", $key);
        $value = $data;

        $key = $modifier = false;
        while(count($keys) > 0)
        {
            $key = array_shift($keys);
            if(strpos($key, "|") !== false) {
                list($key, $modifier) = explode("|", $key);
            }

            try
            {
                $value = self::_access($value, $key);
            }
            catch(\Exception) { return $default; }
        }

        return $modifier ? sprintf($modifier, strval($value)) : strval($value);
    }

    /**
     * Detects the type of the current value and return the appropriate "subvalue". If the value is neither
     * an array nor an object OR the key does not exist, the function will be return the given default value.
     *
     * @param object|array $value The value to check.
     * @param string $key The key to check in the $value.
     * @param string $default The default value.
     * @return mixed
     */
    private static function _access($value, $key)
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

        throw new \Exception(self::tpl("Key {key} not found", array('key' => $key)));
    }
}