<?php
namespace Plugin\Onepay\Form\Type\Admin;

use Eccube\Common\EccubeConfig;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Plugin\Onepay\Entity\Config;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;

class ConfigType extends AbstractType
{
    /** @var EccubeConfig */
    private $eccubeConfig;
    /**
     * ConfigType constructor.
     *
     * @param EccubeConfig $eccubeConfig
     */
    public function __construct(EccubeConfig $eccubeConfig)
    {
        $this->eccubeConfig = $eccubeConfig;
    }

    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('credit_call_url', TextType::class, [
                'label' => trans('onepay.config.call_url.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                    new Url(),
                ],
            ])
            ->add('credit_merchant_id', TextType::class, [
                'label' => trans('onepay.config.merchant_id.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                ],
            ])
            ->add('credit_merchant_access_code', TextType::class, [
                'label' => trans('onepay.config.merchant_access_code.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                ],
            ])
            ->add('credit_secret', TextType::class, [
                'label' => trans('onepay.config.secret.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                ],
            ])

            // Domestic
            ->add('domestic_call_url', TextType::class, [
                'label' => trans('onepay.config.call_url.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                    new Url(),
                ],
            ])
            ->add('domestic_merchant_id', TextType::class, [
                'label' => trans('onepay.config.merchant_id.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                ],
            ])
            ->add('domestic_merchant_access_code', TextType::class, [
                'label' => trans('onepay.config.merchant_access_code.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                ],
            ])
            ->add('domestic_secret', TextType::class, [
                'label' => trans('onepay.config.secret.label'),
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => $this->eccubeConfig->get('eccube_stext_len')]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Config::class,
        ]);
    }
}
