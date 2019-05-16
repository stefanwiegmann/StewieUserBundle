<?php

namespace App\Stefanwiegmann\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Stefanwiegmann\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//       $builder->add('password', RepeatedType::class, [
//     'type' => PasswordType::class,
//     'invalid_message' => 'The password fields must match.',
//     'options' => ['attr' => ['class' => 'password-field']],
//     'required' => true,
//     'first_options'  => ['label' => 'Password'],
//     'second_options' => ['label' => 'Repeat Password'],
// ]);
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                // 'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'mapped' => false,
                'translation_domain' => 'SWUserBundle',
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('submit', SubmitType::class, array('label' => 'label.reset',
            'translation_domain' => 'SWUserBundle',
            'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
