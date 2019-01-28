<?php
namespace Plugin\Onepay\Service\Payment\Method;

use Plugin\Onepay\Entity\PaidLogs;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkCreditCard extends RedirectLinkGateway
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

        $vpcURL = $Config->getCreditCallUrl() . "?";
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

        if (strlen($Config->getCreditSecret()) > 0) {
            $vpcURL .= "&vpc_SecureHash=" . $this->getSecureHash($params, $Config->getCreditSecret());
        }

        return $vpcURL;
    }

    protected function getParameters()
    {
        /** @var \Plugin\Onepay\Entity\Config $Config */
        $Config = $this->configRepository->get();
        return [
            'vpc_Merchant' => $Config->getCreditMerchantId(),
            'vpc_AccessCode' => $Config->getCreditMerchantAccessCode(),
            'vpc_MerchTxnRef' => $this->getTransactionId(), // transaction id
            'vpc_OrderInfo' => $this->getOrderInfo(),
            'vpc_Amount' => $this->Order->getTotal() * 100,
            'vpc_ReturnURL' => $this->container->get('router')->generate('onepay_back', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'vpc_Version' => '2',
            'vpc_Command' => 'pay',
            'vpc_Locale' => 'en',
            'vpc_TicketNo' => $_SERVER['REMOTE_ADDR'],
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
                $result = "onepay.response.credit.msg.Transaction_Successful";
                break;
            case "?" :
                $result = "onepay.response.credit.msg.Transaction_status_is_unknown";
                break;
            case "1" :
                $result = "onepay.response.credit.msg.Bank_system_reject";
                break;
            case "2" :
                $result = "onepay.response.credit.msg.Bank_Declined_Transaction";
                break;
            case "3" :
                $result = "onepay.response.credit.msg.No_Reply_from_Bank";
                break;
            case "4" :
                $result = "onepay.response.credit.msg.Expired_Card";
                break;
            case "5" :
                $result = "onepay.response.credit.msg.Insufficient_funds";
                break;
            case "6" :
                $result = "onepay.response.credit.msg.Error_Communicating_with_Bank";
                break;
            case "7" :
                $result = "onepay.response.credit.msg.Payment_Server_System_Error";
                break;
            case "8" :
                $result = "onepay.response.credit.msg.Transaction_Type_Not_Supported";
                break;
            case "9" :
                $result = "onepay.response.credit.msg.Bank_declined_transaction_Do_not_contact_Bank";
                break;
            case "A" :
                $result = "onepay.response.credit.msg.Transaction_Aborted";
                break;
            case "C" :
                $result = "onepay.response.credit.msg.Transaction_Cancelled";
                break;
            case "D" :
                $result = "onepay.response.credit.msg.Deferred_transaction_has_been_received_and_is_awaiting_processing";
                break;
            case "F" :
                $result = "onepay.response.credit.msg.3D_Secure_Authentication_failed";
                break;
            case "I" :
                $result = "onepay.response.credit.msg.Card_Security_Code_verification_failed";
                break;
            case "L" :
                $result = "onepay.response.credit.msg.Shopping_Transaction_Locked_Please_try_the_transaction_again_later";
                break;
            case "N" :
                $result = "onepay.response.credit.msg.Cardholder_is_not_enrolled_in_Authentication_scheme";
                break;
            case "P" :
                $result = "onepay.response.credit.msg.Transaction_has_been_received_by_the_Payment_Adaptor_and_is_being_processed";
                break;
            case "R" :
                $result = "onepay.response.credit.msg.Transaction_was_not_processed__Reached_limit_of_retry_attempts_allowed";
                break;
            case "S" :
                $result = "onepay.response.credit.msg.Duplicate_SessionID_OrderInfo";
                break;
            case "T" :
                $result = "onepay.response.credit.msg.Address_Verification_Failed";
                break;
            case "U" :
                $result = "onepay.response.credit.msg.Card_Security_Code_Failed";
                break;
            case "V" :
                $result = "onepay.response.credit.msg.Address_Verification_and_Card_Security_Code_Failed";
                break;
            case "99" :
                $result = "onepay.response.credit.msg.User_Cancel";
                break;
            default  :
                $result = "onepay.response.credit.msg.Unable_to_be_determined";
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

        if (isset($params['vpc_SecureHash']) && strlen($Config->getCreditSecret()) > 0 && strlen($params["vpc_TxnResponseCode"]) && $params["vpc_TxnResponseCode"] != "7") {
            if (strtoupper($params['vpc_SecureHash']) == $this->getSecureHash($params, $Config->getCreditSecret())) {
                $hashValidated = "CORRECT";
            } else {
                $hashValidated = "INVALID HASH";
            }
        } else {
            $hashValidated = "INVALID HASH";
        }

        $result = [
            'message' => trans($this->getResponseCodeDescription($params['vpc_TxnResponseCode']))
        ];
        if ($hashValidated=="CORRECT" && $params['vpc_TxnResponseCode']==="0") {
            $result['status'] = 'success';
        } elseif ($hashValidated=="INVALID HASH" && $params['vpc_TxnResponseCode']==="0") {
            $result['status'] = 'pending';
        } else {
            $result['status'] = 'error';
        }

        return $result;
    }
}
