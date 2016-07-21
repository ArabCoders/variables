<?php
/**
 * This file is part of {@see \arabcoders\variables} package.
 *
 * (c) 2013-2016 Abdulmohsen B. A. A.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace arabcoders\variables\Interfaces;

/**
 * Variables Interface.
 *
 * @package arabcoders\variables
 * @author  Abdul.Mohsen B. A. A. <admin@arabcoders.org>
 */
interface Variables
{
    /**
     * GET Variables.
     *
     * @param string  $key       Parameter Key
     * @param mixed   $default   value
     * @param boolean $isNumeric is Numeric Value
     * @param array   $options   options
     *
     * @return mixed
     */
    public function get( string $key, $default = null, bool $isNumeric = false, array $options = [ ] );

    /**
     * POST Variables.
     *
     * @param string  $key       Parameter Key
     * @param mixed   $default   value
     * @param boolean $isNumeric is Numeric Value
     * @param array   $options   options
     *
     * @return mixed
     */
    public function post( string $key, $default = false, bool $isNumeric = false, array $options = [ ] );

    /**
     * PUT Variables.
     *
     * @param string  $key       Parameter Key
     * @param mixed   $default   value
     * @param boolean $isNumeric is Numeric Value
     * @param array   $options   options
     *
     * @return mixed
     */
    public function put( string $key, $default = false, bool $isNumeric = false, array $options = [ ] );
}