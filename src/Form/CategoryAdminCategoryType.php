<?php

# Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
declare(strict_types=1);

namespace App\Cataloging\Form;

use App\Cataloging\Dto\CategoryAdminCategoryData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Provides the category admin category type implementation.
 */
final class CategoryAdminCategoryType extends AbstractType
{
    /** @param array<string,mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('nameEntity', TextType::class, [
                'label' => 'Name',
                'attr' => ['class' => 'form-control', 'maxlength' => 160],
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'attr' => ['class' => 'form-control', 'maxlength' => 180],
            ]);
    }

    /**
     * Handles the configure options workflow.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => CategoryAdminCategoryData::class,
            'csrf_protection' => true,
        ]);
    }
}
