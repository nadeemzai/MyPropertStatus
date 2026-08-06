<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

#[AsCommand(name: 'admin:create')]
class CreateAdminCommand extends Command
{
    protected $signature = 'admin:create
                            {--name= : The admin\'s name}
                            {--email= : A valid and unique email address}
                            {--password= : The password (min. 8 characters)}';

    protected $description = 'Create an admin account for the admin panel';

    public function handle(): int
    {
        $name = $this->option('name') ?? text(
            label: 'Name',
            required: true,
        );

        $email = $this->option('email') ?? text(
            label: 'Email address',
            required: true,
            validate: fn (string $email): ?string => $this->validateEmail($email),
        );

        $plainPassword = $this->option('password') ?? password(
            label: 'Password',
            required: true,
            validate: fn (string $value): ?string => $this->validatePassword($value),
        );

        // Options bypass the prompt validation above, so re-check here to
        // avoid a raw DB constraint error or a silently-too-short password.
        if ($error = $this->validateEmail($email)) {
            $this->components->error($error);

            return self::FAILURE;
        }

        if ($error = $this->validatePassword($plainPassword)) {
            $this->components->error($error);

            return self::FAILURE;
        }

        $admin = Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($plainPassword),
        ]);

        $loginUrl = Filament::getPanel('admin')->getLoginUrl();

        $this->components->info("Admin account created for {$admin->email}. Log in at {$loginUrl}");

        return self::SUCCESS;
    }

    private function validateEmail(string $email): ?string
    {
        return match (true) {
            ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
            Admin::where('email', $email)->exists() => 'An admin with this email address already exists.',
            default => null,
        };
    }

    private function validatePassword(string $password): ?string
    {
        return strlen($password) < 8
            ? 'The password must be at least 8 characters.'
            : null;
    }
}
