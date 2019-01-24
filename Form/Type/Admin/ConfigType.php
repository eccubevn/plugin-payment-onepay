<?php
namespace Plugin\Onepay\Form\Type\Admin;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Plugin\Onepay\Entity\Config;

class ConfigType extends AbstractType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('call_url', TextType::class, [
                'label' => trans('onepay.config.call_url.label'),
                'required' => true
            ])
            ->add('callback_url', TextType::class, [
                'label' => trans('onepay.config.callback_url.label'),
                'required' => true
            ])
            ->add('merchant_id', TextType::class, [
                'label' => trans('onepay.config.merchant_id.label'),
                'required' => true
            ])
            ->add('merchant_access_code', TextType::class, [
                'label' => trans('onepay.config.merchant_access_code.label'),
                'required' => true
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
