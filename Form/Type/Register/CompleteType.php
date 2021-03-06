<?php

namespace Stewie\UserBundle\Form\Type\Register;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class CompleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

             ->add('firstName', TextType::class, array(
               'label' => 'label.firstName',
               'translation_domain' => 'StewieUserBundle',
             ))

             ->add('lastName', TextType::class, array(
               'label' => 'label.lastName',
               'translation_domain' => 'StewieUserBundle',
             ))

             ->add('username', TextType::class, array(
               'label' => 'label.username',
               'translation_domain' => 'StewieUserBundle',
             ))

             ->add('password', RepeatedType::class, [
                 'type' => PasswordType::class,
                 'invalid_message' => 'The password fields must match.',
                 // 'options' => ['attr' => ['class' => 'password-field']],
                 'required' => true,
                 'mapped' => false,
                 'translation_domain' => 'StewieUserBundle',
                 'first_options'  => ['label' => 'label.password.first', 'translation_domain' => 'StewieUserBundle'],
                 'second_options' => ['label' => 'label.password.second', 'translation_domain' => 'StewieUserBundle'],
             ])

             ->add('submit', SubmitType::class, array('label' => 'label.register.complete',
             'translation_domain' => 'StewieUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
