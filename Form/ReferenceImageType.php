<?php
namespace Karls\ReferenceImageBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UniteCMS\CoreBundle\Form\ReferenceType as ReferenceType;

class ReferenceImageType extends ReferenceType
{

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'karls_reference_image';
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['assets'] = [
            [ 'css' => 'main.css', 'package' => 'KarlsReferenceImageBundle' ],
            [ 'js' => 'main.js', 'package' => 'KarlsReferenceImageBundle' ],
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults(
            [
                'compound' => true,
                'error_bubbling' => false,
                'tag' => 'karls-reference-image-field',
                'assets' => [],
                'empty_data' => [
                    'domain' => null,
                    'content_type' => null,
                    'content' => null
                ],
            ]
        );
    }

}
