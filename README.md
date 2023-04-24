# Octopus project  
(or how to use PHPUnit in a Symfony project)

## Install project

1. `git clone git@github.com:Emma1987/em_portfolio.git` to clone the project.  
2. Create your `.env.local` file from the `.env` template: `cp .env .env.local`  
3. [Create a GitHub access token](https://github.com/eckinox/symfony-docker-template/blob/main/docs/usage/getting-started/new-project.md#:~:text=Create%20a%20GitHub%20access%20token) for Composer, and define it in the `GITHUB_ACCESS_TOKEN` variable of your `.env.local` file.  
4. `make build` to build the containers.  
5. `make up` to start the containers.  
6. Open [https://localhost](https://localhost) and trust the auto-generated TLS certificate.  

###### Create a new user
You can run the command `make sf c='app:create-user'` to create a user.  

## Run the test suite

The tests are located in the `tests/` folder at the root of the project.  

To load the fixtures into the database: `make load-fixtures`  
To run the test suite: `make run-test`  
To run a single class of tests:  `docker compose exec php bin/phpunit --filter 'MyTestClass'`  
