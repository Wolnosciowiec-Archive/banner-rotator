<?php declare(strict_types=1);

namespace App\Infrastructure\Form;

use App\Domain\Entity\BannerElement;
use App\Domain\Form\NewBannerForm;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see BannerElement
 */
class NewBannerFormType extends BannerFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('id', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => NewBannerForm::class
        ]);
    }
}
