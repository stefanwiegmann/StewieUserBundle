<?php

namespace App\Stefanwiegmann\UserBundle\Form\Type\Group;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Stefanwiegmann\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
// use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CreateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('name', TextType::class, array(
               'label' => 'label.name',
               'translation_domain' => 'SWUserBundle',
             ))

             ->add('groupRole', EntityType::class, array(
                   'class' => 'StefanwiegmannUserBundle:Role',
                   'query_builder' => function (EntityRepository $er) {
                       return $er->createQueryBuilder('r')
                           ->orderBy('r.id', 'ASC');
                   },
                   'choice_label' => 'name',
                   // 'choices_as_values' => true,
                   'label' => 'label.role',
                   'expanded' => true, 'multiple' => true,
                   'translation_domain' => 'SWUserBundle',
                   'choice_translation_domain' => 'SWUserBundle',
                 ))

             ->add('submit', SubmitType::class, array('label' => 'label.create',
             'translation_domain' => 'SWUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}