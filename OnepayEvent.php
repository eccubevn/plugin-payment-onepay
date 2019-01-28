<?php
namespace Plugin\Onepay;

use Eccube\Common\EccubeConfig;
use Eccube\Entity\Order;
use Eccube\Event\TemplateEvent;
use Eccube\Repository\PaymentRepository;
use Eccube\Twig\Extension\EccubeExtension;
use Plugin\Onepay\Entity\PaidLogs;
use Plugin\Onepay\Repository\PaidLogsRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnepayEvent implements EventSubscriberInterface
{
    /** @var PaidLogsRepository */
    protected $paidLogsRepository;

    /** @var PaymentRepository */
    protected $paymentRepository;

    /** @var EccubeConfig */
    protected $eccubeConfig;

    /**
     * @var \Twig_Environment
     */
    protected $twigEnvironment;

    /**
     * OnepayEvent constructor.
     * @param PaidLogsRepository $paidLogsRepository
     * @param PaymentRepository $paymentRepository
     * @param EccubeConfig $eccubeConfig
     * @param \Twig_Environment $twigEnvironment
     */
    public function __construct(
        PaidLogsRepository $paidLogsRepository,
        PaymentRepository $paymentRepository,
        EccubeConfig $eccubeConfig,
        \Twig_Environment $twigEnvironment
    ) {
        $this->paidLogsRepository = $paidLogsRepository;
        $this->paymentRepository = $paymentRepository;
        $this->eccubeConfig = $eccubeConfig;
        $this->twigEnvironment = $twigEnvironment;
    }


    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            '@admin/Order/edit.twig' => 'adminOrderEditIndexInitialize'
        ];
    }

    /**
     * @param TemplateEvent $event
     */
    public function adminOrderEditIndexInitialize(TemplateEvent $event)
    {
        $parameter = $event->getParameters();
        /** @var Order $Order */
        $Order = $parameter['Order'];

        /** @var PaidLogs $PaidLogs */
        $PaidLogs = $this->paidLogsRepository->findOneBy(["Order" => $Order]);
        if ($PaidLogs) {
            $parameter['payment'] = $this->paymentRepository->find($Order->getPayment()->getId());
            $paidLog = $PaidLogs->getPaidInformation(true);

            $locale = $this->eccubeConfig->get('locale');
            $currency = $this->eccubeConfig->get('currency');
            $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);

            $paidLog['vpc_Amount'] = $formatter->formatCurrency($paidLog['vpc_Amount'] / 100, $currency);
            $parameter['paidLog'] = $paidLog;
            $event->setParameters($parameter);

            $twig = '@Onepay/admin/paid_log.twig';
            $event->addSnippet($twig);
        }
    }
}
