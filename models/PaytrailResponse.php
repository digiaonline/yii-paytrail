<?php
/**
 * PaytrailResponse class file.
 * @author Christoffer Lindqvist <christoffer.lindqvist@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

class PaytrailResponse
{
    /**
     * @var string
     */
    protected $orderNumber;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $url;

    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param array $properties
     */
    public function configure(array $properties)
    {
        foreach ($properties as $name => $value) {
            $this->$name = $value;
        }
    }
} 