<?php

namespace App\Form;

use App\Entity\Materiel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterielType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numeroserie')
            ->add('nommateriel')
            ->add('motscles')
            ->add('date')
            ->add('supprimer')
            ->add('idmarque')
            ->add('idlieu')
            ->add('idtype')
            ->add('idspecificite')
            ->add('iduser')
            ->add('idstatut')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Materiel::class,
        ]);
    }
}
