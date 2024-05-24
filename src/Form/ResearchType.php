<?php

namespace App\Form;

use App\Dto\Research;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ResearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $defaultMinCost = 0;
        $defaultMaxCost = 99999;
        $builder
            ->setAction('/')
            ->add('page', HiddenType::class, [
                'empty_data' => 1,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Category'
            ])
            ->add('query', SearchType::class, [
                'label' => 'Search',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('minCost', NumberType::class, [
                'label' => 'Min cost',
                'required' => false,
                'html5' => true,
                'empty_data' => $defaultMinCost,
                'attr' => [
                    'value' => $defaultMinCost,
                    'placeholder' => $defaultMinCost,
                    'min' => $defaultMinCost,
                ],
            ])
            ->add('maxCost', NumberType::class, [
                'label' => 'Max cost',
                'required' => false,
                'html5' => true,
                'empty_data' => $defaultMaxCost,
                'attr' => [
                    'value' => $defaultMaxCost,
                    'placeholder' => $defaultMaxCost,
                    'min' => $defaultMinCost,
                ],
            ])
            ->add('postcode', TextType::class, [
                'label' => 'Postal Code',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Search'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Research::class,
        ]);
    }
}
