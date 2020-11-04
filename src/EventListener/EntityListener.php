<?php

namespace App\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;

class EntityListener
{
    private const CREATED_AT_FIELD = 'createdAt';
    private const UPDATED_AT_FIELD = 'updatedAt';

    /**
     * When entity has just been created and not inserted in db
     * @param LifecycleEventArgs $args
     */
    final public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::CREATED_AT_FIELD)) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * When entity has just been updated and not inserted in db
     * @param LifecycleEventArgs $args
     */
    final public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (property_exists($entity, self::UPDATED_AT_FIELD)) {
            $entity->setUpdatedAt(new \DateTime());
        }
    }

    /**
     * After the entity has been persisted and inserted in db
     * @param LifecycleEventArgs $args
     */
    final public function postPersist(LifecycleEventArgs $args): void
    {

    }

    /**
     * After the entity has been updated and inserted in db
     * @param LifecycleEventArgs $args
     */
    final public function postUpdate(LifecycleEventArgs $args): void
    {

    }
}