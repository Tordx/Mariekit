<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2021 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class DhlFileHandler
 */
class DhlFileHandler extends AbstractDhlHandler
{
    /** @var bool|resource $fd */
    protected $fd;

    /**
     * DhlFileHandler constructor.
     * @param string $file
     */
    public function __construct($file)
    {
        $this->fd = @fopen($file, 'a+');
    }

    /**
     * @param string $level
     * @param string $message
     * @param string $channel
     * @param array  $details
     * @return bool
     */
    public function log($level, $message, $channel, $details)
    {
        if (is_resource($this->fd)) {
            $string =
                sprintf('[%s] %s.%s: %s %s', date('Y-m-d H:i:s'), $channel, $level, $message, json_encode($details));
            @fwrite($this->fd, $string." []\r\n");
        }

        return true;
    }

    /**
     * @return bool
     */
    public function close()
    {
        if (is_resource($this->fd)) {
            return fclose($this->fd);
        }

        return true;
    }
}
