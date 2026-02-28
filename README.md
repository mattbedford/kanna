# Kezuru
![Development Status](https://img.shields.io/badge/development-active-blue)
![Status](https://img.shields.io/badge/status-in%20progress-yellow)

A lightweight PHP starter kit for developers who want to write PHP, not "framework".
Built on Slim 4, inspired by Japanese craft tooling philosophy: remove what's unnecessary to reveal what's underneath.


## Goals
There's a gap in the PHP world. Laravel sold its soul; Wordpress got taken over by the React Mafia; SlimPHP is too slim. 
And a static site, though beautiful, is often too light or too difficult for customers to accept. ***Kezuru*** is my attempt
to fill that gap. 

***Kezuru*** aims to be a starter package that lets customers have a nice admin panel; make edits to pages; and which 
developers can customize to meet customer needs completely with PHP as the driving engine. 

To succeed it must:
- be light and portable; 
- free of vendor lock-in;
- be easily extensible; 
- allow end-clients to manage their content through a modern UI and not worry about breaking infra or designs;
- feature a modern log in and auth system and allow multi-language sites;
- [in future] allow e-Commerce builds with full security and transparency as well as offer an easy way to build "front-end admin" sites like membership sites or web-apps.


All started from the ideas and hard work of [samuelgfeller/slim-starter](https://github.com/samuelgfeller/slim-starter)


## Tech Stack

| Layer            | Tool                     | Why                                    |
|------------------|--------------------------|----------------------------------------|
| Framework        | Slim 4                   | Micro-framework, no opinions, just PHP |
| DI Container     | PHP-DI                   | Clean, annotation-free                 |
| Database         | SQLite / MariaDB / PgSQL | One config change to switch            |
| Query Builder    | cakephp/database         | Standalone, multi-driver, not an ORM   |
| Migrations       | Phinx                    | Standard PHP migration tool            |
| Templating       | PHP-View                 | Plain PHP templates, no Twig           |
| JS               | Vanilla ES modules       | No jQuery, no bundler, no build step   |
| Logging          | Monolog                  | Industry standard                      |
| Testing          | PHPUnit                  | Industry standard                      |
| Admin UI         | Tabler (TBD)             | MIT, modern, no framework dependency   |



## Design Principles

1. **You write PHP.** Not framework DSL, not YAML incantations, not React.
2. **Clients edit content.** Clean fields, clear labels, nothing they don't need.
3. **One server, one directory.** No microservices, no Docker required, no Node.
4. **Schema defines everything.** One config file per content type. The engine does the rest.
5. **Swap what you want.** SQLite today, PostgreSQL tomorrow. Tabler today, custom UI tomorrow.
6. **No private equity.** MIT licensed. No "free tier" that turns paid. No rug pulls.


## Start up
- Clone repo
- cd inside
- run `php bin/setup.php` to start and download the tailwind cli standalone
- run `php -S localhost:8080 -t public` to start server locally
- Then navigate to localhost:8000 to see what you've got!

##
- Rebuild css file with `./bin/tailwindcss -i resources/css/app.css -o public/assets/css/app.css`