<?php

declare(strict_types=1);

namespace App\Cataloging\Form\Config;

use App\Cataloging\Value\Form\Config\CatalogingOidcConfigData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CatalogingOidcConfigFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('audience', TextType::class, [
                'label' => 'CATALOG_OIDC_AUDIENCE',
                'required' => true,
            ])
            ->add('issuer', TextType::class, [
                'label' => 'CATALOG_OIDC_ISSUER',
                'required' => true,
            ])
            ->add('jwkSetJson', TextareaType::class, [
                'label' => 'CATALOG_OIDC_JWK_SET_JSON',
                'required' => true,
                'attr' => ['rows' => 12],
            ])
            ->add('save', SubmitType::class, ['label' => 'Save pending'])
            ->add('apply', SubmitType::class, ['label' => 'Apply now', 'attr' => ['class' => 'btn btn-primary']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CatalogingOidcConfigData::class,
        ]);
    }
}
