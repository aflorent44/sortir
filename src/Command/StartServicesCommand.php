<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:start-services',
    description: 'Ouvre Symfony server, Tailwind et Messenger dans des terminaux séparés.'
)]
class StartServicesCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Démarrage des services dans des terminaux séparés...</info>');

        // Liste des commandes à exécuter
        $commands = [
            'symfony serve',
            'symfony console tailwind:build --watch',
            'php bin/console messenger:consume async -vv',
            'npm run watch'

        ];

        // Chemin du projet actuel
        $projectPath = getcwd();

        foreach ($commands as $index => $command) {
            // Créer un script bat pour chaque commande
            $scriptName = sys_get_temp_dir() . "/service_" . $index . ".bat";

            $scriptContent = "@echo off\r\n";
            $scriptContent .= "cd /d \"{$projectPath}\"\r\n";
            $scriptContent .= "title {$command}\r\n";
            $scriptContent .= "echo Execution de: {$command}\r\n";
            $scriptContent .= "echo.\r\n";
            $scriptContent .= "{$command}\r\n";
            $scriptContent .= "pause\r\n";

            file_put_contents($scriptName, $scriptContent);

            // Exécuter le script dans une nouvelle fenêtre de commande
            $process = new Process(['cmd', '/c', 'start', 'cmd.exe', '/k', $scriptName]);
            $process->setTimeout(10);
            $process->run();

            if ($process->isSuccessful()) {
                $output->writeln("<info>Terminal lancé pour : {$command}</info>");
            } else {
                $output->writeln("<error>Erreur lors du lancement de : {$command}</error>");
                $output->writeln("<error>" . $process->getErrorOutput() . "</error>");
            }

            // Attendre un peu entre chaque ouverture
            sleep(1);
        }

        $output->writeln('<info>Tous les services ont été lancés dans des terminaux séparés.</info>');
        return Command::SUCCESS;
    }
}