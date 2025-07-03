# Yazamos

### Project setup steps:

- Go to project root directory from terminal
- Copy and paste `.env.example` into `.env` file in root directory
  ```shell
  cp .env.example .env
  ```
- Create the database with name `yazamos`
- Do necessary change in `.env` file (like, set database credentials, app name and app url)
- Run following commands:
  ```shell
  composer install
  php artisan key:generate
  php artisan migrate
  ```
- If you need test data run following `(must be in staging server)`:
  ```shell
  php artisan db:seed
  ```
- Go to the `admin` root folder
- Run the following commands:
  ```shell
  npm install
  npm run serve
  ```
- Copy and paste `.env.example` into `.env` file in admin directory (`Note: you need to change url according to your system`)
    ```shell
    cp .env.example .env
    ```
