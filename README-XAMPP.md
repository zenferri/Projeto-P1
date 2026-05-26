# Instruções rápidas para rodar no XAMPP

Se você recebeu o erro de acesso negado (Access denied for user 'root'@'localhost'), siga esses passos:

1) Abra o painel do XAMPP e inicie Apache e MySQL.
2) Abra o phpMyAdmin: http://localhost/phpmyadmin
3) Crie o banco de dados (se preferir manualmente):

```sql
CREATE DATABASE singularys CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

4) (Opcional - recomendado) Crie um usuário específico para o projeto e dê permissões:

```sql
CREATE USER 'singularys_user'@'localhost' IDENTIFIED BY 'SENHA_SEGURA';
GRANT ALL PRIVILEGES ON singularys.* TO 'singularys_user'@'localhost';
FLUSH PRIVILEGES;
```

5) Atualize as credenciais do banco em `config/config.php` (edite o arquivo e salve).

Exemplo mínimo (XAMPP padrão):

```php
<?php
return [
  'DB_HOST' => '127.0.0.1',
  'DB_PORT' => '3306',
  'DB_NAME' => 'singularys',
  'DB_USER' => 'root',
  'DB_PASS' => '',
];
```

6) Alternativamente, defina variáveis de ambiente no Apache (httpd.conf) ou no painel do Windows, ou use `.htaccess` (SetEnv).

7) Acesse o projeto em:

```
http://localhost/Projeto-P1/
```

