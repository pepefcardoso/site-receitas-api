# Site de Receitas - API

Esta é a API backend para o projeto "Site de Receitas", desenvolvida com Laravel. A API é responsável por gerenciar todos os dados da aplicação, incluindo usuários, receitas, posts, comentários, e mais.

## Principais Funcionalidades

-   **Autenticação de Usuários:** Sistema completo de registro, login e logout com Sanctum, além de recuperação de senha.
-   **Autenticação Social:** Login com provedores OAuth (ex: Google, GitHub) utilizando Laravel Socialite.
-   **Gerenciamento de Conteúdo:** CRUD completo para Posts e Receitas, incluindo categorias, tópicos, e dietas.
-   **Interação do Usuário:** Sistema de comentários, avaliações (ratings) e posts/receitas favoritas.
-   **Busca e Filtragem:** Funcionalidade de busca avançada para posts e receitas.
-   **Upload de Imagens:** Upload de imagens para perfis de usuário, posts e receitas.
-   **Contato e Newsletter:** Formulário de contato e inscrição em newsletter.

## Tecnologias Utilizadas

-   **Backend:**
    -   PHP 8.2
    -   Laravel 11
    -   Laravel Sanctum (Autenticação)
    -   Laravel Socialite (OAuth)
    -   PHPUnit (Testes)
-   **Banco de Dados:**
    -   SQLite (desenvolvimento)
    -   Compatível com MySQL, PostgreSQL

## Arquitetura e Padrões Utilizados

A API foi construída seguindo as melhores práticas do ecossistema Laravel, com foco em escalabilidade, manutenibilidade e clareza do código. A arquitetura principal se baseia em uma clara separação de responsabilidades, utilizando diversos padrões de projeto para organizar a lógica da aplicação.

Abaixo estão os principais padrões e conceitos arquitetônicos empregados:

*   **Service Layer (Camada de Serviço):** A lógica de negócio principal está encapsulada em classes de serviço, localizadas no diretório `app/Services`. Isso mantém os `Controllers` enxutos (thin controllers), responsáveis apenas por receber as requisições HTTP, delegar a execução para os serviços e retornar a resposta. Por exemplo, `CreatePost` e `UpdatePost` contêm a lógica para criar e atualizar posts, respectivamente.

*   **API Resources:** Para a camada de apresentação da API, utilizamos os [API Resources](https://laravel.com/docs/eloquent-resources) do Laravel. Eles são responsáveis por transformar os modelos Eloquent em respostas JSON, garantindo que a estrutura dos dados retornados seja consistente e desacoplada da estrutura do banco de dados. As classes de recurso estão em `app/Http/Resources`.

*   **Form Requests:** A validação dos dados de entrada é tratada pelas classes de [Form Request](https://laravel.com/docs/validation#form-request-validation), localizadas em `app/Http/Requests`. Isso centraliza as regras de validação e autorização para cada requisição, limpando os controllers dessa responsabilidade.

*   **Eloquent ORM e Local Scopes:** O Eloquent é utilizado para a interação com o banco de dados. Para encapsular e reutilizar lógicas de consulta (queries), foram implementados [Local Scopes](https://laravel.com/docs/eloquent#local-scopes) nos modelos. Um exemplo é o `scopeFilter` no modelo `Post`, que aplica filtros de busca de forma organizada.

*   **Policies:** A lógica de autorização é gerenciada pelas [Policies](https://laravel.com/docs/authorization#writing-policies), localizadas em `app/Policies`. Elas determinam se um usuário autenticado pode realizar uma determinada ação em um recurso (por exemplo, se pode deletar um post).

*   **Relações Polimórficas:** O projeto utiliza [relações polimórficas](https://laravel.com/docs/eloquent-relationships#polymorphic-relationships) para funcionalidades como imagens, comentários e avaliações (`ratings`). Isso permite que um mesmo modelo (ex: `Image`) possa ser associado a múltiplos outros modelos (como `Post`, `Recipe`, `User`) de forma limpa e escalável, sem a necessidade de tabelas intermediárias para cada tipo de relação.

*   **Injeção de Dependência:** O Service Container do Laravel é amplamente utilizado para gerenciar as dependências das classes, injetando automaticamente instâncias (como `Services` e `Form Requests`) nos métodos dos controllers.

*   **Autenticação com Sanctum:** A segurança da API é garantida pelo [Laravel Sanctum](https://laravel.com/docs/sanctum), que provê um sistema de autenticação leve baseado em tokens para SPAs (Single Page Applications) e aplicações móveis.

*   **Roteamento Organizado:** As rotas da API (`routes/api.php`) são cuidadosamente organizadas em grupos públicos e protegidos (com middleware `auth:sanctum`), utilizando prefixos e `apiResource` para manter a clareza e o padrão RESTful.

## Instalação e Configuração

Siga os passos abaixo para configurar o ambiente de desenvolvimento local:

1.  **Clone o repositório:**
    ```bash
    git clone https://github.com/seu-usuario/site-receitas-api.git
    cd site-receitas-api
    ```

2.  **Instale as dependências:**
    ```bash
    composer install
    ```

3.  **Configure o ambiente:**
    -   Copie o arquivo de exemplo `.env.example` para `.env`:
        ```bash
        cp .env.example .env
        ```
    -   Gere a chave da aplicação:
        ```bash
        php artisan key:generate
        ```
    -   Configure as variáveis de ambiente no arquivo `.env`, principalmente as de conexão com o banco de dados (`DB_*`).

4.  **Execute as migrações e seeders:**
    ```bash
    php artisan migrate --seed
    ```

5.  **Inicie o servidor de desenvolvimento:**
    ```bash
    php artisan serve
    ```

A API estará disponível em `http://localhost:8000`.

## Visão Geral dos Endpoints da API

A API segue os padrões RESTful. Abaixo uma visão geral dos principais grupos de endpoints.

-   `POST /api/auth/login` - Login de usuário.
-   `POST /api/auth/logout` - Logout de usuário (requer autenticação).
-   `POST /api/users` - Registro de um novo usuário.
-   `GET /api/users/me` - Retorna os dados do usuário autenticado.
-   `GET, POST, PUT, DELETE /api/posts` - CRUD de Posts.
-   `GET, POST, PUT, DELETE /api/recipes` - CRUD de Receitas.
-   `GET /api/post-categories` - Lista de categorias de posts.
-   `POST /{type}/{id}/comments` - Adiciona um comentário a um post ou receita.

Para uma lista completa de todos os endpoints, consulte o arquivo `routes/api.php`.

## Executando os Testes

Para executar a suíte de testes automatizados, utilize o seguinte comando:

```bash
php artisan test
```