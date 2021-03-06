<?php
/**
 * Created by PhpStorm.
 * User: stefankamsker
 * Date: 31.08.18
 * Time: 14:32
 */

namespace UniteCMS\CoreBundle\Field\Types;

use Symfony\Component\Validator\Context\ExecutionContextInterface;
use UniteCMS\CoreBundle\Field\FieldType;
use UniteCMS\CoreBundle\Field\FieldableFieldSettings;
use UniteCMS\CoreBundle\Entity\FieldableField;
use UniteCMS\CoreBundle\Form\StateType;
use UniteCMS\CoreBundle\Model\StateSettings;
use UniteCMS\CoreBundle\SchemaType\SchemaTypeManager;

class StateFieldType extends FieldType
{
    const TYPE = "state";
    const FORM_TYPE = StateType::class;

    /**
     * All settings of this field type by key with optional default value.
     */
    const SETTINGS = ['initial_place', 'places', 'transitions'];

    /**
     * All required settings for this field type.
     */
    const REQUIRED_SETTINGS = ['initial_place', 'places', 'transitions'];

    function getFormOptions(FieldableField $field): array
    {
        return array_merge(
            parent::getFormOptions($field),
                [
                    'settings' => (array) $field->getSettings()
                ]
        );
    }

    /**
     * {@inheritdoc}
     */
    function getGraphQLInputType(FieldableField $field, SchemaTypeManager $schemaTypeManager, $nestingLevel = 0) {
        return $schemaTypeManager->getSchemaType('StateFieldInput');
    }

    /**
     * {@inheritdoc}
     */
    function resolveGraphQLData(FieldableField $field, $value)
    {
        // return NULL on empty value
        if (empty($value))
        {
            return NULL;
        }
        return (string) $value;
    }

    /**
     * {@inheritdoc}
     */
    function validateSettings(FieldableFieldSettings $settings, ExecutionContextInterface $context)
    {
        // Validate allowed and required settings.
        parent::validateSettings($settings, $context);

        // Only continue, if there are no violations yet.
        if ($context->getViolations()->count() > 0) {
            return;
        }

        // initial place must be a string
        if(!is_string($settings->initial_place)) {
            $context->buildViolation('workflow_invalid_initial_place')->atPath('initial_place')->addViolation();
            return;
        }

        // places must be an array.
        if(!is_array($settings->places)) {
            $context->buildViolation('workflow_invalid_places')->atPath('places')->addViolation();
            return;
        }

        // transitions must be a array.
        if(!is_array($settings->transitions)) {
            $context->buildViolation('workflow_invalid_transitions')->atPath('transitions')->addViolation();
            return;
        }

        $state_settings = StateSettings::createFromArray((array) $settings);

        $context->getViolations()->addAll($context->getValidator()->validate($state_settings));

    }

    /**
     * {@inheritdoc}
     */
    function getViewFieldDefinition(FieldableField $field = null) : array {
        return parent::getViewFieldDefinition($field) + [
            'settings' => [
                'places' => $field ? $field->getSettings()->places : []
            ]
        ];
    }
}