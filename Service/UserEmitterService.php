<?php /** @noinspection PhpUnused */

namespace DIT\RabbitMQUserBundle\Service;

use DIT\RabbitMQBundle\Service\AbstractDirectEmitterService;
use DIT\RabbitMQUserBundle\Entity\UserInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Class UserEmitterService
 */
class UserEmitterService extends AbstractDirectEmitterService
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * UserEmitterService constructor.
     * @param ContainerBagInterface $params
     * @param SerializerInterface $serializer
     */
    public function __construct(ContainerBagInterface $params, SerializerInterface $serializer)
    {
        parent::__construct($params);
        $this->serializer = $serializer;
    }

    protected function getExchange(): string
    {
        return 'entities';
    }

    /**
     * @param UserInterface $entity
     * @throws Exception
     */
    public function emitCreateMessage(UserInterface $entity)
    {
        $this->emitUserMessage($entity, 'users.create');
    }

    /**
     * @param UserInterface $entity
     * @throws Exception
     */
    public function emitUpdateMessage(UserInterface $entity)
    {
        $this->emitUserMessage($entity, 'users.update');
    }

    /**
     * @param UserInterface $entity
     * @throws Exception
     */
    public function emitDeleteMessage(UserInterface $entity)
    {
        $this->emitUserMessage($entity, 'users.delete');
    }

    /**
     * @param UserInterface $entity
     * @param string $routingKey
     * @throws Exception
     */
    protected function emitUserMessage(UserInterface $entity, string $routingKey)
    {
        $message = $this->serializer->serialize($entity, 'json');

        $this->emitMessage($message, $routingKey);
    }
}
