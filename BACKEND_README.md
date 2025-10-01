# Backend JEFIGN - Sistema de Briefings

Este é o sistema backend para captura e gerenciamento de briefings do site JEFIGN.

## 🚀 Funcionalidades

- ✅ Captura de dados do formulário de briefing
- ✅ Upload de imagens de referência
- ✅ Armazenamento em banco de dados MySQL
- ✅ Área administrativa protegida
- ✅ Listagem e visualização de briefings
- ✅ Sistema de autenticação seguro
- ✅ Criação de usuários via CLI

## 📁 Estrutura do Projeto

```
jefign/
├── backend/
│   ├── api/
│   │   └── submit_briefing.php    # Endpoint para receber briefings
│   └── classes/
│       ├── AuthManager.php        # Gerenciamento de autenticação
│       └── BriefingManager.php    # Gerenciamento de briefings
├── config/
│   └── database.php               # Configuração do banco de dados
├── admin/
│   ├── login.php                  # Página de login
│   ├── dashboard.php              # Dashboard administrativo
│   ├── view_briefing.php          # Visualização detalhada
│   └── logout.php                 # Logout
├── uploads/                       # Diretório para imagens
├── create_admin.php               # Script CLI para criar usuários
└── briefing.html                  # Formulário atualizado
```

## ⚙️ Instalação

### 1. Configurar Banco de Dados

1. Crie um banco de dados MySQL chamado `jefign_briefing`
2. Edite o arquivo `config/database.php` com suas credenciais:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'jefign_briefing');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
```

### 2. Configurar Servidor Web

- Certifique-se de que o PHP 7.4+ está instalado
- Configure um servidor web (Apache/Nginx) apontando para o diretório do projeto
- Verifique se as extensões PHP necessárias estão ativas:
  - PDO
  - PDO_MySQL
  - JSON
  - Session

### 3. Configurar Permissões

Dê permissão de escrita ao diretório `uploads/`:

```bash
# Linux/Mac
chmod 755 uploads/

# Windows - via propriedades da pasta
```

### 4. Criar Usuário Administrativo

Execute o script CLI para criar o primeiro usuário:

```bash
php create_admin.php
```

Siga as instruções na tela para criar um usuário administrativo.

## 🔧 Uso

### Acessar Área Administrativa

1. Acesse: `http://localhost/jefign/admin/login.php`
2. Faça login com as credenciais criadas
3. Gerencie os briefings no dashboard

### Testar Formulário

1. Acesse: `http://localhost/jefign/briefing.html`
2. Preencha o formulário
3. Envie e verifique na área administrativa

## 🛡️ Segurança

- ✅ Senhas criptografadas com password_hash()
- ✅ Proteção CSRF
- ✅ Validação de uploads
- ✅ Sessões seguras
- ✅ Sanitização de dados
- ✅ Acesso restrito à área admin

## 📊 Banco de Dados

### Tabela: admin_users
- `id` - ID único
- `username` - Nome de usuário
- `email` - Email
- `password` - Senha criptografada
- `created_at` - Data de criação
- `last_login` - Último login

### Tabela: briefings
- `id` - ID único
- `empresa` - Nome da empresa
- `responsavel` - Responsável
- `email` - Email de contato
- `telefone` - Telefone
- `website` - Website
- `segmento` - Segmento de atuação
- `tempo` - Tempo no mercado
- `valores` - Valores da marca
- `missao` - Missão da marca
- `objetivo` - Objetivo do projeto
- `mensagem_marca` - Mensagem da marca
- `desafios` - Desafios
- `clientes` - Público-alvo
- `idade` - Faixa etária
- `habitos` - Hábitos do público
- `linguagem` - Linguagem desejada
- `concorrentes` - Concorrentes
- `preferencias` - Preferências
- `cores` - Paleta de cores
- `fontes` - Tipos de fonte
- `imagem_referencia` - Arquivo de imagem
- `servicos` - Serviços contratados (JSON)
- `data_inicio` - Data de início
- `data_entrega` - Data de entrega
- `etapas` - Etapas importantes
- `formato` - Formato dos arquivos
- `observacoes` - Observações
- `status` - Status do briefing
- `created_at` - Data de criação

## 🔍 Troubleshooting

### Erro de Conexão com Banco
- Verifique as credenciais em `config/database.php`
- Certifique-se de que o MySQL está rodando
- Verifique se o banco `jefign_briefing` existe

### Erro de Upload
- Verifique permissões do diretório `uploads/`
- Confirme se `upload_max_filesize` está adequado no PHP

### Erro de Sessão
- Verifique se as sessões PHP estão habilitadas
- Confirme se o diretório de sessões tem permissão de escrita

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique os logs de erro do PHP
2. Confirme as configurações do banco de dados
3. Teste as permissões de arquivo

---

**Desenvolvido para JEFIGN** - Sistema de captura e gerenciamento de briefings