<?php
namespace Karls\ReferenceImageBundle\Service;

use Karls\ReferenceImageBundle\Field\Types\ReferenceImageFieldType;
use UniteCMS\CoreBundle\Entity\Fieldable;
use UniteCMS\CoreBundle\Entity\FieldableField;
use UniteCMS\CoreBundle\Field\FieldTypeManager;
use UniteCMS\CoreBundle\Field\NestableFieldTypeInterface;

class ReferenceImageService
{

    /**
     * @var FieldTypeManager $fieldTypeManager
     */
    private $fieldTypeManager;

    public function __construct(FieldTypeManager $fieldTypeManager)
    {
        $this->fieldTypeManager = $fieldTypeManager;
    }

    /**
     * Resolves a nestable file field by a given path. At the moment, nestable
     * fields are only used for the collection field type.
     *
     * @param Fieldable $fieldable
     * @param $path
     *
     * @return null|FieldableField
     */
    public function resolveFileFieldPath(Fieldable $fieldable, $path)
    {
        /**
         * @var FieldableField $field
         */
        if (!$field = $fieldable->resolveIdentifierPath($path, true)) {
            return null;
        }

        // If we found an image field, we can return it
        if ($field->getType() == ReferenceImageFieldType::TYPE || $field->getType() == ReferenceImageFieldType::TYPE) {
            return $field;
        } else {

            // If this field is nestable, continue resolving.
            $nestedFieldType = $this->fieldTypeManager->getFieldType($field->getType());
            if ($nestedFieldType instanceof NestableFieldTypeInterface) {
                return $this->resolveFileFieldPath($nestedFieldType::getNestableFieldable($field), $path);
            }
        }

        return null;
    }
}
