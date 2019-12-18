<?php

namespace Karls\ReferenceImageBundle\Field\Types;

use Karls\ReferenceImageBundle\Form\ReferenceImageType;
use UniteCMS\CoreBundle\Entity\FieldableField;
use UniteCMS\CoreBundle\Field\Types\ReferenceFieldType;

class ReferenceImageFieldType extends ReferenceFieldType
{
    const TYPE = "reference_image";
    const FORM_TYPE = ReferenceImageType::class;
    const SETTINGS = ['preview_image', 'not_empty', 'description', 'domain', 'content_type', 'view', 'content_label', 'form_group'];
    const REQUIRED_SETTINGS = ['domain', 'content_type', 'preview_image'];

    /**
     * {@inheritdoc}
     */
    function getFormOptions(FieldableField $field): array {
        $options = parent::getFormOptions($field);
        $options['attr']['preview-image'] = $field->getSettings()->preview_image ?? '';
        return $options;
    }

}
