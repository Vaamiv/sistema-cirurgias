# Sistema Cirurgias — MVP (Laravel)

Este pacote contém **o código de aplicação** (controllers, models, migrations e views) para colar em um projeto Laravel.
Ele **não** contém o core do Laravel. Abaixo segue o passo a passo **do zero**, instalação, e como hospedar.

## 0) Requisitos (Windows 10/11)
1. **PHP 8.2+** — recomendo instalar com o [XAMPP](https://www.apachefriends.org/) (marca Apache + PHP). Alternativa avançada: instalar PHP standalone e adicionar ao PATH.
2. **Composer** — baixe o instalador: https://getcomposer.org/Composer-Setup.exe (ele detecta o PHP automaticamente).
3. **Node.js 18+** — https://nodejs.org/ (LTS).
4. **Git** (opcional, mas recomendado) — https://git-scm.com/download/win
5. **MySQL** (pode vir com XAMPP) ou **PostgreSQL**. Para MVP, MySQL é mais simples.

> Linux (Ubuntu 22.04+): `sudo apt update && sudo apt install -y php8.2-cli php8.2-mbstring php8.2-xml php8.2-curl php8.2-sqlite3 unzip nodejs npm mysql-server git` e instale Composer conforme docs.

## 1) Criar projeto Laravel do zero
```bash
# 1. Crie uma pasta de trabalho e entre nela
mkdir sistema-cirurgias && cd sistema-cirurgias

# 2. Baixe o Laravel base (isso instala vendor/, artisan etc.)
composer create-project laravel/laravel .

# 3. Instale pacotes de app
composer require spatie/laravel-activitylog pusher/pusher-php-server

# 4. Front-end
npm install
npm install fullcalendar pusher-js
```

## 2) Configurar .env e chave do app
```bash
cp .env.example .env
php artisan key:generate
```
Abra `.env` e configure:
- **DB_CONNECTION/DB_HOST/DB_DATABASE/DB_USERNAME/DB_PASSWORD** (MySQL local ou server do hospital)
- **APP_TIMEZONE=America/Sao_Paulo**
- Broadcasting e WebSockets (veja seção *Tempo real*).

## 3) Copiar o conteúdo deste ZIP para dentro do seu projeto
Copie as pastas deste zip para **substituir/mesclar** no seu projeto recém-criado:
```
app/
database/
public/js/
resources/views/
routes/
```
> Se te pedir para **mesclar/substituir**, aceite (são apenas os arquivos do app).

## 4) Rodar migrações e assets
```bash
php artisan migrate
npm run build    # ou: npm run dev
```

## 5) (Opcional) Autenticação rápida (Breeze)
```bash
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build
php artisan migrate
```
Depois, proteja as rotas com `auth` se desejar (já há exemplo no `routes/web.php` comentado).

---

# Como hospedar (visão prática)

### Banco de dados
- **On-premise**: MySQL num servidor do hospital (Windows Server ou Ubuntu). Crie um usuário e banco `sistema_cirurgias`.
- **Cloud**: RDS (AWS), Cloud SQL (GCP), Azure DB. Basta apontar o `.env` para o host da instância.

### Aplicação Laravel
- **On-premise Windows**: IIS com PHP ou rodar com Apache do XAMPP (vhost apontando para `public/`).  
- **Linux/Ubuntu** (recomendado): Nginx + PHP-FPM. Aponta o servidor para a pasta `public/` do Laravel.
- **Docker**: compose com serviços `app`, `nginx`, `mysql` e (se usar) `websockets`.

### Tempo real (2 caminhos)
1) **Pusher** (SaaS): simples — configurar as chaves no `.env`. O front recebe eventos via Pusher; não hospeda servidor WS.
2) **Laravel WebSockets** (on-premise): instalar `beyondcode/laravel-websockets`, rodar `php artisan websockets:serve` em serviço.  
   - Precisa também rodar **queue worker** para broadcasts: `php artisan queue:work`.
   - No `.env`, use host/port locais do servidor WS.

**Fluxo em tempo real:**
- Recepção cria/edita cirurgia ⇒ Controller emite `event(new SurgeryUpdated(...))` ⇒ Laravel transmite via Broadcast (Pusher/WS) no **canal `surgeries`** ⇒ Front-end (**FullCalendar**) escuta e faz `refetchEvents()` ⇒ tela do centro cirúrgico atualiza sem F5.

### Backups e logs
- Ative backup periódico do **banco de dados** (dump diário).  
- `spatie/laravel-activitylog` já registra **quem mudou o quê** (auditoria).

---

# Ordem cronológica dos arquivos a criar/editar

1. `composer.json` e estrutura base → criado pelo `composer create-project` (passo 1).
2. `routes/web.php` e `routes/api.php` (do ZIP) → adicionam rotas da UI e API.
3. `app/Events/SurgeryUpdated.php` → evento broadcast.
4. `app/Models/Patient.php` e `app/Models/Surgery.php` → modelos mínimos.
5. `database/migrations/*create_patients_table.php` e `*create_surgeries_table.php` → esquema do banco.
6. `app/Http/Controllers/CalendarController.php` e `SurgeryController.php` → lógica de CRUD e API.
7. `resources/views/...` → layouts, calendário (FullCalendar) e telas de create/edit/list.
8. `.env` → credenciais de DB + Broadcasting.
9. `public/js/echo-init.js` (opcional, caso queira centralizar Echo) e `resources/js/app.js` (caso vá usar Vite para carregar libs).
10. (Opcional) `breeze` e policies/middlewares para travar acesso por setor.

---

# Rodar localmente (desenvolvimento)
```bash
php artisan serve    # levanta a app (http://127.0.0.1:8000)
# Em outra janela:
php artisan queue:work   # se usando broadcast
# E (se usar WebSockets on-premise em vez de Pusher):
php artisan websockets:serve
```
Acesse `/` para ver o Calendário; `/surgeries` para o CRUD.

---

# Dúvidas comuns
- **Onde ficam os dados?** No **MySQL** configurado no `.env`. O Laravel cria as tabelas via migrations.
- **Preciso de site externo?** Não. Pode ser **on-premise** (rede interna do hospital). Se quiser acesso externo, hospede num VPS e feche firewall/ACLs por IP.
- **Sem internet?** Use **Laravel WebSockets** on-premise. Evite Pusher (depende de internet).
