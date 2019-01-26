<?php

namespace AppBundle\Form;

use AppBundle\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label')
            ->add('description')
            ->add('parent', EntityTreeType::class, array(
                'class' => 'AppBundle\Entity\Category',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ;
                },
                'required' => false,
                'choice_value' => 'id',
                'choice_label' => 'label',
            ))
            ->add('pictureFile', FileType::class, [
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}