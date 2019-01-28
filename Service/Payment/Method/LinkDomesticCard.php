<?php
namespace Plugin\Onepay\Service\Payment\Method;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkDomesticCard extends RedirectLinkGateway
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getCallUrl()
    {
        /** @var \Plugin\Onepay\Entity\Config $Config */
        $Config = $this->configRepository->get();

        $vpcURL = $Config->getDomesticCallUrl() . "?";
        $params = $this->getParameters();
        $appendAmp = 0;
        foreach($params as $key => $value) {
            if (strlen($value) > 0) {
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
            }
        }

        if (strlen($Config->getDomesticSecret()) > 0) {
            $vpcURL .= "&vpc_SecureHash=" . $this->getSecureHash($params, $Config->getDomesticSecret());
        }

        return $vpcURL;
    }

    protected function getParameters()
    {
        /** @var \Plugin\Onepay\Entity\Config $Config */
        $Config = $this->configRepository->get();
        return [
            'vpc_Merchant' => $Config->getDomesticMerchantId(),
            'vpc_AccessCode' => $Config->getDomesticMerchantAccessCode(),
            'vpc_MerchTxnRef' => $this->getTransactionId(), // transaction id
            'vpc_OrderInfo' => $this->getOrderInfo(),
            'vpc_Amount' => $this->Order->getTotal() * 100,
            'vpc_ReturnURL' => $this->container->get('router')->generate('onepay_back', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'vpc_Version' => '2',
            'vpc_Command' => 'pay',
            'vpc_Locale' => 'vn',
            'vpc_TicketNo' => $_SERVER['REMOTE_ADDR'],
            'vpc_Currency' => 'VND',
            'AgainLink' => urlencode($_SERVER['HTTP_REFERER']),
            'Title' => 'VPC 3-Party',
            'AVS_Street01' => $this->Order->getAddr02(),
            'AVS_City' => $this->Order->getPref() ? $this->Order->getPref()->getName() : '',
            'AVS_StateProv' => $this->Order->getAddr01(),
            'AVS_PostCode' => $this->Order->getPostalCode(),
            'AVS_Country' => 'VN',
            'vpc_SHIP_Street01' => '39A Ngo Quyen',
            'vpc_SHIP_Provice' => 'Hoan Kiem',
            'vpc_SHIP_City' =>  'Ha Noi',
            'vpc_SHIP_Country' => 'Viet Nam',
            'vpc_Customer_Phone' => '840904280949',
            'vpc_Customer_Email' => 'support@onepay.vn',
            'vpc_Customer_Id' => 'thanhvt',
        ];
    }

    /**
     * Unique transaction id
     *
     * @return string
     */
    protected function getTransactionId()
    {
        return $this->Order->getPreOrderId();
    }

    /**
     * Order info
     *
     * @return string
     */
    protected function getOrderInfo()
    {
        return str_pad($this->Order->getId(), 11, '0', STR_PAD_LEFT);
    }

    /**
     * Get description of response code
     *
     * @param $responseCode
     * @return string
     */
    public function getResponseCodeDescription($responseCode)
    {
        switch ($responseCode) {
            case "0" :
                $result = "onepay.response.domestic.msg.Approved";
                break;
            case "1" :
                $result = "onepay.response.domestic.msg.Bank_Declined";
                break;
            case "3" :
                $result = "onepay.response.domestic.msg.Merchant_not_exist";
                break;
            case "4" :
                $result = "onepay.response.domestic.msg.Invalid_access_code";
                break;
            case "5" :
                $result = "onepay.response.domestic.msg.Invalid_amount";
                break;
            case "6" :
                $result = "onepay.response.domestic.msg.Invalid_currency";
                break;
            case "7" :
                $result = "onepay.response.domestic.msg.Unspecified_Failure";
                break;
            case "8" :
                $result = "onepay.response.domestic.msg.Invalid_card_Number";
                break;
            case "9" :
                $result = "onepay.response.domestic.msg.Invalid_card_name";
                break;
            case "10" :
                $result = "onepay.response.domestic.msg.Expired_Card";
                break;
            case "11" :
                $result = "onepay.response.domestic.msg.Card_Not_Registed_Service_internet_banking";
                break;
            case "12" :
                $result = "onepay.response.domestic.msg.Invalid_card_date";
                break;
            case "13" :
                $result = "onepay.response.domestic.msg.Exist_Amount";
                break;
            case "21" :
                $result = "onepay.response.domestic.msg.Insufficient_fund";
                break;
            case "99" :
                $result = "onepay.response.domestic.msg.User_cancel";
                break;
            default :
                $result = "onepay.response.domestic.msg.Failured";
        }
        return $result;
    }

    /**
     * @param $params
     * @param string $secret
     * @return string
     */
    public function getSecureHash($params, $secret = '')
    {
        $md5HashData = "";
        ksort ($params);
        foreach($params as $key => $value) {
            if ((strlen($value) > 0) && $key != "vpc_SecureHash" && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                $md5HashData .= $key . "=" . $value . "&";
            }
        }
        $md5HashData = rtrim($md5HashData, "&");

        if ($secret) {
            $md5HashData = strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*', $secret)));
        }

        return $md5HashData;
    }

    /**
     * {@inheritdoc}
     *
     * @param Request $request
     * @return mixed
     */
    public function handleRequest(Request $request)
    {
        /** @var \Plugin\Onepay\Entity\Config $Config */
        $Config = $this->configRepository->get();
        $params = $request->query->all();
        log_info('Onepay return', $params);
        if (isset($params['vpc_SecureHash']) && strlen($Config->getDomesticSecret()) > 0 && strlen($params["vpc_TxnResponseCode"]) && $params["vpc_TxnResponseCode"] != "7") {
            if (strtoupper($params['vpc_SecureHash']) == $this->getSecureHash($params, $Config->getDomesticSecret())) {
                $hashValidated = "CORRECT";
            } else {
                $hashValidated = "INVALID HASH";
            }

        } else {
            $hashValidated = "INVALID HASH";
        }

        $result = [
            'message' => $this->getResponseCodeDescription($params['vpc_TxnResponseCode'])
        ];

        if ($hashValidated == "CORRECT" && $params['vpc_TxnResponseCode'] === "0") {
            $result['status'] = 'success';
        } elseif ($hashValidated == "INVALID HASH" && $params['vpc_TxnResponseCode'] === "0") {
            $result['status'] = 'pending';
        } else {
            $result['status'] = 'error';
        }

        return $result;
    }
}
