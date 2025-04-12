# Laravel Starter Kit

Este é um kit inicial para aplicações Laravel com Filament Admin Panel e várias ferramentas de desenvolvimento já configuradas, incluindo multitenancy single database.

## Requisitos

- PHP 8.4 ou superior
- Composer
- Docker e Docker Compose
- Node.js e npm

## Instalação

Siga os passos abaixo para instalar o projeto:

### 1. Clone o repositório

```bash
git clone https://github.com/seu-usuario/starter-kit.git
cd starter-kit
```

### 2. Instale as dependências

```bash
composer install
npm install
```

### 3. Configure o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

### 4. Execute as migrações

```bash
php artisan migrate --seed
```

### 5. Inicialize o servidor de desenvolvimento

```bash
php artisan serve
# Em outro terminal
npm run dev
```

## Características

- **Filament Admin Panel**: Interface administrativa completa
- **Multitenancy Single Database**: Sistema multi-inquilino em uma única base de dados
- **Ferramentas de Desenvolvimento**:
  - Laravel Pint (PSR-12 based linter)
  - Rector (Refatoração automática)
  - PHPStan (Análise estática)
  - Laravel Debugbar
  - Laravel Telescope
  - Laravel Pulse
  - Pest PHP (Testes)
  
## Comandos Úteis

### Desenvolvimento

```bash
# Executar o servidor, queue, logs e vite simultaneamente
composer run dev

# Executar testes
./vendor/bin/pest

# Executar linting
./vendor/bin/pint

# Executar análise estática
./vendor/bin/phpstan analyse

# Refatoração automática
./vendor/bin/rector process
```

### Docker (opcional)

O projeto inclui configuração para Laravel Sail:

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm run dev
```

## Estrutura de Multitenancy

O projeto utiliza um sistema de multitenancy single database com as seguintes características:

- Modelos filtrados automaticamente por tenant_id
- Middleware para determinar o tenant atual
- Scope global para consultas baseadas em tenant
- Suporte completo no painel Filament

## Configuração do AWS S3 Local com MinIO

```
AWS_ACCESS_KEY_ID=sail
AWS_SECRET_ACCESS_KEY=password
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=local
AWS_ENDPOINT=http://minio:9000
AWS_URL=http://localhost:9000/local
AWS_USE_PATH_STYLE_ENDPOINT=true
```

## Licença

Este projeto está licenciado sob a [MIT License](LICENSE).