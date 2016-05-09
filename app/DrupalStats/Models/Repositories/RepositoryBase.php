<?php

/**
 * @file
 */

namespace App\DrupalStats\Models\Repositories;

use Hussainweb\DrupalApi\Entity\Entity;
use Jenssegers\Mongodb\Eloquent\Model;

abstract class RepositoryBase
{

    /**
     * Get the model relevant to this repository.
     *
     * @return Model
     */
    abstract protected function getModel();

    /**
     * Save the entity using the repository's model.
     *
     * @param \Hussainweb\DrupalApi\Entity\Entity $entity
     *   The entity to save.
     */
    public function saveEntity(Entity $entity)
    {
        /** @var Model $item */
        $model = $this->getModel();
        $item = $model->findOrNew($entity->getId());
        foreach ($entity->getData() as $key => $value) {
            $item->$key = $this->processValue($key, $value);
        }
        $item->_id = $entity->getId();
        $item->save();
    }

    /**
     * Process the value of a property and optionally modify it.
     *
     * @param string $key
     *   The key of the property
     * @param mixed $value
     *   The value of the property
     *
     * @return mixed
     *   The modified value of the property
     */
    protected function processValue($key, $value)
    {
        return $value;
    }
}
