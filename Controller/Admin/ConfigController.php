<?php
namespace Plugin\Onepay\Controller\Admin;

use Plugin\Onepay\Service\Payment\Method\LinkCreditCard;
use Plugin\Onepay\Service\Payment\Method\LinkDomesticCard;
use Plugin\Onepay\Service\Payment\Method\RedirectLinkGateway;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Eccube\Controller\AbstractController;
use Plugin\Onepay\Repository\ConfigRepository;
use Plugin\Onepay\Form\Type\Admin\ConfigType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ConfigController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var LinkDomesticCard
     */
    protected $domesticCard;

    /**
     * @var LinkCreditCard
     */
    protected $creditCard;

    /**
     * ConfigController constructor.
     *
     * @param ConfigRepository $configRepository
     * @param LinkDomesticCard $domesticCard
     * @param LinkCreditCard $creditCard
     */
    public function __construct(
        ConfigRepository $configRepository,
        LinkDomesticCard $domesticCard,
        LinkCreditCard $creditCard
    )
    {
        $this->configRepository = $configRepository;
        $this->domesticCard = $domesticCard;
        $this->creditCard = $creditCard;
    }

    /**
     * @Route("/%eccube_admin_route%/onepay/config", name="onepay_admin_config")
     * @Template("@Onepay/admin/config.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function index(Request $request)
    {
        $Config = $this->configRepository->get();
        $form = $this->createForm(ConfigType::class, $Config);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $Config = $form->getData();
            $typeCheckCard = $request->get('typeCheckCard');
            if ($request->isXmlHttpRequest() && $typeCheckCard) {
                if ($typeCheckCard == 'credit'){
                    $urlCheck = $this->creditCard->checkConn($Config);
                }else{
                    $urlCheck = $this->domesticCard->checkConn($Config);
                }

                return $this->json(['error' => false, 'url' => $urlCheck]);
            }

            if ($request->get('saveConfig')) {
                $this->entityManager->persist($Config);
                $this->entityManager->flush();

                $this->addSuccess('admin.common.save_complete', 'admin');
            }
        }

        return [
            'form' => $form->createView(),
            'urlCheckCredit' => $this->creditCard->checkConn($Config),
            'urlCheckDomestic' => $this->domesticCard->checkConn($Config),
        ];
    }

    /**
     * @Route("/%eccube_admin_route%/onepay/config/check", name="onepay_admin_config_check")
     * @Template("@Onepay/admin/config.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function checkConfig(Request $request)
    {
        $orderId = intval($request->get('vpc_OrderInfo'));

        if ($orderId != RedirectLinkGateway::CREDIT_CHECK_ORDER_ID || $orderId != RedirectLinkGateway::DOMESTIC_CHECK_ORDER_ID) {
            throw new NotFoundHttpException();
        }

        if ($orderId == RedirectLinkGateway::CREDIT_CHECK_ORDER_ID) {
            $PaymentMethod = $this->container->get(LinkCreditCard::class);
        } else {
            $PaymentMethod = $this->container->get(LinkDomesticCard::class);
        }

        $result = $PaymentMethod->handleRequest($request);
        if ($result['status'] === 'success') {
            $this->session->set('eccube.front.shopping.order.id', $orderId);
            return $this->redirectToRoute('onepay_admin_config');
        } else {
            $this->addError($result['message'], 'admin');
            return $this->redirectToRoute('onepay_admin_config');
        }
    }
}
