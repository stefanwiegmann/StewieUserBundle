<?php

namespace Stewie\UserBundle\Form\Type\Role;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class DetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

             ->add('description', TextType::class, array(
               'label' => 'label.description',
               'translation_domain' => 'StewieUserBundle',
             ))

             ->add('sort', TextType::class, array(
               'label' => 'label.sort',
               'translation_domain' => 'StewieUserBundle',
             ))

             ->add('avatarFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => 'label.download',
                'download_uri' => false,
                'image_uri' => true,
                'imagine_pattern' => 'stewie_user_medium_filter',
                'asset_helper' => true,
                'label' => 'label.avatar',
                'translation_domain' => 'StewieUserBundle',
              ])

             ->add('submit', SubmitType::class, array('label' => 'label.update',
             'translation_domain' => 'StewieUserBundle',
             'attr'=> array('class'=>'btn-primary'),))
        ;
    }
}
