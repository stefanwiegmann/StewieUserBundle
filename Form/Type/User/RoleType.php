<?php

namespace Stewie\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Stewie\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Stewie\UserBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;
// use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['data'];

        $builder
            ->add('userRoles', EntityType::class, array(
                  'class' => Role::Class,
                  'query_builder' => function (EntityRepository $er){
                      return $er->createQueryBuilder('r')
                          ->orderBy('r.sort', 'ASC');
                  },
                  // 'choice_label' => 'translationKey',
                  'choice_label' => function($userRoles) use ($user) {
                      if($user->inheritedRole($userRoles->getName())){
                          return $userRoles->getTranslationKey() . '_inherited';
                          }else{
                              return $userRoles->getTranslationKey();
                              }
                  },
                  // 'choices_as_values' => true,
                  'label' => 'label.role',
                  'expanded' => true, 'multiple' => true,
                  'translation_domain' => 'StewieUserBundle',
                  'choice_translation_domain' => 'Roles',
                ))

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'StewieUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
