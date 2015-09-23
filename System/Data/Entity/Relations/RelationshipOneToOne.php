<?php

namespace System\Data\Entity\Relations;

class RelationshipOneToOne extends Relationship {
    public function __construct($entityName, array $bindingParams = array()){
        $this->entityName = $entityName;
        $this->bindingParams = $bindingParams;
        $this->relationshipType = 1;
    }
}