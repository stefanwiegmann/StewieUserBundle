<?php

namespace App\Stefanwiegmann\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Stefanwiegmann\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('username', TextType::class, array(
               'label' => 'label.username',
               'translation_domain' => 'SWUserBundle',
             ))
             ->add('email', EmailType::class, array(
               'label' => 'label.email',
               'translation_domain' => 'SWUserBundle',
             ))
             ->add('firstName', TextType::class, array(
               'label' => 'label.firstName',
               'translation_domain' => 'SWUserBundle',
             ))
             ->add('lastName', TextType::class, array(
               'label' => 'label.lastName',
               'translation_domain' => 'SWUserBundle',
             ))
             ->add('password', RepeatedType::class, [
                 'type' => PasswordType::class,
                 'invalid_message' => 'The password fields must match.',
                 // 'options' => ['attr' => ['class' => 'password-field']],
                 'required' => true,
                 // 'mapped' => false,
                 'translation_domain' => 'SWUserBundle',
                 'first_options'  => ['label' => 'Password'],
                 'second_options' => ['label' => 'Repeat Password'],
             ])
             // ->add('language', ChoiceType::class, array('choices' => $options['languages'],
             //    'label' => 'label.language',
             //    'expanded' => false, 'multiple' => false, 'mapped' => true,
             //    'translation_domain' => 'SWUser',
             //    'choice_translation_domain' => false,
             //  ))
             // ->add('roles', EntityType::class, array('choices' => $options['user_roles'],
             //    'choices_as_values' => true, 'label' => 'label.roles',
             //    'choice_label' => function ($value, $key, $index) {
             //                       return 'role.'.strtolower(str_replace('ROLE_','',$value));
             //                       },
             //    'expanded' => true, 'multiple' => true, 'mapped' => true,
             //    'choice_translation_domain' => 'SWUserBundle',
             //    'translation_domain' => 'SWUserBundle',))

            // ->add('imageFile', FileType::class, array(
            //       'label' => 'label.logo',
            //       'required' => false,
            //       'translation_domain' => 'SWUserBundle',))

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'SWUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
