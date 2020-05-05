<?php

namespace Stewie\UserBundle\Form\Type\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
// use Stewie\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('username', TextType::class, array(
               'label' => 'label.username',
               'translation_domain' => 'StewieUserBundle',
             ))
             ->add('email', EmailType::class, array(
               'label' => 'label.email',
               'translation_domain' => 'StewieUserBundle',
             ))
             ->add('firstName', TextType::class, array(
               'label' => 'label.firstName',
               'translation_domain' => 'StewieUserBundle',
             ))
             ->add('lastName', TextType::class, array(
               'label' => 'label.lastName',
               'translation_domain' => 'StewieUserBundle',
             ))

            // ->add('imageFile', FileType::class, array(
            //       'label' => 'label.logo',
            //       'required' => false,
            //       'translation_domain' => 'StewieUserBundle',))

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'StewieUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
