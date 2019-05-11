<?php

namespace webfilesframework\core\datastore\functions\filter;

use webfilesframework\core\datastore\functions\MIDatastoreFunction;

/**
 * description
 *
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MSubstringFiltering implements MIDatastoreFunction
{

    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

}