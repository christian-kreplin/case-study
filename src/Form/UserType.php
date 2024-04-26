<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email', EmailType::class, ['label' => 'E-Mail']);

        if ($options['isNew']) {
            $builder->add('plainPassword', PasswordType::class, [
                'label' => 'Passwort',
                'mapped' => false,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Gib bitte ein Passwort ein.',
                        ]
                    ),
                    new Length(
                        [
                            'min' => 12,
                            'minMessage' => 'Das Passwort muss mindestens {{ limit }} Zeichen lang sein.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]
                    ),
                    new PasswordStrength(),
                    new NotCompromisedPassword(),
                ],
            ]);
        }

        $builder->add('verified', CheckboxType::class, [
            'label' => 'verifiziert',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'isNew' => true,
            ]
        );
    }
}
