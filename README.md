# Admin

Este repositório contém o sistema administrativo da Azcend.

## Pré-requisitos

Antes de iniciar, certifique-se de ter os seguintes requisitos instalados em seu ambiente de desenvolvimento:

- MySQL
- Nginx
- Redis
- Composer
- PHP 8.2

### Instalação e Configuração

1. **Clone o repositório:**

```bash
   git clone https://github.com/azcendpagamentos/admin.git
   cd admin
```

2. **Instale as dependências do projeto:**

```bash
composer install -vvv
```

3. **Configure o ambiente:**

- Copie o arquivo `.env.example` para `.env` e configure as variáveis de ambiente, incluindo conexões com o banco de
  dados MySQL e Redis.

4. **Execute as migrações para criar as tabelas e popular com dados default:**

```bash
php artisan migrate --seed
```

5. **Crie o token OAuth executando o seguinte comando:**

```bash
php artisan passport:install
```

### O sistema estará disponível em http://localhost.

Ao alterar aquivos `.js` precisamos executar o seguinte comando antes de realizar merge com `main`:
```shell
npm run prod
```
