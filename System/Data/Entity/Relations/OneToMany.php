<?php

namespace System\Data\Entity\Relations;

class OneToMany extends Relationship {
    
    /**
     * Initializes an instance of OneToMany with an entity principal name and 
     * optional binding property names.
     * 
     * @param   string $principalEntityName
     * @param   array $bindingProperties = array()
     */
    public function __construct($principalEntityName, array $bindingProperties = array()){
        $this->principalEntityName = $principalEntityName;
        $this->bindingProperties = $bindingProperties;
        $this->relationshipType = 2;
    }
}