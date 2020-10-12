<?php

namespace App\Form;

use App\Entity\Intervient;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IntervientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateaffectation')
            ->add('idintervention')
            ->add('idmateriel')
            ->add('idlieudepart')
            ->add('idlieuarrive')
            ->add('idstatut')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Intervient::class,
        ]);
    }
}
