# Laravel - ToDo App RESTFull API (using Sanctum)

This is a simple ToDo app with multiple user support.

This is built on Laravel Framework 8.x and Sanctum 2.9.

## Installation

Clone the repository-
```
git clone https://github.com/sagarpardeshi/laravel-todo-restapi-using-sanctum.git
```

Then switch to the directory where the project is located from command prompt and do a composer install
```
composer install
```
Then edit `.env` file with appropriate credential for your database server. Just edit these two parameter(`DB_USERNAME`, `DB_PASSWORD`).

In this example app I've used MySQL database.
Now create a database named `todo` and then do a database migration using this command-
```
php artisan migrate
```

Make sure you've read/write (777) permission to the storage folder.

At last generate application key, which will be used for password hashing, session and cookie encryption etc.
```
php artisan key:generate
```

## Run server

Run server using this command-
```
php artisan serve
```

Then go to `http://localhost:8000` from your browser and see the app.

## API endpoint details

1. **Registration**

	API: `http://localhost:8000/api/register`
	
	METHOD: `POST`
	
	Parameters: `name`, `email`, `password`
	
	Protected: NO
	
	Description: Used for user registration

2. **Login**

	API: `http://localhost:8000/api/login`
	
	METHOD: `POST`
	
	Parameters:  `email`, `password`
	
	Protected: NO
	
	Description: Used for login. You will have to use the `token` returned by this API to authenticate the protected endpoints while using them. This token should be used as a `Bearer token`.

3. **Create Todo**

	API: `http://localhost:8000/api/todo`
	
	METHOD: `POST`
	
	Parameters:  `id` - route parameter, `title`-required, `description` -required, `thumbnail`-optional; should be a file - png, jpg allowed and size limit is upto 2 mb
	
	Protected: YES
	
	Description: Used to create a todo.
	
4. **Todo List**

	API: `http://localhost:8000/api/todo`
	
	METHOD: `GET`
	
	Parameters:  `per_page`, `page`
	
	Protected: YES
	
	Description: Used get the list of todos. `per_page` and `page` parameters and optional and are used for the pagination purpose.  `per_page` is used to specify the number of records returned in result and `page` parameter is used to specify current page number.
	
5. **Get single Todo record**

	API: `http://localhost:8000/api/todo/{id}`
	
	METHOD: `GET`
	
	Parameters:  `id` - is an route parameter
	
	Protected: YES
	
	Description: Used to get a single to record. `id` specified in route will be used to return the specific todo record.
	
6. **Update a Todo record**

	API: `http://localhost:8000/api/todo/{id}`
	
	METHOD: `POST`
	
	Parameters:  `id` - route parameter, `title`-required, `description` -required, `thumbnail`-optional; should be a file of the type png or jpg and the file size should be under 2 mb.
	
	Protected: YES
	
	Description: Used to update a todo record.
	
7. **Delete a Todo record**

	API: `http://localhost:8000/api/todo/{id}`
	
	METHOD: `DELETE`
	
	Parameters:  `id` - route parameter to find the record to be deleted.
	
	Protected: YES
	
	Description: Used to delete a todo record from the database.
	
7. **Mark a Todo either complete / incomplete**

	API: `http://localhost:8000/api/todo/{id}/change-status`
	
	METHOD: `POST`
	
	Parameters:  `id` - route parameter to find the record, `is_completed` - numeric value to specify if the `todo` is complete or incomplete (0 - incomplete, 1 - complete).
	
	Protected: YES
	
	Description: Used to mark a todo complete or incomplete.
	
8. **Logout**

	API: `http://localhost:8000/api/logout`
	
	METHOD: `POST`
	
	Parameters:  none.
	
	Protected: YES
	
	Description: Used to logout. This will delete the used access token from the database causing further API access using it to be denied by the server.