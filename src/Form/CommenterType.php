<?php

namespace App\Form;

use App\Entity\Commenter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class CommenterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('commentaire', CKEditorType::class,  [
                'label' => 'Votre commentaire',

                'config' => array(


                ),
                'attr' => [
                    'class' => 'form-control'
                ]]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Commenter::class,
        ]);
    }
}
