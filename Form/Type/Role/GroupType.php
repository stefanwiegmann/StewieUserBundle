<?php

namespace Stewie\UserBundle\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Stewie\UserBundle\Entity\Group;
use Doctrine\ORM\EntityRepository;
// use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class GroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groups', EntityType::class, array(
                  'class' => Group::Class,
                  'query_builder' => function (EntityRepository $er) {
                      return $er->createQueryBuilder('g')
                          ->orderBy('g.name', 'ASC');
                  },
                  'choice_label' => 'name',
                  // 'choices_as_values' => true,
                  'label' => 'label.group_s',
                  'expanded' => true, 'multiple' => true,
                  'translation_domain' => 'StewieUserBundle',
                ))

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'StewieUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
