<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Marque;
use App\Entity\Materiel;
use App\Entity\Specificite;
use App\Entity\Statut;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class MaterielType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        
            ->add('nommateriel', TextType::class, $this->getConfiguration("Nom", "Nom du matériel"))
            ->add('motscles', TextType::class, $this->getConfiguration("Mot clé", "Entré un mot clé"))
            ->add('idmarque', EntityType::class, $this->getConfiguration("Marque", "Choisir une marque", [
                'class' => Marque::class,
                'choice_label' => function ($marque) {
                    return $marque->getlibelleMarque();
                }
            ]))

            ->add('idtype', EntityType::class, $this->getConfiguration("Type de matériel", "", [
                'class' => Type::class,
                'choice_label' => function ($type) {
                    return $type->getlibelleType();
                }
            ]))
            ->add('idspecificite', EntityType::class, $this->getConfiguration("Spécificité du  matériel", "", [
                'class' => Specificite::class,
                'choice_label' => function ($specificite) {
                    return $specificite->getlibelleSpe();
                }
            ]))
            // ->add('iduser')
            ->add('idstatut', EntityType::class, $this->getConfiguration("Statut du  matériel", "", [
                'class' => Statut::class,
                'choice_label' => function ($statut) {
                    return $statut->getlibelleStatut();
                }
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Materiel::class,
        ]);
    }
}
