<?php /** @noinspection PhpUnused */

namespace DIT\RabbitMQUserBundle\Command;

use DIT\RabbitMQUserBundle\Service\UserReceiverService;
use ErrorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DITListenUserMessageCommand
 */
class DITListenUserMessageCommand extends Command
{
    protected static $defaultName = 'letdoittoday:listen:user-message';

    /**
     * @var UserReceiverService
     */
    protected $receiverService;

    /**
     * ReceiveUserMessage constructor.
     * @param UserReceiverService $receiverService
     * @param string|null $name
     */
    public function __construct(UserReceiverService $receiverService, ?string $name = null)
    {
        parent::__construct($name);

        $this->receiverService = $receiverService;
    }

    protected function configure()
    {
        $this
            ->setDescription('Creates a new user.')
            ->setHelp('This command allows you to create a user...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $now = date('l Y-m-d H:i:s');
        $output->writeln("$now: Waiting for message. To exit press CTRL+C ==============================");

        $this->receiverService->setOutput($output);
        $this->receiverService->receiveMessage();

        return 0;
    }
}
