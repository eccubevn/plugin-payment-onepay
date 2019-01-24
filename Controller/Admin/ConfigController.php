<?php
namespace Plugin\Onepay\Controller\Admin;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Eccube\Controller\AbstractController;
use Plugin\Onepay\Repository\ConfigRepository;
use Plugin\Onepay\Form\Type\Admin\ConfigType;

class ConfigController extends AbstractController
{
    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * ConfigController constructor.
     * @param ConfigRepository $configRepository
     */
    public function __construct(
        ConfigRepository $configRepository
    ) {
        $this->configRepository = $configRepository;
    }

    /**
     * @Route("/%eccube_admin_route%/onepay/config", name="onepay_admin_config")
     * @Template("@Onepay/admin/config.twig")
     * @param Request $request
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function index(Request $request)
    {
        $Config = $this->configRepository->get();
        $form = $this->createForm(ConfigType::class, $Config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($Config);
            $this->entityManager->flush();

            $this->addSuccess('admin.common.save_complete', 'admin');
            return $this->redirectToRoute('onepay_admin_config');
        }

        return [
            'form' => $form->createView()
        ];
    }
}
