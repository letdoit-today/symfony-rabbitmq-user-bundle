<?php /** @noinspection PhpUnused */

namespace DIT\RabbitMQUserBundle\EventListener;

use DIT\RabbitMQUserBundle\Entity\UserInterface;
use DIT\RabbitMQUserBundle\Service\UserEmitterService;
use Exception;

/**
 * Class UserListener
 */
class UserListener
{
    /**
     * @var UserEmitterService
     */
    protected $emitterService;

    /**
     * @var UserInterface
     */
    protected $deletingEntity;

    /**
     * UserListener constructor.
     * @param UserEmitterService $emitterService
     */
    public function __construct(UserEmitterService $emitterService)
    {
        $this->emitterService = $emitterService;
    }

    /**
     * @param UserInterface $entity
     * @throws Exception
     */
    public function postPersist(UserInterface $entity)
    {
        $this->emitterService->emitCreateMessage($entity);
    }

    /**
     * @param UserInterface $entity
     * @throws Exception
     */
    public function postUpdate(UserInterface $entity)
    {
        $this->emitterService->emitUpdateMessage($entity);
    }

    /**
     * @param $entity
     */
    public function preRemove(UserInterface $entity)
    {
        $this->deletingEntity = clone $entity;
    }

    /**
     * @throws Exception
     */
    public function postRemove()
    {
        $this->emitterService->emitDeleteMessage($this->deletingEntity);
    }

}
