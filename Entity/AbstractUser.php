<?php /** @noinspection PhpUnused */

namespace DIT\RabbitMQUserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * Class AbstractUser
 */
abstract class AbstractUser implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @Serializer\Type("int")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Serializer\Type("string")
     */
    protected $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
