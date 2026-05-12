<?php
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function auth_login(array $user): void
{
    $_SESSION['user'] = $user;
}

function auth_logout(): void
{
    unset($_SESSION['user']);
}

function auth_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function auth_is_logged_in(): bool
{
    return auth_user() !== null;
}

function auth_name(): string
{
    $user = auth_user();
    if ($user && !empty($user['name'])) {
        return $user['name'];
    }

    if ($user && !empty($user['email'])) {
        $parts = explode('@', $user['email']);
        return ucfirst($parts[0]);
    }

    return 'Guest';
}

function auth_profile_initial(): string
{
    $name = auth_name();
    return mb_strtoupper(mb_substr($name, 0, 1));
}

function auth_user_email(): ?string
{
    $user = auth_user();
    return $user['email'] ?? null;
}

function auth_role(): string
{
    $user = auth_user();
    return $user['role'] ?? 'customer';
}
