Payment Gateway Application

Requirements:
Docker
Docker Compose

Setup Instructions:
Clone the repository.
.env is provided
Build and start the containers by running: docker-compose up --build
Inside the "app" container, run migrations and seeders with the following command: php artisan migrate --seed

The application should now be available:
Nginx (web server): http://localhost:8000
PHPMyAdmin: http://localhost:8080 u/p - root/root
Testing: To run the tests, execute the following command inside the "app" container: php artisan test

These steps should allow anyone who received the test to build, run, and test the application using Docker.