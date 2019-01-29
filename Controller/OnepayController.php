<?php
namespace Plugin\Onepay\Controller;

use Plugin\Onepay\Entity\PaidLogs;
use Plugin\Onepay\Repository\PaidLogsRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Eccube\Controller\AbstractController;
use Eccube\Service\CartService;
use Eccube\Repository\OrderRepository;
use Eccube\Repository\Master\OrderStatusRepository;
use Eccube\Service\OrderStateMachine;
use Eccube\Entity\Master\OrderStatus;
use Eccube\Entity\Order;

class OnepayController extends AbstractController
{
    /**
     * @var CartService
     */
    protected $cartService;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var OrderStatusRepository
     */
    protected $orderStatusRepository;

    /**
     * @var OrderStateMachine
     */
    protected $orderStateMachine;

    /** @var PaidLogsRepository */
    protected $paidLogsRepository;

    /**
     * OnepayController constructor.
     *
     * @param CartService $cartService
     * @param OrderRepository $orderRepository
     * @param OrderStatusRepository $orderStatusRepository
     * @param OrderStateMachine $orderStateMachine
     * @param PaidLogsRepository $paidLogsRepository
     */
    public function __construct(
        CartService $cartService,
        OrderRepository $orderRepository,
        OrderStatusRepository $orderStatusRepository,
        OrderStateMachine $orderStateMachine,
        PaidLogsRepository $paidLogsRepository
    ) {
        $this->cartService = $cartService;
        $this->orderRepository = $orderRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->orderStateMachine = $orderStateMachine;
        $this->paidLogsRepository = $paidLogsRepository;
    }


    /**
     * @Route("/onepay/back", name="onepay_back")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function back(Request $request)
    {
        $orderId =  intval($request->get('vpc_OrderInfo'));
        $Order = $this->orderRepository->find($orderId);
        if (!$Order instanceof Order) {
            throw new NotFoundHttpException();
        }

        $this->paidLogsRepository->saveLogs($Order, $request);

        if ($this->getUser() != $Order->getCustomer()) {
            throw new NotFoundHttpException();
        }

        $PaymentMethod = $this->container->get($Order->getPayment()->getMethodClass());

        $result = $PaymentMethod->handleRequest($request);
        if ($result['status'] === 'success') {
            $Order->setOrderStatus($this->orderStatusRepository->find(OrderStatus::NEW));
            $Order->setOrderDate(new \DateTime());

            $OrderStatus = $this->orderStatusRepository->find(OrderStatus::PAID);
            if ($this->orderStateMachine->can($Order, $OrderStatus)) {
                $this->orderStateMachine->apply($Order, $OrderStatus);
                $Order->setPaymentDate(new \DateTime());
            }

            $this->cartService->clear();

            $this->session->set('eccube.front.shopping.order.id', $Order->getId());
            $this->entityManager->flush();
            return $this->redirectToRoute('shopping_complete');
        } else {
            $this->addError($result['message']);
            return $this->redirectToRoute('shopping_error');
        }
    }
}