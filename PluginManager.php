<?php

namespace Plugin\OnepagePayment;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Repository\PaymentRepository;
use Eccube\Entity\Payment;
use Plugin\OnepagePayment\Service\Payment\Method\IntlGateway;
use Plugin\OnepagePayment\Entity\Config;
use Plugin\OnepagePayment\Repository\ConfigRepository;

class PluginManager extends AbstractPluginManager
{
    /**
     * {@inheritdoc}
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function install(array $meta, ContainerInterface $container)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();

        $Config = new Config();
        $entityManager->persist($Config);
        $entityManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function enable(array $meta, ContainerInterface $container)
    {
        /** @var TranslatorInterface $translator */
        $translator = $container->get('translator');

        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $container->get(PaymentRepository::class);

        $Payment = $paymentRepository->findOneBy(['method_class' => IntlGateway::class]);
        if ($Payment instanceof Payment) {
            $Payment->setVisible(true);
        } else {
            $Payment = $paymentRepository->findOneBy([], ['sort_no' => 'DESC']);
            $sortNo = $Payment ? $Payment->getSortNo() + 1 : 1;

            $Payment = new Payment();
            $Payment->setCharge(0);
            $Payment->setSortNo($sortNo);
            $Payment->setVisible(true);
            $Payment->setMethod($translator->trans('onepage_payment.intl_gateway.title'));
            $Payment->setMethodClass(IntlGateway::class);
        }

        $entityManager->persist($Payment);
        $entityManager->flush();
    }

    /**
     * {@inheritdoc}
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function disable(array $meta, ContainerInterface $container)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $container->get(PaymentRepository::class);

        $Payment = $paymentRepository->findOneBy(['method_class' => IntlGateway::class]);
        if ($Payment instanceof Payment) {
            $Payment->setVisible(false);
            $entityManager->persist($Payment);
            $entityManager->flush();
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param array $meta
     * @param ContainerInterface $container
     */
    public function uninstall(array $meta, ContainerInterface $container)
    {
        /** @var \Doctrine\ORM\EntityManagerInterface $entityManager */
        $entityManager = $container->get('doctrine')->getManager();

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $container->get(PaymentRepository::class);

        $Payment = $paymentRepository->findOneBy(['method_class' => IntlGateway::class]);
        if ($Payment instanceof Payment) {
            $entityManager->remove($Payment);
            $entityManager->flush($Payment);
        }

        /** @var ConfigRepository $configRepository */
        $configRepository = $container->get(ConfigRepository::class);
        $Config = $configRepository->get();
        $entityManager->remove($Config);
        $entityManager->flush();
    }
}
