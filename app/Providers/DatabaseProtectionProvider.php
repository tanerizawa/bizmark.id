<?php

namespace App\Providers;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class DatabaseProtectionProvider extends ServiceProvider
{
    /**
     * Dangerous commands that should be blocked or require extra confirmation.
     */
    protected array $dangerousCommands = [
        'migrate:fresh',
        'migrate:reset',
        'db:wipe',
        'migrate:rollback',
    ];

    /**
     * Commands that require mandatory backup first.
     */
    protected array $backupRequiredCommands = [
        'migrate:fresh',
        'migrate:reset',
        'db:wipe',
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Skip protection in testing environment
        try {
            if ($this->app->runningUnitTests()) {
                return;
            }
        } catch (\Exception) {
            // Fall through to check environment
        }

        if ($this->app->environment('testing')) {
            return;
        }

        if ($this->app->runningInConsole()) {
            $this->interceptDangerousCommands();
        }
    }

    /**
     * Set up command interception.
     */
    protected function interceptDangerousCommands(): void
    {
        Event::listen(CommandStarting::class, function (CommandStarting $event) {
            // Skip check in testing - even if APP_ENV says production
            $env = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? env('APP_ENV', 'local');
            if ($env === 'testing' || getenv('PHPUNIT') === '1') {
                return;
            }

            $command = $event->command;

            // Check if this is a dangerous command
            if (in_array($command, $this->dangerousCommands)) {
                $this->handleDangerousCommand($command, $event);
            }
        });
    }

    /**
     * Handle a dangerous command execution attempt.
     */
    protected function handleDangerousCommand(string $command, CommandStarting $event): void
    {
        // Always allow in testing environment regardless of APP_ENV value
        $environment = app()->environment();
        $isProduction = in_array($environment, ['production', 'prod', 'live']);

        // Log the attempt
        Log::warning('Dangerous database command attempted', [
            'command' => $command,
            'environment' => $environment,
            'user' => get_current_user(),
            'timestamp' => now()->toIso8601String(),
        ]);

        // In production, we need extra protection
        if ($isProduction && in_array($command, $this->backupRequiredCommands)) {
            // Check if bypass flag is present
            $input = $event->input;
            $hasForce = $input->hasOption('force') && $input->getOption('force');

            if (! $hasForce) {
                $this->outputBlockMessage($command);
                $this->createProtectionLog($command, 'blocked', 'No force flag in production');

                // Exit the application
                exit(1);
            }

            // Even with force, require bypass environment variable
            $bypassCode = env('DB_DANGER_BYPASS_CODE');
            $providedCode = env('DB_DANGER_BYPASS_PROVIDED');

            if (! $bypassCode || $bypassCode !== $providedCode) {
                $this->outputBlockMessage($command);
                $this->createProtectionLog($command, 'blocked', 'Invalid or missing bypass code');
                exit(1);
            }

            // Create automatic backup before allowing
            $this->createAutomaticBackup($command);
        }
    }

    /**
     * Output block message to console.
     */
    protected function outputBlockMessage(string $command): void
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput;

        $output->writeln('');
        $output->writeln('<error>╔═══════════════════════════════════════════════════════════════╗</error>');
        $output->writeln('<error>║  🛑 DATABASE PROTECTION: COMMAND BLOCKED!                     ║</error>');
        $output->writeln('<error>╚═══════════════════════════════════════════════════════════════╝</error>');
        $output->writeln('');
        $output->writeln("<comment>Command: {$command}</comment>");
        $output->writeln('<comment>Environment: '.app()->environment().'</comment>');
        $output->writeln('');
        $output->writeln('<info>This command is blocked in production to prevent data loss.</info>');
        $output->writeln('');
        $output->writeln('<info>Safe alternatives:</info>');
        $output->writeln('  • Use <comment>php artisan migrate</comment> for new migrations');
        $output->writeln('  • Use <comment>php artisan db:backup</comment> to create backups');
        $output->writeln('  • Use <comment>scripts/db-restore.sh</comment> to restore from backup');
        $output->writeln('');
        $output->writeln('<info>If you really need to reset:</info>');
        $output->writeln('  • Use <comment>php artisan migrate:safe-fresh --really-sure</comment>');
        $output->writeln('  • Contact the system administrator');
        $output->writeln('');
    }

    /**
     * Create a log entry for database protection events.
     */
    protected function createProtectionLog(string $command, string $action, string $reason): void
    {
        $logPath = storage_path('logs/db-protection.log');
        $timestamp = now()->toIso8601String();
        $user = get_current_user();
        $env = app()->environment();

        $logEntry = "[{$timestamp}] [{$action}] Command: {$command} | Env: {$env} | User: {$user} | Reason: {$reason}\n";

        File::ensureDirectoryExists(dirname($logPath));
        File::append($logPath, $logEntry);
    }

    /**
     * Create automatic backup before dangerous operation.
     */
    protected function createAutomaticBackup(string $command): void
    {
        $output = new \Symfony\Component\Console\Output\ConsoleOutput;
        $output->writeln('<info>Creating automatic backup before operation...</info>');

        $scriptPath = base_path('scripts/db-backup.sh');

        if (File::exists($scriptPath)) {
            exec("bash {$scriptPath} backup 2>&1", $result, $returnCode);

            if ($returnCode === 0) {
                $output->writeln('<info>Backup created successfully.</info>');
                $this->createProtectionLog($command, 'backup_created', 'Automatic backup before dangerous command');
            } else {
                $output->writeln('<error>Backup failed! Aborting operation.</error>');
                $this->createProtectionLog($command, 'backup_failed', 'Automatic backup failed');
                exit(1);
            }
        }
    }
}
