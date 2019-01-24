<?php
namespace Plugin\Onepay\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="plg_onepay_config")
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
     * @ORM\Column(name="credit_secret", type="string", length=1024, nullable=true)
     */
    protected $creditSecret;

    /**
     * @var string
     *
     * @ORM\Column(name="credit_call_url", type="string", length=1024, nullable=true)
     */
    protected $creditCallUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="credit_callback_url", type="string", length=1024, nullable=true)
     */
    protected $creditCallbackUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="credit_merchant_id", type="string", length=1024, nullable=true)
     */
    protected $creditMerchantId;

    /**
     * @var string
     *
     * @ORM\Column(name="credit_merchant_access_code", type="string", length=1024, nullable=true)
     */
    protected $creditMerchantAccessCode;

    /**
     * @var string
     *
     * @ORM\Column(name="domestic_secret", type="string", length=1024, nullable=true)
     */
    protected $domesticSecret;

    /**
     * @var string
     *
     * @ORM\Column(name="domestic_call_url", type="string", length=1024, nullable=true)
     */
    protected $domesticCallUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="domestic_callback_url", type="string", length=1024, nullable=true)
     */
    protected $domesticCallbackUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="domestic_merchant_id", type="string", length=1024, nullable=true)
     */
    protected $domesticMerchantId;

    /**
     * @var string
     *
     * @ORM\Column(name="domestic_merchant_access_code", type="string", length=1024, nullable=true)
     */
    protected $domesticMerchantAccessCode;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get $creditSecret
     *
     * @return string
     */
    public function getCreditSecret()
    {
        return $this->creditSecret;
    }

    /**
     * Set $creditSecret
     *
     * @param $creditSecret
     * @return $this
     */
    public function setCreditSecret($creditSecret)
    {
        $this->creditSecret = $creditSecret;
        return $this;
    }

    /**
     * Get $creditCallUrl
     *
     * @return string
     */
    public function getCreditCallUrl()
    {
        return $this->creditCallUrl;
    }

    /**
     * Set $creditCallUrl
     *
     * @param $creditCallUrl
     * @return $this
     */
    public function setCreditCallUrl($creditCallUrl)
    {
        $this->creditCallUrl = $creditCallUrl;
        return $this;
    }

    /**
     * Get $creditCallbackUrl
     *
     * @return string
     */
    public function getCreditCallbackUrl()
    {
        return $this->creditCallbackUrl;
    }

    /**
     * Set $creditCallbackUrl
     *
     * @param $creditCallbackUrl
     * @return $this
     */
    public function setCreditCallbackUrl($creditCallbackUrl)
    {
        $this->creditCallbackUrl = $creditCallbackUrl;
        return $this;
    }

    /**
     * Get $creditMerchantId
     *
     * @return string
     */
    public function getCreditMerchantId()
    {
        return $this->creditMerchantId;
    }

    /**
     * Set $creditMerchantId
     *
     * @param $creditMerchantId
     * @return $this
     */
    public function setCreditMerchantId($creditMerchantId)
    {
        $this->creditMerchantId = $creditMerchantId;
        return $this;
    }

    /**
     * Get $creditMerchantAccessCode
     *
     * @return string
     */
    public function getCreditMerchantAccessCode()
    {
        return $this->creditMerchantAccessCode;
    }

    /**
     * Set $creditMerchantAccessCode
     *
     * @param $creditMerchantAccessCode
     * @return $this
     */
    public function setCreditMerchantAccessCode($creditMerchantAccessCode)
    {
        $this->creditMerchantAccessCode = $creditMerchantAccessCode;
        return $this;
    }

    /**
     * Get $creditSecret
     *
     * @return string
     */
    public function getDomesticSecret()
    {
        return $this->creditSecret;
    }

    /**
     * Set $creditSecret
     *
     * @param $creditSecret
     * @return $this
     */
    public function setDomesticSecret($creditSecret)
    {
        $this->creditSecret = $creditSecret;
        return $this;
    }

    /**
     * Get $creditCallUrl
     *
     * @return string
     */
    public function getDomesticCallUrl()
    {
        return $this->creditCallUrl;
    }

    /**
     * Set $creditCallUrl
     *
     * @param $creditCallUrl
     * @return $this
     */
    public function setDomesticCallUrl($creditCallUrl)
    {
        $this->creditCallUrl = $creditCallUrl;
        return $this;
    }

    /**
     * Get $creditCallbackUrl
     *
     * @return string
     */
    public function getDomesticCallbackUrl()
    {
        return $this->creditCallbackUrl;
    }

    /**
     * Set $creditCallbackUrl
     *
     * @param $creditCallbackUrl
     * @return $this
     */
    public function setDomesticCallbackUrl($creditCallbackUrl)
    {
        $this->creditCallbackUrl = $creditCallbackUrl;
        return $this;
    }

    /**
     * Get $creditMerchantId
     *
     * @return string
     */
    public function getDomesticMerchantId()
    {
        return $this->creditMerchantId;
    }

    /**
     * Set $creditMerchantId
     *
     * @param $creditMerchantId
     * @return $this
     */
    public function setDomesticMerchantId($creditMerchantId)
    {
        $this->creditMerchantId = $creditMerchantId;
        return $this;
    }

    /**
     * Get $creditMerchantAccessCode
     *
     * @return string
     */
    public function getDomesticMerchantAccessCode()
    {
        return $this->creditMerchantAccessCode;
    }

    /**
     * Set $creditMerchantAccessCode
     *
     * @param $creditMerchantAccessCode
     * @return $this
     */
    public function setDomesticMerchantAccessCode($creditMerchantAccessCode)
    {
        $this->creditMerchantAccessCode = $creditMerchantAccessCode;
        return $this;
    }
}
