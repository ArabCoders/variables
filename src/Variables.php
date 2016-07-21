<?php
/**
 * This file is part of {@see \arabcoders\variables} package.
 *
 * (c) 2013-2016 Abdulmohsen B. A. A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace arabcoders\variables;

use \arabcoders\variables\Exceptions\VariablesException,
    \arabcoders\variables\Interfaces\Variables as VariablesInterface;

/**
 * Variables
 *
 * @package arabcoders\variables
 * @author  Abdul.Mohsen B. A. A. <admin@arabcoders.org>
 */
class Variables implements VariablesInterface
{
    /**
     * @var string holds the Request Type
     */
    protected $method;

    /**
     * @var array holds Request Variables
     */
    public $params = [ ];

    /**
     * Class Constructor
     *
     * @param array $options
     *
     * @throws VariablesException
     */
    public function __construct( array $options = [ ] )
    {
        if ( !empty( $options['method'] ) )
        {
            $this->method = strtoupper( $options['method'] );
        }
        else
        {
            $this->method = ( !empty( $_SERVER['REQUEST_METHOD'] ) ) ? $_SERVER['REQUEST_METHOD'] : null;
        }

        if ( in_array( $this->method, [ 'PUT', 'DELETE', 'HEAD', 'OPTIONS' ] ) )
        {
            parse_str( file_get_contents( 'php://input' ), $this->params );
        }
        elseif ( $this->method == 'GET' )
        {
            $this->params = &$_GET;
        }
        elseif ( $this->method == 'POST' )
        {
            $this->params = &$_POST;
        }
        else
        {
            throw new VariablesException( sprintf( 'Unable to Parse Request Type [%s]', $this->method ) );
        }

        if ( !empty( $options['mock'] ) )
        {
            $this->params = $options['mock'];
        }
    }

    public function get( string $key, $default = false, bool $isNumeric = false, array $options = [ ] )
    {
        return $this->returnVariable( $key, 'GET', $default, $isNumeric, $options );
    }

    public function post( string $key, $default = false, bool $isNumeric = false, array $options = [ ] )
    {
        return $this->returnVariable( $key, 'POST', $default, $isNumeric, $options );
    }

    public function put( string $key, $default = false, bool $isNumeric = false, array $options = [ ] )
    {
        return $this->returnVariable( $key, 'PUT', $default, $isNumeric, $options );
    }

    /**
     * Return variable
     *
     * @param string $key       key
     * @param string $type      type
     * @param mixed  $default   default value.
     * @param bool   $isNumeric value is numeric
     * @param array  $options
     *
     * @return mixed
     */
    private function returnVariable( string $key, string $type, $default = false, bool $isNumeric = false, array $options = [ ] )
    {
        if ( !array_key_exists( $key, $this->params ) )
        {
            return $default;
        }

        if ( !$this->defaultOut( $type ) )
        {
            return $default;
        }

        $value = &$this->params[$key];

        if ( isset( $options['index'] ) )
        {
            if ( is_array( $value ) && array_key_exists( $options['index'], $value ) )
            {
                return ( $isNumeric ) ? (int) $value[$options['index']] : $value[$options['index']];
            }
            else
            {
                return $default;
            }
        }

        if ( array_key_exists( 'minLength', $options ) && $options['minLength'] > mb_strlen( $value ) )
        {
            return $default;
        }

        if ( $isNumeric && !$this->is_int( $value ) )
        {
            return $default;
        }

        if ( $isNumeric && isset( $options['max'] ) && $value > $options['max'] )
        {
            return $default;
        }

        if ( !empty( $options['raw'] ) )
        {
            return $value;
        }

        if ( $this->is_int( $value ) )
        {
            return ( PHP_INT_MAX > $value ) ? (int) $value : $value;
        }

        if ( is_float( $value ) )
        {
            return (float) $value;
        }

        if ( is_array( $value ) )
        {
            return (array) array_map( [ $this, 'clean' ], $value );
        }

        if ( is_string( $value ) )
        {
            return (string) $this->clean( $value, $options );
        }

        return $default;
    }

    /**
     * Clean Variable.
     *
     * @param string $text
     * @param array  $options .
     *
     * @return string
     */
    public function clean( $text, array $options = [ ] )
    {
        if ( is_array( $text ) )
        {
            return array_map( [ $this, 'clean' ], $text );
        }

        $text = trim( $text );
        $text = strip_tags( $text );
        $text = htmlspecialchars( str_replace( "\'", "'", $text ), ENT_COMPAT, 'UTF-8' );
        $text = rtrim( $text, '\\' );
        $text = str_replace( "'", "\'", $text );

        // -- Replace text out.
        if ( !empty( $options['replace'] ) )
        {
            $text = str_replace( $options['replace'], ( ( !empty( $options['replaceWith'] ) ) ? $options['replaceWith'] : '' ), $text );
        }

        // -- Limit String Length.
        if ( !empty( $options['maxLength'] ) )
        {
            $text = mb_substr( $text, ( ( !empty( $options['start'] ) ) ? $options['start'] : 0 ), $options['maxLength'], 'UTF-8' );
        }

        return $text;
    }

    /**
     * Default to value if request type
     * does not match.
     *
     * @param string $type
     *
     * @return boolean
     */
    protected function defaultOut( $type )
    {
        $methods = [ 'GET', 'POST', 'PUT' ];

        if ( in_array( $this->method, $methods ) )
        {
            return true;
        }

        return ( $this->method != $type ) ? false : true;
    }

    /**
     * Check if the string is pure digits.
     *
     * @param string $text
     *
     * @return boolean
     */
    protected function is_int( $text )
    {
        if ( is_array( $text ) )
        {
            return false;
        }

        return ( preg_match( '/^[0-9]{1,}$/i', $text ) ) ? true : false;
    }
}