<?php

namespace App\Stefanwiegmann\UserBundle\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class DetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

             ->add('description', TextType::class, array(
               'label' => 'label.description',
               'translation_domain' => 'SWUserBundle',
             ))

             ->add('sort', TextType::class, array(
               'label' => 'label.sort',
               'translation_domain' => 'SWUserBundle',
             ))

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'SWUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
