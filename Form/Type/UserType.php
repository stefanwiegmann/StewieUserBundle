<?php

namespace Stewie\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Stewie\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserType extends AbstractType
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
             ->add('userRole', EntityType::class, array(
                   'class' => 'StewieUserBundle:Role',
                   'query_builder' => function (EntityRepository $er) {
                       return $er->createQueryBuilder('r')
                           ->orderBy('r.id', 'ASC');
                   },
                   'choice_label' => 'translationKey',
                   // 'choices_as_values' => true,
                   'label' => 'label.role',
                   'expanded' => true, 'multiple' => true,
                   'translation_domain' => 'SWUserBundle',
                   'choice_translation_domain' => 'Roles',
                 ))

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
