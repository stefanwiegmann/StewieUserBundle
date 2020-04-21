<?php

namespace App\Stefanwiegmann\UserBundle\Form\Type\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
// use App\Stefanwiegmann\UserBundle\Entity\Role;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Vich\UploaderBundle\Form\Type\VichImageType;
// use Symfony\Component\Form\Extension\Core\Type\DateType;
// use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('username', TextType::class, array(
               'label' => 'label.username',
               'translation_domain' => 'SWUserBundle',
             ))

             ->add('firstName', TextType::class, array(
               'label' => 'label.firstName',
               'translation_domain' => 'SWUserBundle',
             ))

             ->add('lastName', TextType::class, array(
               'label' => 'label.lastName',
               'translation_domain' => 'SWUserBundle',
             ))

             ->add('email', EmailType::class, array(
               'label' => 'label.email',
               'translation_domain' => 'SWUserBundle',
             ))

             // ->add('imageFile', FileType::class, array(
             //   'label' => 'label.avatar',
             //   'required' => false,
             //   'translation_domain' => 'SWUserBundle',
             // ))

             ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => 'Download',
                'download_uri' => true,
                'image_uri' => true,
                'imagine_pattern' => 'thumb_filter',
                'asset_helper' => true,
              ])

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'SWUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
