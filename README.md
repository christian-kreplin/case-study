# Case-Study Manager

This is a small web app for demonstration purposes. It's a reload-based and fully responsive web application. The
localization of the whole app is kept in German. Momentary, there is no possibility to switch to English.

## Getting started

### Local setup via DDEV

1. Install and configure [DDEV](https://ddev.readthedocs.io/en/latest/users/install/ddev-installation/)

2. Clone this repo

``` shell
git clone https://github.com/christian-kreplin/case-study
```

3. Fire up project in DDEV. This may take a while. ☕

``` shell
cd case-study
ddev start
```

4. Install composer dependencies, node modules and create database in DDEVs PHP-container

``` sh
ddev ssh
composer install
npm install
npm run build
php bin/console doctrine:migrations:migrate
```

5. Useful commands (optional)

- Execute Fixtures to generate demo data
  ```
  ddev ssh
  php bin/console doctrine:fixtures:load
  ```
- Launch Application
  ```
  ddev launch
  ```
- Get info and links, e. g. for _PhpMyAdmin_ and _MailPit_
  ```
  ddev describe
  ```

## Used Technologies

Non-exhaustive list:

- [DDEV](https://ddev.com/)
- [Symfony](https://symfony.com/)
- [Doctrine](https://www.doctrine-project.org/)
- [TWIG](https://twig.symfony.com/)
- [jQuery](https://jquery.com/)
- [Tailwind CSS](https://tailwindcss.com/)
- [Tailmater (Theme)](https://github.com/aribudin/tailmater)
- [VichUploader](https://github.com/dustin10/VichUploaderBundle)
- [CKEditor (WYSIWYG)](https://ckeditor.com/)

## Scope

This app was designed to fulfill the following purposes:

### General info

- **Account handling**: Let a User create a new account, log in/log out and reset it`s password.
- **Role Management**: Basic management of actions that require a logged-in user. Guest users (not logged-in) can only
  view some parts of the application.
- **BREAD operations**: All Entities can be handled
  via [BREAD operations](https://github.com/thangchung/clean-architecture-dotnet/wiki/BREAD-vs-CRUD). Some are limited
  to logged-in users.

### Entities

- **Redakteur**: This is the users entity and only accessible when logged in.
- **Kunden**: Customers which can be related to Case Studies. Not logged-in users can only see active customers.
- **Case Studies**: The actual case studies with relation to a customer. Not logged-in users can only see case studies
  of active customers.

It's possible to upload images to customers and case studies. If there is no image, the app generates an avatar.