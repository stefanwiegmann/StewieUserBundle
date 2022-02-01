<?php

namespace Stewie\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class AddUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('userAuto', TextType::class, array(
                'label' => 'label.user',
                'required' => false,
                'mapped' => false,
                'translation_domain' => 'StewieUserBundle',
                'attr'=> array('class' => 'form_user_auto', 'autocomplete' => 'off'),
                ))

            ->add('userAutoId', HiddenType::class, array(
                'mapped' => false,
                ))

            ->add('submit', SubmitType::class, array(
                'label' => 'label.add_user',
                'translation_domain' => 'StewieUserBundle',
                'attr'=> array('class'=>'btn-primary'),
                ))
        ;
    }
}
