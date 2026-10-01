<?php

namespace App\Controller\Admin;

use App\Entity\Conference;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\String\Slugger\SluggerInterface;

class ConferenceCrudController extends AbstractCrudController
{
    public function __construct(
        private SluggerInterface $slugger,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Conference::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            TextField::new('city'),
            TextField::new('year'),
            BooleanField::new('isInternational'),
        ];
    }

    public function persistEntity(
        EntityManagerInterface $entityManager,
        $entityInstance
    ): void {
        if ($entityInstance instanceof Conference) {
            $entityInstance->computeSlug($this->slugger);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}