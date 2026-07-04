# Laravel 13 API Starter Kit

Starter kit backend-only para criacao de APIs JSON com Laravel 13, autenticacao JWT, autorizacao por permissoes, logs de atividade, filas, broadcast.

## Stack

- PHP `^8.3`
- Laravel Framework `13.15.0`
- PostgreSQL obrigatorio
- JWT Auth
- Spatie Laravel Permission
- Spatie Laravel Activitylog
- Laravel Reverb

## Requisitos

- PHP 8.3+
- Composer
- PostgreSQL
- Extensao PHP `pdo_pgsql`
- Extensoes PHP exigidas pelo Laravel

## Banco de Dados

Este starter kit assume PostgreSQL como banco obrigatorio.

## Instalacao

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

## Desenvolvimento

```bash
php artisan serve
php artisan queue:listen --tries=1 --timeout=0
```
## Autenticacao

A API usa JWT via `tymon/jwt-auth`.

Rotas principais:

- `POST /api/auth/login`
- `POST /api/auth/refresh-token`
- `POST /api/auth/me`

Rotas protegidas usam o middleware `auth.api`.

## Usuarios

Rotas publicas:

- `POST /api/user/forgot-password`
- `POST /api/user/reset-password`
- `GET /api/user/confirm-mail`
- `POST /api/user/resend-mail-confirmation`

Rota autenticada:

- `PUT /api/user/password`

## Administracao

Rotas administrativas protegidas por JWT e permissoes Spatie:

- `GET /api/admin`
- `GET /api/admin/{id}`
- `POST /api/admin`
- `PUT /api/admin/{id}`
- `PATCH /api/admin/restore/{id}`
- `DELETE /api/admin/destroy/{id}`
- `DELETE /api/admin/{id}`

## Permissoes

Permissoes declaradas em `config/permission_sync.php`:

- `users.view`
- `admin.view`
- `admin.create`
- `admin.update`
- `admin.delete`
- `admin.destroy`
- `admin.restore`
- `roles.view`

Roles iniciais:

- `sys_admin`
- `user`

O seeder cria um admin padrao:

```txt
email: admin@example.com
password: D3f4ult
```

## Dependencias PHP Diretas

### Producao

- `laravel/framework`: `13.15.0`
- `laravel/reverb`: `1.10.2`
- `laravel/tinker`: `3.0.2`
- `spatie/laravel-activitylog`: `5.0.0`
- `spatie/laravel-permission`: `8.0.0`
- `tymon/jwt-auth`: `2.3.0`

### Desenvolvimento

- `fakerphp/faker`: `1.24.1`
- `laravel/pail`: `1.2.7`
- `laravel/pao`: `1.1.1`
- `laravel/pint`: `1.29.1`
- `lucascudo/laravel-pt-br-localization`: `3.0.4`
- `mockery/mockery`: `1.6.12`
- `nunomaduro/collision`: `8.9.4`
- `pestphp/pest`: `4.7.3`

## Scripts

Composer:

- `composer run setup`
- `composer run dev`
- `composer test`

## Recursos Incluidos

- API JSON com respostas padronizadas
- Autenticacao JWT
- Refresh token
- Confirmacao de e-mail
- Recuperacao e redefinicao de senha
- CRUD administrativo com permissoes
- Soft deletes em usuarios
- UUIDs em entidades principais
- Logs de atividade
- Filas usando banco de dados
- Broadcast/Reverb
- Localizacao pt-BR