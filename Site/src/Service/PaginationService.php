<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class PaginationService{

    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getData(){
        // Valcul de l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;

        // Demander au repository de trouver les éléments
        $repo = $this->em->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);

        // Renvoyer les éléments en question
        return $data;
    }

    public function getPages(){
        // Connaitre le total des enregistrements de la table
        $repo = $this->em->getRepository($this->entityClass);
        $total = count($repo->findAll());

        // Faire la division, l'arrondi et le renvoyer
        $pages = ceil($total / $this->limit);

        return $pages;
    }
    

    /**
     * Get the value of entityClass
     */ 
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * Set the value of entityClass
     *
     * @return  self
     */ 
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get the value of limit
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of currentPage
     */ 
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */ 
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }
}