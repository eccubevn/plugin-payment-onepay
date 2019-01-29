<?php

namespace Plugin\Onepay\Service\Payment\Method;

use Eccube\Common\EccubeConfig;
use Eccube\Repository\OrderRepository;
use Plugin\Onepay\Entity\Config;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Eccube\Service\Payment\PaymentMethodInterface;
use Eccube\Service\Payment\PaymentResult;
use Eccube\Service\Payment\PaymentDispatcher;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\PurchaseFlow\PurchaseFlow;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Service\PurchaseFlow\PurchaseContext;
use Plugin\Onepay\Repository\ConfigRepository;

abstract class RedirectLinkGateway implements PaymentMethodInterface
{
    const DOMESTIC_CHECK_ORDER_ID = 99999999998;
    const CREDIT_CHECK_ORDER_ID = 99999999999;

    protected $isCheck = false;
    protected $configCheck;

    /**
     * @var \Eccube\Entity\Order
     */
    protected $Order;

    /**
     * @var \Symfony\Component\Form\FormInterface
     */
    protected $form;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /** @var OrderRepository */
    protected $orderRepository;

    /**
     * @var PurchaseFlow
     */
    protected $purchaseFlow;

    /**
     * @var Config
     */
    protected $OnepayConfig;

    /** @var EccubeConfig */
    protected $eccubeConfig;

    /** @var ContainerInterface */
    protected $container;

    /**
     * RedirectLinkGateway constructor.
     *
     * @param OrderStatusRepository $orderStatusRepository
     * @param PurchaseFlow $shoppingPurchaseFlow
     * @param ConfigRepository $configRepository
     * @param EccubeConfig $eccubeConfig
     * @param OrderRepository $orderRepository
     * @param ContainerInterface $container
     */
    public function __construct(
        OrderStatusRepository $orderStatusRepository,
        PurchaseFlow $shoppingPurchaseFlow,
        ConfigRepository $configRepository,
        EccubeConfig $eccubeConfig,
        OrderRepository $orderRepository,
        ContainerInterface $container
    ) {
        $this->orderStatusRepository = $orderStatusRepository;
        $this->purchaseFlow = $shoppingPurchaseFlow;
        $this->OnepayConfig = $configRepository->get();
        $this->eccubeConfig = $eccubeConfig;
        $this->orderRepository = $orderRepository;
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentResult
     */
    public function verify()
    {
        $result = new PaymentResult();
        $result->setSuccess(true);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentResult
     */
    public function checkout()
    {
        $result = new PaymentResult();
        $result->setSuccess(true);

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * @return PaymentDispatcher
     * @throws \Eccube\Service\PurchaseFlow\PurchaseException
     */
    public function apply()
    {
        $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PENDING);
        $this->Order->setOrderStatus($OrderStatus);

        $this->purchaseFlow->prepare($this->Order, new PurchaseContext());

        $url = $this->getCallUrl();
        $response = new RedirectResponse($url);
        $dispatcher = new PaymentDispatcher();
        $dispatcher->setResponse($response);
        return $dispatcher;

    }

    /**
     * {@inheritdoc}
     *
     * @param \Symfony\Component\Form\FormInterface $form
     * @return $this
     */
    public function setFormType(\Symfony\Component\Form\FormInterface $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @param \Eccube\Entity\Order $Order
     * @return $this
     */
    public function setOrder(\Eccube\Entity\Order $Order)
    {
        $this->Order = $Order;
        return $this;
    }

    /**
     * Check connect to Onepay input card page
     *
     * @param Config $Config
     * @return mixed
     */
    abstract public function checkConn(Config $Config);

    /**
     * Generate url endpoint which will be redirect to process payment
     *
     * @return string
     */
    abstract public function getCallUrl();

    /**
     * Handle response via Request object
     *
     * @param Request $request
     * @return mixed
     */
    abstract public function handleRequest(Request $request);
}

