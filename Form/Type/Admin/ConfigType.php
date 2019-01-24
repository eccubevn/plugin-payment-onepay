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
        $builder
            ->add('credit_call_url', TextType::class, [
                'label' => trans('onepay.config.call_url.label')
            ])
            ->add('credit_callback_url', TextType::class, [
                'label' => trans('onepay.config.callback_url.label')
            ])
            ->add('credit_merchant_id', TextType::class, [
                'label' => trans('onepay.config.merchant_id.label')
            ])
            ->add('credit_merchant_access_code', TextType::class, [
                'label' => trans('onepay.config.merchant_access_code.label')
            ])
            ->add('credit_secret', TextType::class, [
                'label' => trans('onepay.config.secret.label')
            ])
            ->add('domestic_call_url', TextType::class, [
                'label' => trans('onepay.config.call_url.label')
            ])
            ->add('domestic_callback_url', TextType::class, [
                'label' => trans('onepay.config.callback_url.label')
            ])
            ->add('domestic_merchant_id', TextType::class, [
                'label' => trans('onepay.config.merchant_id.label')
            ])
            ->add('domestic_merchant_access_code', TextType::class, [
                'label' => trans('onepay.config.merchant_access_code.label')
            ])
            ->add('domestic_secret', TextType::class, [
                'label' => trans('onepay.config.secret.label')
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
