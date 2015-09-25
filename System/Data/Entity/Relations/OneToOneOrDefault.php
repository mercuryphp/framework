<?php

namespace System\Data\Entity\Relations;

class OneToOneOrDefault extends Relationship {
    public function __construct($principalEntityName, array $bindingParams = array()){
        $this->principalEntityName = $principalEntityName;
        $this->bindingParams = $bindingParams;
        $this->relationshipType = 1;
        $this->singleOrDefault = true;
    }
}