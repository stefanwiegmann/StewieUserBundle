<?php

namespace Stewie\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
// use Stewie\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
// use Symfony\Component\Form\Extension\Core\Type\FileType;
// use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Vich\UploaderBundle\Form\Type\VichImageType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class InviteType extends AbstractType
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

             ->add('email', EmailType::class, array(
               'label' => 'label.email',
               'translation_domain' => 'StewieUserBundle',
             ))

             ->add('submit', SubmitType::class, array('label' => 'label.invite',
             'translation_domain' => 'StewieUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
