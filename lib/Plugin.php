<?php
/**
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 *
 * @category    laemmi-yourls-validate-keyword
 * @author      Michael Lämmlein <laemmi@spacerabbit.de>
 * @copyright   ©2016 laemmi
 * @license     http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version     1.0.0
 * @since       23.02.16
 */

namespace Laemmi\Yourls\Plugin\ValidateKeyword;

use Laemmi\Yourls\Plugin\AbstractDefault;

class Plugin extends AbstractDefault
{
    /**
     * Namespace
     */
    const APP_NAMESPACE = 'laemmi-yourls-validate-keyword';

    ####################################################################################################################

    /**
     * Yourls action plugins_loaded
     */
    public function action_plugins_loaded()
    {
        $this->loadTextdomain();
    }

    /**
     * Filter sanitize_string
     *
     * @return mixed
     */
    public function filter_sanitize_string()
    {
        list ($valid, $string) = func_get_args();

        $valid = strtolower($valid);

        return $valid;
    }

    /**
     * Filter get_shorturl_charset
     *
     * @return mixed
     */
    public function filter_get_shorturl_charset()
    {
        list ($charset) = func_get_args();

        $charset .= '-_';

        return $charset;
    }

    /**
     * Filter shunt_add_new_link
     *
     * @return array|boolean
     */
    public function filter_shunt_add_new_link()
    {
        list ($return, $url, $keyword, $title) = func_get_args();

        if ($this->_validateKeyword($keyword)) {
            return $return;
        }

        $return = [
            'status'    => 'fail',
            'code'      => 'error:illegalCharacter',
            'message'   => yourls__('Illegal character in Short URL', self::APP_NAMESPACE),
            'errorCode' => '400'
        ];

        return $return;
    }

    /**
     * Filter shunt_edit_link
     *
     * @return array
     */
    public function filter_shunt_edit_link()
    {
        list ($return, $keyword, $url, $keyword, $newkeyword, $title) = func_get_args();

        if ($this->_validateKeyword($newkeyword)) {
            return $return;
        }

        $return = [
            'status'    => 'fail',
            'code'      => 'error:illegalCharacter',
            'message'   => yourls__('Illegal character in Short URL', self::APP_NAMESPACE),
            'errorCode' => '400'
        ];

        return $return;
    }

    ####################################################################################################################

    /**
     * Validate keyword
     *
     * @param $keyword
     * @return bool
     */
    private function _validateKeyword($keyword): bool
    {
        if (yourls_apply_filter('sanitize_string', $keyword, $keyword) === yourls_sanitize_string($keyword)) {
            return true;
        }

        return false;
    }
}