<?php

namespace App\Stefanwiegmann\UserBundle\Form\Type\Group;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
// use App\Stefanwiegmann\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
// use Symfony\Component\Form\Extension\Core\Type\EmailType;
// use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DeleteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             // ->add('name', TextType::class, array(
             //   'label' => 'label.name',
             //   'translation_domain' => 'SWUserBundle',
             // ))

             ->add('submit', SubmitType::class, array('label' => 'label.delete',
             'translation_domain' => 'SWUserBundle',
             'attr'=> array('class'=>'btn-danger'),))
        ;
    }
}
