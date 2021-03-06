<?php

namespace Stewie\UserBundle\Form\Type\Register;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
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

             ->add('submit', SubmitType::class, array('label' => 'label.register',
             'translation_domain' => 'StewieUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
