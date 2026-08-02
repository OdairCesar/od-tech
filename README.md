# OD Tec

Site institucional da OD Tec construído em Laravel 13 + Filament 4. Combina páginas públicas
(home, serviços, cidades/estados atendidos, portfólio, blog, FAQ, contato e um consultor de IA)
com landing pages de SEO programático e um painel administrativo para gerenciar conteúdo e leads.

## Stack

- **Backend:** PHP 8.5, Laravel 13, Livewire 3
- **Admin:** Filament 4 (`/admin`)
- **Frontend:** Tailwind CSS 4, Vite
- **Testes:** Pest 4 / PHPUnit 12
- **Qualidade:** Larastan (PHPStan), Laravel Pint
- **IA:** abstração própria (`app/Services/Ai`) sobre `openai-php/laravel` e `google-gemini-php/laravel`,
  escolhida via config (`AI_TEXT_PROVIDER` / `AI_IMAGE_PROVIDER`) — usada para geração de posts de
  blog, conteúdo de clusters de serviço, imagens de capa e relatórios do consultor de IA
- **Mídia:** Cloudinary (via `codebar-ag/laravel-flysystem-cloudinary`) como disco de arquivos
- **Filas:** Laravel Queue (driver `database`)

## Funcionalidades principais

- **Páginas públicas** (`routes/web.php`): home, sobre, serviços, cidades, estados, portfólio, blog,
  FAQ, contato, consultor de IA, `sitemap.xml` e `robots.txt`.
- **SEO programático:** rotas curinga `{service}-em-{city}` que resolvem `LandingPage`s e
  `ServiceClusterLandingPage`s sincronizadas automaticamente a partir de `Service`/`ServiceCluster`/
  `City` (`app/Actions/Landing`, `app/Actions/ServiceCluster`).
- **Blog e clusters de serviço com geração via IA:** jobs `App\Jobs\GenerateAiBlogPost` e
  `App\Jobs\GenerateServiceClusterContent` (fila `database`) geram texto e imagem de capa via IA
  (`app/Services/Blog`).
- **Consultor de IA:** chat (`App\Livewire\ConsultationChat`) que coleta dados do lead e dispara
  `App\Jobs\GenerateConsultationReport`, gerando um relatório com IA e notificando por e-mail.
- **Captura de leads:** formulário de contato (`/contato`) com throttle, listado no painel admin.
- **Painel admin (Filament):** CRUD de Serviços, Clusters de Serviço, Cidades, Estados, Landing
  Pages, Portfólio, Posts, Categorias, Leads e Consultorias em `app/Filament/Resources`.

## Setup local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=ServiceSeeder
php artisan db:seed --class=StateSeeder
php artisan db:seed --class=CitySeeder
npm install
npm run build
```

Ou use o script agregado do Composer, que faz tudo isso de uma vez:

```bash
composer run setup
```

Configure no `.env` as credenciais de banco (`DB_*`), do provedor de IA escolhido (`OPENAI_*` e/ou
`GEMINI_*`, mais `AI_TEXT_PROVIDER`/`AI_IMAGE_PROVIDER`) e Cloudinary (`CLOUDINARY_*`) antes de gerar
conteúdo via IA.

### Ambiente de desenvolvimento

```bash
composer run dev
```

Sobe em paralelo o servidor Laravel, o worker da fila (`queue:listen`) e o Vite em modo watch.

## Testes

```bash
php artisan test --compact
```

## Deploy no Railway

O serviço web padrão do Railway só atende HTTP — nada processa a fila `database` usada pelos jobs
de geração via IA (`GenerateAiBlogPost`, `GenerateServiceClusterContent`, `GenerateConsultationReport`)
e por `RegenerateLandingPages`. É preciso um **segundo serviço** ("worker"), apontando para o mesmo
repositório/branch:

1. Crie um novo serviço no mesmo projeto Railway, mesmo repo/branch do serviço web.
2. Em **Settings → Deploy → Custom Start Command**: `bash railway/run-worker.sh`.
3. Em **Settings → Networking**: não exponha domínio/porta — é um worker, não recebe requisições.
4. Em **Variables**: copie/vincule as mesmas variáveis do serviço web (`DB_*`, `OPENAI_*`/`GEMINI_*`,
   `CLOUDINARY_*`, `QUEUE_CONNECTION`, `DB_QUEUE_RETRY_AFTER`).

Veja [`railway/run-worker.sh`](railway/run-worker.sh) para o comando exato e por que os timeouts
(`--timeout`, `DB_QUEUE_RETRY_AFTER`) precisam ficar alinhados entre si.

## Documentação e ferramentas de IA

O projeto usa [Laravel Boost](https://laravel.com/docs/ai) para dar contexto e ferramentas a
agentes de IA (Claude Code, Cursor, etc). Convenções e regras específicas do projeto estão em
[`CLAUDE.md`](CLAUDE.md).
