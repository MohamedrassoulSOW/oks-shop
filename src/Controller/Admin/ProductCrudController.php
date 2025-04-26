<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits')
        ;
    }

    public function configureFields(string $pageName): iterable
    {

        $requered = true;

        if ($pageName = 'edit') {
            $required = false;
        }
        
        return [
            TextField::new('name')->setLabel('Nom')->setHelp('Nom de votre produit'),
            SlugField::new('slug')->setTargetFieldName('name')->setLabel('Url')->setHelp('Url de votre produit'),
            TextEditorField::new('description')->setLabel('Description')->setHelp('La description de votre produit'),
            ImageField::new('illustration')->setLabel('Image')
            ->setHelp('Image du produit en 600x600px')
            ->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')
            ->setBasePath('/uploads')
            ->setUploadDir('/public/uploads')
            ->setRequired($required),
            NumberField::new('price')->setLabel('Prix H.T')->setHelp('Prix H.T du produit sans le sigle CFA.'),
            ChoiceField::new('tva')->setLabel('Taux de TVA')->setChoices([
                '5,5%'=>'5.5',
                '10%'=>'10',
                '18%'=>'18'
            ]),
            AssociationField::new('category')->setLabel('Catégorie associée')

        ];
    }

}
