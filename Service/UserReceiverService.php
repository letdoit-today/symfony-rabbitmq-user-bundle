<?php

namespace DIT\RabbitMQUserBundle\Service;

use DIT\RabbitMQBundle\Service\AbstractDirectReceiverService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

/**
 * Class UserReceiverService
 */
class UserReceiverService extends AbstractDirectReceiverService
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * UserReceiverService constructor.
     * @param ContainerBagInterface $params
     * @param SerializerInterface $serializer
     * @param EntityManagerInterface $em
     */
    public function __construct(
        ContainerBagInterface $params,
        SerializerInterface $serializer,
        EntityManagerInterface $em
    ) {
        parent::__construct($params);
        $this->serializer = $serializer;
        $this->em = $em;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /** @noinspection PhpUnused */
    public function getOutput()
    {
        return $this->output;
    }

    protected function getRoutingKeys(): array
    {
        return [
            'users.create',
            'users.update',
            'users.delete',
        ];
    }

    protected function getExchange(): string
    {
        return 'entities';
    }

    protected function handleDefault(string $routingKey, string $body)
    {
        $this->writeWarning("Unhandle routingkey '$routingKey'");
    }

    protected function handleUserJson(string $json, callable $callback)
    {
        try {
            $className = $this->params->get('letdoittoday.user_class');
            $entity = $this->serializer->deserialize($json, $className, 'json');
            /* TODO: Replace merge method */
            $entity = $this->em->merge($entity);
            $callback($entity);
        } catch (Exception $exception) {
            $this->writeError($exception->getMessage());
        }
    }

    /** @noinspection PhpUnused */
    protected function handleUsersCreateMessage(string $message)
    {
        $this->handleUserJson(
            $message,
            function ($entity) {
                $this->em->flush();
                $this->writeInfo("Created user: {$entity->getId()}-{$entity->getName()}");
            }
        );
    }

    /** @noinspection PhpUnused */
    protected function handleUsersUpdateMessage(string $message)
    {
        $this->handleUserJson(
            $message,
            function ($entity) {
                $this->em->flush();
                $this->writeInfo("Updated user: {$entity->getId()}-{$entity->getName()}");
            }
        );
    }

    /** @noinspection PhpUnused */
    protected function handleUsersDeleteMessage(string $message)
    {
        $this->handleUserJson(
            $message,
            function ($entity) {
                $message = "Deleted user: {$entity->getId()}-{$entity->getName()}";
                $this->em->remove($entity);
                $this->em->flush();
                $this->writeInfo($message);
            }
        );
    }

    protected function writeInfo(string $message)
    {
        $this->writeMessage("<info>$message</info>");
    }

    protected function writeWarning(string $message)
    {
        $this->writeMessage("<comment>$message</comment>");
    }

    protected function writeError(string $message)
    {
        $this->writeMessage("<error>$message</error>");
    }

    protected function writeMessage(string $message)
    {
        if (!empty($this->output)) {
            $this->output->writeln($message);
        } else {
            echo $message.PHP_EOL;
        }
    }
}
