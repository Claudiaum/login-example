[draw-io]: [https://app.diagrams.net/](https://app.diagrams.net/)

# PHP Login

Um exemplo de Login com PHP 7 utilizando as variáveis $_COOKIE e $\_SESSION.

Um esquema (bem modesto...) pode ser visualizado em [draw.io (diagrams.net)][draw-io] com o arquivo de nome "login" na raiz deste repositório, a conversão em pdf do mesmo não fica muito boa então prefiro deixar este site+arquivo para a visualização do mesmo. A interface do site é bem intuitiva e fácil de se usar.

## Configuração

Para esse exemplo eu usei o **XAMPP** para rodar tanto o Apache quanto o MySQL. Na pasta raiz existe um arquivo chamado **exemplo_login.sql**, ele contem o banco de dados que uso nesse exemplo, caso não queira user ele segue abaixo um esquema do que o seu banco deve conter:

- **login_example_users**
  -- user_id -> int(11) -> chave-primária/auto-increment
  -- user_email -> varchar(255)
  -- user_password -> varchar(20)
  -- user_subscription_date -> timestamp
  -- user_last_login -> timestamp
  -- user_clearance -> int(11) -> pre-definido (1)
  -- user_status -> tinyint(1) -> pre-definido (1)

* **[login_example_sessions**
  -- session_id -> int(11) -> chave-primária/auto-increment
  -- session_token -> varchar(100)
  -- session_expiration -> timestamp
  -- session_user_id -> int(11)
  -- session_console_name -> text
  -- session_console_type -> varchar(10)
