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

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('groupRole', EntityType::class, array(
                  'class' => 'StefanwiegmannUserBundle:Role',
                  'query_builder' => function (EntityRepository $er) {
                      return $er->createQueryBuilder('r')
                          ->orderBy('r.sort', 'ASC');
                  },
                  'choice_label' => 'translationKey',
                  // 'choices_as_values' => true,
                  'label' => 'label.role',
                  'expanded' => true, 'multiple' => true,
                  'translation_domain' => 'SWUserBundle',
                  'choice_translation_domain' => $groupRole->getTranslationDomain(),
                  // 'choice_translation_domain' =>
                  // function($role) {
                  //   return $role->getTranslationDomain();
                  // },
                ))

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'SWUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
