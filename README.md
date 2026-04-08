# 🚀 Projeto ULO - Sistema de Cobrança

Sistema de gestão de cobranças desenvolvido com **Laravel 11** e **Template Phoenix**, integrado ao **n8n** para automação de fluxos de e-mail e onboarding de usuários.

## 🛠️ Tecnologias Utilizadas

* **Backend:** PHP 8.2+ / Laravel 11
* **Frontend:** Blade / Phoenix Template (Bootstrap 5)
* **Banco de Dados:** MySQL 8
* **Automação:** n8n (via Webhooks e OAuth2)
* **Infra:** Docker & Docker Compose

---

## 📦 Requisitos para Instalação

* Docker e Docker Compose instalados.
* Uma conta Google Cloud (para envio de e-mails via Gmail API).
* Instância do n8n ativa.

---

## 🔧 Passo a Passo para Instalação

### 1. Clonar e Configurar o Laravel
```bash
# Clone o repositório
git clone [https://github.com/seu-usuario/prj-ulo-cobranca.git](https://github.com/seu-usuario/prj-ulo-cobranca.git)
cd prj-ulo-cobranca

# Copie o arquivo de exemplo do ambiente
cp .env.example .env
2. Subir os Containers
Bash
docker-compose up -d
3. Instalar Dependências e Gerar Chaves
Bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
⚙️ Configuração do Ambiente (.env)
Edite o seu arquivo .env com as chaves de integração do n8n:

Snippet de código
# URL do seu Laravel (importante para os links de convite)
APP_URL=http://seu-dominio-ou-ip:8000

# Configurações do n8n
N8N_WEBHOOK_URL=[https://seu-n8n.com/webhook/identificador-do-webhook](https://seu-n8n.com/webhook/identificador-do-webhook)
N8N_API_KEY=sua_chave_segura_definida_no_header
🤖 Configuração do Fluxo n8n
O sistema dispara um convite de onboarding via Webhook. Siga estes passos no n8n:

Crie um Webhook Node:

Method: POST

Authentication: Header Auth (Name: X-API-Key)

Path: O ID gerado para o projeto.

Crie um Gmail Node:

Conecte via OAuth2 (Certifique-se de ativar a Gmail API no Google Cloud).

Mapeie os campos: To -> user_email, Subject -> "Ative sua conta".

Ative o Workflow: Mude para o modo Production no n8n e atualize a URL no .env do Laravel.

🔑 Funcionalidades Principais
Gestão de Usuários: Cadastro de novos membros com envio automático de convite.

Onboarding Seguro: O usuário recebe um link assinado (URL::signedRoute) com validade de 2 horas para definir sua própria senha.

Dashboard Phoenix: Visual moderno e responsivo para administração de cobranças.

📝 Comandos Úteis
Limpar Cache de Configurações:
docker-compose exec app php artisan config:clear

Ver Logs do Sistema:
docker-compose exec app tail -f storage/logs/laravel.log

> *Dica: Se estiver testando localmente com HTTPS/n8n Cloud, certifique-se de que o `UserService` utilize `withoutVerifying()` na chamada HTTP para evitar erros de certificado.*