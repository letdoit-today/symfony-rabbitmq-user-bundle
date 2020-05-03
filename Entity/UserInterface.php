<?php

namespace DIT\RabbitMQUserBundle\Entity;

/**
 * Interface UserInterface
 */
interface UserInterface
{
    public function getId(): ?int;

    public function getEmail(): ?string;

    public function setEmail(string $email);
}
