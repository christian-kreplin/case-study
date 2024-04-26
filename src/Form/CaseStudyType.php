<?php

namespace App\Form;

use App\Entity\CaseStudy;
use App\Entity\Customer;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;

class CaseStudyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titel'])
            ->add('description', CKEditorType::class, [
                'label' => 'Beschreibung',
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Gib bitte eine Beschreibung ein.',
                        ]
                    ),
                ],
            ])
            ->add('customer', EntityType::class, options: [
                'label' => 'Kunde',
                'class' => Customer::class,
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'choice_label' => 'name',
            ])
            ->add('imageFile', VichImageType::class, ['required' => false, 'label' => 'Logo']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => CaseStudy::class,
            ]
        );
    }
}
