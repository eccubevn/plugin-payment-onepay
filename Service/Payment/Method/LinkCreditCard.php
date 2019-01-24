<?php
namespace Plugin\Onepay\Service\Payment\Method;

class IntlGateway extends RedirectLinkGateway
{
    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getCallUrl()
    {
        $secret = "6D0870CDE5F24F34F3915FB0045120DB";

        /** @var \Plugin\OnepagePayment\Entity\Config $Config */
        $Config = $this->configRepository->get();

        $vpcURL = 'https://mtf.onepay.vn/vpcpay/vpcpay.op' . "?";
        $md5HashData = "";

        $params = $this->getParameters();
        ksort ($params);
        $appendAmp = 0;
        foreach($params as $key => $value) {

            // create the md5 input and URL leaving out any fields that have no value
            if (strlen($value) > 0) {

                // this ensures the first paramter of the URL is preceded by the '?' char
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                //$md5HashData .= $value; sử dụng cả tên và giá trị tham số để mã hóa
                if ((strlen($value) > 0) && ((substr($key, 0,4)=="vpc_") || (substr($key,0,5) =="user_"))) {
                    $md5HashData .= $key . "=" . $value . "&";
                }
            }
        }

        $md5HashData = rtrim($md5HashData, "&");

        if (strlen($secret) > 0) {
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $md5HashData, pack('H*',$secret)));
        }

        return $vpcURL;
    }

    protected function getParameters()
    {
        /** @var \Plugin\OnepagePayment\Entity\Config $Config */
        $Config = $this->configRepository->get();
        return [
            'vpc_Merchant' => 'TESTONEPAY',
            'vpc_AccessCode' => '6BEB2546',
            'vpc_MerchTxnRef' => $this->getTransactionId(), // transaction id
            'vpc_OrderInfo' => $this->getOrderInfo(),
            'vpc_Amount' => $this->Order->getTotal() * 100,
            'vpc_ReturnURL' => 'http://localhost/domestic_php_v2/source_code/dr.php',
            'vpc_Version' => 2,
            'vpc_Command' => 'pay',
            'vpc_Locale' => 'vn',
            'vpc_TicketNo' => $_SERVER['REMOTE_ADDR'],
            'AgainLink' => urlencode($_SERVER['HTTP_REFERER']),
            'Title' => 'VPC 3-Party'
        ];
    }

    protected function getTransactionId()
    {
        return date('YmdHis') . rand();
        return str_pad($this->Order->getId(), 10, '0', STR_PAD_LEFT);
    }

    protected function getOrderInfo()
    {
        return str_pad($this->Order->getId(), 10, '0', STR_PAD_LEFT);
    }
}
