# Sistema de Login Centralizado - CardioWeb

## Resumo da Consolidação

O sistema de login foi consolidado em uma **página centralizada única** com suporte a múltiplos métodos de autenticação através de modais.

## Arquitetura Nova

### Arquivo Principal
- **index.php** - Página única de login com interface centralizada e todas as opções de autenticação

### Processamento de Autenticação
- **auth-process.php** - Arquivo centralizado que processa todos os tipos de login:
  - Login por Email/Senha
  - Login por Google
  - Login por Apple
  - Login por SMS

### Arquivos Legados (Compatibilidade)
Os arquivos de login separados ainda existem, mas agora redirecionam para o novo sistema:
- **process_login.php** - Redireciona para auth-process.php
- **process_google_login.php** - Redireciona para auth-process.php
- **process_apple_login.php** - Redireciona para auth-process.php
- **process_sms_login.php** - Redireciona para auth-process.php

Os arquivos antigos de página separada podem ser removidos se não forem mais usados:
- apple-login.php
- google-login.php
- sms-login.php

## Como Funciona

### Login por Email/Senha
1. Usuário preenche formulário principal com email e senha
2. Envia para `auth-process.php` com `login_type=email`
3. Sistema valida e redireciona para dashboard.php

### Login por Google/Apple
1. Usuário clica no botão da rede social
2. Modal abre com opções de conta ou formulário
3. Dados são enviados para `auth-process.php` via POST com `login_type=google/apple`
4. Sistema processa e redireciona para dashboard.php

### Login por SMS
1. Usuário clica em botão SMS
2. Modal abre com campo de telefone
3. Após digitar código, envia para `auth-process.php` com `login_type=sms`
4. Sistema valida código e redireciona para dashboard.php

### Recuperação de Senha
- Modal separado para recuperação de senha
- Fluxo com 3 etapas: email → código → nova senha

## Autenticação no auth-process.php

```php
// Exemplo de uso:
$_POST['login_type'] = 'email';  // ou 'google', 'apple', 'sms'
$_POST['email'] = 'user@example.com';
$_POST['password'] = 'senha123';
```

## Variáveis de Sessão Criadas

Após login bem-sucedido, as seguintes variáveis são criadas na sessão:
- `$_SESSION['user_id']` - ID do usuário
- `$_SESSION['user_email']` - Email do usuário
- `$_SESSION['user_name']` - Nome do usuário
- `$_SESSION['login_type']` - Tipo de login (Email, Google, Apple, SMS)
- `$_SESSION['logado']` - Flag de login ativo

## Melhorias Implementadas

✅ **Consolidação de Interface** - Uma única página de login com múltiplas opções
✅ **Código Centralizado** - Processamento de autenticação em um único arquivo
✅ **Sem Duplicação** - Eliminação de código repetido entre diferentes tipos de login
✅ **Melhor UX** - Modais intuitivas em vez de redirecionamentos
✅ **Compatibilidade** - Arquivos legados funcionam redirecionando para novo sistema
✅ **Fácil Manutenção** - Todas as lógicas de autenticação em um único lugar

## Próximos Passos Recomendados

1. **Integração com Banco de Dados**
   - Validar email/senha contra BD em `auth-process.php`
   - Armazenar data/hora de último login

2. **Integração com Provedores OAuth**
   - Implementar autenticação real do Google
   - Implementar autenticação real do Apple
   - Implementar envio real de SMS

3. **Segurança**
   - Adicionar hash de senha em BD
   - Implementar rate limiting
   - Adicionar tokens CSRF
   - Validar tokens de longa duração

4. **Limpeza**
   - Remover arquivos legados obsoletos se não mais usados
   - Remover conflitos de merge (<<<<<<< HEAD) completamente

## Testes

Para testar o novo sistema:

1. **Email/Senha**: Use qualquer email válido (em desenvolvimento aceita qualquer)
2. **Google**: Clique em Google e escolha uma das contas de teste
3. **Apple**: Clique em Apple e use um email qualquer
4. **SMS**: Use telefone (11) 99999-9999 e código 123456

## Contato e Suporte

Para dúvidas sobre o novo sistema de login, consulte este documento ou revisar `auth-process.php` para entender o fluxo completo.
