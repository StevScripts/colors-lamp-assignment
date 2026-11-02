# COLORS UML diagrams

These diagrams describe the COLORS implementation in this repository. The
application source reviewed was commit `cd61ef3377dc36d0c125837099bc44b36cf69e60`.
The UML addition does not change application code.

| Diagram | Editable source | Rendered diagram |
| --- | --- | --- |
| Use case | [usecase.puml](usecase.puml) | [usecase.png](usecase.png) |
| Activity | [activity.puml](activity.puml) | [activity.png](activity.png) |
| Class | [class.puml](class.puml) | [class.png](class.png) |

## Use case diagram

The actor is the user. The four implemented user goals are logging in, adding a
color, searching saved colors, and logging out. The diagram uses a system boundary
around the application. MySQL is an internal implementation detail, not a user
actor. There are no registration, edit, delete, or administrator use cases.

Login is a preceding interaction, not an `include` executed on every add or
search. The browser reads its cookie when the color page loads; the APIs do not
authenticate a server session. The diagram therefore documents the UI
precondition without claiming server-side enforcement.

## Activity diagram

The activity follows a user opening the login page, retrying an invalid login,
entering the color page, repeatedly adding or searching, and logging out.
Partitions separate login from color management. Action labels identify the
browser and PHP endpoints, including their MySQL operations.

The PHP login response for an incorrect login contains `id: 0` and
`error: "No Records Found"`. The browser uses the ID to display
`User/Password combination incorrect`. Search with no matches returns an empty
results array and `No Records Found`, which the browser displays directly.

Search uses a prepared `LIKE` pattern formed as `"%" . search . "%"`, plus
the submitted user ID. An empty search produces `%%` and returns all colors for
that ID. SQL wildcard characters in user input retain their `LIKE` meanings.
Adding a color does not automatically run a search or update the displayed list.

The activity's main flow assumes database operations succeed and HTTP 200 JSON
responses arrive. It is not an exhaustive network or database failure model:

- `connection.php` sends HTTP 500 JSON and exits when settings are missing or a
  connection exception occurs. The browser callbacks only process HTTP 200, so
  the diagram does not invent an error display or retry for those responses.
- Each endpoint also has a `connect_error` branch using `returnWithError()`.
  Add and search display a returned nonempty error on HTTP 200; login checks
  the returned ID. There is no general SQL-exception handler in the endpoints.
- The browser catches synchronous errors inside each request's `try` block,
  including errors thrown by `xhr.send()`. Request construction, `open()`, and
  header setup occur before that block. Later asynchronous JSON parsing errors
  are not caught.
- `readCookie()` runs on color-page load. There is no timer that logs out an
  already-open page when the cookie expires, and no server logout request.

## Class diagram

The app uses procedural JavaScript and PHP, not declared application classes.
The class diagram groups existing functions and global state into `<<module>>`
classifiers, and represents SQL tables as `<<entity>>` classifiers. Function
names and parameter lists match the source. The inferred `mysqli` return type
documents the connection helper; it is not a PHP type declaration.

The usual intended ownership is one user with zero or more saved colors.
However, `Colors.UserID` has no foreign key, defaults to `0`, and the add endpoint
does not validate an existing user. The diagram uses `0..1` at the Users end
and `0..*` at the Colors end to reflect the actual allowed data: a color can
match at most one user row, and a user can have many color rows. This is an
ordinary logical association, not composition, inheritance, or a database-enforced
relationship. The unused `Contacts` table is outside the implemented COLORS
features and is omitted.

## Source checks

| Diagram element | Implementation evidence |
| --- | --- |
| Login page and login action | [index.html](../../public/index.html), [doLogin()](../../public/js/code.js#L8) |
| Color page actions and load-time cookie check | [color.html](../../public/color.html) |
| Cookie contents, 20-minute expiry, load guard, logout | [saveCookie(), readCookie(), doLogout()](../../public/js/code.js#L59) |
| Add request and displayed feedback | [addColor()](../../public/js/code.js#L109) |
| Search request, error handling, displayed names | [searchColor()](../../public/js/code.js#L141) |
| Login query and JSON response | [Login.php](../../api/Login.php) |
| Color insertion and JSON response | [AddColor.php](../../api/AddColor.php) |
| User-filtered LIKE search and no-match response | [SearchColors.php](../../api/SearchColors.php) |
| Database connection and HTTP 500 failures | [connection.php](../../api/connection.php) |
| Attributes, keys, and absence of a foreign key | [schema.sql](../../database/schema.sql) |
| Local mapping of `/LAMPAPI` URLs | [router.php](../../scripts/router.php) |

The command-line demo account creation script is setup tooling, not a web
registration feature. No additional application behavior is inferred from it.

## Rendering

Rendered with PlantUML **1.2026.9** and Java. Use the official
[PlantUML release](https://github.com/plantuml/plantuml/releases/tag/v1.2026.9).
From the repository root, with its JAR stored outside the repository:

```sh
java -Djava.awt.headless=true -jar /path/to/plantuml-1.2026.9.jar -checkonly docs/uml/*.puml
java -Djava.awt.headless=true -jar /path/to/plantuml-1.2026.9.jar -charset UTF-8 -tpng docs/uml/*.puml
```

The use case and class sources select PlantUML's Smetana layout engine, so these
files do not require a separate Graphviz installation. PNGs are committed beside
their sources so the diagrams can be viewed without installing a renderer.

## AI assistance

OpenAI Codex assisted with the UML sources and this documentation. The models
were checked against the JavaScript, PHP, and SQL listed above. PlantUML syntax
checks and visual inspection of rendered PNGs supplement that source review;
they do not establish correctness on their own.
