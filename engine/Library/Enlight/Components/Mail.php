<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

/**
 * Basic Enlight mail component.
 *
 * The Enlight_Components_Mail is a component for sending an email. It extends the zend form
 * with php mailer functions.
 *
 * @category   Enlight
 *
 * @copyright  Copyright (c) 2011, shopware AG (http://www.shopware.de)
 * @license    http://enlight.de/license     New BSD License
 */
class Enlight_Components_Mail extends Zend_Mail
{
    /**
     * Flag if the body is a html body
     *
     * @var bool
     */
    protected $_isHtml = false;

    /**
     * Mail address from the mail sender
     *
     * @var null|array|string
     */
    protected $_fromName = null;

    /**
     * Property for the plain body
     *
     * @var null
     */
    protected $_plainBody = null;

    /**
     * Property for the plain body text. Can be filled by setBodyText function.
     *
     * @var null
     */
    protected $_plainBodyText = null;

    /**
     * Property for the plain subject. Can be filled by setSubject function.
     *
     * @var null
     */
    protected $_plainSubject = null;

    /**
     * Magic setter method
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        switch ($name) {
            case 'From':
                $fromName = $this->getFromName();
                $this->clearFrom();
                $this->setFrom($value, $fromName);
                break;
            case 'FromName':
                $from = $this->getFrom();
                $this->clearFrom();
                $this->setFrom($from, $value);
                break;
            case 'Subject':
                $this->clearSubject();
                $this->setSubject($value);
                break;
            case 'Body':
                if ($this->_isHtml) {
                    $this->setBodyHtml($value);
                } else {
                    $this->setBodyText($value);
                }
                break;
            case 'AltBody':
                if ($this->_isHtml) {
                    $this->setBodyText($value);
                }
                break;
        }
    }

    /**
     * Magic getter method
     *
     * @param string $name
     *
     * @return null|string|\unknown
     */
    public function __get($name)
    {
        switch ($name) {
            case 'From':
                return $this->getFrom();
                break;
            case 'FromName':
                return $this->getFromName();
                break;
            case 'Subject':
                return $this->getSubject();
                break;
            case 'Body':
                if ($this->_isHtml) {
                    return $this->_plainBody;
                }

                    return $this->_plainBodyText;

                break;
            case 'AltBody':
                return $this->_plainBodyText;
                break;
        }
    }

    /**
     * Set mail html mode
     *
     * @deprecated
     *
     * @param bool $isHtml
     */
    public function IsHTML($isHtml = true)
    {
        $this->_isHtml = (bool) $isHtml;
    }

    /**
     * Add a recipient to mail
     *
     * @deprecated
     *
     * @param string $email
     * @param string $name
     *
     * @return Zend_Mail
     */
    public function AddAddress($email, $name = '')
    {
        return $this->addTo($email, $name);
    }

    /**
     * Clears list of recipient email addresses
     *
     * @deprecated
     *
     * @return Zend_Mail
     */
    public function ClearAddresses()
    {
        return $this->clearRecipients();
    }

    /**
     * Sets From-header and sender of the message
     *
     * @param string $email
     * @param string $name
     *
     * @throws Zend_Mail_Exception if called subsequent times
     *
     * @return Zend_Mail Provides fluent interface
     */
    public function setFrom($email, $name = null)
    {
        // mitigate "pwnscriptum" attack
        // see https://framework.zend.com/security/advisory/ZF2016-04 for ZF2+ fix
        if (preg_match('/\\\"/', $email)) {
            throw new \RuntimeException('Potential code injection in From header');
        }

        $this->_fromName = $name;

        return parent::setFrom($email, $name);
    }

    /**
     * Clears the sender from the mail
     *
     * @return Zend_Mail Provides fluent interface
     */
    public function clearFrom()
    {
        $this->_fromName = null;

        return parent::clearFrom();
    }

    /**
     * Returns from name
     *
     * @return unknown
     */
    public function getFromName()
    {
        return $this->_fromName;
    }

    /**
     * Returns a list of recipient email addresses
     *
     * @return array
     */
    public function getTo()
    {
        return $this->_to;
    }

    /**
     * Sets the text body for the message.
     *
     * @param string $txt
     * @param string $charset
     * @param string $encoding
     *
     * @return Zend_Mail Provides fluent interface
     */
    public function setBodyText($txt, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->_plainBodyText = $txt;

        return parent::setBodyText($txt, $charset, $encoding);
    }

    /**
     * Sets the HTML body for the message
     *
     * @param string $html
     * @param string $charset
     * @param string $encoding
     *
     * @return Zend_Mail Provides fluent interface
     */
    public function setBodyHtml($html, $charset = null, $encoding = Zend_Mime::ENCODING_QUOTEDPRINTABLE)
    {
        $this->_plainBody = $html;

        return parent::setBodyHtml($html, $charset, $encoding);
    }

    /**
     * Returns plain body html
     *
     * @return string|null
     */
    public function getPlainBody()
    {
        return $this->_plainBody;
    }

    /**
     * Returns plain body text
     *
     * @return string|null
     */
    public function getPlainBodyText()
    {
        return $this->_plainBodyText;
    }

    /**
     * Returns the plain subject
     *
     * @return string|null
     */
    public function getPlainSubject()
    {
        return $this->_plainSubject;
    }

    /**
     * Overwrites the setSubject function of the Zend_Mail object, to set the plain subject text in the internal
     * helper property.
     *
     * @param string $subject
     *
     * @return Zend_Mail
     */
    public function setSubject($subject)
    {
        $this->_plainSubject = $subject;

        return parent::setSubject($subject);
    }

    /**
     * Sends this email using the given transport or a previously
     * set DefaultTransport or the internal mail function if no
     * default transport had been set.
     *
     * @param Zend_Mail_Transport_Abstract $transport
     *
     * @return Zend_Mail Provides fluent interface
     * @events  Enlight_Components_Mail_Send
     */
    public function send($transport = null)
    {
        Shopware()->Events()->notify(
            'Enlight_Components_Mail_Send',
            [
                'mail' => $this,
                'transport' => $transport,
            ]
        );

        return parent::send($transport);
    }
}
