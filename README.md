# Laravel 13 API Starter Kit

Starter kit backend-only para criacao de APIs JSON com Laravel 13, autenticacao JWT, autorizacao por permissoes, logs de atividade, filas com Redis/Horizon e broadcast.

## Stack

- PHP `^8.4`
- Laravel Framework `13.15.0`
- PostgreSQL (obrigatorio)
- Redis (filas via Laravel Horizon)
- JWT Auth
- Spatie Laravel Permission
- Spatie Laravel Activitylog
- Laravel Reverb

## Requisitos

- PHP 8.4+
- Composer
- PostgreSQL
- Redis
- Extensoes PHP: `pdo_pgsql`, `redis`, `pcntl` (exigida pelo Horizon) e as extensoes padrao do Laravel

## Instalacao

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

Antes de rodar o seed, defina as credenciais do administrador padrao no `.env`:

```env
AUTH_DEFAULT_EMAIL=admin@example.com
AUTH_DEFAULT_PASSWORD=uma-senha-forte
```

Tambem configure a URL do frontend, usada nos links enviados por e-mail (ex.: redefinicao de senha):

```env
FRONTEND_URL=http://localhost:5173
```

## Desenvolvimento

```bash
composer run dev
```

Sobe em paralelo o servidor (`php artisan serve`), o Horizon (`php artisan horizon`) e os logs (`php artisan pail`).

## Filas

As filas usam Redis (`QUEUE_CONNECTION=redis`) e sao processadas pelo Laravel Horizon. Os jobs sao despachados somente apos o commit da transacao (`after_commit`), evitando que um job rode com dados que ainda nao foram gravados.

E-mails (confirmacao de conta e recuperacao de senha) sao enviados por jobs, entao o Horizon precisa estar rodando para que sejam entregues.

### Painel do Horizon

Disponivel em `/horizon`. Em ambiente `local` o acesso e livre. Nos demais ambientes o navegador solicita usuario e senha (HTTP Basic Auth): use o e-mail e a senha de um usuario com a role `sys_admin`.

## Autenticacao

A API usa JWT via `tymon/jwt-auth`. Rotas protegidas usam o middleware `auth.api`.

- `POST /api/auth/login`
- `POST /api/auth/refresh-token`
- `POST /api/auth/me` (autenticada)
- `POST /api/auth/logout` (autenticada)

## Usuarios

Rotas publicas:

- `POST /api/user/forgot-password`
- `POST /api/user/reset-password`
- `GET /api/user/confirm-mail`
- `POST /api/user/resend-mail-confirmation`

Rota autenticada:

- `PUT /api/user/password` (encerra a sessao atual apos a troca)

E-mails sao normalizados para minusculas no cadastro, login e recuperacao de senha.

## Rate Limiting

| Limitador | Rotas | Limite |
|---|---|---|
| `login` | `POST /api/auth/login` | 5/min por e-mail + IP e 20/min por IP |
| `emails` | `forgot-password`, `resend-mail-confirmation` | 3 a cada 10 min por e-mail e 10/min por IP |
| `tokens` | `reset-password`, `confirm-mail` | 10/min por IP |

Definidos em `app/Providers/AppServiceProvider.php`.

## Administracao

Rotas administrativas protegidas por JWT e permissoes Spatie:

| Metodo | Rota | Permissao |
|---|---|---|
| `GET` | `/api/admin` | `admin.view` |
| `GET` | `/api/admin/{id}` | `admin.view` |
| `POST` | `/api/admin` | `admin.create` |
| `PUT` | `/api/admin/{id}` | `admin.update` |
| `PATCH` | `/api/admin/restore/{id}` | `admin.restore` |
| `DELETE` | `/api/admin/{id}` | `admin.delete` (soft delete) |
| `DELETE` | `/api/admin/destroy/{id}` | `admin.destroy` (exclusao definitiva) |

Um administrador nao pode excluir a si mesmo, e o sistema impede a exclusao do ultimo administrador ativo.

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

- `sys_admin` (recebe todas as permissoes)
- `user`

Apos adicionar novas permissoes ao config, sincronize com:

```bash
php artisan permission:sync
```

## Deploy (Docker / Coolify)

O projeto inclui `Dockerfile` e `docker-compose.yml` prontos para o [Coolify](https://coolify.io).

- **Imagem:** baseada em `serversideup/php:8.4-fpm-nginx` (nginx + PHP-FPM, roda como `www-data`, escuta na porta `8080`).
- **Servicos do compose:**
  - `web` - a API. Com `AUTORUN_ENABLED=true`, executa `migrate --force` e gera os caches de config/rotas/views a cada deploy.
  - `horizon` - mesma imagem, executando `php artisan horizon`.
- **PostgreSQL e Redis** sao recursos separados no Coolify (com backups gerenciados pelo proprio Coolify).

### Passos no Coolify

1. Crie PostgreSQL e Redis como recursos.
2. Crie um novo recurso a partir do repositorio usando o build pack **Docker Compose**.
3. No servico `web`, defina o dominio com a porta, ex.: `https://api.seudominio.com:8080`. O `horizon` nao precisa de dominio.
4. Configure as variaveis de ambiente:
   - `APP_KEY`, `JWT_SECRET`
   - `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, `FRONTEND_URL`
   - `LOG_CHANNEL=stderr` (logs visiveis no painel do Coolify)
   - `DB_*` e `REDIS_*` apontando para os hostnames internos dos recursos
   - `MAIL_*`
   - `AUTH_DEFAULT_EMAIL` e `AUTH_DEFAULT_PASSWORD`
5. Faca o deploy e, no terminal do container `web`, rode o seed uma unica vez:

```bash
php artisan db:seed --force
```

A aplicacao confia nos headers do proxy (`trustProxies`), necessario para gerar URLs `https://` atras do Traefik do Coolify.

### Testando a imagem localmente

```bash
docker build -t starter-kit .
docker run --rm -p 8080:8080 \
  -e APP_KEY=base64:... \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e LOG_CHANNEL=stderr \
  starter-kit
```

`http://localhost:8080/up` deve responder `200`.

## Recursos Incluidos

- API JSON com respostas padronizadas
- Autenticacao JWT com refresh token e logout
- Rate limiting nas rotas de autenticacao
- Confirmacao de e-mail
- Recuperacao e redefinicao de senha
- CRUD administrativo com permissoes
- Soft deletes em usuarios
- UUIDs em entidades principais
- Logs de atividade
- Filas com Redis e Laravel Horizon
- Broadcast/Reverb
- Localizacao pt-BR
- Deploy com Docker/Coolify
