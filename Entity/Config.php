<?php
namespace Plugin\OnepagePayment\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="plg_onepage_payment_config")
 * @ORM\Entity(repositoryClass="Plugin\OnepagePayment\Repository\ConfigRepository")
 */
class Config
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", options={"unsigned": true})
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="call_url", type="string", length=1024, nullable=true)
     */
    protected $callUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="callback_url", type="string", length=1024, nullable=true)
     */
    protected $callbackUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="merchant_id", type="string", length=1024, nullable=true)
     */
    protected $merchantId;

    /**
     * @var string
     *
     * @ORM\Column(name="merchant_access_code", type="string", length=1024, nullable=true)
     */
    protected $merchantAccessCode;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get $callUrl
     *
     * @return string
     */
    public function getCallUrl()
    {
        return $this->callUrl;
    }

    /**
     * Set $callUrl
     *
     * @param $callUrl
     * @return $this
     */
    public function setCallUrl($callUrl)
    {
        $this->callUrl = $callUrl;
        return $this;
    }

    /**
     * Get $callbackUrl
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->callbackUrl;
    }

    /**
     * Set $callbackUrl
     *
     * @param $callbackUrl
     * @return $this
     */
    public function setCallbackUrl($callbackUrl)
    {
        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    /**
     * Get $merchantId
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * Set $merchantId
     *
     * @param $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
        return $this;
    }

    /**
     * Get $merchantAccessCode
     *
     * @return string
     */
    public function getMerchantAccessCode()
    {
        return $this->merchantAccessCode;
    }

    /**
     * Set $merchantAccessCode
     *
     * @param $merchantAccessCode
     * @return $this
     */
    public function setMerchantAccessCode($merchantAccessCode)
    {
        $this->merchantAccessCode = $merchantAccessCode;
        return $this;
    }
}
