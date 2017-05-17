# Routes

Scopes are applied through middleware which are normally not shown (but have been added manually to this file. With the exception of the following three methods, all routes are secured by OAuth2 scopes based on their resource name, e.g. `contact.store` is under the `contact` scope (generally referred to as `$thing` in code).


Open routes:

  - root.index
  - root.options
  - request.store

Tokens can be granted by making requests to `oauth/access_token`, check out the curl example in `server/roll.sh`

```
+-----------+-----------+---------------------+------------------+----------------------------------------------------+-----------+------------+--------------------------+------------+
| Host      | Method    | URI                 | Name             | Action                                             | Protected | Version(s) | Scope(s)                 | Rate Limit |
+-----------+-----------+---------------------+------------------+----------------------------------------------------+-----------+------------+--------------------------+------------+
| localhost | POST      | oauth/access_token  |                  | Closure                                            | No        | v1         |                          |            |
| localhost | GET|HEAD  |                     | root.index       | Closure                                            | No        | v1         |                          |            |
| localhost | OPTIONS   |                     | root.options     | Closure                                            | No        | v1         |                          |            |
| localhost | POST      | request             | request.store    | App\Http\Controllers\v1\RequestController@store    | No        | v1         |                          |            |
| localhost | OPTIONS   | contact             | contact.options  | App\Http\Controllers\v1\ContactController@options  | No        | v1         | contact                  |            |
| localhost | GET|HEAD  | contact             | contact.index    | App\Http\Controllers\v1\ContactController@index    | No        | v1         | contact                  |            |
| localhost | POST      | contact             | contact.store    | App\Http\Controllers\v1\ContactController@store    | No        | v1         | contact                  |            |
| localhost | GET|HEAD  | contact/{contact}   | contact.show     | App\Http\Controllers\v1\ContactController@show     | No        | v1         | contact                  |            |
| localhost | PUT|PATCH | contact/{contact}   | contact.update   | App\Http\Controllers\v1\ContactController@update   | No        | v1         | contact                  |            |
| localhost | DELETE    | contact/{contact}   | contact.destroy  | App\Http\Controllers\v1\ContactController@destroy  | No        | v1         | contact                  |            |
| localhost | OPTIONS   | request             | request.options  | App\Http\Controllers\v1\RequestController@options  | No        | v1         | request                  |            |
| localhost | GET|HEAD  | request             | request.index    | App\Http\Controllers\v1\RequestController@index    | No        | v1         | request                  |            |
| localhost | GET|HEAD  | request/{request}   | request.show     | App\Http\Controllers\v1\RequestController@show     | No        | v1         | request                  |            |
| localhost | PUT|PATCH | request/{request}   | request.update   | App\Http\Controllers\v1\RequestController@update   | No        | v1         | request                  |            |
| localhost | DELETE    | request/{request}   | request.destroy  | App\Http\Controllers\v1\RequestController@destroy  | No        | v1         | request                  |            |
| localhost | OPTIONS   | resource            | resource.options | App\Http\Controllers\v1\ResourceController@options | No        | v1         | resource                 |            |
| localhost | GET|HEAD  | resource            | resource.index   | App\Http\Controllers\v1\ResourceController@index   | No        | v1         | resource                 |            |
| localhost | POST      | resource            | resource.store   | App\Http\Controllers\v1\ResourceController@store   | No        | v1         | resource                 |            |
| localhost | GET|HEAD  | resource/{resource} | resource.show    | App\Http\Controllers\v1\ResourceController@show    | No        | v1         | resource                 |            |
| localhost | PUT|PATCH | resource/{resource} | resource.update  | App\Http\Controllers\v1\ResourceController@update  | No        | v1         | resource                 |            |
| localhost | DELETE    | resource/{resource} | resource.destroy | App\Http\Controllers\v1\ResourceController@destroy | No        | v1         | resource                 |            |
| localhost | OPTIONS   | status              | status.options   | App\Http\Controllers\v1\StatusController@options   | No        | v1         | status                   |            |
| localhost | GET|HEAD  | status              | status.index     | App\Http\Controllers\v1\StatusController@index     | No        | v1         | status                   |            |
| localhost | POST      | status              | status.store     | App\Http\Controllers\v1\StatusController@store     | No        | v1         | status                   |            |
| localhost | GET|HEAD  | status/{status}     | status.show      | App\Http\Controllers\v1\StatusController@show      | No        | v1         | status                   |            |
| localhost | PUT|PATCH | status/{status}     | status.update    | App\Http\Controllers\v1\StatusController@update    | No        | v1         | status                   |            |
| localhost | DELETE    | status/{status}     | status.destroy   | App\Http\Controllers\v1\StatusController@destroy   | No        | v1         | status                   |            |
| localhost | OPTIONS   | type                | type.options     | App\Http\Controllers\v1\TypeController@options     | No        | v1         | type                     |            |
| localhost | GET|HEAD  | type                | type.index       | App\Http\Controllers\v1\TypeController@index       | No        | v1         | type                     |            |
| localhost | POST      | type                | type.store       | App\Http\Controllers\v1\TypeController@store       | No        | v1         | type                     |            |
| localhost | GET|HEAD  | type/{type}         | type.show        | App\Http\Controllers\v1\TypeController@show        | No        | v1         | type                     |            |
| localhost | PUT|PATCH | type/{type}         | type.update      | App\Http\Controllers\v1\TypeController@update      | No        | v1         | type                     |            |
| localhost | DELETE    | type/{type}         | type.destroy     | App\Http\Controllers\v1\TypeController@destroy     | No        | v1         | type                     |            |
| localhost | OPTIONS   | user                | user.options     | App\Http\Controllers\v1\UserController@options     | No        | v1         | user                     |            |
| localhost | GET|HEAD  | user                | user.index       | App\Http\Controllers\v1\UserController@index       | No        | v1         | user                     |            |
| localhost | POST      | user                | user.store       | App\Http\Controllers\v1\UserController@store       | No        | v1         | user                     |            |
| localhost | GET|HEAD  | user/{user}         | user.show        | App\Http\Controllers\v1\UserController@show        | No        | v1         | user                     |            |
| localhost | PUT|PATCH | user/{user}         | user.update      | App\Http\Controllers\v1\UserController@update      | No        | v1         | user                     |            |
| localhost | DELETE    | user/{user}         | user.destroy     | App\Http\Controllers\v1\UserController@destroy     | No        | v1         | user                     |            |
+-----------+-----------+---------------------+------------------+----------------------------------------------------+-----------+------------+--------------------------+------------+
```
