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

/**
 * XSS Protection Context Specifiec.
 *
 * @package    arabcoders\variables
 * @author     Abdul.Mohsen B. A. A. <admin@arabcoders.org>
 */

class Clean
{
    /**
     * For HTML context ONLY.
     * Sanitize/Filter < and > so that attacker can not leverage them for JavaScript execution.
     *
     * @param string $input Input text.
     *
     * @return string
     */
    public function html( $input )
    {
        return stripslashes( str_replace( [ "<", ">" ], [ "&lt;", "&gt;" ], $input ) );
    }

    /**
     * For Script context ONLY.
     * Sanitize/Filter meta or control characters that attacker may use to break the context e.g.
     * "; confirm(1); " OR '; prompt(1); // OR </script><script>alert(1)</script>
     * \ and % are filtered because they may break the page e.g., \n or %0a
     * & is sanitized because of complex or nested context (if in use)
     *
     * @param string $input Input text.
     *
     * @return string
     */
    public function script( $input )
    {
        $replaceText = [ "\"", "<", "'", "\\\\", "%", "&" ];
        $replaceWith = [ "&quot;", "&lt;", "&apos;", "&bsol;", "&percnt;", "&amp;" ];

        return stripslashes( str_replace( $replaceText, $replaceWith, $input ) );
    }

    /**
     * For attribute context ONLY.
     * Sanitize/Filter meta or control characters that attacker may use to break the context e.g.,
     * "onmouseover="alert(1) OR 'onfocus='confirm(1) OR ``onmouseover=prompt(1)
     * back-tick i.e., `` is filtered because old IE browsers treat it as a valid separator.
     *
     * @param string $input Input text.
     *
     * @return string
     */
    public function attr( $input )
    {
        $replaceText = [ "\"", "'", "`" ];
        $replaceWith = [ "&quot;", "&apos;", "&grave;" ];

        return stripslashes( str_replace( $replaceText, $replaceWith, $input ) );
    }

    /**
     * For style context ONLY.
     * Sanitize/Filter meta or control characters that attacker may use to execute JavaScript e.g.,
     * ( is filtered because width:expression(alert(1))
     * & is filtered in order to stop decimal + hex + HTML5 entity encoding
     * < is filtered in case developers are using <style></style> tags instead of style attribute.
     * < is filtered because attacker may close the </style> tag and then execute JavaScript.
     *
     * @param string $input Input text.
     *
     * @return string
     */
    public function style( $input )
    {
        $replaceText = [ "\"", "'", "``", "(", "\\\\", "<", "&" ];
        $replaceWith = [ "&quot;", "&apos;", "&grave;", "&lpar;", "&bsol;", "&lt;", "&amp;" ];

        return stripslashes( str_replace( $replaceText, $replaceWith, $input ) );
    }

    /**
     * For url context ONLY.
     * XSS protection function for URL context
     * Only allows URLs that start with http(s) or ftp. e.g.,
     * https://www.google.com
     * Protection against JavaScript, VBScript and Data URI JavaScript code execution etc.
     *
     * @param string $url url.
     *
     * @return string
     */
    public function url( $url )
    {

        if ( preg_match( "#^(?:(?:https?|ftp):{1})\/\/[^\"\s\\\\]*.[^\"\s\\\\]*$#iu", (string) $url, $match ) )
        {
            return $match[0];
        }

        return 'javascript:void(0)';
    }
}