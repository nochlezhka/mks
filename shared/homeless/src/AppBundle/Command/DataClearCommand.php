<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;

class DataClearCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('homeless:data:clear')
            ->setDescription('Удаление всех данных приложения')
            ->addOption('delete', null, InputOption::VALUE_NONE, 'Будет использовано DELETE FROM вместо TRUNCATE TABLE');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $text = '<error>ВНИМАНИЕ!<error> <question>Все данные приложения будут безвозвратно удалены! Вы уверены? (y/n)</question>';

        $question = new ConfirmationQuestion($text, false);

        if (!$input->getOption('no-interaction') && !$helper->ask($input, $output, $question)) {
            return;
        }

        $conn = $this->getContainer()->get('doctrine.orm.entity_manager')->getConnection();

        if ($input->getOption('delete')) {
            $command = 'DELETE FROM ';
        } else {
            $command = 'TRUNCATE TABLE ';
        }


        $queries = $conn->query("SELECT Concat('$command',table_schema,'.',TABLE_NAME, ';') 
                        FROM INFORMATION_SCHEMA.TABLES where  table_schema = 'homeless';")->fetchAll();

        $conn->query('SET FOREIGN_KEY_CHECKS=0')->execute();

        foreach ($queries as $query) {
            $query = reset($query);
            echo $query . "\r\n";
            $conn->query($query)->execute();
        }

        $conn->query('SET FOREIGN_KEY_CHECKS=1')->execute();

        $fs = new Filesystem();
        $rootDir = $this->getContainer()->get('kernel')->getRootDir();

        echo "Удаление загруженных файлов\r\n";
        $fs->remove($rootDir . '/../web/uploads');

        $output->writeln('<info>Удаление данных завершено</info>');
    }
}
